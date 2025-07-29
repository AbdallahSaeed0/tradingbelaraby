<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
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
        // Share categories with all views
        View::composer('layouts.app', function ($view) {
            $categories = CourseCategory::withCount('courses')->take(4)->get();
            $view->with('navigationCategories', $categories);
        });
    }
}
