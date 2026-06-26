<?php

namespace App\Providers;

use App\Models\Student;
use Illuminate\Auth\Access\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
//        Student::class => StudentPolicy::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
//        Gate::before(function ($user, $ability) {
//             if ($user->id == 1) {
//                 return true;
//             }
//
//             if ($user->type == 'super-adminstrator') {
//                 return true;
//             }
//        });
//        # need make a Gate for Roles
//
//        foreach (config('abilities') as $ability => $lable) {
//
//            Gate::define($ability, function ($user) use ($ability) {
//                return $user->hasAbility($ability);
//            });
//        }
    }
}
