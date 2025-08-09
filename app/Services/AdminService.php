<?php

namespace App\Services;

use App\Database\Connection;

final class AdminService
{
    public function __construct(private Connection $connection) {}

    public function createAdmin(string $name, string $email, string $password): void
    {
        $stmt = $this->connection->getPdo()->prepare("
            INSERT INTO admins (name, email, password_hash, created_at)
            VALUES (:name, :email, :password_hash, NOW())
        ");
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':password_hash' => password_hash($password, PASSWORD_DEFAULT),
        ]);
    }

    public function shouldShowSetupForm(): bool
    {
        $stmt = $this->connection->getPdo()->query("SELECT COUNT(*) FROM admins");
        $count = (int) $stmt->fetchColumn();
        return $count === 0;
    }
}