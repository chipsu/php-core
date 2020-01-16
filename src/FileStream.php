<?php

namespace metrica\core;

class FileStream extends Stream {
  protected string $fileName;

  public function __construct(string $fileName) {
    $this->fileName = $fileName;
  }

  public function getHandle() {
    if($this->handle === null) {
      $this->handle = fopen($this->fileName, 'r');
    }
    return parent::getHandle();
  }
}
