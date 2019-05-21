<?php

namespace Awerd\Process;

class Transition {

    public $name = null;
    public $from = null;
    public $to = null;
    public $payload = [];


    public function __construct($name, State $from, State $to, array $payload = [])
    {
        $this->name = $name;
        $this->from = $from;
        $this->to = $to;
        $this->payload = $payload;
    }


    /**
     * @param StateMachine $stateMachine
     * @return bool
     */
    public function canTransit(StateMachine $stateMachine): bool
    {
        return true;
    }


    /**
     * @param StateMachine $stateMachine
     */
    public function transit(StateMachine $stateMachine): void
    {
        // Change object properties if need...
    }

}
