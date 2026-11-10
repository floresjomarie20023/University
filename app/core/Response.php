<?php

namespace Core;

class Response
{
    private int $statusCode = 200;
    private array $headers = [];
    private mixed $content = '';
    private string $contentType = 'text/html; charset=UTF-8';

    public static function make(mixed $content = '', int $statusCode = 200, array $headers = []): self
    {
        $response = new self();
        $response->setContent($content);
        $response->setStatusCode($statusCode);
        $response->setHeaders($headers);
        return $response;
    }

    public static function json(mixed $data, int $statusCode = 200, array $headers = []): self
    {
        $response = self::make(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), $statusCode, $headers);
        $response->header('Content-Type', 'application/json; charset=UTF-8');
        return $response;
    }

    public static function redirect(string $url, int $statusCode = 302): self
    {
        return self::make('', $statusCode, ['Location' => $url]);
    }

    public static function view(string $view, array $data = [], int $statusCode = 200): self
    {
        $controller = new BaseController();
        $content = $controller->view($view, $data);
        return self::make($content, $statusCode);
    }

    public function setContent(mixed $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getContent(): string
    {
        return (string)$this->content;
    }

    public function setStatusCode(int $statusCode): self
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function header(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function setHeaders(array $headers): self
    {
        $this->headers = array_merge($this->headers, $headers);
        return $this;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function send(): void
    {
        http_response_code($this->statusCode);

        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}");
        }

        echo $this->getContent();
    }
}
