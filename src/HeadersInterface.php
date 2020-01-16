<?php

namespace metrica\core;

# TODO: FIX
interface HeadersInterface extends ParamsInterface {
  public function fromEnv(EnvInterface $env): HeadersInterface;
}
