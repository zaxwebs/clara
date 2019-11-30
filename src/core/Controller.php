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

  protected function setStatus(int $status)
  {
    $this->response->setStatus($status);
  }

  protected function setHeader(string $label, string $value)
  {
    $this->response->setHeader($label, $value);
  }
}
