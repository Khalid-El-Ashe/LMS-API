<?php

use App\Http\Middleware\CheckTokenRedis;
use App\Http\Middleware\ExpiredToken;
use App\Http\Middleware\LastActiveAt;
use App\Http\Middleware\LocalizationResponse;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\SessionProtection;
use App\Jobs\FetchPlaylistVideosJob;
use App\Models\Course;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Session\Middleware\StartSession;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
//        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        apiPrefix: 'v1',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api([
            EnsureFrontendRequestsAreStateful::class,
            StartSession::class,
            SubstituteBindings::class,
        ]);
        $middleware->alias([
            'last.active' => LastActiveAt::class, // to update the last activity time with every request for student and mentor and admin
            'abilities' => CheckAbilities::class,
            'ability' => CheckForAnyAbility::class,
//            'admin' => EnsureAdmin::class,
            'expire_token' => ExpiredToken::class,
            'check.token.redis' => CheckTokenRedis::class,
            'check.token.auth' => RedirectIfAuthenticated::class,
            'session.protect' => SessionProtection::class,
        ]);
        $middleware->redirectGuestsTo(function () {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        });
        $middleware->append(LocalizationResponse::class);
    })
    ->withSchedule(function (Schedule $schedule): void {
        // $schedule->command('inspire')->hourly();
        $schedule->call(function () {
            // get all courses that have YouTube playlist url and dispatch the SyncCourseVideosJob for each course
            $courses = Course::query()->whereNotNull('youtube_playlist_url')->get();
            foreach ($courses as $course) {
                FetchPlaylistVideosJob::dispatch($course->id, $course->youtube_playlist_url);
            }
        })->daily(); // to sync the course videos with YouTube playlist daily, in case there are any changes in the playlist

        // now need to make schedule the command DeleteExpiredLinks
        $schedule->command('links:cleanup')->everyMinute();

//        $schedule->call(function () {
        // كم طالب أكمل فيديوهات اليوم
//        })->daily();

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
