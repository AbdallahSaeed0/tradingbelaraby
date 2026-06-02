<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('bank_transfer_enabled')->default(false);
            $table->string('bank_transfer_bank_name')->nullable();
            $table->string('bank_transfer_account_name')->nullable();
            $table->string('bank_transfer_account_number')->nullable();
            $table->string('bank_transfer_iban')->nullable();
            $table->text('bank_transfer_instructions')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_settings');
    }
};
