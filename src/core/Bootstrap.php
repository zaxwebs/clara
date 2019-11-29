<?php

// bootstraps/initializes the application

namespace Clara\core;

class Bootstrap
{
  private $router;
  private $response;

  public function __construct(Router $router, Response $response)
  {
    $this->router = $router;
    $this->response = $response;
    // look at incoming request and dispatch the right callable
    // callables modify Response header and content to be sent as a whole later
    $this->router->dispatch();
    // send appropriate response with correct headers
    $this->response->send();
  }
}
