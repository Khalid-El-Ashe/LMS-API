<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LocalizationResponse
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
//        $locale = $request->header('Accept-Language', config('app.locale'));
        $locale = $request->header('Accept-Language', 'en'); // Default to English if not provided
        if (in_array($locale, ['en', 'ar'])) {
            app()->setLocale($locale);
        }
        else {
            app()->setLocale('en'); // Fallback to default locale
        }
        return $next($request);
    }
}
