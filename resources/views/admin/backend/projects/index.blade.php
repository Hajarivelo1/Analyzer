@extends('admin.admin_master')
@section('admin')

<div class="container-fluid py-4 flex-grow-1">
    <!-- Header avec stats -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="glass-card">
                <div class="glass-card-header p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-1">Projects Management</h2>
                            <p class="mb-0">Manage all your SEO analysis projects</p>
                        </div>
                        <a href="{{route('add.projects')}}" class="btn btn-primary text-decoration-none">
                            <i class="bi bi-plus-circle me-2"></i>Add New Project
                        </a>
                    </div>
                </div>
                
                <!-- Stats en ligne -->
                <div class="row mt-4 px-4 pb-4">
                    <div class="col-md-3 mb-3">
                        <div class="glass-stat-card text-center p-3">
                            <div class="stat-number fs-2 fw-bold text-primary">{{ $totalProjects }}</div>
                            <div class="stat-label">Total Projects</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="glass-stat-card text-center p-3">
                            <div class="stat-number fs-2 fw-bold text-success">{{ $activeProjects }}</div>
                            <div class="stat-label">Active Projects</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="glass-stat-card text-center p-3">
                            <div class="stat-number fs-2 fw-bold text-warning">{{ $totalAnalyses }}</div>
                            <div class="stat-label">Total Analyses</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="glass-stat-card text-center p-3">
                            <div class="stat-number fs-2 fw-bold text-info">{{ number_format($avgScore, 1) }}%</div>
                            <div class="stat-label">Avg SEO Score</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des projets -->
    <div class="row">
        @foreach($projects as $project)
        <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
            <div class="glass-card h-100 p-1">
                <!-- En-tête du projet -->
                <div class="glass-card-header d-flex justify-content-between align-items-center">
                    <div class="glass-icon-bg rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                        <i class="bi bi-folder-fill text-white"></i>
                    </div>
                    <div class="dropdown">
                        <button class="glass-btn btn" data-bs-toggle="dropdown" aria-label="Project options">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="glass-dropdown dropdown-menu">
                            <li><a class="dropdown-item" href="{{route('project.show', $project->id)}}"><i class="bi bi-eye me-2"></i>View</a></li>
                            <li><a class="dropdown-item" href=""><i class="bi bi-graph-up me-2"></i>Analytics</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('projects.destroy', $project->id) }}" method="POST" class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button class="dropdown-item text-danger" type="submit">
                                        <i class="bi bi-trash me-2"></i>Delete
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Contenu du projet -->
                <div class="card-body" style="background-color:#d0cae7 !important; padding:10px;">
                    <h4 class="project-title mb-2">{{ $project->name }}</h4>
                    <p class="project-url text-muted mb-3">{{ $project->base_url }}</p>
                    
                    <div class="project-stats d-flex justify-content-between mb-3">
                        <div class="stat-item text-center">
                            <span class="stat-value d-block fw-bold fs-5">{{ $project->total_analyses ?? 0 }}</span>
                            <span class="stat-label text-muted small">Analyses</span>
                        </div>
                        <div class="stat-item text-center">
                            <span class="stat-value d-block fw-bold fs-5">
                                {{ $project->total_analyses > 0 ? number_format($project->average_score, 1) . '%' : '—' }}
                            </span>
                            <span class="stat-label text-muted small">Current Score</span>
                        </div>
                        <div class="stat-item text-center">
                            <span class="stat-value d-block fw-bold fs-5">
                                {{ $project->seoAnalyses->isNotEmpty() ? $project->seoAnalyses->sum('score') : 0 }}
                            </span>
                            <span class="stat-label text-muted small">Total Score</span>
                        </div>
                    </div>

                    <!-- Barre de progression SEO -->
                    <div class="progress-container mb-3">
                        <div class="progress-label text-muted small mb-1">SEO Score</div>
                        <div class="glass-progress progress">
                            <div class="glass-progress-bar progress-bar" style="width: {{ $project->average_score }}%"></div>
                        </div>
                        <div class="glass-progress-bar progress-bar" style="width: {{ $project->average_score }}%"></div>

                    </div>

                    <!-- Tags -->
                    <div class="project-tags mb-3">
                        @if($project->target_keywords)
                            @foreach(explode(',', $project->target_keywords) as $keyword)
                                <span class="badge glass-badge-success me-1 mb-1">{{ trim($keyword) }}</span>
                            @endforeach
                        @endif
                    </div>
                </div>

                <!-- Footer du projet -->
                <div class="glass-card-footer card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="project-meta">
                            <small class="text-muted me-3">
                                <i class="bi bi-calendar me-1"></i>{{ $project->created_at->format('d/m/Y') }}
                            </small>
                            <small class="text-muted">
                                <i class="bi bi-arrow-clockwise me-1"></i>{{ $project->updated_at->format('d/m/Y') }}
                            </small>
                        </div>
                        <div class="project-status">
                            <span class="badge {{ $project->is_active ? 'glass-badge-success' : 'glass-badge-warning' }}">
                                {{ $project->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    {{ $projects->links('vendor.pagination.custom') }}



</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Delete this project?',
                text: "This action is irreversible.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@if(Session::has('message'))
<script>
Swal.fire({
    icon: '{{ Session::get('alert-type', 'success') }}',
    title: '{{ Session::get('message') }}',
    showConfirmButton: false,
    timer: 2000
});
</script>
@endif


@endsection
