<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OllamaSeoService
{
    public function generateContent(string $prompt): ?string
    {
        try {
            $endpoint = config('ia.ollama.endpoint', 'http://localhost:11434/api/chat');
            $key      = config('ia.ollama.key', '');
            $model    = config('ia.ollama.model', 'gpt-oss:120b-cloud');
            $timeout  = (int) config('ia.ollama.timeout', 30);

            $payload = [
                'model'    => $model,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
                'stream'   => false,
            ];

            $request = Http::timeout($timeout)->asJson();
            if (!empty($key)) {
                $request = $request->withToken($key);
            }

            $response = $request->post($endpoint, $payload);

            if ($response->failed()) {
                Log::error('Ollama SEO Service error', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return null;
            }

            $json = $response->json();

            // 🔎 Log complet pour voir la structure réelle
            Log::info('Ollama raw response', $json);

            // ✅ Extraction robuste du contenu
            if (isset($json['message']['content'])) {
                return $json['message']['content'];
            } elseif (isset($json['choices'][0]['message']['content'])) {
                return $json['choices'][0]['message']['content'];
            } elseif (isset($json['content'])) {
                return $json['content'];
            }

            return null;
        } catch (\Throwable $e) {
            Log::error('Ollama SEO Service exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * 🔥 PARSING ROBUSTE AMÉLIORÉ - Support multiple formats
     */
    public function parseResponse(string $content): array
    {
        Log::debug('🔍 [OLLAMA-PARSE] Début parsing', ['content_length' => strlen($content)]);
        
        $text = preg_replace("/\r\n|\r/", "\n", $content);
        $text = trim($text);

        // 🔥 EXTRACTION ROBUSTE DU SCORE - MULTIPLES MÉTHODES
        $score = $this->extractScoreRobust($text);
        
        // 🔥 EXTRACTION DES PROBLÈMES - MULTIPLES MÉTHODES
        $issuesList = $this->extractIssuesRobust($text);
        
        // 🔥 EXTRACTION DES PRIORITÉS - MULTIPLES MÉTHODES
        $priorities = $this->extractPrioritiesRobust($text);
        
        // 🔥 EXTRACTION DE LA CHECKLIST - MULTIPLES MÉTHODES
        $checklist = $this->extractChecklistRobust($text);

        Log::debug('🔍 [OLLAMA-PARSE-RESULT] Résultat final', [
            'score' => $score,
            'issues_count' => count($issuesList),
            'priorities_count' => count($priorities),
            'checklist_count' => count($checklist)
        ]);

        return [
            'score'      => $score,
            'issues'     => $issuesList,
            'priorities' => $priorities,
            'checklist'  => $checklist,
            'raw'        => $content,
        ];
    }

    /**
     * 🔥 EXTRACTION ROBUSTE DU SCORE
     */
    private function extractScoreRobust(string $text): ?int
    {
        $score = null;

        // Méthode 1: Pattern "Total | **XX/100**" (ancien format)
        if (preg_match('/\*\*Total\*\*.*?\*\*(\d{1,3})\/100\*\*/u', $text, $m)) {
            $score = (int)$m[1];
            Log::debug('✅ [SCORE] Méthode 1 - Total table', ['score' => $score]);
            return $score;
        }

        // Méthode 2: Pattern "Score SEO GLOBAL" avec nombre
        if (preg_match('/Score SEO GLOBAL.*?(\d{1,3})\s*\/\s*100/s', $text, $m)) {
            $score = (int)$m[1];
            Log::debug('✅ [SCORE] Méthode 2 - Score SEO GLOBAL', ['score' => $score]);
            return $score;
        }

        // Méthode 3: Points obtenus dans les tableaux
        if (preg_match('/Points obtenus.*?\|\s*\*\*(\d+)\*\*\s*\|/s', $text, $m)) {
            $score = (int)$m[1];
            Log::debug('✅ [SCORE] Méthode 3 - Points obtenus', ['score' => $score]);
            return $score;
        }

        // Méthode 4: Recherche de nombres entre 0-100
        if (preg_match('/(?:score|note|points?)[\s:]*(\d{1,3})(?:\/100)?/i', $text, $m)) {
            $score = (int)$m[1];
            if ($score <= 100) {
                Log::debug('✅ [SCORE] Méthode 4 - Pattern général', ['score' => $score]);
                return $score;
            }
        }

        // Méthode 5: Total des points dans les tableaux
        if (preg_match_all('/\|\s*\*\*(\d{1,2})\*\*\s*\|/s', $text, $matches)) {
            $points = array_map('intval', $matches[1]);
            $total = array_sum($points);
            if ($total <= 100 && $total > 0) {
                Log::debug('✅ [SCORE] Méthode 5 - Somme tableaux', ['score' => $total]);
                return $total;
            }
        }

        Log::debug('❌ [SCORE] Aucun score détecté');
        return null;
    }

    /**
     * 🔥 EXTRACTION ROBUSTE DES PROBLÈMES
     */
    private function extractIssuesRobust(string $text): array
    {
        $issues = [];

        // Méthode 1: Tableau des problèmes (ancien format)
        if (preg_match_all('/^\|\s*\d+\s*\|\s*\*\*(.*?)\*\*\s*\|/um', $text, $m)) {
            $issues = array_merge($issues, array_map('trim', $m[1]));
            Log::debug('✅ [ISSUES] Méthode 1 - Tableau', ['count' => count($m[1])]);
        }

        // Méthode 2: Section "PROBLÈMES IDENTIFIÉS"
        if (preg_match('/##?\s*2[^\n]*PROBLÈMES IDENTIFIÉS(.*?)(?:##|\Z)/ius', $text, $section)) {
            $sectionText = $section[1];
            // Extraire les éléments de liste
            if (preg_match_all('/[-•*]\s*(.+?)(?=\n[-•*]|\n##|\n$)/s', $sectionText, $m)) {
                $issues = array_merge($issues, array_map('trim', $m[1]));
                Log::debug('✅ [ISSUES] Méthode 2 - Section problèmes', ['count' => count($m[1])]);
            }
        }

        // Méthode 3: Pattern général des listes
        if (preg_match_all('/\n[-•*]\s*([^\n]+)/', $text, $m)) {
            $listItems = array_map('trim', $m[1]);
            // Filtrer les éléments qui ressemblent à des problèmes SEO
            $seoIssues = array_filter($listItems, function($item) {
                return $this->isSeoIssue($item);
            });
            $issues = array_merge($issues, $seoIssues);
            Log::debug('✅ [ISSUES] Méthode 3 - Listes générales', ['count' => count($seoIssues)]);
        }

        // Dédupliquer et filtrer
        $issues = array_unique(array_filter($issues, function($issue) {
            return strlen($issue) > 5 && $this->isSeoIssue($issue);
        }));

        Log::debug('🔍 [ISSUES] Total problèmes', ['count' => count($issues)]);
        return array_values($issues);
    }

    /**
     * 🔥 EXTRACTION ROBUSTE DES PRIORITÉS
     */
    private function extractPrioritiesRobust(string $text): array
    {
        $priorities = [];

        // Méthode 1: Tableau des priorités avec effort
        if (preg_match_all('/\|\s*\*\*(.*?)\*\*\s*\|\s*(.*?)\s*\|\s*(Urgent|Moyen|Long terme)/u', $text, $pm, PREG_SET_ORDER)) {
            foreach ($pm as $row) {
                $priorities[] = [
                    'item'   => trim($row[1]),
                    'detail' => trim($row[2]),
                    'effort' => trim($row[3]),
                ];
            }
            Log::debug('✅ [PRIORITIES] Méthode 1 - Tableau effort', ['count' => count($pm)]);
        }

        // Méthode 2: Section "RECOMMANDATIONS PRIORITAIRES"
        if (preg_match('/##?\s*3[^\n]*RECOMMANDATIONS PRIORITAIRES(.*?)(?:##|\Z)/ius', $text, $section)) {
            $sectionText = $section[1];
            if (preg_match_all('/[-•*]\s*(.+?)(?=\n[-•*]|\n##|\n$)/s', $sectionText, $m)) {
                foreach ($m[1] as $item) {
                    $priorities[] = [
                        'item'   => trim($item),
                        'detail' => '',
                        'effort' => $this->detectEffortLevel($item),
                    ];
                }
                Log::debug('✅ [PRIORITIES] Méthode 2 - Section recommandations', ['count' => count($m[1])]);
            }
        }

        // Méthode 3: Fallback - utiliser les premiers problèmes comme priorités
        if (empty($priorities)) {
            $issues = $this->extractIssuesRobust($text);
            $topIssues = array_slice($issues, 0, 5);
            foreach ($topIssues as $issue) {
                $priorities[] = [
                    'item'   => $issue,
                    'detail' => 'Problème identifié nécessitant une action',
                    'effort' => 'Moyen',
                ];
            }
            Log::debug('✅ [PRIORITIES] Méthode 3 - Fallback issues', ['count' => count($topIssues)]);
        }

        return $priorities;
    }

    /**
     * 🔥 EXTRACTION ROBUSTE DE LA CHECKLIST
     */
    private function extractChecklistRobust(string $text): array
    {
        $checklist = [];

        // Méthode 1: Section "CHECKLIST ACTIONNABLE"
        if (preg_match('/##?\s*4[^\n]*CHECKLIST ACTIONNABLE(.*?)(?:##|\Z)/ius', $text, $section)) {
            $sectionText = $section[1];
            if (preg_match_all('/[-•*]\s*(.+?)(?=\n[-•*]|\n##|\n$)/s', $sectionText, $m)) {
                $checklist = array_map('trim', $m[1]);
                Log::debug('✅ [CHECKLIST] Méthode 1 - Section checklist', ['count' => count($m[1])]);
            }
        }

        // Méthode 2: Pattern général des listes numérotées
        if (preg_match_all('/\d+\.\s*([^\n]+)/', $text, $m)) {
            $numberedItems = array_map('trim', $m[1]);
            $checklist = array_merge($checklist, $numberedItems);
            Log::debug('✅ [CHECKLIST] Méthode 2 - Listes numérotées', ['count' => count($numberedItems)]);
        }

        // Méthode 3: Fallback - créer une checklist basique
        if (empty($checklist)) {
            $checklist = [
                "Vérifier et optimiser la balise title",
                "Optimiser la meta description", 
                "Structurer les balises H1-H6",
                "Ajouter les attributs alt aux images",
                "Vérifier la vitesse de chargement",
                "Optimiser pour mobile",
                "Ajouter les balises Open Graph",
                "Vérifier les liens internes et externes"
            ];
            Log::debug('✅ [CHECKLIST] Méthode 3 - Checklist par défaut');
        }

        // Filtrer les éléments trop courts
        $checklist = array_filter($checklist, function($item) {
            return strlen(trim($item)) > 10;
        });

        return array_values(array_slice($checklist, 0, 10)); // Limiter à 10 éléments
    }

    /**
     * 🔥 DÉTECTION SI UN TEXTE EST UN PROBLÈME SEO
     */
    private function isSeoIssue(string $text): bool
    {
        $text = strtolower($text);
        $seoKeywords = [
            'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'title', 'meta', 'description', 'alt',
            'image', 'lien', 'link', 'url', 'canonical', 'sitemap', 'robot', 'viewport',
            'mobile', 'responsive', 'vitesse', 'speed', 'performance', 'charge', 'load',
            'https', 'ssl', 'secure', 'structure', 'heading', 'balise', 'tag', 'duplicate',
            'content', 'contenu', 'keyword', 'mot-clé', 'densité', 'density', 'lisibilité',
            'readability', 'og:', 'open graph', 'twitter', 'schema', 'structured', 'data'
        ];

        foreach ($seoKeywords as $keyword) {
            if (str_contains($text, $keyword)) {
                return true;
            }
        }

        return false;
    }

    /**
     * 🔥 DÉTECTION DU NIVEAU D'EFFORT
     */
    private function detectEffortLevel(string $text): string
    {
        $text = strtolower($text);
        
        if (str_contains($text, ['urgent', 'critique', 'important', 'prioritaire', 'immédiat'])) {
            return 'Urgent';
        } elseif (str_contains($text, ['long', 'terme', 'futur', 'planification'])) {
            return 'Long terme';
        } else {
            return 'Moyen';
        }
    }
}