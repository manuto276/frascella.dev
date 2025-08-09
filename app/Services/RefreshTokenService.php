<?php
namespace App\Services;

use App\Database\Connection;

final class RefreshTokenService
{
    public function __construct(
        private Connection $connection,
        private array $config
    ) {}

    public static function makeToken(): string
    {
        // 64 bytes -> 128 hex chars; opaco, non JWT
        return bin2hex(random_bytes(64));
    }

    public static function hash(string $token): string
    {
        // sha256 hex (veloce, scopo: lookup; il segreto è l'intero token)
        return hash('sha256', $token);
    }

    public function issue(int $adminId, ?string $ua, ?string $ip): array
    {
        $token   = self::makeToken();
        $hash    = self::hash($token);
        $family  = $this->uuidV4();
        $now     = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $expires = $now->modify('+' . (int)$this->config['ttl'] . ' seconds');

        $pdo = $this->connection->getPdo();

        $stmt = $pdo->prepare(
          "INSERT INTO refresh_tokens (admin_id, token_hash, family_id, user_agent, ip_address, created_at, expires_at)
           VALUES (:admin, :hash, :family, :ua, :ip, :created, :expires)"
        );
        $stmt->execute([
            ':admin'   => $adminId,
            ':hash'    => $hash,
            ':family'  => $family,
            ':ua'      => substr((string)$ua, 0, 255),
            ':ip'      => $ip,
            ':created' => $now->format('Y-m-d H:i:s'),
            ':expires' => $expires->format('Y-m-d H:i:s'),
        ]);

        return ['token' => $token, 'family' => $family, 'expires' => $expires->getTimestamp()];
    }

    public function rotate(string $oldToken): array
    {
        $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $hash = self::hash($oldToken);

        $pdo = $this->connection->getPdo();
        
        $stmt = $pdo->prepare("SELECT * FROM refresh_tokens WHERE token_hash = :h LIMIT 1");
        $stmt->execute([':h' => $hash]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            throw new \RuntimeException('refresh token not found');
        }
        if ($row['revoked_at'] !== null) {
            // reuse detection: se un token già ruotato/revocato viene riusato, revoca tutta la famiglia.
            $this->revokeFamily($row['family_id'], 'reuse_detected');
            throw new \RuntimeException('refresh token reuse detected');
        }
        if (strtotime($row['expires_at']) < $now->getTimestamp()) {
            throw new \RuntimeException('refresh token expired');
        }

        // revoca il precedente e genera nuovo della stessa famiglia
        $upd = $pdo->prepare("UPDATE refresh_tokens SET rotated_at = :now, revoked_at = :now, reason_revoked='rotated' WHERE id = :id");
        $upd->execute([':now' => $now->format('Y-m-d H:i:s'), ':id' => $row['id']]);

        $newToken = self::makeToken();
        $newHash  = self::hash($newToken);
        $expires  = $now->modify('+' . (int)$this->config['ttl'] . ' seconds');

        $ins = $pdo->prepare(
          "INSERT INTO refresh_tokens (admin_id, token_hash, family_id, user_agent, ip_address, created_at, expires_at)
           VALUES (:admin, :hash, :family, :ua, :ip, :created, :expires)"
        );
        $ins->execute([
            ':admin'   => (int)$row['admin_id'],
            ':hash'    => $newHash,
            ':family'  => $row['family_id'],
            ':ua'      => $row['user_agent'],
            ':ip'      => $row['ip_address'],
            ':created' => $now->format('Y-m-d H:i:s'),
            ':expires' => $expires->format('Y-m-d H:i:s'),
        ]);

        return [
            'admin_id' => (int)$row['admin_id'],
            'token'    => $newToken,
            'expires'  => $expires->getTimestamp(),
        ];
    }

    public function revokeFamily(string $familyId, string $reason = 'revoked'): void
    {
        $pdo = $this->connection->getPdo();

        $stmt = $pdo->prepare("UPDATE refresh_tokens SET revoked_at = :now, reason_revoked = :reason WHERE family_id = :fam AND revoked_at IS NULL");
        $stmt->execute([
            ':now'    => (new \DateTimeImmutable('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s'),
            ':reason' => $reason,
            ':fam'    => $familyId
        ]);
    }

    public function revokeAllForAdmin(int $adminId, string $reason = 'logout_all'): void
    {
        $pdo = $this->connection->getPdo();

        $stmt = $pdo->prepare("UPDATE refresh_tokens SET revoked_at = :now, reason_revoked = :reason WHERE admin_id = :admin AND revoked_at IS NULL");
        $stmt->execute([
            ':now'    => (new \DateTimeImmutable('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s'),
            ':reason' => $reason,
            ':admin'  => $adminId
        ]);
    }

    private function uuidV4(): string
    {
        $d = random_bytes(16);
        $d[6] = chr((ord($d[6]) & 0x0f) | 0x40);
        $d[8] = chr((ord($d[8]) & 0x3f) | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($d), 4));
    }
}
