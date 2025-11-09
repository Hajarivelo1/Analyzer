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
        $table->json('headings_structure')->nullable()->after('headings');
        $table->json('duplicate_blocks')->nullable()->after('content_analysis');
        $table->decimal('readability_score', 5, 2)->nullable()->after('duplicate_blocks');
        // Optionnel si tu veux aller plus loin :
        // $table->decimal('accessibility_score', 5, 2)->nullable()->after('readability_score');
    });
}

public function down()
{
    Schema::table('seo_analyses', function (Blueprint $table) {
        $table->dropColumn([
            'headings_structure',
            'duplicate_blocks',
            'readability_score',
            // 'accessibility_score',
        ]);
    });
}

};
