<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('live_classes', function (Blueprint $table) {
            $table->string('name_ar')->nullable()->after('name');
            $table->text('description_ar')->nullable()->after('description');
            $table->text('instructions_ar')->nullable()->after('instructions');
        });
    }

    public function down(): void
    {
        Schema::table('live_classes', function (Blueprint $table) {
            $table->dropColumn(['name_ar', 'description_ar', 'instructions_ar']);
        });
    }
};
