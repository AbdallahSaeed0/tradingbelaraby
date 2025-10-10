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
        Schema::table('homework', function (Blueprint $table) {
            // Fix the due_date column to be nullable to avoid SQL strict mode errors
            $table->timestamp('due_date')->nullable()->change();

            // Add Arabic fields
            $table->string('name_ar')->nullable()->after('name');
            $table->text('description_ar')->nullable()->after('description');
            $table->text('instructions_ar')->nullable()->after('instructions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('homework', function (Blueprint $table) {
            // Revert the due_date column to NOT NULL
            $table->timestamp('due_date')->nullable(false)->change();

            // Drop Arabic fields
            $table->dropColumn(['name_ar', 'description_ar', 'instructions_ar']);
        });
    }
};
