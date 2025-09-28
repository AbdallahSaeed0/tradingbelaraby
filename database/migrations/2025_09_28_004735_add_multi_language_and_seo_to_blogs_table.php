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
            // Multi-language support
            $table->string('title_ar')->nullable()->after('title');
            $table->text('description_ar')->nullable()->after('description');
            $table->text('excerpt_ar')->nullable()->after('excerpt');
            $table->string('image_ar')->nullable()->after('image');
            $table->string('slug_ar')->nullable()->after('slug');

            // Author relationship (change from string to foreign key)
            $table->dropColumn('author'); // Remove old string author field
            $table->foreignId('author_id')->nullable()->constrained('admins')->onDelete('set null')->after('category_id');

            // SEO fields
            $table->string('meta_title')->nullable()->after('is_featured');
            $table->string('meta_title_ar')->nullable()->after('meta_title');
            $table->text('meta_description')->nullable()->after('meta_title_ar');
            $table->text('meta_description_ar')->nullable()->after('meta_description');
            $table->json('meta_keywords')->nullable()->after('meta_description_ar');
            $table->json('meta_keywords_ar')->nullable()->after('meta_keywords');

            // URL slug customization
            $table->string('custom_slug')->nullable()->after('slug_ar');

            // Additional fields
            $table->text('tags')->nullable()->after('meta_keywords_ar'); // Comma-separated tags
            $table->integer('reading_time')->nullable()->after('tags'); // Estimated reading time in minutes

            // Indexes
            $table->index(['author_id', 'status']);
            $table->index(['custom_slug']);
            $table->index(['meta_title']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            // Drop multi-language columns
            $table->dropColumn([
                'title_ar',
                'description_ar',
                'excerpt_ar',
                'image_ar',
                'slug_ar'
            ]);

            // Drop author relationship and restore string field
            $table->dropForeign(['author_id']);
            $table->dropColumn('author_id');
            $table->string('author')->nullable();

            // Drop SEO fields
            $table->dropColumn([
                'meta_title',
                'meta_title_ar',
                'meta_description',
                'meta_description_ar',
                'meta_keywords',
                'meta_keywords_ar',
                'custom_slug',
                'tags',
                'reading_time'
            ]);
        });
    }
};
