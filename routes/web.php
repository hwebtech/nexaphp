<?php

/** @var \core\Router $router */

$router->get('/', [\app\controllers\HomeController::class, 'index']);
$router->get('/about', [\app\controllers\HomeController::class, 'about']);
$router->get('/contact', [\app\controllers\HomeController::class, 'contact']);

// Authentication Routes (example)
// $router->get('/login', [\app\controllers\AuthController::class, 'login']);
// $router->post('/login', [\app\controllers\AuthController::class, 'login']);
// $router->get('/register', [\app\controllers\AuthController::class, 'register']);
// $router->post('/register', [\app\controllers\AuthController::class, 'register']);
// $router->get('/logout', [\app\controllers\AuthController::class, 'logout']);
