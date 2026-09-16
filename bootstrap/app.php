<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
        ]);

        // Railway (and most PaaS hosts) terminate HTTPS at their edge and
        // forward plain HTTP internally to the container, with the original
        // scheme passed via X-Forwarded-Proto. Without trusting that header,
        // Laravel thinks every request is insecure HTTP — form actions,
        // redirect() targets and route()/url() all get generated as
        // http://, and session cookies never get the Secure flag. Trusting
        // all proxies is safe here specifically because the container has
        // no public IP of its own; the platform's edge is the only thing
        // that can reach it.
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
