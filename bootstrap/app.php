<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // TLS is terminated by the web server in front of PHP, so without
        // this every request looks like plain HTTP from in here. The canonical
        // tag, og:url, the hreflang alternates and the sitemap are all built
        // from the request, and would each advertise an http:// address for a
        // site served over https://. Trusting any proxy is right for a single
        // host where nothing but the local web server can reach PHP.
        $middleware->trustProxies(at: '*');

        // Applies to the admin panel as well as the public site, though the
        // policy itself differs between them.
        $middleware->web(append: [\App\Http\Middleware\SecurityHeaders::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
