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
        Schema::create('traders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('sex', ['male', 'female', 'other']);
            $table->date('birthdate');
            $table->string('email');
            $table->string('phone_number')->nullable();
            $table->string('whatsapp_number')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('website')->nullable();
            $table->string('trading_community')->nullable();
            $table->text('certificates')->nullable();
            $table->text('trading_experience')->nullable();
            $table->text('training_experience')->nullable();
            $table->string('first_language');
            $table->string('second_language')->nullable();
            $table->text('available_appointments')->nullable();
            $table->text('comments')->nullable(); // For any additional comments/questions
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('traders');
    }
};
