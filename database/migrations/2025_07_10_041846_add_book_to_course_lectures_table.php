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
        Schema::table('course_lectures', function (Blueprint $table) {
            $table->string('book')->nullable()->after('document_file'); // PDF book file for this lecture
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_lectures', function (Blueprint $table) {
            $table->dropColumn('book');
        });
    }
};
