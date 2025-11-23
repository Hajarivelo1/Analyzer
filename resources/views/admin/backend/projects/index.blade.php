@extends('admin.admin_master')
@section('admin')



<link rel="stylesheet" href="{{ asset('css/projects.css?v=' . filemtime(public_path('css/projects.css'))) }}">



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
                                <a class="dropdown-item" href="#">
                                    <i class="bi bi-graph-up"></i>
                                    Analytics
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
                <div class="project-item-metrics">
                    <div class="project-metric-item">
                        <div class="project-metric-value">{{ $project->total_analyses ?? 0 }}</div>
                        <div class="project-metric-label">Analyses</div>
                    </div>
                    <div class="project-metric-item">
                        <div class="project-metric-value">
                            {{ $project->total_analyses > 0 ? number_format($project->average_score, 1) . '%' : '—' }}
                        </div>
                        <div class="project-metric-label">Current Score</div>
                    </div>
                    <div class="project-metric-item">
                        <div class="project-metric-value">
                            {{ $project->seoAnalyses->isNotEmpty() ? $project->seoAnalyses->sum('score') : 0 }}
                        </div>
                        <div class="project-metric-label">Total Score</div>
                    </div>
                </div>

                <!-- Barre de Progression SEO -->
                <div class="project-progress-section">
                    <div class="project-progress-header">
                        <span class="project-progress-label">SEO Score Progress</span>
                        <span class="project-progress-percentage">{{ number_format($project->average_score, 1) }}%</span>
                    </div>
                    <div class="project-progress-container">
                        <div class="project-progress-bar" style="width: {{ $project->average_score }}%">
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




@endsection