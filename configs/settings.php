<?php

use DI\Container;
use Dotenv\Dotenv;

return function (Container $container) {
    $rootPath = dirname(__DIR__);

    if (file_exists($rootPath . '/.env')) {
        $dotenv = Dotenv::createImmutable($rootPath);
        $dotenv->safeLoad();
    }

    $container->set('settings', [
        'displayErrorDetails' => filter_var($_ENV['APP_DEBUG'] ?? true, FILTER_VALIDATE_BOOL),
        'logErrors'           => true,
        'logErrorDetails'     => true,

        'db' => [
            'driver'   => $_ENV['DB_DRIVER']   ?? 'mysql',
            'host'     => $_ENV['DB_HOST']     ?? '127.0.0.1',
            'port'     => $_ENV['DB_PORT']     ?? '3306',
            'database' => $_ENV['DB_NAME']     ?? 'portfolio',
            'username' => $_ENV['DB_USER']     ?? 'root',
            'password' => $_ENV['DB_PASS']     ?? '',
            'charset'  => $_ENV['DB_CHARSET']  ?? 'utf8mb4',
        ],

        'jwt' => [
            'secret'   => $_ENV['JWT_SECRET']   ?? 'change-me-please',
            'issuer'   => $_ENV['JWT_ISSUER']   ?? 'portfolio-app',
            'audience' => $_ENV['JWT_AUDIENCE'] ?? 'portfolio-admin',
            'ttl'      => (int)($_ENV['JWT_TTL'] ?? 3600),        // seconds (1h)
            'cookie'   => $_ENV['JWT_COOKIE']   ?? 'admin_token', // cookie name
        ],

        // Opzionale: percorso base app
        'paths' => [
            'root'      => $rootPath,
            'public'    => $rootPath . '/public',
            'views'     => $rootPath . '/views',
            'migrations' => $rootPath . '/app/Database/Migrations',
        ]
    ]);
};
