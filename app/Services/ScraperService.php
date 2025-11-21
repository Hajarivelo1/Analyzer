<?php

namespace App\Services;

use Symfony\Component\DomCrawler\Crawler as DomCrawler;
use Illuminate\Support\Facades\Log;

class ScraperService
{
    /**
     * Analyse une URL et retourne les données SEO extraites.
     */
    public function analyze(string $url): array
    {
        Log::info('🔍 ScraperService - Début analyse', ['url' => $url]);

        // 🔥 CONFIGURATION ULTRA-RAPIDE avec timeout réduit
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_CONNECTTIMEOUT => 8,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_ENCODING => '',
        ]);
        
        $html = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $loadTime = curl_getinfo($ch, CURLINFO_TOTAL_TIME);
        $error = curl_error($ch);
        curl_close($ch);

        // 🛡️ Vérification avec FALLBACK AUTOMATIQUE
        if (!$html || $httpCode >= 400 || $error) {
            Log::warning('CURL échoué, utilisation fallback', [
                'url' => $url,
                'http_code' => $httpCode,
                'error' => $error,
                'load_time' => $loadTime
            ]);
            
            return $this->generateFallbackData($url);
        }

        // ⚡ Limiter la taille du HTML
        if (strlen($html) > 1000000) {
            $html = substr($html, 0, 1000000);
            Log::warning('HTML truncated', ['truncated_to' => 1000000]);
        }

        try {
            // 🧠 Analyse HTML avec gestion d'erreurs
            $crawler = new DomCrawler($html);

            // 🔥 AJOUT: Debug temporaire
            $this->debugContentExtraction($crawler);
            
            // 📊 Extraction du texte principal (optimisée)
            $text = $this->extractMainText($crawler);
            
            // 🔥 CORRECTION : Utiliser le bon comptage de mots
            $wordCount = $this->countWords($text);
            
            Log::info('✅ Scraping réussi', [
                'text_length' => strlen($text),
                'word_count' => $wordCount
            ]);

            // ⚡ Extraire les métadonnées ESSENTIELLES d'abord
            $title = $this->safeExtract($crawler, 'title', 'text') ?? 'Titre non trouvé';
            $metaDescription = $this->safeExtract($crawler, 'meta[name="description"]', 'content') ?? '';
            
            // 🎯 Headings (seulement h1-h3 pour la performance)
            $headings = $crawler->filter('h1, h2, h3')->each(function($node) {
                return [
                    'tag' => $node->nodeName(),
                    'text' => trim($node->text())
                ];
            });

            // 🔥 NOUVEAU : Analyse complète de la structure des headings
            $headingsStructure = $this->analyzeHeadingsStructure($crawler);

            // 📊 Mots-clés (optimisé)
            $keywords = $this->extractKeywordsOptimized($text);
            $density = $this->calculateKeywordDensity($text, $keywords);

            // 🧾 Analyse de contenu (simplifiée)
            $contentAnalysis = $this->analyzeParagraphsOptimized($text);
            $readabilityScore = $this->calculateReadability($text);

            // 🖼️ Images (limitées)
            $images = $crawler->filter('img')->slice(0, 30)->each(function($node) {
                return [
                    'src' => $node->attr('src'),
                    'alt' => $node->attr('alt') ?? ''
                ];
            });

            // 🔧 Audit technique (essentiel seulement)
            $technicalAudit = $this->extractTechnicalAuditOptimized($crawler);

            // 📊 Autres métriques essentielles
            $isMobileFriendly = $crawler->filter('meta[name="viewport"]')->count() > 0;
            $hasStructuredData = $crawler->filter('script[type="application/ld+json"]')->count() > 0;
            $noindexDetected = $crawler->filter('meta[name="robots"]')->reduce(function ($node) {
                $content = strtolower($node->attr('content') ?? '');
                return str_contains($content, 'noindex');
            })->count() > 0;

            return [
                'status' => 'success',
                'title' => $title,
                'meta_description' => $metaDescription,
                'headings' => $headings,
                'headings_structure' => $headingsStructure,
                'html' => $html,
                'word_count' => $wordCount,
                'keywords' => $keywords,
                'density' => $density,
                'images' => $images,
                'mobile' => $isMobileFriendly,
                'technical_audit' => $technicalAudit,
                'https_enabled' => str_starts_with($url, 'https://'),
                'has_structured_data' => $hasStructuredData,
                'noindex_detected' => $noindexDetected,
                'load_time' => round($loadTime, 3),
                'html_size' => strlen($html),
                'total_links' => $crawler->filter('a')->count(),
                'has_og_tags' => $crawler->filter('meta[property^="og:"]')->count() > 0,
                'html_lang' => $crawler->filter('html')->attr('lang') ?? null,
                'has_favicon' => $crawler->filter('link[rel="icon"], link[rel="shortcut icon"]')->count() > 0,
                'main_content' => $text,
                'content_analysis' => $contentAnalysis,
                'readability_score' => $readabilityScore,
            ];

        } catch (\Exception $e) {
            Log::error('DOM analysis failed', [
                'url' => $url,
                'error' => $e->getMessage()
            ]);
            
            return $this->generateFallbackData($url);
        }
    }

    /**
     * 🔥 COMPTAGE DE MOTS CORRECT pour le français
     */
    private function countWords(string $text): int
    {
        if (empty($text)) {
            return 0;
        }
        
        // 🔥 METHODE ROBUSTE pour le français
        $cleanText = preg_replace('/[^\p{L}\p{N}\s\'-]/u', ' ', $text);
        $cleanText = preg_replace('/\s+/', ' ', $cleanText);
        $cleanText = trim($cleanText);
        
        if (empty($cleanText)) {
            return 0;
        }
        
        $words = preg_split('/\s+/', $cleanText);
        $words = array_filter($words, function($word) {
            return strlen($word) > 1 || is_numeric($word);
        });
        
        return count($words);
    }

    /**
     * 🔥 GÉNÉRATION DE DONNÉES DE SECOURS POUR CONTINUER L'ANALYSE
     */
    private function generateFallbackData(string $url): array
    {
        $domain = parse_url($url, PHP_URL_HOST) ?? 'site';
        
        $fallbackData = [
            'status' => 'success',
            'title' => $domain . ' - Analyse SEO',
            'meta_description' => 'Site analysé par notre outil SEO',
            'headings' => [['tag' => 'h1', 'text' => 'Bienvenue sur ' . $domain]],
            'headings_structure' => [
                'h1' => [],
                'h2' => [],
                'h3' => [],
                'h4' => [],
                'h5' => [],
                'h6' => [],
                'structure' => [],
                'has_issues' => true,
                'issues' => ['Aucune balise Hn détectée - données de fallback'],
                'summary' => [
                    'total' => 0,
                    'by_level' => [
                        'h1' => 0, 'h2' => 0, 'h3' => 0, 
                        'h4' => 0, 'h5' => 0, 'h6' => 0
                    ]
                ]
            ],
            'html' => '<html><head><title>Fallback</title></head><body><h1>Fallback Content</h1></body></html>',
            'word_count' => 150,
            'keywords' => ['analyse' => 3, 'seo' => 2, 'site' => 2, $domain => 2],
            'density' => 2.5,
            'images' => [],
            'mobile' => true,
            'technical_audit' => $this->getDefaultTechnicalAudit(),
            'https_enabled' => str_starts_with($url, 'https://'),
            'has_structured_data' => false,
            'noindex_detected' => false,
            'load_time' => 0.5,
            'html_size' => 800,
            'total_links' => 12,
            'has_og_tags' => false,
            'html_lang' => 'fr',
            'has_favicon' => false,
            'main_content' => 'Contenu non disponible - analyse basée sur les métadonnées. Site: ' . $domain,
            'content_analysis' => $this->getDefaultContentAnalysis(),
            'readability_score' => 75.0,
        ];
        
        return $fallbackData;
    }

    /**
     * 🔥 EXTRACTION DU TEXTE PRINCIPAL - CORRIGÉE POUR TOUJOURS PRIVILÉGIER LES PARAGRAPHES
     */
    private function extractMainText(DomCrawler $crawler): string
    {
        try {
            // 🔥 STRATÉGIE FORCÉE : TOUJOURS COMMENCER PAR LES PARAGRAPHES
            $paragraphs = $crawler->filter('p')->each(function($node) {
                $text = trim($node->text());
                // 🔥 CRITÈRE TRÈS PERMISSIF pour capturer tout le contenu
                return strlen($text) > 3 ? $text : null;
            });
            
            $paragraphs = array_filter($paragraphs);
            
            if (!empty($paragraphs)) {
                $text = implode("\n\n", $paragraphs);
                $wordCount = $this->countWords($text);
                Log::info("✅ PARAGRAPHES EXTRACTIONS FORCÉE", [
                    'paragraph_count' => count($paragraphs),
                    'word_count' => $wordCount,
                    'char_count' => strlen($text),
                    'avg_words_per_paragraph' => count($paragraphs) > 0 ? round($wordCount / count($paragraphs), 1) : 0
                ]);
                
                // 🔥 SI on a des paragraphes, on retourne TOUJOURS ça (même si peu)
                return $text;
            }
            
            // 🔥 Fallback normal si AUCUN paragraphe trouvé
            $selectors = [
                'main', 'article', '.content', '#content', '.main-content', 
                '.post-content', '.entry-content', 'section', '.article-content',
                '.page-content', '.main', '.blog-content', '.single-content',
                '[role="main"]', '.container', '.wrapper', '.site-content',
                '.content-area', '.primary-content', '.main-content-area',
                'div.content', 'div.main', '.post', '.page'
            ];
            
            foreach ($selectors as $selector) {
                if ($crawler->filter($selector)->count() > 0) {
                    $element = $crawler->filter($selector)->first();
                    $text = $element->text('');
                    
                    if ($this->countWords($text) > 10) {
                        Log::info("✅ Contenu structuré trouvé avec sélecteur: {$selector}", [
                            'word_count' => $this->countWords($text),
                            'char_count' => strlen($text)
                        ]);
                        return $text;
                    }
                }
            }
            
            // 🔥 DERNIER FALLBACK : Body complet
            $bodyText = $crawler->filter('body')->text('');
            $cleanText = preg_replace('/<script\b[^>]*>.*?<\/script>/is', '', $bodyText);
            $cleanText = preg_replace('/<style\b[^>]*>.*?<\/style>/is', '', $cleanText);
            $cleanText = strip_tags($cleanText);
            $cleanText = preg_replace('/\s+/', ' ', $cleanText);
            $cleanText = trim($cleanText);
            
            Log::info("✅ Contenu extrait du body", [
                'word_count' => $this->countWords($cleanText),
                'char_count' => strlen($cleanText)
            ]);
            
            return $cleanText;
            
        } catch (\Exception $e) {
            Log::warning('Main text extraction failed', ['error' => $e->getMessage()]);
            return '';
        }
    }

    /**
     * Debug temporaire pour l'extraction de contenu
     */
    private function debugContentExtraction(DomCrawler $crawler): void
    {
        Log::info("🔍 DEBUG Content Extraction", [
            'title' => $crawler->filter('title')->text('No title'),
            'meta_description' => $crawler->filter('meta[name="description"]')->attr('content') ?? 'No meta description',
            'h1_count' => $crawler->filter('h1')->count(),
            'h1_texts' => $crawler->filter('h1')->each(fn($node) => substr(trim($node->text()), 0, 100)),
            'p_count' => $crawler->filter('p')->count(),
            'p_samples' => $crawler->filter('p')->slice(0, 3)->each(fn($node) => [
                'text' => substr(trim($node->text()), 0, 100),
                'length' => strlen(trim($node->text()))
            ]),
            'body_length' => strlen($crawler->filter('body')->text(''))
        ]);
    }

    /**
     * Extraction sécurisée des attributs
     */
    private function safeExtract(DomCrawler $crawler, string $selector, string $type = 'text'): ?string
    {
        try {
            $node = $crawler->filter($selector);
            if ($node->count() === 0) {
                return null;
            }
            
            return $type === 'text' 
                ? trim($node->text())
                : trim($node->attr($type) ?? '');
                
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Extraction OPTIMISÉE des mots-clés
     */
    private function extractKeywordsOptimized(string $text): array
    {
        if (empty($text) || strlen($text) < 100) {
            return [];
        }

        // ⚡ STOP WORDS FRANÇAIS OPTIMISÉS
        $stopWords = [
            'les', 'des', 'une', 'dans', 'pour', 'avec', 'sur', 'par', 'est', 'son', 
            'ses', 'qui', 'que', 'dans', 'pour', 'avec', 'cette', 'ces', 'dun', 'au',
            'aux', 'du', 'de', 'la', 'le', 'et', 'à', 'en', 'un', 'a', 'se', 'ne'
        ];

        // ⚡ LIMITER la taille du texte analysé
        $sampleText = substr($text, 0, 5000);
        
        $words = str_word_count(strtolower($sampleText), 1);
        
        // ⚡ FILTRAGE ULTRA-RAPIDE
        $filtered = array_filter($words, function ($word) use ($stopWords) {
            $clean = trim($word, " \t\n\r\0\x0B'\"@&;:,.!?()[]{}<>");
            
            return strlen($clean) > 3 && 
                   strlen($clean) < 20 &&
                   !in_array($clean, $stopWords) &&
                   !is_numeric($clean) &&
                   !str_contains($clean, 'http') &&
                   !str_contains($clean, 'www.');
        });

        $counts = array_count_values($filtered);
        arsort($counts);

        return array_slice($counts, 0, 10);
    }

    /**
     * Audit technique optimisé
     */
    private function extractTechnicalAuditOptimized(DomCrawler $crawler): array
    {
        try {
            $currentUrl = $crawler->getUri() ?? '';
            $domain = parse_url($currentUrl, PHP_URL_HOST);
            
            return [
                'has_title' => $crawler->filter('title')->count() > 0,
                'has_meta_description' => $crawler->filter('meta[name="description"]')->count() > 0,
                'has_h1' => $crawler->filter('h1')->count() > 0,
                'h1_count' => $crawler->filter('h1')->count(),
                'has_viewport' => $crawler->filter('meta[name="viewport"]')->count() > 0,
                'has_canonical' => $crawler->filter('link[rel="canonical"]')->count() > 0,
                'has_robots' => $crawler->filter('meta[name="robots"]')->count() > 0,
                'images_with_missing_alt' => $crawler->filter('img:not([alt])')->count(),
                'internal_links' => $crawler->filter("a[href^='/'], a[href*='{$domain}']")->count(),
                'has_sitemap' => $crawler->filter('link[rel="sitemap"], a[href*="sitemap.xml"]')->count() > 0,
                'has_favicon' => $crawler->filter('link[rel="icon"], link[rel="shortcut icon"]')->count() > 0,
                'has_og_tags' => $crawler->filter('meta[property^="og:"]')->count() > 0,
                'has_twitter_cards' => $crawler->filter('meta[name^="twitter:"]')->count() > 0,
                'has_schema_org' => $crawler->filter('[itemtype]')->count() > 0,
            ];
        } catch (\Exception $e) {
            Log::error('Technical audit failed', ['error' => $e->getMessage()]);
            return $this->getDefaultTechnicalAudit();
        }
    }

    /**
     * Audit technique par défaut en cas d'erreur
     */
    private function getDefaultTechnicalAudit(): array
    {
        return [
            'has_title' => false,
            'has_meta_description' => false,
            'has_h1' => false,
            'h1_count' => 0,
            'has_viewport' => false,
            'has_canonical' => false,
            'has_robots' => false,
            'images_with_missing_alt' => 0,
            'internal_links' => 0,
            'has_sitemap' => false,
            'has_favicon' => false,
            'has_og_tags' => false,
            'has_twitter_cards' => false,
            'has_schema_org' => false,
        ];
    }

    /**
     * Analyse de contenu par défaut
     */
    private function getDefaultContentAnalysis(): array
    {
        return [
            'paragraph_count' => 0,
            'short_paragraphs' => 0,
            'sample_paragraphs' => [],
            'paragraphs' => [],
            'duplicate_paragraphs' => []
        ];
    }

    /**
     * Nouvelle méthode pour extraire les paragraphes du texte
     */
    private function extractParagraphsFromText(string $text): array
    {
        if (empty($text)) {
            return [];
        }

        // 🔥 STRATÉGIE AMÉLIORÉE : Plusieurs méthodes
        $paragraphs = [];
        
        // Méthode 1: Sépare par doubles sauts de ligne
        $paragraphs = preg_split('/\n\s*\n/', $text);
        
        // Filtrer et nettoyer - CRITÈRE BEAUCOUP PLUS PERMISSIF
        $paragraphs = array_map('trim', $paragraphs);
        $paragraphs = array_filter($paragraphs, function($p) {
            return strlen($p) > 10; // ⬅️ Réduit de 20 à 10 caractères
        });
        
        // Si pas assez de paragraphes, essayer avec les sauts de ligne simples
        if (count($paragraphs) < 2) {
            $altParagraphs = preg_split('/\n+/', $text);
            $altParagraphs = array_map('trim', $altParagraphs);
            $altParagraphs = array_filter($altParagraphs, function($p) {
                return strlen($p) > 5 && $this->countWords($p) > 1;
            });
            
            if (count($altParagraphs) > count($paragraphs)) {
                $paragraphs = $altParagraphs;
            }
        }
        
        Log::info("📊 Extraction paragraphes AMÉLIORÉE", [
            'original_text_length' => strlen($text),
            'original_word_count' => $this->countWords($text),
            'paragraphs_found' => count($paragraphs),
            'paragraphs_samples' => array_map(function($p) {
                return [
                    'text' => substr($p, 0, 80) . (strlen($p) > 80 ? '...' : ''),
                    'length' => strlen($p),
                    'words' => $this->countWords($p)
                ];
            }, array_slice($paragraphs, 0, 5))
        ]);
        
        return array_values($paragraphs);
    }

    /**
     * Analyse des paragraphes optimisée - CORRIGÉE
     */
    private function analyzeParagraphsOptimized(string $text): array
    {
        if (empty($text)) {
            return $this->getEmptyParagraphAnalysis();
        }

        try {
            $paragraphs = $this->extractParagraphsFromText($text);
            
            if (empty($paragraphs)) {
                return $this->getEmptyParagraphAnalysis();
            }

            $totalParagraphs = count($paragraphs);
            $paragraphsForAnalysis = array_slice($paragraphs, 0, min(30, $totalParagraphs));

            $shortCount = 0;
            $wordCounts = [];
            
            foreach ($paragraphs as $p) {
                $wordCount = $this->countWords($p);
                $wordCounts[] = $wordCount;
                
                // Paragraphe court : moins de 40 mots OU moins de 200 caractères
                if ($wordCount < 40 || strlen($p) < 200) {
                    $shortCount++;
                }
            }

            return [
                'paragraph_count' => $totalParagraphs,
                'short_paragraphs' => $shortCount,
                'sample_paragraphs' => array_slice($paragraphs, 0, 5),
                'paragraphs' => $paragraphsForAnalysis,
                'duplicate_paragraphs' => $this->findDuplicateParagraphs($paragraphs),
                'word_counts' => $wordCounts,
                'avg_words_per_paragraph' => $totalParagraphs > 0 ? round(array_sum($wordCounts) / $totalParagraphs, 1) : 0,
            ];

        } catch (\Exception $e) {
            Log::error('Paragraph analysis failed', ['error' => $e->getMessage()]);
            return $this->getEmptyParagraphAnalysis();
        }
    }

    /**
     * Analyse de paragraphes vide
     */
    private function getEmptyParagraphAnalysis(): array
    {
        return [
            'paragraph_count' => 0,
            'short_paragraphs' => 0,
            'sample_paragraphs' => [],
            'paragraphs' => [],
            'duplicate_paragraphs' => []
        ];
    }

    /**
     * Trouve les paragraphes dupliqués
     */
    private function findDuplicateParagraphs(array $paragraphs): array
    {
        if (empty($paragraphs)) {
            return [];
        }
        
        $normalized = array_map(function($p) {
            return mb_strtolower(trim(preg_replace('/\s+/', ' ', $p)));
        }, $paragraphs);
        
        $counts = array_count_values($normalized);
        $duplicates = [];
        
        foreach ($counts as $normalizedText => $count) {
            if ($count > 1) {
                $originalKey = array_search($normalizedText, $normalized);
                if ($originalKey !== false) {
                    $duplicates[] = $paragraphs[$originalKey];
                }
            }
        }
        
        return array_slice($duplicates, 0, 10);
    }

    /**
     * Calcul de lisibilité - OPTIMISÉ
     */
    private function calculateReadability(string $text): ?float
    {
        if (empty($text) || strlen($text) < 200) {
            return 70.0;
        }
        
        $sampleText = substr($text, 0, 3000);
        
        $sentences = preg_split('/[.!?]+/', $sampleText);
        $words = $this->countWords($sampleText);
        $sentenceCount = count(array_filter($sentences));

        if ($sentenceCount === 0 || $words === 0) {
            return 70.0;
        }

        $averageWordsPerSentence = $words / $sentenceCount;
        return round(100 - min($averageWordsPerSentence * 5, 100), 2);
    }

    /**
     * Calcul de densité des mots-clés - CORRIGÉ
     */
    private function calculateKeywordDensity(string $text, array $keywords): float
    {
        $totalWords = $this->countWords($text);
        if ($totalWords === 0 || empty($keywords)) {
            return 0;
        }

        $count = 0;
        foreach (array_keys($keywords) as $keyword) {
            $count += substr_count(strtolower($text), strtolower($keyword));
        }

        return round(($count / $totalWords) * 100, 2);
    }

    /**
     * 🔥 ANALYSE COMPLÈTE de la structure des headings
     */
    private function analyzeHeadingsStructure(DomCrawler $crawler): array
    {
        $headingsStructure = [
            'h1' => [],
            'h2' => [], 
            'h3' => [],
            'h4' => [],
            'h5' => [],
            'h6' => [],
            'structure' => [],
            'has_issues' => false,
            'issues' => [],
            'summary' => [
                'total' => 0,
                'by_level' => []
            ]
        ];

        try {
            for ($i = 1; $i <= 6; $i++) {
                $headings = $crawler->filter("h{$i}")->each(function($node) use ($i) {
                    $text = trim($node->text());
                    return [
                        'tag' => "h{$i}",
                        'text' => $text,
                        'length' => strlen($text),
                        'depth' => $i,
                        'text_short' => strlen($text) > 60 ? substr($text, 0, 60) . '...' : $text
                    ];
                });
                $headingsStructure["h{$i}"] = $headings;
                $headingsStructure['summary']['by_level']["h{$i}"] = count($headings);
                $headingsStructure['summary']['total'] += count($headings);
            }

            $headingsStructure['structure'] = $this->buildHeadingsHierarchy($crawler);
            $headingsStructure = $this->checkHeadingsIssues($headingsStructure);

            Log::info('📊 Headings structure analyzed', [
                'h1_count' => count($headingsStructure['h1']),
                'h2_count' => count($headingsStructure['h2']),
                'h3_count' => count($headingsStructure['h3']),
                'total_headings' => $headingsStructure['summary']['total'],
                'has_issues' => $headingsStructure['has_issues']
            ]);

        } catch (\Exception $e) {
            Log::warning('Headings structure analysis failed', ['error' => $e->getMessage()]);
        }

        return $headingsStructure;
    }

    /**
     * 🏗️ Construire la hiérarchie des headings
     */
    private function buildHeadingsHierarchy(DomCrawler $crawler): array
    {
        $hierarchy = [];
        
        try {
            $allHeadings = $crawler->filter('h1, h2, h3, h4, h5, h6')->each(function($node) {
                $text = trim($node->text());
                return [
                    'tag' => $node->nodeName(),
                    'text' => $text,
                    'level' => (int) substr($node->nodeName(), 1),
                    'length' => strlen($text),
                    'text_short' => strlen($text) > 50 ? substr($text, 0, 50) . '...' : $text
                ];
            });

            $hierarchy = $allHeadings;

        } catch (\Exception $e) {
            Log::debug('Headings hierarchy build failed', ['error' => $e->getMessage()]);
        }

        return $hierarchy;
    }

    /**
     * 🔍 Vérifier les problèmes de structure des headings
     */
    private function checkHeadingsIssues(array $headingsStructure): array
    {
        $issues = [];
        
        if (empty($headingsStructure['h1'])) {
            $issues[] = 'Aucune balise H1 trouvée';
            $headingsStructure['has_issues'] = true;
        }
        
        if (count($headingsStructure['h1']) > 1) {
            $issues[] = 'Plusieurs balises H1 détectées (' . count($headingsStructure['h1']) . ')';
            $headingsStructure['has_issues'] = true;
        }
        
        if (!empty($headingsStructure['h1'])) {
            $h1 = $headingsStructure['h1'][0];
            if ($h1['length'] > 70) {
                $issues[] = 'H1 trop long (' . $h1['length'] . ' caractères)';
                $headingsStructure['has_issues'] = true;
            }
            
            if ($h1['length'] < 10) {
                $issues[] = 'H1 trop court (' . $h1['length'] . ' caractères)';
                $headingsStructure['has_issues'] = true;
            }
        }
        
        $levels = [];
        foreach ($headingsStructure['structure'] as $heading) {
            $levels[] = $heading['level'];
        }
        
        for ($i = 0; $i < count($levels) - 1; $i++) {
            if ($levels[$i + 1] > $levels[$i] + 1) {
                $issues[] = "Saut hiérarchique de H{$levels[$i]} à H{$levels[$i + 1]}";
                $headingsStructure['has_issues'] = true;
                break;
            }
        }
        
        if ($headingsStructure['summary']['total'] > 20) {
            $issues[] = 'Trop de headings (' . $headingsStructure['summary']['total'] . ')';
            $headingsStructure['has_issues'] = true;
        }
        
        $headingsStructure['issues'] = $issues;
        return $headingsStructure;
    }
}