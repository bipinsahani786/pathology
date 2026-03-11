<div class="auth-cover-wrapper bg-white">
    <div class="d-flex flex-column flex-lg-row min-vh-100">
        
        <div class="auth-cover-sidebar d-none d-lg-flex align-items-center justify-content-center bg-light w-50 position-relative">
            <div class="auth-cover-content text-center p-5">
                <img src="https://img.freepik.com/free-vector/medical-technology-science-background-vector-blue-with-blank-space_53876-117739.jpg" 
                     alt="Auth Cover" class="img-fluid auth-img animate__animated animate__fadeIn">
                <div class="mt-4">
                    <h3 class="fw-bold text-dark">Zytrixon Pathology SaaS</h3>
                    <p class="text-muted mx-auto" style="max-width: 400px;">
                        Manage your laboratory reports, patients, and financial data with our all-in-one enterprise solution.
                    </p>
                </div>
            </div>
            <div class="position-absolute bottom-0 start-0 p-4">
                <span class="fs-12 text-muted fw-medium">© 2026 Zytrixon Labs</span>
            </div>
        </div>

        <div class="auth-cover-form-inner d-flex align-items-center justify-content-center w-100 w-lg-50 p-4 p-md-5">
            <div class="w-100" style="max-width: 420px;">
                <div class="mb-5 d-flex align-items-center gap-2">
                    <div class="bg-primary rounded-3 p-2 d-inline-block shadow-sm">
                        <i class="feather-activity text-white fs-20"></i>
                    </div>
                    <span class="fw-bold fs-20 tracking-tight text-dark">Zytrixon</span>
                </div>

                <div class="mb-4">
                    <h2 class="fw-bolder text-dark mb-1">Login</h2>
                    <p class="text-muted fs-13">Welcome back! Please enter your details.</p>
                </div>

                <form wire:submit.prevent="login" class="mt-4">
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark fs-12 text-uppercase">Email or Username</label>
                        <input type="email" 
                            class="form-control form-control-lg fs-14 @error('email') is-invalid @enderror" 
                            placeholder="admin@lab.com" 
                            wire:model="email" required>
                        @error('email') <span class="text-danger fs-11 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <label class="form-label fw-bold text-dark fs-12 text-uppercase">Password</label>
                            <a href="#" class="fs-11 fw-bold text-primary text-decoration-none">Forgot password?</a>
                        </div>
                        <input type="password" 
                            class="form-control form-control-lg fs-14 @error('password') is-invalid @enderror" 
                            placeholder="••••••••" 
                            wire:model="password" required>
                        @error('password') <span class="text-danger fs-11 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <div class="form-check custom-checkbox">
                            <input type="checkbox" class="form-check-input" id="rememberMe" wire:model="remember">
                            <label class="form-check-label fs-13 text-muted" for="rememberMe">Keep me logged in</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold py-3 shadow-sm">
                        <span wire:loading.remove>Sign In</span>
                        <span wire:loading class="spinner-border spinner-border-sm"></span>
                    </button>
                </form>

                <div class="mt-5">
                    <div class="position-relative text-center mb-4">
                        <hr class="text-light">
                        <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted fs-11 text-uppercase">Or connect with</span>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button class="btn btn-light border flex-fill py-2"><i class="feather-facebook text-primary"></i></button>
                        <button class="btn btn-light border flex-fill py-2"><i class="feather-twitter text-info"></i></button>
                        <button class="btn btn-light border flex-fill py-2"><i class="feather-github text-dark"></i></button>
                    </div>
                </div>

                <div class="mt-5 text-center">
                    <p class="fs-13 text-muted">Don't have an account? <a href="{{ route('register.lab') }}" wire:navigate class="fw-bold text-primary text-decoration-none">Create Account</a></p>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Cover Style Core CSS */
        .auth-cover-wrapper {
            min-height: 100vh;
        }

        /* Left Sidebar Background */
        .auth-cover-sidebar {
            background-color: #f8fafc; /* Professional off-white/light-grey */
            border-right: 1px solid #e2e8f0;
        }

        .auth-img {
            max-width: 100%;
            height: auto;
            max-height: 400px;
        }

        /* Input Styling */
        .form-control-lg {
            border-radius: 8px !important;
            border: 1.5px solid #e2e8f0;
            padding: 12px 16px;
            transition: all 0.2s ease-in-out;
        }

        .form-control-lg:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1);
        }

        /* Button Styling */
        .btn-lg {
            border-radius: 8px !important;
        }

        .tracking-tight { letter-spacing: -0.5px; }

        /* Ensuring mobile responsiveness */
        @media (max-width: 991px) {
            .auth-cover-form-inner {
                background-color: #ffffff;
            }
        }
    </style>
</div>