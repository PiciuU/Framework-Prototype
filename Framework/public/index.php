<?php

define('DREAMCODE_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
*/

require __DIR__.'/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Build kernel
|--------------------------------------------------------------------------
*/

$app = require_once __DIR__.'/../app/Kernel.php';

$request = $app->getRequest();

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
*/

require_once __DIR__.'/../config/app.php';

if (str_starts_with($_SERVER['REQUEST_URI'], '/api')) {
    DEFINE('ROUTING_TYPE', 'api');
    require_once __DIR__.'/../routes/api.php';
}
else {
    DEFINE('ROUTING_TYPE', 'web');
    require_once __DIR__.'/../routes/web.php';
}

require_once __DIR__.'/../app/Router.php';