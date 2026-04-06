<?php

namespace core\middlewares;

use core\Application;
use core\BaseMiddleware;

class AuthMiddleware extends BaseMiddleware
{
    public array $actions = [];
    public string $redirectPath = '/app/login';

    public function __construct(array $actions = [], string $redirectPath = '/app/login')
    {
        $this->actions = $actions;
        $this->redirectPath = $redirectPath;
    }

    public function execute()
    {
        if (Application::isGuest()) {
            if (empty($this->actions) || in_array(Application::$app->controller->action, $this->actions)) {
                header("Location: " . $this->redirectPath);
                exit;
            }
        }
    }
}
