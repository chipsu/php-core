<?php

use metrica\core\Request;
use metrica\core\RequestInterface;
use metrica\core\Env;
use metrica\core\Uri;
use metrica\core\Headers;
use metrica\core\StringStream;
use metrica\core\Params;

describe('Request with application/json', function() {
  beforeEach(function() {
    $uri = new Uri(
      'https',
      'www.example.com',
      1337,
      '/hello/world',
      http_build_query(['foo' => 'bar', 'moo' => 'cow']),
      'fragment',
      'hacker',
      'secret'
    );
    $headers = Headers::fromArray([
      'HTTP_HOST' => 'www.example.com',
      'HTTP_REFERER' => 'www.example.com',
      'HTTPS' => 'on',
      'QUERY_STRING' => 'foo=bar&moo=cow',
      'REQUEST_URI' => '/hello/world',
      'SERVER_PORT' => '1337',
      'PHP_AUTH_USER' => 'hacker',
      'PHP_AUTH_PW' => 'secret',
      'HTTP_CONTENT_TYPE' => 'application/json',
    ]);
    $body = new StringStream(json_encode(['hello' => 'world']));
    $cookies = new Params([]);
    $serverParams = new Params([]);
    $this->request = new Request(Request::METHOD_GET, $uri, $headers, $cookies, $serverParams, $body);
  });

  describe('Basics', function() {
    it('should implement interface', function() {
      assert($this->request instanceof RequestInterface);
    });
  });

  describe('Body parsing', function() {
    it('should parse JSON', function() {
      assert($this->request->getBodyParam('hello') === 'world');
    });

    it('should parse querystring', function() {
      assert($this->request->getQueryParam('foo') === 'bar');
    });
  });
});

describe('Request with POST data', function() {
  beforeEach(function() {
    $uri = new Uri(
      'https',
      'www.example.com',
      1337,
      '/hello/world',
      http_build_query(['foo' => 'bar', 'moo' => 'cow']),
      'fragment',
      'hacker',
      'secret'
    );
    $headers = Headers::fromArray([
      'HTTP_HOST' => 'www.example.com',
      'HTTP_REFERER' => 'www.example.com',
      'HTTPS' => 'on',
      'QUERY_STRING' => 'foo=bar&moo=cow',
      'REQUEST_URI' => '/hello/world',
      'SERVER_PORT' => '1337',
      'PHP_AUTH_USER' => 'hacker',
      'PHP_AUTH_PW' => 'secret',
      'HTTP_CONTENT_TYPE' => 'application/x-www-form-urlencoded',
    ]);
    $body = new StringStream(http_build_query(['hello' => 'world']));
    $cookies = new Params([]);
    $serverParams = new Params([]);
    $this->request = new Request(Request::METHOD_POST, $uri, $headers, $cookies, $serverParams, $body);
  });

  describe('Basics', function() {
    it('should implement interface', function() {
      assert($this->request instanceof RequestInterface);
    });
  });

  describe('Body parsing', function() {
    it('should parse POST-data', function() {
      assert($this->request->getBodyParam('hello') === 'world');
    });

    it('should parse querystring', function() {
      assert($this->request->getQueryParam('foo') === 'bar');
    });
  });
});

/* describe('Request with multipart/form-data', function() {
  beforeEach(function() {
    $uri = new Uri(
      'https',
      'www.example.com',
      1337,
      '/hello/world',
      http_build_query(['foo' => 'bar', 'moo' => 'cow']),
      'fragment',
      'hacker',
      'secret'
    );
    $headers = Headers::fromArray([
      'HTTP_HOST' => 'www.example.com',
      'HTTP_REFERER' => 'www.example.com',
      'HTTPS' => 'on',
      'QUERY_STRING' => 'foo=bar&moo=cow',
      'REQUEST_URI' => '/hello/world',
      'SERVER_PORT' => '1337',
      'PHP_AUTH_USER' => 'hacker',
      'PHP_AUTH_PW' => 'secret',
      'HTTP_CONTENT_TYPE' => 'multipart/form-data',
    ]);
    $body = new StringStream(
"
--boundary
Content-Disposition: form-data; name=\"hello\"

world
--boundary
Content-Disposition: form-data; name=\"field2\"

value2
"
    );
    $cookies = new Params([]);
    $serverParams = new Params([]);
    $this->request = new Request(Request::METHOD_POST, $uri, $headers, $cookies, $serverParams, $body);
  });

  describe('Basics', function() {
    it('should implement interface', function() {
      assert($this->request instanceof RequestInterface);
    });
  });

  describe('Body parsing', function() {
    it('should parse POST-data', function() {
      assert($this->request->getBodyParam('hello') === 'world');
    });

    it('should parse querystring', function() {
      assert($this->request->getQueryParam('foo') === 'bar');
    });
  });
}); */

describe('Request::fromEnv', function() {
  beforeEach(function() {
    $env = Env::fromArray([
      'HTTP_HOST' => 'www.example.com',
      'HTTP_REFERER' => 'www.example.com',
      'HTTPS' => 'on',
      'QUERY_STRING' => 'foo=bar&moo=cow',
      'REQUEST_URI' => '/hello/world',
      'SERVER_PORT' => '1337',
      'PHP_AUTH_USER' => 'hacker',
      'PHP_AUTH_PW' => 'secret',
      'HTTP_TEST_HEADER' => 'test-header-value',
    ]);
    $this->request = Request::fromEnv($env);
  });

  describe('Basics', function() {
    it('should implement interface', function() {
      assert($this->request instanceof RequestInterface);
    });

    it('should set correct headers', function() {
      assert($this->request->getHeader('Test-Header') === 'test-header-value');
    });

    it('should not care about case for headers', function() {
      assert($this->request->getHeader('test-header') === 'test-header-value');
    });
  });
});
