<?php

require_once __DIR__ . '/../vendor/autoload.php';

use \metrica\core\Kernel;
use \metrica\core\Request;

$kernel = new Kernel([
  'init' => ['env', 'error'],
  'components' => [
    'db' =>
      'dsn' => 'sqlite:rest_api_example.sqlite',
      'class' => function($config) {
        return new PDO($config['dsn']);
      },
    ],
  ],
]);

$router = $kernel->getRouter();

$router->get('/', function() {
  return [
    'apiVersion' => 1,
    'endpoints' => ['posts'],
  ];
});

$router->get('/posts', function(PDO $db, Request $request, string $id) {
  $db->all();
  return [
    ['id' => 1, 'name' => 'Moo moo 1'],
    ['id' => 2, 'name' => 'Moo moo 2'],
  ];
});

$router->get('/posts/<id:\d+>', function(PDO $db, Request $request, string $id) {
  return [
    'id' => $id,
    'name' => 'Moo moo' . $id,
  ];
});

$router->post('/posts', function(PDO $db, Request $request, string $id) {
  $data = $request->getBodyParams();
  // insert($data, ['id' => $id])
  return $data;
});

$router->put('/posts/<id:\d+>', function($db, $request, $id) {
  $data = $request->getBodyParams();
  // insert($data, ['id' => $id])
  return $data;
});
