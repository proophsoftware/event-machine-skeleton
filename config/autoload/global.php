<?php
declare(strict_types = 1);

namespace App\Config;

return [
    'pdo' => [
        'dsn' => getenv('PDO_DSN'),
        'user' => getenv('PDO_USER'),
        'pwd' => getenv('PDO_PWD'),
    ]
];