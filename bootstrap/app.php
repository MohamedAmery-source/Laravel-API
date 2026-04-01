<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Middleware\HandleCors;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(prepend: [
            HandleCors::class,
        ]);
        
        $middleware->redirectGuestsTo(function (Request $request) {
            return $request->expectsJson() ? null : '/';
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $jsonError = function (string $message, int $status, mixed $data = null) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'data' => $data,
            ], $status, [], JSON_UNESCAPED_UNICODE);
        };

        $isApi = function (Request $request): bool {
            return $request->is('api/*') || $request->expectsJson();
        };

        $exceptions->render(function (ValidationException $e, Request $request) use ($jsonError, $isApi) {
            if (!$isApi($request)) {
                return null;
            }

            return $jsonError('Validation failed.', 422, $e->errors());
        });

        $exceptions->render(function (AuthenticationException $e, Request $request) use ($jsonError, $isApi) {
            if (!$isApi($request)) {
                return null;
            }

            return $jsonError('Unauthenticated.', 401);
        });

        $exceptions->render(function (AuthorizationException $e, Request $request) use ($jsonError, $isApi) {
            if (!$isApi($request)) {
                return null;
            }

            return $jsonError('Forbidden.', 403);
        });

        $exceptions->render(function (ModelNotFoundException $e, Request $request) use ($jsonError, $isApi) {
            if (!$isApi($request)) {
                return null;
            }

            return $jsonError('Record not found for the provided id.', 404);
        });

        $exceptions->render(function (NotFoundHttpException $e, Request $request) use ($jsonError, $isApi) {
            if (!$isApi($request)) {
                return null;
            }

            return $jsonError('Endpoint not found.', 404);
        });

        $exceptions->render(function (MethodNotAllowedHttpException $e, Request $request) use ($jsonError, $isApi) {
            if (!$isApi($request)) {
                return null;
            }

            return $jsonError('HTTP method not allowed for this endpoint.', 405);
        });

        $exceptions->render(function (QueryException $e, Request $request) use ($jsonError, $isApi) {
            if (!$isApi($request)) {
                return null;
            }

            return $jsonError('Database operation failed. Please check your input and try again.', 422);
        });

        $exceptions->render(function (Throwable $e, Request $request) use ($jsonError, $isApi) {
            if (!$isApi($request)) {
                return null;
            }

            return $jsonError('Unexpected server error.', 500);
        });
    })->create();
