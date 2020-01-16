<?php

namespace metrica\core;

interface StreamInterface {
  public function getHandle();
  public function getContent(): string;
}
