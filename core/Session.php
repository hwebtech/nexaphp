<?php

namespace core;

/**
 * NexaPHP Framework Core - Session Manager
 * ---
 * WARNING: DO NOT MODIFY THIS FILE.
 * Securely manages PHP sessions and Flash messages.
 * ---
 */
class Session
{
    protected const FLASH_KEY = 'flash_messages';

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            // Secure Session Cookie Parameters
            session_set_cookie_params([
                'lifetime' => 0, // Expire on browser close
                'path' => '/',
                'domain' => '',
                'secure' => false, // Set to true if using HTTPS
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
            session_start();
        }

        // Handle Session Timeout (30 minutes of inactivity)
        $timeout = 1800; // 30 minutes in seconds
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
            session_unset();
            session_destroy();
            session_start(); // Restart for flash messages
            $this->setFlash('error', 'Your session has expired due to inactivity.');
        }
        $_SESSION['last_activity'] = time();

        // Regenerate session ID periodically (every 10 minutes)
        if (!isset($_SESSION['created_at'])) {
            $_SESSION['created_at'] = time();
        } elseif (time() - $_SESSION['created_at'] > 600) { // 10 minutes
            session_regenerate_id(true);
            $_SESSION['created_at'] = time();
        }

        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];
        foreach ($flashMessages as $key => &$flashMessage) {
            $flashMessage['remove'] = true;
        }
        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }

    public function setFlash($key, $message)
    {
        $_SESSION[self::FLASH_KEY][$key] = [
            'remove' => false,
            'value' => $message
        ];
    }

    public function getFlash($key)
    {
        return $_SESSION[self::FLASH_KEY][$key]['value'] ?? false;
    }

    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public function get($key)
    {
        return $_SESSION[$key] ?? false;
    }

    public function remove($key)
    {
        unset($_SESSION[$key]);
    }

    public function __destruct()
    {
        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];
        foreach ($flashMessages as $key => &$flashMessage) {
            if ($flashMessage['remove']) {
                unset($flashMessages[$key]);
            }
        }
        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }
}
