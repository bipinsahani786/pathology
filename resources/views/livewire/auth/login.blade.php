<div class="auth-cover-wrapper bg-white">
    <div class="d-flex flex-column flex-lg-row min-vh-100">
        
        <div class="auth-cover-sidebar d-none d-lg-flex flex-column align-items-center justify-content-center w-50 position-relative overflow-hidden">
            
            <div class="position-absolute top-0 start-0 w-100 h-100" style="background: radial-gradient(circle at top left, #f1f5f9 0%, #e2e8f0 100%); z-index: 0;"></div>
            
            <div class="auth-cover-content text-center p-5 position-relative" style="z-index: 1;">
                
                <div class="hero-icon-composition position-relative mx-auto mb-5" style="width: 200px; height: 200px;">
                    <div class="main-icon-circle bg-white shadow-lg rounded-circle d-flex align-items-center justify-content-center position-absolute top-50 start-50 translate-middle z-3" style="width: 120px; height: 120px;">
                        <i class="feather-activity text-primary" style="font-size: 3.5rem;"></i>
                    </div>
                    
                    <div class="floating-icon icon-1 bg-white shadow-sm rounded-circle d-flex align-items-center justify-content-center position-absolute z-2">
                        <i class="feather-file-text text-success fs-5"></i>
                    </div>
                    <div class="floating-icon icon-2 bg-white shadow-sm rounded-circle d-flex align-items-center justify-content-center position-absolute z-2">
                        <i class="feather-users text-info fs-5"></i>
                    </div>
                    <div class="floating-icon icon-3 bg-white shadow-sm rounded-circle d-flex align-items-center justify-content-center position-absolute z-2">
                        <i class="feather-pie-chart text-warning fs-5"></i>
                    </div>
                </div>

                <div class="mt-4 animate-fade-in-up">
                    <h3 class="fw-bolder text-dark mb-3" style="letter-spacing: -0.5px;">Startup Web Support SaaS</h3>
                    <p class="text-muted mx-auto fs-15 lh-lg" style="max-width: 420px;">
                        Manage your laboratory reports, patients, and financial data with our secure, all-in-one enterprise diagnostic solution.
                    </p>
                </div>
            </div>

            <div class="position-absolute bottom-0 start-0 p-4 w-100 text-center" style="z-index: 1;">
                <span class="fs-12 text-muted fw-medium text-uppercase tracking-wide">© 2026 Startup Web Support. All rights reserved.</span>
            </div>
        </div>

        <div class="auth-cover-form-inner d-flex align-items-center justify-content-center w-100 w-lg-50 p-4 p-md-5 bg-white">
            <div class="w-100 animate-fade-in" style="max-width: 420px;">
                
                <div class="mb-5 d-flex align-items-center gap-3 d-lg-none">
                    <div class="bg-primary rounded-4 p-2 d-inline-flex shadow-sm">
                        <i class="feather-activity text-white fs-3"></i>
                    </div>
                    <span class="fw-bolder fs-4 tracking-tight text-dark">SWS Pathology</span>
                </div>

                <div class="mb-5">
                    <h2 class="fw-bolder text-dark mb-2" style="letter-spacing: -1px; font-size: 2rem;">Welcome Back</h2>
                    <p class="text-muted fs-14">Please enter your credentials to access the laboratory dashboard.</p>
                </div>

                <form wire:submit.prevent="login" class="mt-4">
                    
                    <div class="mb-4 position-relative">
                        <label class="form-label fw-bold text-muted fs-11 text-uppercase tracking-wide mb-2">Email or Phone Number</label>
                        <div class="position-relative">
                            <i class="feather-user position-absolute top-50 translate-middle-y text-muted" style="left: 16px;"></i>
                            <input type="text" 
                                class="form-control form-control-lg premium-input @error('email') is-invalid @enderror" 
                                placeholder="Email or 10-digit Phone" 
                                wire:model="email" required>
                        </div>
                        @error('email') <span class="text-danger fs-11 mt-1 fw-medium"><i class="feather-alert-circle me-1"></i>{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4 position-relative">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-bold text-muted fs-11 text-uppercase tracking-wide mb-0">Password</label>
                            <a href="#" class="fs-12 fw-bold text-primary text-decoration-none transition-all hover-opacity">Forgot password?</a>
                        </div>
                        <div class="position-relative">
                            <i class="feather-lock position-absolute top-50 translate-middle-y text-muted" style="left: 16px;"></i>
                            <input type="password" 
                                class="form-control form-control-lg premium-input @error('password') is-invalid @enderror" 
                                placeholder="••••••••" 
                                wire:model="password" required>
                        </div>
                        @error('password') <span class="text-danger fs-11 mt-1 fw-medium"><i class="feather-alert-circle me-1"></i>{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4 d-flex align-items-center">
                        <div class="form-check custom-checkbox m-0">
                            <input type="checkbox" class="form-check-input border-secondary" id="rememberMe" wire:model="remember" style="cursor: pointer;">
                            <label class="form-check-label fs-13 text-dark fw-medium mt-1 ms-1" for="rememberMe" style="cursor: pointer;">Keep me securely logged in</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold py-3 shadow-sm rounded-4 transition-all hover-lift d-flex justify-content-center align-items-center gap-2 mt-2">
                        <span wire:loading.remove>Access Dashboard</span>
                        <i wire:loading.remove class="feather-arrow-right"></i>
                        <span wire:loading class="spinner-border spinner-border-sm"></span>
                        <span wire:loading>Authenticating...</span>
                    </button>
                </form>

                {{-- <div class="mt-5">
                    <div class="position-relative text-center mb-4">
                        <hr class="text-light">
                        <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted fs-11 text-uppercase fw-bold tracking-wide">Or connect with</span>
                    </div>
                    
                    <div class="d-flex gap-3">
                        <button class="btn btn-light bg-white border flex-fill py-2 rounded-3 shadow-sm transition-all hover-bg-light"><i class="feather-google text-danger"></i></button>
                        <button class="btn btn-light bg-white border flex-fill py-2 rounded-3 shadow-sm transition-all hover-bg-light"><i class="feather-github text-dark"></i></button>
                    </div>
                </div> --}}

                <div class="mt-5 text-center">
                    <p class="fs-14 text-muted">New to the platform? <a href="{{ route('register.lab') }}" wire:navigate class="fw-bold text-primary text-decoration-none border-bottom border-primary pb-1 transition-all hover-opacity">Create your Lab Account</a></p>
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
        .hover-bg-light:hover { background-color: #f8fafc !important; }

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