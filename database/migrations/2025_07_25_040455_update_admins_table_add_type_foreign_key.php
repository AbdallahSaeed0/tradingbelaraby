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
        Schema::table('admins', function (Blueprint $table) {
            // Drop the enum type column
            $table->dropColumn('type');
        });

        Schema::table('admins', function (Blueprint $table) {
            // Add the foreign key column
            $table->foreignId('admin_type_id')->nullable()->constrained('admin_types')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropForeign(['admin_type_id']);
            $table->dropColumn('admin_type_id');
        });

        Schema::table('admins', function (Blueprint $table) {
            $table->enum('type', ['admin', 'instructor', 'employee'])->default('admin');
        });
    }
};
