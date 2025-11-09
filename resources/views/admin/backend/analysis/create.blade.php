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
                <div class="glass-card rounded-3 border border-white/20 backdrop-blur-xl p-4" >
                    <!-- New Project Form -->
                    <div id="new-project-form" style="display: none;">
                        <h3 class="text-dark fw-bold mb-4">Create New Project</h3>
                        <form id="new-project-form-element" action="{{ route('analysis.run') }}" method="POST">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label text-gray-300">Project Name</label>
                                    <input type="text" name="name" class="form-control personal-info-input" 
                                           placeholder="My Awesome Website" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-gray-300">Website URL</label>
                                    <input type="url" name="base_url" class="form-control personal-info-input" 
                                           placeholder="https://example.com" required>
                                </div>
                                <div class="col-12 mt-3">
                                    <button type="submit" class="btn btn-primary me-3">
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
                            <button id="run-analysis-btn" class="btn btn-success glass-success-btn"
                                    onclick="startAnalysis()" disabled>
                                <i class="fas fa-chart-line me-2"></i>Run SEO Analysis
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Analysis Results (optionnel si vous voulez garder l'affichage AJAX) -->
        <div id="analysis-results" style="display: none;">
            <!-- Contenu des résultats -->
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
    document.getElementById('new-project-card').classList.remove('glass-card-selected', 'border-blue-400/50');
    document.getElementById('existing-project-card').classList.remove('glass-card-selected', 'border-green-400/50');
    
    // Afficher le formulaire sélectionné
    if (option === 'new') {
        document.getElementById('new-project-form').style.display = 'block';
        document.getElementById('new-project-card').classList.add('glass-card-selected', 'border-blue-400/50');
    } else {
        document.getElementById('existing-project-form').style.display = 'block';
        document.getElementById('existing-project-card').classList.add('glass-card-selected', 'border-green-400/50');
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
        <label class="form-label text-gray-300">Your Projects</label>
        <div class="row g-3">
            ${projects.map(project => `
                <div class="col-lg-4 col-md-6">
                    <div class="glass-card rounded-2 border p-3 cursor-pointer transition-all project-card"
                         data-project-id="${project.id}"
                         onclick="selectProject(${project.id})">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-dark mb-1">${project.name}</h6>
                                <small class="text-gray-300">${project.base_url}</small>
                            </div>
                            <span class="badge bg-white/10 text-white">${project.seo_analyses_count} analyses</span>
                        </div>
                    </div>
                </div>
            `).join('')}
        </div>
    `;
}

function selectProject(projectId) {
    // Retirer la sélection de tous les projets
    document.querySelectorAll('.project-card').forEach(card => {
        card.classList.remove('glass-card-selected', 'border-blue-400/50');
    });
    
    // Sélectionner le projet cliqué
    const selectedCard = document.querySelector(`[data-project-id="${projectId}"]`);
    selectedCard.classList.add('glass-card-selected', 'border-blue-400/50');
    
    selectedProject = projects.find(p => p.id === projectId);
    
    // Afficher le champ URL et activer le bouton
    document.getElementById('url-input-container').style.display = 'block';
    document.getElementById('analysis-url').value = selectedProject.base_url;
    document.getElementById('run-analysis-btn').disabled = false;
}

function startAnalysis() {
    if (!selectedProject) {
        alert('Please select a project first');
        return;
    }
    
    const analysisUrl = document.getElementById('analysis-url').value;
    if (!analysisUrl) {
        alert('Please enter a URL to analyze');
        return;
    }
    
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
    
    // Soumettre le formulaire
    document.body.appendChild(form);
    form.submit();
}

function showError(message) {
    // Vous pouvez utiliser un toast ou une alerte simple
    alert('Error: ' + message);
}

// Gestion de la soumission du formulaire nouveau projet (pas besoin de JS supplémentaire)
document.getElementById('new-project-form-element')?.addEventListener('submit', function(e) {
    // Validation supplémentaire si nécessaire
    const name = this.querySelector('input[name="name"]').value;
    const url = this.querySelector('input[name="base_url"]').value;
    
    if (!name || !url) {
        e.preventDefault();
        alert('Please fill in all fields');
        return;
    }
});
</script>

<style>
.glass-card-selected {
    background: rgba(255, 255, 255, 0.15) !important;
    backdrop-filter: blur(20px) !important;
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
</style>

@endsection
