<?php

namespace metrica\core;

# TODO: FIX
interface HeadersInterface extends ParamsInterface {
  public static function fromEnv(EnvInterface $env): HeadersInterface;
}
