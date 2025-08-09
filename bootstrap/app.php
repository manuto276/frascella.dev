<?php
declare(strict_types=1);

use DI\Container;
use DI\ContainerBuilder;
use Slim\Factory\AppFactory;

// Database & core
use App\Database\Connection;

// Middlewares
use App\Middlewares\StartSession;
use App\Middlewares\JwtAuth;
use App\Middlewares\AdminSetupCheck;
use App\Middlewares\TrafficLogger;

// Services
use App\Services\AdminService;
use App\Services\JwtService;
use App\Services\MailerService;
use App\Services\TrafficService;
use App\Services\ContactService;

// Controllers (web/html)
use App\Controllers\AuthController;
use App\Controllers\AdminDashboardController;
use App\Controllers\AdminContactsController;
use App\Controllers\CsrfController;

// Controllers (API)
use App\Controllers\AdminApi\TrafficApiController;
use App\Controllers\AdminApi\ContactsApiController;
use App\Controllers\PublicApi\ContactApiController;

$containerBuilder = new ContainerBuilder();

// Load settings
$settingsLoader = require __DIR__ . '/../configs/settings.php';
$containerBuilder->addDefinitions([
    'settings' => function () use ($settingsLoader) {
        $c = new Container();
        $settingsLoader($c);
        return $c->get('settings');
    },
]);

// Core services
$containerBuilder->addDefinitions([
    Connection::class => function ($c) {
        return new Connection($c->get('settings')['db']);
    },
]);

// Middlewares (as services when they need deps)
$containerBuilder->addDefinitions([
    StartSession::class   => fn($c) => new StartSession(),
    JwtService::class     => fn($c) => new JwtService($c->get('settings')['jwt']),
    JwtAuth::class        => fn($c) => new JwtAuth($c->get(JwtService::class), $c->get('settings')['jwt']),
    AdminSetupCheck::class=> fn($c) => new AdminSetupCheck($c->get(Connection::class)),
    TrafficLogger::class  => fn($c) => new TrafficLogger($c->get(Connection::class)),
]);

// Domain services
$containerBuilder->addDefinitions([
    AdminService::class   => fn($c) => new AdminService($c->get(Connection::class)),
    MailerService::class  => fn($c) => new MailerService($c->get('settings')['mail']),
    TrafficService::class => fn($c) => new TrafficService($c->get(Connection::class)),
    ContactService::class => fn($c) => new ContactService(
        $c->get(Connection::class),
        $c->get(MailerService::class),
        $c->get('settings')['contact'] ?? $c->get('settings')['mail'] // fallback: usa mail
    ),
]);

// Controllers (web)
$containerBuilder->addDefinitions([
    AuthController::class            => fn($c) => new AuthController($c->get(Connection::class), $c->get(JwtService::class), $c->get('settings')['jwt']),
    AdminDashboardController::class  => fn($c) => new AdminDashboardController(),
    AdminContactsController::class   => fn($c) => new AdminContactsController(),
    CsrfController::class            => fn($c) => new CsrfController(),
]);

// Controllers (API)
$containerBuilder->addDefinitions([
    TrafficApiController::class  => fn($c) => new TrafficApiController($c->get(TrafficService::class)),
    ContactsApiController::class => fn($c) => new ContactsApiController($c->get(ContactService::class)),
    ContactApiController::class  => fn($c) => new ContactApiController($c->get(ContactService::class)),
]);

// Build container
$container = $containerBuilder->build();
AppFactory::setContainer($container);
$app = AppFactory::create();

// Global middlewares
$app->add(StartSession::class);              // necessario per CSRF/session
$app->addBodyParsingMiddleware();            // per parsedBody su POST form/json
$app->addRoutingMiddleware();

// (Opzionali) error middleware, ecc.
// $displayErrorDetails = (bool) ($container->get('settings')['displayErrorDetails'] ?? true);
// $errorMiddleware = $app->addErrorMiddleware($displayErrorDetails, true, true);

return $app;
