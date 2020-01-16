<?php

namespace metrica\core;

class Params implements ParamsInterface
{
  protected array $params;
  protected ?int $case;

  public function __construct(array $params, ?int $case = null) {
    $this->params = $case !== null ? array_change_key_case($params, $case) : $params;
    $this->case = $case;
  }

  public function all(): array {
    return $this->params;
  }

  public function get(string $key, $default = null) {
    if($this->case !== null) {
      switch($this->case) {
      case \CASE_LOWER:
        $key = strtolower($key);
        break;
      case \CASE_UPPER:
        $key = strtoupper($key);
        break;
      }
    }
    return isset($this->params[$key]) ? $this->params[$key] : $default;
  }
}
