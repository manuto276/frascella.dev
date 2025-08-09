<?php
namespace App\Middlewares;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Slim\Psr7\Response as SlimResponse;

class CsrfGuard implements MiddlewareInterface
{
    public function __construct(private string $header = 'X-CSRF-Token') {}

    public static function ensureToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public function process(Request $request, Handler $handler): Response
    {
        $method = strtoupper($request->getMethod());
        $unsafe = in_array($method, ['POST','PUT','PATCH','DELETE'], true);

        if (!$unsafe) {
            return $handler->handle($request);
        }

        $token = $request->getHeaderLine($this->header);
        $valid = isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);

        if (!$valid) {
            $res = new SlimResponse(403);
            $res->getBody()->write(json_encode(['error' => 'csrf_failed']));
            return $res->withHeader('Content-Type', 'application/json')->withHeader('Cache-Control', 'no-store');
        }

        return $handler->handle($request);
    }
}
