<?php

namespace Awerd\Process;

use Closure;
use Exception;

class StateMachine {

    public $object = null;
    public $getter = null;
    public $setter = null;

    public $states = [];
    public $transitions = [];


    public function __construct($object)
    {
        $this->object = $object;
    }


    /**
     * @param State $state
     * @return StateMachine
     * @throws Exception
     */
    public function addState(State $state): StateMachine
    {
        $existsStates = array_filter($this->states, function (State $item) use ($state) {
            return $item === $state;
        });

        if (count($existsStates) > 0) {
            throw new Exception("State {$state->name} already exists");
        }

        $this->states[] = $state;

        return $this;
    }


    /**
     * @param Transition $transition
     * @return StateMachine
     * @throws Exception
     */
    public function addTransition(Transition $transition): StateMachine
    {
        $fromStates = array_filter($this->states, function (State $state) use ($transition) {
            return $state === $transition->from;
        });

        if (count($fromStates) === 0) {
            throw new Exception("State {$transition->from->name} not found");
        }

        $toStates = array_filter($this->states, function (State $state) use ($transition) {
            return $state === $transition->to;
        });

        if (count($toStates) === 0) {
            throw new Exception("State {$transition->to->name} not found");
        }

        /** @var State $from */
        $from = array_shift($fromStates);
        $from->addTransition($transition);

        $this->transitions[] = $transition;

        return $this;
    }


    /**
     * @param Closure $getter
     * @return StateMachine
     */
    public function setGetter(Closure $getter): StateMachine
    {
        $this->getter = $getter;

        return $this;
    }


    /**
     * @param Closure $setter
     * @return StateMachine
     */
    public function setSetter(Closure $setter): StateMachine
    {
        $this->setter = $setter;

        return $this;
    }


    /**
     * @return State
     * @throws Exception
     */
    public function getCurrentState(): State
    {
        $name = $this->getter->call($this, $this->object);

        $states = array_filter($this->states, function (State $state) use ($name) {
            return $state->name === $name;
        });

        if (count($states) === 0) {
            throw new Exception("State {$name} not found");
        }

        return array_shift($states);
    }


    /**
     * @return array
     * @throws Exception
     */
    public function getAvailableTransitions(): array
    {
        $state = $this->getCurrentState();

        return $state->transitions;
    }


    /**
     * @param string $name
     * @return Transition
     * @throws Exception
     */
    public function getAvailableTransitionByName(string $name): ?Transition
    {
        $filteredTransitions = array_filter($this->getAvailableTransitions(), function (Transition $transition) use ($name) {
            return $transition->name === $name;
        });

        return array_shift($filteredTransitions);
    }


    /**
     * @param Transition $transition
     * @return bool
     * @throws Exception
     */
    public function canTransit(Transition $transition): bool
    {
        $state = $this->getCurrentState();

        $existsTransitions = array_filter($state->transitions, function (Transition $item) use ($transition) {
            return $item === $transition;
        });

        if (count($existsTransitions) > 1) {
            throw new Exception("Ambiguous transition {$transition->name}");
        }

        if (count($existsTransitions) === 0) {
            return false;
        }

        /** @var Transition $transition */
        $transition = array_shift($existsTransitions);

        return $transition->canTransit($this);
    }


    /**
     * @param Transition $transition
     * @param array $payload
     * @return StateMachine
     * @throws Exception
     */
    public function transit(Transition $transition, array $payload = []): StateMachine
    {
        if ($this->canTransit($transition)) {
            $transition->transit($this);

            $this->setter->call($this, $this->object, $transition, $payload);

            return $this;
        }

        throw new Exception("Transition {$transition->from->name} to {$transition->to->name} is not available");
    }

}
