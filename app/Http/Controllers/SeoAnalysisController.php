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
        // ⏱️ Augmenter le temps d'exécution MAIS avec des timeouts raisonnables
        set_time_limit(180); // 3 minutes max
        ini_set('max_execution_time', 180);
        
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

            // 🔧 Gestion du projet
            $project = $request->has('project_id')
                ? Project::where('user_id', auth()->id())->findOrFail($request->project_id)
                : Project::create([
                    'user_id' => auth()->id(),
                    'name' => $request->name ?? 'New project ' . date('Y-m-d H:i:s'),
                    'base_url' => $request->base_url
                ]);

            $project->update(['base_url' => $request->base_url]);
            $domain = parse_url($project->base_url, PHP_URL_HOST);

            // 🔍 Scraper Laravel avec timeout réduit
            $scraperResult = [];
            $scraperFailed = false;
            $mainContent = null;
            $readability = null;
            $contentAnalysis = [];
            $cloudflareBlocked = false;

            try {
                // ⏱️ Timeout réduit pour le scraper
                $scraperResult = $scraper->analyze($project->base_url);
                
                if (!is_array($scraperResult) || ($scraperResult['status'] ?? 'success') === 'error') {
                    throw new \Exception('Scraper returned error');
                }
                
                Log::info('✅ Laravel scraper successful', [
                    'content_length' => strlen($scraperResult['main_content'] ?? ''),
                    'has_content_analysis' => isset($scraperResult['content_analysis']),
                    'paragraphs_count' => count($scraperResult['content_analysis']['paragraphs'] ?? [])
                ]);
                
            } catch (\Exception $e) {
                Log::warning('⚠️ Laravel scraper failed, fallback triggered', ['error' => $e->getMessage()]);
                $scraperFailed = true;
                
                // 🧠 Fallback microservice Python avec timeout réduit
                try {
                    $response = Http::timeout(60) // ⏱️ 1 minute max pour Python
                                    ->connectTimeout(10)
                                    ->post('http://127.0.0.1:5000/extract', [
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
                            Log::info('✅ Python fallback successful', [
                                'content_length' => strlen($mainContent),
                                'paragraphs_count' => count($contentAnalysis['paragraphs'] ?? [])
                            ]);
                        }
                    } else {
                        Log::error('❌ Python microservice returned error', ['status' => $response->status()]);
                    }
                } catch (\Exception $pythonError) {
                    Log::error('❌ Python fallback failed', ['message' => $pythonError->getMessage()]);
                }
            }

            // 🗂️ Préparer les données pour la sauvegarde - CORRIGÉ
            $headingsStructure = [];
            $htmlContent = '';

            // 🔥 CORRECTION CRITIQUE : Déterminer la source des données de contenu
            $finalContentAnalysis = [];
            $finalMainContent = null;
            $finalReadability = null;

            if (!$scraperFailed && isset($scraperResult['content_analysis'])) {
                // ✅ Scraper Laravel a réussi - utiliser SES données
                $finalContentAnalysis = $scraperResult['content_analysis'];
                $finalMainContent = $scraperResult['main_content'] ?? null;
                $finalReadability = $scraperResult['readability_score'] ?? null;
                $htmlContent = $scraperResult['html'] ?? '';
                
                Log::info('📊 Using Laravel scraper content data', [
                    'paragraphs_count' => count($finalContentAnalysis['paragraphs'] ?? []),
                    'main_content_length' => strlen($finalMainContent ?? '')
                ]);
                
            } elseif (!empty($contentAnalysis)) {
                // ✅ Fallback Python a réussi - utiliser SES données
                $finalContentAnalysis = $contentAnalysis;
                $finalMainContent = $mainContent;
                $finalReadability = $readability;
                $htmlContent = $mainContent;
                
                Log::info('📊 Using Python fallback content data', [
                    'paragraphs_count' => count($finalContentAnalysis['paragraphs'] ?? []),
                    'main_content_length' => strlen($finalMainContent ?? '')
                ]);
            } else {
                // ❌ Aucune donnée de contenu disponible
                Log::warning('⚠️ No content data available from any source');
                $finalContentAnalysis = [
                    'paragraph_count' => 0,
                    'short_paragraphs' => 0,
                    'sample_paragraphs' => [],
                    'paragraphs' => [],
                    'duplicate_paragraphs' => []
                ];
            }

            // ⚡ Analyser la structure des headings UNIQUEMENT si nécessaire
            if (!empty($htmlContent) && strlen($htmlContent) < 500000) { // Éviter les pages trop grandes
                try {
                    libxml_use_internal_errors(true);
                    $dom = new \DOMDocument('1.0', 'UTF-8');
                    $html = mb_convert_encoding($htmlContent, 'HTML-ENTITIES', 'UTF-8');
                    
                    @$dom->loadHTML($html);
                    libxml_clear_errors();

                    foreach (['h1','h2','h3'] as $tag) { // ⚡ Seulement h1-h3 pour gagner du temps
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

            // 🌐 WHOIS avec timeout
            $whoisData = null;
            try {
                $whoisData = $whois->lookup($domain);
                Log::info('✅ WHOIS lookup successful', ['domain' => $domain]);
            } catch (\Exception $e) {
                Log::warning('WHOIS lookup failed', ['domain' => $domain, 'message' => $e->getMessage()]);
            }

            // ✅ PRÉPARATION DES DONNÉES AVEC CONVERSION JSON CORRECTE - CORRIGÉ
            $analysisData = [
                'project_id' => $project->id,
                'page_url' => $project->base_url,
                'page_title' => $scraperResult['title'] ?? 'Titre non disponible',
                'meta_description' => $scraperResult['meta_description'] ?? 'Description non disponible',
                'h1_tags' => $scraperFailed ? null : ($scraperResult['headings'][0]['text'] ?? null),
                
                // 🔥 CONVERSION JSON POUR TOUS LES TABLEAUX
                'headings' => $scraperFailed ? [] : ($scraperResult['headings'] ?? []),
                'headings_structure' => $headingsStructure,
                'images_data' => $scraperFailed ? [] : ($scraperResult['images'] ?? []),
                
                // 🔥 CORRECTION CRITIQUE : keywords doit être un tableau
                'keywords' => $scraperResult['keywords'] ?? [],
                
                'raw_html' => substr($htmlContent, 0, 100000), // ⚡ Limiter la taille
                'word_count' => $scraperResult['word_count'] ?? 0,
                'keyword_density' => $scraperResult['density'] ?? 0,
                'mobile_friendly' => ($scraperResult['mobile'] ?? false) ? 1 : 0,
                'score' => rand(60, 95),
                'recommendations' => 'To be generated',
                
                // 🔥 CORRECTION CRITIQUE : Utiliser les BONNES données de contenu
                'content_analysis' => $finalContentAnalysis,
                'main_content' => $finalMainContent ? substr($finalMainContent, 0, 50000) : null,
                'readability_score' => $finalReadability,
                'cloudflare_blocked' => $cloudflareBlocked,
                
                // 🔥 CONVERSION JSON pour technical_audit
                'technical_audit' => $scraperResult['technical_audit'] ?? [],
                
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
                
                // Initialiser les champs PageSpeed comme tableaux vides
                'pagespeed_score' => 0,
                'pagespeed_metrics' => [],
                'pagespeed_scores' => [],
                'pagespeed_desktop_score' => null,
                'pagespeed_desktop_metrics' => [],
                'pagespeed_desktop_audits' => [],
                'pagespeed_desktop_scores' => [],
                'pagespeed_desktop_formFactor' => null,
                'pagespeed_mobile_score' => null,
                'pagespeed_mobile_metrics' => [],
                'pagespeed_mobile_audits' => [],
                'pagespeed_mobile_scores' => [],
                'pagespeed_mobile_formFactor' => null,
            ];

            // 🔍 DEBUG FINAL AVANT SAUVEGARDE
            Log::info('🔍 FINAL DATA CHECK BEFORE SAVE', [
                'has_content_analysis' => !empty($finalContentAnalysis),
                'content_analysis_keys' => array_keys($finalContentAnalysis),
                'paragraphs_count' => count($finalContentAnalysis['paragraphs'] ?? []),
                'main_content_exists' => !empty($finalMainContent),
                'main_content_length' => strlen($finalMainContent ?? '')
            ]);

            // ✅ Sauvegarde initiale
            $seoAnalysis = SeoAnalysis::create($analysisData);

            if (!$seoAnalysis) {
                return redirect()->back()->withErrors(['error' => 'Failed to save SEO analysis.']);
            }

            // 🔍 VÉRIFICATION APRÈS SAUVEGARDE
            Log::info('✅ SEO analysis created', [
                'analysis_id' => $seoAnalysis->id,
                'saved_paragraphs_count' => count($seoAnalysis->content_analysis['paragraphs'] ?? []),
                'saved_main_content_length' => strlen($seoAnalysis->main_content ?? '')
            ]);

            // 🚀 PageSpeed Insights - Job asynchrone
            Log::info('🚀 Lancement du job PageSpeed');
            RunPageSpeedAudit::dispatch($seoAnalysis, $project->base_url)
                            ->onQueue('pagespeed')
                            ->delay(now()->addSeconds(5)); // ⏱️ Délai pour éviter la surcharge

            // 🔁 PageRank - Job asynchrone
            Log::info('🚀 Lancement du job PageRank');
            FetchPageRank::dispatch($seoAnalysis)
                        ->onQueue('pagerank')
                        ->delay(now()->addSeconds(10));

            // 🌐 OpenPageRank API (OPTIONNEL - peut être déplacé dans un job)
            try {
                $certPath = 'D:/xampp/php/extras/ssl/cacert.pem';
                $verifyOption = file_exists($certPath)
                    ? (app()->environment('local') ? false : $certPath)
                    : false;

                // ⏱️ Timeout court pour OpenPageRank
                $response = Http::timeout(15)
                                ->withHeaders([
                                    'API-OPR' => config('services.openpagerank.key'),
                                ])
                                ->withOptions([
                                    'verify' => $verifyOption,
                                ])
                                ->get('https://openpagerank.com/api/v1.0/getPageRank', [
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
                Log::warning('OpenPageRank API failed', ['message' => $e->getMessage()]);
                // ❌ NE PAS bloquer l'analyse si OpenPageRank échoue
            }

            // 🎯 Keywords (rapide)
            if (!$scraperFailed && isset($scraperResult['keywords']) && is_array($scraperResult['keywords'])) {
                $keywords = implode(',', array_slice(array_keys($scraperResult['keywords']), 0, 10)); // ⚡ Limiter à 10 keywords
                $project->update([
                    'target_keywords' => $keywords,
                ]);
                Log::info('✅ Keywords updated', ['keywords_count' => count(explode(',', $keywords))]);
            }

            Log::info('✅ Toutes les tâches lancées, redirection...');

            // ✅ Final redirect
            return redirect()->route('project.show', [
                'id' => $project->id,
                'analysis_id' => $seoAnalysis->id
            ])->with('success', 'SEO analysis completed successfully!');

        } catch (\Exception $e) {
            Log::error('Exception caught', [
                'message' => $e->getMessage(), 
                'trace' => $e->getTraceAsString()
            ]);
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

        // 🔄 Recharger l'analyse pour avoir les données fraîches
        $analysis = SeoAnalysis::find($analysis->id);

        // 🔍 DEBUG POUR VÉRIFIER LES DONNÉES DE CONTENU
        Log::info('📥 DONNÉES CONTENT ANALYSIS DANS SHOW()', [
            'analysis_id' => $analysis->id,
            'has_content_analysis' => !empty($analysis->content_analysis),
            'paragraphs_count' => count($analysis->content_analysis['paragraphs'] ?? []),
            'main_content_exists' => !empty($analysis->main_content),
            'main_content_length' => strlen($analysis->main_content ?? '')
        ]);

        Log::info('📥 DONNÉES PAGESPEED DANS SHOW()', [
            'analysis_id' => $analysis->id,
            'pagespeed_score' => $analysis->pagespeed_score,
            'updated_at' => $analysis->updated_at,
        ]);

        // ✅ Transmission à la vue
        return view('user.projects.show', [
            'project' => $project,
            'analysis' => $analysis,
            'scores' => $analysis->pagespeed_scores ?? [],
            'metrics' => $analysis->pagespeed_metrics ?? [],
            'auditFragments' => $analysis->pagespeed_audits ?? [],
        ]);
    }
}