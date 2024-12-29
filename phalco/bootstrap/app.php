<?php

use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\PreventingDuplicateRequestMiddleware;
use App\Models\ErrorLog;
use App\Notifications\ErrorLogNotify;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;
use Symfony\Component\ErrorHandler\Error\FatalError;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        channels: __DIR__ . '/../routes/channels.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->prefix('admin')
                ->as('admin.')
                ->group(base_path('routes/admin.php'));
            Route::middleware('api')
                ->prefix('api')
                ->as('api.')
                ->group(base_path('routes/api.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->statefulApi();

        $middleware->web(append: [
            HandleInertiaRequests::class,
            PreventingDuplicateRequestMiddleware::class
        ]);

        $middleware->api(append: [
            PreventingDuplicateRequestMiddleware::class
        ]);

        // 未認証ユーザーのリダイレクト設定
        $middleware->redirectGuestsTo(function (Request $request) {
            if (request()->routeIs('admin.*')) {
                return request()->expectsJson() ? null : route('admin.login');
            }
        });

        // 認証済みユーザーのリダイレクト設定
        $middleware->redirectUsersTo(function () {
            if (auth('admin')->check() && request()->routeIs('admin.*')) {
                return route('admin.dashboard');
            }
            return route('dashboard');
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->dontReportDuplicates();
        $exceptions->report(function (Throwable $e) {
            try {
                try {
                    $error = ErrorLog::firstOrNew(
                        [
                            'file' => $e->getFile(),
                            'line_no' => $e->getLine(),
                            'error_code' => $e->getCode(),
                            'message' => strval($e->getMessage()),
                            'date' => Carbon::today()->toDateString(),
                        ],
                        [
                            'count' => 0,
                        ]
                    );
                    $error->count++;
                    $error->save();
                } finally {
                    $isFatal = is_a($e, FatalError::class);
                    $to = config('error.adminMail');
                    if (isset($to) && ($isFatal || $error->count == config('error.threshold'))) {
                        Notification::route('mail', $to)
                            ->notify(new ErrorLogNotify($e));
                    }
                }
            } catch (Throwable $nested) {
                // エラーハンドリング中のネストしたエラーは無視
            }
        });
    })->create();
