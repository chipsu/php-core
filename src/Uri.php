<?php

namespace metrica\core;

class Uri implements UriInterface {
  protected ?string $scheme;
  protected ?string $host;
  protected ?int $port;
  protected string $path;
  protected string $query;
  protected string $fragment;
  protected string $username;
  protected string $password;
  protected ?array $queryParams = null;

  public static function parse(string $uri): UriInterface {
    $array = parse_url($uri);

    if(!is_array($array)) {
      throw new \InvalidArgumentException('Invalid URI string: ' . $uri);
    }

    return new static(
      $array['scheme'] ?? null,
      $array['host'] ?? null,
      $array['port'] ?? null,
      $array['path'] ?? '',
      $array['query'] ?? '',
      $array['fragment'] ?? '',
      $array['user'] ?? '',
      $array['pass'] ?? '',
    );
  }

  public static function fromEnv(EnvInterface $env): UriInterface {
    return new Uri(
      $env->get('HTTPS') ? 'https' : 'http',
      $env->get('HTTP_HOST'),
      $env->get('SERVER_PORT'),
      $env->get('REQUEST_URI', ''),
      $env->get('QUERY_STRING', ''),
      '',
      $env->get('PHP_AUTH_USER', ''),
      $env->get('PHP_AUTH_PW', '')
    );
  }

  public function __construct(
    ?string $scheme,
    ?string $host,
    ?int $port = null,
    string $path = '/',
    string $query = '',
    string $fragment = '',
    string $username = '',
    string $password = ''
  ) {
    $this->scheme = $scheme;
    $this->host = $host;
    $this->port = $port;
    $this->path = $path;
    $this->query = $query;
    $this->fragment = $fragment;
    $this->username = $username;
    $this->password = $password;
  }

  public function getScheme(): ?string {
    return $this->scheme;
  }

  public function setScheme(?string $scheme): UriInterface {
    $this->scheme = $scheme;
    return $this;
  }

  public function getHost(): ?string {
    return $this->host;
  }

  public function setHost(?string $host): UriInterface {
    $this->host = $host;
    return $this;
  }

  public function getPort(): ?int {
    return $this->port;
  }

  public function setPort(?int $port): UriInterface {
    $this->port = $port;
    return $this;
  }

  public function getPath(): string {
    return $this->path;
  }

  public function setPath(string $path): UriInterface {
    $this->path = $path;
    return $this;
  }

  public function getQuery(): string {
    return $this->query;
  }

  public function setQuery(string $query): UriInterface {
    $this->query = $query;
    $this->queryParams = null;
    return $this;
  }

  public function getQueryParams(): array {
    if($this->queryParams === null) {
      parse_str($this->getQuery(), $this->queryParams);
    }
    return $this->queryParams;
  }

  public function setQueryParams(array $queryParams): UriInterface {
    $this->queryParams = $queryParams;
    $this->query = http_build_query($queryParams);
    return $this;
  }

  public function getQueryParam(string $key, $default = null) {
    $params = $this->getQueryParams();
    return isset($params[$key]) ? $params[$key] : $default;
  }

  public function getFragment(): string {
    return $this->fragment;
  }

  public function setFragment(string $fragment): UriInterface {
    $this->fragment = $fragment;
    return $this;
  }

  public function getUsername(): string {
    return $this->username;
  }

  public function setUsername(string $username): UriInterface {
    $this->username = $username;
    return $this;
  }

  public function getPassword(): string {
    return $this->password;
  }

  public function setPassword(string $password): UriInterface {
    $this->password = $password;
    return $this;
  }

  public function __toString(): string {
    try {
      $scheme = $this->getScheme();
      $result = $scheme ? $scheme . '://' : '//';

      $username = $this->getUsername();
      $password = $this->getPassword();

      if($username || $password) {
        $result .= $username . ':' . $password . '@';
      }

      $result .= $this->getHost();

      if($port = $this->getPort()) {
        if(!$scheme || $port !== Env::getDefaultPort($scheme)) {
          $result .= ':' . $port;
        }
      }

      if($path = $this->getPath()) {
        $result .= $path[0] === '/' ? $path : '/' . $path;
      }

      if($query = $this->getQuery()) {
        $result .= '?' . $query;
      }

      if($fragment = $this->getFragment()) {
        $result .= '#' . $fragment;
      }

      return $result;
    } catch(\Exception $ex) {
      Error::exceptionHandler($ex);
    }
  }
}
