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
        Schema::create('live_class_registrations', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->foreignId('live_class_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Registration details
            $table->timestamp('registered_at')->useCurrent();
            $table->boolean('attended')->default(false);
            $table->timestamp('joined_at')->nullable();
            $table->timestamp('left_at')->nullable();
            $table->integer('attendance_minutes')->default(0);

            // Notes
            $table->text('notes')->nullable();

            // Timestamps
            $table->timestamps();

            // Ensure one registration per user per live class
            $table->unique(['live_class_id', 'user_id']);

            // Indexes
            $table->index(['live_class_id', 'attended']);
            $table->index(['user_id', 'registered_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('live_class_registrations');
    }
};
