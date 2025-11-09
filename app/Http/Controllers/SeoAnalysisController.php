<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Services\ScraperService;
use App\Models\SeoAnalysis;
use Illuminate\Support\Facades\Http;
use App\Jobs\FetchPageRank;
use App\Services\WhoisService;
use App\Services\PageSpeedService;
use Illuminate\Support\Facades\Log;
use App\Jobs\RunPageSpeedAudit;

class SeoAnalysisController extends Controller
{
    public function create()
    {
        $projects = Project::where('user_id', auth()->id())->get();
        return view('admin.backend.analysis.create', compact('projects'));
    }

    public function run(Request $request, ScraperService $scraper, WhoisService $whois, PageSpeedService $pagespeed)
    {
        try {
            // Activer l'affichage des erreurs pour le débogage
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);
            
            $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'base_url' => 'required|url',
                'project_id' => 'sometimes|exists:projects,id'
            ]);

            Log::info('🚀 Début analyse SEO', ['url' => $request->base_url]);

            // 🔧 Projet
            $project = $request->has('project_id')
                ? Project::where('user_id', auth()->id())->findOrFail($request->project_id)
                : Project::create([
                    'user_id' => auth()->id(),
                    'name' => $request->name ?? 'New project ' . date('Y-m-d H:i:s'),
                    'base_url' => $request->base_url
                ]);

            $project->update(['base_url' => $request->base_url]);
            $domain = parse_url($project->base_url, PHP_URL_HOST);

            // 🔍 Scraper Laravel avec gestion d'erreur améliorée
            $scraperResult = $scraper->analyze($project->base_url);
            $scraperFailed = false;
            $mainContent = null;
            $readability = null;
            $contentAnalysis = [];
            $cloudflareBlocked = false;

            // Vérifier si le résultat du scraper est valide
            if (!is_array($scraperResult) || ($scraperResult['status'] ?? 'success') === 'error') {
                Log::warning('⚠️ Laravel scraper failed, fallback triggered');
                $scraperFailed = true;
                
                // 🧠 Fallback microservice Python
                try {
                    $response = Http::timeout(120)->post('http://127.0.0.1:5000/extract', [
                        'url' => $project->base_url
                    ]);

                    if ($response->successful()) {
                        $json = $response->json();
                        if (isset($json['error'])) {
                            $cloudflareBlocked = str_contains(strtolower($json['error']), 'cloudflare');
                            Log::warning('Cloudflare blocked the request', ['error' => $json['error']]);
                        } else {
                            $mainContent = $json['content'] ?? null;
                            $readability = $json['readability'] ?? null;
                            $contentAnalysis = $json['analysis'] ?? [];
                            Log::info('✅ Python fallback successful', ['content_length' => strlen($mainContent)]);
                        }
                    } else {
                        Log::error('❌ Python microservice returned error', ['status' => $response->status()]);
                    }
                } catch (\Exception $e) {
                    Log::error('❌ Python fallback failed', ['message' => $e->getMessage()]);
                }
            } else {
                // Utiliser les données du scraper réussi
                $mainContent = $scraperResult['main_content'] ?? null;
                $readability = $scraperResult['readability_score'] ?? null;
                $contentAnalysis = $scraperResult['content_analysis'] ?? [];
                Log::info('✅ Laravel scraper successful', ['content_length' => strlen($mainContent)]);
            }

            // 🗂️ Préparer les données pour la sauvegarde avec des valeurs par défaut sécurisées
            $headingsStructure = [];
            $htmlContent = '';

            // Déterminer la source du HTML
            if (!$scraperFailed && isset($scraperResult['html']) && !empty($scraperResult['html'])) {
                $htmlContent = $scraperResult['html'];
            } elseif ($mainContent) {
                $htmlContent = $mainContent;
            }

            // Analyser la structure des headings si on a du contenu HTML
            if (!empty($htmlContent)) {
                try {
                    libxml_use_internal_errors(true);
                    $dom = new \DOMDocument('1.0', 'UTF-8');
                    $html = mb_convert_encoding($htmlContent, 'HTML-ENTITIES', 'UTF-8');
                    
                    @$dom->loadHTML($html);
                    libxml_clear_errors();

                    foreach (['h1','h2','h3','h4','h5','h6'] as $tag) {
                        $elements = $dom->getElementsByTagName($tag);
                        foreach ($elements as $node) {
                            $headingsStructure[] = [
                                'tag' => $tag,
                                'text' => trim($node->textContent),
                                'depth' => $this->getDomDepth($node)
                            ];
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning('DOM analysis failed', ['message' => $e->getMessage()]);
                }
            }

            // 🌐 WHOIS
            $whoisData = null;
            try {
                $whoisData = $whois->lookup($domain);
                Log::info('✅ WHOIS lookup successful', ['domain' => $domain]);
            } catch (\Exception $e) {
                Log::warning('WHOIS lookup failed', ['domain' => $domain, 'message' => $e->getMessage()]);
            }

            // ✅ Préparation des données pour la sauvegarde avec valeurs par défaut
            $analysisData = [
                'project_id' => $project->id,
                'page_url' => $project->base_url,
                'page_title' => $scraperResult['title'] ?? 'Titre non disponible',
                'meta_description' => $scraperResult['meta_description'] ?? 'Description non disponible',
                'h1_tags' => $scraperFailed ? null : ($scraperResult['headings'][0] ?? null),
                'headings' => $scraperFailed ? '[]' : (is_array($scraperResult['headings'] ?? []) ? json_encode($scraperResult['headings']) : '[]'),
                'headings_structure' => json_encode($headingsStructure),
                'images_data' => $scraperFailed ? '[]' : (is_array($scraperResult['images'] ?? []) ? json_encode($scraperResult['images']) : '[]'),
                'keywords' => $scraperResult['keywords'] ?? '',
                'raw_html' => $htmlContent,
                'word_count' => $scraperResult['word_count'] ?? 0,
                'keyword_density' => $scraperResult['density'] ?? 0,
                'mobile_friendly' => ($scraperResult['mobile'] ?? false) ? 1 : 0,
                'score' => rand(60, 95),
                'recommendations' => 'To be generated',
                'content_analysis' => json_encode($contentAnalysis),
                'main_content' => $mainContent,
                'readability_score' => $readability,
                'cloudflare_blocked' => $cloudflareBlocked,
                'technical_audit' => $scraperResult['technical_audit'] ?? '',
                'https_enabled' => $scraperResult['https_enabled'] ?? false,
                'has_structured_data' => $scraperResult['has_structured_data'] ?? false,
                'noindex_detected' => $scraperResult['noindex_detected'] ?? false,
                'html_size' => $scraperResult['html_size'] ?? 0,
                'total_links' => $scraperResult['total_links'] ?? 0,
                'has_og_tags' => $scraperResult['has_og_tags'] ?? false,
                'html_lang' => $scraperResult['html_lang'] ?? '',
                'has_favicon' => $scraperResult['has_favicon'] ?? false,
                'load_time' => $scraperResult['load_time'] ?? 0,
                'whois_data' => $whoisData,
                // Initialiser les champs PageSpeed pour éviter les null
                'pagespeed_score' => 0,
                'pagespeed_metrics' => [],
                'pagespeed_scores' => [],
            ];

            // ✅ Sauvegarde initiale
            $seoAnalysis = SeoAnalysis::create($analysisData);

            if (!$seoAnalysis) {
                return redirect()->back()->withErrors(['error' => 'Failed to save SEO analysis.']);
            }

            Log::info('✅ SEO analysis created', ['analysis_id' => $seoAnalysis->id]);

            // 🚀 PageSpeed Insights - UNIQUEMENT le job, pas d'appel direct
            Log::info('🚀 Lancement du job PageSpeed');
            RunPageSpeedAudit::dispatch($seoAnalysis, $project->base_url)->onQueue('pagespeed');

            // ❌ SUPPRIMER l'appel direct à PageSpeed ici pour éviter les conflits

            // 🔁 PageRank
            Log::info('🚀 Lancement du job PageRank');
            FetchPageRank::dispatch($seoAnalysis);

            // 🌐 OpenPageRank API
            try {
                $certPath = 'D:/xampp/php/extras/ssl/cacert.pem';
                $verifyOption = file_exists($certPath)
                    ? (app()->environment('local') ? false : $certPath)
                    : false;

                $response = Http::withHeaders([
                    'API-OPR' => config('services.openpagerank.key'),
                ])->withOptions([
                    'verify' => $verifyOption,
                ])->get('https://openpagerank.com/api/v1.0/getPageRank', [
                    'domains' => [$domain],
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $rank = $data['response'][0]['page_rank_decimal'] ?? null;
                    $global = $data['response'][0]['rank'] ?? null;

                    if ($rank !== null) {
                        $seoAnalysis->update([
                            'page_rank' => $rank,
                            'page_rank_global' => $global,
                        ]);
                        Log::info('✅ PageRank updated', ['rank' => $rank, 'global' => $global]);
                    }
                }
            } catch (\Exception $e) {
                Log::error('OpenPageRank API failed', ['message' => $e->getMessage()]);
            }

            // 🎯 Keywords (uniquement si le scraper a réussi)
            if (!$scraperFailed && isset($scraperResult['keywords']) && is_array($scraperResult['keywords'])) {
                $keywords = implode(',', array_keys($scraperResult['keywords']));
                $project->update([
                    'target_keywords' => $keywords,
                ]);
                Log::info('✅ Keywords updated', ['keywords' => $keywords]);
            }

            Log::info('✅ Toutes les tâches lancées, redirection...');

            // ✅ Final redirect
            return redirect()->route('project.show', [
                'id' => $project->id,
                'analysis_id' => $seoAnalysis->id
            ])->with('success', 'SEO analysis completed successfully!');
        } catch (\Exception $e) {
            Log::error('Exception caught', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()
                             ->withErrors(['error' => 'Analysis failed: ' . $e->getMessage()])
                             ->withInput();
        }
    }

    private function getDomDepth($node)
    {
        $depth = 0;
        while ($node->parentNode) {
            $depth++;
            $node = $node->parentNode;
        }
        return $depth;
    }

    public function show($id, Request $request)
{
    // 🔍 Récupération sécurisée du projet
    $project = Project::where('id', $id)
                      ->where('user_id', auth()->id())
                      ->firstOrFail();

    // 🧠 Récupération de l'analyse demandée ou la plus récente
    $analysisId = $request->get('analysis_id');

    $analysis = $analysisId
        ? SeoAnalysis::where('id', $analysisId)
                     ->where('project_id', $project->id)
                     ->first()
        : SeoAnalysis::where('project_id', $project->id)
                     ->latest()
                     ->first();

    if (!$analysis) {
        abort(404, 'Aucune analyse SEO disponible pour ce projet.');
    }

    // 🔄 CORRECTION : Rafraîchissement CORRECT des données
    // Ne pas utiliser refresh() car il ne recharge pas les casts JSON
    $analysis = SeoAnalysis::find($analysis->id);

    // 🧾 Log pour vérifier les données PageSpeed AVANT envoi à la vue
    Log::info('📥 DONNÉES PAGESPEED DANS SHOW() - AVANT ENVOI', [
        'analysis_id' => $analysis->id,
        'pagespeed_score' => $analysis->pagespeed_score,
        'pagespeed_metrics' => $analysis->pagespeed_metrics,
        'pagespeed_scores' => $analysis->pagespeed_scores,
        'updated_at' => $analysis->updated_at,
    ]);

    // ✅ Transmission à la vue
    return view('user.projects.show', [
        'project' => $project,
        'analysis' => $analysis,
        'scores' => $analysis->pagespeed_scores ?? [],
        'metrics' => $analysis->pagespeed_metrics ?? [],
    ]);
}

}
