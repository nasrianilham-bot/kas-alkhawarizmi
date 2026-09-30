<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Matikan pengecekan maintenance mode berbasis file di serverless Vercel
        $middleware->preventRequestsDuringMaintenance(except: ['*']);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })
    ->booting(function (Application $app): void {
        // Kunci driver maintenance mode agar tidak bernilai null
        $app['config']->set('app.maintenance.driver', 'cache');
        $app['config']->set('app.maintenance.store', 'array');
    })
    ->create();
