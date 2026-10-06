<?php
declare(strict_types=1);

namespace App\core;

class Router
{
    private array $routes = [];

    public function get(string $path, array|callable $handler, array $middleware = []): void
    {
        $this->addRoute('GET', $path, $handler, $middleware);
    }

    public function post(string $path, array|callable $handler, array $middleware = []): void
    {
        $this->addRoute('POST', $path, $handler, $middleware);
    }

    public function put(string $path, array|callable $handler, array $middleware = []): void
    {
        $this->addRoute('PUT', $path, $handler, $middleware);
    }

    public function delete(string $path, array|callable $handler, array $middleware = []): void
    {
        $this->addRoute('DELETE', $path, $handler, $middleware);
    }

    private function addRoute(string $method, string $path, array|callable $handler, array $middleware): void
    {
        // Convert route with parameters like {id} to regex pattern
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $path);
        $pattern = '#^' . $pattern . '$#';

        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'pattern' => $pattern,
            'handler' => $handler,
            'middleware' => $middleware,
        ];
    }

    public function dispatch(Request $request, Response $response): void
    {
        $requestMethod = $request->getMethod();
        $requestUri = $request->getUri();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $requestMethod) {
                continue;
            }

            if (preg_match($route['pattern'], $requestUri, $matches)) {
                // Extract named parameter matches
                $params = [];
                foreach ($matches as $key => $value) {
                    if (is_string($key)) {
                        $params[$key] = $value;
                    }
                }

                // Execute middleware pipeline
                foreach ($route['middleware'] as $middlewareClass) {
                    if (is_string($middlewareClass) && class_exists($middlewareClass)) {
                        $middlewareInstance = new $middlewareClass();
                        $middlewareInstance->handle($request, $response);
                    } elseif (is_callable($middlewareClass)) {
                        $middlewareClass($request, $response);
                    }
                }

                // Execute route handler
                $handler = $route['handler'];
                if (is_array($handler)) {
                    [$controllerClass, $action] = $handler;
                    if (class_exists($controllerClass)) {
                        $controller = new $controllerClass($request, $response);
                        if (method_exists($controller, $action)) {
                            $controller->$action($params);
                            return;
                        }
                        throw new \BadMethodCallException("Action `{$action}` not found in controller `{$controllerClass}`");
                    }
                    throw new \RuntimeException("Controller `{$controllerClass}` not found");
                }

                if (is_callable($handler)) {
                    call_user_func($handler, $params, $request, $response);
                    return;
                }
            }
        }

        // 404 Route Not Found
        $response->setStatusCode(404);
        if ($request->isAjax()) {
            $response->json([
                'success' => false,
                'message' => 'Endpoint not found',
            ], 404);
            return;
        }

        $errorView = APP_PATH . '/views/errors/404.php';
        if (file_exists($errorView)) {
            require $errorView;
        } else {
            echo "<h1>404 - Page Not Found</h1>";
        }
        exit;
    }
}
