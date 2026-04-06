<?php

declare(strict_types=1);

namespace core;

use app\models\User;

/**
 * NexaPHP Framework Core - Authentication Service
 * ---
 * WARNING: DO NOT MODIFY THIS FILE.
 * Handles user login, logout, and session state.
 * ---
 */
class Auth
{
    private static ?User $user = null;

    public static function attempt(string $email, string $password): bool
    {
        $user = User::findOne(['email' => $email]);
        if (!$user) {
            return false;
        }

        if (password_verify($password, $user->password)) {
            return self::login($user);
        }

        return false;
    }

    public static function login(User $user): bool
    {
        self::$user = $user;
        Application::$app->session->set('user', $user->id);
        return true;
    }

    public static function logout(): void
    {
        self::$user = null;
        Application::$app->session->remove('user');
    }

    public static function isGuest(): bool
    {
        return !self::user();
    }

    public static function user(): ?User
    {
        if (self::$user !== null) {
            return self::$user;
        }

        $userId = Application::$app->session->get('user');
        if ($userId) {
            self::$user = User::findOne(['id' => $userId]);
        }

        return self::$user;
    }
}
