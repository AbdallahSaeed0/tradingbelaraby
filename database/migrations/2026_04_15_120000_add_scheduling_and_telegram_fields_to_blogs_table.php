<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->timestamp('publish_at')->nullable()->after('status');
            $table->timestamp('published_at')->nullable()->after('publish_at');
            $table->boolean('post_to_telegram')->default(false)->after('published_at');
            $table->timestamp('telegram_posted_at')->nullable()->after('post_to_telegram');
            $table->string('telegram_message_id')->nullable()->after('telegram_posted_at');
            $table->string('telegram_post_status')->nullable()->after('telegram_message_id');
            $table->text('telegram_error')->nullable()->after('telegram_post_status');

            $table->index(['status', 'publish_at']);
            $table->index(['post_to_telegram', 'telegram_post_status']);
        });

        // Extend enum status for scheduled posts.
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE blogs MODIFY status ENUM('draft','scheduled','published','archived') NOT NULL DEFAULT 'draft'");
        }
    }

    public function down(): void
    {
        // Revert enum status list.
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE blogs MODIFY status ENUM('draft','published','archived') NOT NULL DEFAULT 'draft'");
        }

        Schema::table('blogs', function (Blueprint $table) {
            $table->dropIndex(['status', 'publish_at']);
            $table->dropIndex(['post_to_telegram', 'telegram_post_status']);
            $table->dropColumn([
                'publish_at',
                'published_at',
                'post_to_telegram',
                'telegram_posted_at',
                'telegram_message_id',
                'telegram_post_status',
                'telegram_error',
            ]);
        });
    }
};
