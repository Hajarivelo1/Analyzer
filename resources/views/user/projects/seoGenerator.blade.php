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
                        ></textarea>
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
                            <option value="en" selected>English</option>
                            <option value="fr">French</option>
                            <option value="de">German</option>
                            <option value="es">Spanish</option>
                            <option value="it">Italian</option>
                        </select>
                    </div>

                    <!-- Submit Button -->
                    <div class="text-center mt-4">
                        <button 
                            type="submit" 
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
                            onclick="copyText('seo-title')"
                            class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2"
                        >
                            <span>📋</span>
                            Copy Title
                        </button>
                    </div>
                    <p id="seo-title" class="text-primary fw-medium fs-5 mb-0"></p>
                </div>
            </div>

            <!-- Meta Description Card -->
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title fw-semibold text-dark mb-0">Meta Description</h5>
                        <button 
                            onclick="copyText('seo-meta')"
                            class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2"
                        >
                            <span>📋</span>
                            Copy Description
                        </button>
                    </div>
                    <p id="seo-meta" class="text-muted mb-0 fst-italic"></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom Styles -->
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

<!-- Script -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById('seo-form');
    const resultBox = document.getElementById('seo-result');
    const loader = document.getElementById('seo-loader');
    const seoTitle = document.getElementById('seo-title');
    const seoMeta = document.getElementById('seo-meta');

    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const prompt = document.getElementById('prompt').value.trim();
            const lang = document.getElementById('lang').value;

            // Validation
            if (!prompt) {
                alert('Please enter a prompt to generate SEO content.');
                return;
            }

            // Show loader, hide results
            loader.classList.remove('d-none');
            resultBox.classList.add('d-none');

            try {
                const response = await fetch("{{ route('user.projects.seo.generate') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json"
                    },
                    body: JSON.stringify({ 
                        prompt: prompt,
                        lang: lang 
                    })
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                console.log("API Response:", data);

                if (data.content) {
                    const content = data.content.replace(/\*\*/g, "").trim();
                    const lines = content.split("\n").map(l => l.trim()).filter(l => l !== "");

                    let titleLine = "";
                    let metaLine = "";

                    // Parse the response to find title and meta description
                    lines.forEach((line, i) => {
                        const lowerLine = line.toLowerCase();
                        if (lowerLine.includes("seo title") || lowerLine.includes("title")) {
                            titleLine = lines[i + 1]?.replace(/^[:\-\s]*/, "") || "";
                        }
                        if (lowerLine.includes("meta") || lowerLine.includes("description")) {
                            metaLine = lines[i + 1]?.replace(/^[:\-\s]*/, "") || "";
                        }
                    });

                    // Fallback: if not found by markers, use first two non-empty lines
                    if (!titleLine && lines.length > 0) {
                        titleLine = lines[0];
                    }
                    if (!metaLine && lines.length > 1) {
                        metaLine = lines[1];
                    }

                    seoTitle.textContent = titleLine || "No title generated";
                    seoMeta.textContent = metaLine || "No meta description generated";

                    // Show results with animation
                    setTimeout(() => {
                        resultBox.classList.remove('d-none');
                        resultBox.scrollIntoView({ 
                            behavior: 'smooth', 
                            block: 'start' 
                        });
                    }, 300);

                } else {
                    throw new Error('No content in response');
                }

            } catch (error) {
                console.error("Error during request:", error);
                
                seoTitle.textContent = "Error";
                seoMeta.textContent = "Unable to generate content. Please try again.";
                
                resultBox.classList.remove('d-none');
                
            } finally {
                loader.classList.add('d-none');
            }
        });
    }
});

// Copy to clipboard function
function copyText(elementId) {
    const element = document.getElementById(elementId);
    const text = element.textContent.trim();
    
    if (text && text !== "No title generated" && text !== "No meta description generated") {
        navigator.clipboard.writeText(text).then(() => {
            // Show temporary success feedback
            const originalText = element.textContent;
            const originalColor = element.style.color;
            
            element.textContent = "✓ Copied to clipboard!";
            element.style.color = "#198754";
            
            setTimeout(() => {
                element.textContent = originalText;
                element.style.color = originalColor;
            }, 1500);
            
        }).catch(err => {
            console.error("Error copying text:", err);
            alert("Failed to copy text to clipboard.");
        });
    }
}
</script>

@endsection