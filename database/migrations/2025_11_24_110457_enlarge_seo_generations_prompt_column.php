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
        Schema::table('seo_generations', function (Blueprint $table) {
            // Changer le type de TEXT à LONGTEXT pour supporter de longs prompts
            $table->longText('prompt')->change();
            $table->longText('original_prompt')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seo_generations', function (Blueprint $table) {
            $table->text('prompt')->change();
            $table->text('original_prompt')->nullable()->change();
        });
    }
};
