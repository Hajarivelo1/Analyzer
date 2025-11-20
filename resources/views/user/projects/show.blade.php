@extends('admin.admin_master')

@section('admin')
<div class="container mt-5">
    <div class="glass-card p-4 mb-5 text-dark">
        
        <div style="background-color: #dbe1f7;" class="px-4 py-3 rounded-top mb-4">
            <h2 class="mb-3 fw-bold" style=" color:#2e4db6;">🗂️ Project: {{ $project->name }}</h2>
        </div>
        
        <p><strong><i class="bi bi-link-45deg text-info me-1"></i>URL:</strong> {{ $project->base_url }}</p>

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
                        <li class="list-group-item bg-transparent text-dark"><strong>Title:</strong> {{ $analysis->page_title }}</li>
                        <li class="list-group-item bg-transparent text-dark"><strong>Meta Description:</strong> {{ $analysis->meta_description }}</li>
                        <li class="list-group-item bg-transparent text-dark"><strong>Word Count:</strong> {{ $analysis->word_count }}</li>
                        <li class="list-group-item bg-transparent text-dark"><strong>Keyword Density:</strong> {{ $analysis->keyword_density }}%</li>
                        <li class="list-group-item bg-transparent text-dark"><strong>Mobile Friendly:</strong> {{ $analysis->mobile_friendly ? '✅ Yes' : '❌ No' }}</li>
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

                {{-- Headings - CORRIGÉ --}}
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

                {{-- Images - CORRIGÉ --}}
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

                {{-- Mots-clés - CORRIGÉ --}}
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

                {{-- Audit Technique --}}
                <div class="tab-pane fade" id="audit" role="tabpanel">
                    @if(is_array($analysis->technical_audit))
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item bg-transparent text-dark">
                                <strong>Title:</strong> {{ $analysis->technical_audit['has_title'] ?? false ? '✅' : '❌' }}
                            </li>
                            <li class="list-group-item bg-transparent text-dark">
                                <strong>Meta Description:</strong> {{ $analysis->technical_audit['has_meta_description'] ?? false ? '✅' : '❌' }}
                            </li>
                            <li class="list-group-item bg-transparent text-dark">
                                <strong>H1:</strong> {{ $analysis->technical_audit['has_h1'] ?? false ? '✅' : '❌' }}
                            </li>
                            <li class="list-group-item bg-transparent text-dark">
                                <strong>Viewport:</strong> {{ $analysis->technical_audit['has_viewport'] ?? false ? '✅' : '❌' }}
                            </li>
                            <li class="list-group-item bg-transparent text-dark">
                                <strong>Canonical:</strong> {{ $analysis->technical_audit['has_canonical'] ?? false ? '✅' : '❌' }}
                            </li>
                            <li class="list-group-item bg-transparent text-dark">
                                <strong>Robots:</strong> {{ $analysis->technical_audit['has_robots'] ?? false ? '✅' : '❌' }}
                            </li>
                            <li class="list-group-item bg-transparent text-dark">
                                <strong>Images sans alt:</strong> {{ $analysis->technical_audit['images_with_missing_alt'] ?? '—' }}
                            </li>
                            <li class="list-group-item bg-transparent text-dark">
                                <strong>Liens internes:</strong> {{ $analysis->technical_audit['internal_links'] ?? '—' }}
                            </li>
                            <li class="list-group-item bg-transparent text-dark">
                                <strong>Balises strong/em:</strong> {{ $analysis->technical_audit['has_strong_or_em'] ?? false ? '✅' : '❌' }}
                            </li>
                            <li class="list-group-item bg-transparent text-dark">
                                <strong>HTTPS activé:</strong> {{ $analysis->https_enabled ? '✅' : '❌' }}
                            </li>
                            <li class="list-group-item bg-transparent text-dark">
                                <strong>Données structurées (JSON-LD):</strong> {{ $analysis->has_structured_data ? '✅' : '❌' }}
                            </li>
                            <li class="list-group-item bg-transparent text-dark">
                                <strong>Noindex détecté:</strong> {{ $analysis->noindex_detected ? ' ✅ Présent' : '⚠️ Absent' }}
                            </li>
                            @php
                                $audit = $analysis->technical_audit ?? [];
                                $loadTime = $audit['load_time'] ?? null;
                            @endphp
                            @if($loadTime !== null)
                                <li class="list-group-item bg-transparent text-dark">
                                    <strong>Temps de chargement :</strong>
                                    <span class="{{ $loadTime < 1 ? 'text-success' : ($loadTime < 2 ? 'text-warning' : 'text-danger') }}">
                                        {{ $loadTime }}s
                                    </span>
                                </li>
                            @else
                                <li class="list-group-item bg-transparent text-dark">
                                    <strong>Load Time:</strong>
                                    {{ $analysis->load_time ? $analysis->load_time . ' seconds' : 'Not measured' }}
                                </li>
                            @endif
                            @php
                                $size = $analysis->html_size;
                                $color = 'text-muted';
                                $label = 'N/A';
                                if ($size) {
                                    $label = number_format($size) . ' bytes';
                                    if ($size < 50000) {
                                        $color = 'text-success';
                                    } elseif ($size < 150000) {
                                        $color = 'text-warning';
                                    } else {
                                        $color = 'text-danger';
                                    }
                                }
                            @endphp
                            <li class="list-group-item bg-transparent text-dark">
                                <strong>📄 HTML Size:</strong>
                                <span class="{{ $color }}">{{ $label }}</span>
                            </li>
                            @php
                                $links = $analysis->total_links;
                                $color = 'text-muted';
                                $label = 'N/A';
                                if ($links !== null) {
                                    $label = number_format($links) . ' links';
                                    if ($links < 10) {
                                        $color = 'text-danger';
                                    } elseif ($links < 50) {
                                        $color = 'text-warning';
                                    } else {
                                        $color = 'text-success';
                                    }
                                }
                            @endphp
                            <li class="list-group-item bg-transparent text-dark">
                                <strong>🔗 Total Links:</strong>
                                <span class="{{ $color }}">{{ $label }}</span>
                            </li>
                            @php
                                $og = $analysis->has_og_tags;
                                $color = $og ? 'text-success' : 'text-danger';
                                $label = $og ? '✅ Present' : '❌ Missing';
                            @endphp
                            <li class="list-group-item bg-transparent text-dark">
                                <strong>📣 Open Graph Tags:</strong>
                                <span class="{{ $color }}">{{ $label }}</span>
                            </li>
                            <li class="list-group-item bg-transparent text-dark">
                                <strong>🌍 Document Language:</strong>
                                <span class="text-primary">
                                    {{ $analysis->html_lang ?? 'Not specified' }}
                                </span>
                            </li>
                            @php
                                $favicon = $analysis->has_favicon;
                                $color = $favicon ? 'text-success' : 'text-danger';
                                $label = $favicon ? '✅ Yes' : '❌ No';
                            @endphp
                            <li class="list-group-item bg-transparent text-dark">
                                <strong>🖼️ Favicon Detected:</strong>
                                <span class="{{ $color }}">{{ $label }}</span>
                            </li>
                        </ul>
                    @else
                        <div class="alert alert-warning mt-3">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>Audit technique non disponible pour cette analyse.
                        </div>
                    @endif
                </div>

                {{-- Audit Structure - CORRIGÉ --}}
                <div class="tab-pane fade" id="audit-structure" role="tabpanel" aria-labelledby="audit-structure-tab">
                    <h5 class="mt-3">Structure of Hn tags</h5>
                    @php
                        $headings = $analysis->headings_structure ?? [];
                        $hasH1 = false;
                        $total = count($headings);
                        $maxDepth = 0;
                        $sumDepth = 0;
                    @endphp
                    @if(is_array($headings) && $total > 0)
                        @foreach($headings as $h)
                            @php
                                $hasH1 = $hasH1 || strtolower($h['tag'] ?? '') === 'h1';
                                $maxDepth = max($maxDepth, $h['depth'] ?? 0);
                                $sumDepth += $h['depth'] ?? 0;
                            @endphp
                        @endforeach
                        @if(!$hasH1)
                            <div class="alert alert-danger">
                                ⚠️ No &lt;h1&gt; detected — the semantic structure is incomplete.
                            </div>
                        @endif
                        <div class="alert alert-secondary">
                            <strong>Total :</strong> {{ $total }} Hn tags |
                            <strong>Average depth:</strong> {{ $total > 0 ? round($sumDepth / $total, 1) : 0 }} |
                            <strong>Max. depth :</strong> {{ $maxDepth }}
                        </div>
                        <table class="table table-bordered table-sm mt-2">
                            <thead class="table-dark">
                                <tr>
                                    <th>Tag</th>
                                    <th>Text</th>
                                    <th>DOM depth</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($headings as $heading)
                                    @php
                                        $depth = $heading['depth'] ?? 0;
                                        $tag = strtolower($heading['tag'] ?? '');
                                        $rowClass = match(true) {
                                            $depth <= 5 => 'table-success',
                                            $depth <= 10 => 'table-info',
                                            default => 'table-warning'
                                        };
                                    @endphp
                                    <tr class="{{ $rowClass }}">
                                        <td><span class="badge bg-primary">{{ strtoupper($tag) }}</span></td>
                                        <td>{{ $heading['text'] ?? 'N/A' }}</td>
                                        <td>{{ $depth }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted">Aucune balise Hn détectée ou structure vide.</p>
                    @endif
                </div>
            </div>
        @else
            <p class="text-muted">No analysis available for this project yet.</p>
        @endif
    </div>

    {{-- ✅ Message d'alerte si Cloudflare bloque le contenu --}}
    @if($analysis && ($analysis->cloudflare_blocked || empty($analysis->main_content)))
        <div class="alert alert-warning">
            ⚠️ The main content could not be extracted.
            @if($analysis->cloudflare_blocked)
            It seems that the website is protected by<strong>Cloudflare</strong>.
            @else
            No useful information was found on this page.
            @endif
        </div>
    @endif

    @if($analysis)
    <div class="glass-card mt-5 pt-4" style="backdrop-filter: blur(12px); border-radius: 16px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2); padding: 2rem; border: 1px solid rgba(255, 255, 255, 0.3);">
       
        <div style="background-color: #dbe1f7;" class="px-4 py-3 rounded-top mb-4">
            <h5 class="fw-bold mb-0" style=" color:#2e4db6;">🧠 Main content analysis</h5>
        </div>
        
        {{-- Vérification du contenu principal --}}
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
                    
                    {{-- Statut du contenu --}}
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
            {{-- Main Content --}}
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
            
            {{-- Paragraphs --}}
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

            {{-- Short Paragraphs --}}
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

            {{-- Duplicated Paragraphs --}}
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

@else
    {{-- Message si pas de contenu --}}
    @if($analysis)
        <div class="alert alert-warning mt-4">
            <i class="bi bi-exclamation-triangle me-2"></i>
            No main content available for analysis.
            @if($analysis->cloudflare_blocked)
                <br><small>The website may be protected by Cloudflare.</small>
            @endif
        </div>
    @endif
@endif

    @if($analysis)
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

        @if(!is_null($analysis->page_rank))
            @php
                $rank = round($analysis->page_rank, 2);
                $color = $rank >= 6 ? '#00ff99' : ($rank >= 3 ? '#ffcc00' : '#ff4d4d');
                $emoji = $rank >= 6 ? '🟢' : ($rank >= 3 ? '🟠' : '🔴');
            @endphp

            <div class="glass-card mt-4 p-4" style="
                backdrop-filter: blur(12px);
                background: #f7f6fc;
                border-radius: 16px;
                box-shadow: 0 8px 32px rgba(0,0,0,0.2);
                border: 1px solid rgba(255,255,255,0.3);
                color: #000;
                margin-bottom: 20px;
            ">

                <div style="background-color: #dbe1f7;" class="px-4 py-3 rounded-top mb-4">
                    <h5 class="fw-bold mb-0" style=" color:#2e4db6;">🔗 Domain PageRank</h5>
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
        @endif

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
</div>

@endsection

<script>
// Votre script JavaScript reste inchangé...
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
        
        // Vérifier immédiatement
        checkPageSpeedStatus();
        
        // Puis toutes les 5 secondes
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
                console.log('📊 Statut:', data);
                
                const desktopReady = data.desktop_ready && data.desktop_score !== null;
                const mobileReady = data.mobile_ready && data.mobile_score !== null;
                
                if (desktopReady) {
                    console.log('✅ Données Desktop prêtes !');
                    stopWatching();
                    showNotification('✅ Données Desktop disponibles', 'success');
                    
                    // Recharger les données si on est sur desktop
                    if (currentStrategy === 'desktop') {
                        fetchPageSpeed('desktop', false, true);
                    }
                }
                
                if (mobileReady) {
                    console.log('✅ Données Mobile prêtes !');
                    // Ne pas stopWatching() ici pour laisser Desktop se déclencher si besoin
                    showNotification('✅ Données Mobile disponibles', 'success');
                }
            })
            .catch(error => {
                console.log('❌ Erreur surveillance:', error);
            });
    }

    function showNotification(message, type = 'info') {
        // Notification simple
        console.log(`📢 ${message}`);
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

        const sections = {
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
            informative: { 
                title: 'Informative Audits', 
                icon: '📘',
                color: 'secondary'
            }
        };

        let accordionId = 0;

        for (const [type, items] of Object.entries(audits)) {
            if (!items || items.length === 0) continue;

            accordionId++;
            const section = sections[type];
            const accordionSectionId = `accordion-${type}-${accordionId}`;
            const collapseId = `collapse-${type}-${accordionId}`;

            html += `
            <div class="accordion audit-accordion mb-3" id="${accordionSectionId}">
                <div class="accordion-item border-${section.color}">
                    <h2 class="accordion-header" id="heading-${type}">
                        <button class="accordion-button ${section.color} ${accordionId === 1 ? '' : 'collapsed'}" 
                                type="button" 
                                data-bs-toggle="collapse" 
                                data-bs-target="#${collapseId}" 
                                aria-expanded="${accordionId === 1 ? 'true' : 'false'}" 
                                aria-controls="${collapseId}">
                            <span class="d-flex align-items-center w-100">
                                <span class="accordion-icon me-2">${section.icon}</span>
                                <span class="accordion-title flex-grow-1">${section.title}</span>
                                <span class="badge bg-${section.color} ms-2">${items.length}</span>
                                <span class="accordion-arrow ms-2">▼</span>
                            </span>
                        </button>
                    </h2>
                    <div id="${collapseId}" 
                         class="accordion-collapse collapse ${accordionId === 1 ? 'show' : ''}" 
                         aria-labelledby="heading-${type}" 
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
                                            ${type === 'opportunities' && audit.estimatedSavingsMs ? 
                                              `<span class="badge bg-info mb-1">+${(audit.estimatedSavingsMs / 1000).toFixed(2)}s</span>` : ''}
                                            ${score !== null && type !== 'informative' ? 
                                              `<span class="badge bg-${badge}">${Math.round(score * 100)}%</span>` : ''}
                                        </div>
                                    </div>
                                    <div class="audit-body">
                                        <p class="audit-description">${audit.description || 'No description available'}</p>
                                        ${audit.displayValue ? `<p class="audit-value">${audit.displayValue}</p>` : ''}
                                        ${score !== null && type === 'opportunities' ? `
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

    // 🚀 DÉMARRAGE
    console.log('🚀 Chargement initial...');
    updateButtonStates('desktop');
    fetchPageSpeed('desktop');
    
    // Démarrer la surveillance après un délai
    setTimeout(() => {
        startWatching();
    }, 2000);
});
</script>