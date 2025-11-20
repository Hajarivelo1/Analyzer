@extends('admin.admin_master')

@section('admin')

<div class="container py-4" style="max-width: 1000px;">
    <h1 class="h3 fw-bold mb-3">SEO Generations History</h1>
    <p class="text-muted mb-4">Hover and click on Title or Meta to copy them instantly.</p>

    <!-- Flash messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($items->isEmpty())
        <div class="alert alert-info">No generations yet.</div>
    @else
        <div class="table-responsive shadow-sm rounded-3">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Prompt</th>
                        <th>Lang</th>
                        <th>Title</th>
                        <th>Meta</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $g)
                        <tr>
                            <td>{{ $g->created_at->format('Y-m-d H:i') }}</td>
                            <td class="text-truncate" style="max-width: 240px;">{{ $g->prompt }}</td>
                            <td><span class="badge bg-secondary">{{ $g->lang }}</span></td>
                            <td class="text-truncate copyable" style="max-width: 240px;" 
                                id="title-{{ $g->id }}" 
                                data-bs-toggle="tooltip" title="Click to copy Title">
                                {{ $g->title }}
                            </td>
                            <td class="text-truncate copyable" style="max-width: 280px;" 
                                id="meta-{{ $g->id }}" 
                                data-bs-toggle="tooltip" title="Click to copy Meta">
                                {{ $g->meta }}
                            </td>
                            <td class="text-end">
                                <!-- Réutiliser -->
                                <form action="{{ route('seo.history.reuse', $g->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="tooltip" title="Réutiliser">
                                        <i class="bi bi-arrow-repeat"></i>
                                    </button>
                                </form>

                                <!-- Supprimer -->
                                <form action="{{ route('seo.history.destroy', $g->id) }}" method="POST" class="d-inline" 
                                      onsubmit="return confirm('Supprimer cet historique ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                            data-bs-toggle="tooltip" title="Supprimer">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $items->links() }}
        </div>
    @endif
</div>

<script>
// Copier au clic
document.addEventListener('DOMContentLoaded', function () {
    // Activer tooltips Bootstrap
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Rendre les cellules copyables
    document.querySelectorAll('.copyable').forEach(el => {
        el.style.cursor = 'pointer';
        el.addEventListener('click', () => {
            const text = el.textContent.trim();
            if (!text) return;

            navigator.clipboard.writeText(text).then(() => {
                const old = el.textContent;
                el.textContent = '✓ Copied!';
                el.style.color = '#198754';
                setTimeout(() => {
                    el.textContent = old;
                    el.style.color = '';
                }, 1200);
            });
        });
    });
});
</script>

@endsection
