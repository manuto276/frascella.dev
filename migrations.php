<?php
declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\Database\Connection;
use App\Database\MigrationRunner;
use App\Database\Migration;
use DI\Container;

// Funzione helper per inizializzare le impostazioni
function loadSettings(): array
{
    $container = new Container();
    $settingsLoader = require __DIR__ . '/configs/settings.php';
    $settingsLoader($container);
    return $container->get('settings');
}

// Funzione helper per ottenere un'istanza di MigrationRunner
function getRunner(array $settings): MigrationRunner
{
    $connection = new Connection($settings['db']);
    return new MigrationRunner($connection, $settings['paths']['migrations']);
}

// Recupero comandi da CLI
$command = $argv[1] ?? null;
$arg = $argv[2] ?? null;

switch ($command) {
    case 'migrate':
        $settings = loadSettings();
        $runner = getRunner($settings);
        $runner->migrate();
        break;

    case 'rollback':
        $settings = loadSettings();
        $runner = getRunner($settings);
        $steps = $arg !== null ? (int)$arg : 1;
        $runner->rollback($steps);
        break;

    case 'status':
        $settings = loadSettings();
        $runner = getRunner($settings);

        $executed = $runner->getExecutedMigrations();
        $files = array_map('basename', glob($settings['paths']['migrations'] . '/*.php', GLOB_NOSORT));
        $pending = array_diff(array_map(fn($f) => basename($f, '.php'), $files), $executed);

        echo "Executed migrations:\n";
        foreach ($executed as $mig) {
            echo "  - $mig\n";
        }

        echo "\nPending migrations:\n";
        foreach ($pending as $mig) {
            echo "  - $mig\n";
        }
        break;

    case 'make':
        if (!$arg) {
            echo "Please provide a migration name.\n";
            exit(1);
        }

        $settings = loadSettings();
        $name = preg_replace('/[^a-z0-9_]/', '_', strtolower($arg));
        $timestamp = date('Y_m_d_His');
        $filename = "{$timestamp}_{$name}.php";
        $filePath = $settings['paths']['migrations'] . '/' . $filename;

        if (!is_dir($settings['paths']['migrations'])) {
            mkdir($settings['paths']['migrations'], 0777, true);
        }

        $template = <<<PHP
<?php
use App\Database\Migration;
use App\Database\Connection;

return function (Connection \$connection): Migration {
    return new class(\$connection) extends Migration {
        public function up(): void
        {
            // TODO: Implement migration logic here
        }

        public function down(): void
        {
            // TODO: Implement rollback logic here
        }
    };
};
PHP;

        file_put_contents($filePath, $template);
        echo "Created new migration: {$filename}\n";
        break;

    default:
        echo "Usage:\n";
        echo "  php migrations.php migrate             Run all pending migrations\n";
        echo "  php migrations.php rollback [steps]    Rollback the last [steps] batches (default 1)\n";
        echo "  php migrations.php status              Show executed and pending migrations\n";
        echo "  php migrations.php make <name>         Create a new empty migration file\n";
        break;
}
