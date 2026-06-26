<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use App\Models\Mentor;
use App\Models\Student;
use App\Traits\ApiResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CheckTokenRedis
{
    use ApiResponseTrait;

    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user('admin') ?? $request->user('student') ?? $request->user('mentor');
        if (!$user) {
            return $this->error('Unauthenticated', 401);
        }

        $type = match (true) {
            $user instanceof Admin => 'admin',
            $user instanceof Student => 'student',
            $user instanceof Mentor => 'mentor',
            default => null,
        };

        $checkToken = Cache::get("{$type}_token_{$user->id}");
        if (!$checkToken) {
            return $this->error('Token expired', 401);
        }

        return $next($request);
    }
}
