<?php

namespace core;

/**
 * NexaPHP Framework Core - Base Controller
 * ---
 * WARNING: DO NOT MODIFY THIS FILE.
 * Base class for all application controllers.
 * ---
 */
class Controller
{
    public string $layout = 'main';
    public string $action = '';
    protected array $middlewares = [];

    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    public function render($view, $params = [])
    {
        return Application::$app->router->renderView($view, $params);
    }

    public function registerMiddleware(BaseMiddleware $middleware)
    {
        $this->middlewares[] = $middleware;
    }

    /**
     * @return \core\BaseMiddleware[]
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }
}

abstract class BaseMiddleware
{
    abstract public function execute();
}
