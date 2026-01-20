<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    //Realizado aplicação de ratelimiter geral na rota api
    ->withMiddleware(function (Middleware $middleware): void {
        // Keep the framework's default `api` group and configure it instead of overriding it.
        // This preserves Sanctum's stateful SPA support and prevents "Session store not set on request".
        $middleware->statefulApi();
        $middleware->throttleApi('api');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
