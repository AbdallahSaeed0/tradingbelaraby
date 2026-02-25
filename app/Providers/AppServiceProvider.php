<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Validation\Rules\Password;
use App\Models\CourseCategory;
use App\Models\CourseSection;
use App\Models\CourseLecture;
use App\Models\Homework;
use App\Models\Quiz;
use App\Models\LiveClass;
use App\Observers\CourseSectionObserver;
use App\Observers\CourseLectureObserver;
use App\Observers\HomeworkObserver;
use App\Observers\QuizObserver;
use App\Observers\LiveClassObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        CourseSection::observe(CourseSectionObserver::class);
        CourseLecture::observe(CourseLectureObserver::class);
        Homework::observe(HomeworkObserver::class);
        Quiz::observe(QuizObserver::class);
        LiveClass::observe(LiveClassObserver::class);

        // Set default password validation rules (min 6 characters)
        Password::defaults(function () {
            return Password::min(6);
        });

        // Share categories with all views
        View::composer('layouts.app', function ($view) {
            $categories = CourseCategory::withCount('courses')->take(4)->get();
            $view->with('navigationCategories', $categories);
        });
    }
}
