@extends('admin.admin_master')

@section('admin')
<style>
.pagerank-card {
    background-color: #ffffff;
    border-radius: 20px;
    box-shadow: 
        0 10px 40px rgba(0, 0, 0, 0.08),
        0 2px 10px rgba(0, 0, 0, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.6);
    overflow: hidden;
    /* SUPPRIMER la transition générale */
}

.pagerank-card:hover {
    transform: translateY(-2px);
    box-shadow: 
        0 20px 60px rgba(0, 0, 0, 0.12),
        0 4px 20px rgba(0, 0, 0, 0.06);
    transition: transform 0.2s ease, box-shadow 0.2s ease; /* Transition spécifique seulement au hover */
}

.pagerank-header {
    color: white;
    padding: 2rem;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    position: relative;
    overflow: hidden;
}

.pagerank-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 200px;
    height: 200px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
}

.header-content {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.header-icon {
    font-size: 2.5rem;
    opacity: 0.9;
}

.pagerank-title {
    font-weight: 700;
    font-size: 1.5rem;
    margin: 0;
}

.pagerank-subtitle {
    opacity: 0.9;
    font-size: 0.9rem;
    margin: 0.25rem 0 0 0;
}

.domain-score {
    padding: 0.75rem 1.5rem;
    border-radius: 25px;
    font-weight: 700;
    font-size: 1.1rem;
   
}

.pagerank-content {
    padding: 2rem;
}

.score-section {
    margin-bottom: 2rem;
}

.score-display {
    display: flex;
    align-items: center;
    gap: 2rem;
}

.score-circle {
    position: relative;
    width: 120px;
    height: 120px;
}

.circle-progress {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background: conic-gradient(
        var(--color) calc(var(--progress) * 1%),
        #f1f5f9 0%
    );
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    position: relative;
}

.circle-progress::before {
    content: '';
    position: absolute;
    width: 90px;
    height: 90px;
    background: white;
    border-radius: 50%;
}

.score-number {
    font-size: 2rem;
    font-weight: 800;
    color: #1f2937;
    position: relative;
    z-index: 1;
}

.score-label {
    font-size: 0.9rem;
    color: #6b7280;
    position: relative;
    z-index: 1;
}

.score-details {
    flex: 1;
}

.score-level {
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
}

.global-rank {
    background: #f8fafc;
    padding: 1rem;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
}

.rank-label {
    font-size: 0.9rem;
    color: #6b7280;
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.rank-value {
    font-size: 1.5rem;
    font-weight: 800;
    color: #1f2937;
}

.progress-section {
    margin-bottom: 2rem;
}

.progress-labels {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.5rem;
    font-size: 0.85rem;
    color: #6b7280;
    font-weight: 500;
}

.progress-bar-container {
    position: relative;
    height: 12px;
    margin-bottom: 1rem;
}

.progress-bar-bg {
    width: 100%;
    height: 100%;
    background: #f1f5f9;
    border-radius: 10px;
    overflow: hidden;
}

.progress-bar-fill {
    height: 100%;
    border-radius: 10px;
    /* SUPPRIMER la transition qui peut causer des saccades */
}

.progress-indicator {
    position: absolute;
    top: 50%;
    transform: translate(-50%, -50%);
    width: 24px;
    height: 24px;
    border-radius: 50%;
    border: 3px solid white;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.indicator-tooltip {
    position: absolute;
    top: -35px;
    left: 50%;
    transform: translateX(-50%);
    background: #1f2937;
    color: white;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
    white-space: nowrap;
}

.indicator-tooltip::after {
    content: '';
    position: absolute;
    top: 100%;
    left: 50%;
    transform: translateX(-50%);
    border: 4px solid transparent;
    border-top-color: #1f2937;
}

.progress-levels {
    display: flex;
    justify-content: space-between;
    margin-top: 1rem;
}

.level-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.85rem;
    color: #6b7280;
    font-weight: 500;
}

.level-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
}

.info-section {
    background: #f8fafc;
    border-radius: 16px;
    padding: 1.5rem;
    border: 1px solid #e2e8f0;
}

.section-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1rem;
}

.section-header i {
    font-size: 1.25rem;
    color: #667eea;
}

.section-header h4 {
    font-weight: 600;
    color: #2d3748;
    margin: 0;
    font-size: 1.1rem;
}


.section-content {
    /* ✅ Permet au contenu de s'adapter */
    width: auto;
    min-width: 0; /* Important pour flex/grid */
    word-wrap: break-word;
    overflow-wrap: break-word;
}


.info-text {
    color: #6b7280;
    line-height: 1.6;
    margin-bottom: 1rem;
}

.info-footer {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.85rem;
    color: #9ca3af;
}

.info-footer i {
    color: #667eea;
}

.pagerank-footer {
    background: #f8fafc;
    padding: 1rem 2rem;
    border-top: 1px solid #e2e8f0;
}

.last-updated {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #718096;
    font-size: 0.85rem;
    font-weight: 500;
}

.last-updated i {
    font-size: 0.8rem;
}

.loading-state {
    display: flex;
    align-items: center;
    gap: 2rem;
    padding: 2rem 0;
}

.loading-spinner {
    flex-shrink: 0;
}

.loading-content {
    flex: 1;
}

.loading-content h4 {
    color: #1f2937;
    margin-bottom: 0.5rem;
    font-weight: 600;
}

.loading-content p {
    color: #6b7280;
    margin: 0;
}

/* Responsive Design */
@media (max-width: 768px) {
    .pagerank-header {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
    
    .header-content {
        flex-direction: column;
        text-align: center;
    }
    
    .score-display {
        flex-direction: column;
        text-align: center;
        gap: 1.5rem;
    }
    
    .loading-state {
        flex-direction: column;
        text-align: center;
        gap: 1.5rem;
    }
    
    .pagerank-content {
        padding: 1.5rem;
    }
}

/* SUPPRIMER TOUTES LES ANIMATIONS LOURDES */
/* 
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.pagerank-card {
    animation: fadeIn 0.6s ease-out;
}

.score-section {
    animation: fadeIn 0.6s ease-out 0.2s both;
}

.progress-section {
    animation: fadeIn 0.6s ease-out 0.3s both;
}

.info-section {
    animation: fadeIn 0.6s ease-out 0.4s both;
}
*/





/* Toast Notifications - Modern Professional Design */
.custom-toast {
    background: #ffffff;
    border: none;
    border-radius: 12px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12), 
                0 2px 8px rgba(0, 0, 0, 0.08);
    margin-bottom: 12px;
    overflow: hidden;
    position: relative;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.toast-content {
    display: flex;
    align-items: flex-start;
    padding: 16px;
    gap: 12px;
}

.toast-icon {
    flex-shrink: 0;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    margin-top: 1px;
}

.toast-body {
    flex: 1;
    min-width: 0;
}

.toast-title {
    font-weight: 600;
    font-size: 14px;
    line-height: 1.3;
    color:rgb(255, 255, 255);
    margin-bottom: 4px;
    letter-spacing: -0.01em;
}

.toast-message {
    font-size: 13px;
    line-height: 1.4;
    color: #ffffff;
    word-wrap: break-word;
    font-weight: 400;
}

.toast-close {
    flex-shrink: 0;
    background: none;
    border: none;
    padding: 6px;
    color: #999;
    cursor: pointer;
    border-radius: 6px;
    transition: all 0.15s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0.7;
    color: #ffffff;
}

.toast-close:hover {
    background: rgba(0, 0, 0, 0.08);
    color: #666;
    opacity: 1;
}

/* Progress Bar Animation */
.toast-progress {
    position: absolute;
    bottom: 0;
    left: 0;
    height: 3px;
    width: 100%;
    transform-origin: left;
    animation: toastProgress 5s linear forwards;
}

.custom-toast.bg-success .toast-progress {
    background: linear-gradient(90deg, #10B981, #059669);
}

.custom-toast.bg-danger .toast-progress {
    background: linear-gradient(90deg, #EF4444, #DC2626);
}

.custom-toast.bg-warning .toast-progress {
    background: linear-gradient(90deg, #F59E0B, #D97706);
}

.custom-toast.bg-info .toast-progress {
    background: linear-gradient(90deg, #3B82F6, #2563EB);
}

@keyframes toastProgress {
    from {
        transform: scaleX(1);
    }
    to {
        transform: scaleX(0);
    }
}

/* Color Variants with Subtle Backgrounds */
.custom-toast.bg-success {
    background: linear-gradient(135deg, #F0FDF4 0%, #FFFFFF 30%);
    border-left: 4px solid #10B981;
}

.custom-toast.bg-success .toast-icon {
    color: #10B981;
}

.custom-toast.bg-danger {
    background: linear-gradient(135deg, #FEF2F2 0%, #FFFFFF 30%);
    border-left: 4px solid #EF4444;
}

.custom-toast.bg-danger .toast-icon {
    color: #EF4444;
}

.custom-toast.bg-warning {
    background: linear-gradient(135deg, #FFFBEB 0%, #FFFFFF 30%);
    border-left: 4px solid #F59E0B;
}

.custom-toast.bg-warning .toast-icon {
    color: #F59E0B;
}

.custom-toast.bg-info {
    background: linear-gradient(135deg, #EFF6FF 0%, #FFFFFF 30%);
    border-left: 4px solid #3B82F6;
}

.custom-toast.bg-info .toast-icon {
    color: #3B82F6;
}

/* Smooth Entrance Animations */
.custom-toast.show {
    animation: toastSlideIn 0.4s cubic-bezier(0.21, 1.02, 0.73, 1);
}

.custom-toast.hiding {
    animation: toastSlideOut 0.3s ease-in forwards;
}

@keyframes toastSlideIn {
    0% {
        opacity: 0;
        transform: translateX(100%) scale(0.9);
    }
    60% {
        opacity: 1;
        transform: translateX(-8px) scale(1);
    }
    100% {
        opacity: 1;
        transform: translateX(0) scale(1);
    }
}

@keyframes toastSlideOut {
    0% {
        opacity: 1;
        transform: translateX(0) scale(1);
    }
    100% {
        opacity: 0;
        transform: translateX(100%) scale(0.9);
    }
}

/* Hover Effects */
.custom-toast:hover {
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15), 
                0 4px 12px rgba(0, 0, 0, 0.1);
    transform: translateY(-1px);
    transition: all 0.2s ease;
}

/* Responsive Design */
@media (max-width: 768px) {
    .toast-content {
        padding: 14px 16px;
        gap: 10px;
    }
    
    .toast-title {
        font-size: 15px;
    }
    
    .toast-message {
        font-size: 14px;
    }
    
    .custom-toast {
        margin: 0 12px 12px 12px;
        border-radius: 10px;
        color: #ffffff;
    }
}

/* Toast Container Positioning */
.toast-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1060;
    min-width: 300px;
    max-width: 400px;
}

@media (max-width: 768px) {
    .toast-container {
        top: 16px;
        right: 16px;
        left: 16px;
        min-width: auto;
        max-width: none;
    }
}

.custom-toast.bg-success {
    background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
    border-left: 4px solid #10B981;
}

.custom-toast.bg-danger {
    background: linear-gradient(135deg, #DC2626 0%, #B91C1C 100%) !important;
    border-left: 4px solid #EF4444;
}

.custom-toast.bg-warning {
    background: linear-gradient(135deg, #D97706 0%, #B45309 100%) !important;
    border-left: 4px solid #F59E0B;
}

.custom-toast.bg-info {
    background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%) !important;
    border-left: 4px solid #3B82F6;
}
</style>
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




        {{-- PageRank Section --}}
        <x-page-rank :analysis="$analysis" />


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
        @include('user.projects.partials.ai-summary', ['ai' => $ai])

    

    {{-- ⬇️ LES DEUX @endif MANQUANTS AJOUTÉS --}}
    {{-- Back to Top Component --}}
    <x-back-to-top />

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
    console.log('✅ Desktop data ready!');
    window.desktopDisplayed = true;
    showNotification('✓ Desktop data available', 'success');
    if (currentStrategy === 'desktop') {
        fetchPageSpeed('desktop', false, true);
    }
}
                    
if (mobileReady && !window.mobileDisplayed) {
    console.log('✅ Mobile analysis completed');
    window.mobileDisplayed = true;
    showNotification('Mobile performance data loaded', 'success');
}

// 🆕 Check PageRank - FIXED
if (data.page_rank !== null && data.page_rank !== undefined && !window.pageRankDisplayed) {
    console.log('✅ PageRank analysis complete', data.page_rank);
    window.pageRankDisplayed = true;
    showNotification('PageRank data available', 'success');
    updatePageRankSection(data.page_rank, data.page_rank_global);
} else if (data.page_rank === null || data.page_rank === undefined) {
    everythingReady = false;
    console.log('⏳ PageRank analysis in progress...', data.page_rank);
}
                    
                    if (!desktopReady || !mobileReady) {
                        everythingReady = false;
                    }
                    
                    if (everythingReady) {
    console.log('✅ All analysis data has been processed');
    stopMonitoring();
    showNotification('SEO analysis completed successfully', 'success');
}
                })
                .catch(error => {
                    console.log('❌ Erreur surveillance:', error);
                });
        }

        function stopWatching() {
            isWatching = false;
            if (watchInterval) {
                clearInterval(watchInterval);
                watchInterval = null;
                console.log('⏹️ Surveillance arrêtée');
            }
        }

        function showNotification(message, type = 'info') {
            // Couleurs et icônes selon le type
            const config = {
    success: { 
        bg: 'bg-success', 
        icon: `
            <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10 2C5.58172 2 2 5.58172 2 10C2 14.4183 5.58172 18 10 18C14.4183 18 18 14.4183 18 10C18 5.58172 14.4183 2 10 2ZM14.0303 8.03033L9.03033 13.0303C8.73744 13.3232 8.26256 13.3232 7.96967 13.0303L5.96967 11.0303C5.67678 10.7374 5.67678 10.2626 5.96967 9.96967C6.26256 9.67678 6.73744 9.67678 7.03033 9.96967L8.5 11.4393L12.9697 6.96967C13.2626 6.67678 13.7374 6.67678 14.0303 6.96967C14.3232 7.26256 14.3232 7.73744 14.0303 8.03033Z"/>
            </svg>
        `,
        title: 'Success'
    },
    warning: { 
        bg: 'bg-warning text-dark', 
        icon: `
            <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10 2C5.58172 2 2 5.58172 2 10C2 14.4183 5.58172 18 10 18C14.4183 18 18 14.4183 18 10C18 5.58172 14.4183 2 10 2ZM10 6C10.5523 6 11 6.44772 11 7V11C11 11.5523 10.5523 12 10 12C9.44772 12 9 11.5523 9 11V7C9 6.44772 9.44772 6 10 6ZM10 16C9.44772 16 9 15.5523 9 15C9 14.4477 9.44772 14 10 14C10.5523 14 11 14.4477 11 15C11 15.5523 10.5523 16 10 16Z"/>
            </svg>
        `,
        title: 'Warning'
    },
    error: { 
        bg: 'bg-danger', 
        icon: `
            <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10 2C5.58172 2 2 5.58172 2 10C2 14.4183 5.58172 18 10 18C14.4183 18 18 14.4183 18 10C18 5.58172 14.4183 2 10 2ZM13.5303 6.46967C13.8232 6.76256 13.8232 7.23744 13.5303 7.53033L11.0607 10L13.5303 12.4697C13.8232 12.7626 13.8232 13.2374 13.5303 13.5303C13.2374 13.8232 12.7626 13.8232 12.4697 13.5303L10 11.0607L7.53033 13.5303C7.23744 13.8232 6.76256 13.8232 6.46967 13.5303C6.17678 13.2374 6.17678 12.7626 6.46967 12.4697L8.93934 10L6.46967 7.53033C6.17678 7.23744 6.17678 6.76256 6.46967 6.46967C6.76256 6.17678 7.23744 6.17678 7.53033 6.46967L10 8.93934L12.4697 6.46967C12.7626 6.17678 13.2374 6.17678 13.5303 6.46967Z"/>
            </svg>
        `,
        title: 'Error'
    },
    info: { 
        bg: 'bg-info', 
        icon: `
            <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10 2C5.58172 2 2 5.58172 2 10C2 14.4183 5.58172 18 10 18C14.4183 18 18 14.4183 18 10C18 5.58172 14.4183 2 10 2ZM10 6C10.5523 6 11 6.44772 11 7C11 7.55228 10.5523 8 10 8C9.44772 8 9 7.55228 9 7C9 6.44772 9.44772 6 10 6ZM10 16C9.44772 16 9 15.5523 9 15V11C9 10.4477 9.44772 10 10 10C10.5523 10 11 10.4477 11 11V15C11 15.5523 10.5523 16 10 16Z"/>
            </svg>
        `,
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
    <div id="${toastId}" class="custom-toast ${bg}" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-content">
            <div class="toast-icon">${icon}</div>
            <div class="toast-body">
                <div class="toast-title">${title}</div>
                <div class="toast-message">${message}</div>
            </div>
            <button type="button" class="toast-close" data-bs-dismiss="toast" aria-label="Close">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                    <path d="M13 1L1 13M1 1L13 13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>
        </div>
        <div class="toast-progress"></div>
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

        // NOUVELLE FONCTION POUR METTRE À JOUR LA SECTION PAGERANK AVEC LE NOUVEAU DESIGN
        function updatePageRankSection(rank, globalRank) {
            console.log('🎯 Mise à jour PageRank section avec nouveau design:', { rank, globalRank });
            
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
            
            // Déterminer le niveau et les couleurs selon le nouveau design
            let color, gradient, icon, level, bgColor;
            
            if (safeRank >= 7) {
                color = '#10b981';
                gradient = 'linear-gradient(135deg, #10b981 0%, #059669 100%)';
                icon = 'bi-trophy-fill';
                level = 'Excellent';
                bgColor = 'rgba(16, 185, 129, 0.1)';
            } else if (safeRank >= 4) {
                color = '#f59e0b';
                gradient = 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)';
                icon = 'bi-graph-up-arrow';
                level = 'Medium';
                bgColor = 'rgba(245, 158, 11, 0.1)';
            } else {
                color = '#ef4444';
                gradient = 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)';
                icon = 'bi-exclamation-triangle-fill';
                level = 'Low';
                bgColor = 'rgba(239, 68, 68, 0.1)';
            }
            
            // Générer le HTML avec le nouveau design professionnel
            pageRankSection.innerHTML = `
                <div class="pagerank-card mb-4">
                    <!-- Header avec icône et titre -->
                    <div class="pagerank-header" style="background: ${gradient};">
                        <div class="header-content">
                            <div class="header-icon">
                                <i class="bi bi-bar-chart-fill"></i>
                            </div>
                            <div>
                                <h3 class="pagerank-title">Domain PageRank</h3>
                                <p class="pagerank-subtitle">Authority score based on OpenPageRank</p>
                            </div>
                        </div>
                        <div class="domain-score" style="background: rgba(255, 255, 255, 0.2); color: white;">
                            ${safeRank}/10
                        </div>
                    </div>

                    <!-- Contenu principal -->
                    <div class="pagerank-content">
                        <!-- Score et indicateur visuel -->
                        <div class="score-section">
                            <div class="score-display">
                                <div class="score-circle">
                                    <div class="circle-progress" style="--progress: ${safeRank * 10}%; --color: ${color};">
                                        <div class="score-number">${safeRank}</div>
                                        <div class="score-label">/10</div>
                                    </div>
                                </div>
                                <div class="score-details">
                                    <div class="score-level" style="color: ${color};">
                                        <i class="bi ${icon} me-2"></i>
                                        ${level}
                                    </div>
                                    ${safeGlobalRank ? `
                                    <div class="global-rank">
                                        <div class="rank-label">Global Ranking</div>
                                        <div class="rank-value">#${new Intl.NumberFormat().format(safeGlobalRank)}</div>
                                    </div>
                                    ` : ''}
                                </div>
                            </div>
                        </div>

                        <!-- Barre de progression détaillée -->
                        <div class="progress-section">
                            <div class="progress-labels">
                                <span>0</span>
                                <span>5</span>
                                <span>10</span>
                            </div>
                            <div class="progress-bar-container">
                                <div class="progress-bar-bg">
                                    <div class="progress-bar-fill" style="width: ${safeRank * 10}%; background: ${gradient};"></div>
                                </div>
                                <div class="progress-indicator" style="left: ${safeRank * 10}%; background: ${color};">
                                    <div class="indicator-tooltip">${safeRank}</div>
                                </div>
                            </div>
                            <div class="progress-levels">
                                <div class="level-item">
                                    <div class="level-dot" style="background: #ef4444;"></div>
                                    <span>Faible</span>
                                </div>
                                <div class="level-item">
                                    <div class="level-dot" style="background: #f59e0b;"></div>
                                    <span>Moyen</span>
                                </div>
                                <div class="level-item">
                                    <div class="level-dot" style="background: #10b981;"></div>
                                    <span>Excellent</span>
                                </div>
                            </div>
                        </div>

                        <!-- Informations contextuelles -->
                        <div class="info-section">
                            <div class="section-header">
                                <i class="bi bi-info-circle"></i>
                                <h4>About PageRank</h4>
                            </div>
                            <p class="info-text">
                                This score reflects the domain's public reputation on the global web, calculated from open source data. 
                                Higher scores indicate greater authority and trustworthiness.
                            </p>
                            <div class="info-footer">
                                <i class="bi bi-shield-check"></i>
                                <span>Data provided by OpenPageRank Initiative</span>
                            </div>
                        </div>
                    </div>

                    <!-- Footer avec timestamp -->
                    <div class="pagerank-footer">
                        <div class="last-updated">
                            <i class="bi bi-clock-history"></i>
                            Last updated ${new Date().toLocaleString('en-US', { 
                                month: 'short', 
                                day: 'numeric', 
                                year: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            })}
                        </div>
                    </div>
                </div>
            `;
            
            console.log('✅ Section PageRank mise à jour avec le nouveau design');
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




    // ... Votre code JavaScript existant ...

// ✅ OPTIMISATION SCROLL - À METTRE À LA FIN
document.addEventListener('DOMContentLoaded', function() {
    let isScrolling;
    
    window.addEventListener('scroll', function() {
        // Désactiver les animations pendant le scroll
        document.documentElement.classList.add('no-scroll-animations');
        
        // Clear le timeout existant
        clearTimeout(isScrolling);
        
        // Réactiver les animations après l'arrêt du scroll
        isScrolling = setTimeout(function() {
            document.documentElement.classList.remove('no-scroll-animations');
        }, 66); // ~16ms * 4 = 66ms pour meilleure performance
    }, { passive: true }); // ✅ Améliore les performances
});

console.log('✅ Scroll optimization loaded');
</script>