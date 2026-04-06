<?php

// Check for Composer autoloader
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

require_once __DIR__ . '/core/utils.php';

// Simple autoloader if composer is not used
spl_autoload_register(function ($class) {
    if (class_exists($class, false)) {
        return;
    }

    $root = __DIR__;
    $file = $root . '/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Initialize DotEnv
$dotenv = new \core\DotEnv(__DIR__ . '/.env');
$dotenv->load();

$config = require_once __DIR__ . '/config.php';
$app = new \core\Application(__DIR__, $config);

// Define Routes
/** @var \core\Router $router */
$router = $app->router;
require_once __DIR__ . '/routes/web.php';

$app->run();
