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
    Schema::table('projects', function (Blueprint $table) {
        $table->boolean('enable_monitoring')->default(false);
        $table->string('analysis_frequency')->nullable();
        $table->string('competitor_analysis')->nullable();

    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            //
        });
    }
};
