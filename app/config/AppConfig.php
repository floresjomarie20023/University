<?php

namespace Config;

class AppConfig
{
    public static function getConfig(): array
    {
        return [
            'app_name' => 'University CRUD App',
            'app_url' => $_ENV['APP_URL'] ?? 'http://localhost:8000',
            'debug' => filter_var($_ENV['APP_DEBUG'] ?? 'true', FILTER_VALIDATE_BOOLEAN),
            'timezone' => 'UTC',
            'session' => [
                'name' => 'UNIVERSITY_CRUD_SESSION',
                'lifetime' => 3600,
                'secure' => false,
                'httponly' => true,
                'samesite' => 'Lax',
            ],
            'csrf' => [
                'token_name' => '_token',
                'header_name' => 'X-CSRF-Token',
            ],
            'pagination' => [
                'default_per_page' => 10,
                'max_per_page' => 100,
            ],
        ];
    }
}