<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('verification_settings', function (Blueprint $table) {
            $table->id();
            $table->string('method', 20)->default('whatsapp'); // 'whatsapp' | 'email' | 'both'
            $table->text('whatsapp_token')->nullable();
            $table->string('whatsapp_phone_number_id', 100)->nullable();
            $table->string('whatsapp_otp_template', 100)->default('otp_verification');
            $table->string('whatsapp_api_version', 20)->default('v21.0');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verification_settings');
    }
};
