<?php

namespace App\Services;

use Symfony\Component\DomCrawler\Crawler as DomCrawler;

class ScraperService
{
    /**
     * Analyse une URL et retourne les données SEO extraites.
     */
    public function analyze(string $url): array
    {
        // 🔍 CURL
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; SEOAnalyzer/1.0)',
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);
        $html = curl_exec($ch);
        $loadTime = curl_getinfo($ch, CURLINFO_TOTAL_TIME);
        $error = curl_error($ch);
        curl_close($ch);
    
        if (!$html) {
            return [
                'status' => 'error',
                'message' => $error ?: 'Unable to fetch the page'
            ];
        }
    
        // 🧠 Analyse HTML
        $crawler = new DomCrawler($html);
        $text = $crawler->filter('body')->count() ? $crawler->filter('body')->text('') : '';
        \Log::info('Scraped text sample', ['text' => substr($text, 0, 500)]);
    
        
    
        // 📊 Mots-clés
        $wordCount = str_word_count($text);
        $keywords = $this->extractKeywords($text);
        \Log::info('Extracted keywords', $keywords);
        $density = $this->calculateKeywordDensity($text, $keywords);


        // 🧾 Analyse des paragraphes et lisibilité
        $contentAnalysis = $this->analyzeParagraphs($text);
        $readabilityScore = $this->calculateReadability($text);
    
        // 🖼️ Images
        $images = $crawler->filter('img')->each(fn($node) => $node->attr('src'));
    
        // 📱 Mobile
        $isMobileFriendly = $crawler->filter('meta[name="viewport"]')->count() > 0;
    
        // 🔧 Audit technique
        $technicalAudit = $this->extractTechnicalAudit($crawler);
    
        // 🔐 HTTPS
        $parsedUrl = parse_url($url);
        $httpsEnabled = isset($parsedUrl['scheme']) && strtolower($parsedUrl['scheme']) === 'https';
    
        // 📦 Données structurées
        $hasStructuredData = $crawler->filter('script[type="application/ld+json"]')->count() > 0;
    
        // 🚫 Noindex
        $noindexDetected = $crawler->filter('meta[name="robots"]')->reduce(function ($node) {
            return str_contains(strtolower($node->attr('content')), 'noindex');
        })->count() > 0;
    
        // 📊 Autres métriques
        $htmlSize = strlen($html);
        $totalLinks = $crawler->filter('a')->count();
        $hasOgTags = $crawler->filter('meta[property^="og:"]')->count() > 0;
        $htmlLang = $crawler->filter('html')->attr('lang') ?? null;
        $hasFavicon = $crawler->filter('link[rel="icon"], link[rel="shortcut icon"]')->count() > 0;
    
        // 🧠 Sécurisation des attributs
        $title = $crawler->filter('title')->count() ? $crawler->filter('title')->text() : null;
        $metaDescription = $crawler->filter('meta[name="description"]')->count()
            ? $crawler->filter('meta[name="description"]')->attr('content')
            : null;
    
        $headings = $crawler->filter('h1,h2,h3,h4,h5,h6')->each(fn($node) => $node->text());
    
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
    }
    


    /**
     * Extrait les mots-clés les plus fréquents (longueur > 4) du texte.
     */
    private function extractKeywords(string $text): array
{
    $words = str_word_count(strtolower($text), 1);

    // Mots techniques à ignorer
    $stopWordsTech = [
        'value', 'false', 'true', 'null', 'param', 'pixel', 'script', 'style',
        'function', 'return', 'var', 'let', 'const', 'https', 'http', 'www',
        'doctype', 'content', 'type', 'meta', 'link', 'rel', 'href', 'src',
        'cdata', 'number', 'https','wp-content', 'default', 'title','domain',
        'lightbox','event', 'background', '-moz-border-radius', '-webkit-border-radius',
        'border-radius', 'border', 'border-top', 'border-bottom', 'border-left', 'bottom',
        'top', 'left', 'right', 'width', 'height', 'background-color', 'gradient',
        'rgba', 'rgb', 'font', 'family', 'size', 'color', 'text', 'align','color-stop',
        'linear-gradient', 'radial-gradient', 'font-weight', 'font-style', 'font-size',
        'font-family', 'text-align', 'text-decoration', 'text-transform', 'text-indent',
        'text-shadow', 'text-overflow', 'text-justify', 'text-wrap', 'text-decoration',
        'text-transform', 'text-overflow', 'text-wrap', 'text-justify', 'text-wrap',
        'text-overflow', 'text-justify', 'text-wrap', 'text-overflow', 'text-justify',
        'text-wrap', 'text-overflow', 'text-justify', 'text-wrap', 'text-overflow',
        'text-justify', 'text-wrap', 'text-overflow', 'text-justify', 'text-wrap',
        'text-overflow', 'text-justify', 'text-wrap', 'text-overflow', 'text-justify',
        'text-wrap', 'text-overflow', 'text-justify', 'text-wrap', 'text-overflow',
        'text-justify', 'text-wrap', 'text-overflow', 'text-justify', 'text-wrap',
        'text-overflow', 'text-justify', 'text-wrap', 'text-overflow', 'text-justify',
        'text-wrap', 'text-overflow', 'text-justify', 'text-wrap', 'text-overflow',
        'text-justify', 'text-wrap', 'text-overflow', 'text-justify', 'text-wrap',
        'text-overflow', 'text-justify', 'text-wrap', 'text-overflow', 'text-justify',
        'hover', 'padding', 'margin', 'border', 'background', 'width', 'height',
        'font', 'color', 'text', 'align', 'float', 'position', 'z-index', 'display',
        'border-color', 'border-style', 'border-width', 'border-radius', 'box-shadow',
        'text-shadow', 'text-decoration', 'text-transform', 'text-overflow', 'text-justify',
        '-webkit-gradient','-webkit-linear-gradient ', '-moz-linear-gradient','linear',
        'border-width', 'border-style', 'border-color', 'border-radius', 'box-shadow',
        'holidu', 'fimages', 'fstatic', 'fregion', 'keyphotouri', 'overlay',
        'prefix', 'photos', 'cdn', 'static', 'assets', 'img', 'svg', 'json', 'api',
        'tracking', 'value', 'false', 'true', 'null', 'param', 'pixel', 'script', 'style',
        'function', 'return', 'var', 'let', 'const','class', 'important','data-cky-tag',
        'jquery','button','document','cookies', 'required','aria-label','foreach','compid',
        'error','cible', 'variants','counter', 'forminator', 'window', 'publique','picture',
        'valide','dateformat','number-','group-','nombre', '--wp--preset--color--nv-c-', 
        'label', 'plugins','selector', 'remove','name-','response','cookie','browser','getversion',
        'getname','getid','getvalue','gettype','getlength','getitem','getattribute','get','set','success',
        'error','errorcode','errormessage','errors','errorcode','errormessage','errors','errorcode','errormessage',
        'slider','modules','textpanel','thumb','elementor','revapi','enable', 'offset','gallery','wp','undefined',
        'strippanel','enabled','revslider','fullscreen','elementor-social-icon','wp-admin','mailpoet','',
        


    ];

    // Mots vides français
    $stopWordsFr = [
        'pour', 'avec', 'dans', 'entre', 'elles', 'nous', 'vous', 'avoir',
        'faire', 'être', 'tout', 'mais', 'comme', 'cette', 'ceux', 'leurs',
        'quelque', 'aucun', 'chaque', 'depuis', 'encore', 'souvent', 'sans',
        'donc', 'alors', 'ainsi', 'très', 'plus', 'moins', 'chez', 'dont', 'notre', 'veuillez',
        'votre', 'date-',
    ];

    // Mots vides anglais
    $stopWordsEn = [
        'about', 'above', 'after', 'again', 'against', 'all', 'almost', 'also',
        'although', 'am', 'among', 'an', 'and', 'any', 'are', 'as', 'at',
        'be', 'because', 'been', 'before', 'being', 'below', 'between', 'both',
        'but', 'by', 'can', 'could', 'did', 'do', 'does', 'doing', 'down',
        'during', 'each', 'few', 'for', 'from', 'further', 'had', 'has',
        'have', 'having', 'he', 'her', 'here', 'hers', 'him', 'his', 'how',
        'i', 'if', 'in', 'into', 'is', 'it', 'its', 'itself', 'just', 'me',
        'more', 'most', 'my', 'myself', 'no', 'nor', 'not', 'now', 'of', 'off',
        'on', 'once', 'only', 'or', 'other', 'our', 'ours', 'ourselves', 'out',
        'over', 'own', 'same', 'she', 'should', 'so', 'some', 'such', 'than',
        'that', 'the', 'their', 'theirs', 'them', 'themselves', 'then', 'there',
        'these', 'they', 'this', 'those', 'through', 'to', 'too', 'under',
        'until', 'up', 'very', 'was', 'we', 'were', 'what', 'when', 'where',
        'which', 'while', 'who', 'whom', 'why', 'will', 'with', 'you', 'your',
        'yours', 'yourself', 'yourselves'
    ];

    $stopWords = array_merge($stopWordsTech, $stopWordsFr, $stopWordsEn);

    // Filtrage : longueur > 4 et non dans la liste
    $filtered = array_filter($words, function ($word) use ($stopWords) {
        $clean = trim($word, " \t\n\r\0\x0B'\"@&;:,.!?()[]{}<>");
    
        // Exclure tous les mots contenant "https"
        if (str_contains($clean, 'https')) {
            return false;
        }
    
        return strlen($clean) > 4 && !in_array($clean, $stopWords);
    });
    

    $counts = array_count_values($filtered);
    arsort($counts);

    // Retourne les 10 mots les plus fréquents avec leur nombre d’occurrences
    return array_slice($counts, 0, 10);
}


    /**
     * Calcule la densité des mots-clés dans le texte.
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

    private function extractTechnicalAudit(\Symfony\Component\DomCrawler\Crawler $crawler): array
{
    return [
        'has_title' => $crawler->filter('title')->count() > 0,
        'has_meta_description' => $crawler->filter('meta[name="description"]')->count() > 0,
        'has_h1' => $crawler->filter('h1')->count() > 0,
        'has_viewport' => $crawler->filter('meta[name="viewport"]')->count() > 0,
        'has_canonical' => $crawler->filter('link[rel="canonical"]')->count() > 0,
        'has_robots' => $crawler->filter('meta[name="robots"]')->count() > 0,
        'images_with_missing_alt' => $crawler->filter('img:not([alt])')->count(),
        'internal_links' => $crawler->filter('a')->reduce(function ($node) {
            $href = $node->attr('href');
            return $href && !str_starts_with($href, 'http') && !str_starts_with($href, '//');
        })->count(), 
        'has_strong_or_em' => $crawler->filter('strong, em')->count() > 0,
        

    ];
}


private function safeAttr(Crawler $crawler, string $selector, string $attribute): ?string
{
    $node = $crawler->filter($selector);
    return $node->count() ? $node->attr($attribute) : null;
}


private function analyzeParagraphs(string $text): array
{
    $paragraphs = preg_split('/(\r\n|\n|\r|\.{1,3}|\?|!)+/', $text);

    $paragraphs = array_filter(array_map('trim', $paragraphs), fn($p) => strlen($p) > 30);


    $shortCount = 0;
    $duplicates = [];
    $seen = [];

    foreach ($paragraphs as $p) {
        if (strlen($p) < 100) $shortCount++;
        if (in_array($p, $seen)) $duplicates[] = $p;
        $seen[] = $p;
    }

    return [
        'paragraphs' => array_values($paragraphs),
        'paragraph_count' => count($paragraphs),
        'short_paragraphs' => $shortCount,
        'duplicate_paragraphs' => array_unique($duplicates),
    ];
}


private function calculateReadability(string $text): ?float
{
    $sentences = preg_split('/[.!?]+/', $text);
    $words = str_word_count($text);
    $sentenceCount = count(array_filter($sentences));

    if ($sentenceCount === 0 || $words === 0) return null;

    $averageWordsPerSentence = $words / $sentenceCount;
    return round(100 - $averageWordsPerSentence * 5, 2); // Score simplifié
}



}
