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
  public function body(string $default = NULL)
  {
    return file_get_contents('php://input') ?: $default;
  }

  // get the value of an item in the $_GET array
  public function get(string $key, string  $default = NULL)
  {
    return $this->search($_GET, $key, $default);
  }

  // get the value of an item in the $_POST array
  public function post(string $key, string $default = NULL)
  {
    return $this->search($_POST, $key, $default);
  }

  // get the value of an item in the $_FILES array
  public function files(string $key, string  $default = NULL)
  {
    return $this->search($_FILES, $key, $default);
  }

  // get the value of an item in the $_SESSION array
  public function session(string $key, string  $default = NULL)
  {
    return $this->search($_SESSION, $key, $default);
  }

  // get the value of an item in the $_COOKIE array
  public function cookie(string $key, string  $default = NULL)
  {
    return $this->search($_COOKIE, $key, $default);
  }

  // get the value of an item in the $_SERVER array
  public function server(string $key, string  $default = NULL)
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
    return $this->server('REQUEST_URI');
  }

  public function path()
  {
    return explode('?', $this->uri())[0];
  }
}
