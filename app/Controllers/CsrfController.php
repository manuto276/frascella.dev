<?php
namespace App\Controllers;

use App\Middlewares\CsrfGuard;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CsrfController
{
    public function token(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $token = CsrfGuard::ensureToken();
        $response->getBody()->write(json_encode(['token' => $token]));
        return $response->withHeader('Content-Type', 'application/json')->withHeader('Cache-Control','no-store');
    }
}
