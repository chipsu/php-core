<?php

namespace metrica\core;

class Headers extends Params implements HeadersInterface {
  protected array $headers;

  public static function fromArray(array $params): HeadersInterface {
    $result = [];
    foreach($params as $key => $value) {
      if(substr($key, 0, 5) === 'HTTP_') {
        $result[str_replace(['_', ' '], '-', substr($key, 5))] = $value;
      }
    }
    return new static($result, \CASE_LOWER);
  }

  public static function fromEnv(EnvInterface $env): HeadersInterface {
    return static::fromArray($env->all());
  }

  # TODO: FIX
 /*  public function __construct(array $headers) {
    $this->headers = array_change_key_case($headers, \CASE_LOWER);
  }

  public function all(): array {
    return $this->headers;
  }

  public function get(string $key, ?string $default = null, bool $strip = false): ?string {
    // TODO: Force Casel-Case?
    $key = strtolower($key);
    $result = isset($this->headers[$key]) ? $this->headers[$key] : $default;
    // TODO: I'm not sure why this exists..
    if($strip && $result) {
      $result = explode(';', $result);
      $result = explode(',', $result[0]);
    }
    return $result;
  } */
}
