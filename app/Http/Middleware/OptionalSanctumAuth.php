<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class OptionalSanctumAuth
{
    /**
     * Resolve an authenticated user via Sanctum when a valid session or bearer token exists; otherwise continue as guest.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $request->setUserResolver(static function (?string $guard = null) {
            return Auth::guard($guard ?? 'sanctum')->user();
        });

        return $next($request);
    }
}
