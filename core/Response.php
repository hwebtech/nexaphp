<?php

namespace core;

/**
 * NexaPHP Framework Core - Response Object
 * ---
 * WARNING: DO NOT MODIFY THIS FILE.
 * Handles headers, redirects, JSON rendering, and HTML output.
 * ---
 */
class Response
{
    private int $statusCode = 200;

    public function status(int $code)
    {
        $this->statusCode = $code;
        http_response_code($code);
        return $this;
    }

    public function setStatusCode(int $code)
    {
        return $this->status($code);
    }

    public function redirect(string $url)
    {
        header("Location: $url");
        exit;
    }

    public function json(array $data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public function send(string $content)
    {
        echo $content;
        exit;
    }

    public function end()
    {
        exit;
    }

    public function header(string $key, string $value)
    {
        header("$key: $value");
        return $this;
    }
}
