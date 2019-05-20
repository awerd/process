# process
Awesome State Machine Library

```php
  use Awerd\Process\StateMachine;
  use Awerd\Process\Transition;
  use Awerd\Process\State;

  class DomainObject {

      public $state = 'initial';

  }

  $object = new DomainObject();

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
  $stateMachine->addTransition($workingComplete = new Transition('working-complete', $working, $complete));
  $stateMachine->addTransition($completeWorking = new Transition('complete-working', $complete, $working));

  var_dump($stateMachine->getCurrentState()->name); // initial

  var_dump($stateMachine->canTransit($initialWorking)); // true
  var_dump($stateMachine->canTransit($workingComplete)); // false

  var_dump($stateMachine->availableTransitions()); // [$initialWorking]

  $stateMachine->transit($initialWorking);

  var_dump($stateMachine->getCurrentState()->name); // working
```
