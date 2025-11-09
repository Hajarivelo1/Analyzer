@extends('admin.admin_master')
@section('admin')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<!-- Profile Content -->
<div id="profile-content" class="content-section pt-4">

                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h2 class="mb-0">Profile Settings</h2>
                                    <button class="btn glass-primary-btn">
                                        <i class="bi bi-pencil me-1"></i> Edit Profile
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <!-- Profile Info -->

                            
                            <div class="col-md-4 mb-4">
                                <div class="card glass-card">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <img src="{{url('upload/admin_images/'.$adminData->photo)}}"  class="rounded-circle avatar-x1 img-thumbnail mb-3" style="width:100px; height:100px;" alt="Profile Picture">
                                            <h4>{{$adminData->name}}</h4>
                                            
                                            <span class="badge glass-badge-success">Premium Member</span>
                                        </div>
                                        
                                        <div class="d-grid gap-2">
                                            <!-- Bouton visible -->
<button class="btn glass-outline-btn">
    <i class="bi bi-camera me-1"></i> Change Photo
</button>



                                            <button class="btn glass-outline-btn">
                                                <i class="bi bi-shield-check me-1"></i> Privacy Settings
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="card glass-card mt-4 enhanced-account-status">
                                    <div class="card-header glass-card-header account-status-header">
                                        <h5 class="card-title mb-0">
                                            <i class="bi bi-graph-up-arrow me-2"></i>Account Status
                                        </h5>
                                    </div>
                                    <div class="card-body account-status-body">
                                        <div class="account-status-item">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="status-label">Plan Type</span>
                                                <span class="status-value fw-bold text-primary">Professional</span>
                                            </div>
                                        </div>
                                        <div class="account-status-item">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="status-label">Member Since</span>
                                                <span class="status-value">Jan 2023</span>
                                            </div>
                                        </div>
                                        <div class="account-status-item">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="status-label">Analyses Used</span>
                                                <span class="status-value">47/300</span>
                                            </div>
                                            <div class="progress enhanced-progress mt-2">
                                                <div class="progress-bar enhanced-progress-bar" style="width: 15.7%"></div>
                                            </div>
                                        </div>
                                        <div class="account-status-item">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="status-label">Account Status</span>
                                                <span class="badge enhanced-badge-success">Active</span>
                                            </div>
                                        </div>
                                        <div class="account-status-item">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="status-label">Next Billing</span>
                                                <span class="status-value">Feb 15, 2024</span>
                                            </div>
                                        </div>
                                        <div class="text-center mt-3">
                                            <button class="btn glass-primary-btn btn-sm">
                                                <i class="bi bi-arrow-up-circle me-1"></i>Upgrade Plan
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Profile Details -->
                            <div class="col-md-8 mb-4">
                                <div class="card glass-card personal-info-card">
                                    <div class="card-header glass-card-header personal-info-header">
                                        <h5 class="card-title mb-0">
                                            <i class="bi bi-person-vcard me-2"></i>Personal Information
                                        </h5>
                                    </div>
                                    <div class="card-body personal-info-body">
                                        <form action="{{route('admin.profile.store') }}" method="post" enctype="multipart/form-data" >
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-12 mb-3">
                                                    <label class="form-label"><i class="bi bi-person me-1"></i>Name</label>
                                                    <input type="text" name="name" class="form-control glass-input personal-info-input" value="{{$adminData->name}}">
                                                </div>
                                               
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label"><i class="bi bi-envelope me-1"></i>Email Address</label>
                                                <input type="email" name="email" class="form-control glass-input personal-info-input" value="{{$adminData->email}}">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label"><i class="bi bi-telephone me-1"></i>Phone Number</label>
                                                <input type="tel" name="phone" class="form-control glass-input personal-info-input" value="{{$adminData->phone}}">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label"><i class="bi bi-building me-1"></i>Address</label>
                                                <input type="text" name="address"  class="form-control glass-input personal-info-input" value="{{$adminData->address}}">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label"><i class="bi bi-building me-1"></i>Profile Image</label>
                                                <input type="file" name="photo" class="form-control" id="image" >
                                            </div>
                                            <div class="mb-3 col-md-6">
                <label for="inputEmail4" class="form-label"> </label>
                 <img id="showImage" src="{{ (!empty($adminData->photo)) ? url('upload/admin_images/'.$adminData->photo) : url('upload/no_image.jpg') }}"  class="rounded-circle avatar-xl img-thumbnail" style="width: 80px; height:80px;" >
            </div>
            
                                            
                                            <div class="d-flex justify-content-end">
                                                <button type="button" class="btn glass-outline-btn me-2">Cancel</button>
                                                <button type="submit" class="btn glass-primary-btn">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                
                                <div class="card glass-card mt-4 enhanced-security-settings">
                                    <div class="card-header glass-card-header security-settings-header">
                                        <h5 class="card-title mb-0">
                                            <i class="bi bi-shield-check me-2"></i>Security Settings
                                        </h5>
                                    </div>
                                    <div class="card-body security-settings-body">

                                    
                                        <div class="security-item mb-4">
                                        <form action="{{route('admin.password.update')}}" method="post" enctype="multipart/form-data">
                                        @csrf
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="mb-0">Password</h6>
                                                <span class="text-muted small">Last changed 3 months ago</span>
                                            </div>

                                            
                                            <div class="mb-3">
                                                <label class="form-label">Old Password</label>
                                                <input name="old_password" id="old_password" type="password" class="form-control enhanced-security-input @error('old_password') is-invalid  @enderror" placeholder="old password">
                                                @error('old_password')
                                                <span class="text-danger">{{$message}}</span>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">New Password</label>
                                                <input name="new_password" id="new_password" type="password" class="form-control enhanced-security-input   @error('new_password') is-invalid  @enderror" placeholder="Enter new password">
                                                @error('new_password')
                                                <span class="text-danger">{{$message}}</span>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Confirm New Password</label>
                                                <input name="new_password_confirmation" id="new_password_confirmation" type="password" class="form-control enhanced-security-input  @error('new_password_confirmation') is-invalid  @enderror" placeholder="Confirm new password">
                                                @error('new_password_confirmation')
                                                <span class="text-danger">{{$message}}</span>
                                                @enderror
                                            </div>
                                            <div class="d-flex justify-content-end">
                                                <button type="submit" class="btn glass-primary-btn security-action-btn">Update Password</button>
                                            </div>
                                            </form>
                                        </div>
                                        
                                        <div class="security-item mb-4">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6 class="mb-0">Two-Factor Authentication</h6>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input enhanced-switch" type="checkbox" id="twoFactor" checked>
                                                </div>
                                            </div>
                                            <p class="text-muted small">Add an extra layer of security to your account</p>
                                            <div class="d-flex gap-2">
                                                <button class="btn glass-outline-btn btn-sm">
                                                    <i class="bi bi-phone me-1"></i>SMS
                                                </button>
                                                <button class="btn glass-outline-btn btn-sm">
                                                    <i class="bi bi-google me-1"></i>Authenticator
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div class="security-item">
                                            <h6 class="mb-3">Active Sessions</h6>
                                            <div class="session-item">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="d-flex align-items-center">
                                                        <i class="bi bi-laptop text-primary me-2"></i>
                                                        <div>
                                                            <div class="fw-bold">Current Session</div>
                                                            <small class="text-muted">Chrome on Windows • Now</small>
                                                        </div>
                                                    </div>
                                                    <span class="badge enhanced-badge-primary">Active</span>
                                                </div>
                                            </div>
                                            <div class="session-item mt-3">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="d-flex align-items-center">
                                                        <i class="bi bi-phone text-muted me-2"></i>
                                                        <div>
                                                            <div class="fw-bold">Mobile Device</div>
                                                            <small class="text-muted">Safari on iPhone • 2 days ago</small>
                                                        </div>
                                                    </div>
                                                    <button class="btn btn-sm glass-outline-btn">Revoke</button>
                                                </div>
                                            </div>
                                            <div class="text-end mt-3">
                                                <button class="btn glass-outline-btn btn-sm">
                                                    <i class="bi bi-box-arrow-right me-1"></i>Sign Out All Devices
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                               
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <script type="text/javascript">
    $(document).ready(function(){
        $('#image').change(function(e){
            var reader = new FileReader();
            reader.onload = function(e){
                $('#showImage').attr('src',e.target.result);
            }
            reader.readAsDataURL(e.target.files['0']);
        })
    })

</script>



@endsection