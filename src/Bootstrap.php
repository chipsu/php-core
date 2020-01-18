<?php

use metrica\core\Depends;
use metrica\core\Router;
use metrica\core\Request;
use metrica\core\Response;
use metrica\core\Env;

namespace metrica\core;

class App {
  protected $router;
  protected $request;
  protected $response;
  protected $events;

  public function __construct($router, $request, $response, $events = null) {
    $this->router = $router;
    $this->request = $request;
    $this->response = $response;
    $this->events = $events;
  }

  public function run() {
    try {
      $responseData = $this->router->invokeRequest($this->request);

      if($this->events) {
        $this->events->fire('run', [
          'responseData' => &$responseData,
        ]);
      }

      if($responseData !== null) {
        $this->response->write($responseData);
      }
    } catch(HttpException $error) {
      $this->response->setStatus($error->getHttpStatus())->write($error->getMessage());
    }
  }
}

class Bootstrap {
  public static $defaultDependencies = [
    'env' => [Env::class, 'fromPhp'],
    'app' => App::class,
    'router' => Router::class,
    'request' => [Request::class, 'fromEnv'],
    'response' => Response::class,
  ];

  public function di(array $extraDependencies = []) {
    $di = new Depends(array_merge(static::$defaultDependencies, $extraDependencies));
    $di->addInstance('depends', $di);
    return $di;
  }
}
