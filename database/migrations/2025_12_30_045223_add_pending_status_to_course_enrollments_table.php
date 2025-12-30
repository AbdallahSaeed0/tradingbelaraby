<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the enum to add 'pending' status
        DB::statement("ALTER TABLE `course_enrollments` MODIFY COLUMN `status` ENUM('active', 'completed', 'cancelled', 'expired', 'pending') NOT NULL DEFAULT 'active'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'pending' from the enum (revert to original)
        DB::statement("ALTER TABLE `course_enrollments` MODIFY COLUMN `status` ENUM('active', 'completed', 'cancelled', 'expired') NOT NULL DEFAULT 'active'");
    }
};
