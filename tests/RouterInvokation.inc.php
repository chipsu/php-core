<?php

use metrica\core\Request;
use metrica\core\Uri;

describe('Closure invokation', function() {
  it('should return correct string', function() {
    $this->router->get('/index', function($request) {
      return 'expectedReturnValue';
    });
    $route = $this->router->getRoute($this->request);
    assert($route !== null);
    $returnValue = $route->invokeRequest($this->request);
    assert($returnValue === 'expectedReturnValue');
  });
});

describe('Static invokation', function() {
  it('should return correct string', function() {
    $this->router->get('/index', 'RouterInvokationTestController::staticTest');
    $route = $this->router->getRoute($this->request);
    assert($route !== null);
    $returnValue = $route->invokeRequest($this->request);
    assert($returnValue === 'expectedReturnValue');
  });
});

describe('Dynamic invokation', function() {
  it('should return correct string', function() {
    $this->router->get('/index', 'RouterInvokationTestController->dynamicTest');
    $route = $this->router->getRoute($this->request);
    assert($route !== null);
    $returnValue = $route->invokeRequest($this->request);
    assert($returnValue === 'expectedReturnValue');
  });
});

describe('Instance invokation', function() {
  it('should return correct string', function() {
    $this->router->get('/index', [new RouterInvokationTestController, 'instanceTest']);
    $route = $this->router->getRoute($this->request);
    assert($route !== null);
    $returnValue = $route->invokeRequest($this->request);
    assert($returnValue === 'expectedReturnValue');
  });
});
