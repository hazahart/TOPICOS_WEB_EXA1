<?php

class Router
{
    private array $routes = [];
    private string $version;
    private string $basePath;

    public function __construct(string $version = 'v1', string $basePath = '')
    {
        $this->version = $version;
        $this->basePath = rtrim($basePath, '/');
    }

    public function addRoute(string $method, string $path, callable|array $handler): void
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => '/api/' . $this->version . $path,
            'handler' => $handler
        ];
    }

    public function dispatch(): mixed
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';

        if (!empty($this->basePath) && str_starts_with($uri, $this->basePath)) {
            $uri = substr($uri, strlen($this->basePath));
        }

        $uri = '/' . ltrim($uri, '/');

        foreach ($this->routes as $route) {
            $pattern = preg_replace('/\{[a-zA-Z0-9_]+}/', '([a-zA-Z0-9_-]+)', $route['path']);
            $pattern = '#^' . $pattern . '$#';

            if ($route['method'] === $method && preg_match($pattern, $uri, $matches)) {
                array_shift($matches);
                return call_user_func_array($route['handler'], $matches);
            }
        }
        http_response_code(404);
        echo json_encode(['message' => 'Ruta no encontrada', 'uri' => $uri]);
        return null;
    }
}