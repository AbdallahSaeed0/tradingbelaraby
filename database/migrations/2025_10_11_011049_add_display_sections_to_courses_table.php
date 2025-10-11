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
        Schema::table('courses', function (Blueprint $table) {
            $table->boolean('show_in_top_discounted')->default(false)->after('is_featured');
            $table->boolean('show_in_subscription_bundles')->default(false)->after('show_in_top_discounted');
            $table->boolean('show_in_live_meeting')->default(false)->after('show_in_subscription_bundles');
            $table->boolean('show_in_recent_courses')->default(false)->after('show_in_live_meeting');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn([
                'show_in_top_discounted',
                'show_in_subscription_bundles',
                'show_in_live_meeting',
                'show_in_recent_courses'
            ]);
        });
    }
};
