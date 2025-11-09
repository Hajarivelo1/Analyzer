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
            $table->unsignedBigInteger('page_rank_global')->nullable()->after('page_rank');
        });
    }
    
    public function down()
    {
        Schema::table('seo_analyses', function (Blueprint $table) {
            $table->dropColumn('page_rank_global');
        });
    }
    
};
