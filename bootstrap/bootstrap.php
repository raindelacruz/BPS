<?php

use Bootstrap\App;
use Bootstrap\Router;

require_once __DIR__ . '/autoload.php';

$router = new Router();
$app = new App($router);

function app(?string $key = null, mixed $default = null): mixed
{
    global $app;

    return $app->config($key, $default);
}

function router(): Router
{
    global $router;

    return $router;
}

$app->bootstrap();

return $app;
