@extends('admin.admin_master')

@section('admin')
{{-- En tête du fichier --}}
@php
    $cacheVersion = '1.0.0'; // 🔥 Changez cette version quand vous modifiez le CSS/JS
@endphp
<div class="container">



<!-- Composant SEO Analysis -->
    <x-seo-analysis-card :project="$project" :analysis="$analysis" />

    {{-- Analyse du Contenu Principal --}}
    <x-main-content-analysis :analysis="$analysis" />


        <div class="glass-card mt-5 p-4 mb-4" style="background: #f7f6fc;  border-radius: 16px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2); border: 1px solid rgba(255, 255, 255, 0.3); color: #000; word-wrap: break-word; overflow-wrap: break-word;">
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

<!-- Section Keywords -->
@if(!empty($keywordsData))

    <x-keywords :keywords="$keywordsData" />

@else
<div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 mb-6">
    <div class="text-center">
        <p class="text-yellow-700">📊 Aucune donnée de mots-clés disponible pour cette analyse.</p>
        <p class="text-yellow-600 text-sm mt-2">Les mots-clés seront extraits lors de la prochaine analyse.</p>
    </div>
</div>
@endif


        {{-- PageRank Section --}}
        <x-page-rank :analysis="$analysis" />


        <x-whois-card :analysis="$analysis" />
        <x-analysis-summary :analysis="$analysis" />

        <div id="analysis-data" data-analysis-id="{{ $analysis->id }}"></div>

        <div class="btn-group mb-3" role="group">
    <button class="btn btn-strategy active" data-strategy="desktop">🖥️ Desktop</button>
    <button class="btn btn-strategy" data-strategy="mobile">📱 Mobile</button>
</div>

        <div id="pagespeed-metrics-wrapper"></div>
        <div id="audit-fragments-wrapper"></div>

        
        @include('user.projects.partials.ai-summary', ['ai' => $ai])

    

    {{-- ⬇️ LES DEUX @endif MANQUANTS AJOUTÉS --}}
    {{-- Back to Top Component --}}
    <x-back-to-top />

</div>
</div>

@endsection

