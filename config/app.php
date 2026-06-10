<?php
return [
    'app_name'     => $_ENV['APP_NAME'] ?? 'SMTP Management Platform',
    'app_version'  => '1.0.0',
    'timezone'     => $_ENV['APP_TIMEZONE'] ?? 'UTC',
    'session_name' => $_ENV['SESSION_NAME'] ?? 'SMMP_SESSION',
    'debug'        => ($_ENV['APP_DEBUG'] ?? 'false') === 'true',
    'otp_expiry'   => 300,
];