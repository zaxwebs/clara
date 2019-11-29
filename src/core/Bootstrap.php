<?php

// bootstraps/initializes the application

namespace Clara\core;

class Bootstrap
{
  private $router;
  public function __construct(Router $router)
  {
    $this->router = $router;
    $this->router->dispatch();
  }
}
