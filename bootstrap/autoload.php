<?php

spl_autoload_register(static function (string $class): void {
    $prefixes = [
        'App\\Controllers\\' => dirname(__DIR__) . '/app/controllers/',
        'App\\Models\\' => dirname(__DIR__) . '/app/models/',
        'App\\Services\\' => dirname(__DIR__) . '/app/services/',
        'App\\Helpers\\' => dirname(__DIR__) . '/app/helpers/',
        'Bootstrap\\' => __DIR__ . '/',
    ];

    foreach ($prefixes as $prefix => $baseDir) {
        if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
            continue;
        }

        $relativeClass = substr($class, strlen($prefix));
        $path = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

        if (is_file($path)) {
            require_once $path;
        }
    }
});
