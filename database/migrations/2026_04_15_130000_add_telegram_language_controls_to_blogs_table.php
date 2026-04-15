<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->boolean('telegram_send_ar')->default(false)->after('post_to_telegram');
            $table->boolean('telegram_send_en')->default(false)->after('telegram_send_ar');

            $table->timestamp('telegram_posted_at_ar')->nullable()->after('telegram_send_en');
            $table->string('telegram_message_id_ar')->nullable()->after('telegram_posted_at_ar');
            $table->string('telegram_post_status_ar')->nullable()->after('telegram_message_id_ar');
            $table->text('telegram_error_ar')->nullable()->after('telegram_post_status_ar');

            $table->timestamp('telegram_posted_at_en')->nullable()->after('telegram_error_ar');
            $table->string('telegram_message_id_en')->nullable()->after('telegram_posted_at_en');
            $table->string('telegram_post_status_en')->nullable()->after('telegram_message_id_en');
            $table->text('telegram_error_en')->nullable()->after('telegram_post_status_en');

            $table->index(['post_to_telegram', 'telegram_send_ar', 'telegram_send_en'], 'blogs_tg_send_flags_idx');
            $table->index(['telegram_post_status_ar', 'telegram_post_status_en'], 'blogs_tg_status_lang_idx');
        });
    }

    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->dropIndex('blogs_tg_send_flags_idx');
            $table->dropIndex('blogs_tg_status_lang_idx');

            $table->dropColumn([
                'telegram_send_ar',
                'telegram_send_en',
                'telegram_posted_at_ar',
                'telegram_message_id_ar',
                'telegram_post_status_ar',
                'telegram_error_ar',
                'telegram_posted_at_en',
                'telegram_message_id_en',
                'telegram_post_status_en',
                'telegram_error_en',
            ]);
        });
    }
};
