<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'certificate_hours')) {
                $table->decimal('certificate_hours', 6, 1)->nullable()->after('enable_certificate');
            }
        });

        Schema::table('course_enrollments', function (Blueprint $table) {
            if (!Schema::hasColumn('course_enrollments', 'certificate_name_en')) {
                $table->string('certificate_name_en')->nullable()->after('certificate_name');
            }
            if (!Schema::hasColumn('course_enrollments', 'certificate_number')) {
                $table->string('certificate_number', 40)->nullable()->unique()->after('certificate_name_en');
            }
            if (!Schema::hasColumn('course_enrollments', 'certificate_status')) {
                // active | revoked
                $table->string('certificate_status', 20)->default('active')->after('certificate_number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('course_enrollments', function (Blueprint $table) {
            foreach (['certificate_status', 'certificate_number', 'certificate_name_en'] as $col) {
                if (Schema::hasColumn('course_enrollments', $col)) {
                    if ($col === 'certificate_number') {
                        $table->dropUnique(['certificate_number']);
                    }
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'certificate_hours')) {
                $table->dropColumn('certificate_hours');
            }
        });
    }
};
