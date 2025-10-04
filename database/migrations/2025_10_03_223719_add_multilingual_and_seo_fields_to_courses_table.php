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
            // Add multilingual fields for course content
            $table->string('name_ar')->nullable()->after('name');
            $table->text('description_ar')->nullable()->after('description');
            $table->json('what_to_learn_ar')->nullable()->after('what_to_learn');
            $table->json('faq_course_ar')->nullable()->after('faq_course');
            $table->text('certificate_text_ar')->nullable()->after('certificate_text');

            // Add SEO fields
            $table->string('meta_title_ar')->nullable()->after('meta_title');
            $table->text('meta_description_ar')->nullable()->after('meta_description');
            $table->string('meta_keywords_ar')->nullable()->after('meta_keywords');
            $table->string('canonical_url')->nullable()->after('meta_keywords_ar');
            $table->string('og_title')->nullable()->after('canonical_url');
            $table->string('og_title_ar')->nullable()->after('og_title');
            $table->text('og_description')->nullable()->after('og_title_ar');
            $table->text('og_description_ar')->nullable()->after('og_description');
            $table->string('og_image')->nullable()->after('og_description_ar');
            $table->string('twitter_title')->nullable()->after('og_image');
            $table->string('twitter_title_ar')->nullable()->after('twitter_title');
            $table->text('twitter_description')->nullable()->after('twitter_title_ar');
            $table->text('twitter_description_ar')->nullable()->after('twitter_description');
            $table->string('twitter_image')->nullable()->after('twitter_description_ar');

            // Add structured data fields
            $table->json('structured_data')->nullable()->after('twitter_image');
            $table->json('structured_data_ar')->nullable()->after('structured_data');

            // Add language support
            $table->string('default_language')->default('en')->after('structured_data_ar');
            $table->json('supported_languages')->nullable()->after('default_language');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn([
                'name_ar',
                'description_ar',
                'what_to_learn_ar',
                'faq_course_ar',
                'certificate_text_ar',
                'meta_title_ar',
                'meta_description_ar',
                'meta_keywords_ar',
                'canonical_url',
                'og_title',
                'og_title_ar',
                'og_description',
                'og_description_ar',
                'og_image',
                'twitter_title',
                'twitter_title_ar',
                'twitter_description',
                'twitter_description_ar',
                'twitter_image',
                'structured_data',
                'structured_data_ar',
                'default_language',
                'supported_languages',
            ]);
        });
    }
};
