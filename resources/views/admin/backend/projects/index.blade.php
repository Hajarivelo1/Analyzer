@extends('admin.admin_master')
@section('admin')


<style>
/* Reset et styles de base */
.projects-header-main,
.projects-stats-grid,
.projects-list-grid,
.project-item-card {
    box-sizing: border-box;
}

/* Header Principal */
.projects-header-main {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    color: white;
    padding: 2.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    position: relative;
    overflow: hidden;
}

.projects-header-main::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 200px;
    height: 200px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
}

.projects-header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    position: relative;
    z-index: 2;
}

.projects-header-text {
    flex: 1;
}

.projects-header-title {
    font-weight: 700;
    font-size: 2.25rem;
    margin: 0 0 0.5rem 0;
    line-height: 1.2;
}

.projects-header-subtitle {
    opacity: 0.9;
    font-size: 1.1rem;
    margin: 0;
    line-height: 1.4;
}

.projects-add-btn {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    padding: 1rem 2rem;
    border-radius: 15px;
    text-decoration: none;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    transition: all 0.3s ease;
    white-space: nowrap;
}

.projects-add-btn:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: translateY(-2px);
    color: white;
}

/* Grille de Statistiques */
.projects-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    position: relative;
    z-index: 2;
}

.projects-stat-card {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border-radius: 15px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
}

.projects-stat-card:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
}

.projects-stat-icon {
    font-size: 2rem;
    opacity: 0.9;
    flex-shrink: 0;
}

.projects-stat-content {
    flex: 1;
}

.projects-stat-value {
    font-size: 2rem;
    font-weight: 800;
    margin-bottom: 0.25rem;
    line-height: 1;
}

.projects-stat-label {
    font-size: 0.9rem;
    opacity: 0.9;
    line-height: 1.2;
}

/* Grille des Projets */
.projects-list-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.project-item-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
    border: 1px solid #e2e8f0;
    overflow: hidden;
    transition: all 0.3s ease;
    opacity: 0;
    transform: translateY(20px);
    display: flex;
    flex-direction: column;
    height: fit-content;
}

.project-item-card.project-card-animate-in {
    opacity: 1;
    transform: translateY(0);
    animation: projectsFadeInUp 0.6s ease-out forwards;
}

.project-item-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
}

/* En-tête du Projet */
.project-item-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem 1.5rem 0;
    flex-shrink: 0;
}

.project-item-icon {
    font-size: 2rem;
    color: #667eea;
}

.project-item-actions {
    position: relative;
}

.project-action-btn {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 0.5rem;
    color: #6b7280;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
}

.project-action-btn:hover {
    background: #f1f5f9;
    color: #374151;
}

/* Contenu du Projet */
.project-item-content {
    padding: 1.5rem;
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.project-item-name {
    font-weight: 700;
    color: #1f2937;
    margin: 0;
    font-size: 1.25rem;
    line-height: 1.3;
}

.project-item-url {
    color: #6b7280;
    margin: 0;
    word-break: break-all;
    line-height: 1.4;
}

/* Métriques du Projet */
.project-item-metrics {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
}

.project-metric-item {
    text-align: center;
    padding: 1rem;
    background: #f8fafc;
    border-radius: 12px;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.project-metric-item:hover {
    background: #f1f5f9;
    transform: translateY(-2px);
}

.project-metric-value {
    font-size: 1.5rem;
    font-weight: 800;
    color: #1f2937;
    margin-bottom: 0.25rem;
    line-height: 1;
}

.project-metric-label {
    font-size: 0.8rem;
    color: #6b7280;
    font-weight: 600;
    line-height: 1.2;
}

/* Barre de Progression */
.project-progress-section {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.project-progress-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.project-progress-label {
    font-size: 0.9rem;
    color: #6b7280;
    font-weight: 600;
}

.project-progress-percentage {
    font-size: 0.9rem;
    color: #667eea;
    font-weight: 700;
}

.project-progress-container {
    height: 8px;
    background: #f1f5f9;
    border-radius: 10px;
    overflow: hidden;
}

.project-progress-bar {
    height: 100%;
    background: linear-gradient(90deg, #667eea, #764ba2);
    border-radius: 10px;
    position: relative;
    transition: width 1s ease-in-out;
}

.project-progress-fill {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, #667eea, #764ba2);
    border-radius: 10px;
}

/* Mots-clés */
.project-keywords-section {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.project-keywords-label {
    font-size: 0.9rem;
    color: #6b7280;
    font-weight: 600;
}

.project-keywords-list {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.project-keyword-tag {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    line-height: 1;
}

/* Footer du Projet */
.project-item-footer {
    padding: 1.5rem;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-shrink: 0;
}

.project-item-meta {
    display: flex;
    gap: 1rem;
}

.project-meta-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.85rem;
    color: #6b7280;
    line-height: 1;
}

.project-meta-item i {
    font-size: 0.8rem;
}

.project-item-status {
    flex-shrink: 0;
}

.project-status-badge {
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 700;
    line-height: 1;
}

.project-status-active {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
}

.project-status-inactive {
    background: #fef3c7;
    color: #92400e;
}

/* Pagination */
.projects-pagination-container {
    display: flex;
    justify-content: center;
    margin-top: 2rem;
}

/* Animations */
@keyframes projectsFadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive Design */
@media (max-width: 1200px) {
    .projects-list-grid {
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    }
}

@media (max-width: 768px) {
    .projects-header-content {
        flex-direction: column;
        gap: 1.5rem;
        text-align: center;
    }
    
    .projects-stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .projects-list-grid {
        grid-template-columns: 1fr;
    }
    
    .project-item-metrics {
        grid-template-columns: 1fr;
    }
    
    .project-item-footer {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
    
    .project-item-meta {
        flex-direction: column;
        gap: 0.5rem;
    }
}

@media (max-width: 480px) {
    .projects-header-main {
        padding: 2rem 1.5rem;
    }
    
    .projects-stats-grid {
        grid-template-columns: 1fr;
    }
    
    .projects-header-title {
        font-size: 1.75rem;
    }
    
    .project-item-content {
        padding: 1rem;
    }
    
    .project-item-header {
        padding: 1rem 1rem 0;
    }
    
    .project-item-footer {
        padding: 1rem;
    }
}
.dropdown-item{
    color: #667eea;
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Delete Project?',
                text: "This action cannot be undone. All project data will be permanently deleted.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, Delete',
                cancelButtonText: 'Cancel',
                background: '#ffffff',
                backdrop: 'rgba(0, 0, 0, 0.1)'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // Animation d'apparition des cartes
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.project-item-card');
        cards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.1}s`;
            card.classList.add('project-card-animate-in');
        });
    });
</script>

@if(Session::has('message'))
<script>
Swal.fire({
    icon: '{{ Session::get('alert-type', 'success') }}',
    title: '{{ Session::get('message') }}',
    showConfirmButton: false,
    timer: 2000,
    background: '#ffffff',
    backdrop: 'rgba(0, 0, 0, 0.1)'
});
</script>
@endif



@endsection