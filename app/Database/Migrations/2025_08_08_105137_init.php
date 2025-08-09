<?php

use App\Database\Migration;
use App\Database\Connection;

return function (Connection $connection): Migration {
    return new class($connection) extends Migration {
        public function up(): void
        {
            $pdo = $this->getConnection()->getPdo();

            $pdo->exec("
                CREATE TABLE IF NOT EXISTS admins (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    email VARCHAR(191) NOT NULL UNIQUE,
                    password_hash VARCHAR(255) NOT NULL,
                    name VARCHAR(191) NULL,
                    last_login_at DATETIME NULL,
                    last_login_ip VARCHAR(45) NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");

            $pdo->exec("
                CREATE TABLE IF NOT EXISTS traffic_logs (
                    id BIGINT AUTO_INCREMENT PRIMARY KEY,
                    session_id VARCHAR(191) NULL,
                    ip_address VARCHAR(45) NOT NULL,
                    user_agent TEXT NULL,
                    method VARCHAR(10) NOT NULL,
                    scheme VARCHAR(10) NOT NULL,
                    host VARCHAR(191) NOT NULL,
                    path VARCHAR(512) NOT NULL,
                    query_string TEXT NULL,
                    full_url TEXT NULL,
                    referrer TEXT NULL,
                    accept_language VARCHAR(191) NULL,
                    accept_header VARCHAR(191) NULL,
                    route_name VARCHAR(191) NULL,
                    status_code SMALLINT NULL,
                    response_time_ms INT NULL,
                    is_ajax TINYINT(1) DEFAULT 0,
                    headers JSON NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    INDEX idx_created_at (created_at),
                    INDEX idx_path (path(191)),
                    INDEX idx_ip (ip_address),
                    INDEX idx_route (route_name)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");
        }

        public function down(): void
        {
            $pdo = $this->getConnection()->getPdo();

            $pdo->exec("DROP TABLE IF EXISTS traffic_logs;");
            $pdo->exec("DROP TABLE IF EXISTS admins;");
        }
    };
};
