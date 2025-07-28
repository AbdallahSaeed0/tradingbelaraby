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
        Schema::table('blogs', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->constrained('blog_categories')->onDelete('set null');
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->text('excerpt')->nullable();
            $table->json('meta_tags')->nullable();
            $table->string('author')->nullable();
            $table->integer('views_count')->default(0);
            $table->boolean('is_featured')->default(false);

            // Indexes
            $table->index(['category_id', 'status']);
            $table->index(['status', 'is_featured']);
            $table->index('views_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn(['category_id', 'status', 'excerpt', 'meta_tags', 'author', 'views_count', 'is_featured']);
        });
    }
};
