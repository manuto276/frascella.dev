<?php

namespace App\Middlewares;

use App\Database\Connection;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Slim\Psr7\Response as SlimResponse;

class AdminSetupCheck implements MiddlewareInterface
{
    public function __construct(private Connection $connection) {}

    public function process(Request $request, Handler $handler): Response
    {
        $path = $request->getUri()->getPath();
        if (preg_match('#^/(setup(/complete)?|css|js|images|assets)#i', $path)) {
            return $handler->handle($request);
        }

        $count = (int) $this->connection->getPdo()
            ->query("SELECT COUNT(*) FROM admins")
            ->fetchColumn();

        if ($count === 0) {
            $res = new SlimResponse(302);
            return $res->withHeader('Location', '/setup');
        }

        return $handler->handle($request);
    }
}