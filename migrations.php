<?php

spl_autoload_register(function ($class) {
    if (class_exists($class, false)) return;
    $root = __DIR__;
    $file = $root . '/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) require_once $file;
});

$config = require __DIR__ . '/config.php';
$app = new \core\Application(__DIR__, $config);

$app->db->applyMigrations();
