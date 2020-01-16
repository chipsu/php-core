<?php

namespace metrica\core;

interface ParserInterface
{
  public function encode(object $data): string;
  public function decode(string $data, callable $errorHandler): array;
}
