<?php

use metrica\core\Uri;
use metrica\core\UriInterface;
use metrica\core\Env;

describe('Uri', function() {
  beforeEach(function() {
    $this->uri = new Uri(
      'https',
      'www.example.com',
      1337,
      '/hello/world',
      http_build_query(['foo' => 'bar', 'moo' => 'cow']),
      'fragment',
      'hacker',
      'secret'
    );
  });

  describe('Basics', function() {
    it('should implement interface', function() {
      assert($this->uri instanceof UriInterface);
    });
  });

  describe('Getters', function() {
    it('should return correct scheme', function() {
      assert($this->uri->getScheme() === 'https');
    });

    it('should return correct host', function() {
      assert($this->uri->getHost() === 'www.example.com');
    });

    it('should return correct port', function() {
      assert($this->uri->getPort() === 1337);
    });

    it('should return correct path', function() {
      assert($this->uri->getPath() === '/hello/world');
    });

    it('should return correct fragment', function() {
      assert($this->uri->getFragment() === 'fragment');
    });

    it('should return correct username', function() {
      assert($this->uri->getUsername() === 'hacker');
    });

    it('should return correct password', function() {
      assert($this->uri->getPassword() === 'secret');
    });
  });

  describe('String conversion', function() {
    it('should convert to string correctly', function() {
      assert((string)$this->uri === 'https://hacker:secret@www.example.com:1337/hello/world?foo=bar&moo=cow#fragment');
    });

    it('should convert to string correctly without username and password', function() {
      $this->uri->setUsername('');
      $this->uri->setPassword('');
      assert((string)$this->uri === 'https://www.example.com:1337/hello/world?foo=bar&moo=cow#fragment');
    });

    it('should not include default port', function() {
      $this->uri->setPort(443);
      assert((string)$this->uri === 'https://hacker:secret@www.example.com/hello/world?foo=bar&moo=cow#fragment');
    });

    it('should not include default port', function() {
      $this->uri->setScheme('http');
      $this->uri->setPort(80);
      assert((string)$this->uri === 'http://hacker:secret@www.example.com/hello/world?foo=bar&moo=cow#fragment');
    });

    it('should not include scheme', function() {
      $this->uri->setScheme('');
      assert((string)$this->uri === '//hacker:secret@www.example.com:1337/hello/world?foo=bar&moo=cow#fragment');
    });
  });
});

describe('Uri::fromEnv', function() {
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
    ]);
    $this->uri = Uri::fromEnv($env);
  });

  describe('Basics', function() {
    it('should implement interface', function() {
      assert($this->uri instanceof UriInterface);
    });
  });

  describe('String conversion', function() {
    it('should convert to string correctly', function() {
      assert((string)$this->uri === 'https://hacker:secret@www.example.com:1337/hello/world?foo=bar&moo=cow');
    });

    it('should convert to string correctly without username and password', function() {
      $this->uri->setUsername('');
      $this->uri->setPassword('');
      assert((string)$this->uri === 'https://www.example.com:1337/hello/world?foo=bar&moo=cow');
    });

    it('should not include default port', function() {
      $this->uri->setPort(443);
      assert((string)$this->uri === 'https://hacker:secret@www.example.com/hello/world?foo=bar&moo=cow');
    });

    it('should not include default port', function() {
      $this->uri->setScheme('http');
      $this->uri->setPort(80);
      assert((string)$this->uri === 'http://hacker:secret@www.example.com/hello/world?foo=bar&moo=cow');
    });
  });
});

describe('Uri::parse', function() {
  beforeEach(function() {
    $this->uri = Uri::parse('https://hacker:secret@www.example.com:1337/hello/world?foo=bar&moo=cow#fragment');
  });

  describe('Basics', function() {
    it('should implement interface', function() {
      assert($this->uri instanceof UriInterface);
    });
  });

  describe('String conversion', function() {
    it('should convert to string correctly', function() {
      assert((string)$this->uri === 'https://hacker:secret@www.example.com:1337/hello/world?foo=bar&moo=cow#fragment');
    });

    it('should convert to string correctly without username and password', function() {
      $this->uri->setUsername('');
      $this->uri->setPassword('');
      assert((string)$this->uri === 'https://www.example.com:1337/hello/world?foo=bar&moo=cow#fragment');
    });

    it('should not include default port', function() {
      $this->uri->setPort(443);
      assert((string)$this->uri === 'https://hacker:secret@www.example.com/hello/world?foo=bar&moo=cow#fragment');
    });

    it('should not include default port', function() {
      $this->uri->setScheme('http');
      $this->uri->setPort(80);
      assert((string)$this->uri === 'http://hacker:secret@www.example.com/hello/world?foo=bar&moo=cow#fragment');
    });
  });
});
