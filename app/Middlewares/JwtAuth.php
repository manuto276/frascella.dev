<?php

namespace App\Middlewares;

use App\Services\JwtService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Slim\Psr7\Response as SlimResponse;

class JwtAuth implements MiddlewareInterface
{
    public function __construct(
        private JwtService $jwt,
        private array $jwtConfig
    ) {}

    private function extractToken(Request $request): ?string
    {
        $auth = $request->getHeaderLine('Authorization');
        if (preg_match('/^Bearer\s+(.+)/i', $auth, $m)) {
            return trim($m[1]);
        }

        $cookieName = $this->jwtConfig['cookie'] ?? 'admin_token';
        $cookies = $request->getCookieParams();
        if (!empty($cookies[$cookieName])) {
            // normalizza in caso di encoding
            return urldecode($cookies[$cookieName]);
        }
        return null;
    }

    public function process(Request $request, Handler $handler): Response
    {
        try {
            $token = $this->extractToken($request);
            if (!$token) return $this->unauthorized($request);

            $claims = $this->jwt->decode($token);

            if (($claims['iss'] ?? null) !== $this->jwtConfig['issuer']) {
                error_log('[JwtAuth] issuer mismatch');
                return $this->unauthorized($request);
            }
            if (($claims['aud'] ?? null) !== $this->jwtConfig['audience']) {
                error_log('[JwtAuth] audience mismatch');
                return $this->unauthorized($request);
            }
            if (($claims['role'] ?? null) !== 'admin') {
                error_log('[JwtAuth] role mismatch');
                return $this->unauthorized($request);
            }

            $request = $request->withAttribute('admin', [
                'id'    => isset($claims['sub']) ? (int)$claims['sub'] : null,
                'email' => $claims['email'] ?? null,
            ]);

            return $handler->handle($request);
        } catch (\Throwable $e) {
            // Vedrai l’errore preciso in output del container
            error_log('[JwtAuth] decode error: ' . $e->getMessage());
            return $this->unauthorized($request);
        }
    }

    private function isApi(Request $request): bool
    {
        $path = $request->getUri()->getPath();
        if (str_starts_with($path, '/admin/api')) return true;

        $accept = strtolower($request->getHeaderLine('Accept'));
        return str_contains($accept, 'application/json');
    }

    private function unauthorized(Request $request): Response
    {
        if ($this->isApi($request)) {
            $res = new SlimResponse(401);
            $res->getBody()->write(json_encode(['error' => 'unauthorized']));
            return $res->withHeader('Content-Type', 'application/json')
                ->withHeader('Cache-Control', 'no-store');
        }

        $res = new SlimResponse(302);
        return $res->withHeader('Location', '/admin/login');
    }
}
