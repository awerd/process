<?php

namespace Awerd\Process;

class State {

    public $name = null;
    public $payload = null;
    public $transitions = [];


    public function __construct($name, array $payload = [])
    {
        $this->name = $name;
        $this->payload = $payload;
    }


    /**
     * @param Transition $transition
     */
    public function addTransition(Transition $transition)
    {
        $this->transitions[] = $transition;
    }

}
