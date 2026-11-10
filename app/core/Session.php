<?php

namespace Core;

class Session
{
    private static ?Session $instance = null;
    private bool $started = false;

    private function __construct()
    {
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function start(): void
    {
        if (!$this->started && session_status() === PHP_SESSION_NONE) {
            $config = \Config\AppConfig::getConfig()['session'];
            session_name($config['name']);
            session_set_cookie_params([
                'lifetime' => $config['lifetime'],
                'path' => '/',
                'domain' => '',
                'secure' => $config['secure'],
                'httponly' => $config['httponly'],
                'samesite' => $config['samesite'],
            ]);
            session_start();
            $this->started = true;
        }
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $this->start();
        return $_SESSION[$key] ?? $default;
    }

    public function put(string $key, mixed $value): void
    {
        $this->start();
        $_SESSION[$key] = $value;
    }

    public function has(string $key): bool
    {
        $this->start();
        return isset($_SESSION[$key]);
    }

    public function forget(string $key): void
    {
        $this->start();
        unset($_SESSION[$key]);
    }

    public function flush(): void
    {
        $this->start();
        $_SESSION = [];
    }

    public function regenerate(bool $deleteOldSession = false): void
    {
        $this->start();
        session_regenerate_id($deleteOldSession);
    }

    public function getId(): string
    {
        $this->start();
        return session_id();
    }

    public function flash(string $key, mixed $value): void
    {
        $this->start();
        $_SESSION['_flash'][$key] = $value;
    }

    public function getFlash(string $key, mixed $default = null): mixed
    {
        $this->start();
        $value = $_SESSION['_flash'][$key] ?? $default;
        unset($_SESSION['_flash'][$key]);
        return $value;
    }

    public function hasFlash(string $key): bool
    {
        $this->start();
        return isset($_SESSION['_flash'][$key]);
    }

    public function destroy(): void
    {
        $this->start();
        session_destroy();
        $this->started = false;
    }
}
