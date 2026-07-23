<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('screenshots', function (Blueprint $table) {
            $table->string('viewport')->default('desktop');
            $table->string('format')->default('png');
            $table->string('quality')->nullable();
            $table->boolean('is_full_page')->default(false);
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('screenshots', function (Blueprint $table) {
            $table->dropColumn(['viewport', 'format', 'quality', 'is_full_page', 'width', 'height']);
        });
    }
};
