<?php

namespace App\Providers;

use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
         RateLimiter::for('students/auth/register', function (Request $request) {
             // return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());

             return Limit::perDay(7)->by(
                 $request->input('email') . '|' . $request->ip()
             )->response(function () {
                 return response()->json([
                     'message' => 'Too many register attempts. Try again tomorrow.'
                 ], 429);
             });
         });

        // RateLimiter::for('students/auth/login', function (Request $request) {
        //     // return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());

        //     return Limit::perDay(1)->by(
        //         $request->input('username') . '|' . $request->ip()
        //     )->response(function () {
        //         return response()->json([
        //             'message' => 'Too many login attempts. Try again tomorrow.'
        //         ], 429);
        //     });
        // });
    }
}
