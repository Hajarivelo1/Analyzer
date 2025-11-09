<div class="pagespeed-audits glass-card p-4 mb-4">

    {{-- Opportunités d’optimisation --}}
    @if(!empty($auditFragments['opportunities']))
        
        <div style="background-color: #dbe1f7;" class="px-4 py-3 rounded-top mb-4">
        <h5 class="fw-bold mb-0" style=" color:#2e4db6;">⚡ Opportunities For Optimization</h5>
    </div>
        <div class="audit-grid mb-4">
            @foreach($auditFragments['opportunities'] as $audit)
                <div class="audit-card">
                    <div class="audit-header">
                        <span class="audit-title">{{ $audit['title'] }}</span>
                        @if(isset($audit['estimatedSavingsMs']))
                            <span class="badge badge-info">
                                +{{ round($audit['estimatedSavingsMs'] / 1000, 2) }}s
                            </span>
                        @endif
                    </div>
                    <div class="audit-body">
                        <p class="audit-description">{{ $audit['description'] }}</p>
                        @if(isset($audit['displayValue']))
                            <p class="audit-value text-muted">Mesure : {{ $audit['displayValue'] }}</p>
                        @endif
                        @if(isset($audit['score']))
                            <div class="progress" style="height: 4px;">
                                <div class="progress-bar {{ 
                                    $audit['score'] >= 0.9 ? 'bg-success' : 
                                    ($audit['score'] >= 0.5 ? 'bg-warning' : 'bg-danger') 
                                }}" style="width: {{ $audit['score'] * 100 }}%"></div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Diagnostics techniques --}}
    @if(!empty($auditFragments['diagnostics']))
        
        <div style="background-color: #dbe1f7;" class="px-4 py-3 rounded-top mb-4">
        <h5 class="fw-bold mb-0" style=" color:#2e4db6;">🔍 Technical Diagnostics</h5>
    </div>
        <div class="audit-grid mb-4">
            @foreach($auditFragments['diagnostics'] as $audit)
                <div class="audit-card">
                    <div class="audit-header">
                        <span class="audit-title">{{ $audit['title'] }}</span>
                        @if(isset($audit['score']))
                            <span class="badge badge-{{ 
                                $audit['score'] >= 0.9 ? 'success' : 
                                ($audit['score'] >= 0.5 ? 'warning' : 'danger') 
                            }}">
                                {{ round($audit['score'] * 100) }}%
                            </span>
                        @endif
                    </div>
                    <div class="audit-body">
                        <p class="audit-description">{{ $audit['description'] }}</p>
                        @if(isset($audit['displayValue']))
                            <p class="audit-value text-muted">Mesure : {{ $audit['displayValue'] }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Audits informatifs --}}
    @if(!empty($auditFragments['informative']))
       
        <div style="background-color: #dbe1f7;" class="px-4 py-3 rounded-top mb-4">
        <h5 class="fw-bold mb-0" style=" color:#2e4db6;">📘 Informative Audits</h5>
    </div>
        <div class="audit-grid">
            @foreach($auditFragments['informative'] as $audit)
                <div class="audit-card">
                    <div class="audit-header">
                        <span class="audit-title">{{ $audit['title'] }}</span>
                    </div>
                    <div class="audit-body">
                        <p class="audit-description">{{ $audit['description'] }}</p>
                        @if(isset($audit['displayValue']))
                            <p class="audit-value text-muted">Mesure : {{ $audit['displayValue'] }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>
