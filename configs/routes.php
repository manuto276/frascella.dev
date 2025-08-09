<?php
declare(strict_types=1);

use Slim\App;

/**
 * Registra tutte le rotte dell'app.
 *
 * @param App $app
 * @return void
 */
return function (App $app): void {
    // Rotte web (frontend, pagine pubbliche)
    if (file_exists(__DIR__ . '/../routes/web.php')) {
        (require __DIR__ . '/../routes/web.php')($app);
    }

    // Rotte API (se presenti)
    if (file_exists(__DIR__ . '/../routes/api.php')) {
        (require __DIR__ . '/../routes/api.php')($app);
    }

    // Eventuali altre rotte (admin, ecc.)
    if (file_exists(__DIR__ . '/../routes/admin.php')) {
        (require __DIR__ . '/../routes/admin.php')($app);
    }
};
