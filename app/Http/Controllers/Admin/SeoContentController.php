<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\OllamaSeoService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\SeoGeneration;
use App\Models\SeoVariant;

class SeoContentController extends Controller
{
    /**
     * Génération de contenu SEO avec variantes multiples
     */
    public function generate(Request $request)
    {
        try {
            // Inputs
            $prompt = $request->input('prompt', 'Titre SEO et meta pour Voyage Madagascar');
            $lang   = $request->input('lang', 'fr');

            $service = new OllamaSeoService();

            // Prompt enrichi avec consigne stricte JSON pour 3 variantes
            $fullPrompt = "En {$lang}, génère 3 variantes de titre SEO (≤70 caractères) et meta-description (≤160 caractères). 
Réponds uniquement en JSON strict sous forme de tableau avec trois objets : 
[
  {\"title\": \"...\", \"meta\": \"...\"},
  {\"title\": \"...\", \"meta\": \"...\"},
  {\"title\": \"...\", \"meta\": \"...\"}
]
Sujet : {$prompt}";

            // Appel au service Ollama
            $content = $service->generateContent($fullPrompt);

            // Nettoyer le contenu (supprimer éventuels ```json ... ```)
            $clean = trim(preg_replace('/```(json)?|```/i', '', $content ?? ''));

            // Extraire uniquement le JSON si Ollama renvoie du texte autour
            $decoded = null;
            if ($clean) {
                // Capture un tableau JSON complet
                if (preg_match('/

\[[\s\S]*\]

/m', $clean, $matches)) {
                    $decoded = json_decode($matches[0], true);
                } else {
                    $decoded = json_decode($clean, true);
                }
            }

            // Vérifier si le JSON est valide et contient des variantes
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded) && count($decoded) > 0) {
                // Sauvegarde de la génération principale avec la première variante
                $first = $decoded[0];
                $generation = SeoGeneration::create([
                    'user_id' => Auth::id(),
                    'prompt'  => $prompt,
                    'lang'    => $lang,
                    'title'   => $first['title'] ?? null,
                    'meta'    => $first['meta'] ?? null,
                ]);

                // Sauvegarde des variantes
                foreach ($decoded as $variant) {
                    if (isset($variant['title'], $variant['meta'])) {
                        $generation->variants()->create([
                            'title' => $variant['title'],
                            'meta'  => $variant['meta'],
                        ]);
                    }
                }

                return response()->json([
                    'success'   => true,
                    'prompt'    => $prompt,
                    'lang'      => $lang,
                    'variants'  => $decoded,
                ]);
            }

            // Fallback : aucune variante valide
            $generation = SeoGeneration::create([
                'user_id' => Auth::id(),
                'prompt'  => $prompt,
                'lang'    => $lang,
                'title'   => '⚠️ Aucun titre généré',
                'meta'    => '⚠️ Aucune meta-description générée',
            ]);

            $generation->variants()->create([
                'title' => '⚠️ Aucun titre généré',
                'meta'  => '⚠️ Aucune meta-description générée',
            ]);

            return response()->json([
                'success'  => true,
                'prompt'   => $prompt,
                'lang'     => $lang,
                'variants' => [['title' => '⚠️ Aucun titre généré', 'meta' => '⚠️ Aucune meta-description générée']],
                'raw'      => $content,
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

    /**
     * Page du générateur SEO
     */
    public function page(Request $request)
    {
        return view('user.projects.seoGenerator', [
            'prefill'       => session('prefill', false),
            'prefillPrompt' => $request->query('prompt', ''), // valeur par défaut vide
            'prefillLang'   => $request->query('lang', 'en'),
        ]);
    }


    public function choose(SeoVariant $variant)
{
    try {
        // Mettre à jour la génération principale avec la variante choisie
        $variant->generation->update([
            'title' => $variant->title,
            'meta'  => $variant->meta,
        ]);

        return redirect()
            ->route('seo.history.index')
            ->with('success', 'Variante choisie et appliquée avec succès.');
    } catch (\Throwable $e) {
        Log::error('Erreur SeoContentController@choose', [
            'message' => $e->getMessage(),
            'trace'   => $e->getTraceAsString(),
        ]);

        return redirect()
            ->route('seo.history.index')
            ->with('error', 'Impossible d’appliquer la variante sélectionnée.');
    }
}

}
