<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\OllamaSeoService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\SeoGeneration;

class SeoContentController extends Controller
{
    public function generate(Request $request)
    {
        try {
            // Inputs
            $prompt = $request->input('prompt', 'Titre SEO et meta pour Voyage Madagascar');
            $lang   = $request->input('lang', 'fr');

            $service = new OllamaSeoService();

            // Prompt enrichi avec consigne stricte JSON
            $fullPrompt = "En {$lang}, génère un titre SEO (≤70 caractères) et une meta-description (≤160 caractères). 
Réponds uniquement en JSON strict avec deux champs : 
{\"title\": \"...\", \"meta\": \"...\"}. 
Sujet : {$prompt}";

            // Appel au service Ollama
            $content = $service->generateContent($fullPrompt);

            // Extraire uniquement le JSON si Ollama renvoie du texte autour
            $decoded = null;
            if ($content) {
                if (preg_match('/\{.*\}/s', $content, $matches)) {
                    $decoded = json_decode($matches[0], true);
                } else {
                    $decoded = json_decode($content, true);
                }
            }

            // Vérifier si le JSON est valide
            if (json_last_error() === JSON_ERROR_NONE && isset($decoded['title'], $decoded['meta'])) {
                // Sauvegarde en base
                SeoGeneration::create([
                    'user_id' => Auth::id(),
                    'prompt'  => $prompt,
                    'lang'    => $lang,
                    'title'   => $decoded['title'],
                    'meta'    => $decoded['meta'],
                ]);

                return response()->json([
                    'success' => true,
                    'prompt'  => $prompt,
                    'lang'    => $lang,
                    'title'   => $decoded['title'],
                    'meta'    => $decoded['meta'],
                ]);
            }

            // Fallback : renvoyer des champs vides mais exploitables
            SeoGeneration::create([
                'user_id' => Auth::id(),
                'prompt'  => $prompt,
                'lang'    => $lang,
                'title'   => '⚠️ Aucun titre généré',
                'meta'    => '⚠️ Aucune meta-description générée',
            ]);

            return response()->json([
                'success' => true,
                'prompt'  => $prompt,
                'lang'    => $lang,
                'title'   => '⚠️ Aucun titre généré',
                'meta'    => '⚠️ Aucune meta-description générée',
                'raw'     => $content,
            ]);

        } catch (\Throwable $e) {
            Log::error('Erreur SeoContentController@generate', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error'   => 'Une erreur interne est survenue',
                'details' => $e->getMessage(),
            ], 500);
        }
    }



    public function page(Request $request)
{
    return view('user.projects.seoGenerator', [
        'prefill'       => session('prefill', false),
        'prefillPrompt' => $request->query('prompt', ''), // ✅ valeur par défaut vide
        'prefillLang'   => $request->query('lang', 'en'),
    ]);
}


}
