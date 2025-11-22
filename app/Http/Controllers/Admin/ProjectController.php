<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Project;
use App\Models\SeoAnalysis; // Ajouter cette ligne
use Illuminate\Pagination\LengthAwarePaginator;

class ProjectController extends Controller
{
    public function AllProjects()
{
    $projects = Project::with('seoAnalyses')
        ->where('user_id', Auth::id())
        ->where('is_active', true)
        ->latest()
        ->paginate(6);

    $allProjects = Project::where('user_id', Auth::id())->get();

    $totalProjects = $allProjects->count();
    $activeProjects = $allProjects->where('is_active', true)->count();
    $projectIds = $allProjects->pluck('id');

    $totalAnalyses = SeoAnalysis::whereIn('project_id', $projectIds)->count();
    // Score moyen basé sur la dernière analyse de chaque projet
    $avgScore = $allProjects->filter(function($project) {
        return $project->seoAnalyses->isNotEmpty();
    })
    ->avg(function($project) {
        return $project->seoAnalyses->first()->score;
    });

    return view('admin.backend.projects.index', compact(
        'projects', 
        'totalProjects', 
        'activeProjects', 
        'totalAnalyses', 
        'avgScore'
    ));
}

    
    // End Method 


    public function destroy($id)
{
    $project = Project::where('user_id', Auth::id())->findOrFail($id);

    // Supprimer les analyses associées
    $project->seoAnalyses()->delete();

    // Supprimer le projet
    $project->delete();

    return redirect()->back()->with('message', 'Project deleted successfully')->with('alert-type', 'success');

}
// End Method





// Dans ProjectController
public function AddProject()
{
    return view('admin.backend.projects.add');
}
//End Method

public function StoreProject(Request $request)
{

    

    try {
    $request->validate([
        'name' => 'required|string|max:255',
        'base_url' => 'required|url|max:255',
        'description' => 'nullable|string',
        'target_keywords' => 'nullable|string',
        'is_active' => 'nullable|in:true,false,1,0,on,off',
        'analysis_frequency' => 'nullable|in:weekly,monthly,quarterly',
        'competitor_analysis' => 'nullable|in:basic,advanced,none'
    ]);

    // Nettoyer les keywords
    if (!empty($validated['target_keywords'])) {
        $validated['target_keywords'] = trim($validated['target_keywords']);
    }

    Project::create([
        'user_id' => Auth::id(),
        'name' => $request->name,
        'base_url' => $request->base_url,
        'description' => $request->description,
        'target_keywords' => $request->target_keywords,
        'is_active' => $request->has('is_active'),
        'enable_monitoring' => $request->has('enable_monitoring'),
        'analysis_frequency' => $request->analysis_frequency ?? 'monthly',
        'competitor_analysis' => $request->competitor_analysis ?? 'basic',

    ]);
} catch (\Exception $e) {
    dd($e->getMessage());
}

$notification = array(
    'message' => 'Project created Successfully',
    'alert-type' => 'success'
);
   

return redirect()->route('all.projects')->with($notification);

}

// End Method


public function getProjectsJson()
{
    $projects = Project::where('user_id', Auth::id())
        ->where('is_active', true)
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

    return response()->json($projects);
}








}
