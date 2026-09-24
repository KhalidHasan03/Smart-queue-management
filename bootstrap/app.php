<?php

use App\Http\Middleware\EnsurePermission;
use App\Http\Middleware\EnsureRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => EnsureRole::class,
            'permission' => EnsurePermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (PostTooLargeException $e, Request $request) {
            $limit = ini_get('post_max_size') ?: 'unknown';

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => "The uploaded file is too large. The server accepts up to {$limit} per request.",
                ], 413);
            }

            return response()->view('errors.post-too-large', ['limit' => $limit], 413);
        });
    })->create();
