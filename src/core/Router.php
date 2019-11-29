<?php

// Router maps URI to defined callable (Controller@Action)

namespace Clara\core;

class Router
{
  protected $request;

  public function __construct(Request $request)
  {
    $this->request = $request;
  }
}
