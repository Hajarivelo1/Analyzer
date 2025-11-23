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
        Schema::create('seo_variants', function (Blueprint $table) {
            $table->id();
        $table->foreignId('generation_id')
              ->constrained('seo_generations')
              ->onDelete('cascade');
        $table->string('title');
        $table->text('meta');
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seo_variants');
    }
};
