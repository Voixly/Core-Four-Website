<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$base = dirname(__DIR__);
foreach ([
    $base.'/storage/app/public',
    $base.'/storage/framework/cache/data',
    $base.'/storage/framework/sessions',
    $base.'/storage/framework/views',
    $base.'/storage/logs',
    $base.'/bootstrap/cache',
] as $dir) {
    if (! is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
}

if (! is_file($autoload = __DIR__.'/../vendor/autoload.php')) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=UTF-8');
    echo "Dependencies are not installed. SSH to the folder that contains artisan and run:\n";
    echo "composer install --no-dev --optimize-autoloader\n";
    echo "php artisan key:generate\n";
    echo "php artisan migrate --force --seed\n";
    exit;
}

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require $autoload;

if (! is_file($base.'/.env') && getenv('APP_KEY') === false && empty($_SERVER['APP_KEY'] ?? null)) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=UTF-8');
    echo "This site is PHP. The Node.js environment settings are not visible to it.\n";
    echo "Create platform/.env on the server with APP_KEY and the MySQL settings.\n";
    exit;
}

(require_once __DIR__.'/../bootstrap/app.php')
    ->handleRequest(Request::capture());
