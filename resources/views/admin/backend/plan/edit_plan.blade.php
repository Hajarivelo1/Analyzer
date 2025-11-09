@extends('admin.admin_master')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<div class="container-fluid px-4 py-4">
    <div class="row">
        <div class="col-12">
            <div class="card glass-card">
                <div class="card-header glass-card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Plan</h5>
                    <a href="{{ route('all.plans') }}" class="btn glass-outline-btn">
                        <i class="bi bi-arrow-left me-2"></i>Back to Plans
                    </a>
                </div>

                <div class="card-body">
                <form action="{{ route('update.plans', $plans->id) }}" method="POST">
    @csrf
    <input type="hidden" name="id" value="{{ $plans->id }}">


    <div class="row g-4">
        <div class="col-md-6">
            <label for="name" class="form-label">Plan Name *</label>
            <input type="text" class="form-control personal-info-input" value="{{ $plans->name }}" id="name" name="name" required>
        </div>

        <div class="col-md-6">
            <label for="slug" class="form-label">Slug *</label>
            <input type="text" class="form-control personal-info-input" value="{{ $plans->slug }}" id="slug" name="slug" required>
            <small class="text-muted">Unique identifier (e.g., "free", "pro")</small>
        </div>

        <div class="col-12">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control personal-info-textarea" id="description" name="description" rows="2">{{ $plans->description }}</textarea>
        </div>

        <div class="col-md-4">
            <label for="price" class="form-label">Price (€) *</label>
            <input type="number" step="0.01" min="0" class="form-control personal-info-input" id="price" name="price" value="{{ $plans->price }}" required>
        </div>

        <div class="col-md-4">
            <label for="currency" class="form-label">Currency *</label>
            <select class="form-select personal-info-input" id="currency" name="currency" required>
                <option value="EUR" {{ $plans->currency == 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                <option value="USD" {{ $plans->currency == 'USD' ? 'selected' : '' }}>USD ($)</option>
                <option value="GBP" {{ $plans->currency == 'GBP' ? 'selected' : '' }}>GBP (£)</option>
            </select>
        </div>

        <div class="col-md-4">
            <label for="sort_order" class="form-label">Sort Order *</label>
            <input type="number" class="form-control personal-info-input" id="sort_order" name="sort_order" value="{{ $plans->sort_order }}" required>
        </div>

        <div class="col-md-3">
            <label for="analyses_per_month" class="form-label">Analyses/Month *</label>
            <input type="number" class="form-control personal-info-input" id="analyses_per_month" name="analyses_per_month" value="{{ $plans->analyses_per_month }}" required>
            <small class="text-muted">0 = unlimited</small>
        </div>

        <div class="col-md-3">
            <label for="projects_limit" class="form-label">Projects Limit *</label>
            <input type="number" class="form-control personal-info-input" id="projects_limit" name="projects_limit" value="{{ $plans->projects_limit }}" required>
            <small class="text-muted">0 = unlimited</small>
        </div>

        <div class="col-md-3">
            <label for="team_members_limit" class="form-label">Team Members *</label>
            <input type="number" class="form-control personal-info-input" id="team_members_limit" name="team_members_limit" value="{{ $plans->team_members_limit }}" required>
        </div>

        <div class="col-md-3">
            <label for="api_calls_per_month" class="form-label">API Calls/Month *</label>
            <input type="number" class="form-control personal-info-input" id="api_calls_per_month" name="api_calls_per_month" value="{{ $plans->api_calls_per_month }}" required>
        </div>

        <div class="col-12">
            <h6 class="mt-4 mb-3">Features</h6>
            <div class="d-flex flex-wrap gap-3">
                <!-- Competitor Analysis -->
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox"
                           id="has_competitor_analysis"
                           name="has_competitor_analysis"
                           value="1"
                           {{ $plans->has_competitor_analysis ? 'checked' : '' }}>
                    <label class="form-check-label" for="has_competitor_analysis">Competitor Analysis</label>
                </div>

                <!-- PDF Export -->
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox"
                           id="has_pdf_export"
                           name="has_pdf_export"
                           value="1"
                           {{ $plans->has_pdf_export ? 'checked' : '' }}>
                    <label class="form-check-label" for="has_pdf_export">PDF Export</label>
                </div>

                <!-- CSV Export -->
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox"
                           id="has_csv_export"
                           name="has_csv_export"
                           value="1"
                           {{ $plans->has_csv_export ? 'checked' : '' }}>
                    <label class="form-check-label" for="has_csv_export">CSV Export</label>
                </div>

                <!-- White Label -->
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox"
                           id="has_white_label"
                           name="has_white_label"
                           value="1"
                           {{ $plans->has_white_label ? 'checked' : '' }}>
                    <label class="form-check-label" for="has_white_label">White Label</label>
                </div>

                <!-- API Access -->
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox"
                           id="has_api_access"
                           name="has_api_access"
                           value="1"
                           {{ $plans->has_api_access ? 'checked' : '' }}>
                    <label class="form-check-label" for="has_api_access">API Access</label>
                </div>

                <!-- Priority Support -->
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox"
                           id="has_priority_support"
                           name="has_priority_support"
                           value="1"
                           {{ $plans->has_priority_support ? 'checked' : '' }}>
                    <label class="form-check-label" for="has_priority_support">Priority Support</label>
                </div>

                <!-- Active Plan -->
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox"
                           id="is_active"
                           name="is_active"
                           value="1"
                           {{ $plans->is_active ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Active Plan</label>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-4">
        <a href="{{ route('all.plans') }}" class="btn glass-outline-btn">Cancel</a>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-circle me-2"></i>Update Plan
        </button>
    </div>
</form>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const nameField = document.getElementById('name');
    const slugField = document.getElementById('slug');

    nameField.addEventListener('blur', function() {
        if (!slugField.value) {
            const slug = this.value.toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
            slugField.value = slug;
        }
    });
});
</script>
@endsection
