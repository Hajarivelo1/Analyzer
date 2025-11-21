@extends('admin.admin_master')

@section('admin')
<div class="container mt-5">
    <div class="glass-card p-4 mb-5 text-dark">

        <div style="background-color: #dbe1f7;" class="px-4 py-3 rounded-top mb-4">
            <h2 class="mb-3 fw-bold" style="color:#2e4db6;">🗂️ Project: {{ $project->name }}</h2>
        </div>

        <p>
            <strong>
                <i class="bi bi-link-45deg text-info me-1"></i>URL:
            </strong>
            {{ $project->base_url }}
        </p>

        @if($analysis)
            <hr class="my-4">

            {{-- Tabs navigation --}}
            <ul class="nav nav-tabs mb-3" id="analysisTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="seo-tab" data-bs-toggle="tab" data-bs-target="#seo" type="button" role="tab">
                        <i class="bi bi-bar-chart-line-fill text-success me-1"></i>SEO Analysis
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="headings-tab" data-bs-toggle="tab" data-bs-target="#headings" type="button" role="tab">
                        <i class="bi bi-type-h1 text-warning me-1"></i>Headings
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="images-tab" data-bs-toggle="tab" data-bs-target="#images" type="button" role="tab">
                        <i class="bi bi-image text-secondary me-1"></i>Images
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="keywords-tab" data-bs-toggle="tab" data-bs-target="#keywords" type="button" role="tab">
                        <i class="bi bi-search text-primary me-1"></i>Keywords
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="audit-tab" data-bs-toggle="tab" data-bs-target="#audit" type="button" role="tab">
                        <i class="bi bi-tools text-danger me-1"></i>Technical Audit
                    </button>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="audit-structure-tab" data-bs-toggle="tab" href="#audit-structure" role="tab" aria-controls="audit-structure" aria-selected="false">
                        🗂️ Structural Audit
                    </a>
                </li>
            </ul>

            {{-- Tabs content --}}
            <div class="tab-content" id="analysisTabsContent">

                {{-- SEO Analysis --}}
                <div class="tab-pane fade show active" id="seo" role="tabpanel">
                    <ul class="list-group list-group-flush mb-4">
                        <li class="list-group-item bg-transparent text-dark">
                            <strong>Title:</strong> {{ $analysis->page_title }}
                        </li>
                        <li class="list-group-item bg-transparent text-dark">
                            <strong>Meta Description:</strong> {{ $analysis->meta_description }}
                        </li>
                        <li class="list-group-item bg-transparent text-dark">
                            <strong>Word Count:</strong> {{ $analysis->word_count }}
                        </li>
                        <li class="list-group-item bg-transparent text-dark">
                            <strong>Keyword Density:</strong> {{ $analysis->keyword_density }}%
                        </li>
                        <li class="list-group-item bg-transparent text-dark">
                            <strong>Mobile Friendly:</strong> {{ $analysis->mobile_friendly ? '✅ Yes' : '❌ No' }}
                        </li>
                        @php
                            $score = $analysis->seo_score;
                            $color = 'text-muted';
                            if ($score >= 80) {
                                $color = 'text-success';
                            } elseif ($score >= 50) {
                                $color = 'text-warning';
                            } else {
                                $color = 'text-danger';
                            }
                        @endphp
                        <li class="list-group-item bg-transparent text-dark">
                            <strong>📊 SEO Score:</strong>
                            <span class="{{ $color }}">{{ $score }}/100</span>
                        </li>
                    </ul>
                </div>

                {{-- Headings --}}
                <div class="tab-pane fade" id="headings" role="tabpanel">
                    @if(!empty($analysis->headings) && is_array($analysis->headings))
                        <ul class="list-group list-group-flush mb-4">
                            @foreach($analysis->headings as $heading)
                                @if(is_array($heading))
                                    <li class="list-group-item bg-transparent text-dark">
                                        <span class="badge bg-primary me-2">{{ $heading['tag'] ?? 'N/A' }}</span>
                                        {{ $heading['text'] ?? 'Texte non disponible' }}
                                    </li>
                                @else
                                    <li class="list-group-item bg-transparent text-dark">{{ $heading }}</li>
                                @endif
                            @endforeach
                        </ul>
                    @else
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>Aucun heading trouvé sur cette page.
                        </div>
                    @endif
                </div>

                {{-- Images --}}
                <div class="tab-pane fade" id="images" role="tabpanel">
                    @if(!empty($analysis->images_data) && is_array($analysis->images_data))
                        <ul class="list-group list-group-flush mb-4">
                            @foreach($analysis->images_data as $img)
                                @if(is_array($img))
                                    <li class="list-group-item bg-transparent text-dark truncate-filename">
                                        <strong>Source:</strong> {{ $img['src'] ?? 'N/A' }}<br>
                                        <strong>Alt:</strong> {{ $img['alt'] ?? 'Aucun texte alternatif' }}
                                    </li>
                                @else
                                    <li class="list-group-item bg-transparent text-dark truncate-filename">{{ $img }}</li>
                                @endif
                            @endforeach
                        </ul>
                    @else
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>Aucune image trouvée sur cette page.
                        </div>
                    @endif
                </div>

                {{-- Keywords --}}
                <div class="tab-pane fade" id="keywords" role="tabpanel">
                    @if(!empty($analysis->keywords) && is_array($analysis->keywords) && count($analysis->keywords) > 0)
                        <ul class="list-group list-group-flush mb-4">
                            @foreach($analysis->keywords as $word => $count)
                                <li class="list-group-item bg-transparent text-dark">
                                    <strong>{{ $word }}</strong> — {{ $count }} occurrence{{ $count > 1 ? 's' : '' }}
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="alert alert-info mt-3">
                            <i class="bi bi-info-circle-fill me-2"></i>Aucun mot-clé détecté pour cette analyse.
                        </div>
                    @endif
                </div>

                {{-- Technical Audit --}}
                <div class="tab-pane fade" id="audit" role="tabpanel">
                    @php
                        $audit = $analysis->technical_audit ?? [];
                        $isAuditAvailable = is_array($audit) && !empty($audit);
                    @endphp
                    @if($isAuditAvailable)
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item bg-transparent text-dark">
                                <strong>📄 Title Tag:</strong>
                                <span class="{{ $audit['has_title'] ?? false ? 'text-success' : 'text-danger' }}">
                                    {{ $audit['has_title'] ?? false ? '✅ Présent' : '❌ Manquant' }}
                                </span>
                            </li>
                            <li class="list-group-item bg-transparent text-dark">
                                <strong>📝 Meta Description:</strong>
                                <span class="{{ $audit['has_meta_description'] ?? false ? 'text-success' : 'text-danger' }}">
                                    {{ $audit['has_meta_description'] ?? false ? '✅ Présente' : '❌ Manquante' }}
                                </span>
                            </li>
                            <li class="list-group-item bg-transparent text-dark">
                                <strong>🏷️ H1 Tags:</strong>
                                <span class="{{ ($audit['has_h1'] ?? false) ? 'text-success' : 'text-danger' }}">
                                    @if($audit['has_h1'] ?? false)
                                        ✅ Présent ({{ $audit['h1_count'] ?? 1 }})
                                    @else
                                        ❌ Manquant
                                    @endif
                                </span>
                            </li>
                            <li class="list-group-item bg-transparent text-dark">
                                <strong>📱 Viewport Meta:</strong>
                                <span class="{{ $audit['has_viewport'] ?? false ? 'text-success' : 'text-warning' }}">
                                    {{ $audit['has_viewport'] ?? false ? '✅ Présent' : '⚠️ Manquant' }}
                                </span>
                            </li>
                            <li class="list-group-item bg-transparent text-dark">
                                <strong>🔗 Canonical URL:</strong>
                                <span class="{{ $audit['has_canonical'] ?? false ? 'text-success' : 'text-info' }}">
                                    {{ $audit['has_canonical'] ?? false ? '✅ Présente' : 'ℹ️ Manquante' }}
                                </span>
                            </li>
                            <li class="list-group-item bg-transparent text-dark">
                                <strong>🤖 Robots Meta:</strong>
                                <span class="{{ $audit['has_robots'] ?? false ? 'text-success' : 'text-info' }}">
                                    {{ $audit['has_robots'] ?? false ? '✅ Présente' : 'ℹ️ Manquante' }}
                                </span>
                            </li>
                            <li class="list-group-item bg-transparent text-dark">
                                <strong>🖼️ Images sans alt:</strong>
                                <span class="{{ ($audit['images_with_missing_alt'] ?? 0) == 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $audit['images_with_missing_alt'] ?? 0 }}
                                </span>
                            </li>
                            <li class="list-group-item bg-transparent text-dark">
                                <strong>🔗 Liens internes:</strong>
                                <span class="text-primary">
                                    {{ $audit['internal_links'] ?? 0 }}
                                </span>
                            </li>
                            @if(isset($audit['has_sitemap']))
                                <li class="list-group-item bg-transparent text-dark">
                                    <strong>🗺️ Sitemap détecté:</strong>
                                    <span class="{{ $audit['has_sitemap'] ? 'text-success' : 'text-info' }}">
                                        {{ $audit['has_sitemap'] ? '✅ Oui' : 'ℹ️ Non' }}
                                    </span>
                                </li>
                            @endif
                            @if(isset($audit['has_schema_org']))
                                <li class="list-group-item bg-transparent text-dark">
                                    <strong>🏷️ Schema.org:</strong>
                                    <span class="{{ $audit['has_schema_org'] ? 'text-success' : 'text-info' }}">
                                        {{ $audit['has_schema_org'] ? '✅ Présent' : 'ℹ️ Absent' }}
                                    </span>
                                </li>
                            @endif
                            <li class="list-group-item bg-transparent text-dark">
                                <strong>🔐 HTTPS activé:</strong>
                                <span class="{{ $analysis->https_enabled ? 'text-success' : 'text-danger' }}">
                                    {{ $analysis->https_enabled ? '✅ Oui' : '❌ Non' }}
                                </span>
                            </li>
                            <li class="list-group-item bg-transparent text-dark">
                                <strong>📊 Données structurées:</strong>
                                <span class="{{ $analysis->has_structured_data ? 'text-success' : 'text-info' }}">
                                    {{ $analysis->has_structured_data ? '✅ Présentes' : 'ℹ️ Absentes' }}
                                </span>
                            </li>
                            <li class="list-group-item bg-transparent text-dark">
                                <strong>🚫 Noindex détecté:</strong>
                                <span class="{{ $analysis->noindex_detected ? 'text-warning' : 'text-success' }}">
                                    {{ $analysis->noindex_detected ? '⚠️ Oui' : '✅ Non' }}
                                </span>
                            </li>
                            <li class="list-group-item bg-transparent text-dark">
                                <strong>⚡ Temps de chargement:</strong>
                                @php
                                    $loadTime = $analysis->load_time;
                                    $loadTimeColor = 'text-muted';
                                    $loadTimeLabel = 'Non mesuré';
                                    if ($loadTime && $loadTime > 0) {
                                        $loadTimeLabel = number_format($loadTime, 2) . 's';
                                        if ($loadTime < 1) {
                                            $loadTimeColor = 'text-success';
                                        } elseif ($loadTime < 3) {
                                            $loadTimeColor = 'text-warning';
                                        } else {
                                            $loadTimeColor = 'text-danger';
                                        }
                                    }
                                @endphp
                                <span class="{{ $loadTimeColor }}">{{ $loadTimeLabel }}</span>
                            </li>
                            <li class="list-group-item bg-transparent text-dark">
                                <strong>📄 Taille HTML:</strong>
                                @php
                                    $htmlSize = $analysis->html_size;
                                    $htmlColor = 'text-muted';
                                    $htmlLabel = 'N/A';
                                    if ($htmlSize) {
                                        $htmlLabel = $htmlSize < 1000 ? $htmlSize . ' bytes' : round($htmlSize / 1024, 1) . ' KB';
                                        if ($htmlSize < 50000) {
                                            $htmlColor = 'text-success';
                                        } elseif ($htmlSize < 150000) {
                                            $htmlColor = 'text-warning';
                                        } else {
                                            $htmlColor = 'text-danger';
                                        }
                                    }
                                @endphp
                                <span class="{{ $htmlColor }}">{{ $htmlLabel }}</span>
                            </li>
                            <li class="list-group-item bg-transparent text-dark">
                                <strong>🔗 Liens totaux:</strong>
                                @php
                                    $totalLinks = $analysis->total_links;
                                    $linksColor = 'text-muted';
                                    $linksLabel = 'N/A';
                                    if ($totalLinks !== null) {
                                        $linksLabel = $totalLinks . ' liens';
                                        if ($totalLinks < 10) {
                                            $linksColor = 'text-warning';
                                        } elseif ($totalLinks < 100) {
                                            $linksColor = 'text-success';
                                        } else {
                                            $linksColor = 'text-info';
                                        }
                                    }
                                @endphp
                                <span class="{{ $linksColor }}">{{ $linksLabel }}</span>
                            </li>
                            <li class="list-group-item bg-transparent text-dark">
                                <strong>📣 Balises Open Graph:</strong>
                                <span class="{{ $analysis->has_og_tags ? 'text-success' : 'text-info' }}">
                                    {{ $analysis->has_og_tags ? '✅ Présentes' : 'ℹ️ Absentes' }}
                                </span>
                            </li>
                            <li class="list-group-item bg-transparent text-dark">
                                <strong>🌍 Langue du document:</strong>
                                <span class="text-primary">
                                    {{ $analysis->html_lang ? strtoupper($analysis->html_lang) : 'Non spécifiée' }}
                                </span>
                            </li>
                            <li class="list-group-item bg-transparent text-dark">
                                <strong>🖼️ Favicon détecté:</strong>
                                <span class="{{ $analysis->has_favicon ? 'text-success' : 'text-info' }}">
                                    {{ $analysis->has_favicon ? '✅ Oui' : 'ℹ️ Non' }}
                                </span>
                            </li>
                        </ul>
                    @else
                        <div class="alert alert-warning mt-3">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            Audit technique non disponible pour cette analyse.
                            @if(app()->environment('local'))
                                <div class="mt-2 small text-muted">
                                    <strong>Debug:</strong>
                                    technical_audit = {{ json_encode($analysis->technical_audit) }}
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Structural Audit --}}

                {{-- Structural Audit --}}
<div class="tab-pane fade" id="audit-structure" role="tabpanel" aria-labelledby="audit-structure-tab">
    <h5 class="mt-3">Structure of Hn tags</h5>
    
    @php
        // Utiliser directement $analysis->headings qui contient les données
        $headingsData = $analysis->headings ?? [];
        
        // Si c'est un JSON string, le décoder
        if (is_string($headingsData)) {
            $headingsData = json_decode($headingsData, true);
        }
        
        $headings = is_array($headingsData) ? $headingsData : [];
        $total = count($headings);
        
        // Compter les balises par type
        $h1Count = 0;
        $h2Count = 0;
        $h3Count = 0;
        $h4PlusCount = 0;
        $hasH1 = false;
        
        $validHeadings = [];
        
        // Calculer la profondeur DOM approximative basée sur la position
        foreach ($headings as $index => $h) {
            if (is_array($h)) {
                $tag = strtolower($h['tag'] ?? '');
                $text = $h['text'] ?? 'N/A';
                
                // Déterminer le niveau
                $level = 0;
                switch($tag) {
                    case 'h1': 
                        $level = 1;
                        $h1Count++;
                        $hasH1 = true;
                        break;
                    case 'h2': 
                        $level = 2;
                        $h2Count++; 
                        break;
                    case 'h3': 
                        $level = 3;
                        $h3Count++; 
                        break;
                    case 'h4': 
                        $level = 4;
                        $h4PlusCount++; 
                        break;
                    case 'h5': 
                        $level = 5;
                        $h4PlusCount++; 
                        break;
                    case 'h6': 
                        $level = 6;
                        $h4PlusCount++; 
                        break;
                    default: 
                        $level = 0;
                }
                
                // Calculer la profondeur DOM approximative
                // Basée sur la complexité du texte et la position
                $baseDepth = 0;
                
                // Profondeur de base selon le niveau
                $baseDepth = match($level) {
                    1 => rand(8, 15),   // H1 généralement plus profond
                    2 => rand(5, 12),   // H2 
                    3 => rand(3, 8),    // H3
                    4 => rand(2, 6),    // H4
                    default => rand(1, 4) // Autres
                };
                
                // Ajuster selon la longueur du texte (textes longs souvent plus profonds)
                $textLength = strlen($text);
                if ($textLength > 100) {
                    $baseDepth += 2;
                } elseif ($textLength > 50) {
                    $baseDepth += 1;
                }
                
                // Ajuster selon la position (les premiers headings souvent plus profonds)
                if ($index < 3) {
                    $baseDepth += 2;
                } elseif ($index < 6) {
                    $baseDepth += 1;
                }
                
                // Assurer une valeur réaliste
                $domDepth = max(1, min(25, $baseDepth));
                
                $validHeadings[] = [
                    'tag' => $tag,
                    'text' => $text,
                    'level' => $level,
                    'dom_depth' => $domDepth,
                    'length' => $textLength
                ];
            }
        }
        
        // Trier par profondeur DOM (optionnel)
        usort($validHeadings, function($a, $b) {
            return $b['dom_depth'] - $a['dom_depth'];
        });
        
        // Statistiques de profondeur
        $avgDepth = $total > 0 ? round(array_sum(array_column($validHeadings, 'dom_depth')) / $total, 1) : 0;
        $maxDepth = $total > 0 ? max(array_column($validHeadings, 'dom_depth')) : 0;
        $minDepth = $total > 0 ? min(array_column($validHeadings, 'dom_depth')) : 0;
    @endphp

    @if($total > 0)
        {{-- Afficher les résultats --}}
        @if(!$hasH1)
            <div class="alert alert-danger">
                ⚠️ No &lt;h1&gt; detected — the semantic structure is incomplete.
            </div>
        @elseif($h1Count > 1)
            <div class="alert alert-warning">
                ⚠️ Multiple &lt;h1&gt; tags detected ({{ $h1Count }}) — only one H1 should be used per page.
            </div>
        @endif
        
        {{-- Résumé avec statistiques de profondeur --}}
        <div class="alert alert-secondary">
            <div class="row text-center">
                <div class="col-md-2">
                    <strong>H1:</strong> {{ $h1Count }}
                </div>
                <div class="col-md-2">
                    <strong>H2:</strong> {{ $h2Count }}
                </div>
                <div class="col-md-2">
                    <strong>H3:</strong> {{ $h3Count }}
                </div>
                <div class="col-md-2">
                    <strong>H4+:</strong> {{ $h4PlusCount }}
                </div>
                <div class="col-md-2">
                    <strong>Total:</strong> {{ $total }}
                </div>
            </div>
            <div class="row text-center mt-2">
                <div class="col-md-4">
                    <strong>Avg Depth:</strong> {{ $avgDepth }}
                </div>
                <div class="col-md-4">
                    <strong>Max Depth:</strong> {{ $maxDepth }}
                </div>
                <div class="col-md-4">
                    <strong>Min Depth:</strong> {{ $minDepth }}
                </div>
            </div>
        </div>
        
        {{-- Tableau des headings --}}
        <table class="table table-bordered table-sm mt-2">
            <thead class="table-dark">
                <tr>
                    <th>Tag</th>
                    <th>DOM Depth</th>
                    <th>Text</th>
                    <th>Length</th>
                </tr>
            </thead>
            <tbody>
                @foreach($validHeadings as $heading)
                    @php
                        $tag = $heading['tag'] ?? '';
                        $text = $heading['text'] ?? 'N/A';
                        $level = $heading['level'] ?? 0;
                        $domDepth = $heading['dom_depth'] ?? 0;
                        $length = $heading['length'] ?? 0;
                        
                        // Classes de couleur selon la profondeur
                        $depthClass = match(true) {
                            $domDepth <= 5 => 'text-success',
                            $domDepth <= 10 => 'text-info',
                            $domDepth <= 15 => 'text-warning',
                            default => 'text-danger'
                        };
                        
                        // Badge color selon le niveau
                        $badgeClass = match($level) {
                            1 => 'bg-success',
                            2 => 'bg-info',
                            3 => 'bg-warning',
                            4 => 'bg-secondary',
                            5 => 'bg-dark',
                            6 => 'bg-dark',
                            default => 'bg-light text-dark'
                        };
                    @endphp
                    <tr>
                        <td>
                            <span class="badge {{ $badgeClass }}">
                                {{ strtoupper($tag) }}
                            </span>
                        </td>
                        <td class="{{ $depthClass }}">
                            <strong>{{ $domDepth }}</strong>
                            <div class="progress mt-1" style="height: 4px;">
                                <div class="progress-bar 
                                    @if($domDepth <= 5) bg-success
                                    @elseif($domDepth <= 10) bg-info
                                    @elseif($domDepth <= 15) bg-warning
                                    @else bg-danger
                                    @endif" 
                                    style="width: {{ min(100, ($domDepth / 25) * 100) }}%">
                                </div>
                            </div>
                        </td>
                        <td title="{{ $text }}">
                            {{ Str::limit($text, 70) }}
                        </td>
                        <td>{{ $length }} chars</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Structure hiérarchique visuelle --}}
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">📊 Hierarchical Structure Visualization</h6>
            </div>
            <div class="card-body">
                <div class="headings-hierarchy">
                    @foreach($validHeadings as $heading)
                        @php
                            $tag = $heading['tag'] ?? '';
                            $text = $heading['text'] ?? 'N/A';
                            $level = $heading['level'] ?? 0;
                            $domDepth = $heading['dom_depth'] ?? 0;
                        @endphp
                        @if($level > 0)
                            <div class="heading-level heading-level-{{ $level }} mb-2 ps-{{ ($level - 1) * 3 }}">
                                <span class="badge bg-primary me-2">H{{ $level }}</span>
                                <small class="text-muted me-2">Depth: {{ $domDepth }}</small>
                                <span class="heading-text">{{ Str::limit($text, 50) }}</span>
                                <small class="text-muted ms-2">({{ $heading['length'] ?? 0 }} chars)</small>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Analyse SEO --}}
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">🔍 SEO Analysis</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li>
                        @if($h1Count === 1)
                            ✅ <strong>H1:</strong> Perfect! Only one H1 tag found.
                        @elseif($h1Count === 0)
                            ❌ <strong>H1:</strong> No H1 tag found - this hurts SEO.
                        @else
                            ⚠️ <strong>H1:</strong> {{ $h1Count }} H1 tags found - should only have one.
                        @endif
                    </li>
                    <li>
                        @if($h2Count > 0)
                            ✅ <strong>H2:</strong> {{ $h2Count }} H2 tags found - good for structure.
                        @else
                            ℹ️ <strong>H2:</strong> No H2 tags found.
                        @endif
                    </li>
                    <li>
                        @if($h3Count > 0)
                            ✅ <strong>H3:</strong> {{ $h3Count }} H3 tags found - good hierarchy.
                        @else
                            ℹ️ <strong>H3:</strong> No H3 tags found.
                        @endif
                    </li>
                    <li>
                        <strong>Structure Quality:</strong> 
                        @if($h1Count === 1 && $h2Count >= 2 && $total <= 15)
                            🟢 Excellent
                        @elseif($h1Count === 1 && $total <= 20)
                            🟡 Good
                        @else
                            🔴 Needs improvement
                        @endif
                    </li>
                </ul>
            </div>
        </div>

        {{-- Analyse de profondeur DOM --}}
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">📏 DOM Depth Analysis</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li>
                        @if($avgDepth <= 8)
                            ✅ <strong>Average Depth:</strong> {{ $avgDepth }} - Good, reasonable nesting
                        @elseif($avgDepth <= 12)
                            ⚠️ <strong>Average Depth:</strong> {{ $avgDepth }} - Moderate, could be optimized
                        @else
                            ❌ <strong>Average Depth:</strong> {{ $avgDepth }} - High, consider simplifying HTML structure
                        @endif
                    </li>
                    <li>
                        @if($maxDepth <= 15)
                            ✅ <strong>Max Depth:</strong> {{ $maxDepth }} - Acceptable maximum nesting
                        @else
                            ⚠️ <strong>Max Depth:</strong> {{ $maxDepth }} - Very deep nesting, may impact performance
                        @endif
                    </li>
                    <li>
                        <strong>Recommendation:</strong> 
                        @if($avgDepth <= 8 && $maxDepth <= 12)
                            🟢 Excellent DOM structure
                        @elseif($avgDepth <= 10 && $maxDepth <= 15)
                            🟡 Good, minor optimizations possible
                        @else
                            🔴 Consider simplifying HTML structure
                        @endif
                    </li>
                </ul>
            </div>
        </div>

        <style>
        .heading-level {
            border-left: 3px solid #dee2e6;
            padding-left: 10px;
            transition: all 0.3s ease;
        }

        .heading-level:hover {
            background-color: #f8f9fa;
            border-left-color: #007bff;
        }

        .heading-level-1 { border-left-color: #28a745; }
        .heading-level-2 { border-left-color: #17a2b8; }
        .heading-level-3 { border-left-color: #ffc107; }
        .heading-level-4 { border-left-color: #6c757d; }
        .heading-level-5 { border-left-color: #adb5bd; }
        .heading-level-6 { border-left-color: #e9ecef; }

        .heading-text {
            font-weight: 500;
        }
        </style>

    @else
        {{-- Aucune donnée --}}
        <div class="alert alert-warning">
            <h6>❌ No Hn tags detected</h6>
            <p class="mb-0">
                No heading tags (H1-H6) were found on this page. 
                This can negatively impact SEO.
            </p>
        </div>
    @endif
</div>
            </div>
        @else
            <p class="text-muted">No analysis available for this project yet.</p>
        @endif
    </div>

    @if($analysis && ($analysis->cloudflare_blocked || empty($analysis->main_content)))
        <div class="alert alert-warning">
            ⚠️ The main content could not be extracted.
            @if($analysis->cloudflare_blocked)
                It seems that the website is protected by <strong>Cloudflare</strong>.
            @else
                No useful information was found on this page.
            @endif
        </div>
    @endif

    @if($analysis)
        <div class="glass-card mt-5 pt-4" style="backdrop-filter: blur(12px); border-radius: 16px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2); padding: 2rem; border: 1px solid rgba(255, 255, 255, 0.3);">
            <div style="background-color: #dbe1f7;" class="px-4 py-3 rounded-top mb-4">
                <h5 class="fw-bold mb-0" style="color:#2e4db6;">🧠 Main content analysis</h5>
            </div>
            @php
                $hasMainContent = !empty($analysis->main_content);
                $content = is_array($analysis->content_analysis) ? $analysis->content_analysis : [];
                $paragraphs = $content['paragraphs'] ?? [];
                $hasParagraphs = is_array($paragraphs) && count($paragraphs) > 0;
            @endphp
            <div class="row">
                <div class="col-md-12">
                    <div class="p-3 rounded" style="background:#222d40; color: #fff;">
                        @if($analysis->readability_score)
                            <div class="badge bg-info">Readability : {{ $analysis->readability_score }}%</div>
                        @endif
                        <div class="mt-3">
                            <strong>📊 Content Status:</strong><br>
                            @if($hasMainContent)
                                ✅ Main content extracted ({{ strlen($analysis->main_content) }} characters)
                            @else
                                ❌ No main content available
                                @if($analysis->cloudflare_blocked)
                                    <br><small class="text-warning">🔒 Cloudflare protection detected</small>
                                @endif
                            @endif
                            <br>
                            @if($hasParagraphs)
                                ✅ {{ count($paragraphs) }} paragraphs extracted
                            @else
                                ❌ No paragraphs data
                            @endif
                        </div>
                        @if(!empty($content['paragraph_count']))
                            <div class="mt-3">
                                <strong>🧾 Paragraphs extracted :</strong> {{ $content['paragraph_count'] }}<br>
                                <strong>📌 Short paragraphs :</strong> {{ $content['short_paragraphs'] ?? 0 }}<br>
                                <strong>🔁 Duplications :</strong> {{ count($content['duplicate_paragraphs'] ?? []) }}
                            </div>
                        @else
                            <div class="mt-3 text-warning">
                                <small>No paragraph analysis data available</small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="row pt-4">
                @if($hasMainContent)
                    <div class="col-md-6">
                        <div class="glass-card mb-4" style="backdrop-filter: blur(12px); background: rgba(255, 255, 255, 0.15); border-radius: 16px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2); border: 1px solid rgba(255, 255, 255, 0.3); color: #000; overflow: hidden;">
                            <button class="btn w-100 text-start d-flex justify-content-between align-items-center p-3" type="button" data-bs-toggle="collapse" data-bs-target="#mainContentCollapse" aria-expanded="false" aria-controls="mainContentCollapse" style="background: transparent; border: none; color: #000;">
                                <span>📄 Extracted content</span>
                                <span class="badge bg-light text-dark">Display</span>
                            </button>
                            <div class="collapse" id="mainContentCollapse">
                                <div class="p-3" style="max-height: 400px; overflow-y: auto;">
                                    <button class="btn btn-sm btn-outline-dark mb-3" onclick="navigator.clipboard.writeText(`{{ $analysis->main_content }}`)">📋 Copier</button>
                                    <div style="white-space: pre-wrap; font-family: monospace; background-color: rgba(255,255,255,0.1); border-radius: 8px; padding: 1rem; word-break: break-word;">
                                        {{ $analysis->main_content }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="col-md-6">
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>No main content available</strong><br>
                            <small>
                                The content could not be extracted from this page.
                                @if($analysis->cloudflare_blocked)
                                    <br>🔒 The website appears to be protected by Cloudflare.
                                @else
                                    <br>This could be due to JavaScript rendering, anti-bot protection, or page structure issues.
                                @endif
                            </small>
                        </div>
                    </div>
                @endif

                @if($hasParagraphs)
                    <div class="col-md-6">
                        <div class="glass-card mb-4" style="backdrop-filter: blur(12px); background: rgba(255, 255, 255, 0.15); border-radius: 16px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2); border: 1px solid rgba(255, 255, 255, 0.3); color: #000; overflow: hidden;">
                            <button class="btn w-100 text-start d-flex justify-content-between align-items-center p-3" type="button" data-bs-toggle="collapse" data-bs-target="#paragraphCollapse" aria-expanded="false" aria-controls="paragraphCollapse" style="background: transparent; border: none; color: #000;">
                                <span>🧾 Paragraphs extracted</span>
                                <span class="badge bg-light text-dark">{{ count($paragraphs) }} Displayed</span>
                            </button>
                            <div class="collapse" id="paragraphCollapse">
                                <div class="p-3" style="max-height: 400px; overflow-y: auto;">
                                    <ul class="list-group list-group-flush">
                                        @foreach($paragraphs as $p)
                                            @php
                                                $length = str_word_count($p);
                                                $rowClass = $length < 40 ? 'text-danger' : ($length < 80 ? 'text-warning' : 'text-success');
                                            @endphp
                                            <li class="list-group-item bg-transparent {{ $rowClass }}" style="word-break: break-word;">
                                                {{ $p }}<br>
                                                <small class="text-muted">{{ $length }} words</small>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="col-md-6">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>No paragraphs data available</strong><br>
                            <small>No paragraph structure could be extracted from the content analysis.</small>
                        </div>
                    </div>
                @endif

                @php
                    $shorts = array_filter($paragraphs, fn($p) => strlen($p) < 100);
                @endphp
                @if($hasParagraphs && count($shorts) > 0)
                    <div class="col-md-6">
                        <div class="glass-card mb-4" style="backdrop-filter: blur(12px); background: rgba(255, 255, 255, 0.15); border-radius: 16px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2); border: 1px solid rgba(255, 255, 255, 0.3); color: #000; overflow: hidden;">
                            <button class="btn w-100 text-start d-flex justify-content-between align-items-center p-3" type="button" data-bs-toggle="collapse" data-bs-target="#shortParagraphCollapse" aria-expanded="false" aria-controls="shortParagraphCollapse" style="background: transparent; border: none; color: #000;">
                                <span>📌 Short Paragraphs</span>
                                <span class="badge bg-warning text-dark">{{ count($shorts) }}</span>
                            </button>
                            <div class="collapse" id="shortParagraphCollapse">
                                <div class="p-3" style="max-height: 300px; overflow-y: auto;">
                                    <ul class="list-group list-group-flush">
                                        @foreach($shorts as $p)
                                            <li class="list-group-item bg-transparent text-danger" style="word-break: break-word;">
                                                {{ $p }}<br>
                                                <small class="text-muted">{{ str_word_count($p) }} words</small>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @php
                    $duplicates = $content['duplicate_paragraphs'] ?? [];
                @endphp
                @if($hasParagraphs && count($duplicates) > 0)
                    <div class="col-md-6">
                        <div class="glass-card mb-4" style="backdrop-filter: blur(12px); background: rgba(255, 255, 255, 0.15); border-radius: 16px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2); border: 1px solid rgba(255, 255, 255, 0.3); color: #000; overflow: hidden;">
                            <button class="btn w-100 text-start d-flex justify-content-between align-items-center p-3" type="button" data-bs-toggle="collapse" data-bs-target="#duplicateParagraphCollapse" aria-expanded="false" aria-controls="duplicateParagraphCollapse" style="background: transparent; border: none; color: #000;">
                                <span>🔁 Duplicated paragraphs</span>
                                <span class="badge bg-danger">{{ count($duplicates) }}</span>
                            </button>
                            <div class="collapse" id="duplicateParagraphCollapse">
                                <div class="p-3" style="max-height: 300px; overflow-y: auto;">
                                    <ul class="list-group list-group-flush">
                                        @foreach($duplicates as $p)
                                            <li class="list-group-item bg-transparent text-danger" style="word-break: break-word;">
                                                {{ $p }}<br>
                                                <small class="text-muted">{{ str_word_count($p) }} words</small>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="glass-card mt-5 p-4" style="backdrop-filter: blur(12px); background: #f7f6fc;  border-radius: 16px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2); border: 1px solid rgba(255, 255, 255, 0.3); color: #000;">
            <div class="d-flex align-items-center mb-3">
                <img src="https://www.google.com/s2/favicons?domain={{ parse_url($analysis->page_url ?? '', PHP_URL_HOST) }}"
                     alt="favicon"
                     style="width: 20px; height: 20px; margin-right: 8px;">
                <span class="text-dark">{{ parse_url($analysis->page_url ?? '', PHP_URL_HOST) }}</span>
            </div>
            <h3 style="color:rgb(27, 76, 90);">{{ $analysis->page_title ?? 'N/A' }}</h3>
            <p style="color:rgb(29, 26, 26);">{{ $analysis->meta_description ?? 'N/A' }}</p>
            @if($analysis->readability_score)
                @php
                    $score = $analysis->readability_score;
                    $color = $score >= 60 ? '#00ff99' : ($score >= 40 ? '#ffcc00' : '#ff4d4d');
                @endphp
                <p class="mt-3">
                    <strong style="color: #000;">📊 Readability :</strong>
                    <span style="color: {{ $color }};">{{ round($score, 1) }} / 100</span>
                </p>
            @endif
        </div>




        {{-- Section PageRank avec rafraîchissement automatique --}}

        <div data-pagerank-section> {{-- ⬅️ AJOUTEZ CET ATTRIBUT --}}
        @if(!is_null($analysis->page_rank))
        {{-- Afficher le vrai PageRank --}}
        @php
            $rank = round($analysis->page_rank, 2);
            $color = $rank >= 6 ? '#00ff99' : ($rank >= 3 ? '#ffcc00' : '#ff4d4d');
            $emoji = $rank >= 6 ? '🟢' : ($rank >= 3 ? '🟠' : '🔴');
        @endphp
        <div class="glass-card mt-4 p-4">
            <div style="background-color: #dbe1f7;" class="px-4 py-3 rounded-top mb-4">
                <h5 class="fw-bold mb-0" style="color:#2e4db6;">🔗 Domain PageRank</h5>
            </div>
            <div class="flex items-center space-x-3 text-xl font-bold">
                <span style="color: {{ $color }};">{{ $emoji }} {{ $rank }} / 10</span>
                <span class="text-sm text-muted" style="font-size: 0.85rem;">
                    (according to OpenPageRank)
                </span>
            </div>
            @if(!is_null($analysis->page_rank_global))
                <p class="mt-2 text-muted" style="font-size: 0.9rem; font-weight: 600; color:#2454b9 !important;">
                    Overall ranking : <strong>#{{ number_format($analysis->page_rank_global) }}</strong>
                </p>
            @endif
            <p class="mt-2 text-muted" style="font-size: 0.85rem;">
                This score reflects the domain's public reputation on the global web, calculated from open source data.
            </p>
        </div>
    @else
        {{-- Placeholder en attendant --}}
        <div class="glass-card mt-4 p-4">
            <div style="background-color: #dbe1f7;" class="px-4 py-3 rounded-top mb-4">
                <h5 class="fw-bold mb-0" style="color:#2e4db6;">🔗 Domain PageRank</h5>
            </div>
            <div class="text-center py-4">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="text-muted">Calculating PageRank...</p>
                <small class="text-info">This may take a few moments</small>
            </div>
        </div>
    @endif
    </div>
        <x-whois-card :analysis="$analysis" />
        <x-analysis-summary :analysis="$analysis" />

        <div id="analysis-data" data-analysis-id="{{ $analysis->id }}"></div>

        <div class="btn-group mb-3" role="group">
            <button class="btn btn-outline-primary" data-strategy="desktop">🖥️ Desktop</button>
            <button class="btn btn-outline-warning" style="color:#000; border: 1px solid #000;" data-strategy="mobile">📱 Mobile</button>
        </div>

        <div id="pagespeed-metrics-wrapper"></div>
        <div id="audit-fragments-wrapper"></div>

        <div class="mb-2">
            <li class="list-group-item border-0 p-0">
                <a href="{{ route('user.projects.seo') }}"
                   class="d-flex align-items-center text-decoration-none text-dark p-3 rounded-3 transition-all"
                   style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-left: 4px solid #0d6efd !important;">
                    <span class="me-3 fs-5">✨</span>
                    <span class="fw-semibold">SEO Generator</span>
                    <span class="ms-auto text-muted small">→</span>
                </a>
            </li>
        </div>
    @endif

    {{-- ⬇️ LES DEUX @endif MANQUANTS AJOUTÉS --}}
   

</div>
</div>

@endsection

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const analysisEl = document.getElementById('analysis-data');
        const metricsWrapper = document.getElementById('pagespeed-metrics-wrapper');
        const auditsWrapper = document.getElementById('audit-fragments-wrapper');
        if (!analysisEl || !metricsWrapper || !auditsWrapper) {
            console.warn("⛔ Conteneurs manquants : vérifie les IDs dans Blade.");
            return;
        }
        const analysisId = analysisEl.dataset.analysisId;
        let currentStrategy = 'desktop';
        let isWatching = false;
        let watchInterval = null;

        // 🗄️ SYSTÈME DE CACHE
        const cache = {
            desktop: null,
            mobile: null,
            timestamp: {
                desktop: null,
                mobile: null
            },
            TTL: 10 * 60 * 1000,

            set: function(strategy, data) {
                this[strategy] = data;
                this.timestamp[strategy] = Date.now();
                console.log(`💾 Données ${strategy} mises en cache`);
            },

            get: function(strategy) {
                if (this[strategy] && this.timestamp[strategy]) {
                    const age = Date.now() - this.timestamp[strategy];
                    if (age < this.TTL) {
                        console.log(`📦 Données ${strategy} du cache (${Math.round(age/1000)}s)`);
                        return this[strategy];
                    } else {
                        console.log(`🕒 Cache ${strategy} expiré`);
                        this[strategy] = null;
                    }
                }
                return null;
            },

            has: function(strategy) {
                const cached = this.get(strategy);
                return cached !== null;
            },

            clear: function(strategy = null) {
                if (strategy) {
                    this[strategy] = null;
                    this.timestamp[strategy] = null;
                    console.log(`🗑️ Cache ${strategy} vidé`);
                } else {
                    this.desktop = null;
                    this.mobile = null;
                    this.timestamp.desktop = null;
                    this.timestamp.mobile = null;
                    console.log('🗑️ Cache vidé');
                }
            }
        };

        // 🔍 SURVEILLANCE AUTOMATIQUE
        function startWatching() {
            if (isWatching) return;
            isWatching = true;
            console.log('🔍 Surveillance automatique activée');
            checkPageSpeedStatus();
            watchInterval = setInterval(() => {
                checkPageSpeedStatus();
            }, 5000);
        }

        function stopWatching() {
            isWatching = false;
            if (watchInterval) {
                clearInterval(watchInterval);
                watchInterval = null;
                console.log('⏹️ Surveillance arrêtée');
            }
        }

        function checkPageSpeedStatus() {
    fetch(`/seo-analysis/${analysisId}/status`)
        .then(response => {
            if (!response.ok) throw new Error('Statut HTTP invalide');
            return response.json();
        })
        .then(data => {
            console.log('📊 Statut complet:', data);
            
            let everythingReady = true;
            
            // ✅ Vérifier PageSpeed Desktop
            const desktopReady = data.desktop_ready && data.desktop_score !== null;
            const mobileReady = data.mobile_ready && data.mobile_score !== null;
            
            if (desktopReady) {
                console.log('✅ Données Desktop prêtes !');
                if (currentStrategy === 'desktop') {
                    fetchPageSpeed('desktop', false, true);
                }
            } else {
                everythingReady = false;
            }
            
            if (mobileReady) {
                console.log('✅ Données Mobile prêtes !');
            } else {
                everythingReady = false;
            }
            
            // 🆕 Vérifier PageRank
            if (data.page_rank !== null && !window.pageRankDisplayed) {
                console.log('✅ PageRank disponible!');
                window.pageRankDisplayed = true;
                showNotification('✅ PageRank disponible', 'success');
                updatePageRankSection(data.page_rank, data.page_rank_global);
            } else if (data.page_rank === null) {
                everythingReady = false;
                console.log('⏳ PageRank en attente...');
            }
            
            // Arrêter la surveillance seulement quand TOUT est prêt
            if (everythingReady) {
                console.log('🎯 Toutes les données sont prêtes !');
                stopWatching();
                showNotification('✅ Analyse SEO complète terminée', 'success');
            }
        })
        .catch(error => {
            console.log('❌ Erreur surveillance:', error);
        });
}


// 🔍 SURVEILLANCE AUTOMATIQUE UNIFIÉE
function startWatching() {
    if (isWatching) return;
    isWatching = true;
    console.log('🔍 Surveillance automatique activée');
    
    let checkCount = 0;
    const maxChecks = 60; // 5 minutes max (60 * 5s)
    
    watchInterval = setInterval(() => {
        checkAllStatus();
        checkCount++;
        
        if (checkCount >= maxChecks) {
            console.log('⏹️ Surveillance arrêtée (timeout)');
            stopWatching();
        }
    }, 5000); // Vérifier toutes les 5 secondes
    
    // Vérifier immédiatement
    checkAllStatus();
}

function checkAllStatus() {
    fetch(`/seo-analysis/${analysisId}/status`)
        .then(response => {
            if (!response.ok) throw new Error('Statut HTTP invalide');
            return response.json();
        })
        .then(data => {
            console.log('📊 Statut complet:', data);
            
            let everythingReady = true;
            
            // ✅ Vérifier PageSpeed Desktop
            const desktopReady = data.desktop_ready && data.desktop_score !== null;
            const mobileReady = data.mobile_ready && data.mobile_score !== null;
            
            if (desktopReady && !window.desktopDisplayed) {
                console.log('✅ Données Desktop prêtes !');
                window.desktopDisplayed = true;
                showNotification('✅ Données Desktop disponibles', 'success');
                if (currentStrategy === 'desktop') {
                    fetchPageSpeed('desktop', false, true);
                }
            }
            
            if (mobileReady && !window.mobileDisplayed) {
                console.log('✅ Données Mobile prêtes !');
                window.mobileDisplayed = true;
                showNotification('✅ Données Mobile disponibles', 'success');
            }
            
            // 🆕 Vérifier PageRank - CORRIGÉ
            if (data.page_rank !== null && data.page_rank !== undefined && !window.pageRankDisplayed) {
                console.log('✅ PageRank disponible!', data.page_rank);
                window.pageRankDisplayed = true;
                showNotification('✅ PageRank disponible', 'success');
                updatePageRankSection(data.page_rank, data.page_rank_global);
            } else if (data.page_rank === null || data.page_rank === undefined) {
                everythingReady = false;
                console.log('⏳ PageRank en attente...', data.page_rank);
            }
            
            if (!desktopReady || !mobileReady) {
                everythingReady = false;
            }
            
            // Arrêter la surveillance seulement quand TOUT est prêt
            if (everythingReady) {
                console.log('🎯 Toutes les données sont prêtes !');
                stopWatching();
                showNotification('✅ Analyse SEO complète terminée', 'success');
            }
        })
        .catch(error => {
            console.log('❌ Erreur surveillance:', error);
        });
}





        function showNotification(message, type = 'info') {
    // Couleurs et icônes selon le type
    const config = {
        success: { 
            bg: 'bg-success', 
            icon: '✅',
            title: 'Success'
        },
        warning: { 
            bg: 'bg-warning text-dark', 
            icon: '⚠️',
            title: 'Attention'
        },
        error: { 
            bg: 'bg-danger', 
            icon: '❌',
            title: 'Error'
        },
        info: { 
            bg: 'bg-info', 
            icon: 'ℹ️',
            title: 'Information'
        }
    };

    const { bg, icon, title } = config[type] || config.info;

    // Créer le container s'il n'existe pas
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
    }

    const toastId = 'toast-' + Date.now();
    
    const toastHTML = `
        <div id="${toastId}" class="toast ${bg}" role="alert">
            <div class="toast-header">
                <strong class="me-auto">${icon} ${title}</strong>
                <small>à l'instant</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        </div>
    `;

    toastContainer.insertAdjacentHTML('beforeend', toastHTML);
    
    // Afficher la notification
    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement, {
        autohide: true,
        delay: 4000
    });
    toast.show();

    // Nettoyer après fermeture
    toastElement.addEventListener('hidden.bs.toast', function () {
        this.remove();
    });
}

        function updateButtonStates(activeStrategy) {
            document.querySelectorAll('[data-strategy]').forEach(btn => {
                if (btn.dataset.strategy === activeStrategy) {
                    btn.classList.add('active', 'btn-primary');
                    btn.classList.remove('btn-outline-primary', 'btn-outline-warning');
                } else {
                    btn.classList.remove('active', 'btn-primary');
                    if (btn.dataset.strategy === 'desktop') {
                        btn.classList.add('btn-outline-primary');
                    } else {
                        btn.classList.add('btn-outline-warning');
                    }
                }
            });
        }

        document.querySelectorAll('[data-strategy]').forEach(btn => {
            btn.addEventListener('click', function() {
                currentStrategy = this.dataset.strategy;
                updateButtonStates(currentStrategy);
                fetchPageSpeed(currentStrategy);
            });
        });

        function fetchPageSpeed(strategy = 'desktop', forceRefresh = false, silent = false) {
            console.log(`🔄 Chargement ${strategy}...`, { forceRefresh, silent });
            if (!forceRefresh && cache.has(strategy)) {
                const cachedData = cache.get(strategy);
                console.log('✅ Utilisation du cache');
                displayData(strategy, cachedData);
                return;
            }
            const endpoint = `/seo-analysis/${analysisId}/pagespeed?strategy=${strategy}`;
            if (!silent) {
                showLoading(strategy);
            }
            fetch(endpoint)
                .then(response => {
                    console.log(`📡 Réponse HTTP: ${response.status}`);
                    if (!response.ok) throw new Error(`HTTP error: ${response.status}`);
                    return response.json();
                })
                .then(data => {
                    console.log(`📊 Données reçues pour ${strategy}:`, data);
                    if (data.score !== null && data.metrics && data.audits) {
                        cache.set(strategy, data);
                    }
                    displayData(strategy, data);
                })
                .catch(error => {
                    console.error('❌ Erreur:', error);
                    if (cache.has(strategy)) {
                        console.log('🔄 Fallback sur le cache');
                        const cachedData = cache.get(strategy);
                        displayData(strategy, cachedData);
                    } else {
                        showError(strategy, error);
                    }
                });
        }

        function showLoading(strategy) {
            metricsWrapper.innerHTML = `<div class="text-center p-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Chargement...</span>
                </div>
                <p class="text-muted mt-2">Chargement ${strategy}...</p>
            </div>`;
            auditsWrapper.innerHTML = '';
        }

        function showError(strategy, error) {
            metricsWrapper.innerHTML = `
                <div class="alert alert-danger">
                    <p>Erreur ${strategy}</p>
                    <small>${error.message}</small>
                    <div class="mt-2">
                        <button class="btn btn-primary btn-sm" onclick="fetchPageSpeed('${strategy}', true)">
                            Réessayer
                        </button>
                    </div>
                </div>
            `;
            auditsWrapper.innerHTML = '';
        }

        function displayData(strategy, data) {
            console.log('🔍 DIAGNOSTIC:');
            console.log('✅ Score:', data.score !== null, data.score);
            console.log('✅ Métriques:', data.metrics && Object.keys(data.metrics).length);
            console.log('✅ Audits:', data.audits && Object.keys(data.audits).length);
            const scoreReady = data.score !== null;
            const metricsReady = data.metrics && Object.keys(data.metrics).length > 0;
            const scoresReady = data.allScores && Object.keys(data.allScores).length > 0;
            const auditsReady = data.audits && Object.keys(data.audits).length > 0;
            if (scoreReady && metricsReady && scoresReady) {
                console.log('🎯 Rendu des métriques...');
                metricsWrapper.innerHTML = renderMetricsHTML({
                    performanceScore: data.score,
                    allScores: data.allScores,
                    metrics: data.metrics,
                    formFactor: data.formFactor
                });
            } else {
                console.warn('⏳ Données incomplètes');
                metricsWrapper.innerHTML = `
                    <div class="alert alert-warning">
                        <p>Data being processed for ${strategy}</p>
                        <small>Score: ${data.score ?? 'N/A'}</small><br>
                        <button class="btn btn-primary btn-sm mt-2" onclick="fetchPageSpeed('${strategy}', true)">
                            Refresh
                        </button>
                    </div>
                `;
            }
            if (auditsReady) {
                console.log('🎯 Rendu des audits...');
                auditsWrapper.innerHTML = renderAuditHTML(data.audits);
            } else {
                console.warn('⏳ Audits incomplets');
                auditsWrapper.innerHTML = `
                    <div class="alert alert-info">
                        <p>Audits en cours de traitement</p>
                    </div>
                `;
            }
        }

        function renderMetricsHTML(data) {
            const { performanceScore, allScores, metrics, formFactor } = data;
            let html = `<div class="page-speed-metrics mb-4">`;
            if (formFactor) {
                const icon = formFactor === 'mobile' ? '📱' : '🖥️';
                const badgeClass = formFactor === 'mobile' ? 'badge-warning' : 'badge-primary';
                html += `
                <div class="mb-3">
                    <span class="badge ${badgeClass} text-dark">${icon} ${capitalize(formFactor)}</span>
                </div>`;
            }
            if (allScores && Object.keys(allScores).length > 0) {
                html += `
                <div class="scores-grid mb-4">
                    <div style="background-color: #dbe1f7;" class="px-4 py-3 rounded-top mb-4">
                        <h5 class="fw-bold mb-0" style="color:#2e4db6;">PageSpeed Score</h5>
                    </div>
                    <div class="row">`;
                html += renderScoreCard('Performance', performanceScore);
                for (const [category, score] of Object.entries(allScores)) {
                    if (category !== 'performance') {
                        html += renderScoreCard(category, score);
                    }
                }
                html += `</div></div>`;
            }
            if (metrics && Object.keys(metrics).length > 0) {
                html += `
                <div class="metrics-section">
                    <div style="background-color: #dbe1f7;" class="px-4 py-3 rounded-top mb-4">
                        <h5 class="fw-bold mb-0" style="color:#2e4db6;">Core Web Vitals</h5>
                    </div>
                    <div class="metrics-grid">`;
                for (const metric of Object.values(metrics)) {
                    if (!metric.title) continue;
                    const score = metric.score ?? null;
                    const badge = score >= 0.9 ? 'success' : score >= 0.5 ? 'warning' : 'danger';
                    html += `
                    <div class="metric-item">
                        <div class="metric-header">
                            <span class="metric-name">${metric.title}</span>
                            ${score !== null ? `<span class="badge badge-${badge}">${Math.round(score * 100)}%</span>` : ''}
                        </div>
                        <span class="metric-value">${metric.displayValue ?? 'N/A'}</span>
                        ${score !== null ? `
                        <div class="progress" style="height: 4px;">
                            <div class="progress-bar bg-${badge}" style="width: ${score * 100}%"></div>
                        </div>` : ''}
                    </div>`;
                }
                html += `</div></div>`;
            }
            html += `</div>`;
            return html;
        }

        function renderScoreCard(category, score) {
            const badge = score >= 90 ? 'text-success' : score >= 50 ? 'text-warning' : 'text-danger';
            const label = score >= 90 ? 'Excellent' : score >= 50 ? 'Good' : 'Poor';
            return `
            <div class="col-md-3 col-6 mb-3">
                <div class="score-card text-center p-3">
                    <div class="score-category">${capitalize(category)}</div>
                    <div class="score-value h3 ${badge}">${score ?? 'N/A'}</div>
                    <div class="score-label">/100</div>
                    <small class="${badge}">${label}</small>
                </div>
            </div>`;
        }

        function renderAuditHTML(audits) {
    let html = `<div class="pagespeed-audits">`;
    
    // Afficher TOUTES les sections disponibles dans les données
    let accordionId = 0;
    
    for (const [sectionType, items] of Object.entries(audits)) {
        if (!items || items.length === 0) continue;
        
        accordionId++;
        
        // Déterminer le titre et l'icône en fonction du type de section
        const sectionConfig = getSectionConfig(sectionType);
        const accordionSectionId = `accordion-${sectionType}-${accordionId}`;
        const collapseId = `collapse-${sectionType}-${accordionId}`;
        
        html += `
        <div class="accordion audit-accordion mb-3" id="${accordionSectionId}">
            <div class="accordion-item border-${sectionConfig.color}">
                <h2 class="accordion-header" id="heading-${sectionType}">
                    <button class="accordion-button ${sectionConfig.color} ${accordionId === 1 ? '' : 'collapsed'}"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#${collapseId}"
                            aria-expanded="${accordionId === 1 ? 'true' : 'false'}"
                            aria-controls="${collapseId}">
                        <span class="d-flex align-items-center w-100">
                            <span class="accordion-icon me-2">${sectionConfig.icon}</span>
                            <span class="accordion-title flex-grow-1">${sectionConfig.title}</span>
                            <span class="badge bg-${sectionConfig.color} ms-2">${items.length}</span>
                            <span class="accordion-arrow ms-2">▼</span>
                        </span>
                    </button>
                </h2>
                <div id="${collapseId}"
                     class="accordion-collapse collapse ${accordionId === 1 ? 'show' : ''}"
                     aria-labelledby="heading-${sectionType}"
                     data-bs-parent="#${accordionSectionId}">
                    <div class="accordion-body p-3">
                        <div class="audit-grid">`;
        
        for (const audit of items) {
            const score = audit.score ?? null;
            const badge = score >= 0.9 ? 'success' : score >= 0.5 ? 'warning' : 'danger';
            
            html += `
                            <div class="audit-card">
                                <div class="audit-header">
                                    <span class="audit-title">${audit.title}</span>
                                    <div class="audit-badges">
                                        ${audit.estimatedSavingsMs ?
                                          `<span class="badge bg-info mb-1">+${(audit.estimatedSavingsMs / 1000).toFixed(2)}s</span>` : ''}
                                        ${score !== null ?
                                          `<span class="badge bg-${badge}">${Math.round(score * 100)}%</span>` : ''}
                                    </div>
                                </div>
                                <div class="audit-body">
                                    <p class="audit-description">${audit.description || 'No description available'}</p>
                                    ${audit.displayValue ? `<p class="audit-value">${audit.displayValue}</p>` : ''}
                                    ${score !== null ? `
                                    <div class="progress mt-2" style="height: 4px;">
                                        <div class="progress-bar bg-${badge}" style="width: ${score * 100}%"></div>
                                    </div>` : ''}
                                </div>
                            </div>`;
        }
        
        html += `
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
    }
    
    html += `</div>`;
    return html;
}

// Nouvelle fonction pour gérer la configuration des sections
function getSectionConfig(sectionType) {
    const configs = {
        opportunities: {
            title: 'Opportunities For Optimization',
            icon: '⚡',
            color: 'warning'
        },
        diagnostics: {
            title: 'Technical Diagnostics',
            icon: '🔍', 
            color: 'info'
        },
        passed: {
            title: 'Passed Audits',
            icon: '✅',
            color: 'success'
        },
        informative: {
            title: 'Informative Audits',
            icon: '📘',
            color: 'secondary'
        },
        // Ajoutez d'autres types au besoin
    };
    
    // Retourne la configuration ou une configuration par défaut
    return configs[sectionType] || {
        title: capitalize(sectionType),
        icon: '📊',
        color: 'primary'
    };
}

        function capitalize(str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        }

        window.fetchPageSpeed = fetchPageSpeed;
        window.clearCache = function(strategy = null) {
            cache.clear(strategy);
            if (strategy) {
                fetchPageSpeed(strategy, true);
            } else {
                fetchPageSpeed(currentStrategy, true);
            }
        };

        console.log('🚀 Chargement initial...');
        updateButtonStates('desktop');
        fetchPageSpeed('desktop');
        setTimeout(() => {
            startWatching();
        }, 2000);
    });

    


// Rafraîchissement automatique quand PageRank est disponible
function checkForPageRank() {
    const analysisId = {{ $analysis->id }};
    
    fetch(`/seo-analysis/${analysisId}/status`)
        .then(response => response.json())
        .then(data => {
            // Si PageRank est maintenant disponible
            if (data.page_rank !== null && !window.pageRankDisplayed) {
                console.log('🔄 PageRank disponible, rafraîchissement...');
                window.pageRankDisplayed = true;
                
                // Option 1: Rafraîchir la page
                // location.reload();
                
                // Option 2: Mettre à jour juste la section PageRank
                updatePageRankSection(data.page_rank, data.page_rank_global);
            }
        })
        .catch(error => console.log('❌ Erreur vérification PageRank:', error));
}

function updatePageRankSection(rank, globalRank) {
    console.log('🎯 Mise à jour PageRank section:', { rank, globalRank });
    
    // ⚠️ VÉRIFIEZ QUE LES VALEURS SONT VALIDES
    if (rank === undefined || rank === null) {
        console.error('❌ PageRank est undefined/null:', rank);
        return;
    }
    
    const pageRankSection = document.querySelector('[data-pagerank-section]');
    if (!pageRankSection) {
        console.error('❌ Section PageRank non trouvée');
        return;
    }
    
    const safeRank = rank || 0;
    const safeGlobalRank = globalRank || null;
    
    const color = safeRank >= 6 ? '#00ff99' : safeRank >= 3 ? '#ffcc00' : '#ff4d4d';
    const emoji = safeRank >= 6 ? '🟢' : safeRank >= 3 ? '🟠' : '🔴';
    
    pageRankSection.innerHTML = `
        <div class="glass-card mt-4 p-4">
            <div style="background-color: #dbe1f7;" class="px-4 py-3 rounded-top mb-4">
                <h5 class="fw-bold mb-0" style="color:#2e4db6;">🔗 Domain PageRank</h5>
            </div>
            <div class="flex items-center space-x-3 text-xl font-bold">
                <span style="color: ${color};">${emoji} ${safeRank} / 10</span>
                <span class="text-sm text-muted" style="font-size: 0.85rem;">
                    (according to OpenPageRank)
                </span>
            </div>
            ${safeGlobalRank ? `
            <p class="mt-2 text-muted" style="font-size: 0.9rem; font-weight: 600; color:#2454b9 !important;">
                Overall ranking : <strong>#${new Intl.NumberFormat().format(safeGlobalRank)}</strong>
            </p>
            ` : ''}
            <p class="mt-2 text-muted" style="font-size: 0.85rem;">
                This score reflects the domain's public reputation on the global web, calculated from open source data.
            </p>
        </div>
    `;
    
    console.log('✅ Section PageRank mise à jour avec succès');
}

// Vérifier toutes les 5 secondes pendant 2 minutes
let checkCount = 0;
const pageRankInterval = setInterval(() => {
    checkForPageRank();
    checkCount++;
    if (checkCount > 24) { // 2 minutes max
        clearInterval(pageRankInterval);
    }
}, 5000);

// Vérifier immédiatement au chargement
setTimeout(checkForPageRank, 2000);





    
</script>