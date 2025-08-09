<?php

use Slim\App;
use Slim\Routing\RouteCollectorProxy as Group;

use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\SetupController;
use App\Middlewares\AdminSetupCheck;
use App\Middlewares\TrafficLogger;

return function (App $app) {
     $c = $app->getContainer();

    // Middleware di base applicati alle rotte "web" (pubbliche)
    $app->group('', function (Group $group) use ($c) {
        $group->get('/', [HomeController::class, 'index'])->setName('home');

        // Setup admin
        $group->get('/setup', [SetupController::class, 'form'])->setName('setup.form');
        $group->post('/setup', [SetupController::class, 'submit'])->setName('setup.submit');
        $group->get('/setup/complete', [SetupController::class, 'complete'])->setName('setup.complete');

        // Admin login
        $group->get('/admin/login', [AuthController::class, 'loginForm'])->setName('admin.login.form');
        $group->post('/admin/login', [AuthController::class, 'login'])->setName('admin.login');
        $group->get('/admin/logout', [AuthController::class, 'logout'])->setName('admin.logout');
    })
    ->add(new AdminSetupCheck($c->get(\App\Database\Connection::class)))
    ->add(new TrafficLogger($c->get(\App\Database\Connection::class)));
};
