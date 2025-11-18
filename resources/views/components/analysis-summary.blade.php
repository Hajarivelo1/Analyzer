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

    // ✅ Décodage JSON comme dans l’audit
    try {
        $headings = json_decode($analysis->headings_structure ?? '[]', true, 512, JSON_THROW_ON_ERROR);
    } catch (\JsonException $e) {
        $headings = [];
    }

    // ✅ Comptage des balises H1–H6
    $hCounts = collect($headings)
        ->pluck('tag')
        ->map(fn($tag) => strtolower($tag))
        ->filter(fn($tag) => in_array($tag, ['h1','h2','h3','h4','h5','h6']))
        ->countBy()
        ->sortKeys();

    // ✅ Mots-clés purifiés
    $stopWords = [
        'div','class','script','style','href','src','img','html','head','body','span','ul','li','a','onclick','form','input','button','meta','title','section','footer','header','nav',
        'php','var','function','true','false','null','array','json','request','csrf','token','route','blade',
        'http','https','www','com','net','org',
        'le','la','les','un','une','de','du','des','et','en','à','au','aux','pour','par','sur','dans','avec','sans','ce','cet','cette','ces','est','sont','il','elle','on','nous','vous','ils','elles'
    ];

    $keywords = collect($analysis->keywords ?? [])
        ->reject(function ($count, $word) use ($stopWords) {
            return strlen($word) < 3
                || preg_match('/[<>{}=\/]/', $word)
                || preg_match('/[_A-Z]/', $word)
                || in_array(strtolower($word), $stopWords);
        })
        ->sortDesc()
        ->take(5);

    // ✅ Réseau
    $whois = $analysis->whois_data ?? [];
    $pagerank = $analysis->page_rank ?? null;
    $cloudflare = $analysis->cloudflare_blocked ? '⚠️ Bloqué' : '✅ Accessible';
    $ssl = $analysis->https_enabled ? '✅ SSL actif' : '⛔️ SSL désactivé';
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
        <ul class="list-inline">
            @foreach ($hCounts as $tag => $count)
                <li class="list-inline-item badge bg-light text-dark me-1">{{ strtoupper($tag) }} : {{ $count }}</li>
            @endforeach
        </ul>
    </div>

    <div class="mb-3">
        <h6 class="fw-bold">🔑 Top 5 keywords</h6>
        <ul class="list-inline">
            @forelse ($keywords as $word => $freq)
                <li class="list-inline-item badge bg-info text-dark me-1">{{ $word }} ({{ $freq }})</li>
            @empty
                <li class="text-muted">No significant keywords detected.</li>
            @endforelse
        </ul>
    </div>

    <div class="mb-3">
        <h6 class="fw-bold">🌐 WHOIS & Network</h6>
        <ul class="list-unstyled ms-2">
            <li><strong>Domain :</strong> {{ $whois['name'] ?? '—' }}</li>
            <li><strong>PageRank :</strong> {{ $pagerank ?? '—' }}</li>
            <li><strong>Cloudflare :</strong> {{ $cloudflare }}</li>
            <li><strong>SSL :</strong> {{ $ssl }}</li>
        </ul>
    </div>
</div>
