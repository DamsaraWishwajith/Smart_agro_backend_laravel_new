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
            $table->integer('water_need_ml')->nullable()->after('plants_count');
            $table->integer('planted_days')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plants', function (Blueprint $table) {
            $table->dropColumn('water_need_ml');
            $table->integer('planted_days')->nullable(false)->change();
        });
    }
};
