<?php

use Slim\App;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

return function (App $app) {
    $app->get('/', function (Request $request, Response $response) {
        ob_start();
        include __DIR__ . '/../views/home.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response;
    });
};
