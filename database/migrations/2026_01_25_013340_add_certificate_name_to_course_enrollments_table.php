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
        Schema::table('course_enrollments', function (Blueprint $table) {
            if (!Schema::hasColumn('course_enrollments', 'certificate_name')) {
                $table->string('certificate_name')->nullable()->after('certificate_issued_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_enrollments', function (Blueprint $table) {
            if (Schema::hasColumn('course_enrollments', 'certificate_name')) {
                $table->dropColumn('certificate_name');
            }
        });
    }
};
