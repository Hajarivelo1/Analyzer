@extends('admin.admin_master')
@section('admin')



<div class="py-4">
                    <!-- Welcome Card & Quick Stats -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card glass-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h2 class="card-title">Welcome back, John! <span class="wave">👋</span></h2>
                                            <p class="text-muted">Here's what's happening with your SEO projects today.</p>
                                        </div>
                                        <button class="btn glass-primary-btn">
                                            <i class="bi bi-plus-circle me-1"></i> New Analysis
                                        </button>
                                    </div>
                                    
                                    <div class="row mt-4">
                                        <div class="col-md-4">
                                            <div class="card glass-stat-card">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center">
                                                        <div class="glass-icon-bg rounded p-2 me-3">
                                                            <i class="bi bi-folder text-white fs-4"></i>
                                                        </div>
                                                        <div>
                                                            <h4 class="mb-0">12</h4>
                                                            <span class="text-muted">Projects</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card glass-stat-card">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center">
                                                        <div class="glass-icon-bg rounded p-2 me-3">
                                                            <i class="bi bi-bar-chart text-white fs-4"></i>
                                                        </div>
                                                        <div>
                                                            <h4 class="mb-0">47/300</h4>
                                                            <span class="text-muted">Analyses This Month</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card glass-stat-card">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center">
                                                        <div class="glass-icon-bg rounded p-2 me-3">
                                                            <i class="bi bi-graph-up text-white fs-4"></i>
                                                        </div>
                                                        <div>
                                                            <h4 class="mb-0">78%</h4>
                                                            <span class="text-muted">Avg. SEO Score</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Analyses Table -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card glass-card">
                                <div class="card-header glass-card-header">
                                    <h5 class="card-title mb-0">Recent Analyses</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover glass-table">
                                            <thead>
                                                <tr>
                                                    <th>URL</th>
                                                    <th>Score</th>
                                                    <th>Status</th>
                                                    <th>Date</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>/blog/seo-tips</td>
                                                    <td>
                                                        <span class="badge glass-badge-success">82%</span>
                                                    </td>
                                                    <td>
                                                        <span class="badge glass-badge-success"><i class="bi bi-check-circle me-1"></i> Complete</span>
                                                    </td>
                                                    <td>2024-01-15</td>
                                                    <td>
                                                        <button class="btn btn-sm glass-outline-btn">View</button>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>/products/ai-tool</td>
                                                    <td>
                                                        <span class="badge glass-badge-warning">65%</span>
                                                    </td>
                                                    <td>
                                                        <span class="badge glass-badge-warning"><i class="bi bi-exclamation-triangle me-1"></i> Needs Work</span>
                                                    </td>
                                                    <td>2024-01-14</td>
                                                    <td>
                                                        <button class="btn btn-sm glass-outline-btn">View</button>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>/about</td>
                                                    <td>
                                                        <span class="badge glass-badge-success">91%</span>
                                                    </td>
                                                    <td>
                                                        <span class="badge glass-badge-success"><i class="bi bi-check-circle me-1"></i> Excellent</span>
                                                    </td>
                                                    <td>2024-01-13</td>
                                                    <td>
                                                        <button class="btn btn-sm glass-outline-btn">View</button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- SEO Score Overview -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card glass-card h-100">
                                <div class="card-header glass-card-header">
                                    <h5 class="card-title mb-0">SEO Score Distribution</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>Excellent (90-100%)</span>
                                            <span>25%</span>
                                        </div>
                                        <div class="progress glass-progress" style="height: 10px;">
                                            <div class="progress-bar glass-progress-bar" style="width: 25%"></div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>Good (70-89%)</span>
                                            <span>45%</span>
                                        </div>
                                        <div class="progress glass-progress" style="height: 10px;">
                                            <div class="progress-bar glass-progress-bar" style="width: 45%"></div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>Needs Work (50-69%)</span>
                                            <span>20%</span>
                                        </div>
                                        <div class="progress glass-progress" style="height: 10px;">
                                            <div class="progress-bar glass-progress-bar" style="width: 20%"></div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>Poor (<50%)</span>
                                            <span>10%</span>
                                        </div>
                                        <div class="progress glass-progress" style="height: 10px;">
                                            <div class="progress-bar glass-progress-bar" style="width: 10%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Quick Actions -->
                        <div class="col-md-6">
                            <div class="card glass-card h-100">
                                <div class="card-header glass-card-header">
                                    <h5 class="card-title mb-0">Quick Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6 mb-3">
                                            <button class="btn glass-action-btn w-100 h-100 py-3">
                                                <i class="bi bi-download fs-1 text-primary mb-2 d-block"></i>
                                                Import Project
                                            </button>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <button class="btn glass-action-btn w-100 h-100 py-3">
                                                <i class="bi bi-file-text fs-1 text-primary mb-2 d-block"></i>
                                                Generate Report
                                            </button>
                                        </div>
                                        <div class="col-6">
                                            <button class="btn glass-action-btn w-100 h-100 py-3">
                                                <i class="bi bi-arrow-repeat fs-1 text-primary mb-2 d-block"></i>
                                                Bulk Analysis
                                            </button>
                                        </div>
                                        <div class="col-6">
                                            <button class="btn glass-action-btn w-100 h-100 py-3">
                                                <i class="bi bi-box-arrow-up fs-1 text-primary mb-2 d-block"></i>
                                                Export Results
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
              
                        


@endsection                