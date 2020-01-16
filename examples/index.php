<?php

namespace demo1;

require_once '../vendor/autoload.php';

$kernel = new \metrica\core\Kernel([
  'init' => ['env', 'error'],
]);
$router = $kernel->getRouter();


/**
 * There's no controller class, but it's easy to create one.
 */
class Controller extends \metrica\core\Component
{
  protected $events = true;

  public function init()
  {
    if($events = $this->getEvents()) {
      $events->on('beforeInvoke', function($event) {
        $event->params['returnValue'] = ['qqq'];
        #return false;
      });

      $events->on('afterInvoke', function($event) {
        $event->params['returnValue'] = ['qqq'];
        #return false;
      });
    }
  }

  public function run($query)
  {
    return ['__METHOD__' => __METHOD__, 'query' => $query, 'd' => count(debug_backtrace())];
  }

  public static function runStatic($query)
  {
    return ['__METHOD__' => __METHOD__, 'query' => $query, 'd' => count(debug_backtrace())];
  }
}


// Dynamic instance
$router->get('/<action:\w+>/<id:\d+>', '\demo1\Controller->run');

// Static call
$router->get('/<action>/<id>', '\demo1\Controller::runStatic');

// Static action
$router->get('/test2_1/<action>/<id>', '\demo1\Controller::<action>');

// Dynamic action
$router->get('/test2_2/<action>/<id>', '\demo1\Controller-><action>');



$router->get('/test3/<action>/<id>', function($request) {
  [$action, $id] = $request->getQueryArray(['action', 'id']);
  return ['time' => time(), 'action' => $action, 'id' => $id, 'query' => $request->getQueryParams(), 'd' => count(debug_backtrace())];
});

/* $kernel->getEvents()->on('run', function($event) {
  if(is_array($event->params['responseData'])) {
    $event->params['responseData']['pooo'] = 'qq';
  }
}); */

$kernel->run();
