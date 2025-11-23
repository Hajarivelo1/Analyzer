@extends('admin.admin_master')
@section('admin')

<div class="min-h-screen py-4" id="analysis-app">
    <div class="container-fluid">
        <!-- Header Section -->
        <div class="project-header-card mb-5">
            <div class="project-header">
                <div class="header-content">
                    <div class="header-icon">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <div>
                        <h1 class="project-title">New SEO Analysis</h1>
                        <p class="project-subtitle">AI-Powered Website Performance Insights</p>
                    </div>
                </div>
                <div class="project-badge">
                    <i class="bi bi-robot"></i>
                    <span>AI Powered Analysis</span>
                </div>
            </div>
        </div>

        <!-- Project Selection Cards -->
        <div class="selection-grid mb-5">
            <div class="selection-card" id="new-project-card" onclick="selectOption('new')">
                <div class="card-icon">
                    <i class="bi bi-plus-circle"></i>
                </div>
                <div class="card-content">
                    <h3>New Project</h3>
                    <p>Start fresh with a comprehensive website analysis</p>
                    <div class="card-footer">
                        <span>Perfect for new websites</span>
                        <i class="bi bi-arrow-right"></i>
                    </div>
                </div>
            </div>
            
            <div class="selection-card" id="existing-project-card" onclick="selectOption('existing')">
                <div class="card-icon">
                    <i class="bi bi-folder"></i>
                </div>
                <div class="card-content">
                    <h3>Existing Project</h3>
                    <p>Add analysis to track progress over time</p>
                    <div class="card-footer">
                        <span>Monitor improvements</span>
                        <i class="bi bi-arrow-right"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Forms Section -->
        <div class="forms-container">
            <div class="analysis-card">
                <!-- New Project Form -->
                <div id="new-project-form" class="form-section" style="display: none;">
                    <div class="form-header">
                        <i class="bi bi-plus-circle"></i>
                        <h2>Create New Project</h2>
                    </div>
                    <form id="new-project-form-element" action="{{ route('analysis.run') }}" method="POST" onsubmit="return validateNewProjectForm(event)">
                        @csrf
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">Project Name</label>
                                <input type="text" name="name" id="new-project-name" class="form-input" 
                                       placeholder="My Awesome Website" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Website URL</label>
                                <input type="url" name="base_url" id="new-project-url" class="form-input" 
                                       placeholder="https://example.com" required>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn-primary" id="new-project-submit-btn">
                                <i class="bi bi-play-circle"></i>
                                Start SEO Analysis
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Existing Project Form -->
                <div id="existing-project-form" class="form-section" style="display: none;">
                    <div class="form-header">
                        <i class="bi bi-folder"></i>
                        <h2>Select Existing Project</h2>
                    </div>

                    <!-- Projects List -->
                    <div id="projects-list"></div>

                    <!-- Loading State -->
                    <div id="projects-loading" class="loading-state" style="display: none;">
                        <div class="spinner"></div>
                        <p>Loading your projects...</p>
                    </div>

                    <!-- URL Input -->
                    <div id="url-input-container" class="url-input-section" style="display: none;">
                        <label class="form-label">URL to Analyze</label>
                        <input type="url" id="analysis-url" class="form-input w-100" 
                               placeholder="Enter URL to analyze">
                    </div>

                    <div class="form-actions">
                        <button id="run-analysis-btn" class="btn-success"
                                onclick="startAnalysis()" disabled>
                            <i class="bi bi-graph-up"></i>
                            Run SEO Analysis
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading Modals (Conservés tels quels) -->
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

<style>
    .project-card.selected .text-truncate,
.project-card.selected small,
.project-card.selected .badge {
    color: white !important;
}
/* Styles pour le nouveau design professionnel */
.project-header-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    color: white;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    margin-bottom: 2rem;
}

.project-header {
    padding: 2.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
}

.project-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 200px;
    height: 200px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
}

.header-content {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.header-icon {
    font-size: 3rem;
    opacity: 0.9;
}

.project-title {
    font-weight: 700;
    font-size: 2rem;
    margin: 0;
}

.project-subtitle {
    opacity: 0.9;
    font-size: 1.1rem;
    margin: 0.5rem 0 0 0;
}

.project-badge {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    background: rgba(255, 255, 255, 0.2);
    padding: 1rem 1.5rem;
    border-radius: 15px;
    backdrop-filter: blur(10px);
    font-weight: 500;
}

/* Selection Grid */
.selection-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.selection-card {
    background: white;
    border-radius: 20px;
    padding: 2rem;
    box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.6);
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: flex-start;
    gap: 1.5rem;
}

.selection-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
}

.selection-card.selected {
    border-color: #667eea;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
}

.card-icon {
    font-size: 2.5rem;
    padding: 1rem;
    border-radius: 15px;
    flex-shrink: 0;
}

#new-project-card .card-icon {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

#existing-project-card .card-icon {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
}

.card-content {
    flex: 1;
}

.card-content h3 {
    font-weight: 700;
    color: #1f2937;
    margin: 0 0 0.5rem 0;
    font-size: 1.5rem;
}

.card-content p {
    color: #6b7280;
    margin: 0 0 1.5rem 0;
    line-height: 1.5;
}

.card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    color: #667eea;
    font-weight: 600;
    font-size: 0.9rem;
}

/* Forms Container */
.forms-container {
    margin-bottom: 2rem;
}

.analysis-card {
    background: white;
    border-radius: 20px;
    padding: 2rem;
    box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.6);
}

.form-section {
    animation: fadeInUp 0.6s ease-out;
}

.form-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 2px solid #f1f5f9;
}

.form-header i {
    font-size: 2rem;
    color: #667eea;
}

.form-header h2 {
    font-weight: 700;
    color: #1f2937;
    margin: 0;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-label {
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
}

.form-input {
    padding: 1rem 1.25rem;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: #f8fafc;
}

.form-input:focus {
    outline: none;
    border-color: #667eea;
    background: white;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}

.btn-primary, .btn-success {
    padding: 1rem 2rem;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
}

.btn-success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
}

.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(16, 185, 129, 0.4);
}

.btn-primary:disabled, .btn-success:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

/* Projects List */
.projects-scroll-container {
    max-height: 400px;
    overflow-y: auto;
    margin-bottom: 1.5rem;
}

.project-card {
    background: #f8fafc;
    border-radius: 12px;
    padding: 1.5rem;
    border: 1px solid #e2e8f0;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-bottom: 1rem;
}

.project-card:hover {
    background: #f1f5f9;
    transform: translateX(5px);
}

.project-card.selected {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-color: #667eea;
}

.project-card.selected .project-info h6,
.project-card.selected .project-info small {
    color: white;
}

.project-info h6 {
    font-weight: 600;
    color: #1f2937;
    margin: 0 0 0.25rem 0;
}

.project-info small {
    color: #6b7280;
    font-size: 0.85rem;
}

/* Loading State */
.loading-state {
    text-align: center;
    padding: 3rem 2rem;
    color: #9ca3af;
}

.spinner {
    width: 3rem;
    height: 3rem;
    border: 3px solid #f1f5f9;
    border-top: 3px solid #667eea;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 1rem;
}

.url-input-section {
    margin-top: 1.5rem;
}

/* Animations */
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

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Responsive Design */
@media (max-width: 768px) {
    .project-header {
        flex-direction: column;
        gap: 1.5rem;
        text-align: center;
    }
    
    .header-content {
        flex-direction: column;
        text-align: center;
    }
    
    .selection-grid {
        grid-template-columns: 1fr;
    }
    
    .selection-card {
        flex-direction: column;
        text-align: center;
    }
    
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .form-actions {
        flex-direction: column;
    }
}

/* Styles pour les modals existants (conservés) */
.glass-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
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

<script>
// Variables globales
let selectedOption = null;
let selectedProject = null;
let projects = [];

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    console.log('SEO Analysis page loaded');
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
                <i class="bi bi-folder-open" style="font-size: 2rem; color: #9ca3af; margin-bottom: 1rem;"></i>
                <p class="text-gray-300">No projects found. Create a new project to get started.</p>
            </div>
        `;
        return;
    }
    
    projectsList.innerHTML = `
        <label class="form-label d-flex justify-content-between align-items-center">
            <span>Your Projects <span class="badge bg-secondary ms-1">${projects.length}</span></span>
        </label>
        
        <div class="projects-scroll-container" id="projects-scroll-container">
            <div class="row g-3">
                ${projects.map((project, index) => `
                    <div class="col-lg-6 col-md-12">
                        <div class="project-card"
                             data-project-id="${project.id}"
                             onclick="selectProject(${project.id})"
                             title="Click to analyze: ${project.base_url}">
                            <div class="d-flex justify-content-between align-items-center h-100">
                                <div class="project-info flex-grow-1" style="min-width: 0;">
                                    <h6 class="mb-1 text-truncate">${project.name}</h6>
                                    <small class="text-truncate d-block">${project.base_url}</small>
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
    if (selectedCard) {
        selectedCard.classList.add('selected');
    }
    
    selectedProject = projects.find(p => p.id === projectId);
    
    // Afficher le champ URL et activer le bouton
    document.getElementById('url-input-container').style.display = 'block';
    document.getElementById('analysis-url').value = selectedProject?.base_url || '';
    document.getElementById('run-analysis-btn').disabled = !selectedProject;
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
    submitBtn.innerHTML = '<i class="bi bi-spinner fa-spin me-2"></i>Starting Analysis...';
    
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

@endsection