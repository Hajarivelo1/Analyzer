<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'base_url',
        'description',
        'target_keywords',
        'is_active',
        'enable_monitoring',
        'analysis_frequency',
        'competitor_analysis',
    ];

    /**
     * Relation avec l'utilisateur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec les analyses SEO
     */
    public function seoAnalyses(): HasMany
    {
        return $this->hasMany(SeoAnalysis::class);
    }

    /**
     * Dernière analyse SEO
     */
    public function latestAnalysis(): HasOne
    {
        return $this->hasOne(SeoAnalysis::class)->latestOfMany();
    }

    /**
     * Scope pour les projets actifs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Nombre total d'analyses
     */
    public function getTotalAnalysesAttribute(): int
    {
        return $this->seoAnalyses()->count();
    }

    /**
     * Score moyen basé sur seo_score dynamique
     */
    public function getAverageScoreAttribute(): float
{
    $analyses = $this->seoAnalyses;
    if ($analyses->isEmpty()) return 0;

    return $analyses->avg(fn($analysis) => $analysis->seo_score); // ⚡ Utiliser seo_score
}

    /**
     * Score total basé sur seo_score dynamique
     */
    public function getTotalScoreAttribute(): int
{
    return $this->seoAnalyses->sum(fn($analysis) => $analysis->seo_score); // ⚡ Utiliser seo_score
}

    public function analysisRuns()
{
    return $this->hasMany(SeoAnalysis::class);
}

}
