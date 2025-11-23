// public/js/projects.js

class ProjectsManager {
    static init() {
        this.initDeleteHandlers();
        this.initCardAnimations();
        this.initFlashMessages();
    }

    // Gestion des suppressions avec SweetAlert2
    static initDeleteHandlers() {
        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                ProjectsManager.showDeleteConfirmation(form);
            });
        });
    }

    // Confirmation de suppression
    static showDeleteConfirmation(form) {
        if (typeof Swal === 'undefined') {
            console.warn('SweetAlert2 non chargé, suppression directe');
            form.submit();
            return;
        }

        Swal.fire({
            title: 'Delete Project?',
            text: "This action cannot be undone. All project data will be permanently deleted.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel',
            background: '#ffffff',
            backdrop: 'rgba(0, 0, 0, 0.1)'
        }).then((result) => {
            if (result.isConfirmed) {
                // Nettoyer le cache local avant suppression
                ProjectsManager.clearLocalCache();
                form.submit();
            }
        });
    }

    // Animation des cartes
    static initCardAnimations() {
        const cards = document.querySelectorAll('.project-item-card');
        cards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.1}s`;
            card.classList.add('project-card-animate-in');
        });
    }

    // Gestion des messages flash
    static initFlashMessages() {
        // Vérifier si SweetAlert2 est disponible et s'il y a un message flash
        if (typeof Swal !== 'undefined' && window.flashMessage) {
            const { type, message } = window.flashMessage;
            
            Swal.fire({
                icon: type || 'success',
                title: message,
                showConfirmButton: false,
                timer: 2000,
                background: '#ffffff',
                backdrop: 'rgba(0, 0, 0, 0.1)'
            });
        }
    }

    // Nettoyage du cache local
    static clearLocalCache() {
        const cacheKeys = [
            'cached_projects_data',
            'cached_projects_json',
            'user_projects_data'
        ];
        
        cacheKeys.forEach(key => {
            localStorage.removeItem(key);
        });
        
        console.log('🗑️ Local cache cleared');
    }

    // Préchargement des assets (bonus)
    static async preloadAssets() {
        if ('caches' in window) {
            try {
                const cache = await caches.open('projects-assets-v1');
                const assets = [
                    '/css/projects.css',
                    '/js/projects.js'
                ];
                
                await cache.addAll(assets);
                console.log('📦 Projects assets pre-cached');
            } catch (error) {
                console.log('⚠️ Cache preload failed:', error);
            }
        }
    }
}

// Initialisation quand le DOM est prêt
document.addEventListener('DOMContentLoaded', function() {
    ProjectsManager.init();
    
    // Préchargement en arrière-plan (optionnel)
    setTimeout(() => {
        ProjectsManager.preloadAssets();
    }, 1000);
});

// Gestion des erreurs globales
window.addEventListener('error', function(e) {
    console.error('Global error in projects.js:', e.error);
});