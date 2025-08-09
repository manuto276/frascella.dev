<?php
use App\Database\Migration;
use App\Database\Connection;

return function (Connection $connection): Migration {
    return new class($connection) extends Migration {
        public function up(): void
        {
            $pdo = $this->connection->getPdo();

            $pdo->exec("
                CREATE TABLE IF NOT EXISTS contact_forms (
                    id BIGINT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(191) NOT NULL,
                    email VARCHAR(191) NOT NULL,
                    subject VARCHAR(191) NOT NULL,
                    message TEXT NOT NULL,
                    ip_address VARCHAR(45) NOT NULL,
                    user_agent TEXT NOT NULL,
                    referrer TEXT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    status ENUM('new', 'read', 'replied', 'archived') DEFAULT 'new',
                    INDEX idx_created_at (created_at),
                    INDEX idx_email (email(191)),
                    INDEX idx_status (status)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");
        }

        public function down(): void
        {
            $pdo = $this->connection->getPdo();

            $pdo->exec("DROP TABLE IF EXISTS contact_forms;");
        }
    };
};