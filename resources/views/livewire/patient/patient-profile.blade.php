<div>
    <style>
        :root {
            --db-primary: #4f46e5;
            --db-bg: #f8fafc;
            --db-card-bg: #ffffff;
            --db-text-main: #1e293b;
            --db-text-muted: #64748b;
            --db-border: #e2e8f0;
            --db-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        }

        html.app-skin-dark {
            --db-bg: #0d0d1a;
            --db-card-bg: #1a1a2e;
            --db-border: rgba(255, 255, 255, 0.08);
        }

        .db-container {
            padding: 1.5rem;
            max-width: 1600px;
            margin: 0 auto;
        }

        .db-card {
            background: var(--db-card-bg);
            border: 1px solid var(--db-border);
            border-radius: 1.5rem;
            box-shadow: var(--db-shadow);
            overflow: hidden;
        }

        .profile-side-card {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            padding: 3.5rem 2rem;
            text-align: center;
            color: white;
            position: relative;
        }

        .profile-side-card::after {
            content: "";
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 56c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-27-8c3.313 0 6-2.687 6-6s-2.687-6-6-6-6 2.687-6 6 2.687 6 6 6zm28 26c3.313 0 6-2.687 6-6s-2.687-6-6-6-6 2.687-6 6 2.687 6 6 6zm56-3c3.313 0 6-2.687 6-6s-2.687-6-6-6-6 2.687-6 6 2.687 6 6 6zm-45-48c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm23 24c4.418 0 8-3.582 8-8s-3.582-8-8-8-8 3.582-8 8 3.582 8 8 8zm-46 60c4.418 0 8-3.582 8-8s-3.582-8-8-8-8 3.582-8 8 3.582 8 8 8zm0-46c5.523 0 10-4.477 10-10s-4.477-10-10-10-10 4.477-10 10 4.477 10 10 10zM66 18c5.523 0 10-4.477 10-10s-4.477-10-10-10-10 4.477-10 10 4.477 10 10 10z' fill='%23ffffff' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/svg%3E");
            opacity: 0.3;
        }

        .avatar-main {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: white;
            color: var(--db-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            font-weight: 900;
            margin: 0 auto 1.5rem;
            position: relative;
            z-index: 2;
            border: 6px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .form-control-premium {
            background-color: #f1f5f9 !important;
            border: 2px solid transparent !important;
            border-radius: 1rem !important;
            padding: 1rem 1.25rem !important;
            font-weight: 600 !important;
            transition: all 0.3s ease !important;
        }

        .form-control-premium:focus {
            background-color: white !important;
            border-color: var(--db-primary) !important;
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.08) !important;
        }

        html.app-skin-dark .form-control-premium {
            background-color: rgba(255, 255, 255, 0.03) !important;
            color: white !important;
        }
    </style>

    <div class="db-container">
        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-3">
            <div>
                <h2 class="fw-black mb-1" style="letter-spacing: -1.5px; font-size: 2.25rem;">Security & Settings</h2>
                <div class="d-flex align-items-center gap-3">
                    <span class="badge bg-soft-primary text-primary px-3 py-1 rounded-pill fw-bold fs-10 text-uppercase tracking-widest">
                        Manage Your Digital Medical Identity
                    </span>
                </div>
            </div>
        </div>

        <div class="row g-5">
            <div class="col-lg-4">
                <div class="db-card shadow-lg animate__animated animate__fadeInLeft">
                    <div class="profile-side-card">
                        <div class="avatar-main">{{ substr($name, 0, 1) }}</div>
                        <h3 class="fw-900 text-white mb-1">{{ strtoupper($name) }}</h3>
                        <p class="fs-12 text-white opacity-60 fw-bold tracking-widest mb-0 uppercase">PATIENT IDENTITY VERIFIED</p>
                    </div>
                    <div class="p-4">
                        <div class="d-flex flex-column gap-4 py-2">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-text avatar-sm bg-soft-primary text-primary rounded-3"><i class="feather-mail"></i></div>
                                <div class="overflow-hidden">
                                    <div class="fs-10 text-muted fw-bold text-uppercase tracking-widest">Registered Email</div>
                                    <div class="fs-14 fw-black text-dark text-truncate">{{ $email }}</div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-text avatar-sm bg-soft-success text-success rounded-3"><i class="feather-phone"></i></div>
                                <div>
                                    <div class="fs-10 text-muted fw-bold text-uppercase tracking-widest">Primary Contact</div>
                                    <div class="fs-14 fw-black text-dark">{{ $phone ?: 'Not provided' }}</div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-text avatar-sm bg-soft-warning text-warning rounded-3"><i class="feather-award"></i></div>
                                <div>
                                    <div class="fs-10 text-muted fw-bold text-uppercase tracking-widest">Member Year</div>
                                    <div class="fs-14 fw-black text-dark">{{ \Carbon\Carbon::parse(auth()->user()->created_at)->format('Y') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                @if (session()->has('success'))
                    <div class="alert alert-success border-0 shadow-lg rounded-4 mb-5 d-flex align-items-center p-4 animate__animated animate__fadeInDown">
                        <div class="avatar-text avatar-sm bg-success text-white rounded-circle me-3 flex-shrink-0 shadow-sm">
                            <i class="feather-check-circle"></i>
                        </div>
                        <div class="fw-black text-success">{{ session('success') }}</div>
                    </div>
                @endif

                <!-- Contact Form -->
                <div class="db-card p-5 mb-5 animate__animated animate__fadeInUp">
                    <h4 class="fw-900 text-dark mb-5 tracking-tighter"><i class="feather-user me-3 text-primary"></i>Profile Configuration</h4>
                    <form wire:submit.prevent="updateProfile">
                        <div class="row g-4">
                            <div class="col-md-12">
                                <label class="form-label fs-11 fw-black text-muted text-uppercase tracking-widest mb-3">Full Legal Name</label>
                                <input type="text" class="form-control form-control-premium @error('name') is-invalid @enderror" wire:model="name">
                                @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fs-11 fw-black text-muted text-uppercase tracking-widest mb-3">Email Account</label>
                                <input type="email" class="form-control form-control-premium @error('email') is-invalid @enderror" wire:model="email">
                                @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fs-11 fw-black text-muted text-uppercase tracking-widest mb-3">Telephone</label>
                                <input type="text" class="form-control form-control-premium @error('phone') is-invalid @enderror" wire:model="phone">
                                @error('phone') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-12 mt-5">
                                <button type="submit" class="btn btn-primary px-5 py-3 fw-900 rounded-pill shadow-lg border-0">
                                    <i class="feather-save me-2"></i>SYNCHRONIZE PROFILE
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Password Form -->
                <div class="db-card p-5 mb-5 animate__animated animate__fadeInUp" style="animation-delay: 0.2s">
                    <h4 class="fw-900 text-danger mb-5 tracking-tighter"><i class="feather-shield me-3 text-danger"></i>Security Access Keys</h4>
                    <form wire:submit.prevent="updatePassword">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fs-11 fw-black text-muted text-uppercase tracking-widest mb-3">New Master Password</label>
                                <input type="password" class="form-control form-control-premium @error('password') is-invalid @enderror" wire:model="password">
                                @error('password') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fs-11 fw-black text-muted text-uppercase tracking-widest mb-3">Confirm Security Key</label>
                                <input type="password" class="form-control form-control-premium" wire:model="password_confirmation">
                            </div>
                            <div class="col-12 mt-5">
                                <button type="submit" class="btn btn-soft-danger px-5 py-3 fw-900 rounded-pill border-0">
                                    <i class="feather-key me-2"></i>UPDATE SECURITY KEY
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
