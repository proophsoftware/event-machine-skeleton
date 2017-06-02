<?php
declare(strict_types = 1);

namespace App\Config;

return [
    'environment' => getenv('PROOPH_ENV')?: 'prod',
    'pdo' => [
        'dsn' => getenv('PDO_DSN'),
        'user' => getenv('PDO_USER'),
        'pwd' => getenv('PDO_PWD'),
    ],
    'mongo' => [
        'server' => getenv('MONGO_SERVER'),
        'db' => getenv('MONGO_DB_NAME'),
    ],
    'event_machine' => [
        'descriptions' => []
    ]
];