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
            $table->longText('polyline')->nullable();
            $table->longText('polyline_encoded')->nullable();
            $table->string('distance')->nullable();
            $table->string('duration')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropColumn('polyline');
            $table->dropColumn('polyline_encoded');
            $table->dropColumn('distance');
            $table->dropColumn('duration');
        });
    }
};
