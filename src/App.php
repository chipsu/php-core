<?php

namespace metrica\core;

class App implements AppInterface {
  protected RouterInterface $router;
  protected RequestInterface $request;
  protected ResponseInterface $response;
  protected ?EventsInterface $events;

  public function __construct(RouterInterface $router, RequestInterface $request, ResponseInterface $response, ?EventsInterface $events = null) {
    $this->router = $router;
    $this->request = $request;
    $this->response = $response;
    $this->events = $events;
  }

  public function run() {
    try {
      if($this->events) {
        $eventData = $this->events->data([
          'app' => $this,
          'router' => $this->router,
          'request' => $this->request,
          'response' => $this->response,
          'events' => $this->events,
          'responseData' => null,
        ]);

        if(!$this->events->fire('beforeRequest', $eventData)) {
          return $eventData['responseData'];
        }
      }

      $responseData = $this->router->invokeRequest($this->request);

      if($this->events) {
        $eventData['responseData'] = $responseData;
        $this->events->fire('afterRequest', $eventData);
      }

      if($responseData !== null) {
        $this->response->write($responseData);
      }
    } catch(HttpException $error) {
      if($this->events) {
        $eventData = $this->events->data([
          'app' => $this,
          'router' => $this->router,
          'request' => $this->request,
          'response' => $this->response,
          'events' => $this->events,
          'error' => $error,
        ]);
        $this->events->fire('error', $eventData);
      }
      throw $error;
    }
  }
}
