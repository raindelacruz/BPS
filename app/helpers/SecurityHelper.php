<?php

namespace App\Helpers;

class SecurityHelper
{
    public static function requireAuth(): void
    {
        if (!SessionHelper::get('auth_user')) {
            SessionHelper::flash('error', 'Please log in to continue.');
            ResponseHelper::redirect('login');
        }
    }

    public static function requireGuest(): void
    {
        if (SessionHelper::get('auth_user')) {
            ResponseHelper::redirect('dashboard');
        }
    }

    public static function requireRole(string $role): void
    {
        $user = SessionHelper::get('auth_user');

        if (!$user || ($user['role'] ?? null) !== $role) {
            ResponseHelper::abort(403, 'You are not authorized to access this resource.');
        }
    }

    public static function csrfToken(): string
    {
        $token = SessionHelper::get('_csrf_token');

        if (!$token) {
            $token = bin2hex(random_bytes(32));
            SessionHelper::put('_csrf_token', $token);
        }

        return $token;
    }

    public static function verifyCsrf(?string $token): bool
    {
        $sessionToken = SessionHelper::get('_csrf_token');

        return is_string($token) && is_string($sessionToken) && hash_equals($sessionToken, $token);
    }

    public static function currentUser(): ?array
    {
        $user = SessionHelper::get('auth_user');

        return is_array($user) ? $user : null;
    }
}
