<?php

use Slim\App;
use Slim\Routing\RouteCollectorProxy as Group;
use App\Controllers\AdminDashboardController;
use App\Controllers\TrafficApiController;
use App\Middlewares\JwtAuth;

return function (App $app) {
    $c = $app->getContainer();

    // Admin HTML
    $app->group('/admin', function (Group $group) {
        $group->get('', function ($request, $response) {
            return $response->withHeader('Location', '/admin/dashboard')->withStatus(302);
        })->setName('admin.home');

        $group->get('/dashboard', [AdminDashboardController::class, 'index'])->setName('admin.dashboard');
    })->add($c->get(JwtAuth::class));

    // Admin API
    $app->group('/admin/api', function (Group $group) {
        $group->get('/traffic/summary', [TrafficApiController::class, 'summary']);
        $group->get('/traffic/timeseries', [TrafficApiController::class, 'timeseries']);
    })->add($c->get(JwtAuth::class));
};
