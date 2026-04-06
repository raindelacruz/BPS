<?php

namespace App\Helpers;

use Throwable;

class LogHelper
{
    public static function error(string $message, array $context = [], ?Throwable $throwable = null): void
    {
        $directory = rtrim((string) app('app.storage_path', dirname(__DIR__, 2) . '/storage'), '/\\') . '/logs';

        if (!is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        $payload = [
            'timestamp' => date('Y-m-d H:i:s'),
            'message' => $message,
            'context' => $context,
        ];

        if ($throwable) {
            $payload['exception'] = [
                'type' => $throwable::class,
                'message' => $throwable->getMessage(),
                'file' => $throwable->getFile(),
                'line' => $throwable->getLine(),
                'trace' => $throwable->getTraceAsString(),
            ];
        }

        file_put_contents($directory . '/app.log', json_encode($payload, JSON_UNESCAPED_SLASHES) . PHP_EOL, FILE_APPEND);
    }
}
