<?php

declare(strict_types=1);

use DI\Container;
use DI\ContainerBuilder;
use Slim\Factory\AppFactory;

use App\Database\Connection;
use App\Middlewares\JwtAuth;
use App\Middlewares\AdminSetupCheck;
use App\Middlewares\TrafficLogger;

use App\Controllers\AuthController;
use App\Controllers\AdminDashboardController;
use App\Controllers\TrafficApiController;
use App\Services\AdminService;
use App\Services\JwtService;
use App\Services\TrafficService;

$containerBuilder = new ContainerBuilder();

$settings = require __DIR__ . '/../configs/settings.php';

$containerBuilder->addDefinitions([
    'settings' => function () use ($settings) {
        $c = new Container();
        $settings($c);
        return $c->get('settings');
    },
    Connection::class => function ($c) {
        $settings = $c->get('settings')['db'];
        return new Connection($settings);
    },
    JwtAuth::class => function($c) {
        return new JwtAuth(
            $c->get(JwtService::class),
            $c->get('settings')['jwt']
        );
    },
    JwtService::class => function($c) {
        return new JwtService($c->get('settings')['jwt']);
    },
    AdminService::class => function($c) {
        return new AdminService($c->get(Connection::class));
    },
    AdminSetupCheck::class => function($c) {
        return new AdminSetupCheck($c->get(Connection::class));
    },
    TrafficLogger::class => function($c) {
        return new TrafficLogger($c->get(Connection::class));
    },
    AdminDashboardController::class => function($c) {
        return new AdminDashboardController();
    },
    AuthController::class => function($c) {
        return new AuthController(
            $c->get(Connection::class),
            $c->get(JwtService::class),
            $c->get('settings')['jwt']
        );
    },
    TrafficService::class => function($c) {
        return new TrafficService($c->get(Connection::class));
    },
    TrafficApiController::class => function($c) {
        return new TrafficApiController(
            $c->get(TrafficService::class)
        );
    },
]);

$container = $containerBuilder->build();

AppFactory::setContainer($container);
$app = AppFactory::create();

return $app;