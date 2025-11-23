@extends('admin.admin_master')
@section('admin')



<link rel="stylesheet" href="{{ asset('css/projects.css?v=' . filemtime(public_path('css/projects.css'))) }}">

<style>
.glass-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.progress-steps {
    display: flex;
    justify-content: space-between;
    margin: 2rem 0;
    position: relative;
}

.progress-steps::before {
    content: '';
    position: absolute;
    top: 20px;
    left: 10%;
    right: 10%;
    height: 2px;
    background: #e9ecef;
    z-index: 1;
}

.step {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    z-index: 2;
    opacity: 0.5;
    transition: all 0.3s ease;
}

.step.active {
    opacity: 1;
    transform: scale(1.1);
}

.step-icon {
    width: 40px;
    height: 40px;
    background: white;
    border: 2px solid #e9ecef;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 0.5rem;
    transition: all 0.3s ease;
}

.step.active .step-icon {
    background: #007bff;
    border-color: #007bff;
    color: white;
}

.step-text {
    font-size: 0.8rem;
    font-weight: 600;
    color: #6c757d;
    text-align: center;
}

.step.active .step-text {
    color: #007bff;
}

.progress-container {
    position: relative;
}

.progress {
    height: 8px;
    background: #e9ecef;
    border-radius: 4px;
    overflow: hidden;
}

.progress-bar {
    transition: width 0.6s ease;
    background: linear-gradient(45deg, #007bff, #0056b3);
}

.progress-percentage {
    position: absolute;
    right: 0;
    top: -25px;
    font-weight: 600;
    color: #007bff;
}

.time-estimate {
    margin-top: 1rem;
}

/* Animation du spinner */
.spinner-border {
    width: 3rem;
    height: 3rem;
    border-width: 0.3em;
}

/* Responsive */
@media (max-width: 576px) {
    .progress-steps::before {
        left: 5%;
        right: 5%;
    }
    
    .step-text {
        font-size: 0.7rem;
    }
}
</style>

<div class="container-fluid py-4">
    <!-- Header Principal -->
    <div class="projects-header-main mb-5">
        <div class="projects-header-content">
            <div class="projects-header-text">
                <h1 class="projects-header-title">Projects Management</h1>
                <p class="projects-header-subtitle">Monitor and manage all your SEO analysis projects</p>
            </div>
            <!-- ✅ Bouton Refresh Cache - Pour tous les users -->
    <form action="{{ route('projects.refresh-cache') }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="projects-add-btn mx-4" style="background: rgba(255, 255, 255, 0.15);" 
                title="Refresh my projects cache">
            <i class="bi bi-arrow-clockwise"></i>
            Refresh My Cache
        </button>
    </form>
            <a href="{{route('add.projects')}}" class="projects-add-btn">
                <i class="bi bi-plus-circle"></i>
                Add New Project
            </a>
        </div>
        
        <!-- Statistiques en Grid -->
        <div class="projects-stats-grid">
            <div class="projects-stat-card">
                <div class="projects-stat-icon">
                    <i class="bi bi-folder"></i>
                </div>
                <div class="projects-stat-content">
                    <div class="projects-stat-value">{{ $totalProjects }}</div>
                    <div class="projects-stat-label">Total Projects</div>
                </div>
            </div>
            <div class="projects-stat-card">
                <div class="projects-stat-icon">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="projects-stat-content">
                    <div class="projects-stat-value">{{ $activeProjects }}</div>
                    <div class="projects-stat-label">Active Projects</div>
                </div>
            </div>
            <div class="projects-stat-card">
                <div class="projects-stat-icon">
                    <i class="bi bi-graph-up"></i>
                </div>
                <div class="projects-stat-content">
                    <div class="projects-stat-value">{{ $totalAnalyses }}</div>
                    <div class="projects-stat-label">Total Analyses</div>
                </div>
            </div>
            <div class="projects-stat-card">
                <div class="projects-stat-icon">
                    <i class="bi bi-speedometer2"></i>
                </div>
                <div class="projects-stat-content">
                    <div class="projects-stat-value">{{ number_format($avgScore, 1) }}%</div>
                    <div class="projects-stat-label">Avg SEO Score</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des Projets -->
    <div class="projects-list-grid">
        @foreach($projects as $project)
        <div class="project-item-card">
            <!-- En-tête du Projet -->
            <div class="project-item-header">
                <div class="project-item-icon">
                    <i class="bi bi-folder-fill"></i>
                </div>
                <div class="project-item-actions">
                    <div class="dropdown">
                        <button class="project-action-btn" data-bs-toggle="dropdown" aria-label="Project options">
                            <i class="bi bi-three-dots"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="{{route('project.show', $project->id)}}">
                                    <i class="bi bi-eye"></i>
                                    View Details
                                </a>
                            </li>
                            <li>
                            <a class="dropdown-item start-analysis-btn" href="#" 
           data-project-id="{{ $project->id }}" 
           data-project-url="{{ $project->base_url }}"
           data-project-name="{{ $project->name }}">
            <i class="bi bi-graph-up"></i>
            Analyze
        </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('projects.destroy', $project->id) }}" method="POST" class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button class="dropdown-item project-delete-btn" type="submit">
                                        <i class="bi bi-trash"></i>
                                        Delete Project
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Contenu du Projet -->
            <div class="project-item-content">
                <h3 class="project-item-name">{{ $project->name }}</h3>
                <p class="project-item-url">{{ $project->base_url }}</p>
                
                <!-- Statistiques du Projet -->
<!-- Statistiques du Projet -->
<div class="project-item-metrics">
    <div class="project-metric-item">
        <div class="project-metric-value">{{ $project->analyses_count }}</div>
        <div class="project-metric-label">Analyses</div>
    </div>
    <div class="project-metric-item">
        <div class="project-metric-value">
            @php
                // ⚡ CORRECTION : Utiliser les données attachées
                $currentScore = $project->current_score;
                $totalAnalyses = $project->analyses_count;
                
                // DEBUG TEMPORAIRE
                $scoreBase = $project->latest_analysis ? $project->latest_analysis->score : null;
                $scoreDynamique = $project->current_score;
            @endphp
            
            {{-- DEBUG TEMPORAIRE - À SUPPRIMER --}}
            <div style="font-size: 10px; color: #666; margin-bottom: 5px;">
                Base: {{ $scoreBase ?? 'N/A' }} | Calc: {{ $scoreDynamique ?? 'N/A' }}
            </div>
            
            @if($currentScore)
                <span class="score-indicator {{ $currentScore >= 80 ? 'score-high' : ($currentScore >= 60 ? 'score-medium' : 'score-low') }}">
                    {{ number_format($currentScore, 1) }}%
                </span>
            @else
                <span class="text-muted">—</span>
            @endif
        </div>
        <div class="project-metric-label">
            @if($totalAnalyses > 0)
                Current Score
                <small class="text-muted d-block">({{ $totalAnalyses }} analyse(s))</small>
            @else
                No analysis
            @endif
        </div>
    </div>
    <div class="project-metric-item">
        <div class="project-metric-value">
            @php
                // Score moyen basé sur toutes les analyses du projet
                $averageScore = \App\Models\SeoAnalysis::where('project_id', $project->id)
                    ->get()
                    ->avg(fn($analysis) => $analysis->seo_score);
            @endphp
            {{ $averageScore ? number_format($averageScore, 1) : '0' }}
        </div>
        <div class="project-metric-label">Avg Score</div>
    </div>
</div>

<!-- Barre de Progression SEO -->
<div class="project-progress-section">
    <div class="project-progress-header">
        <span class="project-progress-label">Current SEO Score</span>
        <span class="project-progress-percentage">
            @if($currentScore)
                {{ number_format($currentScore, 1) }}%
            @else
                —
            @endif
        </span>
    </div>
    <div class="project-progress-container">
        <div class="project-progress-bar" style="width: {{ $currentScore ?? 0 }}%">
            <div class="project-progress-fill"></div>
        </div>
    </div>
</div>

                <!-- Mots-clés -->
               <!-- Mots-clés - Version Test -->
@php
    // TEMPORAIRE: Remplacer par des données de test
    $testKeywords = "seo optimization,digital marketing,web traffic";
    $keywordsValue = !empty(trim($project->target_keywords ?? '')) 
        ? $project->target_keywords 
        : $testKeywords;
    
    $keywords = array_filter(
        array_map('trim', explode(',', $keywordsValue)),
        function($k) { return !empty($k); }
    );
@endphp

<div class="project-keywords-section">
    <div class="project-keywords-label">Target Keywords</div>
    <div class="project-keywords-list">
        @if(count($keywords) > 0)
            @foreach($keywords as $keyword)
                <span class="project-keyword-tag">
                    {{ $keyword }}
                    @if(empty(trim($project->target_keywords ?? '')))
                        <small style="font-size: 0.6em; opacity: 0.7;">(test)</small>
                    @endif
                </span>
            @endforeach
        @else
            <span class="project-no-keyword text-muted">No keywords defined</span>
        @endif
    </div>
    
    <!-- Debug info -->
    @if(empty(trim($project->target_keywords ?? '')))
    <div style="background: #fff3cd; color: #856404; padding: 8px; border-radius: 4px; font-size: 11px; margin-top: 8px; border: 1px solid #ffeaa7;">
        <strong>⚠️ DEBUG:</strong> Using test data - No keywords in database for "{{ $project->name }}"
    </div>
    @endif
</div>
            </div>

            <!-- Footer du Projet -->
            <div class="project-item-footer">
                <div class="project-item-meta">
                    <div class="project-meta-item">
                        <i class="bi bi-calendar"></i>
                        <span>Created {{ $project->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="project-meta-item">
                        <i class="bi bi-arrow-clockwise"></i>
                        <span>Updated {{ $project->updated_at->format('M d, Y') }}</span>
                    </div>
                </div>
                <div class="project-item-status">
                    <span class="project-status-badge {{ $project->is_active ? 'project-status-active' : 'project-status-inactive' }}">
                        {{ $project->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="projects-pagination-container">
        {{ $projects->links('vendor.pagination.custom') }}
    </div>

    {{-- Modal de Loading pour l'analyse --}}
<div class="modal fade" id="analysisLoadingModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card border-0 overflow-hidden">
            <div class="modal-body text-center p-5">
                <div class="spinner-border text-primary mb-4" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                
                <h4 class="text-dark fw-bold mb-3">SEO Analysis in Progress</h4>
                <p class="text-gray-300 mb-4">We're analyzing your website's performance. This may take 1-2 minutes.</p>
                
                <div class="progress-steps mb-4">
                    <div class="step active" data-step="1">
                        <span class="step-icon">🔍</span>
                        <span class="step-text">Scanning Website</span>
                    </div>
                    <div class="step" data-step="2">
                        <span class="step-icon">⚡</span>
                        <span class="step-text">Performance Audit</span>
                    </div>
                    <div class="step" data-step="3">
                        <span class="step-icon">📊</span>
                        <span class="step-text">Generating Report</span>
                    </div>
                </div>
                
                <div class="progress-container mb-3">
                    <div class="progress">
                        <div class="progress-bar progress-bar-animated" role="progressbar" style="width: 0%"></div>
                    </div>
                    <small class="text-muted progress-percentage">0%</small>
                </div>
                
                <div class="time-estimate">
                    <small class="text-muted">
                        <i class="fas fa-clock me-1"></i>
                        Estimated time: <span id="timeRemaining">1-2 minutes</span>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Formulaire caché pour lancer l'analyse --}}
<form id="analysisForm" action="{{ route('analysis.run') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="project_id" id="analysisProjectId">
    <input type="hidden" name="base_url" id="analysisBaseUrl">
    <input type="hidden" name="name" id="analysisProjectName">
</form>


</div>

<!-- Chargement de SweetAlert2 depuis CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Passage des variables PHP à JavaScript -->
<script>
    // Transmettre les messages flash à JavaScript
    window.flashMessage = @if(Session::has('message'))
    {
        type: '{{ Session::get("alert-type", "success") }}',
        message: '{{ Session::get("message") }}'
    }
    @else
    null
    @endif;
</script>

<!-- Chargement de notre JS optimisé -->
<script src="{{ asset('js/projects.js?v=' . filemtime(public_path('js/projects.js'))) }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const analysisModal = new bootstrap.Modal(document.getElementById('analysisLoadingModal'));
    const analysisForm = document.getElementById('analysisForm');
    let analysisInterval;

    // Gestion du clic sur "Analytics"
    document.querySelectorAll('.start-analysis-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const projectId = this.dataset.projectId;
            const projectUrl = this.dataset.projectUrl;
            const projectName = this.dataset.projectName;
            
            // Remplir le formulaire
            document.getElementById('analysisProjectId').value = projectId;
            document.getElementById('analysisBaseUrl').value = projectUrl;
            document.getElementById('analysisProjectName').value = projectName;
            
            // Afficher le modal
            analysisModal.show();
            
            // Démarrer l'animation de progression
            startProgressAnimation();
            
            // Soumettre le formulaire après un court délai
            setTimeout(() => {
                analysisForm.submit();
            }, 1500);
        });
    });

    function startProgressAnimation() {
        const progressBar = document.querySelector('.progress-bar');
        const progressPercentage = document.querySelector('.progress-percentage');
        const steps = document.querySelectorAll('.step');
        let progress = 0;
        
        // Réinitialiser
        progressBar.style.width = '0%';
        progressPercentage.textContent = '0%';
        steps.forEach(step => step.classList.remove('active'));
        steps[0].classList.add('active');
        
        clearInterval(analysisInterval);
        
        analysisInterval = setInterval(() => {
            if (progress < 90) { // S'arrête à 90% pour laisser la place au vrai chargement
                progress += Math.random() * 10 + 5; // Progression aléatoire entre 5% et 15%
                progress = Math.min(progress, 90);
                
                progressBar.style.width = progress + '%';
                progressPercentage.textContent = Math.round(progress) + '%';
                
                // Mettre à jour les étapes
                if (progress >= 30 && progress < 60) {
                    steps.forEach(step => step.classList.remove('active'));
                    steps[1].classList.add('active');
                } else if (progress >= 60) {
                    steps.forEach(step => step.classList.remove('active'));
                    steps[2].classList.add('active');
                }
            }
        }, 800);
    }

    // Arrêter l'animation quand le modal se ferme
    document.getElementById('analysisLoadingModal').addEventListener('hidden.bs.modal', function() {
        clearInterval(analysisInterval);
    });
});
</script>


@endsection