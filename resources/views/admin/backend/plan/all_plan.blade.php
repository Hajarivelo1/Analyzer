

@extends('admin.admin_master')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<div class="container-fluid h-100 d-flex flex-column">
    <h4 class="mb-4 fw-bold text-white pt-2">All Plans</h4>
    <div class="ms-auto pb-2">
        <button class="btn btn-primary">
           <a class="text-white" href="{{route('add.plans')}}"> <i class="bi bi-plus-circle me-1"></i> Add Plan</a>
        </button>
    </div>

    <div class="card glass-card">
        <div class="card-body">
        <table class="table table-striped glass-table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Price</th>
            <th>Billing</th>
            <th>Projects</th>
            <th>Team</th>
            <th>API</th>
            <th>PDF</th>
            <th>CSV</th>
            <th>White Label</th>
            <th>Status</th>
            <th class="text-end">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($plans as $plan)
        <tr>
            <td>{{ $plan->name }}</td>
            <td>{{ $plan->price == 0 ? 'Free' : '€' . number_format($plan->price, 2) }}</td>
            <td>{{ ucfirst($plan->billing_period) }}</td>
            <td>{{ $plan->projects_limit }}</td>
            <td>{{ $plan->team_members_limit }}</td>
            <td>{{ $plan->has_api_access ? '✔' : '✖' }}</td>
            <td>{{ $plan->has_pdf_export ? '✔' : '✖' }}</td>
            <td>{{ $plan->has_csv_export ? '✔' : '✖' }}</td>
            <td>{{ $plan->has_white_label ? '✔' : '✖' }}</td>
            <td>
                @if($plan->is_active)
                    <span class="badge bg-success">Active</span>
                @else
                    <span class="badge bg-secondary">Inactive</span>
                @endif
            </td>
            <td class="text-end">
                <a href="{{route('edit.plans', $plan->id)}}" class="btn btn-sm glass-outline-btn me-2" title="Edit">
                    <i class="bi bi-pencil-square"></i>
                </a>
                <a href="{{route('delete.plans', $plan->id)}}" id="delete" class="btn btn-sm glass-outline-btn me-2 text-danger" title="Edit">
                    <i class="bi bi-trash"></i>
                </a>
                
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

        </div>
    </div>
</div>

@endsection




