<?php

namespace metrica\core;

class Json implements ParserInterface
{
  public function encode(object $data): string
  {
    return json_encode($data);
  }

  public function decode(string $data, callable $errorHandler): array
  {
    $result = json_decode($data, true);
    $error = json_last_error();
    if($error !== JSON_ERROR_NONE) {
      return $errorHandler($data, $error, json_last_error_msg());
    }
    return $result;
  }
}
