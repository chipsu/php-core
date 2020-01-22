<?php

namespace metrica\core;

class Bootstrap
{
  public static $defaultDependencies = [
    'env' => [Env::class, 'fromPhp'],
    'app' => App::class,
    'router' => Router::class,
    'request' => [Request::class, 'fromEnv'],
    'response' => Response::class,
  ];

  public static function di(array $extraDependencies = [])
  {
    $di = new Depends(array_merge(static::$defaultDependencies, $extraDependencies));
    $di->addInstance('depends', $di);
    return $di;
  }
}
