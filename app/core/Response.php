<?php
declare(strict_types=1);

namespace App\core;

class Response
{
    public function setStatusCode(int $code): self
    {
        http_response_code($code);
        return $this;
    }

    public function setHeader(string $name, string $value): self
    {
        header("{$name}: {$value}");
        return $this;
    }

    public function json(mixed $data, int $statusCode = 200): void
    {
        $this->setStatusCode($statusCode);
        $this->setHeader('Content-Type', 'application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public function redirect(string $url, int $statusCode = 302): void
    {
        $this->setStatusCode($statusCode);
        $this->setHeader('Location', $url);
        exit;
    }
}
