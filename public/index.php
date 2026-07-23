<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Localizar la aplicación Laravel
|--------------------------------------------------------------------------
|
| En local, Laravel está un nivel arriba de public.
| En cPanel, la carpeta pública está separada y la aplicación está
| ubicada en /home/miorpaco/miorpa-repo.
|
*/

$basePath = dirname(__DIR__);

if (! is_file($basePath.'/vendor/autoload.php')) {
    $basePath = dirname(__DIR__).'/miorpa-repo';
}

/*
|--------------------------------------------------------------------------
| Comprobar modo mantenimiento
|--------------------------------------------------------------------------
*/

if (file_exists($maintenance = $basePath.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

/*
|--------------------------------------------------------------------------
| Registrar Composer
|--------------------------------------------------------------------------
*/

require $basePath.'/vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Iniciar Laravel
|--------------------------------------------------------------------------
*/

/** @var Application $app */
$app = require_once $basePath.'/bootstrap/app.php';

$app->handleRequest(Request::capture());