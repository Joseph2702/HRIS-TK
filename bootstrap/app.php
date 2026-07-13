<?php

use App\Http\Middleware\CheckRole;
use App\Http\Middleware\JwtAuthenticate;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        apiPrefix: 'api',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'jwt.auth' => JwtAuthenticate::class,
            'role' => CheckRole::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {

        $exceptions->render(function (\App\Common\Exception\BusinessException $e, $request) {
            if ($request->is('api/*')) {
                return \App\Common\Response\ApiResponse::error($e->getMessage(), $e->getCode() ?: 400);
            }
        });

        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, $request) {
            if ($request->is('api/*')) {
                return \App\Common\Response\ApiResponse::validationError($e->errors(), 'Data yang dimasukkan tidak valid');
            }
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            if ($request->is('api/*')) {
                return \App\Common\Response\ApiResponse::notFound('Data tidak ditemukan');
            }
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException $e, $request) {
            if ($request->is('api/*')) {
                return \App\Common\Response\ApiResponse::error('Metode tidak diizinkan', 405);
            }
        });

        $exceptions->render(function (\Illuminate\Database\QueryException $e, $request) {
            if ($request->is('api/*')) {
                \Illuminate\Support\Facades\Log::error('DB Error: '.$e->getMessage());
                return \App\Common\Response\ApiResponse::error('Terjadi kesalahan pada database, silakan coba lagi', 500);
            }
        });

        $exceptions->render(function (\Throwable $e, $request) {
            if ($request->is('api/*')) {
                \Illuminate\Support\Facades\Log::error('Unhandled: '.$e->getMessage());
                return \App\Common\Response\ApiResponse::error('Terjadi kesalahan sistem, silakan coba lagi', 500);
            }
        });
    })->create();
