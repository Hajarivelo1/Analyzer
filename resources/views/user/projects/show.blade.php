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
                        <i class="bi bi-search text-primary me-1"></i>Mots-clés
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="audit-tab" data-bs-toggle="tab" data-bs-target="#audit" type="button" role="tab">
                        <i class="bi bi-tools text-danger me-1"></i>Audit Technique
                    </button>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="audit-structure-tab" data-bs-toggle="tab" href="#audit-structure" role="tab" aria-controls="audit-structure" aria-selected="false">
                        🗂️ Audit Structurel
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

                {{-- Headings --}}
                <div class="tab-pane fade" id="headings" role="tabpanel">
                    <ul class="list-group list-group-flush mb-4">
                        @foreach(json_decode($analysis->headings, true) as $heading)
                            <li class="list-group-item bg-transparent text-dark">{{ $heading }}</li>
                        @endforeach
                    </ul>
                </div>

                {{-- Images --}}
                <div class="tab-pane fade" id="images" role="tabpanel">
                    <ul class="list-group list-group-flush mb-4">
                        @foreach(json_decode($analysis->images_data, true) as $img)
                            <li class="list-group-item bg-transparent text-dark truncate-filename">{{ $img }}</li>
                        @endforeach
                    </ul>
                </div>

                {{-- Mots-clés --}}
                <div class="tab-pane fade" id="keywords" role="tabpanel">
                    @if($analysis->keywords && count((array) $analysis->keywords))
                        <ul class="list-group list-group-flush mb-4">
                            @foreach((array) $analysis->keywords as $word => $count)
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

                {{-- Audit Structure --}}
                <div class="tab-pane fade" id="audit-structure" role="tabpanel" aria-labelledby="audit-structure-tab">
                    <h5 class="mt-3">Structure des balises Hn</h5>
                    @php
                        try {
                            $headings = json_decode($analysis->headings_structure ?? '[]', true, 512, JSON_THROW_ON_ERROR);
                        } catch (\JsonException $e) {
                            $headings = [];
                        }
                        $hasH1 = false;
                        $total = count($headings);
                        $maxDepth = 0;
                        $sumDepth = 0;
                    @endphp
                    @if(is_array($headings) && $total)
                        @foreach($headings as $h)
                            @php
                                $hasH1 = $hasH1 || strtolower($h['tag']) === 'h1';
                                $maxDepth = max($maxDepth, $h['depth']);
                                $sumDepth += $h['depth'];
                            @endphp
                        @endforeach
                        @if(!$hasH1)
                            <div class="alert alert-danger">
                                ⚠️ Aucun &lt;h1&gt; détecté — la structure sémantique est incomplète.
                            </div>
                        @endif
                        <div class="alert alert-secondary">
                            <strong>Total :</strong> {{ $total }} balises Hn |
                            <strong>Profondeur moyenne :</strong> {{ round($sumDepth / $total, 1) }} |
                            <strong>Profondeur max :</strong> {{ $maxDepth }}
                        </div>
                        <table class="table table-bordered table-sm mt-2">
                            <thead class="table-dark">
                                <tr>
                                    <th>Tag</th>
                                    <th>Texte</th>
                                    <th>Profondeur DOM</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($headings as $heading)
                                    @php
                                        $depth = $heading['depth'];
                                        $tag = strtolower($heading['tag']);
                                        $rowClass = match(true) {
                                            $depth <= 5 => 'table-success',
                                            $depth <= 10 => 'table-info',
                                            default => 'table-warning'
                                        };
                                    @endphp
                                    <tr class="{{ $rowClass }}">
                                        <td><span class="badge bg-primary">{{ strtoupper($tag) }}</span></td>
                                        <td>{{ $heading['text'] }}</td>
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
            ⚠️ Le contenu principal n’a pas pu être extrait.
            @if($analysis->cloudflare_blocked)
                Il semble que le site soit protégé par <strong>Cloudflare</strong>.
            @else
                Aucun contenu exploitable n’a été trouvé sur la page.
            @endif
        </div>
    @endif

    @if($analysis->main_content)
        <div class="glass-card mt-5 pt-4" style="backdrop-filter: blur(12px); border-radius: 16px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2); padding: 2rem; border: 1px solid rgba(255, 255, 255, 0.3);">
           
            <div style="background-color: #dbe1f7;" class="px-4 py-3 rounded-top mb-4">
        <h4 class="fw-bold mb-0" style=" color:#2e4db6;">🧠 Analyse du contenu principal</h4>
    </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="p-3 rounded" style="background:#222d40; color: #fff;">
                        @if($analysis->readability_score)
                            <div class="badge bg-info">Lisibilité : {{ $analysis->readability_score }}%</div>
                        @endif
                        @php
                            $analysisData = json_decode($analysis->content_analysis, true);
                        @endphp
                        @if(!empty($analysisData['paragraph_count']))
                            <div class="mt-3">
                                <strong>🧾 Paragraphes extraits :</strong> {{ $analysisData['paragraph_count'] }}<br>
                                <strong>📌 Paragraphes courts :</strong> {{ $analysisData['short_paragraphs'] }}<br>
                                <strong>🔁 Duplications :</strong> {{ count($analysisData['duplicate_paragraphs'] ?? []) }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row pt-4">
                @if(!empty($analysis->main_content))
                    <div class="col-md-6">
                        <div class="glass-card mb-4" style="backdrop-filter: blur(12px); background: rgba(255, 255, 255, 0.15); border-radius: 16px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2); border: 1px solid rgba(255, 255, 255, 0.3); color: #000; overflow: hidden;">
                            <button class="btn w-100 text-start d-flex justify-content-between align-items-center p-3" type="button" data-bs-toggle="collapse" data-bs-target="#mainContentCollapse" aria-expanded="false" aria-controls="mainContentCollapse" style="background: transparent; border: none; color: #000;">
                                <span>📄 Contenu extrait</span>
                                <span class="badge bg-light text-dark">Afficher</span>
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
                @endif

                @php
                    $content = json_decode($analysis->content_analysis ?? '{}', true);
                    $paragraphs = $content['paragraphs'] ?? [];
                @endphp
                @if(is_array($paragraphs) && count($paragraphs) > 0)
                    <div class="col-md-6">
                        <div class="glass-card mb-4" style="backdrop-filter: blur(12px); background: rgba(255, 255, 255, 0.15); border-radius: 16px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2); border: 1px solid rgba(255, 255, 255, 0.3); color: #000; overflow: hidden;">
                            <button class="btn w-100 text-start d-flex justify-content-between align-items-center p-3" type="button" data-bs-toggle="collapse" data-bs-target="#paragraphCollapse" aria-expanded="false" aria-controls="paragraphCollapse" style="background: transparent; border: none; color: #000;">
                                <span>🧾 Paragraphes extraits</span>
                                <span class="badge bg-light text-dark">{{ count($paragraphs) }} affichés</span>
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
                                                <small class="text-muted">{{ $length }} mots</small>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @php
                    $shorts = array_filter($content['paragraphs'] ?? [], fn($p) => strlen($p) < 100);
                @endphp
                @if(is_array($shorts) && count($shorts) > 0)
                    <div class="col-md-6">
                        <div class="glass-card mb-4" style="backdrop-filter: blur(12px); background: rgba(255, 255, 255, 0.15); border-radius: 16px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2); border: 1px solid rgba(255, 255, 255, 0.3); color: #000; overflow: hidden;">
                            <button class="btn w-100 text-start d-flex justify-content-between align-items-center p-3" type="button" data-bs-toggle="collapse" data-bs-target="#shortParagraphCollapse" aria-expanded="false" aria-controls="shortParagraphCollapse" style="background: transparent; border: none; color: #000;">
                                <span>📌 Paragraphes courts</span>
                                <span class="badge bg-warning text-dark">{{ count($shorts) }}</span>
                            </button>
                            <div class="collapse" id="shortParagraphCollapse">
                                <div class="p-3" style="max-height: 300px; overflow-y: auto;">
                                    <ul class="list-group list-group-flush">
                                        @foreach($shorts as $p)
                                            <li class="list-group-item bg-transparent text-danger" style="word-break: break-word;">
                                                {{ $p }}<br>
                                                <small class="text-muted">{{ str_word_count($p) }} mots</small>
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
                @if(is_array($duplicates) && count($duplicates) > 0)
                    <div class="col-md-6">
                        <div class="glass-card mb-4" style="backdrop-filter: blur(12px); background: rgba(255, 255, 255, 0.15); border-radius: 16px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2); border: 1px solid rgba(255, 255, 255, 0.3); color: #000; overflow: hidden;">
                            <button class="btn w-100 text-start d-flex justify-content-between align-items-center p-3" type="button" data-bs-toggle="collapse" data-bs-target="#duplicateParagraphCollapse" aria-expanded="false" aria-controls="duplicateParagraphCollapse" style="background: transparent; border: none; color: #000;">
                                <span>🔁 Paragraphes dupliqués</span>
                                <span class="badge bg-danger">{{ count($duplicates) }}</span>
                            </button>
                            <div class="collapse" id="duplicateParagraphCollapse">
                                <div class="p-3" style="max-height: 300px; overflow-y: auto;">
                                    <ul class="list-group list-group-flush">
                                        @foreach($duplicates as $p)
                                            <li class="list-group-item bg-transparent text-danger" style="word-break: break-word;">
                                                {{ $p }}<br>
                                                <small class="text-muted">{{ str_word_count($p) }} mots</small>
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
    @endif

       
    <div class="glass-cad mt-5 p-4" style="backdrop-filter: blur(12px); background: #f7f6fc;  border-radius: 16px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2); border: 1px solid rgba(255, 255, 255, 0.3); color: #000;">
        <div class="d-flex align-items-center mb-3">
            <img src="https://www.google.com/s2/favicons?domain={{ parse_url($analysis->page_url, PHP_URL_HOST) }}"
                 alt="favicon"
                 style="width: 20px; height: 20px; margin-right: 8px;">
            <span class="text-dark">{{ parse_url($analysis->page_url, PHP_URL_HOST) }}</span>
        </div>
        <h3 style="color:rgb(27, 76, 90);">{{ $analysis->page_title }}</h3>
        <p style="color:rgb(29, 26, 26);">{{ $analysis->meta_description }}</p>
        @if($analysis->readability_score)
            @php
                $score = $analysis->readability_score;
                $color = $score >= 60 ? '#00ff99' : ($score >= 40 ? '#ffcc00' : '#ff4d4d');
            @endphp
            <p class="mt-3">
                <strong style="color: #000;">📊 Lisibilité :</strong>
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

    <div class="glass-car mt-4 p-4 " style="
        backdrop-filter: blur(12px);
        background: #f7f6fc;
        border-radius: 16px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.2);
        border: 1px solid rgba(255,255,255,0.3);
        color: #000;
        margin-bottom: 20px;
    ">

<div style="background-color: #dbe1f7;" class="px-4 py-3 rounded-top mb-4">
        <h4 class="fw-bold mb-0" style=" color:#2e4db6;">🔗 PageRank du domaine</h4>
    </div>
       

        <div class="flex items-center space-x-3 text-xl font-bold">
            <span style="color: {{ $color }};">{{ $emoji }} {{ $rank }} / 10</span>
            <span class="text-sm text-muted" style="font-size: 0.85rem;">
                (selon OpenPageRank)
            </span>
        </div>

        @if(!is_null($analysis->page_rank_global))
            <p class="mt-2 text-muted" style="font-size: 0.9rem; font-weight: 600; color:#2454b9 !important;">
                Classement global : <strong>#{{ number_format($analysis->page_rank_global) }}</strong>
            </p>
        @endif

        <p class="mt-2 text-muted" style="font-size: 0.85rem;">
            Ce score reflète la réputation publique du domaine sur le web mondial, calculé à partir de données open source.
        </p>
    </div>
@endif



<x-whois-card :analysis="$analysis" />
<x-analysis-summary :analysis="$analysis" />

<x-page-speed-metrics 
    :metrics="$analysis->pagespeed_metrics ?? []" 
    :performanceScore="$analysis->pagespeed_score ?? null"
    :allScores="$analysis->pagespeed_scores ?? []"
/>





















</div>
@endsection
