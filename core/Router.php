<?php

declare(strict_types=1);

namespace core;

use Exception;
use Throwable;

class Router
{
    public Request $request;
    public Response $response;
    protected array $routes = [];
    protected array $globalMiddleware = [];
    protected array $groupPrefix = [];
    protected array $groupMiddleware = [];

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;

        // Security Framework (Go/C Standard Protection)
        $this->response->header('X-Content-Type-Options', 'nosniff');
        $this->response->header('X-Frame-Options', 'DENY');
        $this->response->header('X-XSS-Protection', '1; mode=block');
        $this->response->header('Referrer-Policy', 'strict-origin-when-cross-origin');
        $this->response->header('Content-Security-Policy', "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; font-src 'self'; connect-src 'self';");
        
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            $this->response->header('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }
    }

    /**
     * Route Group (Scalability like Express/Laravel)
     */
    public function group(array $options, callable $callback): void
    {
        $oldPrefix = $this->groupPrefix;
        $oldMiddleware = $this->groupMiddleware;

        if (isset($options['prefix'])) {
            $this->groupPrefix[] = trim($options['prefix'], '/');
        }

        if (isset($options['middleware'])) {
            $mids = is_array($options['middleware']) ? $options['middleware'] : [$options['middleware']];
            $this->groupMiddleware = array_merge($this->groupMiddleware, $mids);
        }

        $callback($this);

        $this->groupPrefix = $oldPrefix;
        $this->groupMiddleware = $oldMiddleware;
    }

    public function use($middleware): void
    {
        $this->globalMiddleware[] = $middleware;
    }

    private function addRoute(string $method, string $path, array $callbacks): void
    {
        $prefix = implode('/', $this->groupPrefix);
        $fullPath = $prefix ? '/' . $prefix . '/' . trim($path, '/') : $path;
        $fullPath = rtrim($fullPath, '/') ?: '/';
        
        $allCallbacks = array_merge($this->groupMiddleware, $callbacks);
        $this->routes[$method][$fullPath] = $allCallbacks;
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }

    public function get(string $path, ...$callbacks): void { $this->addRoute('get', $path, $callbacks); }
    public function post(string $path, ...$callbacks): void { $this->addRoute('post', $path, $callbacks); }
    public function put(string $path, ...$callbacks): void { $this->addRoute('put', $path, $callbacks); }
    public function delete(string $path, ...$callbacks): void { $this->addRoute('delete', $path, $callbacks); }

    public function resolve()
    {
        $path = $this->request->getPath();
        $method = $this->request->getMethod();
        $callbacks = $this->routes[$method][$path] ?? false;

        // Dynamic Routes {id} - Enhanced Precision
        if ($callbacks === false) {
            foreach ($this->routes[$method] ?? [] as $route => $cb) {
                if (strpos($route, '{') === false) continue;
                
                $routeRegex = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([a-zA-Z0-9-_]+)', $route);
                if (preg_match("@^" . $routeRegex . "$@", $path, $matches)) {
                    array_shift($matches);
                    $callbacks = $cb;
                    $this->request->setRouteParams($matches);
                    break;
                }
            }
        }

        if ($callbacks === false) {
            $this->response->status(404);
            return $this->renderView('_error', [
                'exception' => new Exception("Page not found", 404)
            ]);
        }

        // --- Optimized Middleware Pipeline (Express Style) ---
        $middlewareStack = array_merge($this->globalMiddleware, $callbacks);
        $index = 0;

        $next = function () use (&$middlewareStack, &$index, &$next) {
            if ($index >= count($middlewareStack)) return null;

            $callback = $middlewareStack[$index++];
            
            if (is_array($callback)) {
                $controller = new $callback[0]();
                Application::$app->controller = $controller;
                $controller->action = $callback[1];
                return call_user_func_array([$controller, $callback[1]], array_merge([$this->request], $this->request->getRouteParams()));
            }

            if (is_string($callback)) return $this->renderView($callback);

            if (is_callable($callback)) {
                return $callback($this->request, $this->response, $next);
            }

            // Existing compatibility with legacy middleware classes
            if (is_object($callback) && method_exists($callback, 'execute')) {
                return $callback->execute() ?? $next();
            }

            return null;
        };

        return $next();
    }

    public function renderView($view, $params = [])
    {
        $layoutContent = $this->layoutContent($view);
        $viewContent = $this->renderOnlyView($view, $params);
        return str_replace('{{content}}', $viewContent, $layoutContent);
    }

    protected function layoutContent($view = null)
    {
        $layout = 'main';
        if ($view === '_error' || strpos($view, 'errors/') === 0) {
            $layout = 'auth';
        } elseif (Application::$app->controller) {
            $layout = Application::$app->controller->layout;
        }

        ob_start();
        $layoutFile = Application::$ROOT_DIR . "/app/views/layouts/$layout.php";
        if (file_exists($layoutFile)) {
            include_once $layoutFile;
        } else {
            return "{{content}}";
        }
        return (string)ob_get_clean();
    }

    protected function renderOnlyView($view, $params)
    {
        foreach ($params as $key => $value) {
            $$key = $value;
        }
        ob_start();
        $viewFile = Application::$ROOT_DIR . "/app/views/$view.php";
        if (file_exists($viewFile)) {
            include_once $viewFile;
        } else {
            return "View '$view' not found";
        }
        return (string)ob_get_clean();
    }
}
