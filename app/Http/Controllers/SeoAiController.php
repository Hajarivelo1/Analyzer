<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Services\OllamaSeoService;
use Illuminate\Support\Facades\Log;

class SeoAiController extends Controller
{
    /**
     * Génère et sauvegarde un résumé IA pour le dernier run d’un projet
     */
    public function generateSummary(Project $project)
    {
        // Use the same relation as in run()/show()/loadProjectData
        $latestRun = $project->seoAnalyses()->latest('id')->first(); // adjust name if different

        if (!$latestRun) {
            return response()->json([
                'score'      => null,
                'issues'     => [],
                'priorities' => [],
                'checklist'  => [],
                'raw'        => 'Aucun run trouvé pour ce projet',
            ]);
        }

        $seo  = $latestRun->seo_metrics ? json_decode($latestRun->seo_metrics, true) : [];
        $perf = $latestRun->pagespeed_opportunities ? json_decode($latestRun->pagespeed_opportunities, true) : [];

        $prompt = view('ai.prompts.summary', compact('seo', 'perf', 'project'))->render();

        $responseRaw = app(OllamaSeoService::class)->generateContent($prompt);

        if (!$responseRaw) {
            Log::warning('OllamaSeoService returned null', [
                'project_id'  => $project->id,
                'analysis_id' => $latestRun->id,
            ]);
            $parsed = [
                'score'      => null,
                'issues'     => [],
                'priorities' => [],
                'checklist'  => [],
                'raw'        => 'Aucune réponse IA générée',
            ];
        } else {
            $parsed = app(OllamaSeoService::class)->parseResponse($responseRaw);
        }

        $latestRun->ai_summary = json_encode($parsed, JSON_UNESCAPED_UNICODE);
        $latestRun->save();

        return response()->json($parsed);
    }
}
