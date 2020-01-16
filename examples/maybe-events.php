<?php

require_once __DIR__ . '/../vendor/autoload.php';

class Controller
{
  public function actionDemo()
  {
    return ['__METHOD__' => __METHOD__];
  }

  public function actionHandler($query)
  {
    if($this->beforeAction($query['action'])) {
      $result = $this->$query['action']();
      $this->afterAction($query['action']);
      return $result;
    }
    return false;
  }
}

$kernel = new \metrica\core\Kernel([
  'initComponents' => ['env', 'error'],
]);

$router = $kernel->getRouter();

$router->get('/', function() {
  return '<a href="/demo/123">demo</a>';
});

$router->get('/<action>/<id>', 'Controller->actionHandler');
