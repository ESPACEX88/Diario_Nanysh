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
    ->withMiddleware(function (Middleware $middleware): void {
        // TrustProxies ANTES de SiteClosed: sin esto, $request->ip() es la del proxy
        // (igual para todos) y el rate-limit de visitas bloquea notificaciones reales.
        $middleware->web(prepend: [
            \App\Http\Middleware\TrustProxies::class,
            \App\Http\Middleware\SiteClosed::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
            \App\Http\Middleware\ForceHttps::class,
        ]);
        
        // Rate limiting para rutas sensibles
        $middleware->alias([
            'throttle.sensitive' => \App\Http\Middleware\ThrottleSensitiveRoutes::class,
            'validate.upload' => \App\Http\Middleware\ValidateFileUpload::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
