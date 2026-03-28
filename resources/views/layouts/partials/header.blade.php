<header class="nxl-header">
    <div class="header-wrapper">
        <div class="header-left d-flex align-items-center gap-4">
            <a href="javascript:void(0);" class="nxl-head-mobile-toggler" id="mobile-collapse">
                <div class="hamburger hamburger--arrowturn">
                    <div class="hamburger-box">
                        <div class="hamburger-inner"></div>
                    </div>
                </div>
            </a>
            <div class="nxl-navigation-toggle">
                <a href="javascript:void(0);" id="menu-mini-button">
                    <i class="feather-align-left"></i>
                </a>
                <a href="javascript:void(0);" id="menu-expend-button" style="display: none">
                    <i class="feather-arrow-right"></i>
                </a>
            </div>
            
            {{-- Global Search Trigger --}}
            <div class="header-search-wrapper d-none d-md-flex">
                <div class="search-form-wrapper">
                    <form action="javascript:void(0);" class="search-form">
                        <div class="input-group search-form-group border rounded px-2" style="background: rgba(0,0,0,0.02);">
                            <span class="input-group-text border-0 ps-2 bg-transparent">
                                <i class="feather-search text-muted"></i>
                            </span>
                            <input type="text" class="form-control border-0 shadow-none bg-transparent fs-13" 
                                placeholder="Search Navigation (Dash, POS, etc...)" 
                                style="width: 300px; cursor: pointer;"
                                data-bs-toggle="modal" data-bs-target="#searchModal" readonly>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="header-right ms-auto">
            <div class="d-flex align-items-center gap-2">
                {{-- Subscription Timer --}}
                @php
                    $company = auth()->user()->company;
                    $daysLeft = $company && $company->trial_ends_at ? now()->diffInHours($company->trial_ends_at, false) / 24 : 0;
                    $daysLeftInt = max(0, ceil($daysLeft));
                    $isExpiringSoon = $daysLeftInt <= 7;
                @endphp

                @if($company)
                    <div class="d-none d-lg-flex align-items-center me-3 border rounded-3 p-1 bg-white shadow-sm border-light">
                        <div class="avatar-text avatar-md bg-soft-primary text-primary rounded-3 me-2">
                            <i class="feather-zap fs-6"></i>
                        </div>
                        <div class="me-3">
                            <span class="fs-10 fw-bold text-uppercase text-muted ls-1 d-block mb-0">Current Plan</span>
                            <span class="fs-12 fw-bolder text-dark">{{ $company->plan->name ?? 'Enterprise Pro' }}</span>
                        </div>
                        <div class="border-start ps-3 py-1 me-2 text-end">
                            <span class="fs-10 fw-bold text-uppercase {{ $isExpiringSoon ? 'text-danger pulse-once' : 'text-success' }} ls-1 d-block mb-0">
                                {{ $daysLeftInt > 0 ? $daysLeftInt . ' Days Left' : 'Expired' }}
                            </span>
                            <span class="fs-12 fw-medium text-muted">Active Trial</span>
                        </div>
                    </div>
                @endif

                <div class="nxl-h-item d-none d-sm-flex">
                    <div class="full-screen-switcher">
                        <a href="javascript:void(0);" class="nxl-head-link me-0"
                            onclick="$('body').fullScreenHelper('toggle');">
                            <i class="feather-maximize maximize"></i>
                            <i class="feather-minimize minimize"></i>
                        </a>
                    </div>
                </div>

                <div class="nxl-h-item dark-light-theme">
                    <a href="javascript:void(0);" class="nxl-head-link me-0 dark-button">
                        <i class="feather-moon"></i>
                    </a>
                    <a href="javascript:void(0);" class="nxl-head-link me-0 light-button" style="display: none">
                        <i class="feather-sun"></i>
                    </a>
                </div>

                <div class="dropdown nxl-h-item">
                    <a href="javascript:void(0);" data-bs-toggle="dropdown" role="button" data-bs-auto-close="outside">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=3b71ca&color=fff&bold=true" alt="user-image"
                            class="img-fluid user-avtar me-0 rounded-2 border shadow-sm" style="width: 38px; height: 38px;" />
                    </a>
                    <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-user-dropdown shadow-lg border-0 rounded-3 overflow-hidden">
                        <div class="dropdown-header p-4" style="background: linear-gradient(135deg, rgba(59,113,202,0.05) 0%, rgba(124,58,237,0.05) 100%);">
                            <div class="d-flex align-items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=3b71ca&color=fff&bold=true" alt="user-image"
                                    class="img-fluid user-avtar rounded-2 border border-white border-4" style="width:50px; height:50px;" />
                                <div>
                                    <h6 class="text-dark fw-bold mb-0 fs-14">{{ auth()->user()->name }} 
                                        <span class="badge bg-soft-success text-success ms-1 fs-9">PRO</span>
                                    </h6>
                                    <span class="fs-12 fw-medium text-muted">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="p-2">
                            <a href="{{ route('lab.profile') }}" wire:navigate class="dropdown-item rounded-3 py-2 px-3 transition-all">
                                <i class="feather-user me-2 text-primary"></i>
                                <span class="fw-medium">Profile Details</span>
                            </a>
                            <a href="{{ route('lab.settings') }}" wire:navigate class="dropdown-item rounded-3 py-2 px-3 transition-all">
                                <i class="feather-settings me-2 text-primary"></i>
                                <span class="fw-medium">Account Settings</span>
                            </a>
                            <div class="dropdown-divider mx-3"></div>
                            <form method="POST" action="{{ route('logout') }}" id="logout-form-header">
                                @csrf
                                <button type="submit" class="dropdown-item rounded-3 py-2 px-3 text-danger transition-all">
                                    <i class="feather-log-out me-2"></i>
                                    <span class="fw-bold">Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Revert to box aesthetics for user avatar */
        .user-avtar.rounded-2 { border-radius: 6px !important; }
        
        /* Modal Backdrop & Global Blur: Remove blur effect */
        .modal-backdrop.show {
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
            background-color: rgba(0, 0, 0, 0.4) !important;
        }

        body.modal-open .nxl-container, 
        body.modal-open .nxl-navigation, 
        body.modal-open .nxl-header {
            filter: none !important;
            backdrop-filter: none !important;
        }

        /* Search Trigger Refinement */
        .search-form-group {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(0,0,0,0.1) !important;
        }
        .search-form-group:hover, .search-form-group:focus-within {
            border-color: var(--bs-primary) !important;
            background: white !important;
            box-shadow: 0 4px 15px rgba(var(--bs-primary-rgb), 0.1) !important;
            transform: scale(1.02);
        }

        .avatar-text.rounded-3 { border-radius: 10px !important; transition: all 0.3s ease; }
        .ls-2 { letter-spacing: 1px; }
        .transition-all { transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); }
        
        /* Dropdown Alignment: Ensure no gap between toggle and menu */
        .nxl-user-dropdown {
            margin-top: 5px !important;
            border: 1px solid rgba(0,0,0,0.05) !important;
        }

        /* Pulse Animation for Expiring Subscription */
        .pulse-once {
            animation: pulse-red 2s infinite;
        }
        @keyframes pulse-red {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); color: #dc3545; }
            100% { transform: scale(1); }
        }
    </style>
</header>
