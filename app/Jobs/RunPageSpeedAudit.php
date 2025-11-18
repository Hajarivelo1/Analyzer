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

   // Dans RunPageSpeedAudit.php, remplacez toute la méthode handle()

public function handle(PageSpeedService $pagespeed): void
{
    Log::info('🔍 Job PageSpeed - Début handle()', [
        'seo_analysis_id' => $this->seoAnalysis->id,
        'url_short' => substr($this->url, 0, 50)
    ]);

    try {
        $strategies = ['desktop', 'mobile'];

        foreach ($strategies as $strategy) {
            Log::info("🔄 Audit PageSpeed avec stratégie: $strategy");

            $audit = $pagespeed->runAudit($this->url, $strategy);

            if (!$this->isAuditValid($audit)) {
                Log::warning("⚠️ Audit $strategy invalide - tentative fallback");
                // Tentative avec l'audit multi-catégories
                $audit = $pagespeed->runMultiCategoryAudit($this->url, $strategy);
                
                if (!$this->isAuditValid($audit)) {
                    Log::error("💥 Audit $strategy définitivement invalide");
                    continue;
                }
            }

            // EXTRACTION DES DONNÉES
            $categories = $audit['lighthouseResult']['categories'] ?? [];
            $audits = $audit['lighthouseResult']['audits'] ?? [];
            
            // Score de performance
            $score = $categories['performance']['score'] ?? null;
            $finalScore = $score ? round($score * 100) : null;
            
            // Métriques core web vitals
            $metrics = $pagespeed->extractCoreMetrics($audit);
            
            // Audits classifiés
            $auditFragments = $pagespeed->extractAuditFragments($audits);
            
            // Tous les scores (accessibilité, SEO, etc.)
            $allScores = $pagespeed->extractAllScores($audit);
            
            $formFactor = $audit['lighthouseResult']['configSettings']['emulatedFormFactor'] ?? $strategy;

            // PRÉPARATION DES DONNÉES POUR LA BASE
            $updateData = [
                "pagespeed_{$strategy}_score" => $finalScore,
                "pagespeed_{$strategy}_metrics" => $metrics ?: [],
                "pagespeed_{$strategy}_audits" => $auditFragments ?: [],
                "pagespeed_{$strategy}_scores" => $allScores ?: [],
                "pagespeed_{$strategy}_formFactor" => $formFactor,
            ];

            Log::info("💾 Données préparées pour $strategy", [
                'score' => $finalScore,
                'metrics_count' => count($metrics),
                'audits_count' => count($auditFragments),
                'scores_count' => count($allScores)
            ]);

            // MISE À JOUR
            $this->seoAnalysis->update($updateData);

            // VÉRIFICATION
            $updated = SeoAnalysis::find($this->seoAnalysis->id);
            Log::info("✅ Vérification après update $strategy", [
                'score_sauvegardé' => $updated->{"pagespeed_{$strategy}_score"},
                'metrics_sauvegardés' => count($updated->{"pagespeed_{$strategy}_metrics"} ?? []),
                'audits_sauvegardés' => count($updated->{"pagespeed_{$strategy}_audits"} ?? [])
            ]);
        }

        Log::info('✅ Job PageSpeed - Terminé avec succès');

    } catch (\Throwable $e) {
        Log::error('💥 Job PageSpeed - Erreur critique', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);

        $this->updateWithErrorValues($e->getMessage());
    }
}



    private function isAuditValid(?array $audit): bool
    {
        if (!is_array($audit)) return false;
        if (!isset($audit['lighthouseResult'])) return false;

        $categories = $audit['lighthouseResult']['categories'] ?? [];
        return !empty($categories);
    }

    private function getAvailableScores(array $categories): array
    {
        $scores = [];
        $availableCategories = ['performance', 'accessibility', 'seo', 'best-practices'];

        foreach ($availableCategories as $category) {
            $scores[$category] = isset($categories[$category]['score'])
                ? round($categories[$category]['score'] * 100)
                : 0;
        }

        return $scores;
    }

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
            'accessibility_title' => 'Accessibility',
            'accessibility_description' => null,
            'accessibility_manual' => null,
            'pagespeed_audits' => null,
        ]);

        Log::info('ℹ️ Données "indisponible" enregistrées');
    }

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
            'pagespeed_audits' => null,
        ]);

        Log::error('💥 Valeurs d\'erreur appliquées', ['message' => $errorMessage]);
    }

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
