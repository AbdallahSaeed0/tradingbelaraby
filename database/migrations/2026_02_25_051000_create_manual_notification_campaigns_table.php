<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manual_notification_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->string('audience_type', 50); // single, segment, broadcast
            $table->json('audience_filter')->nullable();
            $table->string('title_ar');
            $table->string('title_en');
            $table->text('body_ar');
            $table->text('body_en');
            $table->json('action_json')->nullable();
            $table->json('entity_json')->nullable();
            $table->string('priority', 20)->default('normal');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->string('status', 30)->default('draft'); // draft, scheduled, sent, cancelled
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manual_notification_campaigns');
    }
};
