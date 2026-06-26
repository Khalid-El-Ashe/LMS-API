<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SessionProtection
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('last_activity')) {
            session(['last_activity' => now()]);
        }

        // idle timeout (30 min)
        if (now()->diffInMinutes(session('last_activity')) > 30) {
            auth()->logout();
            session()->invalidate();
            session()->regenerateToken();
            abort(401, 'Session expired');
        }

        session(['last_activity' => now()]);

        return $next($request);
    }
}
