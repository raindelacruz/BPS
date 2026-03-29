<?php

return [
    'host' => getenv('MAIL_HOST') ?: 'localhost',
    'port' => getenv('MAIL_PORT') ?: '1025',
    'username' => getenv('MAIL_USERNAME') ?: '',
    'password' => getenv('MAIL_PASSWORD') ?: '',
    'encryption' => getenv('MAIL_ENCRYPTION') ?: null,
    'from_address' => getenv('MAIL_FROM_ADDRESS') ?: 'noreply@agency.gov.ph',
    'from_name' => getenv('MAIL_FROM_NAME') ?: 'eBPS',
];
