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
            $table->json('pagespeed_metrics')->nullable()->after('pagespeed_score');
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
