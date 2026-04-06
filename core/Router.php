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
                'exception' => new \Exception("The page you are looking for does not exist.", 404)
            ]);
        }

        // --- Optimized Middleware Pipeline (Express Style) ---
        $middlewareStack = array_merge($this->globalMiddleware, $callbacks);
        $index = 0;

        $next = function () use (&$middlewareStack, &$index, &$next) {
            if ($index >= count($middlewareStack)) return null;

            $callback = $middlewareStack[$index++];
            
            if (is_array($callback)) {
                if (!class_exists($callback[0])) {
                    throw new \Exception("Controller class '{$callback[0]}' not found.", 500);
                }
                $controller = new $callback[0]();
                
                if (!method_exists($controller, $callback[1])) {
                    throw new \Exception("Action '{$callback[1]}' not found in controller '{$callback[0]}'.", 500);
                }
                
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
            return (string)ob_get_clean();
        } 

        // Clear buffer if file missing
        ob_get_clean();

        // Fallback: Internal Framework Views
        if ($view === '_error') {
            return $this->defaultErrorPage($params['exception'] ?? new \Exception("An unknown error occurred", 500));
        }

        return "<div class='error-notice' style='color:#ef4444; border:1px solid #ef4444; padding:1rem; border-radius:0.5rem; margin:1rem; font-family:sans-serif;'>
                <strong>NexaPHP Developer Trace:</strong> View '<code>$view</code>' not found at <code>app/views/$view.php</code>
                </div>";
    }

    private function defaultErrorPage(\Throwable $exception): string
    {
        $code = $exception->getCode();
        $message = $exception->getMessage();
        if (!$code || $code < 100 || $code > 599) $code = 500;
        $appName = Application::$app->config['app_name'] ?? 'NexaPHP';

        return <<<HTML
        <div class="error-container">
            <div class="error-card">
                <div class="error-code">{$code}</div>
                <div class="error-divider"></div>
                <div class="error-content">
                    <h1 class="error-title">Oops! System Error</h1>
                    <p class="error-message">{$message}</p>
                    <div class="error-actions">
                        <a href="/" class="btn-primary">Return Home</a>
                        <button onclick="window.history.back()" class="btn-secondary">Go Back</button>
                    </div>
                </div>
            </div>
        </div>
        <style>
            :root { --primary: #4f46e5; --primary-hover: #4338ca; --bg: #0f172a; --card-bg: #1e293b; --text: #f8fafc; --text-muted: #94a3b8; }
            body { margin: 0; padding: 0; font-family: 'Inter', sans-serif; background-color: var(--bg); color: var(--text); display: flex; align-items: center; justify-content: center; min-height: 100vh; }
            .error-container { padding: 2rem; animation: cardAppear 0.6s cubic-bezier(0.34, 1.56, 0.64, 1); }
            .error-card { background-color: var(--card-bg); padding: 3rem; border-radius: 1.5rem; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); display: flex; align-items: center; gap: 3rem; max-width: 800px; border: 1px solid rgba(255, 255, 255, 0.1); }
            @keyframes cardAppear { 0% { transform: translateY(30px); opacity: 0; } 100% { transform: translateY(0); opacity: 1; } }
            .error-code { font-size: 7rem; font-weight: 900; background: linear-gradient(135deg, #818cf8 0%, #4f46e5 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; line-height: 1; }
            .error-divider { width: 1px; height: 100px; background: rgba(255, 255, 255, 0.1); }
            .error-title { font-size: 2rem; font-weight: 700; margin: 0 0 1rem 0; }
            .error-message { font-size: 1.1rem; color: var(--text-muted); margin-bottom: 2rem; line-height: 1.6; }
            .error-actions { display: flex; gap: 1rem; }
            .btn-primary { background: var(--primary); color: white; padding: 0.75rem 1.5rem; border-radius: 0.75rem; text-decoration: none; font-weight: 600; transition: 0.2s; border: none; cursor: pointer; }
            .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.4); }
            .btn-secondary { background: rgba(255, 255, 255, 0.05); color: var(--text); padding: 0.75rem 1.5rem; border-radius: 0.75rem; font-weight: 600; transition: 0.2s; border: 1px solid rgba(255, 255, 255, 0.1); cursor: pointer; }
            @media (max-width: 640px) { .error-card { flex-direction: column; text-align: center; gap: 1.5rem; } .error-divider { width: 80%; height: 1px; } }
        </style>
HTML;
    }
}
