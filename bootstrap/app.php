<?php

use App\Http\Middleware\EnsureModuleEnabled;
use App\Http\Middleware\EnsureTenantContext;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\SetPublicLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function (): void {
            require __DIR__.'/../routes/public.php';
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'tenant.context' => EnsureTenantContext::class,
            'locale' => SetLocale::class,
            'public.locale' => SetPublicLocale::class,
            'module' => EnsureModuleEnabled::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $isPublicSurface = function (Request $request): bool {
            return ! str_starts_with($request->path(), 'admin')
                && ! str_starts_with($request->path(), 'app')
                && ! str_starts_with($request->path(), 'supplier')
                && ! str_starts_with($request->path(), 'ops-panel');
        };

        $exceptions->render(function (NotFoundHttpException $e, Request $request) use ($isPublicSurface) {
            if ($request->expectsJson()) {
                return null;
            }

            if ($isPublicSurface($request)) {
                return response()->view('public.errors.404', [
                    'seo' => [
                        'title' => '404 — '.config('public.brand.name'),
                        'description' => __('public.errors.not_found'),
                        'image' => null,
                        'jsonLd' => [
                            '@context' => 'https://schema.org',
                            '@type' => 'WebPage',
                            'name' => 'Not found',
                        ],
                    ],
                ], 404);
            }

            return null;
        });

        $exceptions->render(function (Throwable $e, Request $request) use ($isPublicSurface) {
            if ($request->expectsJson() || config('app.debug')) {
                return null;
            }

            $status = $e instanceof HttpException ? $e->getStatusCode() : 500;
            if ($status !== 500 || ! $isPublicSurface($request)) {
                return null;
            }

            return response()->view('public.errors.500', [
                'seo' => [
                    'title' => '500 — '.config('public.brand.name'),
                    'description' => __('public.errors.server_error'),
                    'image' => null,
                    'jsonLd' => [
                        '@context' => 'https://schema.org',
                        '@type' => 'WebPage',
                        'name' => 'Server error',
                    ],
                ],
            ], 500);
        });
    })->create();
