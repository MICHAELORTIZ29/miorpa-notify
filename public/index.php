<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Ocultar advertencias de dependencias en respuestas JSON
|--------------------------------------------------------------------------
*/

ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');

error_reporting(
    E_ALL &
    ~E_WARNING &
    ~E_NOTICE &
    ~E_DEPRECATED
);

/*
|--------------------------------------------------------------------------
| Localizar la aplicación Laravel
|--------------------------------------------------------------------------
*/

$basePath = dirname(__DIR__);

if (! is_file($basePath . '/vendor/autoload.php')) {
    $basePath = dirname(__DIR__) . '/miorpa-repo';
}

/*
|--------------------------------------------------------------------------
| Comprobar modo mantenimiento
|--------------------------------------------------------------------------
*/

if (
    file_exists(
        $maintenance =
            $basePath . '/storage/framework/maintenance.php'
    )
) {
    require $maintenance;
}

/*
|--------------------------------------------------------------------------
| Registrar Composer
|--------------------------------------------------------------------------
*/

require $basePath . '/vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Iniciar Laravel
|--------------------------------------------------------------------------
*/

/** @var Application $app */
$app = require_once $basePath . '/bootstrap/app.php';

$app->handleRequest(
    Request::capture()
);