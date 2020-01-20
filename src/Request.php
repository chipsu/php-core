<?php

namespace metrica\core;

class Request implements RequestInterface
{
  const METHOD_GET = 'GET';
  const METHOD_POST = 'POST';
  const METHOD_PUT = 'PUT';
  const METHOD_PATCH = 'PATCH';
  const METHOD_DELETE = 'DELETE';
  const METHOD_OPTIONS = 'OPTIONS';
  const METHOD_HEAD = 'HEAD';

  protected string $method;
  protected UriInterface $uri;
  protected HeadersInterface $headers;
  protected ParamsInterface $cookies;
  protected ParamsInterface $serverParams;
  protected ?StreamInterface $body;
  protected ?string $bodyContent = null;
  protected ?array $bodyParams = null;
  protected array $bodyParsers;

  public static function fromEnv(EnvInterface $env): RequestInterface
  {
    return new static(
      $env->get('REQUEST_METHOD', static::METHOD_GET),
      Uri::fromEnv($env),
      Headers::fromEnv($env),
      new Params($_COOKIE),
      $env->getParams(),
      new FileStream('php://input')
    );
  }

  /*
        $method,
        UriInterface $uri,
        HeadersInterface $headers,
        array $cookies,
        array $serverParams,
        StreamInterface $body,
        array $uploadedFiles = []
        */
  public function __construct(string $method, UriInterface $uri, HeadersInterface $headers = null, ParamsInterface $cookies = null, ParamsInterface $serverParams = null, StreamInterface $body = null)
  {
    $this->method = $method;
    $this->uri = $uri;
    $this->headers = $headers ?? Headers::fromArray([]);
    $this->cookies = $cookies ?? new Params([]);
    $this->serverParams = $serverParams ?? new Params([]);
    $this->body = $body;
    $this->bodyParsers = [
      'application/json' => function(string $data, callable $errorHandler) {
        $json = new Json;
        return $json->decode($data, $errorHandler);
      },
      'application/x-www-form-urlencoded' => function(string $data, callable $errorHandler) {
        parse_str($data, $result);
        return $result;
      },
      'multipart/form-data' => function(string $data, callable $errorHandler) {
        parse_str($data, $result);
        print_r([$result, 'xxx']);die;
        return $result;
      },
    ];
  }

  public function getUri(): UriInterface
  {
    return $this->uri;
  }

  public function getMethod(): string
  {
    return $this->method;
  }

  public function getContentType(): ?string
  {
    return $this->getHeader('Content-Type');
  }

  public function getHeaders(): HeadersInterface
  {
    return $this->headers;
  }

  public function getHeader(string $name, string $default = null): ?string
  {
    return $this->getHeaders()->get($name, $default);
  }

  public function getBodyContent(): string
  {
    if($this->bodyContent === null && $this->body) {
      $this->bodyContent = $this->body->getContent();
    }
    return $this->bodyContent;
  }

  public function getQueryParams(): array
  {
    return $this->uri->getQueryParams();
  }

  public function setQueryParams(array $params): RequestInterface {
    $this->uri->setQueryParams($params);
    return $this;
  }

  public function getQueryParam(string $key, $default = null)
  {
    return $this->uri->getQueryParam($key, $default);
  }

  public function getQueryValues(array $keys): array
  {
    return $this->getQueryArray($keys, true);
  }

  public function getQueryArray(array $keys, bool $values = false): array
  {
    $result = [];
    $params = $this->getQueryParams();
    foreach($keys as $k => $v) {
      if(is_numeric($k)) {
        if(!isset($params[$v])) {
          throw new HttpException(403, sprintf('Missing param "%s"', $v));
        }
        $result[$k] = $params[$v];
      } else {
        $result[$k] = isset($params[$k]) ? $params[$k] : $v;
      }
    }
    return $values ? array_values($result) : $result;
  }

  public function getBodyParams(): array
  {
    if($this->bodyParams === null) {
      $contentType = $this->getContentType();
      if(isset($this->bodyParsers[$contentType])) {
        $parser = $this->bodyParsers[$contentType];
        $this->bodyParams = $parser($this->getBodyContent(), function($data, $error, $message) {
          throw new HttpException(400, 'Error parsing body: ' . $message);
        });
      } else if($this->getMethod() === static::METHOD_POST) {
        $this->bodyParams = $_POST; // FIXME
      } else {
        $this->bodyParams = [];
        mb_parse_str($this->getBodyContent(), $this->bodyParams);
      }
    }
    return $this->bodyParams;
  }

  public function getBodyParam(string $key, $default = null)
  {
    $params = $this->getBodyParams();
    return isset($params[$key]) ? $params[$key] : $default;
  }

  public function getParam(string $key, $default = null)
  {
    if($result = $this->getQueryParam($key)) {
      return $result;
    }
    return $this->getBodyParam($key, $default);
  }

  public function __toString(): string
  {
    try {
      if($this->uri instanceof UriInterface) {
        return (string)$this->uri;
      }
      return null;
    } catch(\Exception $ex) {
      Error::exceptionHandler($ex);
    }
  }
}
