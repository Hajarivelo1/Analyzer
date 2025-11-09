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
            if (!Schema::hasColumn('seo_analyses', 'raw_html')) {
                $table->longText('raw_html')->nullable();
            }
    
            if (!Schema::hasColumn('seo_analyses', 'title')) {
                $table->string('title')->nullable();
            }
    
            if (!Schema::hasColumn('seo_analyses', 'headings')) {
                $table->json('headings')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seo_analyses', function (Blueprint $table) {
            $table->dropColumn(['raw_html', 'title', 'headings']);
        });
    }
};
