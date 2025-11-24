<?php

// app/Models/SeoGeneration.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SeoGeneration extends Model
{
    protected $fillable = [
        'user_id', 
        'project_id', // ⬅️ NOUVEAU - Lien vers le projet
        'prompt', 
        'lang', 
        'title', 
        'meta'
    ];

    /**
     * Relation avec l'utilisateur propriétaire
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * ⬅️ NOUVELLE RELATION - Lien vers le projet associé
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Relation avec les variantes de contenu
     */
    public function variants(): HasMany
    {
        return $this->hasMany(SeoVariant::class, 'generation_id');
    }

    /**
     * ⬅️ NOUVELLE MÉTHODE - Scope pour les générations d'un projet
     */
    public function scopeForProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    /**
     * ⬅️ NOUVELLE MÉTHODE - Scope pour une langue spécifique
     */
    public function scopeForLanguage($query, $language)
    {
        return $query->where('lang', $language);
    }

    /**
     * ⬅️ NOUVELLE MÉTHODE - Obtenir les statistiques de base
     */
    public function getStatsAttribute()
    {
        return [
            'title_length' => strlen($this->title),
        'meta_length' => strlen($this->meta),
        'has_title' => !empty($this->title),
        'has_meta' => !empty($this->meta),
        'variants_count' => $this->variants->count(),
        'is_title_optimized' => $this->isTitleOptimized,
        'is_meta_optimized' => $this->isMetaOptimized,
        ];
    }

    /**
     * ⬅️ NOUVELLE MÉTHODE - Vérifier si le title est optimisé (50-60 caractères)
     */
    public function getIsTitleOptimizedAttribute()
    {
        $length = strlen($this->title);
        return $length >= 50 && $length <= 70;
    }

    /**
     * ⬅️ NOUVELLE MÉTHODE - Vérifier si la meta est optimisée (120-160 caractères)
     */
    public function getIsMetaOptimizedAttribute()
    {
        $length = strlen($this->meta);
        return $length >= 120 && $length <= 160;
    }

    /**
     * ⬅️ NOUVELLE MÉTHODE - Dupliquer la génération pour un autre projet
     */
    public function duplicateForProject($projectId)
    {
        $newGeneration = $this->replicate();
        $newGeneration->project_id = $projectId;
        $newGeneration->save();

        return $newGeneration;
    }
    
}