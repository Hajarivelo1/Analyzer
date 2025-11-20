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
    <div class="container" style="max-width: 800px;">
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
                <p class="text-muted fw-medium mb-1">Generating SEO content...</p>
                <p class="text-muted small">This may take a few moments</p>
            </div>
        </div>

        <!-- Error Message -->
        <div id="seo-error" class="alert alert-danger d-none text-center">
            <h5 class="alert-heading">⚠️ Generation Error</h5>
            <p id="error-message" class="mb-0"></p>
        </div>

        <!-- Results Section -->
        <div id="seo-result" class="d-none">
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center rounded-3 bg-success text-white mb-3" 
                     style="width: 70px; height: 70px;">
                    <span class="fs-3">✨</span>
                </div>
                <h3 class="h2 fw-bold text-dark mb-2">Content Generated Successfully</h3>
                <p class="text-muted">Your optimized SEO content is ready</p>
            </div>

            <!-- Title Card -->
            <div class="card border-0 shadow-sm rounded-3 mb-3">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title fw-semibold text-dark mb-0">SEO Title</h5>
                        <button 
                            onclick="copyToClipboard('seo-title')"
                            class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2"
                        >
                            <span>📋</span>
                            Copy Title
                        </button>
                    </div>
                    <p id="seo-title" class="text-primary fw-medium fs-5 mb-0"></p>
                    <small id="seo-title-length" class="text-muted"></small>
                </div>
            </div>

            <!-- Meta Description Card -->
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title fw-semibold text-dark mb-0">Meta Description</h5>
                        <button 
                            onclick="copyToClipboard('seo-meta')"
                            class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2"
                        >
                            <span>📋</span>
                            Copy Description
                        </button>
                    </div>
                    <p id="seo-meta" class="text-muted mb-0 fst-italic"></p>
                    <small id="seo-meta-length" class="text-muted"></small>
                </div>
            </div>
        </div>
        <!-- Regenerate Button -->
<div class="text-center mt-4">
    <button 
        id="regenerate-btn"
        class="btn btn-warning btn-lg px-4 py-2 rounded-2 fw-semibold d-none"
    >
        🔄 Regenerate Content
    </button>
</div>

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
    
    .card:hover {
        transform: translateY(-2px);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ SEO Generator initialized');

    const form = document.getElementById('seo-form');
    const resultBox = document.getElementById('seo-result');
    const loader = document.getElementById('seo-loader');
    const errorBox = document.getElementById('seo-error');
    const seoTitle = document.getElementById('seo-title');
    const seoMeta = document.getElementById('seo-meta');
    const submitBtn = document.getElementById('submit-btn');
    const regenerateBtn = document.getElementById('regenerate-btn');

    if (!form) {
        console.error('❌ Form not found');
        return;
    }

    // --- Soumission du formulaire ---
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        console.log('🔄 Form submission started');

        const prompt = document.getElementById('prompt').value.trim();
        const lang = document.getElementById('lang').value;

        if (!prompt) {
            showToast('Please enter a prompt to generate SEO content.', 'warning');
            return;
        }

        // Reset states
        hideAll();
        loader.classList.remove('d-none');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Generating...';

        try {
            console.log('📤 Sending request...');
            const response = await fetch("{{ route('user.projects.seo.generate') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json"
                },
                body: JSON.stringify({ prompt, lang })
            });

            console.log('📥 Response status:', response.status);
            const data = await response.json();
            console.log('📋 API Response:', data);

            if (!response.ok) {
                throw new Error(data.message || `HTTP error! status: ${response.status}`);
            }

            if (data.success) {
                console.log('✅ Content generated successfully');

                // Mettre à jour l'interface avec title/meta
                seoTitle.textContent = data.title;
                seoMeta.textContent  = data.meta;

                // Afficher les longueurs
                document.getElementById('seo-title-length').textContent = `${data.title.length} characters`;
                document.getElementById('seo-meta-length').textContent  = `${data.meta.length} characters`;

                // Afficher la section résultats
                resultBox.classList.remove('d-none');
                resultBox.scrollIntoView({ behavior: 'smooth', block: 'start' });

                // 👉 Afficher le bouton Regenerate
                regenerateBtn.classList.remove('d-none');
            } else {
                throw new Error(data.error || 'No content generated');
            }

        } catch (error) {
            console.error('❌ Error:', error);
            showToast(error.message || 'Failed to generate content. Please try again.', 'danger');
        } finally {
            loader.classList.add('d-none');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<span class="me-2">🚀</span>Generate Content';
        }
    });

    // --- Bouton Regenerate ---
    regenerateBtn.addEventListener('click', async function() {
        const prompt = document.getElementById('prompt').value.trim();
        const lang = document.getElementById('lang').value;

        if (!prompt) {
            showToast("No prompt available to regenerate!", "warning");
            return;
        }

        loader.classList.remove('d-none');
        regenerateBtn.disabled = true;

        try {
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

            if (data.success) {
                seoTitle.textContent = data.title;
                seoMeta.textContent  = data.meta;

                document.getElementById('seo-title-length').textContent = `${data.title.length} characters`;
                document.getElementById('seo-meta-length').textContent  = `${data.meta.length} characters`;

                showToast("Content regenerated successfully!", "success");
            } else {
                showToast(data.error || "Failed to regenerate content", "danger");
            }
        } catch (err) {
            console.error(err);
            showToast("Error during regeneration", "danger");
        } finally {
            loader.classList.add('d-none');
            regenerateBtn.disabled = false;
        }
    });

    // --- Fonctions utilitaires ---
    function hideAll() {
        resultBox.classList.add('d-none');
        errorBox.classList.add('d-none');
        loader.classList.add('d-none');
    }

    function showToast(message, type = 'danger') {
        const toastEl = document.getElementById('seo-toast');
        const toastMsg = document.getElementById('seo-toast-message');

        toastEl.className = `toast align-items-center text-bg-${type} border-0`;
        toastMsg.textContent = message;

        const toast = new bootstrap.Toast(toastEl);
        toast.show();
    }
});

// ✅ Fonction copier améliorée
function copyToClipboard(elementId) {
    const element = document.getElementById(elementId);
    const text = element.textContent.trim();

    if (!text || text.startsWith('⚠️')) {
        return;
    }

    navigator.clipboard.writeText(text).then(() => {
        const originalText = element.textContent;
        element.textContent = '✓ Copied to clipboard!';
        element.style.color = '#198754';

        setTimeout(() => {
            element.textContent = originalText;
            element.style.color = elementId === 'seo-title' ? '#0d6efd' : '#6c757d';
        }, 1500);

    }).catch(err => {
        console.error('Copy failed:', err);
        showToast("Failed to copy to clipboard. Please select and copy manually.", "danger");
    });
}
</script>



@endsection

@if($prefill)
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('prompt').focus();
});
</script>
@endif
