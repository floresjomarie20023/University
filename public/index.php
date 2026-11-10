<?php

/**
 * University Management System - Entry Point
 */

$autoload = __DIR__ . '/../vendor/autoload.php';

if (file_exists($autoload)) {
    require_once $autoload;
} else {
    spl_autoload_register(function (string $class): void {
        $prefixes = [
            'App\\' => __DIR__ . '/../app/',
            'Core\\' => __DIR__ . '/../app/core/',
            'Config\\' => __DIR__ . '/../app/config/',
        ];

        foreach ($prefixes as $prefix => $baseDir) {
            if (str_starts_with($class, $prefix)) {
                $relative = substr($class, strlen($prefix));
                $file = $baseDir . str_replace('\\', '/', $relative) . '.php';
                if (file_exists($file)) {
                    require_once $file;
                }
                return;
            }
        }
    });
}

$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value, " \t\n\r\0\x0B\"'");
    }
}

use App\Controllers\AuthController;
use App\Controllers\DisciplineController;
use App\Controllers\HomeController;
use App\Controllers\ScheduleController;
use App\Controllers\StudentController;
use App\Controllers\TeacherController;
use Core\HttpException;
use Core\Router;
use Core\Session;

Session::getInstance()->start();

date_default_timezone_set('UTC');

set_exception_handler(function (\Throwable $e): void {
    http_response_code($e instanceof HttpException ? $e->getStatusCode() : 500);

    if (($_ENV['APP_DEBUG'] ?? 'true') === 'true') {
        echo '<h1>Error: ' . htmlspecialchars($e->getMessage()) . '</h1>';
        echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    } else {
        echo '<h1>Internal Server Error</h1>';
        echo '<p>Something went wrong. Please try again later.</p>';
    }

    error_log($e->getMessage() . "\n" . $e->getTraceAsString());
});

set_error_handler(function (int $errno, string $errstr, string $errfile, int $errline): bool {
    if (!(error_reporting() & $errno)) {
        return false;
    }
    throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
});

$router = new Router('');

$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->post('/logout', [AuthController::class, 'logout']);

$router->get('/', [HomeController::class, 'index']);
$router->resource('students', StudentController::class);
$router->resource('teachers', TeacherController::class);
$router->resource('schedules', ScheduleController::class, ['except' => ['show']]);
$router->resource('disciplines', DisciplineController::class);

$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
if ($method === 'POST' && isset($_POST['_method'])) {
    $method = strtoupper((string)$_POST['_method']);
}

if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
    $token = (string)($_POST['_token'] ?? '');
    if (!hash_equals($_SESSION['_csrf_token'] ?? '', $token)) {
        http_response_code(419);
        echo '<h1>419 - Page Expired</h1>';
        echo '<p>Your session expired or the security token is invalid.</p>';
        echo '<p><a href="/">Return to the dashboard</a></p>';
        exit;
    }
}

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
if ($basePath !== '' && $basePath !== '/' && str_starts_with($uri, $basePath)) {
    $uri = substr($uri, strlen($basePath));
}
$uri = $uri ?: '/';

try {
    $router->dispatch($method, $uri);
} catch (HttpException $e) {
    http_response_code($e->getStatusCode());
    echo "<h1>Error {$e->getStatusCode()}</h1>";
    echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<a href="/">Go Home</a>';
} catch (\Throwable $e) {
    http_response_code(500);
    if (($_ENV['APP_DEBUG'] ?? 'true') === 'true') {
        echo '<h1>Error: ' . htmlspecialchars($e->getMessage()) . '</h1>';
        echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    } else {
        echo '<h1>Internal Server Error</h1>';
        echo '<p>Something went wrong. Please try again later.</p>';
    }
    error_log($e->getMessage() . "\n" . $e->getTraceAsString());
}
