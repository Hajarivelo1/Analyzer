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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            
            // Identifiant unique du plan (pour Stripe)
            $table->string('stripe_price_id')->nullable()->unique();
            $table->string('stripe_product_id')->nullable();
            
            // Informations du plan
            $table->string('name'); // "Free", "Starter", "Professional", "Agency"
            $table->string('slug')->unique(); // "free", "starter", "pro", "agency"
            $table->text('description')->nullable();
            
            // Prix et facturation
            $table->decimal('price', 8, 2)->default(0); // Prix mensuel (0 pour gratuit)
            $table->string('currency')->default('eur');
            $table->string('billing_period')->default('monthly'); // monthly, yearly
            
            // Limitations du plan
            $table->integer('analyses_per_month')->default(0); // 0 = illimité
            $table->integer('projects_limit')->default(1);
            $table->integer('team_members_limit')->default(1);
            $table->integer('api_calls_per_month')->default(0);
            
            // Fonctionnalités (booléens)
            $table->boolean('has_competitor_analysis')->default(false);
            $table->boolean('has_pdf_export')->default(false);
            $table->boolean('has_csv_export')->default(false);
            $table->boolean('has_white_label')->default(false);
            $table->boolean('has_api_access')->default(false);
            $table->boolean('has_priority_support')->default(false);
            $table->boolean('is_active')->default(true);
            
            // Ordre d'affichage
            $table->integer('sort_order')->default(0);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
