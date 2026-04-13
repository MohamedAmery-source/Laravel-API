<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Middleware\HandleCors;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;

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

            return $jsonError('فشل التحقق من صحة البيانات المرسلة.', 422, $e->errors());
        });

        $exceptions->render(function (AuthenticationException $e, Request $request) use ($jsonError, $isApi) {
            if (!$isApi($request)) {
                return null;
            }

            return $jsonError('غير مصرح. يرجى تسجيل الدخول أولاً.', 401);
        });

        $exceptions->render(function (AuthorizationException $e, Request $request) use ($jsonError, $isApi) {
            if (!$isApi($request)) {
                return null;
            }

            return $jsonError('غير مصرح لك بتنفيذ هذا الإجراء.', 403);
        });

        $exceptions->render(function (ModelNotFoundException $e, Request $request) use ($jsonError, $isApi) {
            if (!$isApi($request)) {
                return null;
            }

            return $jsonError('العنصر المطلوب غير موجود.', 404);
        });

        $exceptions->render(function (NotFoundHttpException $e, Request $request) use ($jsonError, $isApi) {
            if (!$isApi($request)) {
                return null;
            }

            return $jsonError('المسار المطلوب غير موجود.', 404);
        });

        $exceptions->render(function (MethodNotAllowedHttpException $e, Request $request) use ($jsonError, $isApi) {
            if (!$isApi($request)) {
                return null;
            }

            return $jsonError('طريقة الطلب غير مدعومة لهذا المسار.', 405, [
                'allowed_methods' => $e->getHeaders()['Allow'] ?? null,
            ]);
        });

        $exceptions->render(function (UnsupportedMediaTypeHttpException $e, Request $request) use ($jsonError, $isApi) {
            if (!$isApi($request)) {
                return null;
            }

            return $jsonError('نوع المحتوى غير مدعوم. يرجى التحقق من Content-Type.', 415);
        });

        $exceptions->render(function (PostTooLargeException $e, Request $request) use ($jsonError, $isApi) {
            if (!$isApi($request)) {
                return null;
            }

            return $jsonError('حجم الملف أو البيانات المرسلة أكبر من الحد المسموح.', 413);
        });

        $exceptions->render(function (TooManyRequestsHttpException $e, Request $request) use ($jsonError, $isApi) {
            if (!$isApi($request)) {
                return null;
            }

            return $jsonError('عدد الطلبات كبير جداً. يرجى المحاولة بعد قليل.', 429);
        });

        $exceptions->render(function (ServiceUnavailableHttpException $e, Request $request) use ($jsonError, $isApi) {
            if (!$isApi($request)) {
                return null;
            }

            return $jsonError('الخدمة غير متاحة حالياً. يرجى المحاولة لاحقاً.', 503);
        });

        $exceptions->render(function (QueryException $e, Request $request) use ($jsonError, $isApi) {
            if (!$isApi($request)) {
                return null;
            }

            return $jsonError('تعذر تنفيذ العملية على قاعدة البيانات. يرجى مراجعة البيانات والمحاولة مرة أخرى.', 422);
        });

        $exceptions->render(function (HttpExceptionInterface $e, Request $request) use ($jsonError, $isApi) {
            if (!$isApi($request)) {
                return null;
            }

            return $jsonError('تعذر إكمال الطلب بسبب خطأ في الاتصال بالخادم.', $e->getStatusCode());
        });

        $exceptions->render(function (Throwable $e, Request $request) use ($jsonError, $isApi) {
            if (!$isApi($request)) {
                return null;
            }

            return $jsonError('حدث خطأ غير متوقع في الخادم.', 500);
        });
    })->create();
