<?php
namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

final class JwtService
{
    public function __construct(private array $config) {
        // 60s di tolleranza su iat/nbf/exp
        if (!defined('JWT_LEEWAY_SET')) {
            JWT::$leeway = 60;
            define('JWT_LEEWAY_SET', true);
        }
    }

    public function issueToken(int $adminId, ?string $email = null): string
    {
        $now = time();
        $payload = [
            'iss'   => $this->config['issuer'],
            'aud'   => $this->config['audience'],
            'iat'   => $now,
            'nbf'   => $now - 5,
            'exp'   => $now + (int)$this->config['ttl'],
            'sub'   => (string)$adminId,
            'email' => $email,
            'role'  => 'admin',
        ];
        return JWT::encode($payload, $this->config['secret'], 'HS256');
    }

    public function decode(string $jwt): array
    {
        // Se il cookie è url-encoded, normalizziamo
        $jwt = urldecode($jwt);
        $decoded = JWT::decode($jwt, new Key($this->config['secret'], 'HS256'));
        return json_decode(json_encode($decoded), true);
    }
}
