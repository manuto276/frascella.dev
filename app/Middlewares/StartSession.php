<?php
namespace App\Middlewares;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;

class StartSession implements MiddlewareInterface
{
    public function process(Request $request, Handler $handler): Response
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            // cookie di sessione “lax” per evitare problemi col fetch same-origin
            session_set_cookie_params([
                'httponly' => true,
                'samesite' => 'Lax',
                'secure'   => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
                'path'     => '/',
            ]);
            @session_start();
        }
        return $handler->handle($request);
    }
}
