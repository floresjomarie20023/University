<?php

namespace Core;

use Config\DatabaseConfig;
use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;
    private static array $config = [];

    private function __construct()
    {
    }

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            self::$config = DatabaseConfig::getConfig();
            self::$instance = self::createConnection();

            if (self::$config['driver'] === 'sqlite') {
                self::$instance->exec('PRAGMA foreign_keys = ON');
            }

            if (self::$config['auto_migrate'] ?? false) {
                Migrator::run(self::$instance);
            }
        }

        return self::$instance;
    }

    private static function createConnection(): PDO
    {
        try {
            return new PDO(
                self::$config['dsn'],
                self::$config['username'],
                self::$config['password'],
                self::$config['options']
            );
        } catch (PDOException $e) {
            error_log('Database connection failed: ' . $e->getMessage());
            throw new \RuntimeException('Database connection failed: ' . $e->getMessage(), 0, $e);
        }
    }

    public static function close(): void
    {
        self::$instance = null;
    }

    public static function getConfig(): array
    {
        return self::$config;
    }
}
