@extends('admin.admin_master')

@section('admin')

<div class="container-fluid py-4">
    <!-- Main Glass Card -->
    <div class="card glass-card border-0 rounded-4 overflow-hidden">
        <!-- Card Header -->
        <div class="card-header glass-header border-0 py-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 fw-bold text-gradient text-primary mb-2">SEO Generations History</h1>
                    <p class="text-muted mb-0">Review and manage your generated SEO content across projects</p>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <!-- Project Filter -->
                    @if($projects->count() > 0)
                    <div class="dropdown">
                        <button class="btn btn-outline-primary dropdown-toggle d-flex align-items-center gap-2" 
                                type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-folder2"></i>
                            <span id="filter-label">
                                @if(request('project_id'))
                                    {{ $projects->where('id', request('project_id'))->first()->name ?? 'All Projects' }}
                                @else
                                    All Projects
                                @endif
                            </span>
                        </button>
                        <ul class="dropdown-menu shadow-lg border-0 rounded-3">
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 {{ !request('project_id') ? 'active' : '' }} text-dark" 
                                   href="{{ request()->fullUrlWithQuery(['project_id' => null]) }}">
                                    <i class="bi bi-grid-1x2 text-dark"></i>
                                    All Projects
                                    <small class="text-dark text-muted ms-auto">{{ $items->total() }}</small>
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            @foreach($projects as $project)
                            <li>
                                <a class=" text-dark dropdown-item d-flex align-items-center gap-2 {{ request('project_id') == $project->id ? 'active' : '' }}" 
                                   href="{{ request()->fullUrlWithQuery(['project_id' => $project->id]) }}">
                                    <i class="bi bi-folder text-dark"></i>
                                    {{ $project->name }}
                                    <small class="text-muted ms-auto text-dark">{{ $project->seo_generations_count ?? 0 }}</small>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    
                    <span class="badge bg-primary bg-opacity-10 text-white border-0 px-3 py-2">
    <i class="bi bi-collection me-1 text-white"></i>{{ $items->total() }} Generations
</span>
                </div>
            </div>
        </div>

        <!-- Card Body -->
        <div class="card-body glass-body p-4">
            <!-- Active Filters -->
            @if(request('project_id'))
            <div class="alert alert-info alert-dismissible fade show d-flex align-items-center border-0 shadow-sm mb-4" role="alert">
                <i class="bi bi-funnel me-2 fs-5"></i>
                <span class="fw-medium">
                    Showing generations for: 
                    <strong>{{ $projects->where('id', request('project_id'))->first()->name ?? 'Unknown Project' }}</strong>
                </span>
                <a href="{{ request()->fullUrlWithQuery(['project_id' => null]) }}" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></a>
            </div>
            @endif

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center border-0 shadow-sm mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                    <span class="fw-medium">{{ session('success') }}</span>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center border-0 shadow-sm mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                    <span class="fw-medium">{{ session('error') }}</span>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($items->isEmpty())
                <!-- Empty State -->
                <div class="text-center py-6">
                    <div class="mb-4">
                        <i class="bi bi-file-text display-1 text-muted opacity-50"></i>
                    </div>
                    <h4 class="text-muted mb-3">No generations yet</h4>
                    <p class="text-muted mb-4">
                        @if(request('project_id'))
                            No SEO generations found for this project. Create your first one!
                        @else
                            Start creating SEO content to see your history here
                        @endif
                    </p>
                    <a href="{{ route('user.projects.seo') }}" class="btn btn-primary btn-lg px-4">
                        <i class="bi bi-plus-circle me-2"></i>Create First Generation
                    </a>
                </div>
            @else
                <!-- Statistics Bar -->
                <!-- Statistics Bar -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 bg-primary bg-opacity-10 rounded-3">
            <div class="card-body py-3">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-25 rounded-2 p-2 me-3">
                        <i class="bi bi-collection text-white fs-5"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-white fw-bold">{{ $items->total() }}</h6>
                        <small class="text-white">Total Generations</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 bg-success bg-opacity-10 rounded-3">
            <div class="card-body py-3">
                <div class="d-flex align-items-center">
                    <div class="bg-success bg-opacity-25 rounded-2 p-2 me-3">
                        <i class="bi bi-folder text-white fs-5"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-white fw-bold">{{ $projects->count() }}</h6>
                        <small class="text-white">Projects</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 bg-info bg-opacity-10 rounded-3">
            <div class="card-body py-3">
                <div class="d-flex align-items-center">
                    <div class="bg-info bg-opacity-25 rounded-2 p-2 me-3">
                        <i class="bi bi-layers text-white fs-5"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-white fw-bold">{{ $items->sum(fn($item) => $item->variants->count()) }}</h6>
                        <small class="text-white">Total Variants</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 bg-warning bg-opacity-10 rounded-3">
            <div class="card-body py-3">
                <div class="d-flex align-items-center">
                    <div class="bg-warning bg-opacity-25 rounded-2 p-2 me-3">
                        <i class="bi bi-clock text-white fs-5"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-white fw-bold">{{ $items->first()->created_at->diffForHumans() }}</h6>
                        <small class="text-white">Last Activity</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

                <!-- Generations Timeline -->
                <div class="row g-4">
                    @foreach($items as $g)
                        <div class="col-12">
                            <!-- Generation Card -->
                            <div class="card border-0 shadow-sm rounded-3 overflow-hidden generation-card">
                                <div class="card-header bg-light bg-opacity-50 border-0 py-3">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center gap-3">
                                            <div class="bg-primary bg-opacity-10 rounded-2 p-2">
    <i class="bi bi-clock-history text-white fs-5"></i>
</div>
                                                <div>
                                                    <h6 class="mb-0 fw-semibold text-dark">{{ $g->created_at->format('F j, Y') }}</h6>
                                                    <small class="text-muted">{{ $g->created_at->format('g:i A') }}</small>
                                                    <!-- Project Badge -->
                                                    @if($g->project)
                                                    <div class="mt-1">
                                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border-0">
                                                            <i class="bi bi-folder me-1"></i>
                                                            {{ $g->project->name }}
                                                        </span>
                                                        <small class="text-muted ms-2">
                                                            <i class="bi bi-link-45deg me-1"></i>
                                                            {{ $g->project->base_url }}
                                                        </small>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 text-md-end">
                                            <div class="d-flex align-items-center gap-2 justify-content-md-end">
                                            <span class="badge bg-primary bg-opacity-2 text-white border-0">
    <i class="bi bi-translate me-1 text-white"></i>
    {{ strtoupper($g->lang) }}
</span>
<span class="badge bg-success bg-opacity-10 text-white border-0">
    <i class="bi bi-layers me-1 text-white"></i>
    {{ $g->variants->count() }} variants
</span>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary border-0 rounded-circle" 
                                                            type="button" data-bs-toggle="dropdown">
                                                        <i class="bi bi-three-dots"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end shadow">
                                                        <li>
                                                            <form action="{{ route('seo.history.reuse', $g->id) }}" method="POST" class="d-inline w-100">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item text-dark d-flex align-items-center gap-2">
                                                                    <i class="bi bi-arrow-repeat"></i>
                                                                    Reuse Generation
                                                                </button>
                                                            </form>
                                                        </li>
                                                        <li>
                                                            @if($g->project)
                                                            <a class="dropdown-item text-dark d-flex align-items-center gap-2" 
                                                               href="{{ route('project.show', $g->project->id) }}">
                                                                <i class="bi bi-folder"></i>
                                                                View Project
                                                            </a>
                                                            @endif
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <button type="button" class="dropdown-item text-danger d-flex align-items-center gap-2 delete-generation-btn" 
                                                                    data-generation-id="{{ $g->id }}"
                                                                    data-generation-date="{{ $g->created_at->format('M j, Y') }}"
                                                                    data-project-name="{{ $g->project->name ?? 'Unknown Project' }}">
                                                                <i class="bi bi-trash"></i>
                                                                Delete Generation
                                                            </button>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body p-4">
                                    <!-- Prompt Preview -->
                                    <div class="mb-4">
                                        <label class="form-label text-muted small text-uppercase fw-semibold mb-2">
                                            <i class="bi bi-chat-left-text me-1"></i>Prompt
                                        </label>
                                        <p class="mb-0 text-dark fw-medium">{{ Str::limit($g->original_prompt ?? $g->prompt, 120) }}</p>
                                        @if($g->original_prompt && $g->original_prompt != $g->prompt)
                                        <small class="text-muted">Contextual prompt used for generation</small>
                                        @endif
                                    </div>

                                    <!-- Selected Variant (if any) -->
                                    @php
                                        $selectedVariant = $g->variants->where('is_selected', true)->first();
                                    @endphp
                                    @if($selectedVariant)
                                    <div class="alert alert-success border-0 rounded-3 mb-4">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <i class="bi bi-check-circle-fill text-success"></i>
                                            <strong>Selected Variant</strong>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <strong class="text-dark d-block mb-1">Title:</strong>
                                                <span class="text-dark">{{ $selectedVariant->title }}</span>
                                                <small class="text-muted d-block mt-1">{{ strlen($selectedVariant->title) }} characters</small>
                                            </div>
                                            <div class="col-md-6">
                                                <strong class="text-dark d-block mb-1">Meta Description:</strong>
                                                <span class="text-dark">{{ $selectedVariant->meta }}</span>
                                                <small class="text-muted d-block mt-1">{{ strlen($selectedVariant->meta) }} characters</small>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    <!-- Variants Grid -->
                                    <label class="form-label text-muted small text-uppercase fw-semibold mb-3">
                                        <i class="bi bi-layers me-1"></i>Generated Variants
                                    </label>
                                    <div class="row g-3">
                                        @foreach($g->variants as $variant)
                                            <div class="col-md-4">
                                                <div class="card variant-card border-0 h-100 shadow-sm {{ $variant->is_selected ? 'border-success border-2' : '' }}">
                                                    @if($variant->is_selected)
                                                    <div class="card-header bg-success bg-opacity-10 border-0 py-2">
                                                        <small class="text-success fw-semibold">
                                                            <i class="bi bi-check-circle-fill me-1"></i>Selected
                                                        </small>
                                                    </div>
                                                    @endif
                                                    <div class="card-body p-4">
                                                        <!-- Title Section -->
                                                        <div class="mb-3">
                                                            <label class="form-label text-muted small text-uppercase fw-semibold mb-2 d-flex justify-content-between align-items-center">
                                                                <span>Title</span>
                                                                <i class="bi bi-clipboard text-muted cursor-pointer copy-icon" 
                                                                   data-text="{{ $variant->title }}"
                                                                   data-bs-toggle="tooltip" 
                                                                   title="Copy title"></i>
                                                            </label>
                                                            <h6 class="copyable-title text-dark fw-semibold mb-0 cursor-pointer"
                                                                data-text="{{ $variant->title }}"
                                                                data-bs-toggle="tooltip" 
                                                                title="Click to copy title">
                                                                {{ $variant->title }}
                                                            </h6>
                                                            <small class="text-muted">{{ strlen($variant->title) }} chars</small>
                                                        </div>

                                                        <!-- Meta Description -->
                                                        <div class="mb-4">
                                                            <label class="form-label text-muted small text-uppercase fw-semibold mb-2 d-flex justify-content-between align-items-center">
                                                                <span>Meta Description</span>
                                                                <i class="bi bi-clipboard text-muted cursor-pointer copy-icon" 
                                                                   data-text="{{ $variant->meta }}"
                                                                   data-bs-toggle="tooltip" 
                                                                   title="Copy meta description"></i>
                                                            </label>
                                                            <p class="copyable-meta text-muted small mb-0 cursor-pointer lh-sm"
                                                               data-text="{{ $variant->meta }}"
                                                               data-bs-toggle="tooltip" 
                                                               title="Click to copy meta description">
                                                               {{ $variant->meta }}
                                                            </p>
                                                            <small class="text-muted">{{ strlen($variant->meta) }} chars</small>
                                                        </div>

                                                        <!-- Action Button -->
                                                        @if(!$variant->is_selected)
                                                        <form action="{{ route('seo.variant.choose', $variant->id) }}" method="POST" class="mt-auto">
                                                            @csrf
                                                            <button type="submit" class="btn btn-outline-success w-100 rounded-2 py-2">
                                                                <i class="bi bi-check-lg me-2"></i>Select This Variant
                                                            </button>
                                                        </form>
                                                        @else
                                                        <button class="btn btn-success w-100 rounded-2 py-2" disabled>
                                                            <i class="bi bi-check-lg me-2"></i>Currently Selected
                                                        </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($items->hasPages())
                    <div class="d-flex justify-content-center mt-5">
                        <nav aria-label="Page navigation">
                            {{ $items->links('vendor.pagination.bootstrap-5') }}
                        </nav>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

<style>

.bg-info.bg-opacity-10 {
    background-color: rgb(23 187 218 / 38%) !important;
}
    .tw{
        color: white !important;
    }
    .td{
        color: black!important;
    }

    /* Glassmorphism Styles seulement pour la structure */
    .glass-card {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }

    .glass-header {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(15px);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .glass-body {
        background: rgba(255, 255, 255, 0.05);
    }

    /* Rest of your existing CSS remains the same */
    .text-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .generation-card {
        transition: all 0.3s ease;
        border: 1px solid rgba(0,0,0,0.05);
    }

    .generation-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
    }

    .variant-card {
        transition: all 0.3s ease;
        border: 1px solid rgba(0,0,0,0.03);
    }

    .variant-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.08) !important;
        border-color: rgba(13, 110, 253, 0.2);
    }

    .cursor-pointer {
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .copyable-title, .copyable-meta {
        border-radius: 4px;
        padding: 4px 8px;
        margin: -4px -8px;
        transition: all 0.2s ease;
    }

    .copyable-title:hover, .copyable-meta:hover,
    .copy-icon:hover {
        color: #0d6efd !important;
        background-color: rgba(13, 110, 253, 0.05);
    }

    .copy-icon {
        padding: 4px;
        border-radius: 4px;
        transition: all 0.2s ease;
    }

    .btn-success {
        background: linear-gradient(135deg, #198754, #157347);
        border: none;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-success:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(25, 135, 84, 0.3);
    }

    .badge {
        font-size: 0.75em;
        font-weight: 500;
    }

    .dropdown-menu {
        border: none;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        border-radius: 12px !important;
    }

    .card-header {
        backdrop-filter: blur(10px);
        background-color: rgb(238, 239, 245) !important;
    }

    /* Project filter styles */
    .dropdown-item.active {
        background: linear-gradient(135deg, #0d6efd, #6f42c1);
        color: white;
    }

    /* Smooth animations */
    .generation-card, .variant-card {
        opacity: 0;
        animation: fadeInUp 0.6s ease forwards;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Stagger animation for variants */
    .variant-card:nth-child(1) { animation-delay: 0.1s; }
    .variant-card:nth-child(2) { animation-delay: 0.2s; }
    .variant-card:nth-child(3) { animation-delay: 0.3s; }

    /* Selected variant highlight */
    .variant-card.border-success {
        border: 2px solid #198754 !important;
        background: linear-gradient(135deg, rgba(25, 135, 84, 0.02), rgba(25, 135, 84, 0.05));
    }

    /* Statistics cards */
    .bg-primary.bg-opacity-10 { background-color: rgba(13, 110, 253, 0.1) !important; }
    .bg-success.bg-opacity-10 { background-color: rgba(25, 135, 84, 0.1) !important; }
    .bg-info.bg-opacity-10 { background-color: rgba(13, 202, 240, 0.1) !important; }
    .bg-warning.bg-opacity-10 { background-color: rgba(255, 193, 7, 0.1) !important; }
</style>

<!-- Keep your existing JavaScript exactly the same -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Initialize Bootstrap tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Enhanced copy functionality
    function copyToClipboard(text, element) {
        navigator.clipboard.writeText(text).then(() => {
            const originalContent = element.innerHTML;
            const originalClass = element.className;
            
            // Show success state
            if (element.classList.contains('copy-icon')) {
                element.className = 'bi bi-check-lg text-success cursor-pointer';
                setTimeout(() => {
                    element.className = 'bi bi-clipboard text-muted cursor-pointer copy-icon';
                }, 1500);
            } else {
                element.innerHTML = '<i class="bi bi-check-lg me-1"></i>Copied!';
                element.className = originalClass + ' text-success fw-semibold';
                
                setTimeout(() => {
                    element.innerHTML = originalContent;
                    element.className = originalClass;
                }, 1500);
            }
        }).catch(err => {
            console.error('Failed to copy: ', err);
        });
    }

    // Title copy functionality
    document.querySelectorAll('.copyable-title').forEach(el => {
        el.addEventListener('click', () => {
            const text = el.getAttribute('data-text');
            copyToClipboard(text, el);
        });
    });

    // Meta copy functionality
    document.querySelectorAll('.copyable-meta').forEach(el => {
        el.addEventListener('click', () => {
            const text = el.getAttribute('data-text');
            copyToClipboard(text, el);
        });
    });

    // Copy icon functionality
    document.querySelectorAll('.copy-icon').forEach(el => {
        el.addEventListener('click', (e) => {
            e.stopPropagation();
            const text = el.getAttribute('data-text');
            copyToClipboard(text, el);
        });
    });

    // SweetAlert2 for deletion
    document.querySelectorAll('.delete-generation-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const generationId = this.getAttribute('data-generation-id');
            const generationDate = this.getAttribute('data-generation-date');
            const projectName = this.getAttribute('data-project-name');
            
            Swal.fire({
                title: 'Delete Generation?',
                html: `Are you sure you want to delete the generation from <strong>${generationDate}</strong> for project <strong>${projectName}</strong>?<br><br>This action cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Delete It',
                cancelButtonText: 'Cancel',
                backdrop: true,
                background: '#ffffff',
                showClass: {
                    popup: 'animate__animated animate__fadeInDown'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                },
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create dynamic form for deletion
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/seo/history/${generationId}`;
                    
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    
                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'DELETE';
                    
                    form.appendChild(csrfToken);
                    form.appendChild(methodField);
                    document.body.appendChild(form);
                    
                    // Show loader during deletion
                    Swal.fire({
                        title: 'Deleting...',
                        text: 'Please wait while we delete the generation',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Submit the form
                    form.submit();
                }
            });
        });
    });

    // Update filter label
    const filterLabel = document.getElementById('filter-label');
    const dropdownItems = document.querySelectorAll('.dropdown-item');
    
    dropdownItems.forEach(item => {
        item.addEventListener('click', function() {
            filterLabel.textContent = this.textContent.trim();
        });
    });

    // Add hover effects
    const cards = document.querySelectorAll('.generation-card, .variant-card');
    cards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
    });
});
</script>

@endsection