<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\OllamaSeoService;
use Illuminate\Support\Facades\Log;

class SeoContentController extends Controller
{
    public function generate(Request $request)
    {
        try {
            // Récupération des inputs
            $prompt = $request->input('prompt', 'Titre SEO et meta pour Voyage Madagascar');
            $lang   = $request->input('lang', 'fr');

            // Instanciation du service
            $service = new OllamaSeoService();

            // Construction du prompt enrichi
            $fullPrompt = "En {$lang}, " . $prompt;

            // Appel au service Ollama
            $content = $service->generateContent($fullPrompt);

            // Réponse JSON
            return response()->json([
                'success' => true,
                'prompt'  => $prompt,
                'lang'    => $lang,
                'content' => $content ?? '⚠️ Aucune réponse générée par Ollama',
            ]);

        } catch (\Throwable $e) {
            // Log de l’erreur pour débogage
            Log::error('Erreur SeoContentController@generate', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            // Réponse JSON d’erreur
            return response()->json([
                'success' => false,
                'error'   => 'Une erreur interne est survenue',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
}
