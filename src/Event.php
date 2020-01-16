<?php

namespace metrica\core;

class Event extends Container
{
  public $params;
  protected $sender;
  protected $cancel;

  public function getSender() : Component
  {
    return $this->sender;
  }

  public function getParams() : iterable
  {
    return $this->params;
  }

  public function setParams(iterable $value)
  {
    $this->params = $value;
  }
}
