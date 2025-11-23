@extends('admin.admin_master')

@section('admin')

<div class="min-vh-100 bg-light py-5">
    <!-- Header Section -->
    <div class="text-center mb-5">
        <div class="d-inline-flex align-items-center justify-content-center rounded-3 bg-gradient-primary text-white mb-4" 
             style="width: 80px; height: 80px;">
            <span class="fs-2">✨</span>
        </div>
        <h1 class="display-5 fw-bold text-dark mb-3">
            SEO Content Generator
        </h1>
        <p class="lead text-muted mx-auto" style="max-width: 600px;">
            Optimize your titles and meta descriptions with AI-powered precision using Ollama
        </p>
    </div>

    <!-- Main Content -->
    <div class="container" style="max-width: 1200px;">
        <!-- Form Card -->
        <div class="card border-0 shadow-lg rounded-3 overflow-hidden mb-4">
            <div class="card-body p-4 p-md-5">
                <form id="seo-form" method="POST" action="{{ route('user.projects.seo.generate') }}">
                    @csrf
                    
                    <!-- Prompt Input -->
                    <div class="mb-4">
                        <label for="prompt" class="form-label fw-semibold text-dark text-uppercase small">
                            SEO Prompt
                        </label>
                        <textarea 
                            class="form-control form-control-lg border-2 rounded-2"
                            id="prompt" 
                            name="prompt" 
                            rows="4"
                            placeholder="Example: Generate an SEO title and meta description for 'Madagascar Travel Guide'"
                            style="border-color: #e2e8f0; resize: none;"
                            required
                        >{{ old('prompt', $prefillPrompt) }}</textarea>
                    </div>

                    <!-- Language Selector -->
                    <div class="mb-4">
                        <label for="lang" class="form-label fw-semibold text-dark text-uppercase small">
                            Language
                        </label>
                        <select 
                            class="form-select form-select-lg border-2 rounded-2"
                            id="lang" 
                            name="lang"
                            style="border-color: #e2e8f0;"
                        >
                            <option value="en" {{ ($prefillLang ?? 'en') === 'en' ? 'selected' : '' }}>English</option>
                            <option value="fr" {{ ($prefillLang ?? 'en') === 'fr' ? 'selected' : '' }}>French</option>
                            <option value="de" {{ ($prefillLang ?? 'en') === 'de' ? 'selected' : '' }}>German</option>
                            <option value="es" {{ ($prefillLang ?? 'en') === 'es' ? 'selected' : '' }}>Spanish</option>
                            <option value="it" {{ ($prefillLang ?? 'en') === 'it' ? 'selected' : '' }}>Italian</option>
                        </select>
                    </div>

                    <!-- Submit Button -->
                    <div class="text-center mt-4">
                        <button 
                            type="submit" 
                            id="submit-btn"
                            class="btn btn-primary btn-lg px-5 py-3 rounded-2 fw-semibold text-white"
                            style="background: linear-gradient(135deg, #0d6efd, #6f42c1); border: none;"
                        >
                            <span class="me-2">🚀</span>
                            Generate Content
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Loader -->
        <div id="seo-loader" class="text-center py-5 d-none">
            <div class="d-flex flex-column align-items-center">
                <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="text-muted fw-medium mb-1">Generating SEO content variants...</p>
                <p class="text-muted small">Creating multiple optimized options for you</p>
            </div>
        </div>

        <!-- Error Message -->
        <div id="seo-error" class="alert alert-danger d-none text-center">
            <h5 class="alert-heading">⚠️ Generation Error</h5>
            <p id="error-message" class="mb-0"></p>
        </div>

        <!-- Results Section - MULTIPLE VARIANTS -->
        <div id="seo-result" class="d-none">
            <!-- Results Header -->
            <div class="text-center mb-5">
                <div class="d-inline-flex align-items-center justify-content-center rounded-3 bg-success text-white mb-3" 
                     style="width: 70px; height: 70px;">
                    <span class="fs-3">✨</span>
                </div>
                <h3 class="h2 fw-bold text-dark mb-2">Content Generated Successfully</h3>
                <p class="text-muted">Choose from multiple optimized variants below</p>
                <div class="badge bg-primary bg-opacity-10 text-white px-3 py-2">
                    <i class="bi bi-layers me-1"></i>
                    <span id="variants-count" style="color: #fff !important;">3</span> Variants Generated
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
                        class="btn btn-outline-primary d-flex align-items-center gap-2"
                    >
                        <i class="bi bi-clipboard-check"></i>
                        Copy All Variants
                    </button>
                    <button 
                        id="regenerate-btn"
                        class="btn btn-warning d-flex align-items-center gap-2"
                    >
                        <i class="bi bi-arrow-clockwise"></i>
                        Generate New Variants
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notification CORRIGÉ -->
<div id="seo-toast" class="toast position-fixed bottom-0 end-0 m-3" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-header bg-success text-white"> <!-- ✅ AJOUT bg-success text-white -->
        <i class="bi bi-check-circle-fill text-white me-2"></i> <!-- ✅ text-white -->
        <strong class="me-auto text-white">Success</strong> <!-- ✅ text-white -->
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button> <!-- ✅ btn-close-white -->
    </div>
    <div id="seo-toast-message" class="toast-body bg-success text-white"> <!-- ✅ bg-success text-white -->
        Successfully generated 3 SEO variants
    </div>
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #0d6efd, #6f42c1) !important;
}

.form-control:focus, .form-select:focus {
    border-color: #0d6efd !important;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25) !important;
}

.btn-primary {
    transition: all 0.3s ease !important;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3) !important;
}

.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.variant-card {
    border: 1px solid rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    height: 100%;
}

.variant-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.12) !important;
    border-color: rgba(13, 110, 253, 0.2);
}

.copyable {
    cursor: pointer;
    transition: all 0.2s ease;
    border-radius: 6px;
    padding: 4px 8px;
    margin: -4px -8px;
}

.copyable:hover {
    background-color: rgba(13, 110, 253, 0.08);
    color: #0d6efd !important;
}

.badge-optimized {
    background: linear-gradient(135deg, #198754, #157347);
    color: white;
    font-size: 0.7em;
}

.character-count {
    font-size: 0.75em;
    font-weight: 500;
}

/* Animation for variants */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.variant-animate {
    animation: fadeInUp 0.5s ease forwards;
    opacity: 0;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    console.log('✅ SEO Generator initialized');

    // Elements
    const form = document.getElementById('seo-form');
    const promptEl = document.getElementById('prompt');
    const langEl = document.getElementById('lang');
    const resultBox = document.getElementById('seo-result');
    const loader = document.getElementById('seo-loader');
    const errorBox = document.getElementById('seo-error');
    const submitBtn = document.getElementById('submit-btn');
    const regenerateBtn = document.getElementById('regenerate-btn');
    const variantsContainer = document.getElementById('variants-container');
    const variantsCount = document.getElementById('variants-count');
    const toastEl = document.getElementById('seo-toast');

    // Guards for required elements
    if (!form || !promptEl || !langEl) {
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
        
        // Update icon based on type
        if (type === 'success') {
            icon.className = 'bi bi-check-circle-fill text-success me-2';
            toastHeader.querySelector('strong').textContent = 'Success';
        } else if (type === 'error') {
            icon.className = 'bi bi-exclamation-triangle-fill text-danger me-2';
            toastHeader.querySelector('strong').textContent = 'Error';
        } else {
            icon.className = 'bi bi-info-circle-fill text-primary me-2';
            toastHeader.querySelector('strong').textContent = 'Info';
        }
        
        document.getElementById('seo-toast-message').textContent = message;
        const toast = new bootstrap.Toast(toastEl);
        toast.show();
    }

    function createVariantCard(variant, index) {
        return `
            <div class="col-md-6 col-lg-4">
                <div class="card variant-card variant-animate border-0 shadow-sm" style="animation-delay: ${index * 0.1}s">
                    <div class="card-header bg-light bg-opacity-50 border-0 py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge badge-optimized">Variant ${index + 1}</span>
                            <div class="character-count text-muted small">
                                <span class="title-length">${variant.title.length}</span> chars • 
                                <span class="meta-length">${variant.meta.length}</span> chars
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <!-- Title Section -->
                        <div class="mb-4">
                            <label class="form-label text-muted small text-uppercase fw-semibold mb-2 d-flex align-items-center justify-content-between">
                                <span>SEO Title</span>
                                <button type="button" onclick="copyVariantText('${variant.title.replace(/'/g, "\\'")}')" 
                                        class="btn btn-sm btn-outline-secondary border-0">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </label>
                            <h6 class="copyable text-dark fw-semibold mb-0" 
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
                                        class="btn btn-sm btn-outline-secondary border-0">
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
                                    onclick="useVariant(${index})"
                                    class="btn btn-success btn-sm flex-fill">
                                <i class="bi bi-check-lg me-1"></i>Use This
                            </button>
                            <button type="button" 
                                    onclick="copyVariant(${index})"
                                    class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-files"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    function displayVariants(variants) {
        if (!variants || !Array.isArray(variants) || variants.length === 0) {
            throw new Error('No variants generated');
        }

        if (variantsContainer) {
            variantsContainer.innerHTML = variants.map((variant, index) => 
                createVariantCard(variant, index)
            ).join('');
        }

        if (variantsCount) {
            variantsCount.textContent = variants.length;
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

    async function generate(prompt, lang) {
        try {
            console.log('📤 Generating variants for:', prompt);
            const response = await fetch("{{ route('user.projects.seo.generate') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json"
                },
                body: JSON.stringify({ prompt, lang })
            });

            const data = await response.json();
            console.log('📋 API Response:', data);

            if (!response.ok) {
                throw new Error(data.message || `HTTP error! status: ${response.status}`);
            }
            if (!data.success) {
                throw new Error(data.error || 'No content generated');
            }

            // Display all variants
            displayVariants(data.variants);
            showToast(`Successfully generated ${data.variants.length} SEO variants`, 'success');

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

        if (!prompt) {
            showToast('Please enter a prompt to generate SEO content.', 'info');
            return;
        }

        hideAll();
        if (loader) loader.classList.remove('d-none');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Generating Variants...';
        }

        await generate(prompt, lang);

        if (loader) loader.classList.add('d-none');
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<span class="me-2">🚀</span>Generate Content';
        }
    });

    // Regenerate handler
    if (regenerateBtn) {
        regenerateBtn.addEventListener('click', async function () {
            const prompt = promptEl.value.trim();
            const lang = langEl.value;

            if (!prompt) {
                showToast('No prompt available to regenerate!', 'warning');
                return;
            }

            if (loader) loader.classList.remove('d-none');
            regenerateBtn.disabled = true;
            regenerateBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Regenerating...';

            await generate(prompt, lang);

            if (loader) loader.classList.add('d-none');
            regenerateBtn.disabled = false;
            regenerateBtn.innerHTML = '<i class="bi bi-arrow-clockwise me-1"></i>Generate New Variants';
        });
    }
});

// ✅ Global functions for variant actions
function copyVariantText(text) {
    navigator.clipboard.writeText(text).then(() => {
        // Find and show toast
        const toastEl = document.getElementById('seo-toast');
        const toastMsg = document.getElementById('seo-toast-message');
        if (toastEl && toastMsg) {
            toastEl.querySelector('.toast-header strong').textContent = 'Copied!';
            toastEl.querySelector('i').className = 'bi bi-clipboard-check text-success me-2';
            toastMsg.textContent = 'Text copied to clipboard';
            new bootstrap.Toast(toastEl).show();
        }
    }).catch(err => {
        console.error('Copy failed:', err);
    });
}

function useVariant(index) {
    const variantCards = document.querySelectorAll('.variant-card');
    if (variantCards[index]) {
        // Add visual feedback
        variantCards[index].style.border = '2px solid #198754';
        variantCards[index].style.backgroundColor = 'rgba(25, 135, 84, 0.05)';
        
        setTimeout(() => {
            variantCards[index].style.border = '';
            variantCards[index].style.backgroundColor = '';
        }, 1000);
    }
    showToast(`Variant ${index + 1} selected for use`, 'success');
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