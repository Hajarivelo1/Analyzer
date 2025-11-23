@props([
    'analysis' => null
])

@php
    $hasMainContent = !empty($analysis->main_content);
    $content = is_array($analysis->content_analysis) ? $analysis->content_analysis : [];
    
    // Variables pour les paragraphes
    $allParagraphs = $content['paragraphs'] ?? [];
    $paragraphCount = $content['paragraph_count'] ?? 0;
    $hasParagraphs = $paragraphCount > 0;
    
    // Limiter l'affichage à 30 paragraphes maximum
    $displayedParagraphs = array_slice($allParagraphs, 0, 30);
    $displayedCount = count($displayedParagraphs);
    $hasDisplayedParagraphs = $displayedCount > 0;

    // Données pour les analyses
    $shorts = array_filter($allParagraphs, fn($p) => str_word_count($p) < 40);
    $displayedShorts = array_slice($shorts, 0, 20);
    $duplicates = $content['duplicate_paragraphs'] ?? [];
    $displayedDuplicates = array_slice($duplicates, 0, 20);

    // Calcul des statistiques
    $totalIssues = count($shorts) + count($duplicates);
    $contentStatus = $hasMainContent ? 'success' : ($analysis->cloudflare_blocked ? 'warning' : 'danger');
@endphp

<div class="main-content-analysis">
    <!-- Main Card -->
    <div class="card border-0 shadow-lg mb-4" style="border-radius: 16px;">
        <!-- Header with Gradient -->
        <div class="card-header bg-gradient-primary text-white py-3" style="border-radius: 16px 16px 0 0;">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <div class="header-icon me-3">
                        <i class="bi bi-file-earmark-text-fill"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold">Main Content Analysis</h5>
                        <small class="opacity-75">Semantic extraction and in-depth analysis</small>
                    </div>
                </div>
                @if($analysis->readability_score)
                <div class="readability-score">
                    <div class="score-badge">
                        <span class="score-value">{{ $analysis->readability_score }}%</span>
                        <span class="score-label">Readability</span>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Card Body -->
        <div class="card-body p-4">
            <!-- Statistics Cards -->
            <div class="stats-grid mb-5">
                <div class="row g-3">
                    <!-- Content Status -->
                    <div class="col-xl-4 col-md-4">
                        <div class="stat-card {{ $contentStatus }}">
                            <div class="stat-icon">
                                <i class="bi bi-{{ $hasMainContent ? 'check-circle-fill' : ($analysis->cloudflare_blocked ? 'shield-exclamation' : 'x-circle-fill') }}"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-value">{{ $hasMainContent ? 'Available' : 'Unavailable' }}</div>
                                <div class="stat-label">Main Content</div>
                                @if($hasMainContent)
                                <div class="stat-detail">{{ number_format(strlen($analysis->main_content)) }} characters</div>
                                @elseif($analysis->cloudflare_blocked)
                                <div class="stat-detail">Cloudflare Protected</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Extracted Paragraphs -->
                    <div class="col-xl-4 col-md-4">
                        <div class="stat-card {{ $hasParagraphs ? 'info' : 'secondary' }}">
                            <div class="stat-icon">
                                <i class="bi bi-text-paragraph"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-value">{{ $paragraphCount }}</div>
                                <div class="stat-label">Paragraphs Extracted</div>
                                @if($hasParagraphs && $displayedCount < $paragraphCount)
                                <div class="stat-detail">{{ $displayedCount }} displayed</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Issues Detected -->
                    <div class="col-xl-4 col-md-4">
                        <div class="stat-card {{ $totalIssues > 0 ? 'warning' : 'success' }}">
                            <div class="stat-icon">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-value">{{ $totalIssues }}</div>
                                <div class="stat-label">Issues Detected</div>
                                <div class="stat-detail">
                                    {{ count($shorts) }} short • {{ count($duplicates) }} duplicates
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Analysis Sections -->
            <div class="analysis-sections">
                <div class="row g-4">
                    <!-- Extracted Content -->
                    @if($hasMainContent)
                    <div class="col-lg-6">
                        <div class="analysis-section">
                            <div class="section-header" data-bs-toggle="collapse" data-bs-target="#contentCollapse">
                                <div class="section-title">
                                    <div class="title-icon">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-semibold mb-1">Extracted Content</h6>
                                        <p class="text-muted mb-0">Identified main content</p>
                                    </div>
                                </div>
                                <div class="section-actions">
                                    <span class="badge bg-primary">Preview</span>
                                    <i class="bi bi-chevron-down transition-icon"></i>
                                </div>
                            </div>
                            <div class="collapse" id="contentCollapse">
                                <div class="section-body">
                                    <div class="d-flex gap-2 mb-3">
                                        <button class="btn btn-sm btn-outline-primary" onclick="copyContentToClipboard()">
                                            <i class="bi bi-clipboard me-1"></i>Copy
                                        </button>
                                        <button class="btn btn-sm btn-outline-secondary" onclick="toggleContentPreview()">
                                            <i class="bi bi-arrows-expand me-1"></i>Expand
                                        </button>
                                    </div>
                                    <div class="content-preview" id="contentPreview">
                                        <div class="content-text">
                                            {{ Str::limit($analysis->main_content, 400) }}
                                            @if(strlen($analysis->main_content) > 400)
                                            <span class="text-muted">... (content truncated)</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="content-meta mt-3">
                                        <small class="text-muted">
                                            <i class="bi bi-hash me-1"></i>
                                            {{ number_format(strlen($analysis->main_content)) }} characters •
                                            {{ str_word_count($analysis->main_content) }} words
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="col-lg-6">
                        <div class="analysis-section">
                            <div class="section-header">
                                <div class="section-title">
                                    <div class="title-icon text-warning">
                                        <i class="bi bi-exclamation-triangle"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-semibold mb-1">Content Unavailable</h6>
                                        <p class="text-muted mb-0">Extraction failed</p>
                                    </div>
                                </div>
                            </div>
                            <div class="section-body">
                                <div class="alert alert-warning mb-0">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-{{ $analysis->cloudflare_blocked ? 'shield-exclamation' : 'file-x' }} me-2"></i>
                                        <div>
                                            <strong>Content not accessible</strong>
                                            <p class="mb-0 small">
                                                @if($analysis->cloudflare_blocked)
                                                Website protected by Cloudflare anti-bot.
                                                @else
                                                Extraction issue due to JavaScript rendering or structure.
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Analyzed Paragraphs -->
                    @if($hasDisplayedParagraphs)
                    <div class="col-lg-6">
                        <div class="analysis-section">
                            <div class="section-header" data-bs-toggle="collapse" data-bs-target="#paragraphsCollapse">
                                <div class="section-title">
                                    <div class="title-icon">
                                        <i class="bi bi-text-left"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-semibold mb-1">Analyzed Paragraphs</h6>
                                        <p class="text-muted mb-0">Structure and length analysis</p>
                                    </div>
                                </div>
                                <div class="section-actions">
                                    <span class="badge bg-info">{{ $displayedCount }}</span>
                                    <i class="bi bi-chevron-down transition-icon"></i>
                                </div>
                            </div>
                            <div class="collapse" id="paragraphsCollapse">
                                <div class="section-body">
                                    @if($displayedCount < $paragraphCount)
                                    <div class="alert alert-info mb-3">
                                        <i class="bi bi-info-circle me-2"></i>
                                        Showing {{ $displayedCount }} of {{ $paragraphCount }} paragraphs
                                    </div>
                                    @endif
                                    <div class="paragraphs-grid">
                                        @foreach($displayedParagraphs as $index => $paragraph)
                                        @php
                                            $wordCount = str_word_count($paragraph);
                                            $quality = $wordCount < 40 ? 'poor' : ($wordCount < 80 ? 'medium' : 'good');
                                        @endphp
                                        <div class="paragraph-card {{ $quality }}">
                                            <div class="paragraph-header">
                                                <span class="paragraph-index">#{{ $index + 1 }}</span>
                                                <span class="paragraph-stats">
                                                    <i class="bi bi-fonts me-1"></i>{{ $wordCount }} words
                                                </span>
                                            </div>
                                            <div class="paragraph-content">
                                                {{ $paragraph }}
                                            </div>
                                            <div class="paragraph-indicator">
                                                <i class="bi bi-{{ $quality === 'good' ? 'check-circle' : ($quality === 'medium' ? 'exclamation-circle' : 'x-circle') }}"></i>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Short Paragraphs -->
                    @if(count($shorts) > 0)
                    <div class="col-lg-6">
                        <div class="analysis-section">
                            <div class="section-header" data-bs-toggle="collapse" data-bs-target="#shortsCollapse">
                                <div class="section-title">
                                    <div class="title-icon text-warning">
                                        <i class="bi bi-type"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-semibold mb-1">Short Paragraphs</h6>
                                        <p class="text-muted mb-0">Less than 40 words</p>
                                    </div>
                                </div>
                                <div class="section-actions">
                                    <span class="badge bg-warning text-dark">{{ count($shorts) }}</span>
                                    <i class="bi bi-chevron-down transition-icon"></i>
                                </div>
                            </div>
                            <div class="collapse" id="shortsCollapse">
                                <div class="section-body">
                                    <div class="paragraphs-grid">
                                        @foreach($displayedShorts as $index => $paragraph)
                                        <div class="paragraph-card poor">
                                            <div class="paragraph-header">
                                                <span class="paragraph-index">#{{ $index + 1 }}</span>
                                                <span class="paragraph-stats text-danger">
                                                    <i class="bi bi-fonts me-1"></i>{{ str_word_count($paragraph) }} words
                                                </span>
                                            </div>
                                            <div class="paragraph-content">
                                                {{ $paragraph }}
                                            </div>
                                            <div class="paragraph-indicator">
                                                <i class="bi bi-x-circle text-danger"></i>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Duplications -->
                    @if(count($duplicates) > 0)
                    <div class="col-lg-6">
                        <div class="analysis-section">
                            <div class="section-header" data-bs-toggle="collapse" data-bs-target="#duplicatesCollapse">
                                <div class="section-title">
                                    <div class="title-icon text-danger">
                                        <i class="bi bi-files"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-semibold mb-1">Duplicate Content</h6>
                                        <p class="text-muted mb-0">Repeated paragraphs</p>
                                    </div>
                                </div>
                                <div class="section-actions">
                                    <span class="badge bg-danger">{{ count($duplicates) }}</span>
                                    <i class="bi bi-chevron-down transition-icon"></i>
                                </div>
                            </div>
                            <div class="collapse" id="duplicatesCollapse">
                                <div class="section-body">
                                    <div class="paragraphs-grid">
                                        @foreach($displayedDuplicates as $index => $paragraph)
                                        <div class="paragraph-card duplicate">
                                            <div class="paragraph-header">
                                                <span class="paragraph-index">#{{ $index + 1 }}</span>
                                                <span class="paragraph-stats text-danger">
                                                    <i class="bi bi-files me-1"></i>Duplicate
                                                </span>
                                            </div>
                                            <div class="paragraph-content">
                                                {{ $paragraph }}
                                            </div>
                                            <div class="paragraph-indicator">
                                                <i class="bi bi-files text-danger"></i>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.main-content-analysis {
    font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
}

/* Main Card */
.card {
    border-radius: 16px !important;
    overflow: hidden;
}

/* Header with Gradient */
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}

.header-icon {
    width: 50px;
    height: 50px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
}

.readability-score .score-badge {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    padding: 0.5rem 0.75rem;
    border-radius: 8px;
    text-align: center;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    min-width: 80px;
}

.score-value {
    display: block;
    font-size: 1.1rem;
    font-weight: bold;
    line-height: 1.2;
}

.score-label {
    font-size: 0.7rem;
    opacity: 0.9;
    line-height: 1.2;
    color: #ffffff !important;
}

/* Statistics Cards - REDUCED FONT SIZES */
.stats-grid {
    margin: 1.5rem 0;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 1rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    border: 1px solid #e9ecef;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    transition: all 0.3s ease;
    height: 100%;
    min-height: 90px;
    overflow: hidden;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    flex-shrink: 0;
}

.stat-card.success .stat-icon { background: #dcfce7; color: #16a34a; }
.stat-card.info .stat-icon { background: #dbeafe; color: #2563eb; }
.stat-card.warning .stat-icon { background: #fef3c7; color: #d97706; }
.stat-card.danger .stat-icon { background: #fee2e2; color: #dc2626; }
.stat-card.secondary .stat-icon { background: #f3f4f6; color: #6b7280; }
.stat-card.primary .stat-icon { background: #e0e7ff; color: #4f46e5; }

.stat-content {
    flex: 1;
    min-width: 0;
}

/* REDUCED FONT SIZES FOR KPI TITLES */
.stat-value {
    font-size: 0.85rem; /* Reduced from 0.95rem */
    font-weight: 700;
    margin-bottom: 0.2rem;
    color: #1f2937;
    line-height: 1.2;
    word-wrap: break-word;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.stat-label {
    font-size: 0.7rem; /* Reduced from 0.75rem */
    color: #6b7280;
    margin-bottom: 0.2rem;
    font-weight: 500;
    line-height: 1.2;
}

.stat-detail {
    font-size: 0.6rem; /* Reduced from 0.65rem */
    color: #9ca3af;
    line-height: 1.2;
    word-wrap: break-word;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

/* Analysis Sections */
.analysis-section {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgb(0 0 0 / 17%) !important;
    border: 1px solid #e9ecef;
    overflow: hidden;
    transition: all 0.3s ease;
}

.analysis-section:hover {
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
}

.section-header {
    padding: 1.25rem;
    background: white;
    border-bottom: 1px solid #f1f5f9;
    cursor: pointer;
    display: flex;
    justify-content: between;
    align-items: center;
    transition: background-color 0.2s ease;
}

.section-header:hover {
    background: #f8fafc;
}

.section-header[aria-expanded="true"] {
    background: #f8fafc;
}

.section-header[aria-expanded="true"] .transition-icon {
    transform: rotate(180deg);
}

.section-title {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-grow: 1;
    min-width: 0;
}

.title-icon {
    width: 40px;
    height: 40px;
    background: #f8fafc;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    color: #4f46e5;
    flex-shrink: 0;
}

.section-actions {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-shrink: 0;
}

.section-body {
    padding: 1.25rem;
    background: #f8fafc;
}

/* Content Preview */
.content-preview {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 1rem;
    max-height: 150px;
    overflow-y: auto;
}

.content-text {
    line-height: 1.6;
    color: #374151;
    font-family: 'Monaco', 'Consolas', monospace;
    font-size: 0.8rem;
    word-wrap: break-word;
}

.content-meta {
    border-top: 1px solid #e5e7eb;
    padding-top: 0.5rem;
}

/* Paragraphs Grid */
.paragraphs-grid {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    max-height: 300px;
    overflow-y: auto;
}

.paragraph-card {
    background: white;
    border-radius: 8px;
    padding: 0.75rem;
    border-left: 4px solid;
    position: relative;
    transition: all 0.2s ease;
    word-wrap: break-word;
}

.paragraph-card:hover {
    transform: translateX(2px);
}

.paragraph-card.good { border-left-color: #10b981; }
.paragraph-card.medium { border-left-color: #f59e0b; }
.paragraph-card.poor { border-left-color: #ef4444; }
.paragraph-card.duplicate { border-left-color: #dc2626; }

.paragraph-header {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: 0.25rem;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.paragraph-index {
    font-weight: 600;
    color: #6b7280;
    font-size: 0.75rem;
    flex-shrink: 0;
}

.paragraph-stats {
    font-size: 0.7rem;
    color: #9ca3af;
    flex-shrink: 0;
}

.paragraph-content {
    line-height: 1.4;
    color: #374151;
    font-size: 0.8rem;
    word-wrap: break-word;
}

.paragraph-indicator {
    position: absolute;
    top: 0.75rem;
    right: 0.75rem;
    font-size: 0.875rem;
    flex-shrink: 0;
}

.paragraph-card.good .paragraph-indicator { color: #10b981; }
.paragraph-card.medium .paragraph-indicator { color: #f59e0b; }
.paragraph-card.poor .paragraph-indicator { color: #ef4444; }
.paragraph-card.duplicate .paragraph-indicator { color: #dc2626; }

/* Animations */
.transition-icon {
    transition: transform 0.3s ease;
}

/* Responsive */
@media (max-width: 768px) {
    .card-header .d-flex {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
    }
    
    .header-icon {
        margin: 0 auto;
    }
    
    .stat-card {
        flex-direction: column;
        text-align: center;
        padding: 1rem;
        min-height: 110px;
    }
    
    .stat-content {
        width: 100%;
    }
    
    .section-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.75rem;
    }
    
    .section-actions {
        align-self: flex-end;
    }
    
    .stat-value {
        font-size: 0.8rem;
    }
    
    .stat-label {
        font-size: 0.65rem;
    }
}

@media (max-width: 576px) {
    .stats-grid .row {
        margin: 0 -0.5rem;
    }
    
    .stats-grid .col-xl-4 {
        padding: 0 0.5rem;
    }
    
    .stat-card {
        padding: 0.75rem;
    }
}
</style>

<script>
function copyContentToClipboard() {
    const content = `{{ addslashes($analysis->main_content) }}`;
    navigator.clipboard.writeText(content).then(() => {
        const btn = event.target;
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Copied!';
        btn.classList.remove('btn-outline-primary');
        btn.classList.add('btn-success');
        
        setTimeout(() => {
            btn.innerHTML = originalHTML;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-outline-primary');
        }, 2000);
    });
}

function toggleContentPreview() {
    const preview = document.getElementById('contentPreview');
    const isExpanded = preview.classList.contains('expanded');
    
    if (isExpanded) {
        preview.classList.remove('expanded');
        preview.style.maxHeight = '150px';
        event.target.innerHTML = '<i class="bi bi-arrows-expand me-1"></i>Expand';
    } else {
        preview.classList.add('expanded');
        preview.style.maxHeight = 'none';
        event.target.innerHTML = '<i class="bi bi-arrows-collapse me-1"></i>Collapse';
    }
}

// Collapse animations
document.addEventListener('DOMContentLoaded', function() {
    const collapses = document.querySelectorAll('.collapse');
    collapses.forEach(collapse => {
        collapse.addEventListener('show.bs.collapse', function() {
            const header = this.previousElementSibling;
            header.style.background = '#f8fafc';
        });
        
        collapse.addEventListener('hide.bs.collapse', function() {
            const header = this.previousElementSibling;
            header.style.background = 'white';
        });
    });
});
</script>