<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

/*
|--------------------------------------------------------------------------
| Vercel Compatibility Logic
|--------------------------------------------------------------------------
*/
if (isset($_SERVER['VERCEL_URL'])) {
    // Vercel is read-only. Redirect logs and cache to /tmp
    $tmpDir = '/tmp/storage/framework';
    $dirs = ['/tmp/storage/logs', $tmpDir . '/views', $tmpDir . '/cache', $tmpDir . '/sessions'];
    foreach ($dirs as $dir) {
        if (!is_dir($dir)) mkdir($dir, 0777, true);
    }

    config([
        'logging.channels.single.path' => '/tmp/storage/logs/laravel.log',
        'view.compiled' => $tmpDir . '/views',
        'cache.stores.file.path' => $tmpDir . '/cache',
        'session.driver' => 'cookie', // Avoid local file sessions on Vercel
    ]);
}

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            'payment/pushback',
        ]);
        
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
