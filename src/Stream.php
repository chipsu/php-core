<?php

namespace metrica\core;

class Stream implements StreamInterface {
  protected $handle;

  public function __construct($handle = null) {
    assert($handle === null || is_resource($handle));
    $this->handle = $handle;
  }

  public function getHandle() {
    return $this->handle;
  }

  public function getContent(): string {
    return stream_get_contents($this->getHandle());
  }
}
