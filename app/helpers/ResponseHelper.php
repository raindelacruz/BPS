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
        $target = str_replace(["\r", "\n"], '', self::path($path));
        header('Location: ' . $target);
        exit;
    }

    public static function abort(int $status, string $message): void
    {
        http_response_code($status);
        echo self::renderErrorPage($status, $message);
        exit;
    }

    public static function url(string $path = ''): string
    {
        return self::path($path);
    }

    public static function absoluteUrl(string $path = ''): string
    {
        $baseUrl = rtrim(app('app.url', ''), '/');
        $normalizedPath = ltrim($path, '/');

        return $normalizedPath === '' ? $baseUrl : $baseUrl . '/' . $normalizedPath;
    }

    public static function path(string $path = ''): string
    {
        $scriptName = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
        $basePath = rtrim(str_replace('/index.php', '', $scriptName), '/');
        $normalizedPath = ltrim($path, '/');

        if ($basePath === '') {
            return $normalizedPath === '' ? '/' : '/' . $normalizedPath;
        }

        return $normalizedPath === '' ? $basePath : $basePath . '/' . $normalizedPath;
    }

    private static function renderErrorPage(int $status, string $message): string
    {
        $safeStatus = htmlspecialchars((string) $status, ENT_QUOTES, 'UTF-8');
        $safeMessage = htmlspecialchars($message !== '' ? $message : 'The requested action could not be completed.', ENT_QUOTES, 'UTF-8');
        $homeUrl = htmlspecialchars(self::url(''), ENT_QUOTES, 'UTF-8');
        $appName = htmlspecialchars((string) app('app.name', 'Bid Posting System'), ENT_QUOTES, 'UTF-8');

        return '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>'
            . $safeStatus . ' | ' . $appName
            . '</title><style>body{margin:0;font-family:Segoe UI,Arial,sans-serif;background:#eef3f8;color:#0f172a;padding:32px}main{max-width:680px;margin:0 auto;background:#fff;border:1px solid #d9e2ec;border-radius:18px;padding:28px;box-shadow:0 16px 36px rgba(15,23,42,.08)}h1{margin:0 0 10px;font-size:1.75rem}p{margin:0 0 12px;line-height:1.5;color:#334155}a{display:inline-flex;align-items:center;justify-content:center;min-height:40px;padding:0 14px;border-radius:10px;border:1px solid #cbd5e1;color:#0f5f8c;text-decoration:none;font-weight:700}a:hover{text-decoration:none;background:#f8fafc}</style></head><body><main><h1>'
            . $safeStatus . '</h1><p>' . $safeMessage . '</p><a href="' . $homeUrl . '">Return to application</a></main></body></html>';
    }
}
