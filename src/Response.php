<?php

namespace metrica\core;

class Response implements ResponseInterface {
  protected bool $bufferOutput;
  protected string $outputBuffer;

  public function __construct(bool $bufferOutput = false, string $outputBuffer = '') {
    $this->bufferOutput = $bufferOutput;
    $this->outputBuffer = $outputBuffer;
  }

  public function setStatus(int $status): ResponseInterface {
    http_response_code($status);
    return $this;
  }

  public function setContentType(string $contentType): ResponseInterface {
    return $this->setHeader('Content-Type', $contentType);
  }

  public function setHeader(string $key, string $value, bool $replace = true): ResponseInterface {
    header($key . ': ' . $value, $replace);
    return $this;
  }

  public function redirect(string $url, int $code = 302, bool $die = true): ResponseInterface {
    $this->setStatus($code);
    $this->setHeader('Location', $url);
    if($die) {
      die;
    }
    return $this;
  }

  public function write($data): ResponseInterface {
    if(is_int($data)) {
      $this->setStatus($data);
      $data = null;
    } else if(is_bool($data)) {
      $this->setStatus($data ? 204 : 500);
      $data = null;
    } else if(is_object($data) || is_array($data)) {
      $this->setContentType('application/json');
      $data = json_encode($data);
    }
    if($data !== null) {
      if($this->bufferOutput) {
        $this->outputBuffer .= (string)$data;
      } else {
        echo (string)$data;
      }
    }
    return $this;
  }

  public function send(): ResponseInterface {
    if(strlen($this->outputBuffer) > 0) {
      echo $this->outputBuffer;
    }
    return $this;
  }
}
