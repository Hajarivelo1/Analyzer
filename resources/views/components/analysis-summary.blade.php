@props(['analysis'])

@php
    $score = $analysis->seo_score ?? $analysis->score;
    $status = match(true) {
        $score >= 90 => 'Excellent',
        $score >= 75 => 'Good',
        $score >= 60 => 'Fair',
        $score >= 40 => 'Poor',
        default => 'Critical'
    };

    $color = $analysis->score_color ?? 'secondary';

    // ✅ CORRECTION : Les données sont déjà des tableaux grâce aux casts
    $headings = $analysis->headings_structure ?? [];
    
    // ✅ Comptage des balises H1–H6 (version corrigée)
    $hCounts = [];
    if (is_array($headings) && count($headings) > 0) {
        foreach ($headings as $heading) {
            if (is_array($heading) && isset($heading['tag'])) {
                $tag = strtolower($heading['tag']);
                if (in_array($tag, ['h1','h2','h3','h4','h5','h6'])) {
                    $hCounts[$tag] = ($hCounts[$tag] ?? 0) + 1;
                }
            }
        }
        ksort($hCounts);
    }

    // ✅ Mots-clés purifiés (version corrigée)
    $stopWords = [
        'div','class','script','style','href','src','img','html','head','body','span','ul','li','a','onclick','form','input','button','meta','title','section','footer','header','nav',
        'php','var','function','true','false','null','array','json','request','csrf','token','route','blade',
        'http','https','www','com','net','org',
        'le','la','les','un','une','de','du','des','et','en','à','au','aux','pour','par','sur','dans','avec','sans','ce','cet','cette','ces','est','sont','il','elle','on','nous','vous','ils','elles'
    ];

    $keywords = [];
    if (is_array($analysis->keywords ?? []) && count($analysis->keywords) > 0) {
        foreach ($analysis->keywords as $word => $count) {
            if (strlen($word) >= 3 &&
                !preg_match('/[<>{}=\/]/', $word) &&
                !preg_match('/[_A-Z]/', $word) &&
                !in_array(strtolower($word), $stopWords)) {
                $keywords[$word] = $count;
            }
        }
        arsort($keywords);
        $keywords = array_slice($keywords, 0, 5, true);
    }

    // ✅ Réseau
    $whois = $analysis->whois_data ?? [];
    $pagerank = $analysis->page_rank ?? null;
    $cloudflare = $analysis->cloudflare_blocked ? '⚠️ Bloqué' : '✅ Accessible';
    $ssl = $analysis->https_enabled ? '✅ SSL actif' : '⛔️ SSL désactivé';
    
    // ✅ WHOIS data sécurisée
    $domainName = '';
    if (is_array($whois) && isset($whois['name'])) {
        $domainName = $whois['name'];
    } elseif (is_array($whois) && isset($whois['whois']) && is_array($whois['whois'])) {
        $domainName = $whois['whois']['Domain Name'] ?? $whois['whois']['domain'] ?? '—';
    }
@endphp

<div class="glass-card p-4 mb-4">
    <div style="background-color: #dbe1f7;" class="px-4 py-3 rounded-top mb-4">
        <h5 class="fw-bold mb-0" style=" color:#2e4db6;">🧠 Codex Summary</h5>
    </div>

    <div class="mb-3">
        <span class="badge bg-{{ $color }} px-3 py-2 me-2">SEO Score : {{ $score }}/100</span>
        <span class="text-muted">Status : <strong>{{ ucfirst($status) }}</strong></span>
    </div>

    <div class="mb-3">
        <h6 class="fw-bold">🔠 Tags detected</h6>
        @if(count($hCounts) > 0)
            <ul class="list-inline">
                @foreach ($hCounts as $tag => $count)
                    <li class="list-inline-item badge bg-light text-dark me-1 mb-1">{{ strtoupper($tag) }} : {{ $count }}</li>
                @endforeach
            </ul>
        @else
            <p class="text-muted small">Aucune balise heading détectée</p>
        @endif
    </div>

    <div class="mb-3">
        <h6 class="fw-bold">🔑 Top 5 keywords</h6>
        @if(count($keywords) > 0)
            <ul class="list-inline">
                @foreach ($keywords as $word => $freq)
                    <li class="list-inline-item badge bg-info text-dark me-1 mb-1">{{ $word }} ({{ $freq }})</li>
                @endforeach
            </ul>
        @else
            <p class="text-muted small">Aucun mot-clé significatif détecté</p>
        @endif
    </div>

    <div class="mb-3">
        <h6 class="fw-bold">🌐 WHOIS & Network</h6>
        <ul class="list-unstyled ms-2 small">
            <li><strong>Domain :</strong> {{ $domainName ?: '—' }}</li>
            <li><strong>PageRank :</strong> {{ $pagerank ? round($pagerank, 2) . '/10' : '—' }}</li>
            <li><strong>Cloudflare :</strong> {{ $cloudflare }}</li>
            <li><strong>SSL :</strong> {{ $ssl }}</li>
            @if($analysis->page_rank_global)
                <li><strong>Global Rank :</strong> #{{ number_format($analysis->page_rank_global) }}</li>
            @endif
        </ul>
    </div>

    <!-- Métriques supplémentaires -->
    <div class="row mt-3 pt-3 border-top">
        <div class="col-6 col-md-3 text-center">
            <div class="h6 text-primary mb-1">{{ $analysis->word_count ?? 0 }}</div>
            <small class="text-muted">Mots</small>
        </div>
        <div class="col-6 col-md-3 text-center">
            <div class="h6 text-info mb-1">{{ $analysis->total_links ?? 0 }}</div>
            <small class="text-muted">Liens</small>
        </div>
        <div class="col-6 col-md-3 text-center">
            <div class="h6 text-warning mb-1">{{ count($analysis->images_data ?? []) }}</div>
            <small class="text-muted">Images</small>
        </div>
        <div class="col-6 col-md-3 text-center">
            <div class="h6 text-success mb-1">{{ $analysis->readability_score ?? '—' }}</div>
            <small class="text-muted">Lisibilité</small>
        </div>
    </div>
</div>

<style>
.glass-card {
    backdrop-filter: blur(12px);
    background: #f7f6fc;
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.3);
}
</style>