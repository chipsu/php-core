<?php

namespace metrica\core;

interface EventsInterface
{
  public function on(string $name, callable $callback);
  public function fire(string $name, iterable $params = []);
}
