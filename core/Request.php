<?php

namespace core;

/**
 * NexaPHP Framework Core - Request Object
 * ---
 * WARNING: DO NOT MODIFY THIS FILE.
 * Standardizes incoming HTTP data and URL parameters.
 * ---
 */
class Request
{
    private array $routeParams = [];

    public function getPath()
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $position = strpos($path, '?');
        if ($position === false) {
            return $path;
        }
        return substr($path, 0, $position);
    }

    public function getMethod()
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    public function isGet() { return $this->getMethod() === 'get'; }
    public function isPost() { return $this->getMethod() === 'post'; }
    public function isPut() { return $this->getMethod() === 'put'; }
    public function isDelete() { return $this->getMethod() === 'delete'; }

    public function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }

    public function isJson()
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        return strpos($contentType, 'application/json') !== false;
    }

    public function header(string $key, $default = null)
    {
        $header = 'HTTP_' . strtoupper(str_replace('-', '_', $key));
        return $_SERVER[$header] ?? $default;
    }

    public function ip()
    {
        return $_SERVER['REMOTE_ADDR'] ?? null;
    }

    public function getBody()
    {
        if ($this->isJson()) {
            $input = file_get_contents('php://input');
            return json_decode($input, true) ?? [];
        }

        $body = [];
        if ($this->isGet()) {
            foreach ($_GET as $key => $value) {
                $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        
        // Handle all input methods for non-JSON data
        $inputData = [];
        if ($this->isPost()) {
            $inputData = $_POST;
        } else {
            // For PUT/DELETE from standard form-urlencoded
            parse_str(file_get_contents('php://input'), $inputData);
        }

        foreach ($inputData as $key => $value) {
            $body[$key] = is_array($value) ? $value : filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
        }
        
        return $body;
    }

    public function setRouteParams($params)
    {
        $this->routeParams = $params;
        return $this;
    }

    public function getRouteParams()
    {
        return $this->routeParams;
    }

    public function getParam($key)
    {
        return $this->routeParams[$key] ?? null;
    }
}
