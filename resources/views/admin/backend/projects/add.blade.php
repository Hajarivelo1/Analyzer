@extends('admin.admin_master')
@section('admin')

<div class="container-fluid p-0">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="glass-card p-4">
                <!-- Header -->
                <div class="glass-card-header">
                    <div class="d-flex justify-content-between align-items-center p-4">
                        <div>
                            <h2 class="mb-1"><i class="bi bi-folder-plus me-2"></i>Add New Project</h2>
                            <p class="mb-0">Create a new SEO analysis project</p>
                        </div>
                        <a href="{{ route('all.projects') }}" class="glass-outline-btn text-decoration-none">
                            <i class="bi bi-arrow-left me-2"></i>Back to Projects
                        </a>
                    </div>
                </div>

                <!-- Progress Steps -->
                <div class="px-4 pt-4">
                    <div class="glass-progress-steps">
                        <div class="step active">
                            <div class="step-number">1</div>
                            <div class="step-label">Basic Info</div>
                        </div>
                        <div class="step">
                            <div class="step-number">2</div>
                            <div class="step-label text-dark">SEO Settings</div>
                        </div>
                        <div class="step">
                            <div class="step-number">3</div>
                            <div class="step-label">Review</div>
                        </div>
                    </div>
                </div>

                <!-- Project Form -->
                <div class="card-body">
                    <form action="{{ route('store.projects') }}" method="POST" id="projectForm">
                        @csrf

                        <!-- Step 1: Basic Information -->
                        <div class="form-step active" id="step1">
                            <h5 class="text-primary mb-4"><i class="bi bi-info-circle me-2"></i>Basic Information</h5>
                            
                            <div class="row g-3">
                                <!-- Project Name -->
                                <div class="col-12">
                                    <label for="name" class="form-label">Project Name *</label>
                                    <div class="input-group">
                                        <span class="input-group-text glass-input-icon">
                                            <i class="bi bi-pencil"></i>
                                        </span>
                                        <input type="text" class="form-control personal-info-input" 
                                               id="name" name="name" placeholder="My E-commerce Website" required>
                                    </div>
                                </div>

                                <!-- Website URL -->
                                <div class="col-12">
                                    <label for="base_url" class="form-label">Website URL *</label>
                                    <div class="input-group">
                                        <span class="input-group-text glass-input-icon">
                                            <i class="bi bi-globe"></i>
                                        </span>
                                        <input type="url" class="form-control personal-info-input" 
                                               id="base_url" name="base_url" 
                                               placeholder="https://example.com" required>
                                    </div>
                                    <div class="form-text">Enter the full URL including https://</div>
                                </div>

                                <!-- Project Description -->
                                <div class="col-12">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control personal-info-textarea" 
                                              id="description" name="description" 
                                              rows="3" placeholder="Brief description of your project..."></textarea>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="button" class="btn btn-primary next-step" data-next="step2">
                                    Next <i class="bi bi-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Step 2: SEO Settings -->
                        <div class="form-step" id="step2">
                            <h5 class="text-primary mb-4"><i class="bi bi-gear me-2"></i>SEO Settings</h5>
                            
                            <div class="row g-3">
                                <!-- Target Keywords -->
                                <div class="col-12">
                                    <label for="target_keywords" class="form-label">Target Keywords</label>
                                    <div class="input-group">
                                        <span class="input-group-text glass-input-icon">
                                            <i class="bi bi-tags"></i>
                                        </span>
                                        <input type="text" class="form-control personal-info-input" 
                                               id="target_keywords" name="target_keywords" 
                                               placeholder="seo, digital marketing, search engine optimization">
                                    </div>
                                    <div class="form-text">Separate keywords with commas</div>
                                </div>

                                <!-- Analysis Frequency -->
                                <div class="col-md-6">
                                    <label for="analysis_frequency" class="form-label">Analysis Frequency</label>
                                    <select class="form-select personal-info-input" id="analysis_frequency" name="analysis_frequency">
                                        <option value="weekly">Weekly</option>
                                        <option value="monthly" selected>Monthly</option>
                                        <option value="quarterly">Quarterly</option>
                                    </select>
                                </div>

                                <!-- Competitor Analysis -->
                                <div class="col-md-6">
                                    <label for="competitor_analysis" class="form-label">Competitor Analysis</label>
                                    <select class="form-select personal-info-input" id="competitor_analysis" name="competitor_analysis">
                                        <option value="basic">Basic (Top 3)</option>
                                        <option value="advanced" selected>Advanced (Top 10)</option>
                                        <option value="none">Disabled</option>
                                    </select>
                                </div>

                                <!-- Settings Toggles -->
                                <div class="col-12">
                                    <div class="glass-settings-card p-3">
                                        <h6 class="mb-3">Additional Settings</h6>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                                            <label class="form-check-label" for="is_active">Active Project</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="enable_monitoring" name="enable_monitoring" checked>
                                            <label class="form-check-label" for="enable_monitoring">Enable SEO Monitoring</label>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="auto_reports" name="auto_reports">
                                            <label class="form-check-label" for="auto_reports">Automatic Reports</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="glass-outline-btn prev-step" data-prev="step1">
                                    <i class="bi bi-arrow-left me-2"></i>Previous
                                </button>
                                <button type="button" class="btn btn-primary next-step" data-next="step3">
                                    Next <i class="bi bi-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Step 3: Review & Create -->
                        <div class="form-step" id="step3">
                            <h5 class="text-primary mb-4"><i class="bi bi-check-circle me-2"></i>Review & Create</h5>
                            
                            <div class="glass-review-card p-4 mb-4">
                                <h6 class="mb-3">Project Summary</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Project Name:</strong>
                                        <span id="review-name" class="text-muted">-</span>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Website URL:</strong>
                                        <span id="review-url" class="text-muted">-</span>
                                    </div>
                                    <div class="col-md-6 mt-2">
                                        <strong>Analysis Frequency:</strong>
                                        <span id="review-frequency" class="text-muted">-</span>
                                    </div>
                                    <div class="col-md-6 mt-2">
                                        <strong>Competitor Analysis:</strong>
                                        <span id="review-competitor" class="text-muted">-</span>
                                    </div>
                                </div>
                            </div>

                            

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="glass-outline-btn prev-step" data-prev="step2">
                                    <i class="bi bi-arrow-left me-2"></i>Previous
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-plus-circle me-2"></i>Create Project
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.glass-progress-steps {
    display: flex;
    justify-content: space-between;
    margin-bottom: 2rem;
}

.step {
    display: flex;
    flex-direction: column;
    align-items: center;
    flex: 1;
    position: relative;
}

.step:not(:last-child)::after {
    content: '';
    position: absolute;
    top: 20px;
    left: 60%;
    width: 80%;
    height: 2px;
    background: rgba(255, 255, 255, 0.3);
    z-index: 1;
}

.step-number {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    border: 2px solid rgba(207, 204, 204, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    margin-bottom: 0.5rem;
    z-index: 2;
    transition: all 0.3s ease;
}

.step.active .step-number {
    background: rgba(37, 99, 235, 0.8);
    border-color: rgba(37, 99, 235, 0.9);
    color: white;
}

.step-label {
    font-size: 0.875rem;
    color: rgba(109, 104, 104, 0.7);
}

.step.active .step-label {
    color: #2563eb;
    font-weight: 500;
}

.form-step {
    display: none;
}

.form-step.active {
    display: block;
    animation: fadeIn 0.5s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.glass-settings-card {
    background: rgba(255, 255, 255, 0.3);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 0.75rem;
}

.glass-review-card {
    background: rgba(255, 255, 255, 0.4);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 0.75rem;
}

.form-switch .form-check-input:checked {
    background-color: #2563eb;
    border-color: #2563eb;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Multi-step form functionality
    const steps = document.querySelectorAll('.form-step');
    const stepButtons = document.querySelectorAll('.step');
    
    // Show first step initially
    showStep('step1');
    
    // Next step buttons
    document.querySelectorAll('.next-step').forEach(button => {
        button.addEventListener('click', function() {
            const nextStep = this.dataset.next;
            if (validateStep(getCurrentStep())) {
                showStep(nextStep);
                updateProgress(nextStep);
            }
        });
    });
    
    // Previous step buttons
    document.querySelectorAll('.prev-step').forEach(button => {
        button.addEventListener('click', function() {
            const prevStep = this.dataset.prev;
            showStep(prevStep);
            updateProgress(prevStep);
        });
    });
    
    function showStep(stepId) {
        steps.forEach(step => {
            step.classList.remove('active');
            if (step.id === stepId) {
                step.classList.add('active');
            }
        });
    }
    
    function getCurrentStep() {
        return document.querySelector('.form-step.active').id;
    }
    
    function updateProgress(stepId) {
        stepButtons.forEach((step, index) => {
            step.classList.remove('active');
            if (stepId === `step${index + 1}`) {
                step.classList.add('active');
            }
        });
    }
    
    function validateStep(stepId) {
        const step = document.getElementById(stepId);
        const inputs = step.querySelectorAll('input[required], select[required]');
        
        for (let input of inputs) {
            if (!input.value.trim()) {
                input.focus();
                showError(input, 'This field is required');
                return false;
            }
            
            if (input.type === 'url' && !isValidUrl(input.value)) {
                input.focus();
                showError(input, 'Please enter a valid URL');
                return false;
            }
        }
        
        return true;
    }
    
    function isValidUrl(string) {
        try {
            new URL(string);
            return true;
        } catch (_) {
            return false;
        }
    }
    
    function showError(input, message) {
        // Remove existing error
        const existingError = input.parentNode.querySelector('.error-message');
        if (existingError) existingError.remove();
        
        // Add error message
        const error = document.createElement('div');
        error.className = 'error-message text-danger small mt-1';
        error.textContent = message;
        input.parentNode.appendChild(error);
        
        // Add error styling
        input.classList.add('is-invalid');
        
        // Remove error after 3 seconds
        setTimeout(() => {
            error.remove();
            input.classList.remove('is-invalid');
        }, 3000);
    }
    
    // Update review section
    document.getElementById('name').addEventListener('input', updateReview);
    document.getElementById('base_url').addEventListener('input', updateReview);
    document.getElementById('analysis_frequency').addEventListener('change', updateReview);
    document.getElementById('competitor_analysis').addEventListener('change', updateReview);
    
    function updateReview() {
        document.getElementById('review-name').textContent = document.getElementById('name').value || '-';
        document.getElementById('review-url').textContent = document.getElementById('base_url').value || '-';
        document.getElementById('review-frequency').textContent = document.getElementById('analysis_frequency').options[document.getElementById('analysis_frequency').selectedIndex].text;
        document.getElementById('review-competitor').textContent = document.getElementById('competitor_analysis').options[document.getElementById('competitor_analysis').selectedIndex].text;
    }
    
    // Initial update
    updateReview();

    // Final form submission validation
document.getElementById('projectForm').addEventListener('submit', function(e) {
    const currentStep = getCurrentStep();
    if (!validateStep(currentStep)) {
        e.preventDefault(); // Stop submission if validation fails
        return false;
    }

    console.log('Form submitted'); // Debug confirmation
});

});
</script>








@endsection
