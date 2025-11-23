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

    $statusColors = [
        'Excellent' => 'success',
        'Good' => 'info', 
        'Fair' => 'warning',
        'Poor' => 'orange',
        'Critical' => 'danger'
    ];
    $color = $statusColors[$status] ?? 'secondary';

    // ✅ SOLUTION DÉFINITIVE : Utiliser headings si headings_structure est vide
    $hCounts = [];
    
    // Essayer d'abord headings_structure
    $headingsStructure = $analysis->headings_structure ?? [];
    if (!empty($headingsStructure) && is_array($headingsStructure)) {
        if (isset($headingsStructure['summary']) && isset($headingsStructure['summary']['by_level'])) {
            $hCounts = $headingsStructure['summary']['by_level'];
            $hCounts = array_filter($hCounts, fn($count) => $count > 0);
        } else {
            for ($i = 1; $i <= 6; $i++) {
                $tag = "h{$i}";
                if (isset($headingsStructure[$tag]) && is_array($headingsStructure[$tag])) {
                    $count = count($headingsStructure[$tag]);
                    if ($count > 0) $hCounts[$tag] = $count;
                }
            }
        }
    }
    
    // ✅ FALLBACK : Utiliser les headings simples (fonctionne !)
    if (empty($hCounts)) {
        $simpleHeadings = $analysis->headings ?? [];
        if (is_array($simpleHeadings) && count($simpleHeadings) > 0) {
            foreach ($simpleHeadings as $heading) {
                if (is_array($heading) && isset($heading['tag'])) {
                    $tag = strtolower($heading['tag']);
                    if (in_array($tag, ['h1','h2','h3','h4','h5','h6'])) {
                        $hCounts[$tag] = ($hCounts[$tag] ?? 0) + 1;
                    }
                }
            }
            ksort($hCounts);
        }
    }

    // ✅ Mots-clés purifiés
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

    // ✅ Réseau - ICÔNES PROFESSIONNELLES BOOTSTRAP
    $whois = $analysis->whois_data ?? [];
    $pagerank = $analysis->page_rank ?? null;
    
    // 🔥 CORRECTION : Icônes Bootstrap professionnelles
    $cloudflare = $analysis->cloudflare_blocked 
        ? '<span class="badge bg-warning text-dark"><i class="bi bi-shield-lock me-1"></i>Protected</span>' 
        : '<span class="badge bg-success"><i class="bi bi-shield-check me-1"></i>Accessible</span>';

    $ssl = $analysis->https_enabled 
        ? '<span class="badge bg-success"><i class="bi bi-lock-fill me-1"></i>HTTPS</span>' 
        : '<span class="badge bg-danger"><i class="bi bi-unlock me-1"></i>HTTP</span>';
    
    // ✅ WHOIS data sécurisée
    $domainName = '';
    if (is_array($whois) && isset($whois['name'])) {
        $domainName = $whois['name'];
    } elseif (is_array($whois) && isset($whois['whois']) && is_array($whois['whois'])) {
        $domainName = $whois['whois']['Domain Name'] ?? $whois['whois']['domain'] ?? '—';
    }
@endphp

<div class="analysis-summary-card mb-4">
    <!-- Header avec gradient moderne -->
    <div class="summary-header">
        <div class="d-flex align-items-center">
            <div class="header-icon">
                <i class="bi bi-graph-up-arrow"></i>
            </div>
            <div>
                <h5 class="summary-title mb-0">SEO Analysis Summary</h5>
                <p class="summary-subtitle mb-0">Comprehensive website performance overview</p>
            </div>
        </div>
    </div>

    <!-- Score Principal -->
    <div class="score-section">
        <div class="score-circle">
            <div class="score-progress" data-score="{{ $score }}">
                <span class="score-value">{{ $score }}</span>
                <span class="score-label">/100</span>
            </div>
        </div>
        <div class="score-info">
            <span class="status-badge status-{{ $color }}">{{ ucfirst($status) }}</span>
            <p class="score-description">Overall SEO Performance</p>
        </div>
    </div>

    <!-- Métriques en Grid -->
    <div class="metrics-grid">
        <div class="metric-item">
            <div class="metric-icon text-primary">
                <i class="bi bi-text-paragraph"></i>
            </div>
            <div class="metric-content">
                <div class="metric-value">{{ $analysis->word_count ?? 0 }}</div>
                <div class="metric-label">Words</div>
            </div>
        </div>
        
        <div class="metric-item">
            <div class="metric-icon text-info">
                <i class="bi bi-link-45deg"></i>
            </div>
            <div class="metric-content">
                <div class="metric-value">{{ $analysis->total_links ?? 0 }}</div>
                <div class="metric-label">Links</div>
            </div>
        </div>
        
        <div class="metric-item">
            <div class="metric-icon text-warning">
                <i class="bi bi-image"></i>
            </div>
            <div class="metric-content">
                <div class="metric-value">{{ count($analysis->images_data ?? []) }}</div>
                <div class="metric-label">Images</div>
            </div>
        </div>
        
        <div class="metric-item">
            <div class="metric-icon text-success">
                <i class="bi bi-eye"></i>
            </div>
            <div class="metric-content">
                <div class="metric-value">{{ $analysis->readability_score ?? '—' }}</div>
                <div class="metric-label">Readability</div>
            </div>
        </div>
    </div>

    <!-- Sections d'analyse -->
    <div class="analysis-sections">
        <!-- Headings Structure -->
        <div class="analysis-section">
            <div class="section-header">
                <i class="bi bi-h-square section-icon"></i>
                <h6 class="section-title">Heading Structure</h6>
            </div>
            <div class="section-content">
                @if(count($hCounts) > 0)
                    <div class="tags-grid">
                        @foreach ($hCounts as $tag => $count)
                            <div class="tag-item">
                                <span class="tag-badge {{ $tag }}">{{ strtoupper($tag) }}</span>
                                <span class="tag-count">{{ $count }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <i class="bi bi-search"></i>
                        <p>No heading tags detected</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Keywords -->
        <div class="analysis-section">
            <div class="section-header">
                <i class="bi bi-key section-icon"></i>
                <h6 class="section-title">Top Keywords</h6>
            </div>
            <div class="section-content">
                @if(count($keywords) > 0)
                    <div class="keywords-list">
                        @foreach ($keywords as $word => $freq)
                            <div class="keyword-item">
                                <span class="keyword-text">{{ $word }}</span>
                                <span class="keyword-frequency">{{ $freq }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <i class="bi bi-search"></i>
                        <p>No significant keywords</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Network Info -->
        <div class="analysis-section">
            <div class="section-header">
                <i class="bi bi-globe section-icon"></i>
                <h6 class="section-title">Network Information</h6>
            </div>
            <div class="section-content">
                <div class="network-info">
                    <div class="info-row">
                        <span class="info-label">Domain</span>
                        <span class="info-value">{{ $domainName ?: '—' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">PageRank</span>
                        <span class="info-value">{{ $pagerank ? round($pagerank, 2) . '/10' : '—' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Cloudflare</span>
                        <span class="info-value">
                            {!! $cloudflare !!}
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">SSL</span>
                        <span class="info-value">
                            {!! $ssl !!}
                        </span>
                    </div>
                    @if($analysis->page_rank_global)
                    <div class="info-row">
                        <span class="info-label">Global Rank</span>
                        <span class="info-value">#{{ number_format($analysis->page_rank_global) }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.analysis-summary-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    border-radius: 20px;
    box-shadow: 
        0 10px 40px rgba(0, 0, 0, 0.08),
        0 2px 10px rgba(0, 0, 0, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.6);
    
    overflow: hidden;
    transition: all 0.3s ease;
}

.analysis-summary-card:hover {
    transform: translateY(-2px);
    box-shadow: 
        0 20px 60px rgba(0, 0, 0, 0.12),
        0 4px 20px rgba(0, 0, 0, 0.06);
}

.summary-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    position: relative;
    overflow: hidden;
}

.summary-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 200px;
    height: 200px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
}

.header-icon {
    font-size: 2rem;
    margin-right: 1rem;
    opacity: 0.9;
}

.summary-title {
    font-weight: 700;
    font-size: 1.5rem;
}

.summary-subtitle {
    opacity: 0.9;
    font-size: 0.9rem;
}

.score-section {
    display: flex;
    align-items: center;
    padding: 2rem;
    gap: 2rem;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.score-circle {
    position: relative;
    width: 100px;
    height: 100px;
}

.score-progress {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background: conic-gradient(
        var(--score-color, #667eea) calc({{ $score }} * 3.6deg),
        #e9ecef 0deg
    );
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

.score-progress::before {
    content: '';
    position: absolute;
    width: 80px;
    height: 80px;
    background: white;
    border-radius: 50%;
}

.score-value {
    font-size: 1.8rem;
    font-weight: 700;
    color: #2d3748;
    position: relative;
    z-index: 1;
}

.score-label {
    font-size: 0.8rem;
    color: #718096;
    position: relative;
    z-index: 1;
}

.score-info {
    flex: 1;
}

.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 25px;
    font-weight: 600;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-success { background: #48bb78; color: white; }
.status-info { background: #4299e1; color: white; }
.status-warning { background: #ed8936; color: white; }
.status-orange { background: #ed8936; color: white; }
.status-danger { background: #f56565; color: white; }

.score-description {
    color: #718096;
    margin-top: 0.5rem;
    margin-bottom: 0;
}

.metrics-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
    padding: 1.5rem;
    background: rgba(248, 250, 252, 0.8);
}

.metric-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    transition: all 0.2s ease;
}

.metric-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.metric-icon {
    font-size: 1.5rem;
    opacity: 0.8;
}

.metric-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #2d3748;
    line-height: 1;
}

.metric-label {
    font-size: 0.8rem;
    color: #718096;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
}

.analysis-sections {
    padding: 1.5rem;
}

.analysis-section {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
    border: 1px solid rgba(0, 0, 0, 0.03);
}

.analysis-section:last-child {
    margin-bottom: 0;
}

.section-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1.25rem;
}

.section-icon {
    font-size: 1.25rem;
    color: #667eea;
}

.section-title {
    font-weight: 600;
    color: #2d3748;
    margin: 0;
}

.tags-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(80px, 1fr));
    gap: 0.75rem;
}

.tag-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.75rem;
    background: #f7fafc;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
}

.tag-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
}

.tag-badge.h1 { background: #fed7d7; color: #c53030; }
.tag-badge.h2 { background: #feebc8; color: #dd6b20; }
.tag-badge.h3 { background: #c6f6d5; color: #276749; }
.tag-badge.h4 { background: #bee3f8; color: #2c5aa0; }
.tag-badge.h5 { background: #e9d8fd; color: #6b46c1; }
.tag-badge.h6 { background: #fed7d7; color: #c53030; }

.tag-count {
    font-weight: 600;
    color: #4a5568;
}

.keywords-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.keyword-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem;
    background: #f7fafc;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    transition: all 0.2s ease;
}

.keyword-item:hover {
    background: #edf2f7;
    transform: translateX(4px);
}

.keyword-text {
    font-weight: 600;
    color: #2d3748;
    text-transform: capitalize;
}

.keyword-frequency {
    background: #667eea;
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 600;
}

.network-info {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #f1f5f9;
}

.info-row:last-child {
    border-bottom: none;
}

.info-label {
    font-weight: 600;
    color: #4a5568;
}

.info-value {
    font-weight: 500;
    color: #2d3748;
}

.empty-state {
    text-align: center;
    padding: 2rem;
    color: #a0aec0;
}

.empty-state i {
    font-size: 2rem;
    margin-bottom: 0.5rem;
    opacity: 0.5;
}

.empty-state p {
    margin: 0;
    font-size: 0.9rem;
}

/* Responsive */
@media (max-width: 768px) {
    .score-section {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
    }
    
    .metrics-grid {
        grid-template-columns: 1fr;
    }
    
    .tags-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

/* Animation pour le score */
@keyframes scoreProgress {
    from {
        background: conic-gradient(#667eea 0deg, #e9ecef 0deg);
    }
    to {
        background: conic-gradient(#667eea calc({{ $score }} * 3.6deg), #e9ecef 0deg);
    }
}

.score-progress {
    animation: scoreProgress 1.5s ease-out forwards;
}
</style>

<script>
// Mise à jour dynamique de la couleur du score
document.addEventListener('DOMContentLoaded', function() {
    const score = {{ $score }};
    let scoreColor = '#667eea';
    
    if (score >= 90) scoreColor = '#48bb78';
    else if (score >= 75) scoreColor = '#4299e1';
    else if (score >= 60) scoreColor = '#ed8936';
    else if (score >= 40) scoreColor = '#ed8936';
    else scoreColor = '#f56565';
    
    document.documentElement.style.setProperty('--score-color', scoreColor);
});
</script>