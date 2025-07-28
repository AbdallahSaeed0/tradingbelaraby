<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Helpers\TranslationHelper;

class TranslationServiceProvider extends ServiceProvider
{
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
        // The custom_trans function is now defined in app/helpers.php
        // This service provider is kept for future translation-related functionality

        // Add a custom Blade directive for translations
        Blade::directive('trans', function ($expression) {
            return "<?php echo App\Helpers\TranslationHelper::translate($expression); ?>";
        });

        // Add a custom Blade directive for admin translations
        Blade::directive('adminTrans', function ($expression) {
            return "<?php echo App\Helpers\TranslationHelper::translate($expression, 'admin'); ?>";
        });

        // Add a custom Blade directive for front translations
        Blade::directive('frontTrans', function ($expression) {
            return "<?php echo App\Helpers\TranslationHelper::translate($expression, 'front'); ?>";
        });
    }
}
