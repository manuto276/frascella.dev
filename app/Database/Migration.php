<?php

namespace App\Database;

abstract class Migration
{
    protected Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    abstract public function up(): void;

    abstract public function down(): void;

    protected function getConnection(): Connection
    {
        return $this->connection;
    }
}