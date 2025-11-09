<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('seo_analyses', function (Blueprint $table) {
        $table->boolean('https_enabled')->default(false);
        $table->boolean('has_structured_data')->default(false);
        $table->boolean('noindex_detected')->default(false);
    });
}

public function down()
{
    Schema::table('seo_analyses', function (Blueprint $table) {
        $table->dropColumn([
            'https_enabled',
            'has_structured_data',
            'noindex_detected',
        ]);
    });
}

};
