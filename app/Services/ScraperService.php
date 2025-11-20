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
        // 🔍 CURL avec timeout raisonnable et meilleure gestion d'erreurs
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 30, // ⏱️ 30 secondes au lieu de 10
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_ENCODING => '', // Gérer la compression automatiquement
        ]);
        
        $html = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $loadTime = curl_getinfo($ch, CURLINFO_TOTAL_TIME);
        $error = curl_error($ch);
        curl_close($ch);

        // 🛡️ Vérification robuste du résultat
        if (!$html || $httpCode >= 400) {
            Log::error('CURL failed', [
                'url' => $url,
                'http_code' => $httpCode,
                'error' => $error,
                'html_length' => strlen($html ?? '')
            ]);
            
            return [
                'status' => 'error',
                'message' => $error ?: "HTTP Error: $httpCode",
                'http_code' => $httpCode
            ];
        }

        // ⚡ Limiter la taille du HTML pour éviter la surcharge mémoire
        if (strlen($html) > 2000000) { // 2MB max
            $html = substr($html, 0, 2000000);
            Log::warning('HTML truncated', ['original_size' => strlen($html), 'truncated_to' => 2000000]);
        }

        try {
            // 🧠 Analyse HTML avec gestion d'erreurs
            $crawler = new DomCrawler($html);
            
            // 📊 Extraction du texte principal (optimisée)
            $text = $this->extractMainText($crawler);
            Log::info('Scraped text sample', ['text_length' => strlen($text), 'sample' => substr($text, 0, 200)]);

            // ⚡ Extraire les métadonnées ESSENTIELLES d'abord
            $title = $this->safeExtract($crawler, 'title', 'text');
            $metaDescription = $this->safeExtract($crawler, 'meta[name="description"]', 'content');
            
            // 🎯 Headings (seulement h1-h3 pour la performance)
            $headings = $crawler->filter('h1, h2, h3')->each(function($node) {
                return [
                    'tag' => $node->nodeName(),
                    'text' => trim($node->text())
                ];
            });

            // 📊 Mots-clés (optimisé)
            $wordCount = str_word_count($text);
            $keywords = $this->extractKeywordsOptimized($text);
            $density = $this->calculateKeywordDensity($text, $keywords);

            // 🧾 Analyse de contenu (simplifiée)
            $contentAnalysis = $this->analyzeParagraphsOptimized($text);
            $readabilityScore = $this->calculateReadability($text);

            // 🖼️ Images (limitées)
            $images = $crawler->filter('img')->slice(0, 50)->each(function($node) { // ⚡ Limiter à 50 images
                return [
                    'src' => $node->attr('src'),
                    'alt' => $node->attr('alt') ?? ''
                ];
            });

            // 🔧 Audit technique (essentiel seulement)
            $technicalAudit = $this->extractTechnicalAuditOptimized($crawler);

            // 🔐 HTTPS
            $parsedUrl = parse_url($url);
            $httpsEnabled = ($parsedUrl['scheme'] ?? '') === 'https';

            // 📦 Données structurées
            $hasStructuredData = $crawler->filter('script[type="application/ld+json"]')->count() > 0;

            // 🚫 Noindex
            $noindexDetected = $crawler->filter('meta[name="robots"]')->reduce(function ($node) {
                $content = strtolower($node->attr('content') ?? '');
                return str_contains($content, 'noindex');
            })->count() > 0;

            // 📊 Autres métriques essentielles
            $htmlSize = strlen($html);
            $totalLinks = $crawler->filter('a')->count();
            $hasOgTags = $crawler->filter('meta[property^="og:"]')->count() > 0;
            $htmlLang = $crawler->filter('html')->attr('lang') ?? null;
            $hasFavicon = $crawler->filter('link[rel="icon"], link[rel="shortcut icon"]')->count() > 0;
            $isMobileFriendly = $crawler->filter('meta[name="viewport"]')->count() > 0;

            return [
                'status' => 'success',
                'title' => $title,
                'meta_description' => $metaDescription,
                'headings' => $headings,
                'html' => $html,
                'word_count' => $wordCount,
                'keywords' => $keywords,
                'density' => $density,
                'images' => $images,
                'mobile' => $isMobileFriendly,
                'technical_audit' => $technicalAudit,
                'https_enabled' => $httpsEnabled,
                'has_structured_data' => $hasStructuredData,
                'noindex_detected' => $noindexDetected,
                'load_time' => round($loadTime, 3),
                'html_size' => $htmlSize,
                'total_links' => $totalLinks,
                'has_og_tags' => $hasOgTags,
                'html_lang' => $htmlLang,
                'has_favicon' => $hasFavicon,
                'main_content' => $text,
                'content_analysis' => $contentAnalysis,
                'readability_score' => $readabilityScore,
            ];

        } catch (\Exception $e) {
            Log::error('DOM analysis failed', [
                'url' => $url,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'status' => 'error',
                'message' => 'DOM analysis failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Extraction optimisée du texte principal
     */
    private function extractMainText(DomCrawler $crawler): string
    {
        try {
            // Essayer d'abord les sélecteurs de contenu principal
            $selectors = [
                'main', 'article', '.content', '#content', '.main-content', 
                '.post-content', '.entry-content', 'section'
            ];
            
            foreach ($selectors as $selector) {
                if ($crawler->filter($selector)->count() > 0) {
                    $text = $crawler->filter($selector)->text('');
                    if (str_word_count($text) > 50) {
                        return $text;
                    }
                }
            }
            
            // Fallback sur body
            return $crawler->filter('body')->text('');
            
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
    private function extractKeywordsOptimized(string $text): array
    {
        if (empty($text)) {
            return [];
        }

        // ⚡ Stop words optimisés (beaucoup plus courts)
        $stopWords = [
            // Mots techniques essentiels
            'http', 'https', 'www', 'html', 'css', 'script', 'function',
            'var', 'let', 'const', 'return', 'null', 'undefined',
            
            // Mots vides français/anglais courts
            'the', 'and', 'for', 'are', 'but', 'not', 'you', 'all', 
            'le', 'la', 'les', 'de', 'des', 'du', 'et', 'est',
            'dans', 'pour', 'avec', 'sur', 'par', 'une', 'aux'
        ];

        $words = str_word_count(strtolower($text), 1);
        
        // ⚡ Filtrage optimisé
        $filtered = array_filter($words, function ($word) use ($stopWords) {
            $clean = trim($word, " \t\n\r\0\x0B'\"@&;:,.!?()[]{}<>");
            
            // Exclure les URLs et mots courts/non pertinents
            if (strlen($clean) < 4 || 
                str_contains($clean, 'http') || 
                str_contains($clean, 'www.') ||
                is_numeric($clean) ||
                in_array($clean, $stopWords)) {
                return false;
            }
            
            return true;
        });

        $counts = array_count_values($filtered);
        arsort($counts);

        // Retourne les 15 mots les plus fréquents
        return array_slice($counts, 0, 15);
    }

    /**
     * Audit technique optimisé
     */
    private function extractTechnicalAuditOptimized(DomCrawler $crawler): array
    {
        return [
            'has_title' => $crawler->filter('title')->count() > 0,
            'has_meta_description' => $crawler->filter('meta[name="description"]')->count() > 0,
            'has_h1' => $crawler->filter('h1')->count() > 0,
            'h1_count' => $crawler->filter('h1')->count(),
            'has_viewport' => $crawler->filter('meta[name="viewport"]')->count() > 0,
            'has_canonical' => $crawler->filter('link[rel="canonical"]')->count() > 0,
            'has_robots' => $crawler->filter('meta[name="robots"]')->count() > 0,
            'images_with_missing_alt' => $crawler->filter('img:not([alt])')->count(),
            'internal_links' => $crawler->filter('a[href^="/"], a[href^="' . parse_url($crawler->getUri() ?? '', PHP_URL_HOST) . '"]')->count(),
        ];
    }

    /**
     * Analyse des paragraphes optimisée
     */
    /**
 * Analyse des paragraphes optimisée - CORRIGÉE
 */
private function analyzeParagraphsOptimized(string $text): array
{
    if (empty($text)) {
        return [
            'paragraph_count' => 0,
            'short_paragraphs' => 0,
            'sample_paragraphs' => [],
            'paragraphs' => [], // ← AJOUTÉ
            'duplicate_paragraphs' => [] // ← AJOUTÉ
        ];
    }

    // Séparation en paragraphes
    $paragraphs = preg_split('/(\r\n|\n|\r|\.{1,3}|\?|!)+/', $text);
    $paragraphs = array_filter(array_map('trim', $paragraphs), fn($p) => strlen($p) > 30);
    
    // Limiter le nombre de paragraphes analysés
    $paragraphs = array_slice($paragraphs, 0, 50);

    // Compter les paragraphes courts
    $shortCount = 0;
    foreach ($paragraphs as $p) {
        if (strlen($p) < 100) $shortCount++;
    }

    // Détection des doublons
    $duplicateParagraphs = $this->findDuplicateParagraphs($paragraphs);

    return [
        'paragraph_count' => count($paragraphs),
        'short_paragraphs' => $shortCount,
        'sample_paragraphs' => array_slice($paragraphs, 0, 5),
        'paragraphs' => $paragraphs, // ← TOUS LES PARAGRAPHES
        'duplicate_paragraphs' => $duplicateParagraphs // ← DOUBLONS
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
    private function calculateReadability(string $text): ?float
    {
        if (empty($text)) return null;
        
        $sentences = preg_split('/[.!?]+/', $text);
        $words = str_word_count($text);
        $sentenceCount = count(array_filter($sentences));

        if ($sentenceCount === 0 || $words === 0) return null;

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
}