<?php

// Router maps URI to defined callable (Controller@Action)

namespace Clara\core;

class Router
{
  protected $request;
  protected $response;
  protected $routes = [];
  protected $container;

  public function __construct(Request $request, Response $response, \DI\Container $container)
  {
    $this->request = $request;
    $this->response = $response;
    $this->container = $container;
  }

  public function add(string $method, string $path, string $handler)
  {
    $this->routes[] = [
      'method' => strtoupper($method),
      'path' => $this->normalizePath($path),
      'handler' => $handler,
    ];
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
    if ($method === 'HEAD') {
      $method = 'GET';
    }

    $path = $this->normalizePath($this->request->path());
    $match = $this->findRoute($method, $path);

    if ($match === NULL) {
      $this->response->setStatus(404);
      list($controller, $action) = $this->resolveHandler('_404@index');
    } else {
      list($controller, $action) = $this->resolveHandler($match['handler']);
    }

    // note: you may decouple DI\Container by relying on an PSR-11 interface for Router
    $invoked = $this->container->get($controller);
    if (!method_exists($invoked, $action)) {
      $this->response->setStatus(404);
      $invoked = $this->container->get('\\Clara\\app\\controllers\\_404');
      $action = 'index';
    }

    $invoked->$action();
  }

  protected function findRoute(string $method, string $path)
  {
    foreach ($this->routes as $route) {
      if ($route['method'] === $method && $route['path'] === $path) {
        return $route;
      }
    }

    return NULL;
  }

  protected function normalizePath(string $path)
  {
    if ($path === '') {
      return '/';
    }

    if ($path !== '/') {
      return rtrim($path, '/');
    }

    return $path;
  }

  protected function resolveHandler(string $handler)
  {
    $parts = explode('@', $handler, 2);

    if (count($parts) !== 2 || $parts[0] === '' || $parts[1] === '') {
      return ['\\Clara\\app\\controllers\\_404', 'index'];
    }

    return ['\\Clara\\app\\controllers\\' . $parts[0], $parts[1]];
  }
}
