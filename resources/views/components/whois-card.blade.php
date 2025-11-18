@props(['analysis'])

@php
    $whois = $analysis->whois_data ?? [];
    $registrar = $whois['registrar'] ?? [];
@endphp

@if (!empty($whois))
    <div class="glass-card p-4 mb-4 mt-4">
       
        <div style="background-color: #dbe1f7;" class="px-4 py-3 rounded-top mb-4">
        <h5 class="fw-bold mb-0" style=" color:#2e4db6;">🌐 WHOIS Informations</h5>
    </div>
        <div class="row">
            <div class="col-md-6"><strong>Domain :</strong> {{ $whois['name'] ?? '—' }}</div>
            <div class="col-md-6"><strong>Created on:</strong> {{ $whois['created'] ?? '—' }}</div>
            <div class="col-md-6"><strong>Expires on:</strong> {{ $whois['expires'] ?? '—' }}</div>
            <div class="col-md-6"><strong>Status :</strong> {{ $whois['status'] ?? '—' }}</div>
            <div class="col-md-6"><strong>Registered:</strong> {{ isset($whois['registered']) ? ($whois['registered'] ? 'Yes' : 'No') : '—' }}</div>
            <div class="col-md-6"><strong>DNSSEC :</strong> {{ $whois['dnssec'] ?? '—' }}</div>
            <div class="col-md-6"><strong>Registrar :</strong> {{ $registrar['name'] ?? '—' }}</div>
            <div class="col-md-6"><strong>Email :</strong> {{ $registrar['email'] ?? '—' }}</div>
        </div>
        <div class="mt-3"><strong>Nameservers :</strong>
            <ul class="list-unstyled ms-3">
                @foreach ($whois['nameservers'] ?? [] as $ns)
                    <li>{{ $ns }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@else
    <p class="text-muted fst-italic">Aucune donnée WHOIS disponible.</p>
@endif
