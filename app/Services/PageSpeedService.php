<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class PageSpeedService
{
    // ⏱️ CONFIGURATION ULTRA-RAPIDE
    private const TIMEOUT = 120;
    private const RETRIES = 1;
    private const CACHE_DURATION = 1800; // 30 minutes

    /**
     * 🔥 AUDIT ULTRA-RAPIDE - Une seule requête optimisée
     */
    /**
 * 🔥 AUDIT ULTRA-RAPIDE - VERSION CORRIGÉE
 */
public function runAudit(string $url, string $strategy = 'desktop'): ?array
{
    $cacheKey = "pagespeed_{$strategy}_" . md5($url);
    
    if (Cache::has($cacheKey)) {
        Log::info('📦 PageSpeed depuis cache', ['strategy' => $strategy]);
        return Cache::get($cacheKey);
    }

    Log::info('⚡ PageSpeed audit RAPIDE', ['url' => $url, 'strategy' => $strategy]);

    try {
        $baseUrl = 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed';
        
        // ✅ CORRECTION : Utiliser la syntaxe correcte pour multiples catégories
        $queryParams = [
            'url' => $url,
            'strategy' => $strategy,
            'key' => config('services.pagespeed.key'),
            'locale' => 'fr',
        ];

        // ✅ CORRECTION : Ajouter les catégories comme paramètres séparés
        $categories = ['PERFORMANCE', 'ACCESSIBILITY', 'SEO', 'BEST_PRACTICES'];
        $queryString = http_build_query($queryParams);
        
        // ✅ CORRECTION : Ajouter chaque catégorie individuellement
        foreach ($categories as $category) {
            $queryString .= "&category=" . $category;
        }

        Log::info('🔗 URL PageSpeed appelée', [
            'url' => $baseUrl . '?' . $queryString
        ]);

        $response = Http::timeout(self::TIMEOUT)
            ->retry(self::RETRIES, 500)
            ->withOptions([
                'verify' => false,
                'connect_timeout' => 10,
            ])
            ->get($baseUrl . '?' . $queryString); // ✅ Utiliser la query string construite

        Log::info('📡 Réponse PageSpeed', [
            'status' => $response->status(),
            'strategy' => $strategy
        ]);

        if ($response->successful()) {
            $data = $response->json();
            
            // ✅ VALIDATION AVEC DEBUG
            if ($this->isValidResponse($data)) {
                Cache::put($cacheKey, $data, self::CACHE_DURATION);
                
                // ✅ SCORE ALTERNATIF - Chercher le score dans différents endroits
                $performanceScore = $this->extractPerformanceScore($data);
                
                Log::info('✅ PageSpeed réussi', [
                    'performance_score' => $performanceScore ?? 'N/A',
                    'all_categories' => array_keys($data['lighthouseResult']['categories'] ?? [])
                ]);
                
                return $data;
            } else {
                Log::warning('⚠️ Réponse PageSpeed invalide selon isValidResponse');
                // ✅ SAUVEGARDER QUAND MÊME POUR DEBUG
                Log::info('🔍 DEBUG Données reçues', [
                    'data_keys' => array_keys($data),
                    'lighthouse_keys' => array_keys($data['lighthouseResult'] ?? [])
                ]);
            }
        } else {
            $this->handleErrorResponse($response->status());
        }

    } catch (\Exception $e) {
        Log::error('💥 PageSpeed erreur', ['message' => $e->getMessage()]);
    }

    return null;
}



private function extractPerformanceScore(array $data): ?int
{
    $lighthouseResult = $data['lighthouseResult'];
    
    // 1. Depuis les catégories
    if (isset($lighthouseResult['categories']['performance']['score'])) {
        return round($lighthouseResult['categories']['performance']['score'] * 100);
    }
    
    // 2. Depuis les audits
    if (isset($lighthouseResult['audits']['performance-score']['score'])) {
        return round($lighthouseResult['audits']['performance-score']['score'] * 100);
    }
    
    // 3. Depuis le score global
    if (isset($lighthouseResult['categories']['performance'])) {
        $performance = $lighthouseResult['categories']['performance'];
        if (isset($performance['score'])) {
            return round($performance['score'] * 100);
        }
    }
    
    return null;
}

    /**
     * 🔥 VALIDATION RAPIDE
     */
    private function isValidResponse(?array $data): bool
{
    if (!is_array($data) || !isset($data['lighthouseResult'])) {
        Log::warning('❌ Réponse invalide - pas de lighthouseResult');
        return false;
    }

    $lighthouseResult = $data['lighthouseResult'];
    
    // ✅ DEBUG TEMPORAIRE - Afficher la structure complète
    Log::info('🔍 DEBUG Structure réponse', [
        'has_categories' => isset($lighthouseResult['categories']),
        'has_audits' => isset($lighthouseResult['audits']),
        'categories_keys' => isset($lighthouseResult['categories']) ? array_keys($lighthouseResult['categories']) : 'NONE',
        'audits_count' => isset($lighthouseResult['audits']) ? count($lighthouseResult['audits']) : 0,
        'first_5_audits' => isset($lighthouseResult['audits']) ? array_slice(array_keys($lighthouseResult['audits']), 0, 5) : []
    ]);

    // ✅ CORRECTION : Accepter même sans catégories, si on a des audits
    if (isset($lighthouseResult['audits']) && !empty($lighthouseResult['audits'])) {
        return true;
    }
    
    // ✅ Ou si on a des catégories
    if (isset($lighthouseResult['categories']) && !empty($lighthouseResult['categories'])) {
        return true;
    }
    
    Log::warning('❌ Réponse invalide - ni audits ni catégories');
    return false;
}

    /**
     * 🔥 GESTION D'ERREUR SIMPLIFIÉE
     */
    private function handleErrorResponse(int $status): void
    {
        $errorMap = [
            400 => 'Requête invalide',
            403 => 'Clé API invalide', 
            429 => 'Quota dépassé',
            500 => 'Erreur serveur Google',
        ];

        Log::warning('❌ Erreur API PageSpeed', [
            'status' => $status,
            'message' => $errorMap[$status] ?? 'Erreur inconnue'
        ]);
    }

    /**
     * 🔥 AUDIT MULTI-CATÉGORIES OPTIMISÉ
     */
    public function runMultiCategoryAudit(string $url, string $strategy = 'desktop'): ?array
{
    Log::info('🎯 Audit multi-catégories RAPIDE', ['url' => $url]);
    
    try {
        $baseUrl = 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed';
        
        // ✅ MÊME CORRECTION ICI
        $queryParams = [
            'url' => $url,
            'strategy' => $strategy,
            'key' => config('services.pagespeed.key'),
            'locale' => 'fr',
        ];

        $categories = ['PERFORMANCE', 'ACCESSIBILITY', 'SEO', 'BEST_PRACTICES'];
        foreach ($categories as $category) {
            $queryParams["category"] = $category;
        }

        $response = Http::timeout(self::TIMEOUT)
            ->get($baseUrl, $queryParams); // ✅ Tableau directement

        if ($response->successful()) {
            $data = $response->json();
            
            if ($this->isValidResponse($data)) {
                Log::info('✅ Audit multi-catégories réussi', [
                    'categories' => array_keys($data['lighthouseResult']['categories'])
                ]);
                return $data;
            }
        }

    } catch (\Exception $e) {
        Log::error('💥 Audit multi-catégories échoué', ['message' => $e->getMessage()]);
    }

    return null;
}

    /**
     * 🔥 EXTRACTION ULTRA-RAPIDE des métriques
     */
    public function extractCoreMetrics(?array $data): array
    {
        if (!$this->isValidResponse($data)) {
            return [];
        }

        $audits = $data['lighthouseResult']['audits'];
        $metrics = [];
        
        // 🔥 Uniquement les Core Web Vitals essentiels
        $essentialMetrics = [
            'first-contentful-paint' => 'First Contentful Paint',
            'largest-contentful-paint' => 'Largest Contentful Paint',
            'cumulative-layout-shift' => 'Cumulative Layout Shift', 
            'total-blocking-time' => 'Total Blocking Time',
        ];
        
        foreach ($essentialMetrics as $metricKey => $metricName) {
            if (isset($audits[$metricKey])) {
                $audit = $audits[$metricKey];
                $metrics[$metricKey] = [
                    'title' => $metricName,
                    'score' => $audit['score'] ?? null,
                    'displayValue' => $audit['displayValue'] ?? null,
                    'numericValue' => $audit['numericValue'] ?? null,
                ];
            }
        }

        return $metrics;
    }

    /**
     * 🔥 EXTRACTION RAPIDE des scores
     */
    public function extractAllScores(array $auditResult): array
    {
        if (!isset($auditResult['lighthouseResult']['categories'])) {
            return [
                'performance' => 0,
                'accessibility' => 0,
                'seo' => 0,
                'best-practices' => 0
            ];
        }

        $categories = $auditResult['lighthouseResult']['categories'];
        $scores = [];

        foreach ($categories as $key => $category) {
            $scores[$key] = isset($category['score']) 
                ? round($category['score'] * 100) 
                : 0;
        }

        return [
            'performance' => $scores['performance'] ?? 0,
            'accessibility' => $scores['accessibility'] ?? 0,
            'seo' => $scores['seo'] ?? 0,
            'best-practices' => $scores['best-practices'] ?? 0
        ];
    }

    /**
     * 🔥 EXTRACTION RAPIDE des audits critiques seulement
     */
    /**
 * 🔥 EXTRACTION COMPLÈTE - PREND TOUT
 */
public function extractAuditFragments(array $audits): array
{
    $fragments = [
        'opportunities' => [],
        'diagnostics' => [],
        'passed' => []
    ];

    $processed = 0;
    $excluded = 0;

    foreach ($audits as $auditId => $auditData) {
        // 🔥 DEBUG CHAQUE AUDIT
        $hasScore = isset($auditData['score']);
        $hasTitle = isset($auditData['title']);
        $hasDescription = !empty($auditData['description'] ?? '');
        
        if (!$hasTitle || !$hasDescription) {
            $excluded++;
            continue;
        }

        $fragment = [
            'id' => $auditId,
            'title' => $auditData['title'],
            'description' => $auditData['description'] ?? '',
            'score' => $auditData['score'] ?? null,
            'displayValue' => $auditData['displayValue'] ?? null,
        ];

        if (isset($auditData['details']['overallSavingsMs'])) {
            $fragment['estimatedSavingsMs'] = $auditData['details']['overallSavingsMs'];
        }

        $score = $auditData['score'] ?? 1;
        
        if ($score < 0.9) {
            if (isset($auditData['details']['overallSavingsMs'])) {
                $fragments['opportunities'][] = $fragment;
            } else {
                $fragments['diagnostics'][] = $fragment;
            }
        } else {
            $fragments['passed'][] = $fragment;
        }
        
        $processed++;
    }

    // 🔥 LOG DÉTAILLÉ
    Log::info('🎯 EXTRACT AUDITS - DÉTAILS', [
        'total_input' => count($audits),
        'processed' => $processed,
        'excluded' => $excluded,
        'opportunities' => count($fragments['opportunities']),
        'diagnostics' => count($fragments['diagnostics']),
        'passed' => count($fragments['passed']),
        'TOTAL_OUTPUT' => count($fragments['opportunities']) + 
                         count($fragments['diagnostics']) + 
                         count($fragments['passed'])
    ]);

    return $fragments;
}



private function shouldIncludeAudit(string $auditId, array $auditData): bool
{
    // Exclure les audits techniques/informatifs
    $excludedAudits = [
        'metrics',
        'screenshot-thumbnails',
        'final-screenshot',
        'network-requests',
        'diagnostics',
        'performance-budget',
        'main-thread-tasks',
        'long-tasks',
        'user-timings',
        'trace-elements',
        'network-rtt',
        'network-server-latency'
    ];

    // Inclure seulement les audits avec score et description
    return !in_array($auditId, $excludedAudits) &&
           isset($auditData['title']) &&
           $auditData['title'] !== 'Metrics' &&
           !empty($auditData['description'] ?? '');
}
    /**
     * 🔥 SUPPRIMEZ ces méthodes inutiles pour gagner en performance :
     * - mergeCategoryData() (plus utilisé)
     * - getAuditValue()
     * - formatNumericValue() 
     * - extractScoresByCategory()
     * - extractCategoryDetails()
     */

    /**
     * 🔥 NETTOYAGE du cache
     */
    public function clearCache(string $url, ?string $strategy = null): void
    {
        if ($strategy) {
            $cacheKey = "pagespeed_{$strategy}_" . md5($url);
            Cache::forget($cacheKey);
        } else {
            foreach (['desktop', 'mobile'] as $s) {
                $cacheKey = "pagespeed_{$s}_" . md5($url);
                Cache::forget($cacheKey);
            }
        }
    }
}