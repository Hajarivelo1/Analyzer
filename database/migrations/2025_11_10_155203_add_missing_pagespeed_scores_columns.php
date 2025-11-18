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
            // Ajouter les colonnes manquantes
            $table->json('pagespeed_desktop_scores')->nullable()->after('pagespeed_desktop_audits');
            $table->json('pagespeed_mobile_scores')->nullable()->after('pagespeed_mobile_audits');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seo_analyses', function (Blueprint $table) {
            $table->dropColumn(['pagespeed_desktop_scores', 'pagespeed_mobile_scores']);
        });
    }
};
