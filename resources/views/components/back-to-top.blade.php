{{-- Back to Top Button Component --}}
<button id="backToTop" class="back-to-top-btn" aria-label="Back to top" title="Back to top">
    <i class="bi bi-chevron-up"></i>
</button>

<style>
.back-to-top-btn {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 56px;
    height: 56px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    cursor: pointer;
    box-shadow: 0 4px 20px rgba(102, 126, 234, 0.3);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    opacity: 0;
    visibility: hidden;
    transform: translateY(20px);
    z-index: 1000;
}

.back-to-top-btn.visible {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.back-to-top-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
    background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
}

.back-to-top-btn:active {
    transform: translateY(-1px);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.5);
}

.back-to-top-btn::before {
    content: '';
    position: absolute;
    top: -2px;
    left: -2px;
    right: -2px;
    bottom: -2px;
    background: linear-gradient(135deg, #667eea, #764ba2, #667eea);
    border-radius: 50%;
    z-index: -1;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.back-to-top-btn:hover::before {
    opacity: 1;
}

.back-to-top-btn i {
    transition: transform 0.3s ease;
}

.back-to-top-btn:hover i {
    transform: translateY(-2px);
}

/* Animation d'apparition */
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

.back-to-top-btn.visible {
    animation: fadeInUp 0.5s ease-out;
}

/* Responsive Design */
@media (max-width: 768px) {
    .back-to-top-btn {
        bottom: 20px;
        right: 20px;
        width: 50px;
        height: 50px;
        font-size: 1.1rem;
    }
}

@media (max-width: 576px) {
    .back-to-top-btn {
        bottom: 15px;
        right: 15px;
        width: 46px;
        height: 46px;
        font-size: 1rem;
    }
}

/* Pour les écrans très larges */
@media (min-width: 1400px) {
    .back-to-top-btn {
        bottom: 40px;
        right: 40px;
    }
}

/* Mode sombre support */
@media (prefers-color-scheme: dark) {
    .back-to-top-btn {
        box-shadow: 0 4px 20px rgba(102, 126, 234, 0.5);
    }
    
    .back-to-top-btn:hover {
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.6);
    }
}

/* Accessibilité : focus visible */
.back-to-top-btn:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.3);
}

/* État loading (optionnel) */
.back-to-top-btn.loading {
    pointer-events: none;
    opacity: 0.7;
}

.back-to-top-btn.loading i {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const backToTopBtn = document.getElementById('backToTop');
    const scrollThreshold = 300;
    let scrollTimeout;

    function toggleBackToTop() {
        if (window.pageYOffset > scrollThreshold) {
            backToTopBtn.classList.add('visible');
        } else {
            backToTopBtn.classList.remove('visible');
        }
    }

    function scrollToTop() {
        backToTopBtn.classList.add('loading');
        
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });

        setTimeout(() => {
            backToTopBtn.classList.remove('loading');
        }, 1000);
    }

    window.addEventListener('scroll', function() {
        clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(toggleBackToTop, 10);
    });

    backToTopBtn.addEventListener('click', scrollToTop);

    toggleBackToTop();

    // Keyboard navigation
    backToTopBtn.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            scrollToTop();
        }
    });

    // Touch device optimization
    let touchStartY = 0;
    
    backToTopBtn.addEventListener('touchstart', function(e) {
        touchStartY = e.touches[0].clientY;
    });

    backToTopBtn.addEventListener('touchend', function(e) {
        const touchEndY = e.changedTouches[0].clientY;
        if (Math.abs(touchEndY - touchStartY) < 10) {
            scrollToTop();
        }
    });
});
</script>