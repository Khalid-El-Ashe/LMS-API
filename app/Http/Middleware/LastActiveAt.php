<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use App\Models\Mentor;
use App\Models\Student;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;

class LastActiveAt
{

// هذا الملف هو جزء من مشروع Laravel ويحتوي على Middleware لتحديث وقت آخر نشاط للمستخدم
// Middleware يقوم بتحديث حقل 'last_active_at' في جدول المستخدمين عند كل طلب
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user(); // to get the authenticated user
        if ($user instanceof Student) { // check if the user is a student
            // $user->last_active_at = now(); // update the last activity time to current time
            // $user->save(); // save the changes to the database

            // Update the last active timestamp
            $user->forceFill(['last_active_at' => Carbon::now()])->save();
        } elseif ($user instanceof Mentor) { // check if the user is an admin
            // $user->last_active_at = now(); // update the last activity time to current time
            // $user->save(); // save the changes to the database

            // Update the last active timestamp
            $user->forceFill(['last_active_at' => Carbon::now()])->save();
        } elseif ($user instanceof Admin) {
            $user->forceFill(['last_active_at' => Carbon::now()])->save();
        }
        return $next($request);
    }
}
