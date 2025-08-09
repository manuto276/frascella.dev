<?php

namespace App\Database;

use PDO;
use PDOException;

class Connection
{
    private PDO $pdo;

    public function __construct(array $settings)
    {
        $dsn = sprintf( 
            '%s:host=%s;port=%s;dbname=%s;charset=%s',
            $settings['driver'],
            $settings['host'],
            $settings['port'],
            $settings['database'],
            $settings['charset']
        );

        try {
            $this->pdo = new PDO($dsn, $settings['username'], $settings['password']);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new \RuntimeException('Database connection failed: ' . $e->getMessage());
        }
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }
}