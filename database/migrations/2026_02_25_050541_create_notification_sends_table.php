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
        Schema::create('notification_sends', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('notification_key', 100);
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->timestamp('scheduled_for');
            $table->timestamps();
            $table->unique(['user_id', 'notification_key', 'entity_id', 'scheduled_for'], 'notification_sends_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_sends');
    }
};
