<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;



class SeoAnalysis extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'page_url',
        'page_title',
        'meta_description',
        'h1_tags',
        'headings',
        'images_data',
        'raw_html',
        'word_count',
        'keyword_density',
        'keywords', 
        'load_time',
        'html_size',
    'total_links',
    'has_og_tags',
    'html_lang',
    'has_favicon',
        'mobile_friendly',
        'score',
        'recommendations',
        'content_analysis',
        'technical_audit',
        'https_enabled',
        'has_structured_data',
        'noindex_detected',
        'headings_structure',
        'main_content',
        'readability_score',
        'cloudflare_blocked',
        'page_rank',
        'page_rank_global',
        'whois_data',
        'gtmetrix',
        'pagespeed_score',
        'pagespeed_metrics',
        'pagespeed_scores',
        'url',
        'accessibility_score',
        'accessibility_title',
        'accessibility_description',
        'accessibility_manual',
        'pagespeed_audits',


    ];
    

    /**
     * Casts pour les champs JSON
     */
    protected $casts = [
        'technical_audit' => 'array',
        'recommendations' => 'array',
        'images_data' => 'array',
        'keywords' => 'array',
        'https_enabled' => 'boolean',
        'has_structured_data' => 'boolean',
        'noindex_detected' => 'boolean',
        'load_time' => 'float',
    'html_size' => 'integer',
    'total_links' => 'integer',
    'has_og_tags' => 'boolean',
    'has_favicon' => 'boolean',
    'headings_structure' => 'array',
    'main_content' => 'string',
    'readability_score' => 'float',
    'cloudflare_blocked' => 'boolean',
    'page_rank' => 'float',
    'page_rank_global' => 'integer',
    'whois_data' => 'array',
    'pagespeed_score' => 'float',
    'pagespeed_metrics' => 'array',
    'pagespeed_scores' => 'array',
    'pagespeed_audits' => 'array', // ✅ indispensable

    ];

    /**
     * Relation avec le projet
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Scope pour les analyses avec bon score
     */
    public function scopeHighScore($query, $threshold = 80)
    {
        return $query->where('score', '>=', $threshold);
    }

    /**
     * Scope pour les analyses récentes
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Calcul dynamique du SEO Score
     */
    public function getSeoScoreAttribute(): int
    {
        $score = 0;

        // Load Time
        if ($this->load_time < 2) {
            $score += 15;
        } elseif ($this->load_time < 4) {
            $score += 10;
        } else {
            $score += 5;
        }

        // HTML Size
        if ($this->html_size < 100000) {
            $score += 10;
        } elseif ($this->html_size < 200000) {
            $score += 7;
        } else {
            $score += 4;
        }

        // Total Links
        if ($this->total_links >= 50) {
            $score += 10;
        } elseif ($this->total_links >= 10) {
            $score += 7;
        } else {
            $score += 4;
        }

        // Booléens
        $score += $this->has_og_tags ? 10 : 0;
        $score += $this->html_lang ? 5 : 0;
        $score += $this->has_favicon ? 5 : 0;
        $score += $this->https_enabled ? 10 : 0;
        $score += $this->has_structured_data ? 15 : 0;
        $score += $this->noindex_detected ? 0 : 10;

        return $score;
    }

    /**
     * Getter pour le statut de score
     */
    public function getScoreStatusAttribute(): string
    {
        if ($this->score >= 80) return 'excellent';
        if ($this->score >= 60) return 'good';
        if ($this->score >= 40) return 'average';
        return 'poor';
    }

    /**
     * Getter pour la couleur du score
     */
    public function getScoreColorAttribute(): string
    {
        return match($this->score_status) {
            'excellent' => 'success',
            'good' => 'primary',
            'average' => 'warning',
            'poor' => 'danger',
            default => 'secondary'
        };
    }

    public function whois(): array
{
    $data = $this->whois_data;

    if (is_array($data) && isset($data['whois']) && is_array($data['whois'])) {
        return $data['whois'];
    }

    return [];
}






}
