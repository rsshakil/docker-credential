<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * 多重リクエスト防止
 */
class PreventingDuplicateRequestMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // GETは対象外
        if ($request->method() === 'GET') {
            return $next($request);
        }

        // route名とセッションIDを指定することでリクエストごとでユニークになるように設定
        $key = 'route_lock_' . $request->route()->getName() . '_' . $request->session()->getId();

        // 多重送信されると、TOO MANY REQUESTを返却
        $lock = Cache::lock($key, 10);
        if ($lock->get()) {
            try {
                return $next($request);
            } finally {
                $lock->release();
            }
        } else {
            throw new HttpException(Response::HTTP_TOO_MANY_REQUESTS, 'Too Many Requests');
        }
    }
}
