<?php

namespace Bootstrap;

use App\Helpers\ResponseHelper;

class Router
{
    private array $routes = [];

    public function get(string $uri, callable|array $action): void
    {
        $this->addRoute('GET', $uri, $action);
    }

    public function post(string $uri, callable|array $action): void
    {
        $this->addRoute('POST', $uri, $action);
    }

    public function put(string $uri, callable|array $action): void
    {
        $this->addRoute('PUT', $uri, $action);
    }

    public function delete(string $uri, callable|array $action): void
    {
        $this->addRoute('DELETE', $uri, $action);
    }

    public function dispatch(string $method, string $uri): void
    {
        $cleanUri = '/' . trim($uri, '/');
        $cleanUri = $cleanUri === '//' ? '/' : $cleanUri;

        foreach ($this->routes[$method] ?? [] as $route) {
            $pattern = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_-]*)\}#', '(?P<$1>[^/]+)', $route['uri']);
            $pattern = '#^' . rtrim($pattern, '/') . '$#';

            if ($route['uri'] === '/') {
                $pattern = '#^/$#';
            }

            if (!preg_match($pattern, $cleanUri, $matches)) {
                continue;
            }

            $params = array_filter(
                $matches,
                static fn ($key): bool => !is_int($key),
                ARRAY_FILTER_USE_KEY
            );

            $this->invokeAction($route['action'], $params);
            return;
        }

        ResponseHelper::abort(404, 'Page not found.');
    }

    private function addRoute(string $method, string $uri, callable|array $action): void
    {
        $normalizedUri = '/' . trim($uri, '/');
        $normalizedUri = $normalizedUri === '//' ? '/' : $normalizedUri;

        $this->routes[$method][] = [
            'uri' => $normalizedUri,
            'action' => $action,
        ];
    }

    private function invokeAction(callable|array $action, array $params): void
    {
        if (is_callable($action)) {
            call_user_func($action, $params);
            return;
        }

        [$controllerClass, $method] = $action;
        $controller = new $controllerClass();
        $controller->{$method}($params);
    }
}
