<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureRole::class,
        ]);
        $middleware->trustProxies(at: '*');
        $middleware->web(append: [
            \App\Http\Middleware\EnsureTrailingSlash::class,
        ]);
    })
    ->withSchedule(function (\Illuminate\Console\Scheduling\Schedule $schedule) {
        $schedule->command('email:send-due')->everyFiveMinutes();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Throwable $e, $request) {
            if (config('app.debug')) {
                return null;
            }

            $message = $e->getMessage();
            if ($e instanceof \Illuminate\Encryption\MissingAppKeyException || str_contains($message, 'encryption key')) {
                $text = "PHP is running, but APP_KEY is missing. Node.js environment settings are not passed to PHP. Add them in platform/.env.";
            } elseif ($e instanceof \Illuminate\Database\QueryException || $e instanceof \PDOException) {
                preg_match('/SQLSTATE\[[^\]]+\](?:\s*\[[^\]]+\])?/', $message, $state);
                preg_match('/using password: (YES|NO)/i', $message, $password);
                $mysql = config('database.connections.mysql');
                $detail = trim(implode(' ', array_filter([
                    $state[0] ?? null,
                    isset($password[1]) ? 'Password '.$password[1].'.' : null,
                    'Host '.($mysql['host'] ?? '').', database '.($mysql['database'] ?? '').'.',
                ])));
                $text = str_contains($message, '42S02') || str_contains($message, 'Base table')
                    ? 'MySQL connected, but the tables are not there yet. '.$detail
                    : 'MySQL refused the login. '.$detail;
            } else {
                $text = "PHP is running, but the page failed: ".$e::class;
                if (! preg_match('/password|SQLSTATE|\/Users\/|\/home\//i', $message)) {
                    $text .= "\n".$message;
                }
            }

            return response($text, 500, ['Content-Type' => 'text/plain; charset=UTF-8']);
        });
    })->create();
