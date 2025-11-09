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
            $table->integer('html_size')->nullable();
        $table->integer('total_links')->nullable();
        $table->boolean('has_og_tags')->default(false);
        $table->string('html_lang', 10)->nullable();
        $table->boolean('has_favicon')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seo_analyses', function (Blueprint $table) {
            $table->dropColumn([
                'html_size',
                'total_links',
                'has_og_tags',
                'html_lang',
                'has_favicon',
                
            ]);
        });
    }
};
