<?php

declare(strict_types=1);

use Slim\App;

return function (App $app): void {
    $app->addBodyParsingMiddleware();

    (require __DIR__ . '/../routes/web.php')($app);
};
