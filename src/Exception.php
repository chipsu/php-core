<?php

namespace metrica\core;

class Exception extends \Exception
{
  public static function defaultResponseData(\Exception $exception): array
  {
    return [
      'code' => $exception->getCode(),
      'message' => $exception->getMessage(),
      'file' => basename($exception->getFile()),
      'line' => $exception->getLine(),
    ];
  }

  public function getResponseData(): array
  {
    return static::defaultResponseData($exception);
  }
}
