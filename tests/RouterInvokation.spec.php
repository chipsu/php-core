<?php

use metrica\core\Router;
use metrica\core\RouterInterface;
use metrica\core\Request;
use metrica\core\Uri;
use metrica\core\Params;
use metrica\core\Headers;
use metrica\core\StringStream;

class RouterInvokationTestController {
  public static function staticTest(Request $request): string {
    return 'expectedReturnValue';
  }

  public function dynamicTest(Request $request): string {
    return 'expectedReturnValue';
  }

  public function instanceTest(Request $request): string {
    return 'expectedReturnValue';
  }

  public function reflectionTest(string $param): string {
    return $param;
  }
}

describe('Router', function() {
  beforeEach(function() {
    $uri = new Uri(
      'https',
      'www.example.com',
      null,
      '/index',
    );
    $headers = Headers::fromArray([
      'HTTP_CONTENT_TYPE' => 'application/json',
    ]);
    $body = new StringStream(json_encode(['hello' => 'world']));
    $cookies = new Params([]);
    $serverParams = new Params([]);
    $this->request = new Request(Request::METHOD_GET, $uri, $headers, $cookies, $serverParams, $body);
    $this->router = new Router;
    $this->router->get('/foo/bar', function() {
      return 'FooBar';
    });
  });

  require 'RouterInvokation.inc.php';
});

/* describe('Router with reflection', function() {
  beforeEach(function() {
    $this->router = new Router([
      'argumentGenerator' => new ArgumentGenerator([
        'useReflection' => true,
        # kernel ok here maybe?
        # ($db, $json), FactoryInterface
      ]),
    ]);
  });

  require 'RouterInvokation.inc.php';

  // TODO: Reflection specific tests here
}); */
