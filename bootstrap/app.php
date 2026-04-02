<?php

use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use App\Jobs\DispatchCursorAgentRepairJob;
use App\Support\CursorAgentServerErrorClassifier;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

        $middleware->web(append: [
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->api(prepend: [
            ThrottleRequests::class.':api',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->stopIgnoring(HttpException::class);

        $exceptions->dontReportWhen(function (Throwable $e): bool {
            if ($e instanceof HttpException) {
                return $e->getStatusCode() < 500;
            }

            return false;
        });

        $exceptions->reportable(function (Throwable $e): void {
            if (! config('cursor_agent.enabled')) {
                return;
            }

            if (! CursorAgentServerErrorClassifier::shouldTrigger($e)) {
                return;
            }

            $fingerprint = CursorAgentServerErrorClassifier::fingerprint($e);

            RateLimiter::attempt(
                'cursor-agent:'.$fingerprint,
                1,
                function () use ($e, $fingerprint): void {
                    DispatchCursorAgentRepairJob::dispatch($fingerprint, [
                        'exception_class' => $e::class,
                        'message' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'trace' => $e->getTraceAsString(),
                        'occurred_at' => now()->toIso8601String(),
                        'request_url' => request()?->fullUrl(),
                    ]);
                },
                (int) config('cursor_agent.rate_limit_seconds'),
            );
        });
    })->create();
