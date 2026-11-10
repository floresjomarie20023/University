<?php

namespace Core;

use App\Models\User;

class Auth
{
    private static ?User $cachedUser = null;
    private static bool $loaded = false;

    public static function attempt(string $email, string $password): bool
    {
        $user = User::where('email', '=', $email)->first();

        if (!$user || !$user->is_active) {
            return false;
        }

        if (!password_verify($password, $user->password)) {
            return false;
        }

        self::login($user);
        return true;
    }

    public static function login(User $user): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }

        $_SESSION['auth_user_id'] = $user->id;
        self::$cachedUser = $user;
        self::$loaded = true;
    }

    public static function user(): ?User
    {
        if (self::$loaded) {
            return self::$cachedUser;
        }

        self::$loaded = true;
        $id = $_SESSION['auth_user_id'] ?? null;

        if ($id === null) {
            self::$cachedUser = null;
            return null;
        }

        try {
            $user = User::find($id);
        } catch (\Throwable) {
            $user = null;
        }

        if (!$user || !$user->is_active) {
            unset($_SESSION['auth_user_id']);
            self::$cachedUser = null;
            return null;
        }

        self::$cachedUser = $user;
        return $user;
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function id(): ?int
    {
        $user = self::user();
        return $user?->id;
    }

    public static function logout(): void
    {
        unset($_SESSION['auth_user_id']);
        self::$cachedUser = null;
        self::$loaded = true;

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }
}
