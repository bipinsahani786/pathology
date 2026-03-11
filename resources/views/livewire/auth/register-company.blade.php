<div class="auth-cover-wrapper bg-white">
    <div class="d-flex flex-column flex-lg-row min-vh-100">
        
        <div class="auth-cover-sidebar d-none d-lg-flex align-items-center justify-content-center bg-light w-50 position-relative">
            <div class="auth-cover-content text-center p-5">
                <img src="https://img.freepik.com/free-vector/scientists-working-lab-illustration_23-2148496417.jpg" 
                     alt="Lab Setup" class="img-fluid auth-img rounded-4 shadow-sm mb-4" style="max-height: 350px;">
                <div class="mt-4">
                    <h3 class="fw-bold text-dark">Grow Your Lab with Zytrixon</h3>
                    <p class="text-muted mx-auto fs-14" style="max-width: 420px;">
                        Manage patients, tests, and billing effortlessly. <strong>Start your free trial today.</strong>
                    </p>
                </div>
            </div>
            <div class="position-absolute bottom-0 start-0 p-4">
                <span class="fs-12 text-muted fw-medium">© 2026 Zytrixon Pathology SaaS</span>
            </div>
        </div>

        <div class="auth-cover-form-inner d-flex align-items-center justify-content-center w-100 w-lg-50 p-4 p-md-5">
            <div class="w-100" style="max-width: 450px;">
                
                <div class="mb-4 d-flex align-items-center gap-2">
                    <div class="bg-primary rounded-3 p-2 d-inline-block shadow-sm">
                        <i class="feather-activity text-white fs-20"></i>
                    </div>
                    <span class="fw-bold fs-20 text-dark">Zytrixon</span>
                </div>

                <div class="mb-4">
                    <h3 class="fw-bolder text-dark mb-1">Create workspace</h3>
                    <p class="text-muted fs-13">Register your lab to get started.</p>
                </div>

                <form wire:submit.prevent="register">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark fs-11 text-uppercase">Clinic / Lab Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg fs-14 @error('lab_name') is-invalid @enderror" 
                               wire:model="lab_name" placeholder="e.g. City Care Pathology">
                        @error('lab_name') <span class="text-danger fs-11 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark fs-11 text-uppercase">Your Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg fs-14 @error('owner_name') is-invalid @enderror" 
                                   wire:model="owner_name" placeholder="John Doe">
                            @error('owner_name') <span class="text-danger fs-11 mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark fs-11 text-uppercase">Phone Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg fs-14 @error('phone') is-invalid @enderror" 
                                   wire:model="phone" placeholder="9876543210">
                            @error('phone') <span class="text-danger fs-11 mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark fs-11 text-uppercase">Email Address <span class="text-danger">*</span></label>
                        <input type="email" class="form-control form-control-lg fs-14 @error('email') is-invalid @enderror" 
                               wire:model="email" placeholder="admin@citycare.com">
                        @error('email') <span class="text-danger fs-11 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark fs-11 text-uppercase">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control form-control-lg fs-14 @error('password') is-invalid @enderror" 
                                   wire:model="password" placeholder="••••••••">
                            @error('password') <span class="text-danger fs-11 mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark fs-11 text-uppercase">Confirm Password</label>
                            <input type="password" class="form-control form-control-lg fs-14" 
                                   wire:model="password_confirmation" placeholder="••••••••">
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="form-check custom-checkbox">
                            <input type="checkbox" class="form-check-input @error('agree_terms') is-invalid @enderror" id="agreeTerms" wire:model="agree_terms">
                            <label class="form-check-label fs-13 text-muted" for="agreeTerms">
                                I agree to the <a href="#" class="text-primary text-decoration-none fw-medium">Terms of Service</a>.
                            </label>
                        </div>
                        @error('agree_terms') <span class="text-danger fs-11 mt-1 d-block">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold py-3 shadow-sm">
                        <span wire:loading.remove>Start Free Trial</span>
                        <span wire:loading class="spinner-border spinner-border-sm"></span>
                        <span wire:loading> Processing...</span>
                    </button>
                </form>

                <div class="mt-4 text-center">
                    <p class="fs-13 text-muted">Already have an account? <a href="{{ route('login') }}" class="fw-bold text-primary text-decoration-none">Log in here</a></p>
                </div>
            </div>
        </div>
    </div>
    
    <style>
        .auth-cover-wrapper { min-height: 100vh; }
        .auth-cover-sidebar { border-right: 1px solid #e2e8f0; }
        .form-control-lg { border-radius: 8px !important; border: 1.5px solid #e2e8f0; padding: 12px 16px; }
        .form-control-lg:focus { border-color: #0d6efd; box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1); }
    </style>
</div>