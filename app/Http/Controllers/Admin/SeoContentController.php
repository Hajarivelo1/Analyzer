<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\OllamaSeoService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\SeoGeneration;
use App\Models\SeoVariant;
use App\Models\Project;

class SeoContentController extends Controller
{
    protected $ollamaService;

    public function __construct(OllamaSeoService $ollamaService)
    {
        $this->ollamaService = $ollamaService;
    }

    /**
     * Page du générateur SEO avec sélection de projet
     */
    public function page(Request $request)
    {
        $user = Auth::user();
        
        // Récupérer les projets de l'utilisateur
        $projects = Project::where('user_id', $user->id)->get();
        
        return view('user.projects.seoGenerator', [
            'projects' => $projects,
            'prefill' => session('prefill', false),
            'prefillPrompt' => $request->query('prompt', ''),
            'prefillLang' => $request->query('lang', 'fr'),
            'prefillProjectId' => $request->query('project_id', ''),
        ]);
    }

    /**
     * Génération de contenu SEO avec variantes multiples LIÉ À UN PROJET
     */
    public function generate(Request $request)
    {
        try {
            // Validation des inputs
            $validated = $request->validate([
                'project_id' => 'required|exists:projects,id',
                'prompt' => 'required|string|max:1000',
                'lang' => 'required|string|size:2',
            ]);

            $projectId = $validated['project_id'];
            $prompt = $validated['prompt'];
            $lang = $validated['lang'];

            // Vérifier que le projet appartient à l'utilisateur
            $project = Project::where('id', $projectId)
                            ->where('user_id', Auth::id())
                            ->firstOrFail();

            // Construire le prompt contextuel avec les infos du projet
            $contextualPrompt = $this->buildContextualPrompt($prompt, $project, $lang);

            // Appel au service Ollama
            $content = $this->ollamaService->generateContent($contextualPrompt);

            // Ajouter un check supplémentaire pour le timeout
        if ($content === null) {
            throw new \Exception('The AI service took too long to respond.');
        }

            // Nettoyer le contenu (supprimer éventuels ```json ... ```)
            $clean = trim(preg_replace('/```(json)?|```/i', '', $content ?? ''));

            // Extraire uniquement le JSON si Ollama renvoie du texte autour
            // Par cette version plus robuste :
$decoded = null;
if ($clean) {
    // Essayer plusieurs méthodes d'extraction JSON
    $jsonPatterns = [
        '/\[[\s\S]*\]/m', // Tableau complet
        '/\{[\s\S]*\}/m', // Objet unique
    ];
    
    foreach ($jsonPatterns as $pattern) {
        if (preg_match($pattern, $clean, $matches)) {
            $decoded = json_decode($matches[0], true);
            if (json_last_error() === JSON_ERROR_NONE && $decoded !== null) {
                break;
            }
        }
    }
    
    // Si aucune pattern ne marche, essayer directement
    if ($decoded === null) {
        $decoded = json_decode($clean, true);
    }
}

            // Vérifier si le JSON est valide et contient des variantes
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded) && count($decoded) > 0) {
                // Sauvegarde de la génération principale avec la première variante LIÉE AU PROJET
                $first = $decoded[0];
                $generation = SeoGeneration::create([
                    'user_id' => Auth::id(),
                    'project_id' => $projectId, // ⬅️ LIEN CRUCIAL AVEC LE PROJET
                    'prompt' => $contextualPrompt, // On sauvegarde le prompt contextuel
                    'original_prompt' => $prompt, // Et le prompt original de l'utilisateur
                    'lang' => $lang,
                    'title' => $first['title'] ?? null,
                    'meta' => $first['meta'] ?? null,
                ]);

                // Sauvegarde des variantes
                foreach ($decoded as $index => $variant) {
                    if (isset($variant['title'], $variant['meta'])) {
                        $generation->variants()->create([
                            'title' => $variant['title'],
                            'meta' => $variant['meta'],
                            'variant_order' => $index, // Pour garder l'ordre
                        ]);
                    }
                }

                return response()->json([
                    'success' => true,
                    'project' => [
                        'id' => $project->id,
                        'name' => $project->name,
                        'url' => $project->base_url,
                    ],
                    'prompt' => $prompt,
                    'lang' => $lang,
                    'variants' => $decoded,
                    'generation_id' => $generation->id,
                ]);

            }

            // Fallback : aucune variante valide - création quand même avec lien projet
            $generation = SeoGeneration::create([
                'user_id' => Auth::id(),
                'project_id' => $projectId, // ⬅️ Même en fallback, on lie au projet
                'prompt' => $contextualPrompt,
                'original_prompt' => $prompt,
                'lang' => $lang,
                'title' => '⚠️ Aucun titre généré',
                'meta' => '⚠️ Aucune meta-description générée',
            ]);

            $generation->variants()->create([
                'title' => '⚠️ Aucun titre généré',
                'meta' => '⚠️ Aucune meta-description générée',
                'variant_order' => 0,
            ]);

            return response()->json([
                'success' => true,
                'project' => [
                    'id' => $project->id,
                    'name' => $project->name,
                    'url' => $project->base_url,
                ],
                'prompt' => $prompt,
                'lang' => $lang,
                'variants' => [['title' => '⚠️ Aucun titre généré', 'meta' => '⚠️ Aucune meta-description générée']],
                'generation_id' => $generation->id,
                'raw' => $content,
                'warning' => 'Aucune variante valide générée, contenu de fallback créé',
            ]);

        } catch (\Throwable $e) {
            Log::error('Erreur SeoContentController@generate', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'project_id' => $request->input('project_id'),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Une erreur interne est survenue',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Construit un prompt contextuel avec les infos du projet
     */
    private function buildContextualPrompt(string $userPrompt, Project $project, string $lang): string
{
    $languageNames = [
        'fr' => 'français',
        'en' => 'anglais', 
        'es' => 'espagnol',
        'de' => 'allemand',
        'it' => 'italien',
    ];

    $languageName = $languageNames[$lang] ?? $lang;

    // Version plus lisible et maintenable
    $contextLines = [
        "CONTEXTE DU PROJET:",
        "- Nom: {$project->name}",
        "- URL: {$project->base_url}",
        "- Description: " . ($project->description ?? 'Non spécifiée'),
        "- Mots-clés cibles: " . ($project->target_keywords ?? 'Non spécifiés'),
        "",
        "DEMANDE SPÉCIFIQUE:",
        $userPrompt,
        "",
        "FORMAT DE RÉPONSE STRICTE:",
        "Réponds UNIQUEMENT en JSON valide sous forme de tableau avec exactement 3 objets :",
        '[',
        '  {"title": "Titre variante 1 optimisé SEO", "meta": "Meta description variante 1 engageante"},',
        '  {"title": "Titre variante 2 optimisé SEO", "meta": "Meta description variante 2 engageante"},',
        '  {"title": "Titre variante 3 optimisé SEO", "meta": "Meta description variante 3 engageante"}',
        ']',
        "",
        "IMPORTANT:",
        "- Titres: 50-70 caractères, accrocheurs, incluent les mots-clés principaux",
        "- Meta descriptions: 120-160 caractères, incitatives, avec appel à l'action", 
        "- Chaque variante doit être unique et adaptée au contexte du projet"
    ];

    $contextualPrompt = "En {$languageName}, génère 3 variantes de titre SEO (50-70 caractères) et meta-description (120-160 caractères) pour le projet suivant :\n\n";
    $contextualPrompt .= implode("\n", $contextLines);

    return $contextualPrompt;
}

    /**
     * Choisir une variante spécifique
     */
    public function choose(SeoVariant $variant)
    {
        try {
            // Vérifier que l'utilisateur peut accéder à cette variante
            if ($variant->generation->user_id !== Auth::id()) {
                abort(403);
            }

            // Mettre à jour la génération principale avec la variante choisie
            $variant->generation->update([
                'title' => $variant->title,
                'meta' => $variant->meta,
            ]);

            // Marquer cette variante comme sélectionnée
            $variant->generation->variants()->update(['is_selected' => false]);
            $variant->update(['is_selected' => true]);

            return redirect()
                ->route('seo.history.index')
                ->with('success', 'Variante choisie et appliquée avec succès.');

        } catch (\Throwable $e) {
            Log::error('Erreur SeoContentController@choose', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'variant_id' => $variant->id,
                'user_id' => Auth::id(),
            ]);

            return redirect()
                ->route('seo.history.index')
                ->with('error', 'Impossible d\'appliquer la variante sélectionnée.');
        }
    }

    /**
     * Récupérer l'historique des générations pour un projet spécifique (API)
     */
    public function getProjectGenerations($projectId)
    {
        try {
            // Vérifier que le projet appartient à l'utilisateur
            $project = Project::where('id', $projectId)
                            ->where('user_id', Auth::id())
                            ->firstOrFail();

            $generations = SeoGeneration::where('project_id', $projectId)
                ->with(['variants' => function($query) {
                    $query->orderBy('variant_order');
                }])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($generation) {
                    return [
                        'id' => $generation->id,
                        'prompt' => $generation->original_prompt ?? $generation->prompt,
                        'lang' => $generation->lang,
                        'title' => $generation->title,
                        'meta' => $generation->meta,
                        'created_at' => $generation->created_at->format('d/m/Y H:i'),
                        'variants' => $generation->variants->map(function($variant) {
                            return [
                                'id' => $variant->id,
                                'title' => $variant->title,
                                'meta' => $variant->meta,
                                'is_selected' => $variant->is_selected,
                                'variant_order' => $variant->variant_order,
                            ];
                        }),
                        'stats' => $generation->stats,
                    ];
                });

            return response()->json([
                'success' => true,
                'project' => [
                    'id' => $project->id,
                    'name' => $project->name,
                    'url' => $project->base_url,
                ],
                'generations' => $generations,
                'total' => $generations->count(),
            ]);

        } catch (\Throwable $e) {
            Log::error('Erreur SeoContentController@getProjectGenerations', [
                'message' => $e->getMessage(),
                'project_id' => $projectId,
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Projet non trouvé ou accès refusé',
            ], 404);
        }
    }
}