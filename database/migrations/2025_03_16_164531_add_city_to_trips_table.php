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
        Schema::table('trips', function (Blueprint $table) {
            $table->string('start_city')->nullable();
            $table->string('start_state')->nullable();
            $table->string('end_city')->nullable();
            $table->string('end_state')->nullable();
            

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropColumn('start_city');
            $table->dropColumn('start_state');
            $table->dropColumn('end_city');
            $table->dropColumn('end_state');
        });
    }
};
