<?php

return [
    'transport' => getenv('MAIL_TRANSPORT') ?: 'smtp',
    'host' => getenv('MAIL_HOST') ?: 'smtp.gmail.com',
    'port' => getenv('MAIL_PORT') ?: '587',
    'username' => getenv('MAIL_USERNAME') ?: 'tech.support@nfa.gov.ph',
    'password' => getenv('MAIL_PASSWORD') ?: 'azqhbbfbhhttstpx',
    'encryption' => getenv('MAIL_ENCRYPTION') ?: 'tls',
    'timeout' => (int) (getenv('MAIL_TIMEOUT') ?: 10),
    'from_address' => getenv('MAIL_FROM_ADDRESS') ?: 'tech.support@nfa.gov.ph',
    'from_name' => getenv('MAIL_FROM_NAME') ?: 'eBPS Technical Support',
];
