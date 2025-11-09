@extends('admin.admin_master')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>


<div class="container-fluid px-4 py-4">
    <div class="row">
        <div class="col-12">
            <div class="card glass-card">
                <div class="card-header glass-card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Add New Plan</h5>
                    <a href="{{ route('all.plans') }}" class="btn glass-outline-btn">
                        <i class="bi bi-arrow-left me-2"></i>Back to Plans
                    </a>
                </div>

                <div class="card-body">
                    <form action="{{ route('store.plans') }}" method="POST">
                        @csrf

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Plan Name *</label>
                                <input type="text" class="form-control personal-info-input" id="name" name="name" required>
                            </div>

                            <div class="col-md-6">
                                <label for="slug" class="form-label">Slug *</label>
                                <input type="text" class="form-control personal-info-input" id="slug" name="slug" required>
                                <small class="text-muted">Unique identifier (e.g., "free", "pro")</small>
                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control personal-info-textarea" id="description" name="description" rows="2"></textarea>
                            </div>

                            <div class="col-md-4">
                                <label for="price" class="form-label">Price (€) *</label>
                                <input type="number" step="0.01" min="0" class="form-control personal-info-input" id="price" name="price" required>
                            </div>

                            <div class="col-md-4">
                                <label for="currency" class="form-label">Currency *</label>
                                <select class="form-select personal-info-input" id="currency" name="currency" required>
                                    <option value="EUR">EUR (€)</option>
                                    <option value="USD">USD ($)</option>
                                    <option value="GBP">GBP (£)</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="sort_order" class="form-label">Sort Order *</label>
                                <input type="number" class="form-control personal-info-input" id="sort_order" name="sort_order" value="0" required>
                            </div>

                            <div class="col-md-3">
                                <label for="analyses_per_month" class="form-label">Analyses/Month *</label>
                                <input type="number" class="form-control personal-info-input" id="analyses_per_month" name="analyses_per_month" value="0" required>
                                <small class="text-muted">0 = unlimited</small>
                            </div>

                            <div class="col-md-3">
                                <label for="projects_limit" class="form-label">Projects Limit *</label>
                                <input type="number" class="form-control personal-info-input" id="projects_limit" name="projects_limit" value="1" required>
                                <small class="text-muted">0 = unlimited</small>
                            </div>

                            <div class="col-md-3">
                                <label for="team_members_limit" class="form-label">Team Members *</label>
                                <input type="number" class="form-control personal-info-input" id="team_members_limit" name="team_members_limit" value="1" required>
                            </div>

                            <div class="col-md-3">
                                <label for="api_calls_per_month" class="form-label">API Calls/Month *</label>
                                <input type="number" class="form-control personal-info-input" id="api_calls_per_month" name="api_calls_per_month" value="0" required>
                            </div>

                            <div class="col-12">
                                <h6 class="mt-4 mb-3">Features</h6>
                                <div class="d-flex flex-wrap gap-3">
                                    @foreach([
                                        ['id' => 'has_competitor_analysis', 'label' => 'Competitor Analysis'],
                                        ['id' => 'has_pdf_export', 'label' => 'PDF Export'],
                                        ['id' => 'has_csv_export', 'label' => 'CSV Export'],
                                        ['id' => 'has_white_label', 'label' => 'White Label'],
                                        ['id' => 'has_api_access', 'label' => 'API Access'],
                                        ['id' => 'has_priority_support', 'label' => 'Priority Support'],
                                        ['id' => 'is_active', 'label' => 'Active Plan', 'checked' => true]
                                    ] as $feature)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox"
                                               id="{{ $feature['id'] }}"
                                               name="{{ $feature['id'] }}"
                                               value="1"
                                               {{ isset($feature['checked']) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="{{ $feature['id'] }}">{{ $feature['label'] }}</label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="reset" class="btn glass-outline-btn">Reset</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Add Plan
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
