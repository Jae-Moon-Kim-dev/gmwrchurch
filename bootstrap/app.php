<?php

use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Routing\Middleware\SubstituteBindings;
use App\Http\Middleware\JwtMiddleware;
use Illuminate\Http\Middleware\HandleCors;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->group('web', [
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->group('api', [
            // EnsureFrontendRequestsAreStateful::class,
            HandleCors::class,
            'throttle:api',
            SubstituteBindings::class,
        ]);

        $middleware->append(JwtMiddleware::class);

        $middleware->encryptCookies(except: [
            'gmwr_token',
            'gmwr_refreshToken',
        ]);

        // $middleware->validateCsrfTokens(except: [
        //     'api/v1/login',
        //     'api/v1/register',
        // ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
