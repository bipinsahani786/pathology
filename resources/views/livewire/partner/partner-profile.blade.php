<div>
    <div class="page-header mb-4">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10 fw-bold text-dark">My Profile</h5>
                <p class="fs-13 text-muted mb-0 font-medium">Manage your personal details and security settings.</p>
            </div>
            <ul class="breadcrumb d-none d-md-flex ms-3">
                <li class="breadcrumb-item"><a href="{{ url()->previous() }}" wire:navigate class="text-muted small">Home</a></li>
                <li class="breadcrumb-item text-primary fw-medium small">Account Settings</li>
            </ul>
        </div>
    </div>

    <div class="main-content">
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 h-100">
                    <div class="card-header bg-white border-bottom-0 pt-3 px-4 pb-0">
                        <h5 class="card-title mb-1 fw-bold text-dark"><i class="feather-user me-2 text-primary"></i>Personal Information</h5>
                        <p class="text-muted small mb-0">Update your primary contact and identification details</p>
                    </div>
                    <div class="card-body px-4 pb-4">
                        @if (session()->has('success'))
                            <div class="alert alert-soft-success border-0 rounded-3 mb-4 d-flex align-items-center py-2 px-3">
                                <i class="feather-check-circle me-2 fs-14"></i>
                                <span class="fs-13 fw-medium">{{ session('success') }}</span>
                            </div>
                        @endif

                        <form wire:submit.prevent="updateProfile" class="row g-3">
                            <!-- Profile Photo Section -->
                            <div class="col-12 mb-4">
                                <label class="form-label fw-bold text-dark fs-12 mb-3 d-block">Profile Photo</label>
                                <div class="d-flex align-items-center gap-4">
                                    <div class="position-relative profile-upload-container">
                                        <div class="avatar-preview-wrapper border-dashed border-2 rounded-circle p-1">
                                            @if ($new_photo)
                                                <img src="{{ $new_photo->temporaryUrl() }}" class="avatar-initials rounded-circle border object-fit-cover shadow-sm" style="width: 100px; height: 100px;">
                                            @elseif ($profile_photo_url)
                                                <img src="{{ $profile_photo_url }}" class="avatar-initials rounded-circle border object-fit-cover shadow-sm" style="width: 100px; height: 100px;">
                                            @else
                                                <div class="avatar-text bg-soft-primary text-primary rounded-circle border shadow-sm fw-black" style="width: 100px; height: 100px; font-size: 2rem;">
                                                    {{ auth()->user()->initials() }}
                                                </div>
                                            @endif
                                        </div>
                                        <label for="profilePhotoInput" class="position-absolute bottom-0 end-0 bg-primary text-white p-2 rounded-circle shadow-lg cursor-pointer hover-scale transition-all" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border: 3px solid white;">
                                            <i class="feather-camera fs-12"></i>
                                            <input type="file" id="profilePhotoInput" wire:model="new_photo" class="d-none" accept="image/*">
                                        </label>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="fw-bold text-dark mb-1 fs-14">Your Avatar</h6>
                                        <p class="text-muted small mb-2 font-medium">Clear photos with 1:1 aspect ratio work best.</p>
                                        <div wire:loading wire:target="new_photo" class="text-primary small fw-bold">
                                            <span class="spinner-border spinner-border-sm me-1" role="status"></span> Uploading...
                                        </div>
                                        @error('new_photo') <span class="text-danger fs-11 mt-1 d-block fw-bold">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold text-dark fs-12 mb-1">Full Name</label>
                                <div class="input-group shadow-sm border rounded-3 overflow-hidden bg-white">
                                    <span class="input-group-text bg-light border-0"><i class="feather-user text-muted fs-12"></i></span>
                                    <input type="text" wire:model="name" class="form-control border-0 shadow-none fs-13 fw-medium py-2" placeholder="Enter your full name">
                                </div>
                                @error('name') <span class="text-danger fs-11 mt-1 d-block fw-bold">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark fs-12 mb-1">Email Address</label>
                                <div class="input-group shadow-sm border rounded-3 overflow-hidden bg-white">
                                    <span class="input-group-text bg-light border-0"><i class="feather-mail text-muted fs-12"></i></span>
                                    <input type="email" wire:model="email" class="form-control border-0 shadow-none fs-13 fw-medium py-2" placeholder="email@example.com">
                                </div>
                                @error('email') <span class="text-danger fs-11 mt-1 d-block fw-bold">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark fs-12 mb-1">Mobile Number</label>
                                <div class="input-group shadow-sm border rounded-3 overflow-hidden bg-white">
                                    <span class="input-group-text bg-light border-0"><i class="feather-phone text-muted fs-12"></i></span>
                                    <input type="text" wire:model="phone" class="form-control border-0 shadow-none fs-13 fw-medium py-2" placeholder="10-digit mobile">
                                </div>
                                @error('phone') <span class="text-danger fs-11 mt-1 d-block fw-bold">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-12 mt-4 pt-2">
                                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm transition-all hover-scale d-flex align-items-center gap-2">
                                    <i class="feather-save"></i> Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-3 h-100">
                    <div class="card-header bg-white border-bottom-0 pt-3 px-4 pb-0">
                        <h5 class="card-title mb-1 fw-bold text-dark"><i class="feather-lock me-2 text-warning"></i>Security</h5>
                        <p class="text-muted small mb-0">Secure your account</p>
                    </div>
                    <div class="card-body px-4 pb-4">
                        @if (session()->has('password_success'))
                            <div class="alert alert-soft-success border-0 rounded-3 mb-4 d-flex align-items-center py-2 px-3">
                                <i class="feather-check-circle me-2 fs-14"></i>
                                <span class="fs-13 fw-medium">{{ session('password_success') }}</span>
                            </div>
                        @endif

                        <form wire:submit.prevent="updatePassword" class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-bold text-dark fs-12 mb-1">New Password</label>
                                <div class="input-group shadow-sm border rounded-3 overflow-hidden bg-white">
                                    <span class="input-group-text bg-light border-0"><i class="feather-key text-muted fs-12"></i></span>
                                    <input type="password" wire:model="password" class="form-control border-0 shadow-none fs-13 fw-medium py-2" placeholder="Minimum 6 characters">
                                </div>
                                @error('password') <span class="text-danger fs-11 mt-1 d-block fw-bold">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold text-dark fs-12 mb-1">Confirm Password</label>
                                <div class="input-group shadow-sm border rounded-3 overflow-hidden bg-white">
                                    <span class="input-group-text bg-light border-0"><i class="feather-shield text-muted fs-12"></i></span>
                                    <input type="password" wire:model="password_confirmation" class="form-control border-0 shadow-none fs-13 fw-medium py-2" placeholder="Repeat new password">
                                </div>
                            </div>

                            <div class="col-12 mt-3 pt-1">
                                <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold text-white shadow-sm transition-all hover-scale d-flex align-items-center gap-2">
                                    <i class="feather-refresh-cw fs-12"></i> Update Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-soft-primary border-primary border-opacity-10">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar-text bg-primary text-white rounded-circle shadow-sm" style="width: 32px; height: 32px; font-size: 14px;">
                                <i class="feather-shield"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0 fs-13">Two-Factor Authentication</h6>
                                <p class="text-muted small mb-0 font-medium fs-11">Coming soon for extra security.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .font-medium { font-weight: 500; }
        .bg-soft-success { background-color: rgba(16, 185, 129, 0.08) !important; color: #10b981 !important; }
        .bg-soft-primary { background-color: rgba(59, 113, 202, 0.08) !important; }
        .text-primary { color: #3b71ca !important; }
        .text-warning { color: #f59e0b !important; }
        
        .transition-all { transition: all 0.2s ease-in-out; }
        .hover-scale:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important; }
        
        .avatar-text { width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
        
        .stretch { height: auto; }
        .stretch-full { height: 100%; }
        .h-100 { height: 100% !important; }
        
        input::placeholder { font-size: 12px; opacity: 0.5; font-weight: 400; }
    </style>
</div>
