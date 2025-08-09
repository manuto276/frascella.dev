<?php
use App\Database\Migration;
use App\Database\Connection;

return function (Connection $connection): Migration {
    return new class($connection) extends Migration {
        public function up(): void
        {
            $pdo = $this->connection->getPdo();
            $stmt = $pdo->prepare(
                "CREATE TABLE IF NOT EXISTS refresh_tokens (
                    id BIGINT AUTO_INCREMENT PRIMARY KEY,
                    admin_id BIGINT NOT NULL,
                    token_hash      CHAR(64) NOT NULL,          -- sha256 hex del token
                    family_id       CHAR(36) NOT NULL,          -- per reuse detection (UUID v4)
                    user_agent      VARCHAR(255) NULL,
                    ip_address      VARCHAR(45) NULL,
                    created_at      DATETIME NOT NULL,
                    expires_at      DATETIME NOT NULL,
                    rotated_at      DATETIME NULL,
                    revoked_at      DATETIME NULL,
                    reason_revoked  VARCHAR(50) NULL,
                    UNIQUE KEY uk_token_hash (token_hash),
                    KEY idx_admin_expires (admin_id, expires_at)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
                "
            );
            $stmt->execute();
        }

        public function down(): void
        {
            $pdo = $this->connection->getPdo();
            $pdo->exec("DROP TABLE IF EXISTS refresh_tokens;");
            $pdo->exec("DROP INDEX IF EXISTS uk_token_hash ON refresh_tokens;");
            $pdo->exec("DROP INDEX IF EXISTS idx_admin_expires ON refresh_tokens;");
        }
    };
};