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
        // Add bundle_id to cart_items
        Schema::table('cart_items', function (Blueprint $table) {
            $table->foreignId('bundle_id')->nullable()->after('course_id')->constrained()->onDelete('cascade');
            $table->index('bundle_id');
            
            // Drop the old unique constraint
            $table->dropUnique(['user_id', 'course_id']);
        });

        // Add bundle_id to order_items table
        if (!Schema::hasTable('order_items')) {
            Schema::create('order_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained()->onDelete('cascade');
                $table->foreignId('course_id')->nullable()->constrained()->onDelete('cascade');
                $table->foreignId('bundle_id')->nullable()->constrained()->onDelete('cascade');
                $table->decimal('price', 10, 2);
                $table->timestamps();

                // Indexes
                $table->index('order_id');
                $table->index('course_id');
                $table->index('bundle_id');
            });
        } else {
            Schema::table('order_items', function (Blueprint $table) {
                if (!Schema::hasColumn('order_items', 'bundle_id')) {
                    $table->foreignId('bundle_id')->nullable()->after('course_id')->constrained()->onDelete('cascade');
                    $table->index('bundle_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropForeign(['bundle_id']);
            $table->dropIndex(['bundle_id']);
            $table->dropColumn('bundle_id');
            
            // Re-add the unique constraint
            $table->unique(['user_id', 'course_id']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['bundle_id']);
            $table->dropIndex(['bundle_id']);
            $table->dropColumn('bundle_id');
        });
    }
};

