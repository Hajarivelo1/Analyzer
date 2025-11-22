<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Models\Project;
use App\Models\SeoAnalysis;
use Illuminate\Pagination\LengthAwarePaginator;

class ProjectController extends Controller
{
    public function AllProjects()
    {
        $userId = Auth::id();
        $page = request('page', 1);
        
        // 🔥 CLÉ DE CACHE AVEC PAGINATION
        $cacheKey = "user_projects_data_{$userId}_page_{$page}";
        
        // 🔥 OPTIMISATION : Cache pendant 5 minutes seulement pour les tests
        $data = Cache::remember($cacheKey, 300, function () use ($userId) {
            \Log::info('🔄 Chargement FRESH des projets', ['user_id' => $userId]);
            
            // 🔥 CRITIQUE : Chargement OPTIMISÉ sans les données lourdes
            $projects = Project::with([
                'seoAnalyses' => function ($query) {
                    // ⚡ SEULEMENT les champs nécessaires, pas tout le contenu
                    $query->select('id', 'project_id', 'score', 'created_at')
                          ->latest()
                          ->limit(1); // ⚡ Seulement la dernière analyse
                }
            ])
            ->where('user_id', $userId)
            ->where('is_active', true)
            ->select('id', 'name', 'base_url', 'target_keywords', 'is_active', 'created_at', 'updated_at') // ⚡ Seulement les champs nécessaires
            ->latest()
            ->paginate(6);

            // 🔥 OPTIMISATION : Calculs séparés pour éviter de charger toutes les données
            $allProjects = Project::where('user_id', $userId)
                                ->select('id', 'is_active')
                                ->get();

            $totalProjects = $allProjects->count();
            $activeProjects = $allProjects->where('is_active', true)->count();
            $projectIds = $allProjects->pluck('id');

            // 🔥 OPTIMISATION : Count simple sans charger les relations
            $totalAnalyses = SeoAnalysis::whereIn('project_id', $projectIds)->count();
            
            // 🔥 OPTIMISATION : Score moyen avec requête optimisée
            $avgScore = SeoAnalysis::whereIn('project_id', $projectIds)
                ->select('project_id', 'score')
                ->whereIn('id', function($query) use ($projectIds) {
                    $query->selectRaw('MAX(id)')
                          ->from('seo_analyses')
                          ->whereIn('project_id', $projectIds)
                          ->groupBy('project_id');
                })
                ->avg('score') ?? 0;

            return compact(
                'projects', 
                'totalProjects', 
                'activeProjects', 
                'totalAnalyses', 
                'avgScore'
            );
        });

        \Log::info('📦 Données projets servies', [
            'from_cache' => Cache::has($cacheKey) ? '✅ OUI' : '❌ NON',
            'user_id' => $userId,
            'page' => $page
        ]);

        return view('admin.backend.projects.index', $data);
    }
    
    public function destroy($id)
    {
        $project = Project::where('user_id', Auth::id())->findOrFail($id);

        // Supprimer les analyses associées
        $project->seoAnalyses()->delete();
        $project->delete();

        // 🔥 NETTOYAGE DU CACHE
        $this->clearUserProjectsCache(Auth::id());
        
        return redirect()->back()->with('message', 'Project deleted successfully')->with('alert-type', 'success');
    }

    public function AddProject()
    {
        return view('admin.backend.projects.add');
    }

    public function StoreProject(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'base_url' => 'required|url|max:255',
                'description' => 'nullable|string',
                'target_keywords' => 'nullable|string',
                'is_active' => 'nullable|in:true,false,1,0,on,off',
                'analysis_frequency' => 'nullable|in:weekly,monthly,quarterly',
                'competitor_analysis' => 'nullable|in:basic,advanced,none'
            ]);

            if (!empty($validated['target_keywords'])) {
                $validated['target_keywords'] = trim($validated['target_keywords']);
            }

            Project::create([
                'user_id' => Auth::id(),
                'name' => $validated['name'],
                'base_url' => $validated['base_url'],
                'description' => $validated['description'] ?? null,
                'target_keywords' => $validated['target_keywords'] ?? null,
                'is_active' => $request->has('is_active'),
                'enable_monitoring' => $request->has('enable_monitoring'),
                'analysis_frequency' => $validated['analysis_frequency'] ?? 'monthly',
                'competitor_analysis' => $validated['competitor_analysis'] ?? 'basic',
            ]);

            // 🔥 NETTOYAGE DU CACHE
            $this->clearUserProjectsCache(Auth::id());
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Project creation failed: ' . $e->getMessage()])
                ->withInput();
        }

        $notification = [
            'message' => 'Project created Successfully',
            'alert-type' => 'success'
        ];
        
        return redirect()->route('all.projects')->with($notification);
    }

    public function getProjectsJson()
    {
        $userId = Auth::id();
        $cacheKey = "user_projects_json_{$userId}";
        
        // 🔥 OPTIMISATION : Données légères pour le JSON
        $projects = Cache::remember($cacheKey, 300, function () use ($userId) {
            return Project::where('user_id', $userId)
                ->where('is_active', true)
                ->select('id', 'name', 'base_url') // ⚡ Seulement les champs nécessaires
                ->withCount('seoAnalyses')
                ->latest()
                ->get()
                ->map(function ($project) {
                    return [
                        'id' => $project->id,
                        'name' => $project->name,
                        'base_url' => $project->base_url,
                        'seo_analyses_count' => $project->seo_analyses_count,
                    ];
                });
        });

        return response()->json($projects);
    }

    /**
     * 🔥 NETTOYAGE DU CACHE
     */
    private function clearUserProjectsCache($userId): void
    {
        try {
            // Nettoyer seulement les 5 premières pages
            for ($page = 1; $page <= 5; $page++) {
                $cacheKey = "user_projects_data_{$userId}_page_{$page}";
                Cache::forget($cacheKey);
            }
            
            $jsonCacheKey = "user_projects_json_{$userId}";
            Cache::forget($jsonCacheKey);
            
        } catch (\Exception $e) {
            \Log::error('Erreur nettoyage cache', ['user_id' => $userId, 'error' => $e->getMessage()]);
        }
    }

    public function refreshProjectsCache()
    {
        $userId = Auth::id();
        $this->clearUserProjectsCache($userId);
        
        return redirect()->route('all.projects')
            ->with('message', 'Projects cache refreshed successfully!')
            ->with('alert-type', 'success');
    }
}