<?php

namespace App\Helpers;

class ResponseHelper
{
    public static function json(array $payload, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_THROW_ON_ERROR);
        exit;
    }

    public static function redirect(string $path): void
    {
        header('Location: ' . self::url($path));
        exit;
    }

    public static function abort(int $status, string $message): void
    {
        http_response_code($status);
        echo $message;
        exit;
    }

    public static function url(string $path = ''): string
    {
        $baseUrl = rtrim(app('app.url', ''), '/');
        $normalizedPath = ltrim($path, '/');

        return $normalizedPath === '' ? $baseUrl : $baseUrl . '/' . $normalizedPath;
    }
}
