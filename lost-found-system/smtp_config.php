<?php
// Basic SMTP configuration. Fill these values with your SMTP provider details.
// Never commit real credentials to version control.

return [
    'host' => 'smtp.example.com',      // e.g., smtp.gmail.com
    'port' => 587,                     // 587 (TLS) or 465 (SSL)
    'encryption' => 'tls',             // 'tls' or 'ssl'
    'username' => 'no-reply@example.com',
    'password' => 'CHANGE_ME',

    // From details
    'from_email' => 'no-reply@example.com',
    'from_name'  => 'Lost & Found System',
    // Set to true to enable verbose SMTP debugging (errors returned in response)
    'debug'      => false,
];


