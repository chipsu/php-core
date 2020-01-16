<?php

namespace metrica\core;

interface UriInterface {
  public static function parse(string $uri): UriInterface;
  public static function fromEnv(EnvInterface $env): UriInterface;
  public function getScheme(): ?string;
  public function setScheme(?string $scheme): UriInterface;
  public function getHost(): ?string;
  public function setHost(?string $host): UriInterface;
  public function getPort(): ?int;
  public function setPort(?int $port): UriInterface;
  public function getPath(): string;
  public function setPath(string $path): UriInterface;
  public function getQuery(): string;
  public function setQuery(string $query): UriInterface;
  public function getQueryParams(): array;
  public function setQueryParams(array $queryParams): UriInterface;
  public function getQueryParam(string $key, $default = null);
  public function getFragment(): string;
  public function setFragment(string $fragment): UriInterface;
  public function getUsername(): string;
  public function setUsername(string $username): UriInterface;
  public function getPassword(): string;
  public function setPassword(string $password): UriInterface;
  public function __toString(): string;
}
