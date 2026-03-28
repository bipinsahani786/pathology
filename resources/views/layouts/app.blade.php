<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $title ?? 'Pathology SaaS' }}</title>

    <link rel="shortcut icon" type="image/x-icon" href="{{ \App\Models\Configuration::getFor('lab_favicon') ? asset('storage/' . \App\Models\Configuration::getFor('lab_favicon')) : asset('assets/images/icon.webp') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/vendors.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/daterangepicker.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/theme.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/custom-theme.css') }}?v={{ time() }}" />

    @livewireStyles
</head>

<body style="display: flex; flex-direction: column; min-height: 100vh;">
    @include('layouts.partials.sidebar')

    @include('layouts.partials.header')

    <main class="nxl-container" style="flex: 1; display: flex; flex-direction: column;">
        <div class="nxl-content" style="flex: 1;">

            {{ $slot }}

        </div>

        @include('layouts.partials.footer')
    </main>

    @include('layouts.partials.customizer')

    <script src="{{ asset('assets/vendors/js/vendors.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/js/daterangepicker.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/js/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/js/circle-progress.min.js') }}"></script>
    <script src="{{ asset('assets/js/common-init.min.js') }}"></script>
    <script src="{{ asset('assets/js/theme-customizer-init.min.js') }}"></script>
    <script src="{{ asset('assets/js/dashboard-init.min.js') }}"></script>
    
    <script src="https://cdn.ckeditor.com/ckeditor5/35.1.0/classic/ckeditor.js"></script>

    <script>
        document.addEventListener('livewire:navigated', () => {
            if (typeof bootstrap !== 'undefined') {
                const dropdowns = document.querySelectorAll('[data-bs-toggle="dropdown"]');
                dropdowns.forEach(dropdown => {
                    new bootstrap.Dropdown(dropdown, {
                        popperConfig(defaultBsPopperConfig) {
                            return {
                                ...defaultBsPopperConfig,
                                strategy: 'fixed'
                            };
                        }
                    });
                });
            }
            setTimeout(() => {
                document.dispatchEvent(new Event('DOMContentLoaded'));
                window.dispatchEvent(new Event('load'));
            }, 100);
        });
    </script>
    @livewireScripts
    
    {{-- Global Search Modal --}}
    <div class="modal fade" id="searchModal" tabindex="-1" aria-hidden="true" style="z-index: 9999;">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden" style="background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(10px);">
                <div class="modal-header border-0 p-4 pb-0">
                    <div class="input-group search-modal-input-group border-0 rounded-4 px-3 py-2 bg-light shadow-sm">
                        <span class="input-group-text border-0 ps-0 bg-transparent">
                            <i class="feather-search text-primary fs-4"></i>
                        </span>
                        <input type="text" id="globalSearchInput" class="form-control border-0 shadow-none bg-transparent fs-5 fw-medium" 
                            placeholder="What can I help you find?" autofocus>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-white text-muted border shadow-sm px-2 py-1 fs-10">ESC</span>
                            <button type="button" class="btn-close ms-2" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                    </div>
                </div>
                <div class="modal-body p-4 pt-3" style="min-height: 400px; max-height: 75vh; overflow-y: auto;">
                    <div class="fs-10 fw-bold text-muted text-uppercase mb-3 ls-2">Common Destinations</div>
                    <div class="row g-4" id="navigationLinks">
                        @php
                            $navItems = [
                                ['name' => 'Dashboard', 'route' => 'lab.dashboard', 'icon' => 'feather-airplay', 'desc' => 'Overview & Analytics', 'color' => 'primary'],
                                ['name' => 'New Bill (POS)', 'route' => 'lab.pos', 'icon' => 'feather-plus-circle', 'desc' => 'Quick Billing', 'color' => 'success'],
                                ['name' => 'All Invoices', 'route' => 'lab.invoices', 'icon' => 'feather-file-text', 'desc' => 'Manage Bills', 'color' => 'info'],
                                ['name' => 'Test Reports', 'route' => 'lab.reports', 'icon' => 'feather-clipboard', 'desc' => 'Process & Print', 'color' => 'warning'],
                                ['name' => 'Patients List', 'route' => 'lab.patients', 'icon' => 'feather-users', 'desc' => 'Patient Registry', 'color' => 'purple'],
                                ['name' => 'Lab Tests', 'route' => 'lab.tests', 'icon' => 'feather-activity', 'desc' => 'Manage Tests', 'color' => 'danger'],
                                ['name' => 'Test Packages', 'route' => 'lab.packages', 'icon' => 'feather-package', 'desc' => 'Bundle Tests', 'color' => 'teal'],
                                ['name' => 'Collection Centers', 'route' => 'lab.collection.centers', 'icon' => 'feather-map', 'desc' => 'Manage CCs', 'color' => 'orange'],
                                ['name' => 'Main Branches', 'route' => 'lab.branches', 'icon' => 'feather-home', 'desc' => 'Lab Locations', 'color' => 'indigo'],
                                ['name' => 'Referral Doctors', 'route' => 'lab.doctors', 'icon' => 'feather-user-check', 'desc' => 'Doctor Partners', 'color' => 'blue'],
                                ['name' => 'Referral Agents', 'route' => 'lab.agents', 'icon' => 'feather-briefcase', 'desc' => 'Agent Partners', 'color' => 'pink'],
                                ['name' => 'Partner Settlements', 'route' => 'lab.settlements', 'icon' => 'feather-dollar-sign', 'desc' => 'Commissions', 'color' => 'cyan'],
                                ['name' => 'Lab Settings', 'route' => 'lab.settings', 'icon' => 'feather-settings', 'desc' => 'Configurations', 'color' => 'gray'],
                                ['name' => 'My Profile', 'route' => 'lab.profile', 'icon' => 'feather-user', 'desc' => 'Account Info', 'color' => 'dark'],
                            ];
                        @endphp

                        @foreach($navItems as $item)
                            <div class="col-md-4 nav-search-item animated fadeInUp">
                                <a href="{{ route($item['route']) }}" wire:navigate class="nav-card d-flex align-items-center p-3 rounded-4 border bg-white shadow-sm transition-all h-100">
                                    <div class="avatar-text avatar-md bg-soft-{{ $item['color'] }} text-{{ $item['color'] }} rounded-3 me-3 flex-shrink-0 card-icon">
                                        <i class="{{ $item['icon'] }} fs-5"></i>
                                    </div>
                                    <div class="overflow-hidden flex-grow-1">
                                        <h6 class="mb-0 fs-13 fw-bold text-dark text-truncate nav-title">{{ $item['name'] }}</h6>
                                        <small class="text-muted fs-11 text-truncate d-block">{{ $item['desc'] }}</small>
                                    </div>
                                    <i class="feather-chevron-right fs-11 text-muted opacity-0 arrow-indicator transition-all"></i>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .modal-backdrop { z-index: 9998 !important; }
        .modal { z-index: 9999 !important; }
        
        /* THE ULTIMATE BLUR KILLER */
        body.modal-open > *:not(.modal):not(.modal-backdrop) {
            filter: none !important;
            backdrop-filter: none !important;
        }
        
        body.modal-open .nxl-container, 
        body.modal-open .nxl-navigation, 
        body.modal-open .nxl-header {
            filter: none !important;
            backdrop-filter: none !important;
        }

        .search-modal-input-group:focus-within {
            background: white !important;
            border: 1px solid var(--bs-primary) !important;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05) !important;
        }

        .nav-card:hover {
            border-color: var(--bs-primary) !important;
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
        }
        .nav-card:hover .card-icon { transform: scale(1.1); }
        .nav-card:hover .arrow-indicator { opacity: 1 !important; transform: translateX(3px); }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translate3d(0, 20px, 0); }
            to { opacity: 1; transform: translate3d(0, 0, 0); }
        }
        .animated.fadeInUp {
            animation-name: fadeInUp;
            animation-duration: 0.4s;
            animation-fill-mode: both;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('globalSearchInput');
            const navItems = document.querySelectorAll('.nav-search-item');

            if(searchInput) {
                searchInput.addEventListener('input', function() {
                    const query = this.value.toLowerCase().trim();
                    navItems.forEach(item => {
                        const title = item.querySelector('.nav-title').textContent.toLowerCase();
                        item.classList.toggle('d-none', !title.includes(query));
                    });
                });

                document.getElementById('searchModal').addEventListener('shown.bs.modal', () => {
                    searchInput.focus();
                });
            }
        });

        // Handle navigation to ensure modal closes
        document.addEventListener('livewire:navigated', () => {
            const modalElement = document.getElementById('searchModal');
            if (modalElement) {
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) modal.hide();
            }
            // Force remove backdrops
            const backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(b => b.remove());
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        });
    </script>
</body>

</html>
