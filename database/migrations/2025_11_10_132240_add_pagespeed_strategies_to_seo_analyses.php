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
            $table->float('pagespeed_mobile_score')->nullable();
        $table->json('pagespeed_mobile_metrics')->nullable();
        $table->json('pagespeed_mobile_audits')->nullable();
        $table->string('pagespeed_mobile_formFactor')->nullable();

        $table->float('pagespeed_desktop_score')->nullable();
        $table->json('pagespeed_desktop_metrics')->nullable();
        $table->json('pagespeed_desktop_audits')->nullable();
        $table->string('pagespeed_desktop_formFactor')->nullable();
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
