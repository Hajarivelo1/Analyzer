<div class="page-speed-metrics mb-4">
    {{-- Debug (à enlever après test) --}}
    <div style="display: none;">
        Performance: {{ $performanceScore ?? 'NULL' }}<br>
        All Scores: {{ json_encode($allScores ?? []) }}<br>
        Metrics count: {{ count($metrics ?? []) }}
    </div>

    {{-- Scores principaux --}}
    @if(isset($allScores) && count($allScores) > 0)
        <div class="scores-grid mb-4">
          
            <div style="background-color: #dbe1f7;" class="px-4 py-3 rounded-top mb-4">
        <h5 class="fw-bold mb-0" style=" color:#2e4db6;">PageSpeed Score</h5>
    </div>
            <div class="row">
                {{-- Performance --}}
                <div class="col-md-3 col-6 mb-3">
                    <div class="score-card text-center p-3">
                        <div class="score-category">Performance</div>
                        <div class="score-value h3 {{ 
                            ($performanceScore ?? 0) >= 90 ? 'text-success' : 
                            (($performanceScore ?? 0) >= 50 ? 'text-warning' : 'text-danger') 
                        }}">
                            {{ $performanceScore ?? 'N/A' }}
                        </div>
                        <div class="score-label">/100</div>
                        @if($performanceScore >= 90)
                            <small class="text-success">Excellent</small>
                        @elseif($performanceScore >= 50)
                            <small class="text-warning">To be improved</small>
                        @else
                            <small class="text-danger">Low</small>
                        @endif
                    </div>
                </div>

                {{-- Autres scores --}}
                @foreach($allScores as $category => $score)
                    @if($category !== 'performance')
                        <div class="col-md-3 col-6 mb-3">
                            <div class="score-card text-center p-3">
                                <div class="score-category">{{ ucfirst($category) }}</div>
                                <div class="score-value h3 {{ 
                                    $score >= 90 ? 'text-success' : 
                                    ($score >= 50 ? 'text-warning' : 'text-danger') 
                                }}">
                                    {{ $score ?? 'N/A' }}
                                </div>
                                <div class="score-label">/100</div>
                                @if($score >= 90)
                                    <small class="text-success">Excellent</small>
                                @elseif($score >= 50)
                                    <small class="text-warning">Good</small>
                                @else
                                    <small class="text-danger">Low</small>
                                @endif
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    {{-- Métriques détaillées Core Web Vitals --}}
    @if($metrics !== null && is_array($metrics) && count($metrics) > 0)
        <div class="metrics-section">
           
          
            <div style="background-color: #dbe1f7;" class="px-4 py-3 rounded-top mb-4">
        
        <h5 class="fw-bold mb-0" style=" color:#2e4db6;">Core Web Vitals</h5>
       
    </div>
            <div class="metrics-grid">
                @foreach($metrics as $metricKey => $metricData)
                    @if(is_array($metricData) && isset($metricData['title']))
                        <div class="metric-item">
                            <div class="metric-header">
                                <span class="metric-name">{{ $metricData['title'] }}</span>
                                @if(isset($metricData['score']))
                                    <span class="metric-score badge badge-{{ $metricData['score'] >= 0.9 ? 'success' : ($metricData['score'] >= 0.5 ? 'warning' : 'danger') }}">
                                        {{ round($metricData['score'] * 100) }}%
                                    </span>
                                @endif
                            </div>
                            <span class="metric-value">
                                {{ $metricData['displayValue'] ?? 'N/A' }}
                            </span>
                            @if(isset($metricData['score']))
                                <div class="metric-progress">
                                    <div class="progress" style="height: 4px;">
                                        <div class="progress-bar {{ 
                                            $metricData['score'] >= 0.9 ? 'bg-success' : 
                                            ($metricData['score'] >= 0.5 ? 'bg-warning' : 'bg-danger') 
                                        }}" style="width: {{ $metricData['score'] * 100 }}%"></div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @else
        <p class="text-muted">Metrics loading...</p>
    @endif
</div>