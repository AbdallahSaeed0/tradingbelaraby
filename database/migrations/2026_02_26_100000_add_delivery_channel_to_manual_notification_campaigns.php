<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('manual_notification_campaigns', function (Blueprint $table) {
            $table->string('delivery_channel', 30)->default('notification')->after('priority');
        });
    }

    public function down(): void
    {
        Schema::table('manual_notification_campaigns', function (Blueprint $table) {
            $table->dropColumn('delivery_channel');
        });
    }
};
