<?php

namespace metrica\core;

interface CacheInterface
{
  public function get(string $key, callable $source);
}
