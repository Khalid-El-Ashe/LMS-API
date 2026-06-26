<?php

namespace App\Providers;

use App\Repositories\Admin\AdminModelRepository;
use App\Repositories\Admin\AdminRepository;
use App\Repositories\Course\CourseModelRepository;
use App\Repositories\Course\CourseRepository;
use App\Repositories\Course\Link\LinkModelRepository;
use App\Repositories\Course\Link\LinkRepository;
use App\Repositories\Course\Task\TaskModelRepository;
use App\Repositories\Course\Task\TaskRepository;
use App\Repositories\LandingPage\LandingPageModelRepository;
use App\Repositories\LandingPage\LandingPageRepository;
use App\Repositories\Mentor\MentorModelRepository;
use App\Repositories\Mentor\MentorRepository;
use App\Repositories\Student\StudentModelRepository;
use App\Repositories\Student\StudentRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // in here I need to inject the interfaces with the implementations
        $this->app->bind(StudentRepository::class, StudentModelRepository::class);
        $this->app->bind(MentorRepository::class, MentorModelRepository::class);
        $this->app->bind(CourseRepository::class, CourseModelRepository::class);
        $this->app->bind(LandingPageRepository::class, LandingPageModelRepository::class);
        $this->app->bind(AdminRepository::class, AdminModelRepository::class);
        $this->app->bind(LinkRepository::class, LinkModelRepository::class);
        $this->app->bind(TaskRepository::class, TaskModelRepository ::class);

        // in here need to load the folder migrations
        $this->loadMigrationsFrom(database_path('migrations/course'));
        $this->loadMigrationsFrom(database_path('migrations/landingPages'));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
