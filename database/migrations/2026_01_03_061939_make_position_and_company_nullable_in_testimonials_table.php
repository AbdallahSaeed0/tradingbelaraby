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
        Schema::table('testimonials', function (Blueprint $table) {
            $table->string('position')->nullable()->change();
            $table->string('position_ar')->nullable()->change();
            $table->string('company')->nullable()->change();
            $table->string('company_ar')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('testimonials', function (Blueprint $table) {
            $table->string('position')->nullable(false)->change();
            $table->string('position_ar')->nullable()->change();
            $table->string('company')->nullable(false)->change();
            $table->string('company_ar')->nullable()->change();
        });
    }
};
