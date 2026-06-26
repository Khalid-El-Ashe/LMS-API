<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next, string $guard = 'admin'): Response
    {
        $user = $request->user($guard);

        if ($user) {
            # check the Redis
            $cachedData = Cache::get("{$guard}_token_{$user->id}");

            if ($cachedData) {
                return response()->json([
                    'success' => true,
                    'message' => 'Already logged in',
                    'token' => $cachedData['token'],
                    'permissions' => $cachedData['permissions'],
                    'auto_login' => true,
                ], 200);
            }
        }

        return $next($request);
    }
}
