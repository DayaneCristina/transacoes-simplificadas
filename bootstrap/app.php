<?php

use App\Exceptions\Business\BusinessException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (ValidationException $exception) {
            return response()
                ->json([
                    'error' => $exception->getMessage()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
        });

        $exceptions->render(function (BusinessException $exception) {
            return response()->json([
                'error' => $exception->getMessage()
            ], $exception->getCode());
        });
    })->create();
