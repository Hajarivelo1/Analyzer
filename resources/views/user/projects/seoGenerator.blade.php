@extends('admin.admin_master')

@section('admin')

<div class="min-vh-100 bg-gradient-light py-5">
    <!-- Header Section -->
    <div class="text-center mb-5">
        <div class="d-inline-flex align-items-center justify-content-center rounded-4 bg-gradient-primary text-white mb-4 shadow-lg" 
             style="width: 90px; height: 90px;">
            <i class="bi bi-magic fs-2"></i>
        </div>
        <h1 class="display-5 fw-bold text-dark mb-3">
            SEO Content Generator
        </h1>
        <p class="lead text-muted mx-auto" style="max-width: 600px;">
            Create AI-optimized titles and meta descriptions for your projects with precision
        </p>
    </div>

    <!-- Main Content -->
    <div class="container" style="max-width: 1200px;">
        <!-- Form Card -->
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-4">
            <div class="card-header bg-white py-4 border-0">
                <div class="d-flex align-items-center">
                    <i class="bi bi-pencil-square text-primary fs-4 me-3"></i>
                    <div>
                        <h5 class="fw-bold mb-1">Content Generation</h5>
                        <p class="text-muted mb-0">Fill in the details below to generate SEO content</p>
                    </div>
                </div>
            </div>
            
            <div class="card-body p-4 p-md-5">
                <form id="seo-form" method="POST" action="{{ route('user.projects.seo.generate') }}">
                    @csrf
                    
                    <!-- Project Selection -->
                    <div class="mb-4">
                        <label for="project_id" class="form-label fw-semibold text-dark mb-3">
                            <i class="bi bi-folder me-2 text-primary"></i>
                            Select Project
                        </label>
                        <select 
                            class="form-select form-select-lg border-2 rounded-3 py-3"
                            id="project_id" 
                            name="project_id"
                            style="border-color: #e2e8f0;"
                            required
                        >
                            <option value="">Choose a project...</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" 
                                    {{ (old('project_id', $prefillProjectId) == $project->id) ? 'selected' : '' }}>
                                    {{ $project->name }} - {{ $project->base_url }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text text-muted mt-2">
                            <i class="bi bi-info-circle me-1"></i>
                            The generated content will be linked to the selected project
                        </div>
                    </div>

                    <!-- Prompt Input -->
                    <div class="mb-4">
                        <label for="prompt" class="form-label fw-semibold text-dark mb-3">
                            <i class="bi bi-chat-left-text me-2 text-primary"></i>
                            SEO Instructions
                        </label>
                        <textarea 
                            class="form-control form-control-lg border-2 rounded-3"
                            id="prompt" 
                            name="prompt" 
                            rows="4"
                            placeholder="Example: Generate an SEO title and meta description for a travel blog about Madagascar focusing on adventure activities..."
                            style="border-color: #e2e8f0; resize: none; font-size: 1rem;"
                            required
                        >{{ old('prompt', $prefillPrompt) }}</textarea>
                        <div class="form-text text-muted mt-2">
                            <i class="bi bi-lightbulb me-1"></i>
                            Be specific about your target audience and keywords for better results
                        </div>
                    </div>

                    <!-- Language & Settings Row -->
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label for="lang" class="form-label fw-semibold text-dark mb-3">
                                <i class="bi bi-translate me-2 text-primary"></i>
                                Language
                            </label>
                            <select 
                                class="form-select form-select-lg border-2 rounded-3 py-3"
                                id="lang" 
                                name="lang"
                                style="border-color: #e2e8f0;"
                            >
                                <option value="en" {{ ($prefillLang ?? 'en') === 'en' ? 'selected' : '' }}>
                                    🇺🇸 English
                                </option>
                                <option value="fr" {{ ($prefillLang ?? 'en') === 'fr' ? 'selected' : '' }}>
                                    🇫🇷 French
                                </option>
                                <option value="de" {{ ($prefillLang ?? 'en') === 'de' ? 'selected' : '' }}>
                                    🇩🇪 German
                                </option>
                                <option value="es" {{ ($prefillLang ?? 'en') === 'es' ? 'selected' : '' }}>
                                    🇪🇸 Spanish
                                </option>
                                <option value="it" {{ ($prefillLang ?? 'en') === 'it' ? 'selected' : '' }}>
                                    🇮🇹 Italian
                                </option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark mb-3">
                                <i class="bi bi-gear me-2 text-primary"></i>
                                Output Settings
                            </label>
                            <div class="bg-light rounded-3 p-3 h-100">
                                <div class="d-flex justify-content-between text-sm">
                                    <span class="text-muted">Variants:</span>
                                    <span class="fw-semibold text-primary">3 Options</span>
                                </div>
                                <div class="d-flex justify-content-between text-sm mt-2">
                                    <span class="text-muted">Title Length:</span>
                                    <span class="fw-semibold text-success">50-70 chars</span>
                                </div>
                                <div class="d-flex justify-content-between text-sm mt-2">
                                    <span class="text-muted">Meta Length:</span>
                                    <span class="fw-semibold text-success">120-160 chars</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="text-center mt-5 pt-3">
                        <button 
                            type="submit" 
                            id="submit-btn"
                            class="btn btn-primary btn-lg px-5 py-3 rounded-3 fw-semibold text-white shadow-lg"
                            style="background: linear-gradient(135deg, #0d6efd, #6f42c1); border: none; min-width: 200px;"
                        >
                            <i class="bi bi-lightning-charge me-2"></i>
                            Generate Content
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Loader -->
        <div id="seo-loader" class="text-center py-5 d-none">
            <div class="d-flex flex-column align-items-center">
                <div class="spinner-grow text-primary mb-4" style="width: 4rem; height: 4rem;" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <h5 class="text-dark mb-2">Generating SEO Content</h5>
                <p class="text-muted mb-3">Creating multiple optimized variants for your project...</p>
                <div class="progress w-50" style="height: 6px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 100%"></div>
                </div>
            </div>
        </div>

        <!-- Error Message -->
        <div id="seo-error" class="alert alert-danger alert-dismissible fade show d-none rounded-3 border-0 shadow-sm" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                <div class="flex-grow-1">
                    <h5 class="alert-heading mb-2">Generation Error</h5>
                    <p id="error-message" class="mb-0"></p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>

        <!-- Results Section -->
        <div id="seo-result" class="d-none">
            <!-- Results Header -->
            <div class="text-center mb-5">
                <div class="d-inline-flex align-items-center justify-content-center rounded-4 bg-success text-white mb-3 shadow-lg" 
                     style="width: 80px; height: 80px;">
                    <i class="bi bi-check-lg fs-3"></i>
                </div>
                <h3 class="h2 fw-bold text-dark mb-2">Content Generated Successfully</h3>
                <p class="text-muted mb-3">Choose from multiple optimized variants for your project</p>
                <div class="d-flex justify-content-center align-items-center gap-3">
                    <span class="badge bg-primary bg-opacity-10 px-3 py-2 rounded-3 text-white">
                        <i class="bi bi-layers me-1 text-white"></i>
                        <span id="variants-count text-white">3</span> Variants Generated
                    </span>
                    <span class="badge bg-success bg-opacity-10 text-white px-3 py-2 rounded-3" id="project-badge">
                        <i class="bi bi-folder me-1"></i>
                        Project: <span id="project-name">Loading...</span>
                    </span>
                </div>
            </div>

            <!-- Variants Grid -->
            <div class="row g-4" id="variants-container">
                <!-- Variants will be dynamically inserted here -->
            </div>

            <!-- Quick Actions -->
            <div class="text-center mt-5 pt-4 border-top">
                <div class="d-flex flex-wrap justify-content-center gap-3">
                    <button 
                        onclick="copyAllVariants()"
                        class="btn btn-outline-primary d-flex align-items-center gap-2 px-4 py-2 rounded-3"
                    >
                        <i class="bi bi-clipboard-check"></i>
                        Copy All Variants
                    </button>
                    <button 
                        id="regenerate-btn"
                        class="btn btn-warning d-flex align-items-center gap-2 px-4 py-2 rounded-3"
                    >
                        <i class="bi bi-arrow-clockwise"></i>
                        Generate New Variants
                    </button>
                    <a 
                        href="{{ route('seo.history.index') }}"
                        class="btn btn-outline-secondary d-flex align-items-center gap-2 px-4 py-2 rounded-3"
                    >
                        <i class="bi bi-clock-history"></i>
                        View History
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div id="seo-toast" class="toast position-fixed top-0 end-0 m-4" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-header bg-success text-white rounded-3">
        <i class="bi bi-check-circle-fill me-2"></i>
        <strong class="me-auto">Success</strong>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
    </div>
    <div id="seo-toast-message" class="toast-body bg-success text-white rounded-bottom-3">
        Successfully generated 3 SEO variants
    </div>
</div>

<style>
.bg-gradient-light {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #0d6efd, #6f42c1) !important;
}

.form-control:focus, .form-select:focus {
    border-color: #0d6efd !important;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15) !important;
}

.btn-primary {
    transition: all 0.3s ease !important;
    background: linear-gradient(135deg, #0d6efd, #6f42c1) !important;
    border: none !important;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(13, 110, 253, 0.35) !important;
}

.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.variant-card {
    border: 1px solid rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    height: 100%;
    border-radius: 1rem !important;
}

.variant-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 35px rgba(0,0,0,0.15) !important;
    border-color: rgba(13, 110, 253, 0.3);
}

.variant-card.selected {
    border: 2px solid #198754 !important;
    background: linear-gradient(135deg, rgba(25, 135, 84, 0.03), rgba(25, 135, 84, 0.08)) !important;
}

.copyable {
    cursor: pointer;
    transition: all 0.2s ease;
    border-radius: 8px;
    padding: 8px 12px;
    margin: -8px -12px;
}

.copyable:hover {
    background-color: rgba(13, 110, 253, 0.08);
    color: #0d6efd !important;
}

.character-count {
    font-size: 0.75em;
    font-weight: 500;
}

.optimization-indicator {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 6px;
}

.optimized {
    background-color: #198754;
}

.warning {
    background-color: #ffc107;
}

/* Animation for variants */
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

.variant-animate {
    animation: fadeInUp 0.6s ease forwards;
    opacity: 0;
}

/* Project badge styling */
#project-badge {
    font-size: 0.85em;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    console.log('✅ SEO Generator initialized');

    // Elements
    const form = document.getElementById('seo-form');
    const projectSelect = document.getElementById('project_id');
    const promptEl = document.getElementById('prompt');
    const langEl = document.getElementById('lang');
    const resultBox = document.getElementById('seo-result');
    const loader = document.getElementById('seo-loader');
    const errorBox = document.getElementById('seo-error');
    const submitBtn = document.getElementById('submit-btn');
    const regenerateBtn = document.getElementById('regenerate-btn');
    const variantsContainer = document.getElementById('variants-container');
    const variantsCount = document.getElementById('variants-count');
    const projectBadge = document.getElementById('project-name');
    const toastEl = document.getElementById('seo-toast');

    // Guards for required elements
    if (!form || !projectSelect || !promptEl) {
        console.error('❌ Required elements missing');
        return;
    }

    // Helpers
    function hideAll() {
        if (resultBox) resultBox.classList.add('d-none');
        if (errorBox) errorBox.classList.add('d-none');
        if (loader) loader.classList.add('d-none');
    }

    function showToast(message, type = 'success') {
        if (!toastEl || typeof bootstrap === 'undefined') return;
        
        const toastHeader = toastEl.querySelector('.toast-header');
        const icon = toastHeader.querySelector('i');
        const strong = toastHeader.querySelector('strong');
        
        // Update styling based on type
        if (type === 'success') {
            toastHeader.className = 'toast-header bg-success text-white rounded-top-3';
            toastEl.querySelector('.toast-body').className = 'toast-body bg-success text-white rounded-bottom-3';
            icon.className = 'bi bi-check-circle-fill me-2';
            strong.textContent = 'Success';
        } else if (type === 'error') {
            toastHeader.className = 'toast-header bg-danger text-white rounded-top-3';
            toastEl.querySelector('.toast-body').className = 'toast-body bg-danger text-white rounded-bottom-3';
            icon.className = 'bi bi-exclamation-triangle-fill me-2';
            strong.textContent = 'Error';
        } else {
            toastHeader.className = 'toast-header bg-info text-white rounded-top-3';
            toastEl.querySelector('.toast-body').className = 'toast-body bg-info text-white rounded-bottom-3';
            icon.className = 'bi bi-info-circle-fill me-2';
            strong.textContent = 'Info';
        }
        
        document.getElementById('seo-toast-message').textContent = message;
        const toast = new bootstrap.Toast(toastEl);
        toast.show();
    }

    function createVariantCard(variant, index, projectInfo) {
        const isTitleOptimized = variant.title.length >= 50 && variant.title.length <= 70;
        const isMetaOptimized = variant.meta.length >= 120 && variant.meta.length <= 160;
        
        return `
            <div class="col-md-6 col-lg-4">
                <div class="card variant-card variant-animate border-0 shadow-sm" style="animation-delay: ${index * 0.1}s">
                    <div class="card-header bg-light bg-opacity-50 border-0 py-3 rounded-top-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-primary bg-opacity-10 text-white">Variant ${index + 1}</span>
                            <div class="character-count text-muted small">
                                <span class="optimization-indicator ${isTitleOptimized ? 'optimized' : 'warning'}"></span>
                                <span class="title-length">${variant.title.length}</span> • 
                                <span class="optimization-indicator ${isMetaOptimized ? 'optimized' : 'warning'}"></span>
                                <span class="meta-length">${variant.meta.length}</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <!-- Project Info -->
                        <div class="mb-3 p-2 bg-light rounded-2">
                            <small class="text-muted d-block">
                                <i class="bi bi-folder me-1"></i>
                                ${projectInfo.name}
                            </small>
                        </div>

                        <!-- Title Section -->
                        <div class="mb-4">
                            <label class="form-label text-muted small text-uppercase fw-semibold mb-2 d-flex align-items-center justify-content-between">
                                <span>SEO Title</span>
                                <button type="button" onclick="copyVariantText('${variant.title.replace(/'/g, "\\'")}')" 
                                        class="btn btn-sm btn-outline-secondary border-0 rounded-2">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </label>
                            <h6 class="copyable text-dark fw-semibold mb-0 lh-base" 
                                onclick="copyVariantText('${variant.title.replace(/'/g, "\\'")}')"
                                data-bs-toggle="tooltip" title="Click to copy title">
                                ${variant.title}
                            </h6>
                        </div>

                        <!-- Meta Description -->
                        <div class="mb-4">
                            <label class="form-label text-muted small text-uppercase fw-semibold mb-2 d-flex align-items-center justify-content-between">
                                <span>Meta Description</span>
                                <button type="button" onclick="copyVariantText('${variant.meta.replace(/'/g, "\\'")}')" 
                                        class="btn btn-sm btn-outline-secondary border-0 rounded-2">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </label>
                            <p class="copyable text-muted small mb-0 lh-sm" 
                               onclick="copyVariantText('${variant.meta.replace(/'/g, "\\'")}')"
                               data-bs-toggle="tooltip" title="Click to copy meta description">
                               ${variant.meta}
                            </p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-2">
                            <button type="button" 
                                    onclick="useVariant(${index}, '${variant.title.replace(/'/g, "\\'")}', '${variant.meta.replace(/'/g, "\\'")}')"
                                    class="btn btn-success btn-sm flex-fill rounded-2">
                                <i class="bi bi-check-lg me-1"></i>Use This
                            </button>
                            <button type="button" 
                                    onclick="copyVariant(${index})"
                                    class="btn btn-outline-primary btn-sm rounded-2">
                                <i class="bi bi-files"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    function displayVariants(variants, projectInfo) {
        if (!variants || !Array.isArray(variants) || variants.length === 0) {
            throw new Error('No variants generated');
        }

        if (variantsContainer) {
            variantsContainer.innerHTML = variants.map((variant, index) => 
                createVariantCard(variant, index, projectInfo)
            ).join('');
        }

        if (variantsCount) {
            variantsCount.textContent = variants.length;
        }

        if (projectBadge && projectInfo) {
            projectBadge.textContent = projectInfo.name;
        }

        if (resultBox) {
            resultBox.classList.remove('d-none');
            resultBox.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        // Initialize tooltips for new elements
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    async function generate(prompt, lang, projectId) {
        try {
            console.log('📤 Generating variants for project:', projectId);
            
            if (!projectId) {
                throw new Error('Please select a project first');
            }

            const response = await fetch("{{ route('user.projects.seo.generate') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json"
                },
                body: JSON.stringify({ 
                    prompt: prompt, 
                    lang: lang,
                    project_id: projectId 
                })
            });

            const data = await response.json();
            console.log('📋 API Response:', data);

            if (!response.ok) {
                throw new Error(data.message || `HTTP error! status: ${response.status}`);
            }
            if (!data.success) {
                throw new Error(data.error || 'No content generated');
            }

            // Display all variants with project info
            displayVariants(data.variants, data.project);
            showToast(`Successfully generated ${data.variants.length} SEO variants for ${data.project.name}`, 'success');

        } catch (error) {
            console.error('❌ Error:', error);
            if (errorBox) {
                errorBox.classList.remove('d-none');
                document.getElementById('error-message').textContent = error.message || 'Failed to generate content.';
            }
            showToast(error.message || 'Failed to generate content. Please try again.', 'error');
        }
    }

    // Submit handler
    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        console.log('🔄 Form submission started');

        const prompt = promptEl.value.trim();
        const lang = langEl.value;
        const projectId = projectSelect.value;

        if (!projectId) {
            showToast('Please select a project to generate content for.', 'info');
            projectSelect.focus();
            return;
        }

        if (!prompt) {
            showToast('Please enter a prompt to generate SEO content.', 'info');
            promptEl.focus();
            return;
        }

        hideAll();
        if (loader) loader.classList.remove('d-none');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Generating...';
        }

        await generate(prompt, lang, projectId);

        if (loader) loader.classList.add('d-none');
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-lightning-charge me-2"></i>Generate Content';
        }
    });

    // Regenerate handler
    if (regenerateBtn) {
        regenerateBtn.addEventListener('click', async function () {
            const prompt = promptEl.value.trim();
            const lang = langEl.value;
            const projectId = projectSelect.value;

            if (!projectId) {
                showToast('No project selected!', 'warning');
                return;
            }

            if (!prompt) {
                showToast('No prompt available to regenerate!', 'warning');
                return;
            }

            if (loader) loader.classList.remove('d-none');
            regenerateBtn.disabled = true;
            regenerateBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Regenerating...';

            await generate(prompt, lang, projectId);

            if (loader) loader.classList.add('d-none');
            regenerateBtn.disabled = false;
            regenerateBtn.innerHTML = '<i class="bi bi-arrow-clockwise me-1"></i>Generate New Variants';
        });
    }
});

// ✅ Global functions for variant actions
function copyVariantText(text) {
    navigator.clipboard.writeText(text).then(() => {
        const toastEl = document.getElementById('seo-toast');
        const toastMsg = document.getElementById('seo-toast-message');
        if (toastEl && toastMsg) {
            toastEl.querySelector('.toast-header strong').textContent = 'Copied!';
            toastEl.querySelector('i').className = 'bi bi-clipboard-check me-2';
            toastMsg.textContent = 'Text copied to clipboard';
            new bootstrap.Toast(toastEl).show();
        }
    }).catch(err => {
        console.error('Copy failed:', err);
    });
}

function useVariant(index, title, meta) {
    const variantCards = document.querySelectorAll('.variant-card');
    
    // Remove selected class from all cards
    variantCards.forEach(card => card.classList.remove('selected'));
    
    // Add selected class to clicked card
    if (variantCards[index]) {
        variantCards[index].classList.add('selected');
    }
    
    showToast(`Variant ${index + 1} selected for use`, 'success');
    
    // You can add additional logic here to save the selected variant
    console.log('Selected variant:', { title, meta });
}

function copyVariant(index) {
    const variantCards = document.querySelectorAll('.variant-card');
    const card = variantCards[index];
    if (card) {
        const title = card.querySelector('h6').textContent;
        const meta = card.querySelector('p').textContent;
        const variantText = `Title: ${title}\nMeta: ${meta}`;
        copyVariantText(variantText);
    }
}

function copyAllVariants() {
    const variants = [];
    document.querySelectorAll('.variant-card').forEach((card, index) => {
        const title = card.querySelector('h6').textContent;
        const meta = card.querySelector('p').textContent;
        variants.push(`Variant ${index + 1}:\nTitle: ${title}\nMeta: ${meta}\n`);
    });
    
    const allText = variants.join('\n---\n\n');
    copyVariantText(allText);
    showToast('All variants copied to clipboard!', 'success');
}
</script>

@if($prefill)
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('prompt').focus();
});
</script>
@endif

@endsection