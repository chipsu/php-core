<?php

use metrica\core\Bootstrap;
use metrica\core\EnvInterface;
use metrica\core\RouterInterface;
use metrica\core\RequestInterface;
use metrica\core\ResponseInterface;

describe('Bootstrap', function() {
  beforeEach(function() {
    $this->bootstrap = Bootstrap::di();
  });

  describe('should have a', function() {
    it('env', function() {
      assert($this->bootstrap->get('env') instanceof EnvInterface);
    });
    it('router', function() {
      assert($this->bootstrap->get('router') instanceof RouterInterface);
    });
    it('request', function() {
      assert($this->bootstrap->get('request') instanceof RequestInterface);
    });
    it('response', function() {
      assert($this->bootstrap->get('response') instanceof ResponseInterface);
    });
  });
});
