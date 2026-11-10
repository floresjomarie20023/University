<?php

namespace Core;

class Request
{
    private array $query;
    private array $request;
    private array $files;
    private array $cookies;
    private array $server;
    private ?string $content = null;
    private array $parsedBody = [];

    public function __construct()
    {
        $this->query = $_GET;
        $this->request = $_POST;
        $this->files = $_FILES;
        $this->cookies = $_COOKIE;
        $this->server = $_SERVER;
    }

    public static function capture(): self
    {
        return new self();
    }

    public function getMethod(): string
    {
        return $this->server['REQUEST_METHOD'] ?? 'GET';
    }

    public function getUri(): string
    {
        return $this->server['REQUEST_URI'] ?? '/';
    }

    public function getPath(): string
    {
        $uri = $this->getUri();
        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
        }
        return $uri;
    }

    public function getQueryString(): string
    {
        return $this->server['QUERY_STRING'] ?? '';
    }

    public function query(string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->query;
        }
        return $this->query[$key] ?? $default;
    }

    public function input(string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return array_merge($this->request, $this->parsedBody);
        }
        return $this->request[$key] ?? $this->parsedBody[$key] ?? $default;
    }

    public function all(): array
    {
        return array_merge($this->request, $this->parsedBody, $this->query);
    }

    public function only(array $keys): array
    {
        $data = $this->all();
        return array_intersect_key($data, array_flip($keys));
    }

    public function except(array $keys): array
    {
        $data = $this->all();
        return array_diff_key($data, array_flip($keys));
    }

    public function has(string $key): bool
    {
        return isset($this->request[$key]) || isset($this->parsedBody[$key]) || isset($this->query[$key]);
    }

    public function filled(string $key): bool
    {
        $value = $this->input($key);
        return $value !== null && $value !== '';
    }

    public function file(string $key = null): mixed
    {
        if ($key === null) {
            return $this->files;
        }
        return $this->files[$key] ?? null;
    }

    public function hasFile(string $key): bool
    {
        return isset($this->files[$key]) && $this->files[$key]['error'] === UPLOAD_ERR_OK;
    }

    public function cookie(string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->cookies;
        }
        return $this->cookies[$key] ?? $default;
    }

    public function header(string $key = null, mixed $default = null): mixed
    {
        $key = strtolower(str_replace('-', '_', $key));
        $serverKey = 'HTTP_' . strtoupper($key);

        if ($key === null) {
            $headers = [];
            foreach ($this->server as $k => $v) {
                if (str_starts_with($k, 'HTTP_')) {
                    $headers[strtolower(substr($k, 5))] = $v;
                }
            }
            return $headers;
        }

        return $this->server[$serverKey] ?? $default;
    }

    public function bearerToken(): ?string
    {
        $header = $this->header('authorization');
        if ($header && str_starts_with($header, 'Bearer ')) {
            return substr($header, 7);
        }
        return null;
    }

    public function ip(): string
    {
        $keys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];
        foreach ($keys as $key) {
            if (!empty($this->server[$key])) {
                $ips = explode(',', $this->server[$key]);
                return trim($ips[0]);
            }
        }
        return 'unknown';
    }

    public function userAgent(): string
    {
        return $this->server['HTTP_USER_AGENT'] ?? '';
    }

    public function isAjax(): bool
    {
        return $this->header('x_requested_with') === 'XMLHttpRequest';
    }

    public function isJson(): bool
    {
        return str_contains($this->header('content_type'), 'application/json');
    }

    public function getContent(): string
    {
        if ($this->content === null) {
            $this->content = file_get_contents('php://input');
            $this->parseContent();
        }
        return $this->content;
    }

    public function json(mixed $default = null): mixed
    {
        $content = $this->getContent();
        if (empty($content)) {
            return $default;
        }
        $decoded = json_decode($content, true);
        return json_last_error() === JSON_ERROR_NONE ? $decoded : $default;
    }

    private function parseContent(): void
    {
        $contentType = $this->header('content_type') ?? '';

        if (str_contains($contentType, 'application/json')) {
            $this->parsedBody = $this->json() ?? [];
        } elseif (str_contains($contentType, 'application/x-www-form-urlencoded')) {
            parse_str($this->content, $this->parsedBody);
        }
    }

    public function session(): Session
    {
        return Session::getInstance();
    }
}
