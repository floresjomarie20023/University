<?php

namespace Core;

class Router
{
    private array $routes = [];
    private array $middleware = [];
    private string $basePath = '';

    public function __construct(string $basePath = '')
    {
        $this->basePath = rtrim($basePath, '/');
    }

    public function get(string $uri, callable|array|string $action): self
    {
        return $this->addRoute('GET', $uri, $action);
    }

    public function post(string $uri, callable|array|string $action): self
    {
        return $this->addRoute('POST', $uri, $action);
    }

    public function put(string $uri, callable|array|string $action): self
    {
        return $this->addRoute('PUT', $uri, $action);
    }

    public function patch(string $uri, callable|array|string $action): self
    {
        return $this->addRoute('PATCH', $uri, $action);
    }

    public function delete(string $uri, callable|array|string $action): self
    {
        return $this->addRoute('DELETE', $uri, $action);
    }

    public function any(string $uri, callable|array|string $action): self
    {
        foreach (['GET', 'POST', 'PUT', 'PATCH', 'DELETE'] as $method) {
            $this->addRoute($method, $uri, $action);
        }
        return $this;
    }

    public function match(array $methods, string $uri, callable|array|string $action): self
    {
        foreach ($methods as $method) {
            $this->addRoute(strtoupper($method), $uri, $action);
        }
        return $this;
    }

    public function group(array $attributes, callable $callback): self
    {
        $prefix = $attributes['prefix'] ?? '';
        $middleware = $attributes['middleware'] ?? [];

        $previousRoutes = $this->routes;
        $previousMiddleware = $this->middleware;

        $this->middleware = array_merge($this->middleware, $middleware);

        $callback($this);

        $this->routes = $previousRoutes;
        $this->middleware = $previousMiddleware;

        return $this;
    }

    public function resource(string $name, string $controller, array $options = []): self
    {
        $only = $options['only'] ?? ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'];
        $except = $options['except'] ?? [];

        $actions = [
            'index' => ['GET', '', 'index'],
            'create' => ['GET', '/create', 'create'],
            'store' => ['POST', '', 'store'],
            'show' => ['GET', '/{id}', 'show'],
            'edit' => ['GET', '/{id}/edit', 'edit'],
            'update' => ['PUT', '/{id}', 'update'],
            'destroy' => ['DELETE', '/{id}', 'destroy'],
        ];

        foreach ($actions as $action => [$method, $uri, $methodName]) {
            if (in_array($action, $except) || !in_array($action, $only)) {
                continue;
            }

            $fullUri = rtrim($name . $uri, '/');
            $this->addRoute($method, $fullUri, [$controller, $methodName]);
        }

        return $this;
    }

    protected function addRoute(string $method, string $uri, callable|array|string $action): self
    {
        $uri = $this->basePath . '/' . ltrim($uri, '/');
        $uri = preg_replace('#/{2,}#', '/', $uri);
        $uri = $uri === '' ? '/' : $uri;

        $pattern = $this->convertToRegex($uri);

        $this->routes[] = [
            'method' => strtoupper($method),
            'uri' => $uri,
            'pattern' => $pattern,
            'action' => $action,
            'middleware' => $this->middleware,
        ];

        return $this;
    }

    protected function convertToRegex(string $uri): string
    {
        $pattern = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', $uri);
        $pattern = str_replace('/', '\/', $pattern);
        return '#^' . $pattern . '$#';
    }

    public function dispatch(string $method, string $uri): mixed
    {
        $method = strtoupper($method);
        $uri = $this->basePath . '/' . ltrim($uri, '/');
        $uri = preg_replace('#/{2,}#', '/', $uri);
        $uri = $uri === '' ? '/' : $uri;

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method && $route['method'] !== 'ANY') {
                continue;
            }

            if (!preg_match($route['pattern'], $uri, $matches)) {
                continue;
            }

            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

            return $this->executeAction($route['action'], $params, $route['middleware']);
        }

        throw new HttpException(404, 'Route not found');
    }

    protected function executeAction(callable|array|string $action, array $params, array $middleware): mixed
    {
        foreach ($middleware as $mw) {
            $result = $this->executeMiddleware($mw);
            if ($result !== null) {
                return $result;
            }
        }

        if (is_callable($action)) {
            return $action(...$params);
        }

        if (is_array($action) && count($action) === 2) {
            [$controllerClass, $method] = $action;
            if (!class_exists($controllerClass)) {
                throw new \RuntimeException("Controller {$controllerClass} not found");
            }
            $controller = new $controllerClass();
            if (!method_exists($controller, $method)) {
                throw new \RuntimeException("Method {$method} not found in {$controllerClass}");
            }
            return $controller->$method(...$params);
        }

        if (is_string($action) && str_contains($action, '@')) {
            [$controllerClass, $method] = explode('@', $action);
            if (!class_exists($controllerClass)) {
                throw new \RuntimeException("Controller {$controllerClass} not found");
            }
            $controller = new $controllerClass();
            if (!method_exists($controller, $method)) {
                throw new \RuntimeException("Method {$method} not found in {$controllerClass}");
            }
            return $controller->$method(...$params);
        }

        throw new \RuntimeException('Invalid route action');
    }

    protected function executeMiddleware(string $middleware): mixed
    {
        if (!class_exists($middleware)) {
            return null;
        }

        $instance = new $middleware();
        if (method_exists($instance, 'handle')) {
            return $instance->handle();
        }

        return null;
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }

    public function hasRoute(string $method, string $uri): bool
    {
        $method = strtoupper($method);
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $route['uri'] === $uri) {
                return true;
            }
        }
        return false;
    }
}
