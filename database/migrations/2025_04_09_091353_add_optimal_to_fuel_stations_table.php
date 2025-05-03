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
        Schema::table('fuel_stations', function (Blueprint $table) {
            $table->boolean('firstOptimal')->default(false);
            $table->boolean('midOptimal')->default(false);
            $table->boolean('secondOptimal')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fuel_stations', function (Blueprint $table) {
            $table->dropColumn('firstOptimal');
            $table->dropColumn('midOptimal');
            $table->dropColumn('secondOptimal');
        });
    }
};
