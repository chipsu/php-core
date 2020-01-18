<?php

namespace metrica\core;

# TODO: FIX
interface RouterInterface {
  public function get(string $pattern, $callback): RouterInterface;
  public function post(string $pattern, $callback): RouterInterface;
  public function put(string $pattern, $callback): RouterInterface;
  public function delete(string $pattern, $callback): RouterInterface;
  public function patch(string $pattern, $callback): RouterInterface;
  public function on(array $methods, string $pattern, $callback): RouterInterface;
  public function getRoutes(): array;
  public function addRoute(RouteInterface $route): RouterInterface;
  public function setRoutes(array $routes): RouterInterface;
  public function getRoute(RequestInterface $request): ?RouteInterface;
  public function getRouteMatch(RequestInterface $request): ?array;
  public function invokeRequest(RequestInterface $request, array $extraParams = [], ?int $throwHttpError = 404, $default = null);
}
