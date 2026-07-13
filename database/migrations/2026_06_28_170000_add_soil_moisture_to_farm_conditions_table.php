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
        Schema::table('farm_conditions', function (Blueprint $table) {
            $table->decimal('soil_moisture', 5, 2)->nullable()->after('humidity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('farm_conditions', function (Blueprint $table) {
            $table->dropColumn('soil_moisture');
        });
    }
};
