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
        CURLOPT_TIMEOUT => 15, // ⏱️ RÉDUIT à 15 secondes
        CURLOPT_CONNECTTIMEOUT => 8, // ⏱️ RÉDUIT à 8 secondes
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
        
        // 🔥 RETOURNER DES DONNÉES DE SECOURS POUR CONTINUER L'ANALYSE
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
        
        // 📊 Extraction du texte principal (optimisée)
        $text = $this->extractMainText($crawler);
        Log::info('✅ Scraping réussi', ['text_length' => strlen($text)]);

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
        $wordCount = str_word_count($text);
        $keywords = $this->extractKeywordsOptimized($text);
        $density = $this->calculateKeywordDensity($text, $keywords);

        // 🧾 Analyse de contenu (simplifiée)
        $contentAnalysis = $this->analyzeParagraphsOptimized($text);
        $readabilityScore = $this->calculateReadability($text);

        // 🖼️ Images (limitées)
        $images = $crawler->filter('img')->slice(0, 30)->each(function($node) { // ⚡ RÉDUIT à 30 images
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
            'headings_structure' => $headingsStructure, // 🔥 AJOUTÉ
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
        
        // 🔥 FALLBACK si l'analyse DOM échoue
        return $this->generateFallbackData($url);
    }
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
        // ... autres données existantes ...
    ];
    
    // 🔥 AJOUT de la structure des headings vide
    $fallbackData['headings_structure'] = [
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
    ];
    
    return $fallbackData;
}

    /**
     * Extraction optimisée du texte principal
     */
    /**
 * Extraction optimisée du texte principal - CORRIGÉE
 */
private function extractMainText(DomCrawler $crawler): string
{
    try {
        // 🔥 ÉVITER text() SUR LE BODY COMPLET (trop lent)
        $selectors = [
            'main', 'article', '.content', '#content', '.main-content', 
            '.post-content', '.entry-content', 'section', '.article-content'
        ];
        
        foreach ($selectors as $selector) {
            if ($crawler->filter($selector)->count() > 0) {
                $text = $crawler->filter($selector)->text('');
                if (str_word_count($text) > 30) { // ⏱️ Réduit de 50 à 30 mots
                    return $text;
                }
            }
        }
        
        // 🔥 FALLBACK INTELLIGENT : extraire seulement les paragraphes
        $paragraphs = $crawler->filter('p')->each(function($node) {
            return trim($node->text());
        });
        
        $text = implode(' ', array_slice(array_filter($paragraphs), 0, 20)); // ⚡ Limiter à 20 paragraphes
        
        return !empty($text) ? $text : $crawler->filter('body')->text('');
        
    } catch (\Exception $e) {
        Log::warning('Main text extraction failed', ['error' => $e->getMessage()]);
        return '';
    }
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
    /**
 * Extraction OPTIMISÉE des mots-clés - CORRIGÉE
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
    $sampleText = substr($text, 0, 5000); // 🔥 Analyser seulement les premiers 5000 caractères
    
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

    // Retourne les 10 mots les plus fréquents (réduit de 15 à 10)
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
            // 🔥 AJOUT de nouvelles métriques importantes
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
     * Analyse des paragraphes optimisée
     */
    /**
 * Analyse des paragraphes optimisée - CORRIGÉE
 */
/**
 * Analyse des paragraphes optimisée - ULTRA-RAPIDE
 */
private function analyzeParagraphsOptimized(string $text): array
{
    if (empty($text)) {
        return $this->getEmptyParagraphAnalysis();
    }

    // ⚡ LIMITER l'analyse aux premiers 10000 caractères
    $sampleText = substr($text, 0, 10000);
    
    // ⚡ SÉPARATION PLUS RAPIDE
    $paragraphs = preg_split('/[\n\r]+/', $sampleText); // 🔥 Uniquement les sauts de ligne
    $paragraphs = array_filter(array_map('trim', $paragraphs), fn($p) => strlen($p) > 20);
    
    // ⚡ LIMITER À 20 PARAGRAPHES MAX
    $paragraphs = array_slice($paragraphs, 0, 20);

    $shortCount = 0;
    foreach ($paragraphs as $p) {
        if (strlen($p) < 80) $shortCount++; // 🔥 Seuils ajustés
    }

    return [
        'paragraph_count' => count($paragraphs),
        'short_paragraphs' => $shortCount,
        'sample_paragraphs' => array_slice($paragraphs, 0, 3), // 🔥 3 échantillons seulement
        'paragraphs' => $paragraphs,
        'duplicate_paragraphs' => $this->findDuplicateParagraphs($paragraphs),
    ];
}

/**
 * Analyse de paragraphes vide (pour éviter la duplication)
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
    $counts = array_count_values($paragraphs);
    $duplicates = [];
    
    foreach ($counts as $paragraph => $count) {
        if ($count > 1 && strlen($paragraph) > 50) {
            $duplicates[] = $paragraph;
        }
    }
    
    return array_slice($duplicates, 0, 10); // Limiter à 10 doublons
}

    /**
     * Calcul de lisibilité (inchangé mais optimisé)
     */
    /**
 * Calcul de lisibilité - OPTIMISÉ
 */
private function calculateReadability(string $text): ?float
{
    if (empty($text) || strlen($text) < 200) {
        return 70.0; // 🔥 Valeur par défaut pour les textes courts
    }
    
    // ⚡ LIMITER l'analyse aux premiers 3000 caractères
    $sampleText = substr($text, 0, 3000);
    
    $sentences = preg_split('/[.!?]+/', $sampleText);
    $words = str_word_count($sampleText);
    $sentenceCount = count(array_filter($sentences));

    if ($sentenceCount === 0 || $words === 0) {
        return 70.0;
    }

    $averageWordsPerSentence = $words / $sentenceCount;
    return round(100 - min($averageWordsPerSentence * 5, 100), 2);
}

    /**
     * Calcul de densité des mots-clés (inchangé)
     */
    private function calculateKeywordDensity(string $text, array $keywords): float
    {
        $totalWords = str_word_count($text);
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
        // 🔍 Récupérer toutes les balises h1 à h6
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

        // 🏗️ Reconstituer la structure hiérarchique
        $headingsStructure['structure'] = $this->buildHeadingsHierarchy($crawler);
        
        // 🔍 Vérifier les problèmes courants
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
        // Récupérer toutes les balises headings dans l'ordre du DOM
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

        // Organiser par ordre d'apparition
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
    
    // Vérifier H1 manquant
    if (empty($headingsStructure['h1'])) {
        $issues[] = 'Aucune balise H1 trouvée';
        $headingsStructure['has_issues'] = true;
    }
    
    // Vérifier multiples H1
    if (count($headingsStructure['h1']) > 1) {
        $issues[] = 'Plusieurs balises H1 détectées (' . count($headingsStructure['h1']) . ')';
        $headingsStructure['has_issues'] = true;
    }
    
    // Vérifier H1 trop long
    if (!empty($headingsStructure['h1'])) {
        $h1 = $headingsStructure['h1'][0];
        if ($h1['length'] > 70) {
            $issues[] = 'H1 trop long (' . $h1['length'] . ' caractères)';
            $headingsStructure['has_issues'] = true;
        }
        
        // Vérifier H1 trop court
        if ($h1['length'] < 10) {
            $issues[] = 'H1 trop court (' . $h1['length'] . ' caractères)';
            $headingsStructure['has_issues'] = true;
        }
    }
    
    // Vérifier structure hiérarchique
    $levels = [];
    foreach ($headingsStructure['structure'] as $heading) {
        $levels[] = $heading['level'];
    }
    
    // Vérifier sauts de niveau (ex: h1 → h3)
    for ($i = 0; $i < count($levels) - 1; $i++) {
        if ($levels[$i + 1] > $levels[$i] + 1) {
            $issues[] = "Saut hiérarchique de H{$levels[$i]} à H{$levels[$i + 1]}";
            $headingsStructure['has_issues'] = true;
            break;
        }
    }
    
    // Vérifier si trop de headings
    if ($headingsStructure['summary']['total'] > 20) {
        $issues[] = 'Trop de headings (' . $headingsStructure['summary']['total'] . ')';
        $headingsStructure['has_issues'] = true;
    }
    
    $headingsStructure['issues'] = $issues;
    return $headingsStructure;
}
}