<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Zoom passcode for joining a live class inside the website (Zoom Meeting SDK).
 * The SDK needs the plain passcode; the pwd=... part of a Zoom link is an encoded value.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('live_classes', 'meeting_passcode')) {
            Schema::table('live_classes', function (Blueprint $table) {
                $table->string('meeting_passcode', 64)->nullable()->after('link');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('live_classes', 'meeting_passcode')) {
            Schema::table('live_classes', function (Blueprint $table) {
                $table->dropColumn('meeting_passcode');
            });
        }
    }
};
