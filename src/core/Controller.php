<?php

declare(strict_types=1);

namespace Clara\core;

abstract class Controller
{
    public function __construct(
        protected readonly Request $request,
        protected readonly Response $response,
        protected readonly ?DB $db = null,
    ) {
    }

    protected function view(string $view, array $data = []): void
    {
        $this->response->view($view, $data);
    }

    protected function setStatus(int $status): void
    {
        $this->response->setStatus($status);
    }

    protected function setHeader(string $label, string $value): void
    {
        $this->response->setHeader($label, $value);
    }

    protected function get(string $key, ?string $default = null): mixed
    {
        return $this->request->get($key, $default);
    }

    protected function post(string $key, ?string $default = null): mixed
    {
        return $this->request->post($key, $default);
    }

    protected function session(string $key, ?string $default = null): mixed
    {
        return $this->request->session($key, $default);
    }

    protected function cookie(string $key, ?string $default = null): mixed
    {
        return $this->request->cookie($key, $default);
    }
}
