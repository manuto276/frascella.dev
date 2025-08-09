<?php

namespace App\Controllers;

use App\Database\Connection;
use App\Services\JwtService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AuthController
{
    public function __construct(
        private Connection $connection,
        private JwtService $jwt,
        private array $jwtConfig // settings['jwt']
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

        $response = $response->withAddedHeader('Set-Cookie', $cookie);
        return $response->withHeader('Location', '/admin/dashboard')->withStatus(302);
    }

    public function logout(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $cookieName = $this->jwtConfig['cookie'] ?? 'admin_token';
        $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        setcookie($cookieName, '', [
            'expires'  => time() - 3600,
            'path'     => '/',
            'domain'   => '',
            'secure'   => $secure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        return $response->withHeader('Location', '/admin/login')->withStatus(302);
    }
}
