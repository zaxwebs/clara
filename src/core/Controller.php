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
    extract($data);
    require_once __DIR__ . '/../app/views/' . $view . '.php';
  }
}
