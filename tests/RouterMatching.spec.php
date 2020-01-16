<?php

use metrica\core\Router;
use metrica\core\Request;
use metrica\core\Uri;
use metrica\core\HttpException;

describe('Router', function() {
  beforeEach(function() {
    $this->router = new Router;
  });

  describe('Method matching', function() {
    it('should not match other methods than GET', function() {
      $router = $this->router;

      $router->get('/static/1', function() {
        return '/static/1';
      });

      $returnValue = $router->getRoute(new Request('GET', Uri::parse('/static/1')));
      assert($returnValue !== null);

      $returnValue = $router->getRoute(new Request('POST', Uri::parse('/static/1')));
      assert($returnValue === null);

      $returnValue = $router->getRoute(new Request('PUT', Uri::parse('/static/1')));
      assert($returnValue === null);

      $returnValue = $router->getRoute(new Request('PATCH', Uri::parse('/static/1')));
      assert($returnValue === null);

      $returnValue = $router->getRoute(new Request('DELETE', Uri::parse('/static/1')));
      assert($returnValue === null);
    });

    
  });
});

/* describe('Router With Reflection', function() {
  beforeEach(function() {
    $this->router = new Router(['kernel' => new Kernel(['components' => [
      'argumentGenerator' => ['class' => '\metrica\core\ArgumentGenerator', 'useReflection' => true],
    ]])]);
  });

  it('should match dynamic string', function() {
    $router = $this->router;

    $router->get('/dynamic/<id>', function($id) {
      return '/dynamic/' . $id;
    });

    $returnValue = $router->invokeRequest(new Request([
      'uri' => new Uri('/dynamic/1'),
    ]));
    assert($returnValue === '/dynamic/1');

    $returnValue = $router->invokeRequest(new Request([
      'uri' => new Uri('/dynamic/2'),
    ]));
    assert($returnValue === '/dynamic/2');
  });

  it('should match regex', function() {
    $router = $this->router;

    $router->get('/regex/<alpha:[a-z]+>', function($alpha) {
      return '/regex/' . $alpha;
    });

    $returnValue = $router->invokeRequest(new Request([
      'uri' => new Uri('/regex/moocow'),
    ]));
    assert($returnValue === '/regex/moocow');

    $returnValue = $router->invokeRequest(new Request([
      'uri' => new Uri('/regex/meowcat'),
    ]));
    assert($returnValue === '/regex/meowcat');
  });
});
 */
