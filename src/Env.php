<?php

namespace metrica\core;

class Env implements EnvInterface {
  protected ParamsInterface $params;

  public static array $defaultPorts = array(
    'http' => 80,
    'https' => 443,
  );

  public static function getDefaultPort(string $scheme): int
  {
    return static::$defaultPorts[$scheme];
  }

  public static function fromPhp(): EnvInterface {
    return static::fromArray($_ENV + $_SERVER);
  }

  public static function fromArray(array $params): EnvInterface {
    $params = static::normalizeEnvParams($params);
    return new static(new Params($params));
  }

  protected static function normalizeEnvParams(array $params): array
  {
    if(isset($params['HTTPS'])) {
      $params['HTTPS'] = !empty($params['HTTPS']) && $params['HTTPS'] !== 'off';
    }
    if(isset($params['HTTP_HOST']) && strpos($params['HTTP_HOST'], ':') !== false) {
      $host = explode(':', $params['HTTP_HOST']);
      $params['HTTP_HOST'] = $host[0];
      if(empty($params['SERVER_PORT'])) {
        $params['SERVER_PORT'] = $host[1];
      }
    }
    return $params;
  }

  public function __construct(ParamsInterface $params)
  {
    $this->params = $params;
  }

  public function getParams(): ParamsInterface
  {
    return $this->params;
  }

  public function all(): array
  {
    return $this->getParams()->all();
  }

  public function get(string $key, $default = null)
  {
    return $this->getParams()->get($key, $default);
  }

  /* public $file = '.env';
  public $defines = [
    'HI_ENV' => 'production',
    'HI_DEBUG' => false,
  ];

  public function init()
  {
    if(is_file($this->file)) {
      $this->load($this->file);
    }

    foreach($this->defines as $key => $default) {
      !defined($key) && define($key, $this->get($key, $default));
    }
  }

  public function get($key, $default = null)
  {
    if(isset($_ENV[$key])) {
      return $_ENV[$key];
    }
    return getenv($key) ?? $default;
  }

  public function load($file)
  {
    foreach(file($file) as $line) {
      $line = trim($line);
      if($line && $line[0] != '#') {
        $parts = explode('=', $line, 2);
        $_ENV[$parts[0]] = count($parts) > 1 ? $parts[1] : true;
      }
    }
  } */
}
