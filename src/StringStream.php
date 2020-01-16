<?php

namespace metrica\core;

class StringStream extends Stream {
  public function __construct(string $content) {
    $handle = fopen('php://memory', 'w+');
    fwrite($handle, $content);
    rewind($handle);
    parent::__construct($handle);
  }
}
