<?php

return [
    'name' => 'Bid Posting System',
    'env' => getenv('APP_ENV') ?: 'development',
    'debug' => filter_var(getenv('APP_DEBUG') ?: true, FILTER_VALIDATE_BOOLEAN),
    'url' => getenv('APP_URL') ?: 'http://localhost/BPS/public',
    'timezone' => getenv('APP_TIMEZONE') ?: 'Asia/Manila',
    'session_name' => getenv('SESSION_NAME') ?: 'ebps_session',
    'verification_code_expiry_minutes' => (int) (getenv('VERIFICATION_CODE_EXPIRY_MINUTES') ?: 15),
    'views_path' => dirname(__DIR__) . '/views',
    'storage_path' => dirname(dirname(__DIR__)) . '/storage',
    'session_path' => dirname(dirname(__DIR__)) . '/storage/temp/sessions',
    'upload_path' => dirname(dirname(__DIR__)) . '/storage/uploads/notices',
];
