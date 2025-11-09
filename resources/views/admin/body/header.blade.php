<header class="d-flex justify-content-between align-items-center pt-3 pb-2 border-bottom ">
                    <button class="btn d-md-none glass-btn" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu">
                        <i class="bi bi-list"></i>
                    </button>
                    
                    <div class="d-flex align-items-center">
                        <div class="input-group me-3 glass-search" style="width: 300px;">
                            <span class="input-group-text glass-input-icon"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control glass-input" placeholder="Search projects...">
                        </div>
                        
                        <div class="dropdown">
                            <button class="btn glass-btn position-relative me-2" type="button">
                                <i class="bi bi-bell"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">3</span>
                            </button>
                            
                            <button class="btn glass-btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle me-1"></i> John
                            </button>
                            <ul class="dropdown-menu glass-dropdown">
                                <li><a class="dropdown-item" href="{{ route('admin.profile')}}"> <i class="bi bi-person-circle me-1"></i> Profile</a></li>
                                <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-1"></i> Settings</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{route('admin.logout')}}"><i class="bi bi-plugin"></i> Sign out</a></li>
                            </ul>
                        </div>
                    </div>
                </header>