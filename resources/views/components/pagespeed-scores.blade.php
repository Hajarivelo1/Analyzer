@props(['scores'])

@if($scores !== null && is_array($scores) && count($scores) > 0)
<div class="pagespeed-scores">
    <h3>Scores Secondaires PageSpeed</h3>
    
    <div class="scores-grid">
        @foreach($scores as $category => $score)
            <div class="score-item">
                <span class="score-category">{{ ucfirst($category) }}:</span>
                <span class="score-value">
                    @if($score !== null)
                        {{ $score }}%
                    @else
                        <span class="text-muted">N/A</span>
                    @endif
                </span>
            </div>
        @endforeach
    </div>
</div>
@else
<div class="pagespeed-scores">
    <h3>Scores Secondaires PageSpeed</h3>
    <p class="text-warning">Chargement des scores en cours...</p>
</div>
@endif
