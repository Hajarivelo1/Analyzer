@props(['keywords' => []])

@php
    // Separate keywords by word count
    $singleWords = [];
    $twoWords = [];
    $threeWords = [];
    $fourWords = [];
    
    foreach ($keywords as $phrase => $count) {
        $wordCount = count(explode(' ', $phrase));
        
        switch ($wordCount) {
            case 1:
                $singleWords[$phrase] = $count;
                break;
            case 2:
                $twoWords[$phrase] = $count;
                break;
            case 3:
                $threeWords[$phrase] = $count;
                break;
            case 4:
                $fourWords[$phrase] = $count;
                break;
        }
    }
    
    // Sort by occurrence
    arsort($singleWords);
    arsort($twoWords);
    arsort($threeWords);
    arsort($fourWords);

    // Calculate totals for summary
    $totalKeywords = count($keywords);
    $totalPhrases = count($twoWords) + count($threeWords) + count($fourWords);
@endphp

<div {{ $attributes->merge(['class' => 'keywords-dashboard']) }}>
    <!-- Header with statistics -->
    <div class="dashboard-header">
        <div class="header-content">
            <div class="header-icon">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <div class="header-text">
                <h3 class="header-title">Keyword Analysis</h3>
                <p class="header-subtitle">Intelligent extraction of the most relevant terms</p>
            </div>
        </div>
        <div class="header-stats">
            <div class="stat-item">
                <div class="stat-number text-dark">{{ $totalKeywords }}</div>
                <div class="stat-label">Keywords</div>
            </div>
            <div class="stat-item text-dark">
                <div class="stat-number">{{ $totalPhrases }}</div>
                <div class="stat-label">Phrases</div>
            </div>
        </div>
    </div>

    <!-- Categories grid -->
    <div class="keywords-grid">
        <!-- Single words -->
        <div class="keyword-category card-hover">
            <div class="category-header">
                <div class="category-badge category-badge-primary">
                    <span class="badge-number">1</span>
                    <span class="badge-text">Word</span>
                </div>
                <div class="category-count">{{ count($singleWords) }}</div>
            </div>
            <div class="keywords-list">
                @forelse(array_slice($singleWords, 0, 8) as $phrase => $count)
                    <div class="keyword-item">
                        <span class="keyword-text">{{ $phrase }}</span>
                        <span class="keyword-count">{{ $count }}</span>
                    </div>
                @empty
                    <div class="empty-state">
                        <svg class="empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="empty-text">No single words</span>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- 2-word phrases -->
        <div class="keyword-category card-hover">
            <div class="category-header">
                <div class="category-badge category-badge-success">
                    <span class="badge-number">2</span>
                    <span class="badge-text">Words</span>
                </div>
                <div class="category-count">{{ count($twoWords) }}</div>
            </div>
            <div class="keywords-list">
                @forelse(array_slice($twoWords, 0, 8) as $phrase => $count)
                    <div class="keyword-item">
                        <span class="keyword-text">{{ $phrase }}</span>
                        <span class="keyword-count">{{ $count }}</span>
                    </div>
                @empty
                    <div class="empty-state">
                        <svg class="empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="empty-text">No phrases found</span>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- 3-word phrases -->
        <div class="keyword-category card-hover">
            <div class="category-header">
                <div class="category-badge category-badge-warning">
                    <span class="badge-number">3</span>
                    <span class="badge-text">Words</span>
                </div>
                <div class="category-count">{{ count($threeWords) }}</div>
            </div>
            <div class="keywords-list">
                @forelse(array_slice($threeWords, 0, 6) as $phrase => $count)
                    <div class="keyword-item">
                        <span class="keyword-text">{{ $phrase }}</span>
                        <span class="keyword-count">{{ $count }}</span>
                    </div>
                @empty
                    <div class="empty-state">
                        <svg class="empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="empty-text">No phrases found</span>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- 4-word phrases -->
        <div class="keyword-category card-hover">
            <div class="category-header">
                <div class="category-badge category-badge-danger">
                    <span class="badge-number">4</span>
                    <span class="badge-text">Words</span>
                </div>
                <div class="category-count">{{ count($fourWords) }}</div>
            </div>
            <div class="keywords-list">
                @forelse(array_slice($fourWords, 0, 4) as $phrase => $count)
                    <div class="keyword-item">
                        <span class="keyword-text">{{ $phrase }}</span>
                        <span class="keyword-count">{{ $count }}</span>
                    </div>
                @empty
                    <div class="empty-state">
                        <svg class="empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="empty-text">No phrases found</span>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Advanced summary -->
    @if(!empty($keywords))
    <div class="keywords-summary">
        <div class="summary-header">
            <h4 class="summary-title text-white px-2">Keyword Distribution</h4>
            <div class="summary-legend">
                <div class="legend-item">
                    <div class="legend-color legend-primary"></div>
                    <span class="text-white">Single words</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color legend-success"></div>
                    <span class="text-white">Phrases</span>
                </div>
            </div>
        </div>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-value">{{ count($singleWords) }}</div>
                <div class="summary-label">Single words</div>
                <div class="summary-bar">
                    <div class="bar-fill bar-primary" style="width: {{ $totalKeywords > 0 ? (count($singleWords) / $totalKeywords * 100) : 0 }}%"></div>
                </div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ count($twoWords) }}</div>
                <div class="summary-label">Bigrams</div>
                <div class="summary-bar">
                    <div class="bar-fill bar-success" style="width: {{ $totalKeywords > 0 ? (count($twoWords) / $totalKeywords * 100) : 0 }}%"></div>
                </div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ count($threeWords) }}</div>
                <div class="summary-label">Trigrams</div>
                <div class="summary-bar">
                    <div class="bar-fill bar-warning" style="width: {{ $totalKeywords > 0 ? (count($threeWords) / $totalKeywords * 100) : 0 }}%"></div>
                </div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ count($fourWords) }}</div>
                <div class="summary-label">Quadrigrams</div>
                <div class="summary-bar">
                    <div class="bar-fill bar-danger" style="width: {{ $totalKeywords > 0 ? (count($fourWords) / $totalKeywords * 100) : 0 }}%"></div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<style>
.keywords-dashboard {
    background-color: #fff;
    border-radius: 16px !important;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    overflow: hidden !important;
    margin-bottom: 2rem !important;
    padding-bottom: 1rem; /* 🔥 Additional internal space */
}

/* Header */
.dashboard-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 24px;
    display: flex;
    justify-content: between;
    align-items: center;
    gap: 24px;
}

.header-content {
    display: flex;
    align-items: center;
    gap: 16px;
    flex: 1;
}

.header-icon {
    background: rgba(255, 255, 255, 0.2);
    padding: 12px;
    border-radius: 12px;
    backdrop-filter: blur(10px);
}

.header-icon svg {
    width: 24px;
    height: 24px;
}

.header-title {
    font-size: 20px;
    font-weight: 600;
    margin: 0 0 4px 0;
}

.header-subtitle {
    font-size: 14px;
    opacity: 0.9;
    margin: 0;
}

.header-stats {
    display: flex;
    gap: 32px;
}

.stat-item {
    text-align: center;
}

.stat-number {
    font-size: 24px;
    font-weight: 700;
    line-height: 1;
}

.stat-label {
    font-size: 12px;
    opacity: 0.8;
    margin-top: 4px;
}

/* Grid */
.keywords-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
    padding: 24px;
}

.keyword-category {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 20px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.card-hover:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    border-color: #cbd5e0;
}

.category-header {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: 16px;
    padding-bottom: 12px;
    border-bottom: 1px solid #e2e8f0;
}

.category-badge {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.badge-number {
    background: white;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
}

.category-badge-primary { background: #dbeafe; color: #1e40af; }
.category-badge-success { background: #d1fae5; color: #065f46; }
.category-badge-warning { background: #fef3c7; color: #92400e; }
.category-badge-danger { background: #fecaca; color: #991b1b; }

.category-count {
    font-size: 18px;
    font-weight: 700;
    color: #1e293b;
}

/* Keywords List */
.keywords-list {
    space-y: 8px;
}

.keyword-item {
    display: flex;
    justify-content: between;
    align-items: center;
    padding: 10px 12px;
    background: white;
    border: 1px solid #f1f5f9;
    border-radius: 8px;
    transition: all 0.2s ease;
}

.keyword-item:hover {
    background: #f8fafc;
    border-color: #e2e8f0;
    transform: translateX(4px);
}

.keyword-text {
    font-size: 13px;
    font-weight: 500;
    color: #334155;
    flex: 1;
}

.keyword-count {
    font-size: 11px;
    font-weight: 600;
    background: #64748b;
    color: white;
    padding: 4px 8px;
    border-radius: 12px;
    min-width: 24px;
    text-align: center;
}

/* Empty State */
.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 32px 16px;
    color: #94a3b8;
    text-align: center;
}

.empty-icon {
    width: 48px;
    height: 48px;
    margin-bottom: 12px;
    opacity: 0.5;
}

.empty-text {
    font-size: 14px;
    font-weight: 500;
}

/* Summary */
.keywords-summary {
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    padding: 24px;
}

.summary-header {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: 20px;
}

.summary-title {
    font-size: 16px;
    font-weight: 600;
    color: #1e293b;
    margin: 0;
}

.summary-legend {
    display: flex;
    gap: 16px;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: #64748b;
}

.legend-color {
    width: 12px;
    height: 12px;
    border-radius: 2px;
}

.legend-primary { background: #3b82f6; }
.legend-success { background: #10b981; }

.summary-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 16px;
}

.summary-item {
    text-align: center;
}

.summary-value {
    font-size: 20px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 4px;
}

.summary-label {
    font-size: 12px;
    color: #64748b;
    margin-bottom: 8px;
}

.summary-bar {
    background: #e2e8f0;
    height: 6px;
    border-radius: 3px;
    overflow: hidden;
}

.bar-fill {
    height: 100%;
    border-radius: 3px;
    transition: width 0.5s ease;
}

.bar-primary { background: #3b82f6; }
.bar-success { background: #10b981; }
.bar-warning { background: #f59e0b; }
.bar-danger { background: #ef4444; }

/* Responsive */
@media (max-width: 768px) {
    .dashboard-header {
        flex-direction: column;
        text-align: center;
        gap: 16px;
    }
    
    .header-content {
        flex-direction: column;
        text-align: center;
    }
    
    .header-stats {
        justify-content: center;
    }
    
    .keywords-grid {
        grid-template-columns: 1fr;
        padding: 16px;
    }
    
    .summary-header {
        flex-direction: column;
        gap: 12px;
        text-align: center;
    }
    
    .summary-legend {
        justify-content: center;
    }
}
</style>