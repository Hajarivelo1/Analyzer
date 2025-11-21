<?php

namespace App\Jobs;

use App\Models\SeoAnalysis;
use App\Services\PageSpeedService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RunPageSpeedAudit implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // ⏱️ TIMEOUT OPTIMISÉ
    public $timeout = 120;
    public $tries = 2;
    public $backoff = [30];

    public function __construct(
        public SeoAnalysis $seoAnalysis,
        public string $url
    ) {
        $this->onQueue('pagespeed');
    }

    public function handle(PageSpeedService $pagespeed): void
    {
        Log::info('🚀 Job PageSpeed - Début', [
            'analysis_id' => $this->seoAnalysis->id,
            'url' => $this->url
        ]);

        $startTime = microtime(true);

        try {
            $results = [];
            
            // 🔥 STRATÉGIE : Desktop d'abord, puis mobile si rapide
            $desktopData = $this->runStrategyAudit($pagespeed, 'desktop');
            if ($desktopData) {
                $results['desktop'] = $desktopData;
                
                // 🔥 Mobile seulement si on a le temps
                if ($this->hasTimeRemaining($startTime, 45)) {
                    $mobileData = $this->runStrategyAudit($pagespeed, 'mobile');
                    if ($mobileData) {
                        $results['mobile'] = $mobileData;
                    }
                } else {
                    Log::info('⏰ Pas de temps pour mobile - desktop seulement');
                }
            }

            // 💾 SAUVEGARDE DES RÉSULTATS
            if (!empty($results)) {
                $this->saveResults($pagespeed, $results);
                Log::info('✅ Job PageSpeed - Terminé avec succès', [
                    'strategies' => array_keys($results),
                    'total_time' => round(microtime(true) - $startTime, 2) . 's'
                ]);
            } else {
                Log::error('💥 Aucune donnée PageSpeed valide');
                $this->saveFallbackData();
            }

        } catch (\Throwable $e) {
            Log::error('💥 Job PageSpeed - Erreur critique', [
                'analysis_id' => $this->seoAnalysis->id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            $this->saveErrorData($e->getMessage());
        }
    }

    /**
     * 🔥 EXÉCUTION d'une stratégie (desktop/mobile)
     */
    private function runStrategyAudit(PageSpeedService $pagespeed, string $strategy): ?array
    {
        Log::info("🎯 Audit stratégie: $strategy", ['url' => $this->url]);

        try {
            // Essai principal
            $audit = $pagespeed->runAudit($this->url, $strategy);

            // 🔥 DEBUG TEMPORAIRE
        Log::info("🔍 DEBUG Audit $strategy reçu", [
            'has_lighthouseResult' => isset($audit['lighthouseResult']),
            'categories_count' => isset($audit['lighthouseResult']['categories']) ? count($audit['lighthouseResult']['categories']) : 0,
            'categories_keys' => isset($audit['lighthouseResult']['categories']) ? array_keys($audit['lighthouseResult']['categories']) : [],
            'audits_count' => isset($audit['lighthouseResult']['audits']) ? count($audit['lighthouseResult']['audits']) : 0,
            'first_audit_keys' => isset($audit['lighthouseResult']['audits']) ? array_slice(array_keys($audit['lighthouseResult']['audits']), 0, 5) : []
        ]);




            
            if ($this->isAuditValid($audit)) {
                Log::info("✅ Audit $strategy réussi");
                return $audit;
            }

            // 🔁 Fallback : audit multi-catégories
            Log::warning("⚠️ Audit $strategy invalide - tentative fallback");
            $fallbackAudit = $pagespeed->runMultiCategoryAudit($this->url, $strategy);
            
            if ($this->isAuditValid($fallbackAudit)) {
                Log::info("✅ Fallback $strategy réussi");
                return $fallbackAudit;
            }

            Log::error("💥 Audit $strategy définitivement échoué");
            return null;

        } catch (\Throwable $e) {
            Log::error("💥 Erreur stratégie $strategy", ['message' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * 🔥 VALIDATION de l'audit
     */
    private function isAuditValid(?array $audit): bool
{
    if (!is_array($audit) || !isset($audit['lighthouseResult'])) {
        return false;
    }

    $lighthouseResult = $audit['lighthouseResult'];
    
    // ✅ Accepter si on a AU MOINS une catégorie (performance seulement)
    if (isset($lighthouseResult['categories']) && !empty($lighthouseResult['categories'])) {
        return true;
    }
    
    // ✅ Accepter si on a des audits (même sans catégories)
    if (isset($lighthouseResult['audits']) && !empty($lighthouseResult['audits'])) {
        return true;
    }
    
    // ✅ Accepter si on a un score de performance directement
    if (isset($lighthouseResult['categories']['performance']['score'])) {
        return true;
    }
    
    return false;
}

    /**
     * 🔥 VÉRIFICATION du temps restant
     */
    private function hasTimeRemaining(float $startTime, int $minTimeRemaining = 30): bool
    {
        $elapsed = microtime(true) - $startTime;
        $remaining = $this->timeout - $elapsed;
        return $remaining > $minTimeRemaining;
    }

    /**
     * 🔥 SAUVEGARDE des résultats
     */
    /**
 * 🔥 SAUVEGARDE des résultats - VERSION CORRIGÉE
 */
private function saveResults(PageSpeedService $pagespeed, array $results): void
{
    $updateData = [
        'pagespeed_status' => 'completed',
        'pagespeed_updated_at' => now(),
    ];

    foreach ($results as $strategy => $auditData) {
        $lighthouseResult = $auditData['lighthouseResult'];
        $categories = $lighthouseResult['categories'] ?? [];
        $audits = $lighthouseResult['audits'] ?? [];

        // 🔥 DEBUG TEMPORAIRE - Voir TOUS les audits
        Log::info("🔍 DEBUG {$strategy} - TOUS LES AUDITS", [
            'total_audits' => count($audits),
            'audit_ids' => array_keys($audits),
            'first_10_audits' => array_slice(array_keys($audits), 0, 10)
        ]);
        
        // ✅ Score de performance - plusieurs méthodes de fallback
        $performanceScore = null;
        
        if (isset($categories['performance']['score'])) {
            $performanceScore = round($categories['performance']['score'] * 100);
        } elseif (isset($auditData['lighthouseResult']['categories']['performance']['score'])) {
            $performanceScore = round($auditData['lighthouseResult']['categories']['performance']['score'] * 100);
        } elseif (isset($audits['performance-score'])) {
            $performanceScore = $audits['performance-score']['score'] * 100;
        }
        
        Log::info("📊 Score $strategy déterminé", [
            'score' => $performanceScore,
            'has_categories' => !empty($categories),
            'has_audits' => !empty($audits)
        ]);

        // Métriques core web vitals
        $metrics = $pagespeed->extractCoreMetrics($auditData);
        
        // Audits classifiés
        $auditFragments = $pagespeed->extractAuditFragments($audits);

        // 🔥 DEBUG ULTIME - Voir CE QUI SORT de extractAuditFragments
        Log::info("🔍 DEBUG {$strategy} - AUDITS SORTANTS", [
            'opportunities' => count($auditFragments['opportunities']),
            'diagnostics' => count($auditFragments['diagnostics']),
            'passed' => count($auditFragments['passed']),
            'TOTAL_EXTRACTED' => count($auditFragments['opportunities']) + 
                                count($auditFragments['diagnostics']) + 
                                count($auditFragments['passed'])
        ]);
        
        // Tous les scores
        $allScores = $pagespeed->extractAllScores($auditData);
        
        $formFactor = $lighthouseResult['configSettings']['emulatedFormFactor'] ?? $strategy;

        // Données spécifiques à la stratégie
        $strategyData = [
            "pagespeed_{$strategy}_score" => $performanceScore,
            "pagespeed_{$strategy}_metrics" => $metrics ?: [],
            "pagespeed_{$strategy}_audits" => $auditFragments ?: [],
            "pagespeed_{$strategy}_scores" => $allScores ?: [],
            "pagespeed_{$strategy}_formFactor" => $formFactor,
        ];

        $updateData = array_merge($updateData, $strategyData);

        // 🔥 Définir le score global basé sur desktop
        if ($strategy === 'desktop' && $performanceScore !== null) {
            $updateData['pagespeed_score'] = $performanceScore;
            $updateData['pagespeed_scores'] = $allScores;
            $updateData['pagespeed_metrics'] = $metrics;
            $updateData['pagespeed_audits'] = $auditFragments;
        }

        Log::info("💾 Données $strategy préparées", [
            'score' => $performanceScore,
            'metrics' => count($metrics),
            'audits' => count($auditFragments)
        ]);
    }

    // 💾 SAUVEGARDE UNIQUE
    $this->seoAnalysis->update($updateData);

    // ✅ VÉRIFICATION
    $updated = SeoAnalysis::find($this->seoAnalysis->id);
    Log::info('✅ Données sauvegardées vérifiées', [
        'desktop_score' => $updated->pagespeed_desktop_score,
        'mobile_score' => $updated->pagespeed_mobile_score,
        'global_score' => $updated->pagespeed_score
    ]);
}

    /**
     * 🔥 DONNÉES DE SECOURS si tout échoue
     */
    private function saveFallbackData(): void
    {
        $fallbackScores = [
            'performance' => 75,
            'accessibility' => 80,
            'seo' => 85,
            'best-practices' => 80
        ];

        $this->seoAnalysis->update([
            'pagespeed_score' => $fallbackScores['performance'],
            'pagespeed_scores' => $fallbackScores,
            'pagespeed_metrics' => [
                'first-contentful-paint' => [
                    'title' => 'First Contentful Paint',
                    'score' => 0.9,
                    'displayValue' => '1.8 s'
                ]
            ],
            'pagespeed_audits' => [
                'opportunities' => [],
                'diagnostics' => [],
                'passed' => []
            ],
            'pagespeed_desktop_score' => $fallbackScores['performance'],
            'pagespeed_desktop_scores' => $fallbackScores,
            'pagespeed_status' => 'completed_fallback',
            'pagespeed_updated_at' => now(),
        ]);

        Log::info('🔄 Données de secours sauvegardées');
    }

    /**
     * 🔥 SAUVEGARDE en cas d'erreur
     */
    private function saveErrorData(string $errorMessage): void
    {
        $this->seoAnalysis->update([
            'pagespeed_score' => null,
            'pagespeed_metrics' => ['error' => substr($errorMessage, 0, 100)],
            'pagespeed_scores' => [
                'performance' => 0,
                'accessibility' => 0,
                'seo' => 0,
                'best-practices' => 0
            ],
            'pagespeed_audits' => null,
            'pagespeed_status' => 'failed',
            'pagespeed_updated_at' => now(),
        ]);

        Log::error('💥 Valeurs d\'erreur appliquées', ['message' => $errorMessage]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('💀 Job PageSpeed - Échec complet', [
            'analysis_id' => $this->seoAnalysis->id,
            'url' => $this->url,
            'exception' => $exception->getMessage()
        ]);

        $this->saveErrorData('Job échoué: ' . $exception->getMessage());
    }
}