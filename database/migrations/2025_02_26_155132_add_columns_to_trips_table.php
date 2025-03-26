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
            $table->decimal('updated_start_lat', 20, 8)->nullable();
            $table->decimal('updated_start_lng', 20, 8)->nullable();
            $table->decimal('updated_end_lat', 20, 8)->nullable();
            $table->decimal('updated_end_lng', 20, 8)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropColumn('updated_start_lat');
            $table->dropColumn('updated_start_lng');
            $table->dropColumn('updated_end_lat');
            $table->dropColumn('updated_end_lng');
        });
    }
};
