<?php

namespace Clara\core;

class Response
{
  protected $version = '1.1';
  protected $status = 200;
  protected $headers = [];

  public function getVersion()
  {
    return $this->version;
  }

  public function setVersion(string $version)
  {
    $this->version = $version;
  }

  public function setStatus(int $status)
  {
    $this->status = $status;
  }

  public function setHeader(string $label, string $value)
  {
    $this->headers[$label] = $value;
  }

  //sends status header
  protected function sendStatus()
  {
    header("HTTP/{$this->version} {$this->status}", true, $this->status);
  }

  protected function sendHeaders()
  {
    foreach ($this->headers as $label => $value) {
      header("{$label}: {$value}", false);
    }
  }

  //sends status as well as other headers
  public function send()
  {
    $this->sendStatus();
    $this->sendHeaders();
  }

  //sends redirection header to redirect
  public function redirect(string $uri)
  {
    $this->setStatus(302);
    $this->setHeader('location', $uri);
    // send headers
    $this->send();
  }

  //send headers first and render view
  public function view(string $view, array $data = [])
  {
    // send headers
    $this->send();
    // render view
    extract($data);
    require_once __DIR__ . '/../app/views/' . $view . '.php';
  }
}
