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
        if (!Schema::hasColumn('modes', 'mist_auto_schedule')) {
            Schema::table('modes', function (Blueprint $table) {
                $table->boolean('mist_auto_schedule')->default(false)->after('mode');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('modes', 'mist_auto_schedule')) {
            Schema::table('modes', function (Blueprint $table) {
                $table->dropColumn('mist_auto_schedule');
            });
        }
    }
};
