@extends('admin.admin_master')
@section('admin')

<div class="min-h-screen py-4" id="analysis-app">
    <div class="container-fluid">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="glass-card rounded-3 p-4 border border-white/20 backdrop-blur-xl">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="display-6 fw-bold text-dark mb-2">New SEO Analysis</h1>
                            <p class="lead text-gray-300 mb-0">Get AI-powered insights to boost your website's performance</p>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <span class="badge bg-white/10 border border-white/20 px-3 py-2 rounded-pill text-dark">
                                <i class="fas fa-robot me-2"></i>AI Powered
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Project Selection Cards -->
        <div class="row mb-4">
            <div class="col-md-6 mb-3">
                <div class="glass-card rounded-3 border border-white/20 p-4 h-100 cursor-pointer transition-all"
                     id="new-project-card"
                     onclick="selectOption('new')">
                    <div class="d-flex align-items-start gap-3">
                        <div class="flex-shrink-0 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-2 p-3">
                            <i class="fas fa-plus text-white fa-lg"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h4 class="text-dark fw-semibold mb-2">New Project</h4>
                            <p class="text-gray-300 mb-3">Start fresh with a new website analysis</p>
                            <div class="d-flex align-items-center text-blue-300">
                                <small>Perfect for new websites</small>
                                <i class="fas fa-arrow-right ms-2 transition-transform"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-3">
                <div class="glass-card rounded-3 border border-white/20 p-4 h-100 cursor-pointer transition-all"
                     id="existing-project-card"
                     onclick="selectOption('existing')">
                    <div class="d-flex align-items-start gap-3">
                        <div class="flex-shrink-0 bg-gradient-to-br from-green-500 to-emerald-500 rounded-2 p-3">
                            <i class="fas fa-folder text-white fa-lg"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h4 class="text-dark fw-semibold mb-2">Existing Project</h4>
                            <p class="text-gray-300 mb-3">Add analysis to existing project</p>
                            <div class="d-flex align-items-center text-green-300">
                                <small>Track progress over time</small>
                                <i class="fas fa-arrow-right ms-2 transition-transform"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Forms Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="glass-card rounded-3 border border-white/20 backdrop-blur-xl p-4">
                    <!-- New Project Form -->
                    <div id="new-project-form" style="display: none;">
                        <h3 class="text-dark fw-bold mb-4">Create New Project</h3>
                        <form id="new-project-form-element" action="{{ route('analysis.run') }}" method="POST" onsubmit="return validateNewProjectForm(event)">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label text-gray-300">Project Name</label>
                                    <input type="text" name="name" id="new-project-name" class="form-control personal-info-input" 
                                           placeholder="My Awesome Website" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-gray-300">Website URL</label>
                                    <input type="url" name="base_url" id="new-project-url" class="form-control personal-info-input" 
                                           placeholder="https://example.com" required>
                                </div>
                                <div class="col-12 mt-3">
                                    <button type="submit" class="btn btn-primary me-3" id="new-project-submit-btn">
                                        <i class="fas fa-play-circle me-2"></i>Start SEO Analysis
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Existing Project Form -->
                    <div id="existing-project-form" style="display: none;">
                        <h3 class="text-gray-300 fw-bold mb-4">Select Existing Project</h3>

                        <!-- Projects List -->
                        <div id="projects-list"></div>

                        <!-- Loading State -->
                        <div id="projects-loading" class="text-center py-4" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="text-gray-300 mt-2">Loading your projects...</p>
                        </div>

                        <!-- URL Input -->
                        <div id="url-input-container" class="mt-3" style="display: none;">
                            <label class="form-label text-gray-300">URL to Analyze</label>
                            <input type="url" id="analysis-url" class="form-control personal-info-input" 
                                   placeholder="Enter URL to analyze">
                        </div>

                        <div class="mt-4">
                            <button id="run-analysis-btn" class="btn btn-success"
                                    onclick="startAnalysis()" disabled>
                                <i class="fas fa-chart-line me-2"></i>Run SEO Analysis
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading Modals -->
        <div class="modal fade" id="newProjectLoadingModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content glass-card border-0 overflow-hidden">
                    <div class="modal-body text-center p-5">
                        <div class="spinner-border text-primary mb-4" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        
                        <h4 class="text-dark fw-bold mb-3">SEO Analysis in Progress</h4>
                        <p class="text-gray-300 mb-4">We're analyzing your website's performance. This may take 1-2 minutes.</p>
                        
                        <div class="progress-steps mb-4">
                            <div class="step active" data-step="1">
                                <span class="step-icon">🔍</span>
                                <span class="step-text">Scanning Website</span>
                            </div>
                            <div class="step" data-step="2">
                                <span class="step-icon">⚡</span>
                                <span class="step-text">Performance Audit</span>
                            </div>
                            <div class="step" data-step="3">
                                <span class="step-icon">📊</span>
                                <span class="step-text">Generating Report</span>
                            </div>
                        </div>
                        
                        <div class="progress-container mb-3">
                            <div class="progress">
                                <div class="progress-bar progress-bar-animated" role="progressbar" style="width: 0%"></div>
                            </div>
                            <small class="text-muted progress-percentage">0%</small>
                        </div>
                        
                        <div class="time-estimate">
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>
                                Estimated time: <span id="newProjectTimeRemaining">1-2 minutes</span>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="analysisLoadingModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content glass-card border-0 overflow-hidden">
                    <div class="modal-body text-center p-5">
                        <div class="spinner-border text-primary mb-4" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        
                        <h4 class="text-dark fw-bold mb-3">SEO Analysis in Progress</h4>
                        <p class="text-gray-300 mb-4">We're analyzing your website's performance. This may take 1-2 minutes.</p>
                        
                        <div class="progress-steps mb-4">
                            <div class="step active" data-step="1">
                                <span class="step-icon">🔍</span>
                                <span class="step-text">Scanning Website</span>
                            </div>
                            <div class="step" data-step="2">
                                <span class="step-icon">⚡</span>
                                <span class="step-text">Performance Audit</span>
                            </div>
                            <div class="step" data-step="3">
                                <span class="step-icon">📊</span>
                                <span class="step-text">Generating Report</span>
                            </div>
                        </div>
                        
                        <div class="progress-container mb-3">
                            <div class="progress">
                                <div class="progress-bar progress-bar-animated" role="progressbar" style="width: 0%"></div>
                            </div>
                            <small class="text-muted progress-percentage">0%</small>
                        </div>
                        
                        <div class="time-estimate">
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>
                                Estimated time: <span id="timeRemaining">1-2 minutes</span>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Variables globales
let selectedOption = null;
let selectedProject = null;
let projects = [];

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    // Si vous voulez charger les projets au chargement de la page
    // fetchProjects();
});

function selectOption(option) {
    selectedOption = option;
    
    // Réinitialiser l'interface
    document.getElementById('new-project-form').style.display = 'none';
    document.getElementById('existing-project-form').style.display = 'none';
    document.getElementById('new-project-card').classList.remove('selected');
    document.getElementById('existing-project-card').classList.remove('selected');
    
    // Afficher le formulaire sélectionné
    if (option === 'new') {
        document.getElementById('new-project-form').style.display = 'block';
        document.getElementById('new-project-card').classList.add('selected');
    } else {
        document.getElementById('existing-project-form').style.display = 'block';
        document.getElementById('existing-project-card').classList.add('selected');
        fetchProjects();
    }
}

async function fetchProjects() {
    const projectsList = document.getElementById('projects-list');
    const loadingElement = document.getElementById('projects-loading');
    
    projectsList.innerHTML = '';
    loadingElement.style.display = 'block';
    
    try {
        const response = await fetch('/admin/projects/json');
        if (response.ok) {
            projects = await response.json();
            displayProjects(projects);
        } else {
            showError('Failed to load projects');
        }
    } catch (error) {
        console.error('Error loading projects:', error);
        showError('Network error: ' + error.message);
    } finally {
        loadingElement.style.display = 'none';
    }
}

function displayProjects(projects) {
    const projectsList = document.getElementById('projects-list');
    
    if (projects.length === 0) {
        projectsList.innerHTML = `
            <div class="text-center py-4">
                <i class="fas fa-folder-open fa-2x text-gray-300 mb-3"></i>
                <p class="text-gray-300">No projects found. Create a new project to get started.</p>
            </div>
        `;
        return;
    }
    
    projectsList.innerHTML = `
        <label class="form-label text-gray-300 d-flex justify-content-between align-items-center">
            <span>Your Projects <span class="badge bg-secondary ms-1">${projects.length}</span></span>
        </label>
        
        <div class="projects-scroll-container" id="projects-scroll-container" style="max-height: 400px; overflow-y: auto;">
            <div class="row g-3">
                ${projects.map((project, index) => `
                    <div class="col-lg-6 col-md-12">
                        <div class="glass-card rounded-2 border p-3 cursor-pointer transition-all project-card"
                             data-project-id="${project.id}"
                             onclick="selectProject(${project.id})"
                             title="Click to analyze: ${project.base_url}">
                            <div class="d-flex justify-content-between align-items-center h-100">
                                <div class="project-info flex-grow-1" style="min-width: 0;">
                                    <h6 class="text-dark mb-1 text-truncate">${project.name}</h6>
                                    <small class="text-gray-300 text-truncate d-block">${project.base_url}</small>
                                </div>
                                <span class="badge bg-white/10 text-dark ms-2 flex-shrink-0">
                                    ${project.seo_analyses_count || 0} analyses
                                </span>
                            </div>
                        </div>
                    </div>
                `).join('')}
            </div>
        </div>
    `;
}

function selectProject(projectId) {
    // Retirer la sélection de tous les projets
    document.querySelectorAll('.project-card').forEach(card => {
        card.classList.remove('selected');
    });
    
    // Sélectionner le projet cliqué
    const selectedCard = document.querySelector(`[data-project-id="${projectId}"]`);
    selectedCard.classList.add('selected');
    
    selectedProject = projects.find(p => p.id === projectId);
    
    // Afficher le champ URL et activer le bouton
    document.getElementById('url-input-container').style.display = 'block';
    document.getElementById('analysis-url').value = selectedProject.base_url;
    document.getElementById('run-analysis-btn').disabled = false;
}

function startAnalysis() {
    if (!selectedProject) {
        showError('Please select a project first');
        return;
    }
    
    const analysisUrl = document.getElementById('analysis-url').value;
    if (!analysisUrl) {
        showError('Please enter a URL to analyze');
        return;
    }
    
    // Afficher le modal de loading
    showLoadingModal();
    
    // Simulation de progression
    simulateProgress();
    
    // Créer un formulaire dynamique pour la soumission
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/analysis/run';
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    
    // Ajouter le token CSRF
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = csrfToken;
    form.appendChild(csrfInput);
    
    // Ajouter les données du projet
    const projectIdInput = document.createElement('input');
    projectIdInput.type = 'hidden';
    projectIdInput.name = 'project_id';
    projectIdInput.value = selectedProject.id;
    form.appendChild(projectIdInput);
    
    const urlInput = document.createElement('input');
    urlInput.type = 'hidden';
    urlInput.name = 'base_url';
    urlInput.value = analysisUrl;
    form.appendChild(urlInput);
    
    // Soumettre le formulaire après un petit délai
    setTimeout(() => {
        document.body.appendChild(form);
        form.submit();
    }, 2000);
}

function showLoadingModal() {
    const modal = new bootstrap.Modal(document.getElementById('analysisLoadingModal'));
    modal.show();
    
    // Réinitialiser la progression
    updateProgress(0);
    updateStep(1);
}

function simulateProgress() {
    let progress = 0;
    const interval = setInterval(() => {
        progress += Math.random() * 5;
        if (progress >= 100) {
            progress = 100;
            clearInterval(interval);
        }
        
        updateProgress(progress);
        
        // Mettre à jour les étapes en fonction de la progression
        if (progress >= 30 && progress < 70) {
            updateStep(2);
        } else if (progress >= 70) {
            updateStep(3);
        }
    }, 500);
}

function updateProgress(percent) {
    const progressBar = document.querySelector('#analysisLoadingModal .progress-bar');
    const percentage = document.querySelector('#analysisLoadingModal .progress-percentage');
    
    if (progressBar && percentage) {
        progressBar.style.width = percent + '%';
        percentage.textContent = Math.round(percent) + '%';
        
        // Mettre à jour le temps estimé
        const timeRemaining = document.getElementById('timeRemaining');
        if (timeRemaining) {
            if (percent < 30) {
                timeRemaining.textContent = '1-2 minutes';
            } else if (percent < 70) {
                timeRemaining.textContent = '30-60 seconds';
            } else {
                timeRemaining.textContent = '10-20 seconds';
            }
        }
    }
}

function updateStep(stepNumber) {
    const steps = document.querySelectorAll('#analysisLoadingModal .step');
    
    steps.forEach((step, index) => {
        if (index + 1 <= stepNumber) {
            step.classList.add('active');
        } else {
            step.classList.remove('active');
        }
    });
}

// Function to validate and submit new project form
function validateNewProjectForm(event) {
    event.preventDefault();
    
    const projectName = document.getElementById('new-project-name').value;
    const projectUrl = document.getElementById('new-project-url').value;
    const submitBtn = document.getElementById('new-project-submit-btn');
    
    if (!projectName || !projectUrl) {
        showError('Please fill in all fields');
        return false;
    }
    
    // Validate URL format
    if (!isValidUrl(projectUrl)) {
        showError('Please enter a valid URL (e.g., https://example.com)');
        return false;
    }
    
    // Show loading modal
    showNewProjectLoadingModal();
    
    // Disable submit button to prevent multiple clicks
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Starting Analysis...';
    
    // Simulate progress
    simulateNewProjectProgress();
    
    // Submit the form after a delay to show the loading animation
    setTimeout(() => {
        document.getElementById('new-project-form-element').submit();
    }, 3000);
    
    return false;
}

function showNewProjectLoadingModal() {
    const modal = new bootstrap.Modal(document.getElementById('newProjectLoadingModal'));
    modal.show();
    
    // Reset progress
    updateNewProjectProgress(0);
    updateNewProjectStep(1);
}

function simulateNewProjectProgress() {
    let progress = 0;
    const interval = setInterval(() => {
        progress += Math.random() * 8;
        if (progress >= 100) {
            progress = 100;
            clearInterval(interval);
        }
        
        updateNewProjectProgress(progress);
        
        // Update steps based on progress
        if (progress >= 30 && progress < 70) {
            updateNewProjectStep(2);
        } else if (progress >= 70) {
            updateNewProjectStep(3);
        }
    }, 300);
}

function updateNewProjectProgress(percent) {
    const progressBar = document.querySelector('#newProjectLoadingModal .progress-bar');
    const percentage = document.querySelector('#newProjectLoadingModal .progress-percentage');
    
    if (progressBar && percentage) {
        progressBar.style.width = percent + '%';
        percentage.textContent = Math.round(percent) + '%';
        
        // Update estimated time
        const timeRemaining = document.getElementById('newProjectTimeRemaining');
        if (timeRemaining) {
            if (percent < 30) {
                timeRemaining.textContent = '1-2 minutes';
            } else if (percent < 70) {
                timeRemaining.textContent = '30-60 seconds';
            } else {
                timeRemaining.textContent = '10-20 seconds';
            }
        }
    }
}

function updateNewProjectStep(stepNumber) {
    const steps = document.querySelectorAll('#newProjectLoadingModal .step');
    
    steps.forEach((step, index) => {
        if (index + 1 <= stepNumber) {
            step.classList.add('active');
        } else {
            step.classList.remove('active');
        }
    });
}

function isValidUrl(string) {
    try {
        const url = new URL(string);
        return url.protocol === 'http:' || url.protocol === 'https:';
    } catch (_) {
        return false;
    }
}

// Function to show error messages
function showError(message) {
    // You can use a toast notification instead of alert
    const toast = document.createElement('div');
    toast.className = 'alert alert-danger alert-dismissible fade show position-fixed';
    toast.style.top = '20px';
    toast.style.right = '20px';
    toast.style.zIndex = '9999';
    toast.innerHTML = `
        <strong>Error!</strong> ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(toast);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (toast.parentElement) {
            toast.remove();
        }
    }, 5000);
}

</script>

<style>
.glass-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.glass-card.selected {
    background: rgba(255, 255, 255, 0.15) !important;
    backdrop-filter: blur(20px) !important;
    border-color: rgba(59, 130, 246, 0.5) !important;
}

.cursor-pointer {
    cursor: pointer;
}

.transition-all {
    transition: all 0.3s ease;
}

.project-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

.progress-steps {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 20px 0;
}

.step {
    display: flex;
    flex-direction: column;
    align-items: center;
    flex: 1;
    opacity: 0.5;
    transition: all 0.3s ease;
}

.step.active {
    opacity: 1;
}

.step-icon {
    font-size: 1.5rem;
    margin-bottom: 5px;
}

.step-text {
    font-size: 0.8rem;
    text-align: center;
}

.progress-container {
    position: relative;
}

.progress-bar-animated {
    transition: width 0.6s ease;
}
</style>

@endsection