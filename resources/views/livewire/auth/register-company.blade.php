<x-slot:title>
    Create Workspace | Pathology Lab Management Software
</x-slot:title>
<div class="auth-cover-wrapper bg-white">
    <div class="d-flex flex-column flex-lg-row min-vh-100">
        
        <div class="auth-cover-sidebar d-none d-lg-flex flex-column align-items-center justify-content-center w-50 position-relative overflow-hidden">
            
            <div class="position-absolute top-0 start-0 w-100 h-100" style="background: radial-gradient(circle at bottom right, #f1f5f9 0%, #e2e8f0 100%); z-index: 0;"></div>
            
            <div class="auth-cover-content text-center p-5 position-relative" style="z-index: 1;">
                
                <div class="hero-icon-composition position-relative mx-auto mb-5" style="width: 200px; height: 200px;">
                    <div class="main-icon-circle bg-white shadow-lg rounded-circle d-flex align-items-center justify-content-center position-absolute top-50 start-50 translate-middle z-3" style="width: 120px; height: 120px;">
                        <i class="feather-layers text-primary" style="font-size: 3.5rem;"></i>
                    </div>
                    
                    <div class="floating-icon icon-1 bg-white shadow-sm rounded-circle d-flex align-items-center justify-content-center position-absolute z-2">
                        <i class="feather-home text-success fs-5"></i>
                    </div>
                    <div class="floating-icon icon-2 bg-white shadow-sm rounded-circle d-flex align-items-center justify-content-center position-absolute z-2">
                        <i class="feather-user-check text-info fs-5"></i>
                    </div>
                    <div class="floating-icon icon-3 bg-white shadow-sm rounded-circle d-flex align-items-center justify-content-center position-absolute z-2">
                        <i class="feather-trending-up text-warning fs-5"></i>
                    </div>
                </div>

                <div class="mt-4 animate-fade-in-up">
                    <h3 class="fw-bolder text-dark mb-3" style="letter-spacing: -0.5px;">Grow Your Lab with Zytrixon</h3>
                    <p class="text-muted mx-auto fs-15 lh-lg" style="max-width: 420px;">
                        Manage patients, automate reports, and track billing effortlessly. <strong>Start your free trial today</strong> and digitize your diagnostic center.
                    </p>
                </div>
            </div>

            <div class="position-absolute bottom-0 start-0 p-4 w-100 text-center" style="z-index: 1;">
                <span class="fs-12 text-muted fw-medium text-uppercase tracking-wide">© 2026 Zytrixon Pathology SaaS.</span>
            </div>
        </div>

        <div class="auth-cover-form-inner d-flex align-items-center justify-content-center w-100 w-lg-50 p-4 p-md-5 bg-white">
            <div class="w-100 animate-fade-in" style="max-width: 480px;">
                
                <div class="mb-5 d-flex align-items-center gap-3 d-lg-none">
                    <div class="bg-primary rounded-4 p-2 d-inline-flex shadow-sm">
                        <i class="feather-activity text-white fs-3"></i>
                    </div>
                    <span class="fw-bolder fs-4 tracking-tight text-dark">Zytrixon</span>
                </div>

                <div class="mb-4">
                    <h2 class="fw-bolder text-dark mb-2" style="letter-spacing: -1px; font-size: 2rem;">Create Workspace</h2>
                    <p class="text-muted fs-14">Register your laboratory to get started in seconds.</p>
                </div>

                <form wire:submit.prevent="register" class="mt-4">
                    
                    <div class="mb-3 position-relative">
                        <label class="form-label fw-bold text-muted fs-11 text-uppercase tracking-wide mb-2">Clinic / Lab Name <span class="text-danger">*</span></label>
                        <div class="position-relative">
                            <i class="feather-briefcase position-absolute top-50 translate-middle-y text-muted" style="left: 16px;"></i>
                            <input type="text" 
                                class="form-control form-control-lg premium-input @error('lab_name') is-invalid @enderror" 
                                wire:model="lab_name" placeholder="e.g. City Care Pathology">
                        </div>
                        @error('lab_name') <span class="text-danger fs-11 mt-1 fw-medium"><i class="feather-alert-circle me-1"></i>{{ $message }}</span> @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6 position-relative">
                            <label class="form-label fw-bold text-muted fs-11 text-uppercase tracking-wide mb-2">Your Name <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <i class="feather-user position-absolute top-50 translate-middle-y text-muted" style="left: 16px;"></i>
                                <input type="text" 
                                    class="form-control form-control-lg premium-input @error('owner_name') is-invalid @enderror" 
                                    wire:model="owner_name" placeholder="John Doe">
                            </div>
                            @error('owner_name') <span class="text-danger fs-11 mt-1 fw-medium"><i class="feather-alert-circle me-1"></i>{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6 position-relative">
                            <label class="form-label fw-bold text-muted fs-11 text-uppercase tracking-wide mb-2">Phone Number <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <i class="feather-phone position-absolute top-50 translate-middle-y text-muted" style="left: 16px;"></i>
                                <input type="text" 
                                    class="form-control form-control-lg premium-input @error('phone') is-invalid @enderror" 
                                    wire:model="phone" placeholder="9876543210">
                            </div>
                            @error('phone') <span class="text-danger fs-11 mt-1 fw-medium"><i class="feather-alert-circle me-1"></i>{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="mb-3 position-relative">
                        <label class="form-label fw-bold text-muted fs-11 text-uppercase tracking-wide mb-2">Email Address <span class="text-danger">*</span></label>
                        <div class="position-relative">
                            <i class="feather-mail position-absolute top-50 translate-middle-y text-muted" style="left: 16px;"></i>
                            <input type="email" 
                                class="form-control form-control-lg premium-input @error('email') is-invalid @enderror" 
                                wire:model="email" placeholder="admin@citycare.com">
                        </div>
                        @error('email') <span class="text-danger fs-11 mt-1 fw-medium"><i class="feather-alert-circle me-1"></i>{{ $message }}</span> @enderror
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6 position-relative">
                            <label class="form-label fw-bold text-muted fs-11 text-uppercase tracking-wide mb-2">Password <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <i class="feather-lock position-absolute top-50 translate-middle-y text-muted" style="left: 16px;"></i>
                                <input type="password" 
                                    class="form-control form-control-lg premium-input @error('password') is-invalid @enderror" 
                                    wire:model="password" placeholder="••••••••">
                            </div>
                            @error('password') <span class="text-danger fs-11 mt-1 fw-medium"><i class="feather-alert-circle me-1"></i>{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6 position-relative">
                            <label class="form-label fw-bold text-muted fs-11 text-uppercase tracking-wide mb-2">Confirm Password</label>
                            <div class="position-relative">
                                <i class="feather-shield position-absolute top-50 translate-middle-y text-muted" style="left: 16px;"></i>
                                <input type="password" 
                                    class="form-control form-control-lg premium-input" 
                                    wire:model="password_confirmation" placeholder="••••••••">
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="form-check custom-checkbox m-0 d-flex align-items-start">
                            <input type="checkbox" class="form-check-input border-secondary mt-1 @error('agree_terms') is-invalid @enderror" id="agreeTerms" wire:model="agree_terms" style="cursor: pointer;">
                            <label class="form-check-label fs-13 text-muted ms-2 lh-sm" for="agreeTerms" style="cursor: pointer;">
                                I agree to the <a href="#" class="text-primary text-decoration-none fw-bold transition-all hover-opacity">Terms of Service</a> and <a href="#" class="text-primary text-decoration-none fw-bold transition-all hover-opacity">Privacy Policy</a>.
                            </label>
                        </div>
                        @error('agree_terms') <span class="text-danger fs-11 mt-1 fw-medium d-block"><i class="feather-alert-circle me-1"></i>{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold py-3 shadow-sm rounded-4 transition-all hover-lift d-flex justify-content-center align-items-center gap-2">
                        <span wire:loading.remove wire:target="register">Start Free Trial</span>
                        <i wire:loading.remove wire:target="register" class="feather-arrow-right"></i>
                        <span wire:loading wire:target="register" class="spinner-border spinner-border-sm"></span>
                        <span wire:loading wire:target="register">Creating Workspace...</span>
                    </button>
                </form>

                <div class="mt-5 text-center">
                    <p class="fs-14 text-muted">Already have an account? <a href="{{ route('login') }}" wire:navigate class="fw-bold text-primary text-decoration-none border-bottom border-primary pb-1 transition-all hover-opacity">Log in here</a></p>
                </div>
            </div>
        </div>
    </div>
    
    <style>
        .auth-cover-wrapper { min-height: 100vh; background-color: #ffffff; }

        /* Left Sidebar & Icons */
        .auth-cover-sidebar { border-right: 1px solid #e2e8f0; }
        
        .floating-icon {
            width: 48px; height: 48px;
            animation: float 4s ease-in-out infinite;
        }
        .icon-1 { top: 0; left: 10px; animation-delay: 0s; }
        .icon-2 { bottom: 20px; right: 0; animation-delay: 1.5s; }
        .icon-3 { bottom: -10px; left: 30px; animation-delay: 2.5s; }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-12px); }
            100% { transform: translateY(0px); }
        }

        /* Typography & Utilities */
        .tracking-wide { letter-spacing: 0.5px; }
        .tracking-tight { letter-spacing: -0.5px; }
        .transition-all { transition: all 0.25s ease; }
        .hover-lift:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(13, 110, 253, 0.2) !important; }
        .hover-opacity:hover { opacity: 0.8; }

        /* Premium Form Inputs */
        .premium-input {
            border-radius: 10px !important;
            border: 1.5px solid #e2e8f0;
            padding: 14px 16px 14px 45px; /* Extra left padding for the icon */
            font-size: 14px;
            font-weight: 500;
            color: #1e293b;
            background-color: #ffffff;
            transition: all 0.2s ease-in-out;
        }

        .premium-input::placeholder {
            color: #94a3b8;
            font-weight: 400;
        }

        .premium-input:focus {
            border-color: #0d6efd;
            background-color: #ffffff;
            box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.12);
        }

        /* Animations */
        .animate-fade-in { animation: fadeIn 0.8s ease-out forwards; }
        .animate-fade-in-up { animation: fadeInUp 0.8s ease-out forwards; }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</div>