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

    public $timeout = 300;

    protected SeoAnalysis $seoAnalysis;
    protected string $url;

    public function __construct(SeoAnalysis $seoAnalysis, string $url)
    {
        $this->seoAnalysis = $seoAnalysis;
        $this->url = $url;
        $this->onQueue('pagespeed');
    }

    public function handle(PageSpeedService $pagespeed): void
    {
        Log::info('🔍 Job PageSpeed - Début handle()', [
            'seo_analysis_id' => $this->seoAnalysis->id,
            'url_short' => substr($this->url, 0, 50)
        ]);

        try {
            $audit = null;
            $strategies = ['desktop', 'mobile'];
            
            foreach ($strategies as $strategy) {
                Log::info("🔄 Essai avec stratégie: $strategy");
                $audit = $pagespeed->runAudit($this->url, $strategy);
                
                if ($this->isAuditValid($audit)) {
                    Log::info("✅ Stratégie $strategy réussie");
                    break;
                }
                
                if ($strategy === 'desktop') {
                    sleep(2);
                }
            }

            if (!$this->isAuditValid($audit)) {
                Log::warning('⚠️ PageSpeed is unavailable for this site');
                $this->updateWithUnavailableValues();
                return;
            }

            Log::info('🔍 Job PageSpeed - Audit réussi, extraction des données');
            
            $categories = $audit['lighthouseResult']['categories'] ?? [];
            
            Log::info('🔍 Catégories trouvées', [
                'categories' => array_keys($categories),
                'scores_presents' => $this->getAvailableScores($categories)
            ]);

            // 🔥🔥🔥 CORRECTION CRITIQUE : Utilisation des méthodes du Service
            $score = $categories['performance']['score'] ?? null;
            $metrics = $pagespeed->extractCoreMetrics($audit);
            $secondaryScores = $pagespeed->extractScoresByCategory($audit); // 🔥 LIGNE CORRIGÉE
            $accessibilityDetails = $pagespeed->extractCategoryDetails($audit, 'accessibility');

            $finalScore = $score ? round($score * 100) : null;

            // Log détaillé
            Log::info('🎯 Scores PageSpeed extraits', [
                'performance' => $finalScore,
                'accessibilité' => $secondaryScores['accessibilité'],
                'seo' => $secondaryScores['seo'],
                'bonnes_pratiques' => $secondaryScores['bonnes pratiques'],
                
                'metrics_count' => count($metrics)
            ]);

            // Mise à jour de la base de données
            $updateData = [
                'pagespeed_score' => $finalScore,
                'pagespeed_metrics' => $metrics,
                'pagespeed_scores' => $secondaryScores,
                'accessibility_score' => $accessibilityDetails['score'],
                'accessibility_title' => $accessibilityDetails['title'],
                'accessibility_description' => $accessibilityDetails['description'],
                'accessibility_manual' => $accessibilityDetails['manualDescription'],
            ];

            $success = $this->seoAnalysis->update($updateData);

            Log::info('💾 Mise à jour base de données', [
                'success' => $success,
                'score' => $finalScore,
                'has_accessibility' => $accessibilityDetails['score'] > 0
            ]);

            if ($success) {
                Log::info('✅ Job PageSpeed - Terminé avec succès');
            } else {
                Log::error('❌ Échec de la mise à jour BD');
            }

        } catch (\Throwable $e) {
            Log::error('💥 Job PageSpeed - Erreur critique', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            $this->updateWithErrorValues($e->getMessage());
        }
    }

    /**
     * Vérifie si l'audit est valide
     */
    private function isAuditValid(?array $audit): bool
    {
        if (!is_array($audit)) {
            return false;
        }

        if (!isset($audit['lighthouseResult'])) {
            return false;
        }

        $categories = $audit['lighthouseResult']['categories'] ?? [];
        return !empty($categories);
    }

    /**
     * Récupère les scores disponibles
     */
    private function getAvailableScores(array $categories): array
    {
        $scores = [];
        $availableCategories = ['performance', 'accessibility', 'seo', 'best-practices'];
        
        foreach ($availableCategories as $category) {
            if (isset($categories[$category]['score'])) {
                $scores[$category] = round($categories[$category]['score'] * 100);
            } else {
                $scores[$category] = 0;
            }
        }
        
        return $scores;
    }

    /**
     * Mettre à jour avec des valeurs "indisponible"
     */
    private function updateWithUnavailableValues(): void
    {
        $this->seoAnalysis->update([
            'pagespeed_score' => null,
            'pagespeed_metrics' => ['info' => 'Analyse PageSpeed indisponible pour ce site'],
            'pagespeed_scores' => [
                'accessibilité' => null,
                'seo' => null,
                'bonnes pratiques' => null,
                
            ],
            'accessibility_score' => 0,
            'accessibility_title' => 'Accessibilité',
            'accessibility_description' => null,
            'accessibility_manual' => null,
        ]);
        
        Log::info('ℹ️ Données "indisponible" enregistrées');
    }

    /**
     * Mettre à jour avec des valeurs d'erreur
     */
    private function updateWithErrorValues(string $errorMessage): void
    {
        $this->seoAnalysis->update([
            'pagespeed_score' => null,
            'pagespeed_metrics' => ['error' => $errorMessage],
            'pagespeed_scores' => [
                'accessibilité' => null,
                'seo' => null,
                'bonnes pratiques' => null,
                
            ],
            'accessibility_score' => 0,
            'accessibility_title' => 'Accessibilité',
            'accessibility_description' => null,
            'accessibility_manual' => null,
        ]);
        
        Log::error('💥 Valeurs d\'erreur appliquées', ['message' => $errorMessage]);
    }

    /**
     * Gestion de l'échec du job
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('💀 Job PageSpeed - Échec complet', [
            'seo_analysis_id' => $this->seoAnalysis->id,
            'url' => $this->url,
            'exception' => $exception->getMessage()
        ]);
        
        $this->updateWithErrorValues('Job échoué: ' . $exception->getMessage());
    }
}