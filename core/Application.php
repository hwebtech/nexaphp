<?php

declare(strict_types=1);

namespace core;

use Throwable;

/**
 * NexaPHP Framework Core - Application Class
 * ---
 * WARNING: DO NOT MODIFY THIS FILE.
 * This file bootstraps the NexaPHP kernel.
 * ---
 */
class Application
{
    public static string $ROOT_DIR;
    public static Application $app;
    
    public Router $router;
    public Request $request;
    public Response $response;
    public Session $session;
    public Database $db;
    public Mailer $mailer;
    public ?Controller $controller = null;
    public ?object $user = null;
    public array $config;
    
    protected array $container = [];

    public function __construct(string $rootPath, array $config)
    {
        $this->config = $config;
        self::$ROOT_DIR = $rootPath;
        self::$app = $this;

        // Core Components
        $this->request = new Request();
        $this->response = new Response();
        $this->session = new Session();
        $this->db = new Database($config['db']);
        $this->mailer = new Mailer($config['mail'] ?? []);
        $this->router = new Router($this->request, $this->response);

        // Basic Auth
        $userId = $this->session->get('user');
        if ($userId) {
            $this->user = (object)['id' => $userId]; // Placeholder
        }

        date_default_timezone_set($config['timezone'] ?? 'UTC');
    }

    /**
     * Service Container - Binding
     */
    public function bind(string $key, callable $resolver): void
    {
        $this->container[$key] = $resolver;
    }

    /**
     * Service Container - Singelton
     */
    public function singleton(string $key, callable $resolver): void
    {
        $this->container[$key] = function() use ($resolver) {
            static $instance;
            if ($instance === null) {
                $instance = $resolver($this);
            }
            return $instance;
        };
    }

    /**
     * Service Container - Resolution
     */
    public function make(string $key)
    {
        if (isset($this->container[$key])) {
            return ($this->container[$key])($this);
        }
        return null;
    }

    public static function isGuest(): bool
    {
        return !self::$app->user;
    }

    public function login($user): bool
    {
        $this->user = $user;
        $this->session->set('user', $user->id);
        return true;
    }

    public function logout(): void
    {
        $this->user = null;
        $this->session->remove('user');
    }

    public function run(): void
    {
        // Check Maintenance Mode
        if (file_exists(self::$ROOT_DIR . '/storage/framework/down')) {
            $this->response->status(503);
            if ($this->request->isJson()) {
                echo json_encode(['message' => 'Service Unavailable (Maintenance Mode)']);
                return;
            }
            echo "<h1>Application is currently under maintenance.</h1><p>Please try again in a few minutes.</p>";
            return;
        }

        try {
            echo $this->router->resolve();
        } catch (Throwable $e) {
            Logger::error("Fatal Error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
            
            $code = (int)$e->getCode();
            $statusCode = ($code >= 100 && $code < 600) ? $code : 500;
            
            $this->response->status($statusCode);
            echo $this->router->renderView('_error', [
                'exception' => $e
            ]);
        }
    }
}
