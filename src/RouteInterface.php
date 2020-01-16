<?php

namespace metrica\core;

# TODO: FIX
interface RouteInterface {
  public function getMethods(): array;
  public function getPattern(): string;
  public function getRegex(): string;
  public function getCallback(): callable;
  public function getMatch(RequestInterface $request): ?array;
  public function invokeRequest($request, array $extraParams = []); // TODO: Not sure if we want this here, maybe move this and event stuff to it's own thing?
}
