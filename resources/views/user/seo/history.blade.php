@extends('admin.admin_master')

@section('admin')

<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="h2 fw-bold text-gradient text-primary mb-2">SEO Generations History</h1>
            <p class="text-muted mb-0">Review and manage your generated SEO content variants</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="badge bg-primary bg-opacity-10 text-primary border-0 px-3 py-2">
                <i class="bi bi-collection me-1"></i>{{ $items->total() }} Generations
            </span>
        </div>
    </div>

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
            <p class="text-muted mb-4">Start creating SEO content to see your history here</p>
            <a href="{{ route('user.projects.seo') }}" class="btn btn-primary btn-lg px-4">
                <i class="bi bi-plus-circle me-2"></i>Create First Generation
            </a>
        </div>
    @else
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
                                            <i class="bi bi-clock-history text-primary fs-5"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-semibold text-dark">{{ $g->created_at->format('F j, Y') }}</h6>
                                            <small class="text-muted">{{ $g->created_at->format('g:i A') }}</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <div class="d-flex align-items-center gap-2 justify-content-md-end">
                                        <span class="badge bg-primary bg-opacity-10 text-primary border-0">
                                            {{ strtoupper($g->lang) }}
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
                                                        <button type="submit" class="dropdown-item text-dark">
                                                            <i class="bi bi-arrow-repeat me-2"></i>Reuse Generation
                                                        </button>
                                                    </form>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('seo.history.destroy', $g->id) }}" method="POST" 
                                                          onsubmit="return confirm('Are you sure you want to delete this generation?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="dropdown-item text-danger delete-generation-btn" 
        data-generation-id="{{ $g->id }}"
        data-generation-date="{{ $g->created_at->format('M j, Y') }}">
    <i class="bi bi-trash me-2"></i>Delete Generation
</button>
                                                    </form>
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
                                <label class="form-label text-muted small text-uppercase fw-semibold mb-2">Prompt</label>
                                <p class="mb-0 text-dark fw-medium">{{ Str::limit($g->prompt, 120) }}</p>
                            </div>

                            <!-- Variants Grid -->
                            <label class="form-label text-muted small text-uppercase fw-semibold mb-3">Generated Variants</label>
                            <div class="row g-3">
                                @foreach($g->variants as $variant)
                                    <div class="col-md-4">
                                        <div class="card variant-card border-0 h-100 shadow-sm">
                                            <div class="card-body p-4">
                                                <!-- Title Section -->
                                                <div class="mb-3">
                                                    <label class="form-label text-muted small text-uppercase fw-semibold mb-2">
                                                        Title <i class="bi bi-clipboard ms-1 text-muted"></i>
                                                    </label>
                                                    <h6 class="copyable-title text-dark fw-semibold mb-0 cursor-pointer"
                                                        data-text="{{ $variant->title }}"
                                                        data-bs-toggle="tooltip" 
                                                        title="Click to copy title">
                                                        {{ $variant->title }}
                                                    </h6>
                                                </div>

                                                <!-- Meta Description -->
                                                <div class="mb-4">
                                                    <label class="form-label text-muted small text-uppercase fw-semibold mb-2">
                                                        Meta Description <i class="bi bi-clipboard ms-1 text-muted"></i>
                                                    </label>
                                                    <p class="copyable-meta text-muted small mb-0 cursor-pointer lh-sm"
                                                       data-text="{{ $variant->meta }}"
                                                       data-bs-toggle="tooltip" 
                                                       title="Click to copy meta description">
                                                       {{ $variant->meta }}
                                                    </p>
                                                </div>

                                                <!-- Action Button -->
                                                <form action="{{ route('seo.variant.choose', $variant->id) }}" method="POST" class="mt-auto">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success w-100 rounded-2 py-2">
                                                        <i class="bi bi-check-lg me-2"></i>Select This Variant
                                                    </button>
                                                </form>
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

<style>
    .bi-clock-history{
        color: #ffffff !important;
    }
    span.bg-primary{
        color: #fff !important;
    }
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
    border-radius: 4px;
    padding: 2px 4px;
    margin: -2px -4px;
}

.copyable-title:hover, .copyable-meta:hover {
    color: #0d6efd !important;
    background-color: rgba(13, 110, 253, 0.05);
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
}

.card-header {
    backdrop-filter: blur(10px);
    background-color:rgb(238, 239, 245) !important;
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
</style>

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
            
            element.innerHTML = '<i class="bi bi-check-lg me-1"></i>Copied!';
            element.className = originalClass + ' text-success fw-semibold';
            
            setTimeout(() => {
                element.innerHTML = originalContent;
                element.className = originalClass;
            }, 1500);
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



     // ✅ SWEETALERT2 pour la suppression
     document.querySelectorAll('.delete-generation-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const generationId = this.getAttribute('data-generation-id');
            const generationDate = this.getAttribute('data-generation-date');
            
            Swal.fire({
                title: 'Delete Generation?',
                html: `Are you sure you want to delete the generation from <strong>${generationDate}</strong>?<br><br>This action cannot be undone.`,
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
                    // Créer un formulaire dynamique pour la suppression
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
                    
                    // Afficher un loader pendant la suppression
                    Swal.fire({
                        title: 'Deleting...',
                        text: 'Please wait while we delete the generation',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Soumettre le formulaire
                    form.submit();
                }
            });
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