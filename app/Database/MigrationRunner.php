<?php

namespace App\Database;

use PDO;

class MigrationRunner
{
    private Connection $connection;
    private string $migrationPath;

    public function __construct(Connection $connection, string $migrationPath)
    {
        $this->connection = $connection;
        $this->migrationPath = $migrationPath;
        $this->createChangelogTable();
    }


    private function createChangelogTable(): void
    {
        // Create the DB_CHANGELOG table if it does not exist
        $sql = "CREATE TABLE IF NOT EXISTS DB_CHANGELOG (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) NOT NULL,
            batch VARCHAR(255) NOT NULL,
            applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";

        $this->connection->getPdo()->exec($sql);

        // Create a unique index on the migration column to prevent duplicates
        $check = $this->connection->getPdo()->query("SHOW INDEX FROM DB_CHANGELOG WHERE Key_name = 'idx_migration'");
        if ($check->rowCount() === 0) {
            $this->connection->getPdo()->exec("CREATE UNIQUE INDEX idx_migration ON DB_CHANGELOG (migration)");
        }
    }

    public function migrate(): void
    {
        $executed = $this->getExecutedMigrations();

        $migrations = glob($this->migrationPath . '/*.php');
        $batch = $this->getLastBatch() + 1;

        foreach ($migrations as $migrationFile) {
            $migrationName = basename($migrationFile, '.php');

            if (in_array($migrationName, $executed)) {
                continue; // Skip already executed migrations
            }

            $factory = require $migrationFile;

            if (!is_callable($factory)) {
                throw new \RuntimeException("Migration file $migrationFile must return a callable factory.");
            }

            $migration = $factory($this->connection);

            if (!$migration instanceof Migration) {
                throw new \RuntimeException("Factory in $migrationFile must return an instance of App\\Database\\Migration.");
            }

            $migration->up();
            
            // Log the migration in the DB_CHANGELOG table
            $stmt = $this->connection->getPdo()->prepare("INSERT INTO DB_CHANGELOG (migration, batch) VALUES (:migration, :batch)");
            $stmt->execute([
                ':migration' => $migrationName,
                ':batch' => $batch
            ]);

            echo "Migrated: $migrationName\n";
        }
    }

    public function rollback(int $steps = 1): void
    {
        if ($steps < 1) {
            echo "⚠ Invalid rollback step count.\n";
            return;
        }

        $maxBatch = $this->getLastBatch();
        if ($maxBatch === 0) {
            echo "No migrations to rollback.\n";
            return;
        }

        $targetBatches = range($maxBatch, max($maxBatch - $steps + 1, 1));

        foreach ($targetBatches as $batch) {
            $stmt = $this->connection->getPdo()->prepare(
                "SELECT migration FROM DB_CHANGELOG WHERE batch = :batch ORDER BY id DESC"
            );
            $stmt->execute([':batch' => $batch]);
            $migrations = $stmt->fetchAll(PDO::FETCH_COLUMN);

            foreach ($migrations as $migrationName) {
                $migrationFile = $this->migrationPath . '/' . $migrationName . '.php';
                if (!file_exists($migrationFile)) {
                    echo "⚠ Skipped missing file: $migrationName\n";

                    $del = $this->connection->getPdo()->prepare(
                        "DELETE FROM DB_CHANGELOG WHERE migration = :migration"
                    );
                    $del->execute([':migration' => $migrationName]);
                    continue;
                }

                $factory = require $migrationFile;
                if (!is_callable($factory)) {
                    throw new \RuntimeException("Migration file $migrationFile must return a callable factory.");
                }

                $migration = $factory($this->connection);
                if (!$migration instanceof Migration) {
                    throw new \RuntimeException("Factory in $migrationFile must return an instance of App\\Database\\Migration.");
                }

                $migration->down();

                $del = $this->connection->getPdo()->prepare(
                    "DELETE FROM DB_CHANGELOG WHERE migration = :migration"
                );
                $del->execute([':migration' => $migrationName]);

                echo "Rolled back: $migrationName\n";
            }
        }
    }

    public function reset(): void
    {
        $this->rollback($this->getLastBatch());
        echo "All migrations rolled back.\n";
    }

    public function getExecutedMigrations(): array
    {
        $stmt = $this->connection->getPdo()->query("SELECT migration FROM DB_CHANGELOG");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getLastBatch(): int
    {
        $stmt = $this->connection->getPdo()->query("SELECT MAX(batch) FROM DB_CHANGELOG");
        $result = $stmt->fetchColumn();
        return $result ? (int)$result : 0;
    }
}