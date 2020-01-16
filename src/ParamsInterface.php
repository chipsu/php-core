<?php

namespace metrica\core;

interface ParamsInterface
{
  public function all(): array;
  public function get(string $key, $default = null);
}
