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
        Schema::table('seo_analyses', function (Blueprint $table) {
            $table->integer('accessibility_score')->nullable()->after('pagespeed_scores');
            $table->string('accessibility_title')->nullable()->after('accessibility_score');
            $table->text('accessibility_description')->nullable()->after('accessibility_title');
            $table->text('accessibility_manual')->nullable()->after('accessibility_description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seo_analyses', function (Blueprint $table) {
            //
        });
    }
};
