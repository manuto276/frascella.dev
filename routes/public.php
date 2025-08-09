<?php

use Slim\App;
use Slim\Routing\RouteCollectorProxy as Group;

use App\Controllers\HomeController;
use App\Controllers\CsrfController;
use App\Controllers\PublicApi\ContactApiController;
use App\Controllers\SetupController;
use App\Middlewares\AdminSetupCheck;
use App\Middlewares\CsrfGuard;
use App\Middlewares\StartSession;
use App\Middlewares\TrafficLogger;

return function (App $app) {
    $c = $app->getContainer();

    $app->add(StartSession::class);

    $app->get('/api/csrf-token', [CsrfController::class, 'token']);

    $app->group('/api', function (Group $group) use ($c) {
        $group->post('/contact', [ContactApiController::class, 'submit'])->setName('api.contact.submit');
    })->add(new CsrfGuard('X-CSRF-Token'));

    // Middleware di base applicati alle rotte "web" (pubbliche)
    $app->group('', function (Group $group) use ($c) {
        $group->get('/', [HomeController::class, 'index'])->setName('home');

        // Setup admin
        $group->get('/setup', [SetupController::class, 'form'])->setName('setup.form');
        $group->post('/setup', [SetupController::class, 'submit'])->setName('setup.submit');
        $group->get('/setup/complete', [SetupController::class, 'complete'])->setName('setup.complete');
    })
    ->add(new AdminSetupCheck($c->get(\App\Database\Connection::class)))
    ->add(new TrafficLogger($c->get(\App\Database\Connection::class)));
};
