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
        // Pricing modes on the course: lifetime ("buy forever" using courses.price) and/or time-limited subscription
        Schema::table('courses', function (Blueprint $table) {
            $table->boolean('allow_lifetime')->default(true)->after('original_price');
            $table->boolean('allow_subscription')->default(false)->after('allow_lifetime');
        });

        // Subscription plans per course (e.g. 3, 6, 12 months)
        Schema::create('course_subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->unsignedSmallInteger('duration_months');
            $table->decimal('price', 10, 2);
            // Price an existing subscriber pays to add this period to their access (null = same as price)
            $table->decimal('renewal_price', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['course_id', 'duration_months']);
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->foreignId('subscription_plan_id')->nullable()->after('bundle_id')
                ->constrained('course_subscription_plans')->nullOnDelete();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('subscription_plan_id')->nullable()->after('bundle_id')
                ->constrained('course_subscription_plans')->nullOnDelete();
            $table->unsignedSmallInteger('duration_months')->nullable()->after('subscription_plan_id');
            $table->boolean('is_renewal')->default(false)->after('duration_months');
            // Set once the paid access (lifetime or extra months) has been applied to the enrollment
            $table->timestamp('access_applied_at')->nullable()->after('is_renewal');
        });

        Schema::table('course_enrollments', function (Blueprint $table) {
            $table->string('access_type', 20)->default('lifetime')->after('status');
            $table->foreignId('subscription_plan_id')->nullable()->after('access_type')
                ->constrained('course_subscription_plans')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_enrollments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('subscription_plan_id');
            $table->dropColumn('access_type');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('subscription_plan_id');
            $table->dropColumn(['duration_months', 'is_renewal', 'access_applied_at']);
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('subscription_plan_id');
        });

        Schema::dropIfExists('course_subscription_plans');

        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['allow_lifetime', 'allow_subscription']);
        });
    }
};
