<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Ensure instructor admin type has all necessary permissions for courses, quizzes, homework, live classes, etc.
     */
    public function up(): void
    {
        $instructorPermissions = [
            'manage_own_courses',
            'manage_own_quizzes',
            'manage_own_homework',
            'manage_own_live_classes',
            'view_own_analytics',
            'manage_own_questions_answers',
        ];

        $instructorType = DB::table('admin_types')->where('name', 'instructor')->first();
        if ($instructorType) {
            $currentPermissions = json_decode($instructorType->permissions ?? '[]', true) ?: [];
            $merged = array_unique(array_merge($currentPermissions, $instructorPermissions));
            DB::table('admin_types')
                ->where('name', 'instructor')
                ->update(['permissions' => json_encode(array_values($merged))]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse - cannot reliably remove permissions that may have been added
    }
};
