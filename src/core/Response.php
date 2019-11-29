<?php

namespace Clara\core;

class Response
{
  protected $version = '1.1';
  protected $status = 200;
  protected $headers = [];
  protected $content = '';

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

  public function addContent($content)
  {
    $this->content .= $content;
  }

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

  protected function sendContent()
  {
    echo $this->content;
  }

  public function send()
  {
    $this->sendStatus();
    $this->sendHeaders();
    $this->sendContent();
  }

  public function redirect(string $uri)
  {
    $this->setStatus(302);
    $this->setHeader('location', $uri);
  }
}
