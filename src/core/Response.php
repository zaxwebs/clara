<?php

declare(strict_types=1);

namespace Clara\core;

class Response
{
    private string $version = '1.1';
    private int $status = 200;

    /** @var array<string, string> */
    private array $headers = [];

    public function getVersion(): string
    {
        return $this->version;
    }

    public function setVersion(string $version): void
    {
        $this->version = $version;
    }

    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    public function setHeader(string $label, string $value): void
    {
        $this->headers[$label] = $value;
    }

    private function sendStatus(): void
    {
        header("HTTP/{$this->version} {$this->status}", true, $this->status);
    }

    private function sendHeaders(): void
    {
        foreach ($this->headers as $label => $value) {
            header("{$label}: {$value}", false);
        }
    }

    public function send(): void
    {
        $this->sendStatus();
        $this->sendHeaders();
    }

    public function view(string $view, array $data = []): void
    {
        $this->send();
        extract($data, EXTR_SKIP);
        require __DIR__ . '/../app/views/' . $view . '.php';
    }

    public function redirect(string $uri): never
    {
        $this->setStatus(302);
        $this->setHeader('Location', $uri);
        $this->send();
        exit;
    }

    public function back(): never
    {
        if (isset($_SERVER['HTTP_REFERER'])) {
            $this->redirect((string) $_SERVER['HTTP_REFERER']);
        }

        $this->setStatus(302);
        $this->setHeader('Location', 'javascript://history.go(-1)');
        $this->send();

        echo 'It seems like Javascript is disabled on your browser. You can proceed to previous page manually...<br/>';
        exit;
    }
}
