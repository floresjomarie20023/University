<?php

namespace Config;

class DatabaseConfig
{
    public static function getConfig(): array
    {
        $driver = strtolower($_ENV['DB_DRIVER'] ?? 'sqlite');

        if ($driver === 'mysql') {
            $host = $_ENV['DB_HOST'] ?? 'localhost';
            $port = (int)($_ENV['DB_PORT'] ?? 3306);
            $dbname = $_ENV['DB_NAME'] ?? 'university_crud';
            $charset = 'utf8mb4';

            return [
                'driver' => 'mysql',
                'dsn' => "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}",
                'username' => $_ENV['DB_USER'] ?? 'root',
                'password' => $_ENV['DB_PASS'] ?? '',
                'options' => [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                    \PDO::ATTR_EMULATE_PREPARES => false,
                ],
                'auto_migrate' => false,
            ];
        }

        $path = $_ENV['DB_DATABASE'] ?? dirname(__DIR__, 2) . '/storage/database.sqlite';
        $directory = dirname($path);

        if (!is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        return [
            'driver' => 'sqlite',
            'dsn' => 'sqlite:' . $path,
            'username' => null,
            'password' => null,
            'options' => [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            ],
            'auto_migrate' => true,
        ];
    }
}
