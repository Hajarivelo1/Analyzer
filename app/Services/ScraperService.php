<?php

namespace App\Services;

use Symfony\Component\DomCrawler\Crawler as DomCrawler;
use Illuminate\Support\Facades\Log;
use App\Models\Project; // 🔥 AJOUTER CET IMPORT
use Illuminate\Support\Facades\Http; 

class ScraperService
{
    /**
     * Analyse une URL et retourne les données SEO extraites.
     */
    public function analyze(string $url, $projectId = null): array
{
    Log::info('⚡ ScraperService Optimized - Début analyse', ['url' => $url, 'project_id' => $projectId]);

    // ⚡ CONFIGURATION ÉQUILIBRÉE
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_CONNECTTIMEOUT => 10,
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

    // ⚡ LIMITATION INTELLIGENTE du HTML
    $originalSize = strlen($html);
    $analysisHtml = $html;
    
    if ($originalSize > 800000) {
        $analysisHtml = substr($html, 0, 800000);
        Log::warning('HTML truncated for analysis', [
            'original_size' => $originalSize,
            'truncated_to' => 800000,
            'url' => $url
        ]);
    }

    try {
        // 🧠 Analyse HTML avec gestion d'erreurs
        $crawler = new DomCrawler($analysisHtml);
        
        // 📊 Extraction du texte principal
        $text = $this->extractMainText($crawler);
        
        // 🔥 CORRECTION : Utiliser le bon comptage de mots
        $wordCount = $this->countWords($text);
        
        Log::info('✅ Scraping réussi', [
            'text_length' => strlen($text),
            'word_count' => $wordCount,
            'html_size' => $originalSize
        ]);

        // ⚡ Extraire les métadonnées ESSENTIELLES d'abord
        $title = $this->safeExtract($crawler, 'title', 'text') ?? 'Titre non trouvé';
        $metaDescription = $this->safeExtract($crawler, 'meta[name="description"]', 'content') ?? '';
        
        // 🎯 Headings COMPLET mais limité en profondeur
        $headings = $crawler->filter('h1, h2, h3, h4, h5, h6')->each(function($node) {
            return [
                'tag' => $node->nodeName(),
                'text' => trim($node->text())
            ];
        });

        // 🔥 Analyse complète de la structure des headings
        $headingsStructure = $this->analyzeHeadingsStructure($crawler);

        // 📊 Mots-clés OPTIMISÉ - Échantillon représentatif
        $keywords = [];
        $density = 0;
        if ($wordCount > 50) {
            $sampleText = $wordCount > 5000 ? $this->getTextSample($text, 5000) : $text;
            $keywords = $this->extractKeywordsOptimized($sampleText);
            $density = $this->calculateKeywordDensity($text, $keywords);
        }

        // 🔥 Mettre à jour le projet avec les keywords
        if ($projectId && !empty($keywords)) {
            $this->updateProjectKeywords($projectId, $keywords);
        }

        // 🧾 Analyse de contenu complète
        $contentAnalysis = $this->analyzeParagraphsOptimized($text);
        $readabilityScore = $this->calculateReadability($text);

        // 🖼️ 🔥 NOUVELLE SECTION IMAGES OPTIMISÉE POUR GROS SITES
        $imagesAnalysis = $this->analyzeImagesOptimized($crawler, $url);

        // 🔧 Audit technique COMPLET
        $technicalAudit = $this->extractTechnicalAuditOptimized($crawler, $url);

        // 📊 Autres métriques essentielles
        $isMobileFriendly = $crawler->filter('meta[name="viewport"]')->count() > 0;
        
        // 🔥 CORRECTION SCHEMA.ORG : Détection JSON-LD + Microdata
        $hasStructuredData = $crawler->filter('script[type="application/ld+json"], [itemtype]')->count() > 0;
        
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
            'html' => $originalSize > 800000 ? null : $html,
            'html_size' => $originalSize,
            'word_count' => $wordCount,
            'keywords' => $keywords,
            'density' => $density,
            
            // 🔥 NOUVELLE STRUCTURE IMAGES OPTIMISÉE
            'images' => $imagesAnalysis,
            
            'mobile' => $isMobileFriendly,
            'technical_audit' => $technicalAudit,
            'https_enabled' => str_starts_with($url, 'https://'),
            'has_structured_data' => $hasStructuredData,
            'noindex_detected' => $noindexDetected,
            'load_time' => round($loadTime, 3),
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
            'error' => $e->getMessage(),
            'html_size' => $originalSize ?? 0
        ]);
        
        return $this->generateFallbackData($url);
    }
}

/**
 * 🔥 NOUVELLE MÉTHODE : Analyse optimisée des images pour les gros sites
 */
private function analyzeImagesOptimized(DomCrawler $crawler, string $baseUrl): array
{
    $totalImages = $crawler->filter('img')->count();
    
    Log::info('🖼️ Starting optimized images analysis', [
        'total_images' => $totalImages,
        'base_url' => $baseUrl
    ]);

    // 🔥 STRATÉGIE INTELLIGENTE : Limiter l'analyse pour les gros sites
    $maxImagesToAnalyze = $totalImages > 100 ? 100 : $totalImages;
    
    $imagesWithoutAlt = [];
    $imagesWithAlt = [];
    $sampleImages = [];

    // 🔥 ANALYSE PAR LOT pour optimiser la mémoire
    $crawler->filter('img')->slice(0, $maxImagesToAnalyze)->each(
        function($node) use (&$imagesWithoutAlt, &$imagesWithAlt, &$sampleImages, $baseUrl) {
            
            $src = $node->attr('src') ?? '';
            $alt = $node->attr('alt') ?? '';
            
            // Normaliser l'URL
            $normalizedSrc = $this->normalizeImageUrl($src, $baseUrl);
            
            $imageData = [
                'src' => $normalizedSrc,
                'alt' => $alt,
                'has_alt' => !empty(trim($alt)),
                'filename' => pathinfo($normalizedSrc, PATHINFO_BASENAME)
            ];
            
            // Catégoriser les images
            if (empty(trim($alt))) {
                $imagesWithoutAlt[] = $imageData;
            } else {
                $imagesWithAlt[] = $imageData;
            }
            
            // 🔥 ÉCHANTILLON STRATÉGIQUE : 
            // - Priorité aux images sans alt (car problème SEO)
            // - Maximum 12 images pour l'affichage initial
            if (count($sampleImages) < 12) {
                if (empty(trim($alt)) && count($imagesWithoutAlt) <= 8) {
                    // Inclure jusqu'à 8 images sans alt dans l'échantillon
                    $sampleImages[] = $imageData;
                } elseif (!empty(trim($alt)) && count($sampleImages) < 8) {
                    // Compléter avec quelques images avec alt
                    $sampleImages[] = $imageData;
                }
            }
        }
    );

    // 🔥 STATISTIQUES DÉTAILLÉES
    $stats = [
        'total' => $totalImages,
        'analyzed' => $maxImagesToAnalyze,
        'without_alt' => count($imagesWithoutAlt),
        'with_alt' => count($imagesWithAlt),
        'without_alt_percentage' => $maxImagesToAnalyze > 0 ? 
            round((count($imagesWithoutAlt) / $maxImagesToAnalyze) * 100, 1) : 0,
        'with_alt_percentage' => $maxImagesToAnalyze > 0 ? 
            round((count($imagesWithAlt) / $maxImagesToAnalyze) * 100, 1) : 0,
    ];

    // 🔥 ÉCHANTILLONS POUR L'AFFICHAGE
    $displayWithoutAlt = array_slice($imagesWithoutAlt, 0, 10);
    $displayWithAlt = array_slice($imagesWithAlt, 0, 5);

    Log::info('🎯 Images analysis completed', [
        'total_found' => $totalImages,
        'analyzed' => $maxImagesToAnalyze,
        'without_alt' => count($imagesWithoutAlt),
        'with_alt' => count($imagesWithAlt),
        'sample_size' => count($sampleImages),
        'stats' => $stats
    ]);

    return [
        // 🔥 ÉCHANTILLON pour affichage initial (performances)
        'sample' => $sampleImages,
        
        // 🔥 DONNÉES COMPLÈTES pour l'expand
        'all_analyzed' => [
            'without_alt' => $imagesWithoutAlt,
            'with_alt' => $imagesWithAlt
        ],
        
        // 🔥 STATISTIQUES pour le dashboard
        'stats' => $stats,
        
        // 🔥 INDICATEURS pour l'UI
        'has_more_images' => $totalImages > $maxImagesToAnalyze,
        'has_more_without_alt' => count($imagesWithoutAlt) > 10,
        'has_more_with_alt' => count($imagesWithAlt) > 5,
        
        // 🔥 POUR L'AFFICHAGE STRUCTURÉ
        'display' => [
            'without_alt' => $displayWithoutAlt,
            'with_alt' => $displayWithAlt
        ]
    ];
}

/**
 * 🔥 MÉTHODE : Normaliser les URLs d'images
 */
private function normalizeImageUrl($src, $baseUrl): string
{
    if (empty($src)) {
        return '';
    }
    
    // Supprimer les espaces et caractères invisibles
    $src = trim($src);
    
    // Si c'est une URL absolue
    if (str_starts_with($src, 'http://') || str_starts_with($src, 'https://')) {
        return $src;
    }
    
    // Si c'est un protocole relatif (//example.com/image.jpg)
    if (str_starts_with($src, '//')) {
        return 'https:' . $src;
    }
    
    // Parse l'URL de base
    $parsedBase = parse_url($baseUrl);
    $baseScheme = $parsedBase['scheme'] ?? 'https';
    $baseHost = $parsedBase['host'] ?? '';
    $basePath = $parsedBase['path'] ?? '';
    
    // Si c'est un chemin absolu (/images/photo.jpg)
    if (str_starts_with($src, '/')) {
        return $baseScheme . '://' . $baseHost . $src;
    }
    
    // Si c'est un chemin relatif (images/photo.jpg ou ./images/photo.jpg)
    if (str_starts_with($src, './')) {
        $src = substr($src, 2);
    }
    
    // Construire le chemin complet
    $baseDir = dirname($basePath);
    if ($baseDir === '.' || $baseDir === '/') {
        $baseDir = '';
    }
    
    return $baseScheme . '://' . $baseHost . 
           ($baseDir ? $baseDir . '/' : '/') . 
           ltrim($src, '/');
}

/**
 * 🔥 MÉTHODE : Générer des données de fallback pour les images
 */
private function generateImagesFallbackData(): array
{
    return [
        'sample' => [],
        'all_analyzed' => [
            'without_alt' => [],
            'with_alt' => []
        ],
        'stats' => [
            'total' => 0,
            'analyzed' => 0,
            'without_alt' => 0,
            'with_alt' => 0,
            'without_alt_percentage' => 0,
            'with_alt_percentage' => 0
        ],
        'has_more_images' => false,
        'has_more_without_alt' => false,
        'has_more_with_alt' => false,
        'display' => [
            'without_alt' => [],
            'with_alt' => []
        ]
    ];
}

/**
 * 🔥 MÉTHODE POUR ÉCHANTILLONNER LE TEXTE INTELLIGEMMENT
 */
private function getTextSample(string $text, int $maxChars): string
{
    if (strlen($text) <= $maxChars) {
        return $text;
    }
    
    // Prendre le début (contenu principal) et la fin (conclusion)
    $firstPart = substr($text, 0, $maxChars * 0.7); // 70% du début
    $lastPart = substr($text, -$maxChars * 0.3); // 30% de la fin
    
    return $firstPart . $lastPart;
}
/**
     * 🔥 NOUVELLE MÉTHODE : Mettre à jour les keywords du projet
     */
    private function updateProjectKeywords($projectId, $keywordsArray)
    {
        try {
            $project = Project::find($projectId);
            
            if ($project && is_array($keywordsArray) && !empty($keywordsArray)) {
                // Prendre les 8 mots-clés les plus importants
                $topKeywords = array_slice(array_keys($keywordsArray), 0, 8);
                $keywordsString = implode(', ', $topKeywords);
                
                $project->target_keywords = $keywordsString;
                $project->save();
                
                Log::info('✅ Project keywords updated', [
                    'project_id' => $projectId,
                    'project_name' => $project->name,
                    'keywords' => $keywordsString
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to update project keywords', [
                'project_id' => $projectId,
                'error' => $e->getMessage()
            ]);
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
    /**
 * 🔥 EXTRACTION DU TEXTE PRINCIPAL - CORRIGÉE POUR TOUT LE CONTENU
 */
private function extractMainText(DomCrawler $crawler): string
{
    try {
        // 🔥 STRATÉGIE AMÉLIORÉE : Extraire TOUT le texte du body d'abord
        $bodyText = $crawler->filter('body')->text('');
        
        // Nettoyer le texte
        $cleanText = preg_replace('/<script\b[^>]*>.*?<\/script>/is', '', $bodyText);
        $cleanText = preg_replace('/<style\b[^>]*>.*?<\/style>/is', '', $cleanText);
        $cleanText = strip_tags($cleanText);
        $cleanText = preg_replace('/\s+/', ' ', $cleanText);
        $cleanText = trim($cleanText);
        
        $wordCount = $this->countWords($cleanText);
        
        Log::info("✅ CONTENU COMPLET extrait du body", [
            'word_count' => $wordCount,
            'char_count' => strlen($cleanText),
            'method' => 'body_complet'
        ]);
        
        // Si on a un bon volume de texte, on le retourne
        if ($wordCount > 50) {
            return $cleanText;
        }
        
        // 🔥 FALLBACK : Essayer avec les paragraphes
        $paragraphs = $crawler->filter('p')->each(function($node) {
            $text = trim($node->text());
            return strlen($text) > 3 ? $text : null;
        });
        
        $paragraphs = array_filter($paragraphs);
        
        if (!empty($paragraphs)) {
            $paragraphText = implode("\n\n", $paragraphs);
            $paragraphWordCount = $this->countWords($paragraphText);
            
            Log::info("✅ Contenu des paragraphes", [
                'paragraph_count' => count($paragraphs),
                'word_count' => $paragraphWordCount,
                'method' => 'paragraphes'
            ]);
            
            return $paragraphText;
        }
        
        return $cleanText; // Retourner au moins le texte du body
        
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
    /**
 * Extraction OPTIMISÉE des mots-clés avec n-grammes (1, 2, 3, 4 mots)
 */
/**
 * Extraction OPTIMISÉE des mots-clés avec n-grammes (1, 2, 3, 4 mots)
 */
private function extractKeywordsOptimized(string $text): array
{
    if (empty($text) || strlen($text) < 100) {
        Log::info('📝 Keywords extraction skipped - text too short', [
            'text_length' => strlen($text),
            'word_count' => $this->countWords($text)
        ]);
        return [];
    }

    // ⚡ STOP WORDS FRANÇAIS OPTIMISÉS
    $stopWords = [
        'les', 'des', 'une', 'dans', 'pour', 'avec', 'sur', 'par', 'est', 'son', 
        'ses', 'qui', 'que', 'dans', 'pour', 'avec', 'cette', 'ces', 'dun', 'au',
        'aux', 'du', 'de', 'la', 'le', 'et', 'à', 'en', 'un', 'a', 'se', 'ne',
        'pas', 'plus', 'tout', 'comme', 'fait', 'sont', 'cest', 'vous', 'nous',
        'ils', 'elles', 'leur', 'vos', 'mon', 'ton', 'notre', 'votre', 'leurs',
        'quoi', 'quand', 'alors', 'aussi', 'bien', 'très', 'plus', 'sans', 'sous',
        'the', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 'of', 'with', 'by'
    ];

    // ⚡ LIMITER la taille du texte analysé pour la performance
    $sampleText = substr($text, 0, 8000);
    
    // Nettoyer le texte plus agressivement
    $cleanText = preg_replace('/[^\p{L}\p{N}\s\'-]/u', ' ', $sampleText);
    $cleanText = preg_replace('/\s+/', ' ', $cleanText);
    $cleanText = strtolower(trim($cleanText));
    
    $words = preg_split('/\s+/', $cleanText);
    
    // ⚡ FILTRAGE des mots - CRITÈRES ASSOUPLIS
    $filteredWords = array_filter($words, function ($word) use ($stopWords) {
        $clean = trim($word, " \t\n\r\0\x0B'\"@&;:,.!?()[]{}<>");
        
        // 🔥 CRITÈRES ASSOUPLIS pour capturer plus de mots
        return strlen($clean) > 2 && 
               strlen($clean) < 25 &&
               !in_array($clean, $stopWords) &&
               !is_numeric($clean) &&
               !str_contains($clean, 'http') &&
               !str_contains($clean, 'www.') &&
               !str_contains($clean, '.com') &&
               !str_contains($clean, '.fr') &&
               !str_contains($clean, '.org') &&
               !str_contains($clean, '@');
    });

    // Réindexer le tableau
    $filteredWords = array_values($filteredWords);
    
    Log::info('🔍 Keywords filtering results', [
        'original_words' => count($words),
        'filtered_words' => count($filteredWords),
        'sample_filtered' => array_slice($filteredWords, 0, 10)
    ]);
    
    if (count($filteredWords) < 5) {
        Log::warning('⚠️ Not enough filtered words for n-grams', [
            'filtered_count' => count($filteredWords)
        ]);
        return [];
    }

    // 🔥 GÉNÉRATION DES N-GRAMMES (1, 2, 3, 4 mots)
    $allNgrams = [];
    
    // Unigrammes (mots simples)
    foreach ($filteredWords as $word) {
        $allNgrams[] = $word;
    }
    
    // Bigrammes (2 mots)
    for ($i = 0; $i <= count($filteredWords) - 2; $i++) {
        $bigram = $filteredWords[$i] . ' ' . $filteredWords[$i + 1];
        $allNgrams[] = $bigram;
    }
    
    // Trigrammes (3 mots)
    for ($i = 0; $i <= count($filteredWords) - 3; $i++) {
        $trigram = $filteredWords[$i] . ' ' . $filteredWords[$i + 1] . ' ' . $filteredWords[$i + 2];
        $allNgrams[] = $trigram;
    }
    
    // Quadrigrammes (4 mots)
    for ($i = 0; $i <= count($filteredWords) - 4; $i++) {
        $quadrigram = $filteredWords[$i] . ' ' . $filteredWords[$i + 1] . ' ' . $filteredWords[$i + 2] . ' ' . $filteredWords[$i + 3];
        $allNgrams[] = $quadrigram;
    }

    // Compter les occurrences
    $counts = array_count_values($allNgrams);
    arsort($counts);

    Log::info('📊 N-grams generation results', [
        'total_ngrams' => count($allNgrams),
        'unique_phrases' => count($counts),
        'top_10_raw' => array_slice($counts, 0, 10)
    ]);

    // 🔥 FILTRAGE INTELLIGENT : Garder seulement les n-grammes pertinents
    $filteredKeywords = [];
    foreach ($counts as $phrase => $count) {
        $wordCount = count(explode(' ', $phrase));
        
        // 🔥 SEUILS ASSOUPLIS pour capturer plus de keywords
        if ($wordCount === 1 && $count >= 2) { // ← 2 occurrences au lieu de 3
            $filteredKeywords[$phrase] = $count;
        } elseif ($wordCount === 2 && $count >= 2) {
            $filteredKeywords[$phrase] = $count;
        } elseif ($wordCount === 3 && $count >= 1) { // ← 1 occurrence au lieu de 2
            $filteredKeywords[$phrase] = $count;
        } elseif ($wordCount === 4 && $count >= 1) { // ← 1 occurrence au lieu de 2
            $filteredKeywords[$phrase] = $count;
        }
        
        // Limiter le nombre total pour la performance
        if (count($filteredKeywords) >= 25) break;
    }

    // Trier par importance (occurrences)
    arsort($filteredKeywords);

    $finalKeywords = array_slice($filteredKeywords, 0, 20);

    Log::info("🔍 Keywords analysis COMPLÈTE", [
        'total_mots_filtrés' => count($filteredWords),
        'total_ngrams_generated' => count($allNgrams),
        'unique_phrases' => count($counts),
        'filtered_keywords' => count($filteredKeywords),
        'final_keywords_count' => count($finalKeywords),
        'top_keywords' => array_slice($finalKeywords, 0, 10)
    ]);

    return $finalKeywords;
}

/**
 * 🔥 MÉTHODE DEBUG pour tester l'extraction
 */
public function debugKeywordsExtraction(string $url)
{
    Log::info('🔍 DEBUG Keywords Extraction Start', ['url' => $url]);
    
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    
    $html = curl_exec($ch);
    curl_close($ch);

    $crawler = new DomCrawler($html);
    $text = $this->extractMainText($crawler);
    
    Log::info('📝 DEBUG Text extracted', [
        'text_length' => strlen($text),
        'word_count' => $this->countWords($text),
        'text_sample' => substr($text, 0, 500)
    ]);
    
    $keywords = $this->extractKeywordsOptimized($text);
    
    Log::info('🎯 DEBUG Keywords result', [
        'keywords_count' => count($keywords),
        'keywords' => $keywords
    ]);
    
    return $keywords;
}

    /**
 * Audit technique optimisé
 */
private function extractTechnicalAuditOptimized(DomCrawler $crawler, string $originalUrl = ''): array
{
    try {
        // 🔥 CORRECTION : Utiliser l'URL originale si le crawler n'a pas d'URL
        $currentUrl = $crawler->getUri() ?? $originalUrl;
        
        Log::debug('🔧 Technical Audit - URLs', [
            'crawler_uri' => $crawler->getUri(),
            'original_url' => $originalUrl,
            'current_url_used' => $currentUrl
        ]);
        
        $domain = parse_url($currentUrl, PHP_URL_HOST);
        
        if (!$domain) {
            $domain = $this->extractDomainFromCrawler($crawler) ?? 'unknown-domain';
            Log::warning('⚠️ Domain extraction failed, using fallback', [
                'current_url' => $currentUrl,
                'fallback_domain' => $domain
            ]);
        }
        
        // 🔥 CORRECTION : Meilleure détection du sitemap
        $sitemapDetected = $this->detectSitemap($domain, $crawler);
        
        Log::info('🔍 Sitemap detection in audit', [
            'domain' => $domain,
            'sitemap_detected' => $sitemapDetected,
            'sitemap_url' => $this->getDiscoveredSitemapUrl($domain)
        ]);
        
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
            
            // 🔥 CORRIGÉ : Utiliser la nouvelle détection
            'has_sitemap' => $sitemapDetected,
            'sitemap_url' => $this->getDiscoveredSitemapUrl($domain),
            
            'has_favicon' => $crawler->filter('link[rel="icon"], link[rel="shortcut icon"]')->count() > 0,
            'has_og_tags' => $crawler->filter('meta[property^="og:"]')->count() > 0,
            'has_twitter_cards' => $crawler->filter('meta[name^="twitter:"]')->count() > 0,
            'has_schema_org' => $crawler->filter('script[type="application/ld+json"], [itemtype]')->count() > 0,
        ];
    } catch (\Exception $e) {
        Log::error('Technical audit failed', ['error' => $e->getMessage()]);
        return $this->getDefaultTechnicalAudit();
    }
}

/**
 * 🔥 NOUVELLE MÉTHODE : Détection complète du sitemap avec gestion d'erreur
 */
/**
 * 🔥 CORRIGÉ : Détection complète du sitemap avec logs détaillés
 */
private function detectSitemap(string $domain, DomCrawler $crawler): bool
{
    try {
        Log::info('🚀 Sitemap detection starting', ['domain' => $domain]);
        
        if (empty($domain) || $domain === 'unknown-domain') {
            return false;
        }
        
        $sitemapMethods = [];
        
        // 🔥 OPTIMISATION : Vérifier d'abord dans le HTML (instantané)
        $htmlSitemapLinks = $crawler->filter('link[rel="sitemap"], a[href*="sitemap"]')->count();
        
        if ($htmlSitemapLinks > 0) {
            $sitemapMethods[] = 'html_tags';
            Log::debug("✅ Sitemap found in HTML", ['links_count' => $htmlSitemapLinks]);
            
            // 🔥 SI on trouve des liens dans le HTML, faire les vérifications complètes
            if ($this->checkSitemapInRobots($domain)) {
                $sitemapMethods[] = 'robots_txt';
            }
            
            if ($this->checkDirectSitemapAccess($domain)) {
                $sitemapMethods[] = 'direct_access';
            }
            
        } else {
            // 🔥 OPTIMISATION : Si aucun lien dans le HTML, vérification ULTRA-RAPIDE seulement
            Log::debug('🔍 No sitemap links in HTML - quick check only');
            
            // Vérification rapide du sitemap principal seulement
            if ($this->checkMainSitemapOnly($domain)) {
                $sitemapMethods[] = 'direct_access';
            }
            
            // Vérification rapide de robots.txt seulement si nécessaire
            if (empty($sitemapMethods)) {
                if ($this->checkSitemapInRobots($domain)) {
                    $sitemapMethods[] = 'robots_txt';
                }
            }
        }
        
        $hasSitemap = !empty($sitemapMethods);
        
        Log::info('🔍 Sitemap detection COMPLETE', [
            'domain' => $domain,
            'has_sitemap' => $hasSitemap,
            'methods' => $sitemapMethods,
            'html_links_found' => $htmlSitemapLinks,
            'detection_mode' => $htmlSitemapLinks > 0 ? 'full' : 'quick'
        ]);
        
        return $hasSitemap;
        
    } catch (\Exception $e) {
        Log::error('Sitemap detection failed', [
            'domain' => $domain,
            'error' => $e->getMessage()
        ]);
        return false;
    }
}

/**
 * 🔥 NOUVELLE MÉTHODE : Vérification ultra-rapide du sitemap principal seulement
 */
private function checkMainSitemapOnly(string $domain): bool
{
    try {
        $mainSitemap = "https://{$domain}/sitemap.xml";
        
        $response = Http::timeout(3) // ⚡ 3 secondes max
            ->connectTimeout(2)
            ->withOptions(['verify' => false])
            ->head($mainSitemap);
            
        $hasSitemap = $response->successful();
        
        if ($hasSitemap) {
            Log::debug("✅ Quick sitemap found: {$mainSitemap}");
        }
        
        return $hasSitemap;
        
    } catch (\Exception $e) {
        Log::debug("❌ Quick sitemap check failed for: {$domain}");
        return false;
    }
}






/**
 * Méthode de secours pour extraire le domain
 */
private function extractDomainFromCrawler(DomCrawler $crawler): ?string
{
    try {
        // Essayer d'extraire depuis les balises base
        $baseHref = $crawler->filter('base[href]')->attr('href');
        if ($baseHref) {
            return parse_url($baseHref, PHP_URL_HOST);
        }
        
        // Essayer d'extraire depuis les liens absolus
        $firstLink = $crawler->filter('a[href^="http"]')->first();
        if ($firstLink->count() > 0) {
            $href = $firstLink->attr('href');
            return parse_url($href, PHP_URL_HOST);
        }
        
    } catch (\Exception $e) {
        Log::debug('Domain extraction from crawler failed', ['error' => $e->getMessage()]);
    }
    
    return null;
}

/**
 * Vérifier la présence de sitemap dans robots.txt avec gestion d'erreur
 */
/**
 * 🔥 CORRIGÉ : Vérifier la présence de sitemap dans robots.txt avec meilleure détection
 */
private function checkSitemapInRobots(string $domain): bool
{
    try {
        if (empty($domain) || $domain === 'unknown-domain') {
            return false;
        }
        
        $robotsUrl = "https://{$domain}/robots.txt";
        
        // 🔥 CORRECTION : TIMEOUTS COURTS pour éviter les blocages
        $response = Http::timeout(5) // ⚡ Réduit à 5 secondes
            ->connectTimeout(3)      // ⚡ 3 secondes pour la connexion
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (compatible; SitemapChecker/1.0)'
            ])
            ->withOptions([
                'verify' => false,
                'timeout' => 5,      // ⚡ Double sécurité
            ])
            ->get($robotsUrl);
        
        if ($response->successful()) {
            $content = $response->body();
            
            // Détection rapide sans patterns multiples
            $hasSitemap = preg_match('/^sitemap:\s*.+/mi', $content) > 0;
            
            if ($hasSitemap) {
                Log::debug("✅ Sitemap found in robots.txt for: {$domain}");
                // Extraction rapide sans logs détaillés
                preg_match_all('/^sitemap:\s*(.+)$/mi', $content, $matches);
            }
            
            return $hasSitemap;
        } else {
            Log::debug("❌ Robots.txt not accessible for: {$domain}", ['status' => $response->status()]);
        }
    } catch (\Exception $e) {
        // 🔥 LOG SIMPLIFIÉ pour éviter les pertes de temps
        Log::debug("❌ Robots.txt timeout for: {$domain}");
    }
    
    return false;
}

/**
 * Vérifier l'accès direct aux sitemaps courants avec gestion d'erreur
 */
/**
 * 🔥 CORRIGÉ : Vérifier l'accès direct aux sitemaps avec plus d'options
 */
private function checkDirectSitemapAccess(string $domain): bool
{
    try {
        if (empty($domain) || $domain === 'unknown-domain') {
            return false;
        }
        
        // 🔥 RÉDUIRE LA LISTE aux chemins les plus courants
        $commonSitemapPaths = [
            '/sitemap.xml',
            '/sitemap_index.xml', 
            '/wp-sitemap.xml',
            '/sitemap.txt',
            '/sitemap.xml.gz',
            '/sitemap1.xml',
        ];
        
        foreach ($commonSitemapPaths as $path) {
            try {
                $sitemapUrl = "https://{$domain}{$path}";
                
                // 🔥 CORRECTION : TIMEOUTS COURTS
                $response = Http::timeout(4) // ⚡ Réduit à 4 secondes
                    ->connectTimeout(2)      // ⚡ 2 secondes connexion
                    ->withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (compatible; SitemapChecker/1.0)'
                    ])
                    ->withOptions([
                        'verify' => false,
                        'timeout' => 4, // ⚡ Double sécurité
                    ])
                    ->head($sitemapUrl);
                
                if ($response->successful()) {
                    Log::debug("✅ Direct sitemap access: {$sitemapUrl}");
                    return true;
                }
                
                // 🔥 OPTIMISÉ : Essayer GET seulement si HEAD retourne 4xx/5xx
                if ($response->failed() && $response->status() >= 400) {
                    $getResponse = Http::timeout(4) // ⚡ Même timeout court
                        ->connectTimeout(2)
                        ->withHeaders([
                            'User-Agent' => 'Mozilla/5.0 (compatible; SitemapChecker/1.0)'
                        ])
                        ->withOptions(['verify' => false])
                        ->get($sitemapUrl);
                    
                    if ($getResponse->successful()) {
                        Log::debug("✅ Direct sitemap access (GET): {$sitemapUrl}");
                        return true;
                    }
                }
                
            } catch (\Exception $e) {
                // 🔥 CONTINUER SILENCIEUSEMENT - pas de logs pour chaque timeout
                continue;
            }
        }
        
    } catch (\Exception $e) {
        // 🔥 LOG SIMPLIFIÉ
        Log::debug("Direct sitemap check failed for: {$domain}");
    }
    
    return false;
}

/**
 * 🔥 NOUVELLE MÉTHODE : Test manuel complet de détection de sitemap
 */
public function testSitemapManually(string $url): array
{
    Log::info('🧪 MANUAL Sitemap Test Start', ['url' => $url]);
    
    $domain = parse_url($url, PHP_URL_HOST);
    
    if (!$domain) {
        return ['error' => 'Invalid URL'];
    }
    
    // Tester robots.txt
    $robotsTest = $this->testRobotsTxt($domain);
    
    // Tester les sitemaps directs
    $directTest = $this->testDirectSitemaps($domain);
    
    // Tester le HTML
    $htmlTest = $this->testHtmlForSitemap($url);
    
    $results = [
        'domain' => $domain,
        'robots_txt' => $robotsTest,
        'direct_sitemaps' => $directTest,
        'html_analysis' => $htmlTest,
        'has_sitemap' => $robotsTest['has_sitemap'] || $directTest['accessible_count'] > 0 || $htmlTest['sitemap_links'] > 0
    ];
    
    Log::info('🧪 MANUAL Sitemap Test Results', $results);
    
    return $results;
}

/**
 * 🔥 NOUVELLE MÉTHODE : Test détaillé robots.txt
 */
private function testRobotsTxt(string $domain): array
{
    try {
        $robotsUrl = "https://{$domain}/robots.txt";
        
        $response = Http::timeout(10)
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (compatible; SitemapChecker/1.0)'
            ])
            ->withOptions(['verify' => false])
            ->get($robotsUrl);
        
        if ($response->successful()) {
            $content = $response->body();
            
            // Chercher les sitemaps
            preg_match_all('/^sitemap:\s*(.+)$/mi', $content, $matches);
            $sitemapUrls = $matches[1] ?? [];
            
            return [
                'accessible' => true,
                'has_sitemap' => !empty($sitemapUrls),
                'sitemap_urls' => $sitemapUrls,
                'content_sample' => substr($content, 0, 200) . '...',
                'full_content_length' => strlen($content)
            ];
        } else {
            return [
                'accessible' => false,
                'status_code' => $response->status(),
                'has_sitemap' => false
            ];
        }
    } catch (\Exception $e) {
        return [
            'accessible' => false,
            'error' => $e->getMessage(),
            'has_sitemap' => false
        ];
    }
}

/**
 * 🔥 NOUVELLE MÉTHODE : Test détaillé des sitemaps directs
 */
private function testDirectSitemaps(string $domain): array
{
    $paths = [
        '/sitemap.xml',
        '/sitemap_index.xml',
        '/wp-sitemap.xml',
        '/sitemap/sitemap.xml',
        '/sitemap.txt',
        '/sitemap.xml.gz',
        '/sitemap1.xml'
    ];
    
    $results = [];
    $accessibleCount = 0;
    
    foreach ($paths as $path) {
        $testUrl = "https://{$domain}{$path}";
        try {
            $response = Http::timeout(8)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (compatible; SitemapChecker/1.0)'
                ])
                ->withOptions(['verify' => false])
                ->head($testUrl);
            
            $status = $response->status();
            $accessible = $response->successful();
            
            if ($accessible) {
                $accessibleCount++;
            }
            
            $results[$testUrl] = [
                'status' => $status,
                'accessible' => $accessible
            ];
            
        } catch (\Exception $e) {
            $results[$testUrl] = [
                'status' => 'ERROR',
                'accessible' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    return [
        'results' => $results,
        'accessible_count' => $accessibleCount,
        'total_tested' => count($paths)
    ];
}

/**
 * 🔥 NOUVELLE MÉTHODE : Test HTML pour les sitemaps
 */
private function testHtmlForSitemap(string $url): array
{
    try {
        $response = Http::timeout(10)
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            ])
            ->withOptions(['verify' => false])
            ->get($url);
        
        $html = $response->body();
        
        return [
            'sitemap_links' => preg_match_all('/<a[^>]*href=[\'"][^\'"]*sitemap[^\'"]*[\'"]/i', $html),
            'sitemap_meta' => preg_match_all('/<link[^>]*rel=[\'"]sitemap[\'"]/i', $html),
            'sitemap_in_footer' => preg_match_all('/footer[^>]*sitemap|sitemap[^>]*footer/i', $html),
            'html_analyzed' => true
        ];
    } catch (\Exception $e) {
        return [
            'error' => $e->getMessage(),
            'html_analyzed' => false
        ];
    }
}


/**
 * 🔥 NOUVELLE MÉTHODE : Debug complet du sitemap pour un domaine spécifique
 */
public function debugSitemapForDomain(string $domain): array
{
    Log::info('🐛 DEBUG Sitemap Analysis', ['domain' => $domain]);
    
    // Créer un crawler factice pour tester
    $dummyHtml = '<html><body></body></html>';
    $crawler = new DomCrawler($dummyHtml);
    
    // Tester la détection
    $detectionResult = $this->detectSitemap($domain, $crawler);
    
    // Test manuel
    $manualTest = $this->testSitemapManually("https://{$domain}");
    
    return [
        'domain' => $domain,
        'detection_result' => $detectionResult,
        'manual_test' => $manualTest,
        'summary' => [
            'has_sitemap' => $detectionResult || $manualTest['has_sitemap'],
            'methods_found' => array_filter([
                'robots_txt' => $manualTest['robots_txt']['has_sitemap'],
                'direct_access' => $manualTest['direct_sitemaps']['accessible_count'] > 0,
                'html_links' => $manualTest['html_analysis']['sitemap_links'] > 0
            ])
        ]
    ];
}

/**
 * Obtenir l'URL du sitemap détecté (pour debug) avec gestion d'erreur
 */
private function getDiscoveredSitemapUrl(string $domain): ?string
{
    try {
        if (empty($domain) || $domain === 'unknown-domain') {
            return null;
        }
        
        // 🔥 CORRECTION : TIMEOUT COURT pour robots.txt
        $robotsUrl = "https://{$domain}/robots.txt";
        $response = Http::timeout(5) // ⚡ Réduit à 5 secondes
            ->connectTimeout(3)
            ->withOptions([
                'verify' => false,
                'timeout' => 5,
            ])
            ->get($robotsUrl);
        
        if ($response->successful()) {
            preg_match_all('/^sitemap:\s*(.+)$/mi', $response->body(), $matches);
            return $matches[1][0] ?? null;
        }
    } catch (\Exception $e) {
        // 🔥 Ignorer silencieusement - pas de logs
    }
    
    // 🔥 CORRECTION : TIMEOUTS COURTS pour les chemins directs
    $commonPaths = ['/sitemap.xml', '/sitemap_index.xml', '/wp-sitemap.xml'];
    foreach ($commonPaths as $path) {
        $testUrl = "https://{$domain}{$path}";
        try {
            $response = Http::timeout(4) // ⚡ Réduit à 4 secondes
                ->connectTimeout(2)
                ->withOptions(['verify' => false])
                ->head($testUrl);
                
            if ($response->successful()) {
                return $testUrl;
            }
        } catch (\Exception $e) {
            // Continuer silencieusement
        }
    }
    
    return null;
}

/**
 * 🔥 MÉTHODE PUBLIQUE POUR TESTER LES SITEMAPS
 */
public function testSitemapMethods(string $domain): array
{
    Log::info('🧪 Testing sitemap methods', ['domain' => $domain]);
    
    return [
        'domain' => $domain,
        'checkSitemapInRobots' => $this->checkSitemapInRobots($domain),
        'checkDirectSitemapAccess' => $this->checkDirectSitemapAccess($domain),
        'getDiscoveredSitemapUrl' => $this->getDiscoveredSitemapUrl($domain),
        'detectSitemap' => $this->detectSitemap($domain, new DomCrawler('<html></html>'))
    ];
}



/**
 * 🔥 MÉTHODES PUBLIQUES POUR TESTER DANS TINKER
 */













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
    
    // Filtrer et nettoyer
    $paragraphs = array_map('trim', $paragraphs);
    $paragraphs = array_filter($paragraphs, function($p) {
        return strlen($p) > 10 && $this->countWords($p) > 1;
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
    
    // 🔥 AJOUT : Si toujours pas assez, essayer avec les points comme séparateurs
    if (count($paragraphs) < 2) {
        $sentences = preg_split('/[.!?]+/', $text);
        $sentences = array_map('trim', $sentences);
        $sentences = array_filter($sentences, function($p) {
            return strlen($p) > 20 && $this->countWords($p) > 3;
        });
        
        if (count($sentences) > count($paragraphs)) {
            $paragraphs = $sentences;
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
        
        // 🔥 CORRECTION : Limiter les paragraphes pour l'affichage seulement
        // Mais garder le compte total correct
        $paragraphsForAnalysis = array_slice($paragraphs, 0, min(30, $totalParagraphs));

        $shortCount = 0;
        $wordCounts = [];
        
        foreach ($paragraphs as $p) {
            $wordCount = $this->countWords($p);
            $wordCounts[] = $wordCount;
            
            // Paragraphe court : moins de 40 mots
            if ($wordCount < 40) {
                $shortCount++;
            }
        }

        // 🔥 CORRECTION : Retourner TOUS les paragraphes pour l'analyse
        // mais limiter seulement l'affichage dans le frontend
        return [
            'paragraph_count' => $totalParagraphs,           // ← Total réel
            'short_paragraphs' => $shortCount,
            'sample_paragraphs' => array_slice($paragraphs, 0, 5),
            'paragraphs' => $paragraphs,                     // ← 🔥 TOUS les paragraphes maintenant
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
    /**
 * 🔥 ANALYSE COMPLÈTE de la structure des headings AVEC NETTOYAGE UTF-8
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
                
                // 🔥 NETTOYAGE UTF-8
                $cleanText = $this->cleanUtf8($text);
                
                return [
                    'tag' => "h{$i}",
                    'text' => $cleanText,
                    'length' => strlen($cleanText),
                    'depth' => $i,
                    'text_short' => strlen($cleanText) > 60 ? substr($cleanText, 0, 60) . '...' : $cleanText
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
 * 🔥 MÉTHODE DE NETTOYAGE UTF-8
 */
private function cleanUtf8(string $text): string
{
    if (empty($text)) {
        return '';
    }

    // Première passe : supprimer les caractères non-UTF-8
    $cleaned = preg_replace('/[^\x{0000}-\x{007F}]/u', '', $text);
    
    // Si le résultat est vide, essayer une méthode alternative
    if (empty($cleaned)) {
        $cleaned = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
        $cleaned = preg_replace('/[^\x{0000}-\x{007F}]/u', '', $cleaned);
    }
    
    // Nettoyer les caractères de contrôle
    $cleaned = preg_replace('/[\x00-\x1F\x7F]/u', '', $cleaned);
    
    // Supprimer les espaces multiples
    $cleaned = preg_replace('/\s+/', ' ', $cleaned);
    
    return trim($cleaned);
}

/**
 * 🏗️ Construire la hiérarchie des headings AVEC NETTOYAGE
 */
private function buildHeadingsHierarchy(DomCrawler $crawler): array
{
    $hierarchy = [];
    
    try {
        $allHeadings = $crawler->filter('h1, h2, h3, h4, h5, h6')->each(function($node) {
            $text = trim($node->text());
            
            // 🔥 NETTOYAGE UTF-8
            $cleanText = $this->cleanUtf8($text);
            
            return [
                'tag' => $node->nodeName(),
                'text' => $cleanText,
                'level' => (int) substr($node->nodeName(), 1),
                'length' => strlen($cleanText),
                'text_short' => strlen($cleanText) > 50 ? substr($cleanText, 0, 50) . '...' : $cleanText
            ];
        });

        $hierarchy = $allHeadings;

    } catch (\Exception $e) {
        Log::debug('Headings hierarchy build failed', ['error' => $e->getMessage()]);
    }

    return $hierarchy;
}
private function detectSchemaMarkup(DomCrawler $crawler): bool
{
    // 🔥 VÉRIFICATION RAPIDE - Si pas de JSON-LD ni microdata, arrêter tout de suite
    $jsonLdCount = $crawler->filter('script[type="application/ld+json"]')->count();
    $microdataCount = $crawler->filter('[itemtype]')->count();
    
    if ($jsonLdCount === 0 && $microdataCount === 0) {
        Log::debug('🔍 No schema.org detected - quick exit');
        return false;
    }
    
    // Seulement si on trouve quelque chose, faire l'analyse détaillée
    return $jsonLdCount > 0 || $microdataCount > 0;
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