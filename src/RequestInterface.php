<?php

namespace metrica\core;

# TODO: FIX
interface RequestInterface
{
  public static function fromEnv(EnvInterface $env): RequestInterface;
  public function getUri(): UriInterface;
  public function __toString(): string;
}
