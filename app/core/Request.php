<?php
declare(strict_types=1);

namespace App\core;

class Request
{
    private string $method;
    private string $uri;
    private array $queryParams;
    private array $bodyParams;
    private array $files;
    private ?array $jsonBody = null;

    public function __construct()
    {
        $this->uri = $this->parseUri();
        $this->queryParams = $_GET;
        $this->bodyParams = $_POST;
        $this->files = $_FILES;
        $this->method = $this->parseMethod();
        $this->parseJson();
    }

    private function parseUri(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $position = strpos($uri, '?');
        if ($position !== false) {
            $uri = substr($uri, 0, $position);
        }
        $uri = rawurldecode($uri);

        // Normalize trailing slash
        if ($uri !== '/' && str_ends_with($uri, '/')) {
            $uri = rtrim($uri, '/');
        }

        return $uri;
    }

    private function parseMethod(): string
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

        // Allow method spoofing via hidden _method field or X-HTTP-Method-Override header
        if ($method === 'POST') {
            if (isset($_POST['_method']) && in_array(strtoupper($_POST['_method']), ['PUT', 'PATCH', 'DELETE'])) {
                return strtoupper($_POST['_method']);
            }
            if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
                return strtoupper($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);
            }
        }

        return $method;
    }

    private function parseJson(): void
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (str_contains($contentType, 'application/json')) {
            $rawInput = file_get_contents('php://input');
            if ($rawInput) {
                $decoded = json_decode($rawInput, true);
                if (is_array($decoded)) {
                    $this->jsonBody = $decoded;
                    $this->bodyParams = array_merge($this->bodyParams, $decoded);
                }
            }
        }
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->bodyParams[$key] ?? $this->queryParams[$key] ?? $default;
    }

    public function all(): array
    {
        return array_merge($this->queryParams, $this->bodyParams);
    }

    public function file(string $key): ?array
    {
        return $this->files[$key] ?? null;
    }

    public function isAjax(): bool
    {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
            || (str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json'));
    }

    public function ip(): string
    {
        return $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }
}
