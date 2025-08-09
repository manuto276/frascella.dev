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

        'mail' => [
            'driver'   => $_ENV['MAIL_DRIVER']   ?? 'smtp',
            'host'     => $_ENV['MAIL_HOST']     ?? '127.0.0.1',
            'port'     => $_ENV['MAIL_PORT']     ?? '587',
            'username' => $_ENV['MAIL_USER']     ?? 'user@example.com',
            'password' => $_ENV['MAIL_PASS']     ?? '',
            'encryption' => $_ENV['MAIL_ENCRYPTION'] ?? 'tls',
            'from'     => [
                'address' => $_ENV['MAIL_FROM_ADDRESS'] ?? 'no-reply@example.com',
                'name'    => $_ENV['MAIL_FROM_NAME'] ?? 'Portfolio Admin',
            ],
            'to' => [
                'address' => $_ENV['MAIL_TO_ADDRESS'] ?? 'user@example.com',
                'name'    => $_ENV['MAIL_TO_NAME'] ?? 'User',
            ],
        ],

        'paths' => [
            'root'      => $rootPath,
            'public'    => $rootPath . '/public',
            'views'     => $rootPath . '/views',
            'migrations' => $rootPath . '/app/Database/Migrations',
        ]
    ]);
};
