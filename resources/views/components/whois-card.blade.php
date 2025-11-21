@props(['analysis'])

@php
    $whois = $analysis->whois_data ?? [];
    $registrar = $whois['registrar'] ?? [];
    
    // Formatage des dates
    $createdDate = isset($whois['created']) ? \Carbon\Carbon::parse($whois['created'])->format('M d, Y') : '—';
    $expiresDate = isset($whois['expires']) ? \Carbon\Carbon::parse($whois['expires'])->format('M d, Y') : '—';
    
    // Calcul des jours restants avant expiration
    $daysRemaining = '—';
    if (isset($whois['expires'])) {
        $expiry = \Carbon\Carbon::parse($whois['expires']);
        $now = \Carbon\Carbon::now();
        $days = $now->diffInDays($expiry, false);
        
        if ($days > 0) {
            $daysRemaining = $days . ' days';
        } else {
            $daysRemaining = 'Expired';
        }
    }
@endphp

@if (!empty($whois))
<div class="whois-card mb-4">
    <!-- Header avec icône et titre -->
    <div class="whois-header">
        <div class="header-content">
            <div class="header-icon">
                <i class="bi bi-globe-americas"></i>
            </div>
            <div>
                <h3 class="whois-title">Domain Registration</h3>
                <p class="whois-subtitle">WHOIS database information</p>
            </div>
        </div>
        <div class="domain-status {{ isset($whois['registered']) && $whois['registered'] ? 'status-active' : 'status-inactive' }}">
            {{ isset($whois['registered']) && $whois['registered'] ? 'Active' : 'Inactive' }}
        </div>
    </div>

    <!-- Informations principales -->
    <div class="whois-content">
        <!-- Domain Info -->
        <div class="info-section">
            <div class="section-header">
                <i class="bi bi-info-circle"></i>
                <h4>Domain Information</h4>
            </div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">
                        <i class="bi bi-link-45deg"></i>
                        Domain Name
                    </div>
                    <div class="info-value domain-name">{{ $whois['name'] ?? '—' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">
                        <i class="bi bi-calendar-plus"></i>
                        Created Date
                    </div>
                    <div class="info-value">{{ $createdDate }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">
                        <i class="bi bi-calendar-x"></i>
                        Expires Date
                    </div>
                    <div class="info-value">{{ $expiresDate }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">
                        <i class="bi bi-clock"></i>
                        Days Remaining
                    </div>
                    <div class="info-value days-remaining {{ str_contains($daysRemaining, 'Expired') ? 'text-danger' : 'text-success' }}">
                        {{ $daysRemaining }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Status & Security -->
        <div class="info-section">
            <div class="section-header">
                <i class="bi bi-shield-check"></i>
                <h4>Status & Security</h4>
            </div>
            <div class="status-grid">
                <div class="status-item">
                    <div class="status-label">Domain Status</div>
                    <div class="status-badge status-{{ $whois['status'] ? 'active' : 'inactive' }}">
                        {{ $whois['status'] ?? 'Unknown' }}
                    </div>
                </div>
                <div class="status-item">
                    <div class="status-label">DNSSEC</div>
                    <div class="status-badge status-{{ $whois['dnssec'] ? 'active' : 'inactive' }}">
                        {{ $whois['dnssec'] ?? '—' }}
                    </div>
                </div>
                <div class="status-item">
                    <div class="status-label">Registration</div>
                    <div class="status-badge status-{{ isset($whois['registered']) && $whois['registered'] ? 'active' : 'inactive' }}">
                        {{ isset($whois['registered']) ? ($whois['registered'] ? 'Registered' : 'Available') : '—' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Registrar Information -->
        @if(!empty($registrar))
        <div class="info-section">
            <div class="section-header">
                <i class="bi bi-building"></i>
                <h4>Registrar</h4>
            </div>
            <div class="registrar-info">
                <div class="registrar-details">
                    <div class="registrar-name">
                        <i class="bi bi-building"></i>
                        {{ $registrar['name'] ?? '—' }}
                    </div>
                    @if(isset($registrar['email']))
                    <div class="registrar-contact">
                        <i class="bi bi-envelope"></i>
                        <a href="mailto:{{ $registrar['email'] }}">{{ $registrar['email'] }}</a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Nameservers -->
        @if(!empty($whois['nameservers']))
        <div class="info-section">
            <div class="section-header">
                <i class="bi bi-server"></i>
                <h4>Nameservers</h4>
            </div>
            <div class="nameservers-list">
                @foreach ($whois['nameservers'] as $ns)
                <div class="nameserver-item">
                    <i class="bi bi-hdd-network"></i>
                    <span class="nameserver-text">{{ $ns }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- Footer avec timestamp -->
    <div class="whois-footer">
        <div class="last-updated">
            <i class="bi bi-clock-history"></i>
            WHOIS data retrieved {{ now()->format('M d, Y \a\t H:i') }}
        </div>
    </div>
</div>
@else
<div class="empty-state">
    <div class="empty-icon">
        <i class="bi bi-database-x"></i>
    </div>
    <h4>No WHOIS Data Available</h4>
    <p>Domain registration information could not be retrieved</p>
</div>
@endif

<style>
.whois-card {
    background-color: #ffffff;
    border-radius: 20px;
    box-shadow: 
        0 10px 40px rgba(0, 0, 0, 0.08),
        0 2px 10px rgba(0, 0, 0, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.6);
    
    overflow: hidden;
    transition: all 0.3s ease;
}

.whois-card:hover {
    transform: translateY(-2px);
    box-shadow: 
        0 20px 60px rgba(0, 0, 0, 0.12),
        0 4px 20px rgba(0, 0, 0, 0.06);
}

.whois-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    position: relative;
    overflow: hidden;
}

.whois-header::before {
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
    gap: 1rem;
}

.header-icon {
    font-size: 2.5rem;
    opacity: 0.9;
}

.whois-title {
    font-weight: 700;
    font-size: 1.5rem;
    margin: 0;
}

.whois-subtitle {
    opacity: 0.9;
    font-size: 0.9rem;
    margin: 0.25rem 0 0 0;
}

.domain-status {
    padding: 0.5rem 1.25rem;
    border-radius: 25px;
    font-weight: 600;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-active {
    background: rgba(72, 187, 120, 0.9);
    color: white;
}

.status-inactive {
    background: rgba(245, 101, 101, 0.9);
    color: white;
}

.whois-content {
    padding: 2rem;
}

.info-section {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
    border: 1px solid rgba(0, 0, 0, 0.03);
}

.info-section:last-child {
    margin-bottom: 0;
}

.section-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #f1f5f9;
}

.section-header i {
    font-size: 1.25rem;
    color: #667eea;
}

.section-header h4 {
    font-weight: 600;
    color: #2d3748;
    margin: 0;
    font-size: 1.1rem;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.info-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    color: #4a5568;
    font-size: 0.9rem;
}

.info-label i {
    color: #667eea;
    font-size: 0.9rem;
}

.info-value {
    font-weight: 500;
    color: #2d3748;
    font-size: 1rem;
}

.domain-name {
    font-weight: 700;
    color: #667eea;
    font-size: 1.1rem;
}

.days-remaining {
    font-weight: 600;
}

.status-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.status-item {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.status-label {
    font-weight: 600;
    color: #4a5568;
    font-size: 0.9rem;
}

.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.8rem;
    text-align: center;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-active {
    background: #c6f6d5;
    color: #276749;
}

.status-inactive {
    background: #fed7d7;
    color: #c53030;
}

.registrar-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.registrar-details {
    flex: 1;
}

.registrar-name {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    color: #2d3748;
    font-size: 1.1rem;
    margin-bottom: 0.5rem;
}

.registrar-name i {
    color: #667eea;
}

.registrar-contact {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #718096;
    font-size: 0.9rem;
}

.registrar-contact a {
    color: #4299e1;
    text-decoration: none;
}

.registrar-contact a:hover {
    text-decoration: underline;
}

.nameservers-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.nameserver-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    background: #f7fafc;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    transition: all 0.2s ease;
}

.nameserver-item:hover {
    background: #edf2f7;
    transform: translateX(4px);
}

.nameserver-item i {
    color: #667eea;
    font-size: 1rem;
}

.nameserver-text {
    font-family: 'Courier New', monospace;
    font-weight: 500;
    color: #2d3748;
}

.whois-footer {
    background: #f8fafc;
    padding: 1rem 2rem;
    border-top: 1px solid #e2e8f0;
}

.last-updated {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #718096;
    font-size: 0.85rem;
    font-weight: 500;
}

.last-updated i {
    font-size: 0.8rem;
}

.empty-state {
    text-align: center;
    padding: 3rem 2rem;
    color: #a0aec0;
}

.empty-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.empty-state h4 {
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: #718096;
}

.empty-state p {
    margin: 0;
    font-size: 0.9rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .whois-header {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
    
    .header-content {
        flex-direction: column;
        text-align: center;
    }
    
    .info-grid,
    .status-grid {
        grid-template-columns: 1fr;
    }
    
    .whois-content {
        padding: 1.5rem;
    }
    
    .info-section {
        padding: 1.25rem;
    }
}

/* Animations */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.whois-card {
    animation: fadeIn 0.6s ease-out;
}

.info-section {
    animation: fadeIn 0.6s ease-out 0.2s both;
}

.info-section:nth-child(2) {
    animation-delay: 0.3s;
}

.info-section:nth-child(3) {
    animation-delay: 0.4s;
}

.info-section:nth-child(4) {
    animation-delay: 0.5s;
}
</style>