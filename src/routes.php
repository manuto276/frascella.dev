<?php

use Slim\App;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

return function (App $app) {
    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write("Benvenuto!");
        return $response;
    });

    $app->get('/users', function (Request $request, Response $response) {
        $users = \App\Models\User::all();
        $response->getBody()->write($users->toJson());
        return $response->withHeader('Content-Type', 'application/json');
    });
};
