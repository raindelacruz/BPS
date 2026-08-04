<?php

$debugEnv = getenv('APP_DEBUG');
$publicVisibleStatusEnv = getenv('PUBLIC_VISIBLE_STATUSES');
$publicVisibleStatuses = $publicVisibleStatusEnv === false || trim($publicVisibleStatusEnv) === ''
    ? ['open', 'closed']
    : array_values(array_filter(array_map(
        static fn (string $status): string => trim($status),
        explode(',', $publicVisibleStatusEnv)
    )));

$detectedAppUrl = (static function (): string {
    $configuredUrl = getenv('APP_URL');
    if (is_string($configuredUrl) && trim($configuredUrl) !== '') {
        return rtrim(trim($configuredUrl), '/');
    }

    if (PHP_SAPI === 'cli') {
        return 'http://localhost/BPS/public';
    }

    $scheme = 'http';
    $https = strtolower((string) ($_SERVER['HTTPS'] ?? ''));
    $forwardedProto = strtolower(trim((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')));
    $forwardedSsl = strtolower(trim((string) ($_SERVER['HTTP_X_FORWARDED_SSL'] ?? '')));
    $serverPort = (string) ($_SERVER['SERVER_PORT'] ?? '');

    if (
        ($https !== '' && $https !== 'off')
        || $forwardedProto === 'https'
        || $forwardedSsl === 'on'
        || $serverPort === '443'
    ) {
        $scheme = 'https';
    }

    $host = trim((string) ($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost'));
    if ($host === '') {
        $host = 'localhost';
    }

    $scriptName = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
    $basePath = rtrim(str_replace('/index.php', '', $scriptName), '/');

    return $scheme . '://' . $host . $basePath;
})();

return [
    'name' => 'Bid Posting System',
    'env' => getenv('APP_ENV') ?: 'production',
    'debug' => $debugEnv === false ? false : filter_var($debugEnv, FILTER_VALIDATE_BOOLEAN),
    'url' => $detectedAppUrl,
    'timezone' => getenv('APP_TIMEZONE') ?: 'Asia/Manila',
    'session_name' => getenv('SESSION_NAME') ?: 'ebps_session',
    'verification_code_expiry_minutes' => (int) (getenv('VERIFICATION_CODE_EXPIRY_MINUTES') ?: 15),
    'login_max_attempts' => (int) (getenv('LOGIN_MAX_ATTEMPTS') ?: 5),
    'login_decay_minutes' => (int) (getenv('LOGIN_DECAY_MINUTES') ?: 15),
    'max_upload_bytes' => (int) (getenv('MAX_UPLOAD_BYTES') ?: 20 * 1024 * 1024),
    'public_visible_statuses' => $publicVisibleStatuses,
    'views_path' => dirname(__DIR__) . '/views',
    'storage_path' => dirname(dirname(__DIR__)) . '/storage',
    'session_path' => dirname(dirname(__DIR__)) . '/storage/temp/sessions',
    'upload_path' => dirname(dirname(__DIR__)) . '/storage/uploads/notices',
];
