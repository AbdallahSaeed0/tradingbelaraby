<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_fcm_tokens', function (Blueprint $table) {
            $table->string('locale', 10)->nullable()->after('device_type');
        });
    }

    public function down(): void
    {
        Schema::table('user_fcm_tokens', function (Blueprint $table) {
            $table->dropColumn('locale');
        });
    }
};
