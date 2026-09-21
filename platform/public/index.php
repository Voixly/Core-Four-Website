<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

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

(require_once __DIR__.'/../bootstrap/app.php')
    ->handleRequest(Request::capture());
