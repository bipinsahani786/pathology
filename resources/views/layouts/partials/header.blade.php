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
            @if(!auth()->user()->hasRole('super_admin') && !auth()->user()->patientProfile)
            <div class="header-search-wrapper d-none d-md-flex">
                <div class="search-form-wrapper">
                    <form action="javascript:void(0);" class="search-form">
                        <div class="position-relative" style="width: 220px;">
                            <i class="feather-search text-muted position-absolute" style="left: 12px; top: 50%; transform: translateY(-50%); z-index: 5; font-size: 14px;"></i>
                            <input type="text" class="form-control border shadow-none fs-13" 
                                placeholder="Search Navigation (Dash, POS, etc...)" 
                                style="padding-left: 35px; height: 38px; cursor: pointer; background: rgba(0,0,0,0.02); border-radius: 8px !important;"
                                data-bs-toggle="modal" data-bs-target="#searchModal" readonly>
                        </div>
                    </form>
                </div>
            </div>
            @endif
        </div>

        <div class="header-right ms-auto">
            <div class="d-flex align-items-center gap-1 gap-md-2 gap-lg-3">
                {{-- Branch Switcher --}}
                @if(!auth()->user()->hasRole('super_admin'))
                    <livewire:lab.branch-switcher />
                @endif

                {{-- Subscription Timer --}}
                @php
                    $company = auth()->user()->company;
                    $daysLeft = $company && $company->trial_ends_at ? now()->diffInHours($company->trial_ends_at, false) / 24 : 0;
                    $daysLeftInt = max(0, ceil($daysLeft));
                    $isExpiringSoon = $daysLeftInt <= 7;
                @endphp

                @if($company && auth()->user()->hasAnyRole(['lab_admin', 'staff', 'branch_admin']))
                    <div class="d-none d-xl-flex align-items-center me-2 border rounded-3 p-1 bg-white shadow-sm border-light overflow-hidden">
                        <div class="avatar-text avatar-md bg-soft-primary text-primary rounded-3 me-2 flex-shrink-0" style="width: 32px; height: 32px; line-height: 32px;">
                            <i class="feather-zap fs-12"></i>
                        </div>
                        <div class="me-3 flex-shrink-0">
                            <span class="fs-9 fw-bold text-uppercase text-muted ls-1 d-block mb-0" style="font-size: 8px !important;">Current Plan</span>
                            <span class="fs-11 fw-bolder text-dark">{{ $company->plan->name ?? 'Professional' }}</span>
                        </div>
                        <div class="border-start ps-3 py-1 me-1 text-end flex-shrink-0">
                            <span class="fs-9 fw-bold text-uppercase {{ $isExpiringSoon ? 'text-danger pulse-once' : 'text-success' }} ls-1 d-block mb-0" style="font-size: 8px !important;">
                                {{ $daysLeftInt > 0 ? $daysLeftInt . ' Days Left' : 'Expired' }}
                            </span>
                            <span class="fs-11 fw-medium text-muted" style="font-size: 10px !important;">Active Trial</span>
                        </div>
                    </div>
                @endif

                <div class="nxl-h-item d-none d-sm-flex">
                    <div class="full-screen-switcher">
                        <a href="javascript:void(0);" class="nxl-head-link me-0"
                            onclick="$('html').fullScreenHelper('toggle');">
                            <i class="feather-maximize maximize"></i>
                            <i class="feather-minimize minimize"></i>
                        </a>
                    </div>
                </div>

                @if(!auth()->user()->patientProfile)
                <div class="nxl-h-item dark-light-theme">
                    <a href="javascript:void(0);" class="nxl-head-link me-0 dark-button">
                        <i class="feather-moon"></i>
                    </a>
                    <a href="javascript:void(0);" class="nxl-head-link me-0 light-button" style="display: none">
                        <i class="feather-sun"></i>
                    </a>
                </div>
                @endif

                <div class="dropdown nxl-h-item">
                    @php
                        $userPhoto = auth()->user()->details->profile_photo ?? null;
                        $avatarUrl = $userPhoto 
                            ? Storage::url($userPhoto) 
                            : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=3b71ca&color=fff&bold=true';
                    @endphp
                    <a href="javascript:void(0);" data-bs-toggle="dropdown" role="button" data-bs-auto-close="outside">
                        <img src="{{ $avatarUrl }}" alt="user-image"
                            class="img-fluid user-avtar me-0 rounded-2 border shadow-sm" style="width: 38px; height: 38px; object-fit: cover;" />
                    </a>
                    <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-user-dropdown shadow-lg border-0 rounded-3">
                        <div class="dropdown-header p-4" style="background: linear-gradient(135deg, rgba(59,113,202,0.05) 0%, rgba(124,58,237,0.05) 100%);">
                            <div class="d-flex align-items-center gap-3">
                                <img src="{{ $avatarUrl }}" alt="user-image"
                                    class="img-fluid user-avtar rounded-2 border border-white border-4 shadow-sm" style="width:50px; height:50px; object-fit: cover;" />
                                <div>
                                    <h6 class="text-dark fw-bold mb-0 fs-14 text-truncate" style="max-width: 150px;">{{ auth()->user()->name }} 
                                        <span class="badge bg-soft-success text-success ms-1 fs-9 text-uppercase">
                                            {{ $company->plan->name ?? 'Free' }}
                                        </span>
                                    </h6>
                                    <span class="fs-12 fw-medium text-muted d-block text-truncate" style="max-width: 150px;">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="p-2">
                            @php
                                $isInternalStaff = auth()->user()->hasAnyRole(['lab_admin', 'staff', 'branch_admin']);
                                $profileRoute = $isInternalStaff ? 'lab.profile' : 'partner.profile';
                                $settingsRoute = $isInternalStaff ? 'lab.settings' : 'partner.profile';
                            @endphp
                            <a href="{{ route($profileRoute) }}" wire:navigate class="dropdown-item rounded-3 py-2 px-3 transition-all">
                                <i class="feather-user me-2 text-primary"></i>
                                <span class="fw-medium">Profile Details</span>
                            </a>
                            <a href="{{ route($settingsRoute) }}" wire:navigate class="dropdown-item rounded-3 py-2 px-3 transition-all">
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
        
        /* Dropdown Alignment: Eliminate hover 'dead zone' with a pseudo-element bridge */
        .nxl-h-dropdown {
            margin-top: 10px !important;
            border: 1px solid rgba(0,0,0,0.05) !important;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1) !important;
            border-radius: 12px !important;
            overflow: visible !important; /* Allow pseudo-element to overflow for the hover bridge */
            background: white !important;
        }
        
        /* The Hover Bridge: Standardized for all header dropdowns */
        .nxl-h-dropdown::before {
            content: "";
            position: absolute;
            top: -20px; /* Increased coverage to ensure it overlaps the trigger */
            left: 0;
            right: 0;
            height: 20px;
            background: transparent;
            z-index: -1;
        }

        /* Explicitly keep dropdown open on hover for supported themes */
        @media (min-width: 992px) {
            .nxl-h-item.dropdown:hover > .dropdown-menu {
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
            }
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
