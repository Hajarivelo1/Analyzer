<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SEOAnalyzer Pro - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" media="print" onload="this.media='all'">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" >
    <link rel="stylesheet" href="{{ mix('css/show.css') }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<style>
    html, body {
    height: 100%;
    min-height: 100vh;
}
main {
    min-height: 100vh;
}



body {
    
     margin: 0;
     padding: 0;
     font-family: 'Inter', sans-serif;
    background-color: #8788c6;
    color: #1f2937 !important;
    
    display: flex;
    flex-direction: column;
    height: 100vh;
    
   
    
    
   
}
nav {
    height: auto;
    min-height: 100vh;
}


.glass-sidebar {
    min-height: 500px;
    
    background-color: #1e293b;
    
    border-right: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 5px 0 15px rgba(0, 0, 0, 0.2);
}

.glass-nav-item {
    color: #f8fafc;
    padding: 0.75rem 1rem;
    border-radius: 0.5rem;
    margin-bottom: 0.25rem;
    background: rgba(255, 255, 255, 0.08);
   
    border: 1px solid rgba(255, 255, 255, 0.1);
    transition: opacity 0.3s ease; /* ✅ Remplacer par spécifique */
}

.glass-nav-item.active, .glass-nav-item:hover {
    background: rgba(37, 99, 235, 0.7);
    color: white;
    
    box-shadow: 0 5px 15px rgba(37, 99, 235, 0.4);
}

.glass-user-area {
    background: rgba(30, 41, 59, 0.8);
    
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.glass-background {
    background: rgba(248, 250, 252, 0.7);
    
}

.glass-header {
    background: rgba(255, 255, 255, 0.9);
    
    border-bottom: 1px solid rgba(255, 255, 255, 0.3);
    position: relative; /* Ajoutez cette ligne */
    z-index: 2040;
}

.glass-search {
    background: rgba(255, 255, 255, 0.6);
    
    border-radius: 0.5rem;
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.glass-input-icon {
    background: transparent;
    border: none;
    color: #6b7280;
}

.glass-input {
    background: transparent;
    border: none;
    color: #1f2937;
}

.glass-input:focus {
    background: transparent;
    border: none;
    box-shadow: none;
}

.glass-btn {
    background: rgba(255, 255, 255, 0.6);
   
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 0.5rem;
    color: #1f2937;
    transition: opacity 0.3s ease, transform 0.3s ease;
}

.glass-btn:hover {
    background: rgba(255, 255, 255, 0.8);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}


.glass-dropdown {
    background: #283548;
    
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 0.5rem;
    z-index: 2050 !important; /* Augmentez le z-index */
}
.dropdown {
    position: relative;
    border: 1px solid rgba(255, 255, 255, 0.3);
    
    z-index: 2060 !important; /* Assurez-vous que le conteneur dropdown a un z-index élevé */
}
.dropdown-item{
    color:white;
}
.dropdown-divider{
    color: white !important;
}

.glass-card {
    background: rgba(255, 255, 255, 0.7);
    
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 1rem;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    transition: box-shadow 0.3s ease; /* ✅ Remplacer par spécifique */
}

.glass-card:hover {
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
}

.glass-card-header {
    background: rgba(255, 255, 255, 0.5);
    
    border-bottom: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 1rem 1rem 0 0 !important;
}

.glass-primary-btn {
    background: linear-gradient(135deg, rgba(37, 99, 235, 0.8), rgba(29, 78, 216, 0.8));
    
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 0.5rem;
    color: white;
    padding: 0.75rem 1.5rem;
   
}

.glass-primary-btn:hover {
    background: linear-gradient(135deg, rgba(37, 99, 235, 0.9), rgba(29, 78, 216, 0.9));
    box-shadow: 0 8px 25px rgba(37, 99, 235, 0.4);
    color: white;
}

.glass-stat-card {
    background: rgba(255, 255, 255, 0.5);
    
    border: 1px solid rgba(77, 72, 72, 0.3);
    border-radius: 0.75rem;
    
}

.glass-stat-card:hover {
    background: rgba(255, 255, 255, 0.7);
}

.glass-icon-bg {
    background: linear-gradient(135deg, rgba(37, 99, 235, 0.8), rgba(29, 78, 216, 0.8));
   
    box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
}

.glass-table {
    background: rgba(255, 255, 255, 0.5);
   
    border-radius: 0.5rem;
}

.glass-table thead th {
    background: rgba(255, 255, 255, 0.6);
   
    border-bottom: 1px solid rgba(255, 255, 255, 0.3);
}

.glass-table tbody tr {
    background: rgba(255, 255, 255, 0.3);
    
}

.glass-table tbody tr:hover {
    background: rgba(255, 255, 255, 0.6);
}

.glass-badge-success {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.8), rgba(5, 150, 105, 0.8));
    
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.glass-badge-warning {
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.8), rgba(217, 119, 6, 0.8));
    
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.glass-outline-btn {
    background: rgba(255, 255, 255, 0.3);
    
    border: 1px solid rgba(37, 99, 235, 0.5);
    color: #2563eb;
    border-radius: 0.375rem;
    
}

.glass-outline-btn:hover {
    background: rgba(37, 99, 235, 0.1);
    box-shadow: 0 3px 10px rgba(37, 99, 235, 0.2);
}

.glass-progress {
    background: rgba(255, 255, 255, 0.3);
    
    border-radius: 0.5rem;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.glass-progress-bar {
    background: linear-gradient(135deg, rgba(37, 99, 235, 0.8), rgba(29, 78, 216, 0.8));
    
    border-radius: 0.5rem;
}

.glass-action-btn {
    background: rgba(255, 255, 255, 0.5);
    
    border: 1px solid rgba(102, 99, 99, 0.3);
    border-radius: 0.75rem;
   
}

.glass-action-btn:hover {
    background: rgba(255, 255, 255, 0.8);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.bg-primary { background: linear-gradient(135deg, rgba(37, 99, 235, 0.8), rgba(29, 78, 216, 0.8)) !important; }
.bg-success { background: linear-gradient(135deg, rgba(16, 185, 129, 0.8), rgba(5, 150, 105, 0.8)) !important; }
.bg-warning { background: linear-gradient(135deg, rgba(245, 158, 11, 0.8), rgba(217, 119, 6, 0.8)) !important; }
.bg-danger { background: linear-gradient(135deg, rgba(239, 68, 68, 0.8), rgba(220, 38, 38, 0.8)) !important; }

.text-primary { color: #2563eb !important; }
.text-success { color: #10b981 !important; }
.text-warning { color: #f59e0b !important; }
.text-danger { color: #ef4444 !important; }

.table-hover tbody tr:hover {
    background-color: rgba(37, 99, 235, 0.1);
}


/* Enhanced Personal Information Styling */
.personal-info-card {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.8), rgba(248, 250, 252, 0.9));
    border: 1px solid rgba(255, 255, 255, 0.5);
    box-shadow: 0 12px 40px rgba(37, 99, 235, 0.15);
    transition: box-shadow 0.3s ease; /* ✅ Simplifier */
}

.personal-info-card:hover {
    
    box-shadow: 0 20px 50px rgba(37, 99, 235, 0.2);
}

.personal-info-header {
    background: linear-gradient(135deg, rgba(37, 99, 235, 0.1), rgba(29, 78, 216, 0.15));
    border-bottom: 1px solid rgba(37, 99, 235, 0.2);
    padding: 1.25rem 1.5rem;
}

.personal-info-header h5 {
    color: #1e40af;
    font-weight: 600;
}

.personal-info-body {
    padding: 2rem;
}

.personal-info-input {
    background: rgba(255, 255, 255, 0.9);
    border: 1px solid rgba(37, 99, 235, 0.2);
    border-radius: 0.75rem;
    padding: 0.75rem 1rem;
    transition: box-shadow 0.3s ease;
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.05);
}

.personal-info-input:focus {
    background: rgba(255, 255, 255, 0.95);
    border-color: rgba(37, 99, 235, 0.5);
    box-shadow: 0 4px 15px rgba(37, 99, 235, 0.1);
    transform: translateY(-2px);
}

.personal-info-textarea {
    background: rgba(255, 255, 255, 0.9);
    border: 1px solid rgba(37, 99, 235, 0.2);
    border-radius: 0.75rem;
    padding: 0.75rem 1rem;
    transition: box-shadow 0.3s ease;
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.05);
    resize: vertical;
    min-height: 100px;
}

.personal-info-textarea:focus {
    background: rgba(255, 255, 255, 0.95);
    border-color: rgba(37, 99, 235, 0.5);
    box-shadow: 0 4px 15px rgba(37, 99, 235, 0.1);
}

.personal-info-body .form-label {
    font-weight: 500;
    color: #374151;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
}

.personal-info-body .form-label i {
    color: #2563eb;
    margin-right: 0.5rem;
    font-size: 0.9em;
}

.personal-info-body .form-check-label {
    color: #374151;
}

/* Enhanced icons and visual hierarchy */
.personal-info-header i {
    color: #2563eb;
    font-size: 1.2em;
}

/* Enhanced Security Settings */
.enhanced-security-settings {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.85), rgba(248, 250, 252, 0.95));
}

.security-settings-header {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(5, 150, 105, 0.15));
}

.security-settings-header h5 {
    color: #047857;
}

.enhanced-security-input {
    background: rgba(255, 255, 255, 0.9);
    border: 1px solid rgba(16, 185, 129, 0.2);
    border-radius: 0.75rem;
    padding: 0.75rem 1rem;
    transition: opacity 0.3s ease, transform 0.3s ease;
}

.enhanced-security-input:focus {
    border-color: rgba(16, 185, 129, 0.5);
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.1);
}

.enhanced-switch {
    width: 3rem;
    height: 1.5rem;
}

.enhanced-badge-primary {
    background: linear-gradient(135deg, rgba(37, 99, 235, 0.9), rgba(29, 78, 216, 0.9));
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 1rem;
}

.enhanced-badge-success {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.9), rgba(5, 150, 105, 0.9));
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 1rem;
}

/* Enhanced Account Status */
.enhanced-account-status {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.9), rgba(248, 250, 252, 0.95));
}

.account-status-header {
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(217, 119, 6, 0.15));
}

.account-status-header h5 {
    color: #b45309;
}

.account-status-item {
    padding: 0.75rem 0;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.account-status-item:last-child {
    border-bottom: none;
}

.enhanced-progress {
    height: 8px;
    background: rgba(245, 158, 11, 0.2);
    border-radius: 1rem;
}

.enhanced-progress-bar {
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.8), rgba(217, 119, 6, 0.8));
    border-radius: 1rem;
}

.wave {
    animation: wave 2s infinite;
    display: inline-block;
}



@media (max-width: 768px) {
    .glass-sidebar {
        position: fixed;
        z-index: 1000;
        transform: translateX(-100%);
        transition: transform 0.3s ease;
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        
    }
    
    .glass-sidebar.show {
        transform: translateX(0);
    }
    
    main {
        margin-left: 0 !important;
    }
    
    .glass-search {
        width: 200px !important;
    }
    .personal-info-body {
        padding: 1.5rem;
    }
    
    .personal-info-card:hover {
        transform: none;
    }
}

@media (max-width: 576px) {
    .personal-info-body {
        padding: 1rem;
    }
    
    .personal-info-header {
        padding: 1rem;
    }
    
    .glass-search {
        width: 150px !important;
    }
}

.truncate-filename {
    max-width: 100%;
    overflow-wrap: break-word;
    word-break: break-all;
    white-space: normal;
    display: block;
}




.page-speed-metrics {
    
    background: #f7f6fc;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
    color: #000000;
}

.scores-title,
.metrics-title {
    font-weight: 600;
    margin-bottom: 1rem;
    color:rgb(20, 20, 20);
}

.score-card {
    background: rgb(255, 255, 255);
    border-radius: 10px;
    box-shadow: 0 0 8px rgba(0,0,0,0.05);
    transition: transform 0.2s ease;
}
.score-card:hover {
    box-shadow: 0 0 12px rgba(0,0,0,0.1);
}

.score-category {
    font-weight: 500;
    font-size: 1rem;
    margin-bottom: 0.5rem;
    color:rgb(97, 95, 95);
}

.score-value {
    font-size: 2rem;
    font-weight: bold;
}

.score-label {
    font-size: 0.9rem;
    color:rgb(45, 31, 66);
}

.metrics-section {
    margin-top: 2rem;
}

.metrics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 1rem;
}

.metric-item {
    background: rgb(255, 255, 255);
    border-radius: 10px;
    padding: 1rem;
    box-shadow: 0 0 8px rgba(0,0,0,0.05);
}

.metric-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.metric-name {
    font-weight: 500;
    color:rgb(63, 16, 85);
}

.metric-score.badge-success {
    background-color: #28a745;
}
.metric-score.badge-warning {
    background-color: #ffc107;
    color: #000;
}
.metric-score.badge-danger {
    background-color: #dc3545;
}

.metric-value {
    font-size: 1.1rem;
    color:rgb(63, 59, 59);
}

.metric-progress .progress {
    background-color: rgba(255,255,255,0.1);
}
.metric-progress .progress-bar {
    transition: width 0.4s ease;
}

/* Correction pour le débordement des cartes d'audit */
.pagespeed-audits {
    max-width: 100%;
    overflow: hidden;
}

.audit-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 1rem;
    width: 100%;
}

.audit-card {
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 1.25rem;
    background: white;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    transition: opacity 0.3s ease, transform 0.3s ease;
    display: flex;
    flex-direction: column;
    height: fit-content;
    min-height: 120px;
    word-wrap: break-word;
    overflow-wrap: break-word;
}

.audit-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    
}

.audit-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.75rem;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.audit-title {
    font-weight: 600;
    color: #1a202c;
    font-size: 0.95rem;
    line-height: 1.4;
    flex: 1;
    min-width: 200px;
}

.audit-body {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.audit-description {
    color: #4a5568;
    font-size: 0.875rem;
    line-height: 1.5;
    margin: 0;
}

.audit-value {
    color: #718096;
    font-size: 0.8rem;
    font-style: italic;
    margin: 0;
}

table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        
        thead {
            background-color: #2e4db6;
        }
        
        th {
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            font-size: 16px;
        }
        
        tbody tr {
            border-bottom: 1px solid #e9ecef;
            transition: background-color 0.2s ease;
        }
        
        tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        td {
            padding: 15px;
            vertical-align: top;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        /* Styles pour les accordéons d'audit */
.audit-accordion .accordion-button {
    background-color: #dbe1f7;
    border: none;
    font-weight: 600;
    color: #2e4db6;
    padding: 1rem 1.25rem;
}

.audit-accordion .accordion-button:not(.collapsed) {
    background-color: #2e4db6;
    color: white;
    box-shadow: none;
}

.audit-accordion .accordion-button:focus {
    box-shadow: none;
    border-color: transparent;
}

.audit-accordion .accordion-item {
    border: 1px solid #dbe1f7;
    border-radius: 8px;
    margin-bottom: 0.5rem;
    overflow: hidden;
}

.audit-accordion .accordion-body {
    background-color: #f8f9fa;
    border-top: 1px solid #e9ecef;
}

.audit-accordion .accordion-title {
    display: flex;
    align-items: center;
    font-size: 1rem;
}

.audit-accordion .badge {
    font-size: 0.7rem;
    padding: 0.2rem 0.4rem;
}

/* Transition smooth */
.audit-accordion .accordion-collapse {
    transition: opacity 0.3s ease, transform 0.3s ease;
}

/* Responsive */
@media (max-width: 768px) {
    .audit-accordion .accordion-button {
        padding: 0.75rem 1rem;
        font-size: 0.9rem;
    }
    
    .audit-accordion .accordion-title {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.25rem;
    }
}
/* Styles pour la version avancée des accordéons */
.audit-accordion .accordion-button.warning {
    background-color: #fff3cd;
    color: #856404;
}

.audit-accordion .accordion-button.info {
    background-color: #d1ecf1;
    color: #0c5460;
}

.audit-accordion .accordion-button.secondary {
    background-color: #e2e3e5;
    color: #383d41;
}

.audit-accordion .accordion-button:not(.collapsed).warning {
    background-color: #856404;
    color: white;
}

.audit-accordion .accordion-button:not(.collapsed).info {
    background-color: #0c5460;
    color: white;
}

.audit-accordion .accordion-button:not(.collapsed).secondary {
    background-color: #383d41;
    color: white;
}

.audit-accordion .accordion-icon {
    font-size: 1.2rem;
}

.audit-accordion .accordion-arrow {
    font-size: 0.8rem;
    transition: transform 0.3s ease;
}

.audit-accordion .accordion-button:not(.collapsed) .accordion-arrow {
    transform: rotate(180deg);
}

.border-warning {
    border-color: #ffeaa7 !important;
}

.border-info {
    border-color: #81ecec !important;
}

.border-secondary {
    border-color: #dfe6e9 !important;
}
.accordion-button::after{
    display: none;
}
.btn-outlin-warnin:focus{
    color: white !important;
}


/* Styles pour la liste de projets scrollable */
.projects-scroll-container {
    max-height: 400px; /* Hauteur fixe pour 6 projets */
    overflow-y: auto;
    padding-right: 8px;
    margin-bottom: 1rem;
}

/* Style de la scrollbar */
.projects-scroll-container::-webkit-scrollbar {
    width: 6px;
}

.projects-scroll-container::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.1);
    border-radius: 3px;
}

.projects-scroll-container::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, 0.3);
    border-radius: 3px;
}

.projects-scroll-container::-webkit-scrollbar-thumb:hover {
    background: rgba(0, 0, 0, 0.5);
}

/* Ajustement de la hauteur des cartes de projet */
.project-card {
    height: 100px; /* Hauteur fixe pour uniformité */
    display: flex;
    flex-direction: column;
    justify-content: center;
}

/* Pour les écrans mobiles */
@media (max-width: 768px) {
    .projects-scroll-container {
        max-height: 350px;
    }
    
    .project-card {
        height: auto;
        min-height: 90px;
    }
}

/* Amélioration du badge */
.project-card .badge {
    white-space: nowrap;
    flex-shrink: 0;
}


/* Responsive */
@media (max-width: 768px) {
    .audit-grid {
        grid-template-columns: 1fr;
    }
    
    .audit-header {
        flex-direction: column;
        align-items: stretch;
    }
    
    .audit-title {
        min-width: 100%;
        margin-bottom: 0.5rem;
    }
}

/* Style pour les badges */
.badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    white-space: nowrap;
}

/* Progress bar */
.metric-progress {
    margin-top: 0.5rem;
}

/* Pour les très longs textes */
.audit-description {
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    max-height: 4.5em;
}

/* Correction spécifique pour les sections */
.pagespeed-audits .glass-card {
    background: rgba(255, 255, 255, 0.95);
    
    border: 1px solid rgba(255, 255, 255, 0.2);
}

/* Correction pour les cartes de projet avec retour à la ligne */
.project-card {
    width: 100%;
    max-width: 100%;
    overflow: hidden;
    word-wrap: break-word;
    overflow-wrap: break-word;
}

.project-card .d-flex {
    flex-wrap: wrap; /* Permet le retour à la ligne */
    gap: 0.5rem;
}

.project-card h6 {
    font-size: 0.95rem;
    font-weight: 600;
    color: #1a202c;
    word-wrap: break-word;
    overflow-wrap: break-word;
    margin-bottom: 0.25rem;
    line-height: 1.3;
}

.project-card small {
    font-size: 0.8rem;
    color: #718096 !important;
    word-wrap: break-word;
    overflow-wrap: break-word;
    line-height: 1.4;
    display: block;
    max-width: 100%;
}

.project-card .badge {
    flex-shrink: 0;
    min-width: max-content;
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    align-self: flex-start; /* Aligne le badge en haut */
    margin-left: auto; /* Pousse le badge à droite */
}

/* Version pour les très longues URLs avec retour à la ligne intelligent */
.project-card .url-text {
    word-break: break-word;
    hyphens: auto;
    -webkit-hyphens: auto;
    -ms-hyphens: auto;
}

/* Responsive */
@media (max-width: 768px) {
    .project-card .d-flex {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .project-card .badge {
        margin-left: 0;
        margin-top: 0.5rem;
        align-self: flex-start;
    }
}
/* Modern Loading Animations */
.loading-spinner {
    position: relative;
    width: 80px;
    height: 80px;
    margin: 0 auto;
}

.spinner-gradient {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background: conic-gradient(from 0deg, #667eea, #764ba2, #f093fb, #f5576c, #667eea);
    animation: rotate 2s linear infinite;
    opacity: 0.1;
}

.spinner-ring {
    position: absolute;
    top: 5px;
    left: 5px;
    right: 5px;
    bottom: 5px;
    border: 3px solid transparent;
    border-top: 3px solid #667eea;
    border-radius: 50%;
    animation: spin 1.5s linear infinite;
}

.spinner-dots {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    display: flex;
    gap: 4px;
}

.dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #667eea;
    animation: bounce 1.4s infinite ease-in-out;
}

.dot-1 { animation-delay: -0.32s; }
.dot-2 { animation-delay: -0.16s; }
.dot-3 { animation-delay: 0s; }



@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

@keyframes bounce {
    0%, 80%, 100% { transform: scale(0); }
    40% { transform: scale(1); }
}

/* Progress Steps */
.progress-steps {
    display: flex;
    justify-content: space-between;
    margin: 2rem 0;
    position: relative;
}

.progress-steps::before {
    content: '';
    position: absolute;
    top: 15px;
    left: 10%;
    right: 10%;
    height: 2px;
    background: #e9ecef;
    z-index: 1;
}

.step {
    display: flex;
    flex-direction: column;
    align-items: center;
    z-index: 2;
    position: relative;
    flex: 1;
}

.step-icon {
    width: 30px;
    height: 30px;
    background: #e9ecef;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    margin-bottom: 0.5rem;
    transition: opacity 0.3s ease, transform 0.3s ease;
}

.step.active .step-icon {
    background: #667eea;
    color: white;
    transform: scale(1.1);
}

.step-text {
    font-size: 0.75rem;
    color: #6c757d;
    text-align: center;
}

.step.active .step-text {
    color: #667eea;
    font-weight: 600;
}

/* Progress Bar Animation */
.progress-bar-animated {
    background: linear-gradient(135deg, #667eea, #764ba2); /* ✅ Simplifier */
    
}



.progress-container {
    position: relative;
}

.progress-percentage {
    position: absolute;
    right: 0;
    top: -25px;
    font-weight: 600;
}

/* Time Estimate */
.time-estimate {
    margin-top: 1rem;
}

/* Modal Glass Effect */
#analysisLoadingModal .modal-content {
    
    background: rgba(255, 255, 255, 0.95);
}

#analysisLoadingModal .modal-body {
    background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(248,249,250,0.9) 100%);
}
/* Specific styles for new project loading modal */
#newProjectLoadingModal .modal-content {
    
    background: rgba(255, 255, 255, 0.95);
}

#newProjectLoadingModal .modal-body {
    background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(248,249,250,0.9) 100%);
}

/* Button loading state */
.btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

/* Form validation styles */
.personal-info-input:invalid {
    border-color: #dc3545;
}

.personal-info-input:valid {
    border-color: #198754;
}
/* Specific styles for new project loading modal */
#newProjectLoadingModal .modal-content {
    
    background: rgba(255, 255, 255, 0.95);
}

#newProjectLoadingModal .modal-body {
    background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(248,249,250,0.9) 100%);
}

/* Button loading state */
.btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

/* Form validation styles */
.personal-info-input:invalid {
    border-color: #dc3545;
}

.personal-info-input:valid {
    border-color: #198754;
}


</style>
</head>
<body class="min-vh-100 d-flex flex-column min-vh-100">
    <div class="container-fluid h-100 flex-grow-1 d-flex flex-column">
        <div class="row">
            <!-- Sidebar Navigation -->
            @include('admin.body.sidebar')
            
            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 glass-background d-flex flex-column">
                <!-- Header -->
                @include('admin.body.header')
                
                <!-- Dashboard Content -->
               @yield('admin')

                <!-- Footer -->
                @include('admin.body.footer')
            </main>
        </div>
    </div>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>// Initialize tooltips
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
createFloatingShapes();</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script> 
<script src="{{ asset('backend/assets/js/code.js') }}"></script>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="{{ mix('js/show.js') }}"></script>
<script>
 @if(Session::has('message'))
 var type = "{{ Session::get('alert-type','info') }}"
 switch(type){
    case 'info':
    toastr.info(" {{ Session::get('message') }} ");
    break;

    case 'success':
    toastr.success(" {{ Session::get('message') }} ");
    break;

    case 'warning':
    toastr.warning(" {{ Session::get('message') }} ");
    break;

    case 'error':
    toastr.error(" {{ Session::get('message') }} ");
    break; 
 }
 @endif 
</script>





</body>