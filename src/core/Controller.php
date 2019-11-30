<?php

namespace Clara\core;

abstract class Controller
{
  protected $request;
  protected $response;

  public function __construct(Request $request, Response $response, DB $db)
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

  protected function get(string $key, string  $default = NULL)
  {
    $this->request->get($key, $default);
  }

  protected function post(string $key, string  $default = NULL)
  {
    $this->request->post($key, $default);
  }

  protected function session(string $key, string  $default = NULL)
  {
    $this->request->session($key, $default);
  }

  protected function cookie(string $key, string  $default = NULL)
  {
    $this->request->cookie($key, $default);
  }
}
