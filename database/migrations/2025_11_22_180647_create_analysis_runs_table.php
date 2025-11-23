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
        Schema::create('analysis_runs', function (Blueprint $table) {
            $table->id();
    $table->foreignId('project_id')->constrained()->onDelete('cascade');
    $table->json('seo_metrics')->nullable();
    $table->json('pagespeed_opportunities')->nullable();
    $table->json('ai_summary')->nullable();
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analysis_runs');
    }
};
