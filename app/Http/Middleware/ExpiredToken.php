<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ExpiredToken
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth('sanctum')->user();

        $token = $user?->currentAccessToken();

        if ($token && $token->expires_at && now()->isAfter($token->expires_at)) {
            return response()->json([
                'message' => 'Token expired'
            ], 401);
        }

        return $next($request);
    }
}
