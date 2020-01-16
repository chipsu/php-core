<?php

class ComplexityTest
{
  public function testCallDepth()
  {
    $kernel = new Kernel;
    $router = new Router(['kernel' => $kernel]);

    $router->get('/no-spaghet', function() {
      return count(debug_backtrace());
    });

    $returnValue = $router->invokeRequest(new Request([
      'uri' => new Uri('/no-spaghet'),
    ]));
    $this->assertLessThan($returnValue, count(debug_backtrace()) + 5);
  }
}
