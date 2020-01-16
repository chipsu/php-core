<?php

namespace metrica\core;

class Router implements RouterInterface {
  protected array $routes;
  protected string $routeClass;
  protected ?CacheInterface $cache;

  public function __construct(array $routes = [], ?string $routeClass = null, ?CacheInterface $cache = null) {
    $this->routes = $routes;
    $this->routeClass = $routeClass ?? __NAMESPACE__ . '\Route';
    $this->cache = $cache;
  }

  public function get(string $pattern, $callback): RouterInterface {
    return $this->on(['GET'], $pattern, $callback);
  }

  public function post(string $pattern, $callback): RouterInterface {
    return $this->on(['POST'], $pattern, $callback);
  }

  public function put(string $pattern, $callback): RouterInterface {
    return $this->on(['PUT'], $pattern, $callback);
  }

  public function delete(string $pattern, $callback): RouterInterface {
    return $this->on(['DELETE'], $pattern, $callback);
  }

  public function patch(string $pattern, $callback): RouterInterface {
    return $this->on(['PATCH'], $pattern, $callback);
  }

  public function on(array $methods, string $pattern, $callback): RouterInterface {
    $class = $this->routeClass;
    $this->routes[] = new $class($methods, $pattern, $callback);
    return $this;
  }

  public function getRoutes(): array {
    return $this->routes;
  }

  public function addRoute(RouteInterface $route): RouterInterface {
    $this->routes[] = $route;
    return $this;
  }

  public function setRoutes(array $routes): RouterInterface {
    $this->routes = $routes;
    return $this;
  }

  public function getRoute(RequestInterface $request): ?RouteInterface {
    if($match = $this->getRouteMatch($request)) {
      return $match['route'];
    }
    return null;
  }

  public function getRouteMatch(RequestInterface $request): ?array {
    if($this->cache) {
      return $this->cache->get($request, function() use($request) {
        return $this->matchRoute($request);
      });
    }
    return $this->matchRoute($request);
  }

  protected function matchRoute(RequestInterface $request): ?array {
    foreach($this->getRoutes() as $route) {
      $match = $route->getMatch($request);
      if($match !== null) {
        return [
          'route' => $route,
          'match' => $match,
        ];
      }
    }
    return null;
  }

  // TODO: DI extra args?
  public function invokeRequest(RequestInterface $request, ?int $throwHttpError = 404, $default = null) {
    $routeMatch = $this->getRouteMatch($request);

    if(!$routeMatch) {
      if($throwHttpError) {
        throw new HttpException($throwHttpError);
      } else {
        return $default;
      }
    }

    return $routeMatch['route']->invokeRequest($request, $routeMatch['match']);
  }
}
