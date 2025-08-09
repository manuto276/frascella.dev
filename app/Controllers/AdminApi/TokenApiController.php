<?php

namespace App\Controllers\AdminApi;

use App\Database\Connection;
use App\Services\JwtService;
use App\Services\RefreshTokenService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response as SlimResponse;

class TokenApiController
{
    public function __construct(
        private Connection $connection,
        private RefreshTokenService $refreshTokenService,
        private JwtService $jwtService,
        private array $jwtConfig,
        private array $refreshConfig
    ) {}

    public function refresh(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            $cookieName = $this->refreshConfig['cookie'] ?? 'admin_refresh';
            $refresh = $request->getCookieParams()[$cookieName] ?? '';
            if (!$refresh) return $this->unauthorized();

            $rot = $this->refreshTokenService->rotate(urldecode($refresh));

            $access = $this->jwtService->issueToken((int)$rot['admin_id']);
            $accessCookie = $this->buildCookie(
                $this->jwtConfig['cookie'] ?? 'admin_token',
                rawurlencode($access),
                time() + (int)$this->jwtConfig['ttl'],
                '/',
                '',
                'Lax',
                $this->isSecure(),
                true
            );

            $refreshCookie = $this->buildCookie(
                $this->refreshConfig['cookie'] ?? 'admin_refresh',
                rawurlencode($rot['token']),
                (int)$rot['expires'],
                $this->refreshConfig['path'] ?? '/admin',
                $this->refreshConfig['domain'] ?? '',
                $this->refreshConfig['sameSite'] ?? 'Strict',
                $this->isSecure(),
                true
            );

            $res = new SlimResponse(200);
            $res->getBody()->write(json_encode(['ok' => true]));
            return $res->withHeader('Content-Type', 'application/json')
                ->withAddedHeader('Set-Cookie', $accessCookie)
                ->withAddedHeader('Set-Cookie', $refreshCookie)
                ->withHeader('Cache-Control', 'no-store');
        } catch (\Throwable $e) {
            error_log('[TokenController] refresh error: '.$e->getMessage());
            return $this->unauthorized();
        }
    }

    private function unauthorized(): ResponseInterface
    {
        $res = new SlimResponse(401);
        $res->getBody()->write(json_encode(['error' => 'unauthorized']));
        return $res->withHeader('Content-Type', 'application/json')
            ->withHeader('Cache-Control', 'no-store');
    }

    private function buildCookie(
        string $name,
        string $value,
        int $expires,
        string $path = '/',
        string $domain = '',
        string $sameSite = 'Lax',
        bool $secure = false,
        bool $httpOnly = true
    ): string {
        return sprintf(
            '%s=%s; Expires=%s; Path=%s; Domain=%s; SameSite=%s; %s%s',
            $name,
            $value,
            gmdate('D, d M Y H:i:s T', $expires),
            $path,
            $domain,
            $sameSite,
            $secure ? 'Secure; ' : '',
            $httpOnly ? 'HttpOnly' : ''
        );
    }

    private function isSecure(): bool
    {
        return isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    }
}