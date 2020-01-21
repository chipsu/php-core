<?php

namespace metrica\core;

class Route implements RouteInterface {
  protected array $methods;
  protected string $pattern;
  protected $callback;
  protected ?string $regex = null;
  protected ?EventsInterface $events = null;
  protected ?DependsInterface $depends = null;

  public function __construct(array $methods, string $pattern, $callback, ?EventsInterface $events = null, ?DependsInterface $depends = null) {
    $this->methods = $methods;
    $this->pattern = $pattern;
    $this->callback = $callback;
    $this->events = $events;
    $this->depends = $depends;
  }

  public function getMethods(): array {
    return $this->methods;
  }

  public function getPattern(): string {
    return $this->pattern;
  }

  public function getRegex(): string {
    if($this->regex === null) {
      $this->regex = $this->compilePattern($this->pattern);
    }
    return $this->regex;
  }

  public function getCallback(): callable {
    if(is_string($this->callback)) {
      if(strpos($this->callback, '->') !== false) {
        [$class, $method] = explode('->', $this->callback);
        $instance = new $class;
        $this->callback = [$instance, $method];
      } else if(strpos($this->callback, '::') !== false) {
        $this->callback = explode('::', $this->callback);
      }
    }
    return $this->callback;
  }

  public function getMatch(RequestInterface $request): ?array {
    if(!in_array($request->getMethod(), $this->getMethods())) {
      return null;
    }

    if(!preg_match($this->getRegex(), $request->getUri()->getPath(), $match)) {
      return null;
    }

    foreach($match as $k => $v) {
      if(is_numeric($k)) {
        unset($match[$k]);
      }
    }

    return $match;
  }

  public function invokeRequest($request, array $extraParams = []) {
    $args = [];
    $callback = $this->getCallback();

    if($extraParams) {
      $request = clone $request;
      $request->setQueryParams(array_merge($request->getQueryParams(), $extraParams));
    }

    if(is_string($callback)) {
      $callback = $this->replaceRouteParams($callback, $request->getQueryParams());
    } else if(is_array($callback)) {
      $callback = array_map(function($item) use($request) {
        return is_string($item) ? $this->replaceRouteParams($item, $request->getQueryParams()) : $item;
      }, $callback);
    }

    if($this->depends) {
      $args = $this->depends->params($callback, array_merge($request->getQueryParams(), ['request' => $request]));
    } else {
      $args = [$request];
    }

    if($this->events) {
      $eventData = $this->events->data([
        'route' => $this,
        'request' => $request,
        'extraParams' => $extraParams,
        'events' => $this->events,
        'depends' => $this->depends,
        'args' => $args,
        'callback' => $callback,
        'returnValue' => null,
      ]);

      if(!$this->events->fire('beforeRoute', $eventData)) {
        return $eventData['returnValue'];
      }
    }

    $returnValue = call_user_func_array($callback, $args);

    if($this->events) {
      $eventData['returnValue'] = $returnValue;
      if(!$this->events->fire('afterRoute', $eventData)) {
        return $eventData['returnValue'];
      }
    }

    return $returnValue;
  }

  protected function compilePattern($source): string {
    $tokens = [];

    // Extract params
    $pattern = preg_replace_callback('/\<(?<param>[^\>]+)\>/', function($match) use(&$tokens) {
      $parts = explode(':', $match['param'], 2);
      $param = $parts[0];
      $regex = $parts[1] ?? '\w+';
      $token = '_@@@(' . $param . ')@@@_';
      $tokens[preg_quote($token)] = '(?<' . $param . '>' . $regex . ')';
      return $token;
    }, $source);

    // Escape slashes and stuff
    $pattern = '/^' . preg_quote($pattern, '/') . '$/';

    // Insert params
    $pattern = strtr($pattern, $tokens);

    // Verify regex
    // TODO: Don't do this in production
    try {
      preg_match($pattern, 'TEST');
    } catch(\Throwable $ex) {
      throw new Exception(sprintf('Error compiling pattern "%s"', $source), 0, $ex);
    }

    return $pattern;
  }

  protected function replaceRouteParams($string, array $params): string {
    return preg_replace_callback('/\<(?<param>\w+)\>/', function($match) use($params) {
      $key = $match['param'];
      if(empty($params[$key])) {
        throw new HttpException(500, sprintf('Invalid route param "%s"', $key));
      }
      return $params[$key];
    }, $string);
  }
}
