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
        Schema::create('course_sections', function (Blueprint $table) {
            $table->id();

            // Course relationship
            $table->foreignId('course_id')->constrained()->onDelete('cascade');

            // Section details
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('order')->default(0); // For ordering sections

            // Section status and visibility
            $table->boolean('is_published')->default(true);
            $table->boolean('is_free')->default(false); // Free preview section

            // Section duration and content info
            $table->integer('total_lectures')->default(0);
            $table->integer('total_duration_minutes')->default(0);

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->index(['course_id', 'order']);
            $table->index(['course_id', 'is_published']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_sections');
    }
};
