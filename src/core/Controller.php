<?php

namespace Clara\core;

abstract class Controller
{
  protected $request;
  protected $response;

  public function __construct(Request $request, Response $response)
  {
    $this->request = $request;
    $this->response = $response;
  }

  protected function view(string $view, array $data = [])
  {
    $this->response->view($view, $data);
  }
}
