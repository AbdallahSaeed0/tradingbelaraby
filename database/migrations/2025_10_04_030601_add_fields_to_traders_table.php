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
        Schema::table('traders', function (Blueprint $table) {
            $table->string('name')->after('id');
            $table->enum('sex', ['male', 'female', 'other'])->after('name');
            $table->date('birthdate')->after('sex');
            $table->string('email')->after('birthdate');
            $table->string('phone_number')->nullable()->after('email');
            $table->string('whatsapp_number')->nullable()->after('phone_number');
            $table->string('linkedin')->nullable()->after('whatsapp_number');
            $table->string('website')->nullable()->after('linkedin');
            $table->string('trading_community')->nullable()->after('website');
            $table->text('certificates')->nullable()->after('trading_community');
            $table->text('trading_experience')->nullable()->after('certificates');
            $table->text('training_experience')->nullable()->after('trading_experience');
            $table->string('first_language')->after('training_experience');
            $table->string('second_language')->nullable()->after('first_language');
            $table->text('available_appointments')->nullable()->after('second_language');
            $table->text('comments')->nullable()->after('available_appointments');
            $table->boolean('is_active')->default(true)->after('comments');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('traders', function (Blueprint $table) {
            $table->dropColumn([
                'name', 'sex', 'birthdate', 'email', 'phone_number',
                'whatsapp_number', 'linkedin', 'website', 'trading_community',
                'certificates', 'trading_experience', 'training_experience',
                'first_language', 'second_language', 'available_appointments',
                'comments', 'is_active'
            ]);
        });
    }
};
