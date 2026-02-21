<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Validation\Rules\Password;
use App\Models\CourseCategory;

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
