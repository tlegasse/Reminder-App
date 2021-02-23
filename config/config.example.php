<?php

// General configurations
$config = [
    'db' => [
        'username' => '',
        'password' => '',
        'db_conn_str' => 'mysql:host={host};dbname={dbname}',
    ],
    'session' => [
        'age' => 86400
    ],
    'security' => [
        'salt' => "" // Salt your passwords
    ],
    'email' => [
        'sender' => '', // Admin email
    ],
    'general' => [
        'app_name' => '' // App name
    ]
];
