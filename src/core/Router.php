<?php

// Router maps URI to defined callable (Controller@Action)

namespace Clara\core;

class Router
{
  protected $request;
  protected $response;
  protected $routes = [];

  public function __construct(Request $request, Response $response, \DI\Container $container)
  {
    $this->request = $request;
    $this->response = $response;
    $this->container = $container;
  }

  public function add(string $method, string $path, string $handler)
  {
    $this->routes[] = ['method' => \strtoupper($method), 'path' => $path, 'handler' => $handler];
  }

  public function get(string $path, string $handler)
  {
    $this->add('GET', $path, $handler);
  }

  public function post(string $path, string $handler)
  {
    $this->add('POST', $path, $handler);
  }

  public function dispatch()
  {
    $method = $this->request->method();
    $path = $this->request->path();
    // set $match to false initially
    $match = false;
    // iterate over $this->routes for a matching path
    foreach ($this->routes as $route) {
      if ($route['method'] === $method && $route['path'] === $path) {
        // set $match to matched route & return
        $match = $route;
        break; // stop matching at first find
      }
    }

    if ($match) {
      $handler = explode('@', $match['handler']);
      $controller = '\\Clara\\app\\controllers\\' . $handler[0];
      $action = $handler[1];
    } else {
      // 404
      $controller = '\\Clara\\app\\controllers\\_404';
      $action = 'index';
    }

    // note: you may decouple DI\Container by relying on an PSR-11 interface for Router
    $invoked = $this->container->get($controller);
    $invoked->$action();
  }
}
