{{-- Section Résumé IA --}}
@if(isset($ai) && ($ai['score'] || !empty($ai['issues']) || !empty($ai['priorities']) || !empty($ai['checklist']) || !empty($ai['raw'])))
    <div class="card border-0 shadow-lg mb-4 ai-summary-card">
        <div class="card-header bg-gradient-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <i class="bi bi-robot me-3 fs-4"></i>
                    <div>
                        <h4 class="mb-0 fw-bold">Analyse SEO Intelligente</h4>
                        <small class="opacity-75">Résumé généré par intelligence artificielle</small>
                    </div>
                </div>
                @if(isset($ai['score']) && !is_null($ai['score']))
                    <div class="text-end">
                        <div class="score-badge">
                            <span class="score-number">{{ $ai['score'] }}</span>
                            <span class="score-label">/100</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        
        <div class="card-body p-4">
            {{-- Aucune donnée IA --}}
            @if(empty($ai['score']) && empty($ai['issues']) && empty($ai['priorities']) && empty($ai['checklist']) && empty($ai['raw']))
                <div class="text-center py-5">
                    <div class="loading-animation mb-3">
                        <i class="bi bi-cpu fs-1 text-primary"></i>
                    </div>
                    <h5 class="text-muted mb-2">Analyse en cours de génération</h5>
                    <p class="text-muted small">Notre IA analyse votre site pour fournir des recommandations personnalisées</p>
                </div>

            {{-- Afficher le contenu brut formaté --}}
            @elseif(!empty($ai['raw']))
                <div class="ai-markdown-content">
                    <div class="markdown-header bg-light rounded-top p-3 border-bottom">
                        <i class="bi bi-markdown me-2 text-primary"></i>
                        <span class="fw-semibold">Rapport détaillé</span>
                    </div>
                    <div class="markdown-body p-4">
                        {!! \Illuminate\Support\Str::markdown($ai['raw']) !!}
                    </div>
                </div>

            {{-- Affichage structuré --}}
            @else
                {{-- Score avec indicateur visuel amélioré --}}
                @if(isset($ai['score']) && !is_null($ai['score']))
                    <div class="score-section mb-5">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <div class="progress-circle-lg me-4">
                                        <div class="progress-circle-inner" data-score="{{ $ai['score'] }}">
                                            <span class="progress-value fw-bold">{{ $ai['score'] }}</span>
                                        </div>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-1">Score SEO Global</h5>
                                        @php
                                            $scoreStatus = match(true) {
                                                $ai['score'] >= 80 => ['class' => 'text-success', 'icon' => 'bi-trophy', 'text' => 'Excellent'],
                                                $ai['score'] >= 60 => ['class' => 'text-primary', 'icon' => 'bi-check-circle', 'text' => 'Bon'],
                                                $ai['score'] >= 40 => ['class' => 'text-warning', 'icon' => 'bi-exclamation-triangle', 'text' => 'Moyen'],
                                                default => ['class' => 'text-danger', 'icon' => 'bi-exclamation-octagon', 'text' => 'À améliorer']
                                            };
                                        @endphp
                                        <div class="d-flex align-items-center {{ $scoreStatus['class'] }}">
                                            <i class="bi {{ $scoreStatus['icon'] }} me-2"></i>
                                            <span class="fw-semibold">{{ $scoreStatus['text'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="score-breakdown">
                                    <div class="breakdown-item">
                                        <span class="breakdown-label">Performance</span>
                                        <div class="breakdown-bar">
                                            <div class="breakdown-fill" style="width: {{ min(100, $ai['score'] + 10) }}%"></div>
                                        </div>
                                    </div>
                                    <div class="breakdown-item">
                                        <span class="breakdown-label">Optimisation</span>
                                        <div class="breakdown-bar">
                                            <div class="breakdown-fill" style="width: {{ min(100, $ai['score'] + 5) }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Problèmes détectés --}}
                @if(!empty($ai['issues']) && is_array($ai['issues']))
                    <div class="issues-section mb-5">
                        <div class="section-header mb-4">
                            <div class="d-flex align-items-center">
                                <div class="icon-wrapper bg-danger bg-opacity-10 text-danger rounded-circle p-3 me-3">
                                    <i class="bi bi-exclamation-octagon fs-5"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-1">Problèmes Identifiés</h5>
                                    <span class="text-muted">{{ count($ai['issues']) }} points à corriger</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="issues-grid">
                            @foreach($ai['issues'] as $index => $issue)
                                <div class="issue-card">
                                    <div class="issue-number">{{ $index + 1 }}</div>
                                    <div class="issue-content">
                                        <p class="mb-0">{{ $issue }}</p>
                                    </div>
                                    <div class="issue-action">
                                        <i class="bi bi-chevron-right text-muted"></i>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Priorités d'optimisation --}}
                @if(!empty($ai['priorities']) && is_array($ai['priorities']))
                    <div class="priorities-section mb-5">
                        <div class="section-header mb-4">
                            <div class="d-flex align-items-center">
                                <div class="icon-wrapper bg-warning bg-opacity-10 text-warning rounded-circle p-3 me-3">
                                    <i class="bi bi-flag fs-5"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-1">Priorités d'Optimisation</h5>
                                    <span class="text-muted">{{ count($ai['priorities']) }} actions recommandées</span>
                                </div>
                            </div>
                        </div>

                        <div class="priorities-list">
                            @foreach($ai['priorities'] as $priority)
                                @php
                                    if (is_array($priority)) {
                                        $effort = $priority['effort'] ?? '';
                                        $item = $priority['item'] ?? 'Action non spécifiée';
                                        $detail = $priority['detail'] ?? null;
                                    } else {
                                        $effort = 'Moyen';
                                        $item = $priority;
                                        $detail = null;
                                    }
                                    
                                    $priorityConfig = match(strtolower($effort)) {
                                        'urgent', 'haute', 'must-have', 'critique' => [
                                            'class' => 'priority-high',
                                            'icon' => 'bi-arrow-up-circle',
                                            'text' => 'Haute priorité'
                                        ],
                                        'moyen', 'moyenne', 'should-have', 'normal' => [
                                            'class' => 'priority-medium', 
                                            'icon' => 'bi-arrow-right-circle',
                                            'text' => 'Priorité moyenne'
                                        ],
                                        'long terme', 'basse', 'nice-to-have', 'faible' => [
                                            'class' => 'priority-low',
                                            'icon' => 'bi-arrow-down-circle',
                                            'text' => 'Long terme'
                                        ],
                                        default => [
                                            'class' => 'priority-medium',
                                            'icon' => 'bi-circle',
                                            'text' => $effort ?: 'À définir'
                                        ]
                                    };
                                @endphp
                                
                                <div class="priority-item {{ $priorityConfig['class'] }}">
                                    <div class="priority-icon">
                                        <i class="bi {{ $priorityConfig['icon'] }}"></i>
                                    </div>
                                    <div class="priority-content">
                                        <h6 class="fw-semibold mb-1">{{ $item }}</h6>
                                        @if($detail)
                                            <p class="text-muted mb-0 small">{{ $detail }}</p>
                                        @endif
                                    </div>
                                    <div class="priority-badge">
                                        <span class="badge">{{ $priorityConfig['text'] }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Checklist actionnable --}}
                @if(!empty($ai['checklist']) && is_array($ai['checklist']))
                    <div class="checklist-section">
                        <div class="section-header mb-4">
                            <div class="d-flex align-items-center">
                                <div class="icon-wrapper bg-success bg-opacity-10 text-success rounded-circle p-3 me-3">
                                    <i class="bi bi-check-square fs-5"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-1">Checklist Actionnable</h5>
                                    <span class="text-muted">{{ count($ai['checklist']) }} tâches à accomplir</span>
                                </div>
                            </div>
                        </div>

                        <div class="checklist-items">
                            @foreach($ai['checklist'] as $index => $task)
                                <div class="checklist-item">
                                    <input type="checkbox" class="checklist-checkbox" id="task-{{ $index }}">
                                    <label for="task-{{ $index }}" class="checklist-label">
                                        <span class="checklist-text">{{ $task }}</span>
                                        <span class="checklist-toggle">
                                            <i class="bi bi-check-lg"></i>
                                        </span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif
        </div>
        
        {{-- Pied de carte --}}
        @if(!empty($ai['score']) || !empty($ai['issues']) || !empty($ai['raw']))
            <div class="card-footer bg-light py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        <i class="bi bi-clock-history me-1"></i>
                        Généré le 
                        @if(isset($analysis->ai_generated_at))
                            {{ $analysis->ai_generated_at->format('d/m/Y à H:i') }}
                        @else
                            {{ now()->format('d/m/Y à H:i') }}
                        @endif
                    </small>
                    <div class="ai-badge">
                        <i class="bi bi-cpu me-1"></i>
                        <small>Powered by AI</small>
                    </div>
                </div>
            </div>
        @endif
    </div>
@else
    {{-- Message si pas de données IA --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body text-center py-5">
            <div class="ai-placeholder mb-3">
                <i class="bi bi-robot fs-1 text-muted opacity-50"></i>
            </div>
            <h5 class="text-muted mb-2">En attente de l'analyse IA</h5>
            <p class="text-muted small mb-0">L'analyse SEO est en cours de traitement</p>
            <p class="text-muted small">Le résumé intelligent sera disponible sous peu</p>
        </div>
    </div>
@endif

<style>
.ai-summary-card {
    border-radius: 16px;
    overflow: hidden;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

/* Score Section */
.score-badge {
    background: rgba(255,255,255,0.2);
    padding: 8px 16px;
    border-radius: 20px;
    backdrop-filter: blur(10px);
}

.score-number {
    font-size: 1.5rem;
    font-weight: bold;
}

.score-label {
    font-size: 0.875rem;
    opacity: 0.8;
}

.progress-circle-lg {
    position: relative;
    width: 100px;
    height: 100px;
}

.progress-circle-inner {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background: conic-gradient(#10b981 0% calc(var(--score-percent) * 1%), #e5e7eb calc(var(--score-percent) * 1%) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

.progress-circle-inner::before {
    content: '';
    position: absolute;
    width: 80px;
    height: 80px;
    background: white;
    border-radius: 50%;
}

.progress-value {
    position: relative;
    font-size: 1.5rem;
    color: #10b981;
}

.score-breakdown {
    padding: 20px;
    background: #f8fafc;
    border-radius: 12px;
}

.breakdown-item {
    margin-bottom: 12px;
}

.breakdown-label {
    font-size: 0.875rem;
    color: #64748b;
    margin-bottom: 4px;
    display: block;
}

.breakdown-bar {
    height: 6px;
    background: #e2e8f0;
    border-radius: 3px;
    overflow: hidden;
}

.breakdown-fill {
    height: 100%;
    background: linear-gradient(90deg, #3b82f6, #10b981);
    border-radius: 3px;
    transition: width 0.8s ease;
}

/* Section Headers */
.section-header {
    border-bottom: 2px solid #f1f5f9;
    padding-bottom: 1rem;
}

.icon-wrapper {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Issues Grid */
.issues-grid {
    display: grid;
    gap: 12px;
}

.issue-card {
    display: flex;
    align-items: center;
    padding: 16px;
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: 12px;
    transition: all 0.3s ease;
}

.issue-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.1);
}

.issue-number {
    background: #ef4444;
    color: white;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 0.875rem;
    margin-right: 16px;
    flex-shrink: 0;
}

.issue-content {
    flex: 1;
}

.issue-action {
    color: #9ca3af;
}

/* Priorities */
.priorities-list {
    display: grid;
    gap: 12px;
}

.priority-item {
    display: flex;
    align-items: center;
    padding: 20px;
    border-radius: 12px;
    transition: all 0.3s ease;
}

.priority-item:hover {
    transform: translateX(4px);
}

.priority-high {
    background: #fef2f2;
    border-left: 4px solid #ef4444;
}

.priority-medium {
    background: #fffbeb;
    border-left: 4px solid #f59e0b;
}

.priority-low {
    background: #f0f9ff;
    border-left: 4px solid #0ea5e9;
}

.priority-icon {
    margin-right: 16px;
    font-size: 1.25rem;
}

.priority-high .priority-icon { color: #ef4444; }
.priority-medium .priority-icon { color: #f59e0b; }
.priority-low .priority-icon { color: #0ea5e9; }

.priority-content {
    flex: 1;
}

.priority-badge .badge {
    font-size: 0.75rem;
    padding: 4px 8px;
}

.priority-high .priority-badge .badge { background: #ef4444; }
.priority-medium .priority-badge .badge { background: #f59e0b; }
.priority-low .priority-badge .badge { background: #0ea5e9; }

/* Checklist */
.checklist-items {
    display: grid;
    gap: 8px;
}

.checklist-item {
    position: relative;
}

.checklist-checkbox {
    display: none;
}

.checklist-label {
    display: flex;
    align-items: center;
    padding: 16px;
    background: #f8fafc;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.checklist-label:hover {
    border-color: #3b82f6;
    background: #f0f9ff;
}

.checklist-checkbox:checked + .checklist-label {
    background: #f0f9ff;
    border-color: #3b82f6;
    text-decoration: line-through;
    color: #64748b;
}

.checklist-text {
    flex: 1;
    margin-right: 12px;
}

.checklist-toggle {
    width: 24px;
    height: 24px;
    border: 2px solid #d1d5db;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.checklist-checkbox:checked + .checklist-label .checklist-toggle {
    background: #3b82f6;
    border-color: #3b82f6;
    color: white;
}

/* Markdown Content */
.markdown-body {
    background: white;
    border-radius: 0 0 12px 12px;
}

.markdown-header {
    border-radius: 12px 12px 0 0;
}

.ai-markdown-content table {
    width: 100%;
    border-collapse: collapse;
    margin: 1rem 0;
    font-size: 0.9rem;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.ai-markdown-content table th {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-weight: 600;
    padding: 12px 16px;
}

.ai-markdown-content table td {
    padding: 12px 16px;
    border-bottom: 1px solid #e5e7eb;
}

.ai-markdown-content table tr:hover {
    background: #f8fafc;
}

/* Responsive */
@media (max-width: 768px) {
    .progress-circle-lg {
        width: 80px;
        height: 80px;
    }
    
    .progress-circle-inner::before {
        width: 60px;
        height: 60px;
    }
    
    .progress-value {
        font-size: 1.25rem;
    }
    
    .priority-item {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .priority-icon {
        margin-bottom: 8px;
    }
    
    .priority-badge {
        margin-top: 8px;
    }
}
</style>

<script>
// Animation du score
document.addEventListener('DOMContentLoaded', function() {
    const progressCircles = document.querySelectorAll('.progress-circle-inner');
    
    progressCircles.forEach(circle => {
        const score = parseInt(circle.getAttribute('data-score'));
        circle.style.setProperty('--score-percent', score);
    });
    
    // Animation des barres de progression
    const breakdownFills = document.querySelectorAll('.breakdown-fill');
    breakdownFills.forEach(fill => {
        const width = fill.style.width;
        fill.style.width = '0%';
        setTimeout(() => {
            fill.style.width = width;
        }, 500);
    });
});
</script>