<?php
/**
 * NexaPHP Framework Core - Global Helpers
 * ---
 * WARNING: DO NOT MODIFY THIS FILE.
 * Global helper functions for the NexaPHP framework.
 * ---
 */
use core\Application;
use core\Auth;

if (!function_exists('app')) {
    function app(): Application {
        return Application::$app;
    }
}

if (!function_exists('auth')) {
    function auth() {
        return Auth::class;
    }
}

if (!function_exists('session')) {
    function session() {
        return app()->session;
    }
}

if (!function_exists('db')) {
    function db() {
        return app()->db;
    }
}

if (!function_exists('mailer')) {
    function mailer() {
        return app()->mailer;
    }
}

if (!function_exists('view')) {
    function view($view, $params = []) {
        return app()->router->renderView($view, $params);
    }
}

if (!function_exists('redirect')) {
    function redirect($url) {
        app()->response->redirect($url);
        exit;
    }
}

if (!function_exists('response')) {
    function response() {
        return app()->response;
    }
}

if (!function_exists('json')) {
    function json($data, $code = 200) {
        app()->response->setStatusCode($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}

if (!function_exists('request')) {
    function request() {
        return app()->request;
    }
}

if (!function_exists('env')) {
    function env($key, $default = null) {
        $value = getenv($key);
        if ($value === false) {
            return $default;
        }
        return $value;
    }
}

if (!function_exists('url')) {
    function url($path = '') {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'];
        return rtrim($protocol . $host, '/') . '/' . ltrim($path, '/');
    }
}

if (!function_exists('asset')) {
    function asset($path = '') {
        return url('assets/' . ltrim($path, '/'));
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token() {
        $token = session()->get('csrf_token');
        if (!$token) {
            $token = bin2hex(random_bytes(32));
            session()->set('csrf_token', $token);
        }
        return $token;
    }
}

if (!function_exists('old')) {
    function old($key) {
        return app()->request->getBody()[$key] ?? '';
    }
}
