<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PageSpeedService
{
    public function runAudit(string $url, string $strategy = 'desktop'): ?array
    {
        Log::info('🎯 PageSpeedService - Début de runAudit', [
            'url' => $url,
            'strategy' => $strategy
        ]);

        try {
            $strategies = [$strategy, $strategy === 'desktop' ? 'mobile' : 'desktop'];
            $lastError = null;
            $fullUrl = ''; // 🔥 CORRECTION : Déclarer la variable avant le bloc try

            foreach ($strategies as $currentStrategy) {
                try {
                    Log::info("🎯 Essai avec stratégie: $currentStrategy");

                    $baseUrl = 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed';

                    $queryParams = [
                        'url=' . urlencode($url),
                        'strategy=' . $currentStrategy,
                        'key=' . config('services.pagespeed.key'),
                        'locale=en',
                    ];

                    $categories = ['PERFORMANCE', 'ACCESSIBILITY', 'SEO', 'BEST_PRACTICES'];
                    foreach ($categories as $category) {
                        $queryParams[] = 'category=' . $category;
                    }

                    $queryString = implode('&', $queryParams);
                    $fullUrl = $baseUrl . '?' . $queryString; // 🔥 CORRECTION : Assigner à la variable déclarée

                    Log::info('🔗 URL PageSpeed construite', [
                        'strategy' => $currentStrategy,
                        'url_short' => substr($fullUrl, 0, 120) . '...'
                    ]);

                    $response = Http::timeout(300)
                        ->withOptions([
                            'verify' => app()->environment('local') ? false : true
                        ])
                        ->get($fullUrl);

                    Log::info('📡 PageSpeedService - Réponse API reçue', [
                        'status' => $response->status(),
                        'strategy' => $currentStrategy
                    ]);

                    if ($response->successful()) {
                        $data = $response->json();

                        if (isset($data['lighthouseResult'])) {
                            Log::info("✅ Succès avec stratégie: $currentStrategy", [
                                'categories_retournées' => array_keys($data['lighthouseResult']['categories'] ?? [])
                            ]);
                            return $data;
                        }

                        Log::warning("⚠️ Réponse sans lighthouseResult", [
                            'strategy' => $currentStrategy,
                            'body' => substr($response->body(), 0, 200) // 🔥 CORRECTION : Limiter la taille du log
                        ]);
                    } else {
                        $lastError = $response->body();
                        Log::warning("❌ Échec stratégie $currentStrategy", [
                            'status' => $response->status(),
                            'error' => substr($lastError, 0, 200) // 🔥 CORRECTION : Limiter la taille du log
                        ]);

                        if ($response->status() === 500) {
                            continue;
                        }
                    }
                } catch (\Exception $e) {
                    $lastError = $e->getMessage();
                    Log::error("💥 Erreur stratégie $currentStrategy", [
                        'message' => $e->getMessage()
                    ]);
                    continue;
                }
            }

            Log::error('💥 Toutes les stratégies ont échoué', [
                'last_error' => $lastError ? substr($lastError, 0, 200) : 'Unknown error' // 🔥 CORRECTION : Gérer le cas null
            ]);
            return null;

        } catch (\Exception $e) {
            Log::error('❌ PageSpeed Insights failed', [
                'message' => $e->getMessage(),
                'url' => $url,
                'strategy' => $strategy
            ]);
            return null;
        }
    }

    /**
     * 🔥 NOUVELLE MÉTHODE : Test avec une seule catégorie à la fois
     */
    public function runMultiCategoryAudit(string $url, string $strategy = 'desktop'): ?array
    {
        Log::info('🎯 Début audit multi-catégories', ['url' => $url]);
        
        $allData = [];
        $categories = ['PERFORMANCE', 'ACCESSIBILITY', 'SEO', 'BEST_PRACTICES'];
        
        foreach ($categories as $category) {
            try {
                $baseUrl = 'https://pagespeedonline.googleapis.com/pagespeedonline/v5/runPagespeed';
                
                $queryParams = http_build_query([
                    'url' => $url,
                    'strategy' => $strategy,
                    'key' => config('services.pagespeed.key'),
                    'category' => $category,
                    'locale' => 'fr',
                ]);
                
                $fullUrl = $baseUrl . '?' . $queryParams;
                
                Log::info("🔗 Audit catégorie: $category", ['url_short' => substr($fullUrl, 0, 80) . '...']);
                
                $response = Http::timeout(300)->get($fullUrl);
                
                if ($response->successful()) {
                    $data = $response->json();
                    if (isset($data['lighthouseResult'])) {
                        $allData[$category] = $data;
                        Log::info("✅ Catégorie $category réussie", [
                            'score' => $data['lighthouseResult']['categories'][strtolower($category)]['score'] ?? 'non trouvé'
                        ]);
                    }
                } else {
                    Log::warning("❌ Catégorie $category échouée", ['status' => $response->status()]);
                    $allData[$category] = null;
                }
                
                sleep(1);
                
            } catch (\Exception $e) {
                Log::error("💥 Erreur catégorie $category", ['message' => $e->getMessage()]);
                $allData[$category] = null;
            }
        }
        
        return $this->mergeCategoryData($allData);
    }
    
    /**
     * Fusionner les données des différentes catégories
     */
    private function mergeCategoryData(array $allData): array
    {
        $merged = [
            'lighthouseResult' => [
                'categories' => [],
                'audits' => []
            ]
        ];
        
        foreach ($allData as $category => $data) {
            if ($data && isset($data['lighthouseResult']['categories'][strtolower($category)])) {
                $categoryKey = strtolower($category);
                $merged['lighthouseResult']['categories'][$categoryKey] = 
                    $data['lighthouseResult']['categories'][$categoryKey];
            }
            
            if ($category === 'PERFORMANCE' && $data && isset($data['lighthouseResult']['audits'])) {
                $merged['lighthouseResult']['audits'] = $data['lighthouseResult']['audits'];
            }
        }
        
        Log::info('🔗 Données fusionnées', [
            'categories_trouvées' => array_keys($merged['lighthouseResult']['categories'])
        ]);
        
        return $merged;
    }

    /**
     * 🔥 CORRECTION : Méthode extractCoreMetrics améliorée
     */
    public function extractCoreMetrics(?array $data): array
    {
        if (!is_array($data) || !isset($data['lighthouseResult']['audits'])) {
            Log::warning('⚠️ extractCoreMetrics received invalid data', [
                'data_type' => gettype($data),
                'has_audits' => isset($data['lighthouseResult']['audits'])
            ]);
            return [];
        }

        $audits = $data['lighthouseResult']['audits'];
        $metrics = [];
        
        try {
            // Métriques principales Core Web Vitals
            $coreMetrics = [
                'first-contentful-paint' => 'First Contentful Paint',
                'largest-contentful-paint' => 'Largest Contentful Paint',
                'cumulative-layout-shift' => 'Cumulative Layout Shift',
                'total-blocking-time' => 'Total Blocking Time',
                'speed-index' => 'Speed Index',
                'interactive' => 'Time to Interactive',
                'first-meaningful-paint' => 'First Meaningful Paint'
            ];
            
            foreach ($coreMetrics as $metricKey => $metricName) {
                if (isset($audits[$metricKey])) {
                    $audit = $audits[$metricKey];
                    $metrics[$metricKey] = [
                        'title' => $metricName,
                        'score' => $audit['score'] ?? null,
                        'displayValue' => $audit['displayValue'] ?? null,
                        'numericValue' => $audit['numericValue'] ?? null,
                        'scoreDisplayMode' => $audit['scoreDisplayMode'] ?? 'numeric'
                    ];
                    
                    Log::debug("📊 Métrique extraite: $metricKey", [
                        'score' => $metrics[$metricKey]['score'],
                        'displayValue' => $metrics[$metricKey]['displayValue']
                    ]);
                } else {
                    Log::debug("❌ Métrique non trouvée: $metricKey");
                }
            }
            
        } catch (\Exception $e) {
            Log::error('❌ Erreur lors de l\'extraction des métriques', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        return $metrics;
    }

    /**
     * Méthode helper pour extraire la valeur d'un audit de manière sécurisée
     */
    private function getAuditValue(array $audits, string $auditKey): ?string
    {
        if (!isset($audits[$auditKey])) {
            Log::debug("Audit {$auditKey} non trouvé");
            return null;
        }

        $audit = $audits[$auditKey];
        
        if (isset($audit['displayValue'])) {
            return $audit['displayValue'];
        }
        
        if (isset($audit['numericValue'])) {
            return $this->formatNumericValue($audit['numericValue'], $auditKey);
        }
        
        return null;
    }

    /**
     * Formater les valeurs numériques selon le type d'audit
     */
    private function formatNumericValue(float $value, string $auditKey): string
    {
        switch ($auditKey) {
            case 'first-contentful-paint':
            case 'largest-contentful-paint':
            case 'speed-index':
            case 'interactive':
            case 'first-meaningful-paint':
                return round($value / 1000, 1) . ' s';
                
            case 'cumulative-layout-shift':
                return number_format($value, 3);
                
            case 'total-blocking-time':
                return round($value) . ' ms';
                
            default:
                return (string) $value;
        }
    }

    /**
     * 🔥 CORRECTION : Méthode extractScoresByCategory améliorée
     */
    public function extractScoresByCategory(array $auditResult): array
    {
        if (!isset($auditResult['lighthouseResult']['categories'])) {
            Log::warning('⚠️ Aucune catégorie trouvée dans extractScoresByCategory');
            return [
                'accessibilité' => 0,
                'seo' => 0,
                'bonnes pratiques' => 0,
               
            ];
        }

        $categories = $auditResult['lighthouseResult']['categories'];
        $map = [
            'accessibility' => 'accessibilité',
            'seo' => 'seo',
            'best-practices' => 'bonnes pratiques',
           
        ];
    
        $scores = [];
    
        foreach ($map as $key => $label) {
            $scores[$label] = isset($categories[$key]['score'])
                ? round($categories[$key]['score'] * 100)
                : 0;
                
            Log::debug("📊 Score $label", ['score' => $scores[$label]]);
        }
    
        return $scores;
    }

    /**
     * 🔥 CORRECTION : Méthode extractCategoryDetails améliorée
     */
    public function extractCategoryDetails(array $auditResult, string $categoryKey): array
    {
        $category = $auditResult['lighthouseResult']['categories'][$categoryKey] ?? null;

        if (!$category) {
            Log::debug("❌ Catégorie $categoryKey non trouvée");
            return [
                'score' => 0,
                'title' => ucfirst($categoryKey),
                'description' => null,
                'manualDescription' => null,
            ];
        }

        $details = [
            'score' => isset($category['score']) ? round($category['score'] * 100) : 0,
            'title' => $category['title'] ?? ucfirst($categoryKey),
            'description' => $category['description'] ?? null,
            'manualDescription' => $category['manualDescription'] ?? null,
        ];
        
        Log::debug("📊 Détails catégorie $categoryKey", [
            'score' => $details['score'],
            'title' => $details['title']
        ]);

        return $details;
    }

    /**
     * 🔥 NOUVELLE MÉTHODE : Extraire tous les scores d'une seule fois
     */
    public function extractAllScores(array $auditResult): array
    {
        if (!isset($auditResult['lighthouseResult']['categories'])) {
            return [];
        }

        $categories = $auditResult['lighthouseResult']['categories'];
        $scores = [];

        foreach ($categories as $key => $category) {
            $scores[$key] = [
                'score' => isset($category['score']) ? round($category['score'] * 100) : 0,
                'title' => $category['title'] ?? ucfirst($key),
            ];
        }

        return $scores;
    }



    public function extractAuditFragments(array $audits): array
{
    $opportunities = [];
    $diagnostics = [];
    $informative = [];

    foreach ($audits as $id => $audit) {
        if (!isset($audit['scoreDisplayMode'])) continue;

        $fragment = [
            'id' => $id,
            'title' => $audit['title'] ?? $id,
            'description' => $audit['description'] ?? null,
            'score' => $audit['score'] ?? null,
            'displayValue' => $audit['displayValue'] ?? null,
            'items' => $audit['details']['items'] ?? [],
        ];

        match ($audit['scoreDisplayMode']) {
            'numeric', 'binary' => $fragment['score'] < 1
                ? $diagnostics[] = $fragment
                : null,
            'informative' => $informative[] = $fragment,
            'manual' => $informative[] = $fragment,
            default => null,
        };

        if (isset($audit['details']['overallSavingsMs'])) {
            $fragment['estimatedSavingsMs'] = $audit['details']['overallSavingsMs'];
            $opportunities[] = $fragment;
        }
    }

    return compact('opportunities', 'diagnostics', 'informative');
}








}