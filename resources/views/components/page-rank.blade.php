@props([
    'analysis' => null
])

@php
    $rank = !is_null($analysis->page_rank) ? round($analysis->page_rank, 2) : null;
    $globalRank = $analysis->page_rank_global ?? null;
    
    // Determine level and colors
    if (!is_null($rank)) {
        if ($rank >= 7) {
            $color = '#10b981';
            $gradient = 'linear-gradient(135deg, #10b981 0%, #059669 100%)';
            $icon = 'bi-trophy-fill';
            $level = 'Excellent';
            $bgColor = 'rgba(16, 185, 129, 0.1)';
        } elseif ($rank >= 4) {
            $color = '#f59e0b';
            $gradient = 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)';
            $icon = 'bi-graph-up-arrow';
            $level = 'Medium';
            $bgColor = 'rgba(245, 158, 11, 0.1)';
        } else {
            $color = '#ef4444';
            $gradient = 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)';
            $icon = 'bi-exclamation-triangle-fill';
            $level = 'Low';
            $bgColor = 'rgba(239, 68, 68, 0.1)';
        }
    }
@endphp

<div data-pagerank-section>
    @if(!is_null($rank))
        <div class="pagerank-card mb-4">
            <!-- Header with icon and title -->
            <div class="pagerank-header" style="background: {{ $gradient }};">
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
                    {{ $rank }}/10
                </div>
            </div>

            <!-- Main content -->
            <div class="pagerank-content">
                <!-- Score and visual indicator -->
                <div class="score-section">
                    <div class="score-display">
                        <div class="score-circle">
                            <div class="circle-progress" style="--progress: {{ $rank * 10 }}%; --color: {{ $color }};">
                                <div class="score-number">{{ $rank }}</div>
                                <div class="score-label">/10</div>
                            </div>
                        </div>
                        <div class="score-details">
                            <div class="score-level" style="color: {{ $color }};">
                                <i class="bi {{ $icon }} me-2"></i>
                                {{ $level }}
                            </div>
                            @if(!is_null($globalRank))
                                <div class="global-rank">
                                    <div class="rank-label">Global Ranking</div>
                                    <div class="rank-value">#{{ number_format($globalRank) }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Detailed progress bar -->
                <div class="progress-section">
                    <div class="progress-labels">
                        <span>0</span>
                        <span>5</span>
                        <span>10</span>
                    </div>
                    <div class="progress-bar-container">
                        <div class="progress-bar-bg">
                            <div class="progress-bar-fill" style="width: {{ $rank * 10 }}%; background: {{ $gradient }};"></div>
                        </div>
                        <div class="progress-indicator" style="left: {{ $rank * 10 }}%; background: {{ $color }};">
                            <div class="indicator-tooltip">{{ $rank }}</div>
                        </div>
                    </div>
                    <div class="progress-levels">
                        <div class="level-item">
                            <div class="level-dot" style="background: #ef4444;"></div>
                            <span>Low</span>
                        </div>
                        <div class="level-item">
                            <div class="level-dot" style="background: #f59e0b;"></div>
                            <span>Medium</span>
                        </div>
                        <div class="level-item">
                            <div class="level-dot" style="background: #10b981;"></div>
                            <span>Excellent</span>
                        </div>
                    </div>
                </div>

                <!-- Contextual information -->
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

            <!-- Footer with timestamp -->
            <div class="pagerank-footer">
                <div class="last-updated">
                    <i class="bi bi-clock-history"></i>
                    Last updated {{ now()->format('M d, Y \a\t H:i') }}
                </div>
            </div>
        </div>
    @else
        <!-- Loading state -->
        <div class="pagerank-card mb-4">
            <div class="pagerank-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="header-content">
                    <div class="header-icon">
                        <i class="bi bi-bar-chart-fill"></i>
                    </div>
                    <div>
                        <h3 class="pagerank-title">Domain PageRank</h3>
                        <p class="pagerank-subtitle">Calculating authority score</p>
                    </div>
                </div>
                <div class="domain-score" style="background: rgba(255, 255, 255, 0.2); color: white;">
                    ...
                </div>
            </div>

            <div class="pagerank-content">
                <div class="loading-state">
                    <div class="loading-spinner">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <div class="loading-content">
                        <h4>Analyzing Domain Authority</h4>
                        <p>We're calculating the PageRank score based on global web data. This may take a few moments.</p>
                        <div class="progress mt-3" style="height: 6px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 65%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>