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
        // COLONNES PAGESPEED STRATÉGIE DESKTOP
        'pagespeed_desktop_score',
        'pagespeed_desktop_metrics',
        'pagespeed_desktop_audits',
        'pagespeed_desktop_scores',
        'pagespeed_desktop_formFactor',
        
        // COLONNES PAGESPEED STRATÉGIE MOBILE  
        'pagespeed_mobile_score',
        'pagespeed_mobile_metrics',
        'pagespeed_mobile_audits',
        'pagespeed_mobile_scores',
        'pagespeed_mobile_formFactor',
    ];

    /**
     * Casts pour les champs JSON - CORRIGÉS
     */
    protected $casts = [
        // 🔥 CORRECTION CRITIQUE : Ajout des casts manquants
        'headings' => 'array',
        'headings_structure' => 'array',
        'images_data' => 'array',
        'keywords' => 'array',
        'content_analysis' => 'array',
        'technical_audit' => 'array',
        'recommendations' => 'array',
        'whois_data' => 'array',
        'pagespeed_metrics' => 'array',
        'pagespeed_scores' => 'array',
        'pagespeed_audits' => 'array',
        
        // Casts pour les champs Pagespeed desktop
        'pagespeed_desktop_score' => 'integer',
        'pagespeed_desktop_metrics' => 'array',
        'pagespeed_desktop_audits' => 'array',
        'pagespeed_desktop_scores' => 'array',
        'pagespeed_desktop_formFactor' => 'string',
        
        // Casts pour les champs Pagespeed mobile
        'pagespeed_mobile_score' => 'integer',
        'pagespeed_mobile_metrics' => 'array',
        'pagespeed_mobile_audits' => 'array',
        'pagespeed_mobile_scores' => 'array',
        'pagespeed_mobile_formFactor' => 'string',
        
        // Casts pour les booléens
        'https_enabled' => 'boolean',
        'has_structured_data' => 'boolean',
        'noindex_detected' => 'boolean',
        'has_og_tags' => 'boolean',
        'has_favicon' => 'boolean',
        'cloudflare_blocked' => 'boolean',
        'mobile_friendly' => 'boolean',
        
        // Casts pour les nombres
        'load_time' => 'float',
        'html_size' => 'integer',
        'total_links' => 'integer',
        'word_count' => 'integer',
        'keyword_density' => 'float',
        'readability_score' => 'float',
        'pagespeed_score' => 'float',
        'page_rank' => 'float',
        'page_rank_global' => 'integer',
        'accessibility_score' => 'float',
        'score' => 'integer',
    ];

    /**
     * Relation avec le projet
     */
    public function project(): BelongsTo
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
     * Accesseur pour le contenu principal (sécurisé)
     */
    public function getMainContentAttribute($value): string
    {
        return $value ?? '';
    }

    /**
     * Accesseur pour les données WHOIS (sécurisé)
     */
    public function getWhoisDataAttribute($value): array
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }
        
        return is_array($value) ? $value : [];
    }

    /**
     * Méthode whois() améliorée
     */
    public function whois(): array
    {
        $data = $this->whois_data;

        if (is_array($data) && isset($data['whois']) && is_array($data['whois'])) {
            return $data['whois'];
        }

        // Fallback : essayer de parser les données brutes
        if (is_string($data)) {
            $parsed = $this->parseWhoisData($data);
            return $parsed['whois'] ?? [];
        }

        return [];
    }

    /**
     * Parse les données WHOIS brutes
     */
    private function parseWhoisData(string $whoisText): array
    {
        $lines = explode("\n", $whoisText);
        $whois = [];
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || str_starts_with($line, '%') || str_starts_with($line, '#')) {
                continue;
            }
            
            if (str_contains($line, ':')) {
                [$key, $value] = explode(':', $line, 2);
                $key = trim($key);
                $value = trim($value);
                
                if (!empty($key) && !empty($value)) {
                    $whois[$key] = $value;
                }
            }
        }
        
        return ['whois' => $whois];
    }

    /**
     * Calcul dynamique du SEO Score
     */
    public function getSeoScoreAttribute(): int
    {
        $score = 0;

        // Load Time (0-15 points)
        if ($this->load_time < 2) {
            $score += 15;
        } elseif ($this->load_time < 4) {
            $score += 10;
        } elseif ($this->load_time < 6) {
            $score += 5;
        }

        // HTML Size (0-10 points)
        if ($this->html_size < 100000) {
            $score += 10;
        } elseif ($this->html_size < 200000) {
            $score += 7;
        } elseif ($this->html_size < 500000) {
            $score += 4;
        }

        // Total Links (0-10 points)
        if ($this->total_links >= 10 && $this->total_links <= 100) {
            $score += 10;
        } elseif ($this->total_links > 100) {
            $score += 7;
        } elseif ($this->total_links >= 5) {
            $score += 5;
        }

        // Booléens et flags
        $score += $this->has_og_tags ? 10 : 0;
        $score += $this->html_lang ? 5 : 0;
        $score += $this->has_favicon ? 5 : 0;
        $score += $this->https_enabled ? 10 : 0;
        $score += $this->has_structured_data ? 15 : 0;
        $score += $this->noindex_detected ? 0 : 10;
        $score += $this->mobile_friendly ? 10 : 0;

        // Limiter à 100 points max
        return min($score, 100);
    }

    /**
     * Getter pour le statut de score
     */
    public function getScoreStatusAttribute(): string
    {
        $score = $this->seo_score;
        
        if ($score >= 80) return 'excellent';
        if ($score >= 60) return 'good';
        if ($score >= 40) return 'average';
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

    /**
     * Vérifie si l'analyse est complète
     */
    public function getIsCompleteAttribute(): bool
    {
        return !empty($this->page_title) && 
               !empty($this->meta_description) && 
               $this->word_count > 0;
    }

    /**
     * Récupère les mots-clés principaux (top 5)
     */
    public function getTopKeywordsAttribute(): array
    {
        $keywords = $this->keywords;
        
        if (empty($keywords) || !is_array($keywords)) {
            return [];
        }
        
        // Trier par fréquence décroissante et prendre les 5 premiers
        arsort($keywords);
        return array_slice($keywords, 0, 5, true);
    }

    /**
     * Récupère les images sans alt
     */
    public function getImagesWithoutAltAttribute(): array
    {
        $images = $this->images_data;
        
        if (empty($images) || !is_array($images)) {
            return [];
        }
        
        return array_filter($images, function($image) {
            return empty($image['alt'] ?? '');
        });
    }
}