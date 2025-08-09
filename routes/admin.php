<?php

use App\Controllers\AdminApi\ContactsApiController;
use Slim\App;
use Slim\Routing\RouteCollectorProxy as Group;
use App\Controllers\AdminDashboardController;
use App\Controllers\AuthController;
use App\Controllers\AdminApi\TrafficApiController;
use App\Controllers\AdminContactsController;
use App\Middlewares\JwtAuth;

return function (App $app) {
    $c = $app->getContainer();

    // Admin HTML
    $app->get('/admin/login', [AuthController::class, 'loginForm'])->setName('admin.login.form');
    $app->post('/admin/login', [AuthController::class, 'login'])->setName('admin.login');
    $app->get('/admin/logout', [AuthController::class, 'logout'])->setName('admin.logout');

    $app->group('/admin', function (Group $group) {
        $group->get('', function ($request, $response) {
            return $response->withHeader('Location', '/admin/dashboard')->withStatus(302);
        })->setName('admin.home');

        $group->get('/dashboard', [AdminDashboardController::class, 'index'])->setName('admin.dashboard.index');
        $group->get('/contacts', [AdminContactsController::class, 'index'])->setName('admin.contacts.index');
    })->add($c->get(JwtAuth::class));

    // Admin API
    $app->group('/admin/api', function (Group $group) {
        $group->get('/traffic/summary', [TrafficApiController::class, 'summary']);
        $group->get('/traffic/timeseries', [TrafficApiController::class, 'timeseries']);
        $group->get('/contacts/latest', [ContactsApiController::class, 'latest']);
        $group->get('/contacts', [ContactsApiController::class, 'index']);
        $group->post('/contacts/{id}/status', [ContactsApiController::class, 'updateStatus'])->setName('admin.contacts.updateStatus');
    })->add($c->get(JwtAuth::class));
};
