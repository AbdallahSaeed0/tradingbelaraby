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
            $table->string('footer_copyright')->nullable()->after('site_author');
            $table->string('footer_copyright_ar')->nullable()->after('footer_copyright');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('main_content_settings', function (Blueprint $table) {
            $table->dropColumn(['footer_copyright', 'footer_copyright_ar']);
        });
    }
};
