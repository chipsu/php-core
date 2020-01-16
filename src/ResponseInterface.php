<?php

namespace metrica\core;

# TODO: FIX
interface ResponseInterface {
  public function setStatus(int $status): ResponseInterface;
  public function setContentType(string $contentType): ResponseInterface;
  public function setHeader(string $key, string $value, bool $replace = true): ResponseInterface;
  public function redirect(string $url, int $code = 302, bool $die = true): ResponseInterface;
  public function write($data): ResponseInterface;
  public function send(): ResponseInterface;
}
