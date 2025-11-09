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
        Schema::create('seo_analyses', function (Blueprint $table) {
            $table->id();
            
            // Clé étrangère vers le projet
            $table->foreignId('project_id')
                  ->constrained()
                  ->onDelete('cascade');
            
            // Informations de la page analysée
            $table->string('page_url');
            $table->string('page_title')->nullable();
            
            // Résultats de l'analyse SEO
            $table->integer('score')->nullable(); // Score global sur 100
            $table->json('recommendations')->nullable(); // Recommandations en JSON
            $table->text('content_analysis')->nullable(); // Analyse du contenu
            
            // Métriques techniques
            $table->integer('word_count')->nullable();
            $table->decimal('keyword_density', 5, 2)->nullable();
            $table->integer('load_time')->nullable(); // en ms
            $table->boolean('mobile_friendly')->nullable();
            
            // Métadonnées
            $table->text('meta_description')->nullable();
            $table->string('h1_tags')->nullable();
            $table->json('images_data')->nullable(); // Infos sur les images
            
            $table->timestamps();
            
            // Index pour les performances
            $table->index('project_id');
            $table->index('created_at');
            $table->index('score');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seo_analyses');
    }
};
