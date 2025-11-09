<nav class="col-md-3 col-lg-2 d-md-block sidebar collapse glass-sidebar p-2 position-fixed vh-100 overflow-auto" 
     id="sidebarMenu" 
     style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%); top: 0; left: 0; z-index: 1000;">
               
          <div class="pt-3">
                    <!-- Logo -->
                    <div class="px-3 py-4">
                        <h2 class="fw-bold text-white" style="font-size:22px;">SEOAnalyzer</h2>
                    </div>

                    @php
                      $id= Auth::user()->id;
                      $adminData= App\Models\User::find($id);
                    @endphp
                    <div class=" bottom-0 start-0 end-0 p-3 glass-user-area" style="background: rgba(30, 41, 59, 0.8); border-top: 1px solid rgba(255, 255, 255, 0.1);">
                        <div class="d-flex align-items-center">
                            <img src="{{ (!empty($adminData->photo)) ? url('upload/admin_images/'.$adminData->photo) : url('upload/no_image.jpg') }}" class="rounded-circle avatar-x1 img-thumbnail me-2" style="width:40px; height:40px;" alt="User Avatar">
                            <div>
                                <div class="fw-bold text-white">John Doe</div>
                                <small class="text-light" style="opacity: 0.8;">Professional <i class="bi bi-stars text-warning"></i></small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Navigation -->
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active glass-nav-item" href="{{route('admin.dashboard')}}" style="color: #f8fafc;">
                                <i class="bi bi-house me-2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
    <a class="nav-link glass-nav-item d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#managePlanMenu" role="button" aria-expanded="false" aria-controls="managePlanMenu" style="color: #f8fafc;">
        <span><i class="bi bi-box-seam me-2"></i> Manage Plan</span>
        <i class="bi bi-chevron-down small"></i>
    </a>
    <div class="collapse ps-4" id="managePlanMenu">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link glass-nav-item" href="{{ route('all.plans')  }}" style="color: #f8fafc;">
                    <i class="bi bi-list-ul me-2"></i> All Plans
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link glass-nav-item" href="{{route('add.plans')}}" style="color: #f8fafc;">
                    <i class="bi bi-plus-circle me-2"></i> Add Plan
                </a>
            </li>
        </ul>
    </div>
</li>

                        <li class="nav-item">
                            <a class="nav-link glass-nav-item" href="{{ route('all.projects')  }}" style="color: #f8fafc;">
                                <i class="bi bi-folder me-2"></i> Projects
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link glass-nav-item" href="{{ route('analysis.create') }}" style="color: #f8fafc;">
                                <i class="bi bi-search me-2"></i> New Analysis
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link glass-nav-item" href="#" style="color: #f8fafc;">
                                <i class="bi bi-bar-chart me-2"></i> Reports
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link glass-nav-item" href="#" style="color: #f8fafc;">
                                <i class="bi bi-gear me-2"></i> Settings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link glass-nav-item" href="#" style="color: #f8fafc;">
                                <i class="bi bi-question-circle me-2"></i> Help Center
                            </a>
                        </li>
                    </ul>
                    
                    <!-- User Area -->
                    
                </div>
            </nav>