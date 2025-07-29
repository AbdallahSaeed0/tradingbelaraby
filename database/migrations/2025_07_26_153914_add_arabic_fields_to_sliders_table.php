<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sliders', function (Blueprint $table) {
            $table->string('title_ar')->nullable()->after('title');
            $table->text('welcome_text_ar')->nullable()->after('welcome_text');
            $table->string('subtitle_ar')->nullable()->after('subtitle');
            $table->string('button_text_ar')->nullable()->after('button_text');
            $table->string('search_placeholder_ar')->nullable()->after('search_placeholder');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sliders', function (Blueprint $table) {
            $table->dropColumn([
                'title_ar',
                'welcome_text_ar',
                'subtitle_ar',
                'button_text_ar',
                'search_placeholder_ar'
            ]);
        });
    }
};
