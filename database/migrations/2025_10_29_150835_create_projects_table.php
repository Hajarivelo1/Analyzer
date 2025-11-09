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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            
            // Clé étrangère vers l'utilisateur
            $table->foreignId('user_id')
                  ->constrained()  // Crée la relation avec la table users
                  ->onDelete('cascade'); // Si user supprimé, projets aussi
            
            // Informations du projet
            $table->string('name'); // Nom du projet (ex: "Mon Site E-commerce")
            $table->string('base_url'); // URL de base (ex: "https://monsite.com")
            
            // Métadonnées SEO
            $table->text('description')->nullable(); // Description du projet
            $table->string('target_keywords')->nullable(); // Mots-clés cibles
            
            // Statistiques
            $table->integer('total_analyses')->default(0); // Nombre total d'analyses
            $table->decimal('average_score', 5, 2)->nullable(); // Score SEO moyen
            
            // Timestamps automatiques
            $table->timestamps();
            
            // Index pour les performances
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
