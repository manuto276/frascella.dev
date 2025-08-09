<?php

namespace App\Controllers;

use App\Database\Connection;
use App\Services\JwtService;
use App\Services\RefreshTokenService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AuthController
{
    public function __construct(
        private Connection $connection,
        private JwtService $jwt,
        private RefreshTokenService $refreshTokenService,
        private array $jwtConfig,
        private array $refreshConfig
    ) {}

    public function loginForm(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = $request->getQueryParams();
        $loginError = !empty($params['error']) ? 'Invalid credentials.' : '';
        ob_start();
        include __DIR__ . '/../../views/pages/admin/login.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response;
    }

    public function login(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data  = (array) ($request->getParsedBody() ?? []);
        $email = trim($data['email'] ?? '');
        $pass  = (string) ($data['password'] ?? '');

        $stmt = $this->connection->getPdo()->prepare(
            "SELECT id, email, password_hash FROM admins WHERE email = :email LIMIT 1"
        );
        $stmt->execute([':email' => $email]);
        $admin = $stmt->fetch();

        if (!$admin || !password_verify($pass, $admin['password_hash'])) {
            return $response->withHeader('Location', '/admin/login?error=1')->withStatus(302);
        }

        $token = $this->jwt->issueToken((int)$admin['id'], $admin['email']);

        $meta = $this->refreshTokenService->issue(
            (int)$admin['id'],
            $request->getHeaderLine('User-Agent') ?? null,
            $request->getAttribute('ip_address') ?? null
        );

        // HttpOnly cookie for browser access
        $cookieName = $this->jwtConfig['cookie'] ?? 'admin_token';
        $ttl     = (int)$this->jwtConfig['ttl'];
        $expires = gmdate('D, d M Y H:i:s T', time() + $ttl);
        $secure  = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';

        $cookie = sprintf(
            '%s=%s; Expires=%s; Path=/; HttpOnly; SameSite=Lax%s',
            $cookieName,
            rawurlencode($token), // safe in header
            $expires,
            $secure ? '; Secure' : ''
        );

        $refreshCookieName = $this->refreshConfig['cookie'] ?? 'admin_refresh';
        $refreshExpires = gmdate('D, d M Y H:i:s T', time() + $this->refreshConfig['ttl']);
        $refreshCookie = sprintf(
            '%s=%s; Expires=%s; Path=%s; HttpOnly; SameSite=%s%s',
            $refreshCookieName,
            rawurlencode($meta['token']),
            $refreshExpires,
            $this->refreshConfig['path'],
            $this->refreshConfig['sameSite'],
            !empty($this->refreshConfig['domain']) ? '; Domain=' . $this->refreshConfig['domain'] : ''
        );

        $response = $response->withAddedHeader('Set-Cookie', $cookie);
        $response = $response->withAddedHeader('Set-Cookie', $refreshCookie);
        return $response->withHeader('Location', '/admin/dashboard')->withStatus(302);
    }

    public function logout(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $cookieName = $this->jwtConfig['cookie'] ?? 'admin_token';
        $refreshCookieName = $this->refreshConfig['cookie'] ?? 'admin_refresh';
        $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';

        $admin = $request->getAttribute('admin');

        if ($admin && $admin['id']) {
            $this->refreshTokenService->revokeAllForAdmin((int)$admin['id'], 'logout');
        }

        foreach ([$cookieName, $refreshCookieName] as $name) {
            $cookie = sprintf(
                '%s=; Expires=Thu, 01 Jan 1970 00:00:00 GMT; Path=/; HttpOnly%s',
                $name,
                $secure ? '; Secure' : ''
            );
            $response = $response->withAddedHeader('Set-Cookie', $cookie);
        }

        return $response->withHeader('Location', '/admin/login')->withStatus(302);
    }
}
