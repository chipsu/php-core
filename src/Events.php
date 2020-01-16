<?php

namespace metrica\core;

class Events implements EventsInterface {
  protected $listeners;

  public function __construct(array $listeners = []) {
    $this->listeners = $listeners;
  }

  public function on(string $name, callable $callback)
  {
    $this->listeners[$name][] = $callback;
  }

  public function fire(string $name, iterable $params = [])
  {
    if(!isset($this->listeners[$name])) {
      return true;
    }

    $event = new Event([
      'sender' => $this,
      'params' => $params,
    ]);

    foreach($this->listeners[$name] as $callback) {
      throw new \Exception('FIX THIS! Use DI?');
      if($argumentGenerator = $this->getArgumentGenerator()) {
        /* $params = is_array($event->params) ? $event->params : (array)$event->params;
        $args = $argumentGenerator->createMethodArgs($callback, array_merge($params, [
          'event' => $event,
        ])); */
      } else {
        $args = [$event];
      }

      $result = call_user_func_array($callback, $args);

      if($result === false) {
        return false;
      }
    }

    return true;
  }
}
