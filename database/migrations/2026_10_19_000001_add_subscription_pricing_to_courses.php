<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Every step is guarded so the migration can be re-run after a partial failure
     * (MySQL DDL is not transactional), and new columns are appended rather than
     * positioned after columns that may not exist on every environment.
     */
    public function up(): void
    {
        // Pricing modes on the course: lifetime ("buy forever" using courses.price) and/or time-limited subscription
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'allow_lifetime')) {
                $table->boolean('allow_lifetime')->default(true);
            }
            if (!Schema::hasColumn('courses', 'allow_subscription')) {
                $table->boolean('allow_subscription')->default(false);
            }
        });

        // Subscription plans per course (e.g. 3, 6, 12 months)
        if (!Schema::hasTable('course_subscription_plans')) {
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
        }

        if (!Schema::hasColumn('cart_items', 'subscription_plan_id')) {
            Schema::table('cart_items', function (Blueprint $table) {
                $table->foreignId('subscription_plan_id')->nullable()
                    ->constrained('course_subscription_plans')->nullOnDelete();
            });
        }

        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'subscription_plan_id')) {
                $table->foreignId('subscription_plan_id')->nullable()
                    ->constrained('course_subscription_plans')->nullOnDelete();
            }
            if (!Schema::hasColumn('order_items', 'duration_months')) {
                $table->unsignedSmallInteger('duration_months')->nullable();
            }
            if (!Schema::hasColumn('order_items', 'is_renewal')) {
                $table->boolean('is_renewal')->default(false);
            }
            if (!Schema::hasColumn('order_items', 'access_applied_at')) {
                // Set once the paid access (lifetime or extra months) has been applied to the enrollment
                $table->timestamp('access_applied_at')->nullable();
            }
        });

        Schema::table('course_enrollments', function (Blueprint $table) {
            if (!Schema::hasColumn('course_enrollments', 'access_type')) {
                $table->string('access_type', 20)->default('lifetime');
            }
            if (!Schema::hasColumn('course_enrollments', 'subscription_plan_id')) {
                $table->foreignId('subscription_plan_id')->nullable()
                    ->constrained('course_subscription_plans')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('course_enrollments', 'subscription_plan_id')) {
            Schema::table('course_enrollments', function (Blueprint $table) {
                $table->dropConstrainedForeignId('subscription_plan_id');
            });
        }
        if (Schema::hasColumn('course_enrollments', 'access_type')) {
            Schema::table('course_enrollments', function (Blueprint $table) {
                $table->dropColumn('access_type');
            });
        }

        if (Schema::hasColumn('order_items', 'subscription_plan_id')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->dropConstrainedForeignId('subscription_plan_id');
            });
        }
        foreach (['duration_months', 'is_renewal', 'access_applied_at'] as $column) {
            if (Schema::hasColumn('order_items', $column)) {
                Schema::table('order_items', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }

        if (Schema::hasColumn('cart_items', 'subscription_plan_id')) {
            Schema::table('cart_items', function (Blueprint $table) {
                $table->dropConstrainedForeignId('subscription_plan_id');
            });
        }

        Schema::dropIfExists('course_subscription_plans');

        foreach (['allow_lifetime', 'allow_subscription'] as $column) {
            if (Schema::hasColumn('courses', $column)) {
                Schema::table('courses', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }
};
