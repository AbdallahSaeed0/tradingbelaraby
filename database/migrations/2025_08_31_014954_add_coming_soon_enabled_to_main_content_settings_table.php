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
        Schema::table('main_content_settings', function (Blueprint $table) {
            $table->boolean('coming_soon_enabled')->default(false)->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('main_content_settings', function (Blueprint $table) {
            $table->dropColumn('coming_soon_enabled');
        });
    }
};
