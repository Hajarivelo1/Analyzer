var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
})



// Enhanced card hover effects with glassmorphism (without movement)
document.querySelectorAll('.glass-card').forEach(card => {
    card.addEventListener('mouseenter', function() {
        this.style.backdropFilter = 'blur(25px)';
        this.style.background = 'rgba(255, 255, 255, 0.85)';
        this.style.transition = 'all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
    });
    
    card.addEventListener('mouseleave', function() {
        this.style.backdropFilter = 'blur(20px)';
        this.style.background = 'rgba(255, 255, 255, 0.7)';
    });
});

// Enhanced navigation item effects (keep movement for sidebar nav items only)
document.querySelectorAll('.glass-nav-item').forEach(navItem => {
    navItem.addEventListener('mouseenter', function() {
        if (!this.classList.contains('active')) {
            this.style.background = 'rgba(255, 255, 255, 0.15)';
            this.style.transform = 'translateX(8px)';
        }
    });
    
    navItem.addEventListener('mouseleave', function() {
        if (!this.classList.contains('active')) {
            this.style.background = 'rgba(255, 255, 255, 0.08)';
            this.style.transform = 'translateX(0)';
        }
    });
});

// Enhanced button effects (remove movement)
document.querySelectorAll('.glass-btn, .glass-primary-btn, .glass-outline-btn').forEach(btn => {
    btn.addEventListener('mouseenter', function() {
        this.style.boxShadow = '0 10px 25px rgba(0, 0, 0, 0.15)';
    });
    
    btn.addEventListener('mouseleave', function() {
        this.style.boxShadow = 'none';
    });
});

// Mobile sidebar toggle with enhanced animation
const sidebar = document.getElementById('sidebarMenu');
const toggleBtn = document.querySelector('[data-bs-toggle="collapse"]');

toggleBtn.addEventListener('click', function() {
    sidebar.classList.toggle('show');
    
    // Add overlay when sidebar is open on mobile
    if (sidebar.classList.contains('show')) {
        const overlay = document.createElement('div');
        overlay.className = 'glass-overlay';
        overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.3);
            z-index: 999;
            transition: opacity 0.3s ease;
        `;
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('show');
            this.remove();
        });
        document.body.appendChild(overlay);
    } else {
        const overlay = document.querySelector('.glass-overlay');
        if (overlay) overlay.remove();
    }
});

// Simulate loading state for new analysis button with enhanced glass effect
document.querySelector('.glass-primary-btn').addEventListener('click', function(e) {
    e.preventDefault();
    const originalText = this.innerHTML;
    
    // Enhanced loading state
    this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Starting Analysis...';
    this.disabled = true;
    this.style.background = 'rgba(37, 99, 235, 0.6)';
    this.style.backdropFilter = 'blur(15px)';
    
    setTimeout(() => {
        this.innerHTML = originalText;
        this.disabled = false;
        this.style.background = 'linear-gradient(135deg, rgba(37, 99, 235, 0.8), rgba(29, 78, 216, 0.8))';
        
        // Enhanced success notification with glass effect
        const alert = document.createElement('div');
        alert.className = 'alert glass-alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3';
        alert.style.cssText = `
            background: rgba(16, 185, 129, 0.9);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 0.75rem;
            color: white;
            z-index: 1050;
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
        `;
        alert.innerHTML = `
            <i class="bi bi-check-circle me-2"></i>
            Analysis started successfully!
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alert);
        
        setTimeout(() => {
            if (alert.parentNode) {
                alert.parentNode.removeChild(alert);
            }
        }, 3000);
    }, 1500);
});

// Add subtle background animation
function createFloatingShapes() {
    const shapesContainer = document.createElement('div');
    shapesContainer.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: -1;
        overflow: hidden;
    `;
    
    for (let i = 0; i < 5; i++) {
        const shape = document.createElement('div');
        shape.style.cssText = `
            position: absolute;
            width: ${100 + Math.random() * 200}px;
            height: ${100 + Math.random() * 200}px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            filter: blur(40px);
            animation: float ${15 + Math.random() * 10}s infinite ease-in-out;
        `;
        shape.style.left = `${Math.random() * 100}%`;
        shape.style.top = `${Math.random() * 100}%`;
        shapesContainer.appendChild(shape);
    }
    
    document.body.appendChild(shapesContainer);
}

// Add floating animation
const style = document.createElement('style');
style.textContent = `
    @keyframes float {
        0%, 100% { transform: translate(0, 0) rotate(0deg); }
        25% { transform: translate(20px, -20px) rotate(5deg); }
        50% { transform: translate(-15px, 15px) rotate(-5deg); }
        75% { transform: translate(10px, 10px) rotate(3deg); }
    }
`;
document.head.appendChild(style);

// Initialize floating shapes
createFloatingShapes();




