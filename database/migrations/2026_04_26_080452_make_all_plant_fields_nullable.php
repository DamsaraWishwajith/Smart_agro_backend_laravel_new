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
        Schema::table('plants', function (Blueprint $table) {
            $table->string('crop_name')->nullable()->change();
            $table->decimal('temperature', 5, 2)->nullable()->change();
            $table->integer('plants_count')->nullable()->change();
            $table->integer('water_time_seconds')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plants', function (Blueprint $table) {
            $table->string('crop_name')->nullable(false)->change();
            $table->decimal('temperature', 5, 2)->nullable(false)->change();
            $table->integer('plants_count')->nullable(false)->change();
            $table->integer('water_time_seconds')->nullable(false)->change();
        });
    }
};
