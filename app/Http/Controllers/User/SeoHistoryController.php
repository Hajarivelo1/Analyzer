<?php
// App\Http\Controllers\User\SeoHistoryController.php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\SeoGeneration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SeoHistoryController extends Controller
{
    // Liste paginée des générations avec filtre par projet
    public function index(Request $request)
{
    $query = SeoGeneration::where('user_id', Auth::id())
        ->with(['project', 'variants' => function($query) {
            $query->orderBy('variant_order');
        }])
        ->latest();

    // Filtre par projet si spécifié
    if ($request->has('project_id') && $request->project_id) {
        $query->where('project_id', $request->project_id);
    }

    $items = $query->paginate(10)
        ->appends($request->except('page'));

    // Charger les projets AVEC le compte des générations pour chaque projet
    $projects = Project::where('user_id', Auth::id())
        ->withCount(['seoGenerations' => function($query) {
            $query->where('user_id', Auth::id());
        }])
        ->orderBy('name')
        ->get();

    // Statistiques pour l'interface
    $stats = [
        'total_generations' => SeoGeneration::where('user_id', Auth::id())->count(),
        'total_projects' => $projects->count(),
        'filtered_count' => $items->total(),
    ];

    return view('user.seo.history', compact('items', 'projects', 'stats'));
}

    // Réutiliser une génération passée
    public function reuse(SeoGeneration $generation)
    {
        if ($generation->user_id !== Auth::id()) {
            abort(403);
        }

        return redirect()->route('user.projects.seo', [
            'project_id' => $generation->project_id, // ⬅️ Inclure le project_id
            'prompt' => $generation->prompt,
            'lang' => $generation->lang,
        ])->with('prefill', true);
    }

    public function destroy(SeoGeneration $generation)
    {
        if ($generation->user_id !== Auth::id()) {
            abort(403);
        }

        $generation->delete();

        return redirect()->route('seo.history.index')
            ->with('success', 'Génération supprimée avec succès.');
    }
}