<?php

    require __DIR__ . '/../vendor/autoload.php';

    use Awerd\Process\StateMachine;
    use Awerd\Process\Transition;
    use Awerd\Process\State;

    class DomainObject {

        public $state = 'initial';
        public $total = 20;

    }


    class WorkingCompleteTransition extends Transition {

        public function canTransit(StateMachine $stateMachine): bool
        {
            /** @var DomainObject $object */
            $object = $stateMachine->object;

            return $object->total > 100;
        }

    }


    $object = new DomainObject();

    try {

        $stateMachine = new StateMachine($object);

        $stateMachine->setGetter(function (DomainObject $object) {
            return $object->state;
        });

        $stateMachine->setSetter(function (DomainObject $object, Transition $transition) {
            $object->state = $transition->to->name;
        });

        $stateMachine->addState($initial = new State('initial'));
        $stateMachine->addState($working = new State('working'));
        $stateMachine->addState($complete = new State('complete'));

        $stateMachine->addTransition($initialWorking = new Transition('initial-working', $initial, $working));
        $stateMachine->addTransition($workingComplete = new WorkingCompleteTransition('working-complete', $working, $complete));
        $stateMachine->addTransition($completeWorking = new Transition('complete-working', $complete, $working));

        var_dump($stateMachine->getCurrentState()->name);
        var_dump($stateMachine->canTransit($initialWorking));
        var_dump($stateMachine->canTransit($workingComplete));
        var_dump($stateMachine->getAvailableTransitions());

        $stateMachine->transit($initialWorking);

        var_dump($stateMachine->getCurrentState()->name);
        var_dump($stateMachine->canTransit($workingComplete));

        $object->total = 200;

        var_dump($stateMachine->canTransit($workingComplete));

        $stateMachine->transit($workingComplete);

        var_dump($stateMachine->getCurrentState()->name);

    } catch (Exception $exception) {
        throw $exception;
    }
