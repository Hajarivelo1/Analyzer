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
        Schema::table('seo_generations', function (Blueprint $table) {
            // Ajouter la colonne project_id après user_id
            $table->foreignId('project_id')
                  ->after('user_id')
                  ->nullable()
                  ->constrained('projects')
                  ->onDelete('cascade'); // Si projet supprimé, générations aussi

            // Index pour les performances
            $table->index(['user_id', 'project_id']);
            $table->index('project_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seo_generations', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropColumn('project_id');
        });
    }
};
