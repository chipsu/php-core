<?php

use metrica\core\Router;
use metrica\core\RouterInterface;
use metrica\core\Request;
use metrica\core\Uri;
use metrica\core\Params;
use metrica\core\Headers;
use metrica\core\RequestInterface;
use metrica\core\StringStream;
use metrica\core\Depends;

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

class Dummy {
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

describe('Router with DI', function() {
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
    $this->depends = new Depends([
      'dummy' => Dummy::class,
    ]);
    $this->router = new Router([], null, null, null, $this->depends);
    $this->router->get('/foo/bar', function() {
      return 'FooBar';
    });
  });

  require 'RouterInvokation.inc.php';

  describe('Closure invokation', function() {
    it('should provide $request', function() {
      $this->router->get('/index', function(RequestInterface $request) {
        assert($request instanceof RequestInterface);
        return 'expectedReturnValue';
      });
      $route = $this->router->getRoute($this->request);
      assert($route !== null);
      $returnValue = $route->invokeRequest($this->request);
      assert($returnValue === 'expectedReturnValue');
    });

    it('should provide $dummy', function() {
      $this->router->get('/index', function(Dummy $dummy) {
        assert($dummy instanceof Dummy);
        return 'expectedReturnValue';
      });
      $route = $this->router->getRoute($this->request);
      assert($route !== null);
      $returnValue = $route->invokeRequest($this->request);
      assert($returnValue === 'expectedReturnValue');
    });

    it('should provide $request and $dummy', function() {
      $this->router->get('/index', function(RequestInterface $request, Dummy $dummy) {
        assert($request instanceof RequestInterface);
        assert($dummy instanceof Dummy);
        return 'expectedReturnValue';
      });
      $route = $this->router->getRoute($this->request);
      assert($route !== null);
      $returnValue = $route->invokeRequest($this->request);
      assert($returnValue === 'expectedReturnValue');
    });
  });
});
