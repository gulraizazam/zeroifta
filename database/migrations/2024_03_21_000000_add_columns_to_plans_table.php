<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->text('features')->nullable();
            $table->string('badge_text')->nullable();
            $table->string('badge_color')->nullable();
        });
    }

    public function down()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn([
                'is_active',
                'sort_order', 
                'features',
                'badge_text',
                'badge_color'
            ]);
        });
    }
}; 