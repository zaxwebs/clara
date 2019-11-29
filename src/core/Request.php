<?php

// Request is a wrapper for HTTP requests
// It provides a collection of simple methods for easily and reliably retrieving information associated with an HTTP request

namespace Clara\core;

class Request
{

  function search(array $array, string $key, string $default = NULL)
  {
    return isset($array[$key]) ? $array[$key] : $default;
  }

  // get the raw body of a request
  public function body($default = NULL)
  {
    return file_get_contents('php://input') ?: $default;
  }

  // get the value of an item in the $_GET array
  public function get($key, $default = NULL)
  {
    return $this->search($_GET, $key, $default);
  }

  // get the value of an item in the $_POST array
  public function post($key, $default = NULL)
  {
    return $this->search($_POST, $key, $default);
  }

  // get the value of an item in the $_FILES array
  public function files($key, $default = NULL)
  {
    return $this->search($_FILES, $key, $default);
  }

  // get the value of an item in the $_SESSION array
  public function session($key, $default = NULL)
  {
    return $this->search($_SESSION, $key, $default);
  }

  // get the value of an item in the $_COOKIE array
  public function cookie($key, $default = NULL)
  {
    return $this->search($_COOKIE, $key, $default);
  }

  // get the value of an item in the $_SERVER array
  public function server($key, $default = NULL)
  {
    return $this->search($_SERVER, $key, $default);
  }

  // get the request method
  public function method()
  {
    return strtoupper($this->server('REQUEST_METHOD'));
  }

  // get the request uri
  public function uri()
  {
    return strtoupper($this->server('REQUEST_URI'));
  }
}
