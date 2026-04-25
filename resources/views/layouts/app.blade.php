<!DOCTYPE html>
@php
    // Server-side theme detection from cookie - page arrives already dark if needed
    $themeCookie = $_COOKIE['nxl_theme'] ?? '';
    $isDark = str_contains($themeCookie, 'dark');
    $skinClass = $isDark ? 'app-skin-dark' : '';
@endphp
<html lang="en" class="{{ $skinClass }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $title ?? 'Pathology SaaS' }}</title>

    {{-- THEME GUARD: Runs before anything renders. Reads localStorage and applies classes instantly. --}}
    <script>
        (function() {
            try {
                // The template uses TWO keys: 'app-skin' (customizer) and 'app-skin-dark' (header toggle)
                var skinCustomizer = localStorage.getItem('app-skin') || '';
                var skinToggle = localStorage.getItem('app-skin-dark') || '';
                var isDark = skinCustomizer === 'app-skin-dark' || skinToggle === 'app-skin-dark';

                if (isDark) {
                    document.documentElement.classList.add('app-skin-dark');
                } else {
                    document.documentElement.classList.remove('app-skin-dark');
                }

                // Apply navigation preference
                var nav = localStorage.getItem('app-navigation');
                if (nav) document.documentElement.classList.add(nav);

                // Apply header preference
                var header = localStorage.getItem('app-header');
                if (header) document.documentElement.classList.add(header);

                // Apply font preference
                var font = localStorage.getItem('font-family');
                if (font) document.documentElement.classList.add(font);

                // Apply color preference
                var color = localStorage.getItem('app-color');
                if (color) document.documentElement.classList.add(color);

                // Sync cookie for server-side rendering on next request
                document.cookie = "nxl_theme=" + (isDark ? 'dark' : 'light') + "; path=/; max-age=31536000; SameSite=Lax";
            } catch (e) {}
        })();
    </script>

    {{-- Inline critical dark-mode styles so there's zero flash even before CSS loads --}}
    <style>
        :root {
            --ui-font-scale: {{ \App\Models\Configuration::getFor('ui_font_scale', 100) }}%;
        }
        html.app-skin-dark,
        html.app-skin-dark body {
            background-color: #1a1d29 !important;
        }
    </style>

    @php
        $siteFavicon = \App\Models\SiteSetting::get('site_favicon');
        $labFavicon = \App\Models\Configuration::getFor('lab_favicon');
        $faviconUrl = $labFavicon 
            ? secure_storage_url($labFavicon) 
            : ($siteFavicon ? secure_storage_url($siteFavicon) : asset('assets/images/icon.webp'));
    @endphp
    <link rel="shortcut icon" type="image/x-icon" href="{{ $faviconUrl }}" />
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

    @if(config('features.impersonation', true) && session()->has('impersonate_original_id'))
        <div class="impersonation-banner"
            style="background: linear-gradient(90deg, #ff416c, #ff4b2b); color: white; padding: 12px 20px; position: sticky; top: 0; z-index: 1060; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 15px rgba(255, 65, 108, 0.3);">
            <div class="d-flex align-items-center gap-3">
                <div class="spinner-grow spinner-grow-sm text-white" role="status"></div>
                <span class="fw-bold fs-14">IMPERSONATION MODE: You are currently viewing the panel as
                    <u>{{ auth()->user()->name }}</u></span>
            </div>
            <a href="{{ route('impersonate.stop') }}" class="btn btn-sm btn-light fw-black rounded-pill px-4 shadow-sm"
                style="color: #ff416c;">
                <i class="feather-log-out me-2"></i>RETURN TO ADMIN
            </a>
        </div>
    @endif

    <main class="nxl-container" style="flex: 1; display: flex; flex-direction: column;">
        <div class="nxl-content" style="flex: 1;">

            {{ $slot }}

        </div>

        @include('layouts.partials.footer')
    </main>

    @include('layouts.partials.customizer')

    {{-- Vendor scripts: Using data-navigate-once for stable delegation in SPA mode --}}
    <script src="{{ asset('assets/vendors/js/vendors.min.js') }}" data-navigate-once></script>
    <script src="{{ asset('assets/vendors/js/daterangepicker.min.js') }}" data-navigate-once></script>
    <script src="{{ asset('assets/vendors/js/apexcharts.min.js') }}" data-navigate-once></script>
    <script src="{{ asset('assets/vendors/js/circle-progress.min.js') }}" data-navigate-once></script>
    <script src="{{ asset('assets/js/common-init.min.js') }}"></script>
    <script src="{{ asset('assets/js/theme-customizer-init.min.js') }}"></script>
    <script src="{{ asset('assets/js/dashboard-init.min.js') }}"></script>

    <script src="https://cdn.ckeditor.com/ckeditor5/35.1.0/classic/ckeditor.js" data-navigate-once></script>

    @livewireScripts

    {{-- Global Search Modal --}}
    <div class="modal fade" id="searchModal" tabindex="-1" aria-hidden="true" style="z-index: 9999;">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden"
                style="background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(10px);">
                <div class="modal-header border-0 p-4 pb-0">
                    <div class="input-group search-modal-input-group border-0 rounded-4 px-3 py-2 bg-light shadow-sm">
                        <span class="input-group-text border-0 ps-0 bg-transparent">
                            <i class="feather-search text-primary fs-4"></i>
                        </span>
                        <input type="text" id="globalSearchInput"
                            class="form-control border-0 shadow-none bg-transparent fs-5 fw-medium"
                            placeholder="What can I help you find?" autofocus>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-white text-muted border shadow-sm px-2 py-1 fs-10">ESC</span>
                            <button type="button" class="btn-close ms-2" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                    </div>
                </div>
                <div class="modal-body p-4 pt-3" style="min-height: 400px; max-height: 75vh; overflow-y: auto;">
                    <div class="fs-10 fw-bold text-muted text-uppercase mb-3 ls-2">Common Destinations</div>
                    <div class="row g-4" id="navigationLinks">
                        @php
                            $user = auth()->user();
                            $isLabStaff = $user->hasAnyRole(['lab_admin', 'staff', 'branch_admin']);
                            $isCollectionCenter = $user->hasRole('collection_center');
                            $isDoctorAgent = $user->hasAnyRole(['doctor', 'agent']);

                            $navItems = [];

                            if ($isLabStaff) {
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
                            } elseif ($isCollectionCenter) {
                                $navItems = [
                                    ['name' => 'Center Dashboard', 'route' => 'partner.dashboard', 'icon' => 'feather-airplay', 'desc' => 'My Dashboard', 'color' => 'primary'],
                                    ['name' => 'New Bill (POS)', 'route' => 'lab.pos', 'icon' => 'feather-plus-circle', 'desc' => 'Billing Portal', 'color' => 'success'],
                                    ['name' => 'Center Invoices', 'route' => 'partner.invoices', 'icon' => 'feather-file-text', 'desc' => 'My Billings', 'color' => 'info'],
                                    ['name' => 'Patients List', 'route' => 'partner.patients', 'icon' => 'feather-users', 'desc' => 'My Patients', 'color' => 'purple'],
                                    ['name' => 'Profit Settlements', 'route' => 'partner.settlements', 'icon' => 'feather-dollar-sign', 'desc' => 'Earnings', 'color' => 'cyan'],
                                    ['name' => 'My Profile', 'route' => 'partner.profile', 'icon' => 'feather-user', 'desc' => 'Account Info', 'color' => 'dark'],
                                ];
                            } elseif ($isDoctorAgent) {
                                $navItems = [
                                    ['name' => 'Partner Dashboard', 'route' => 'partner.dashboard', 'icon' => 'feather-airplay', 'desc' => 'My Dashboard', 'color' => 'primary'],
                                    ['name' => 'Referred Invoices', 'route' => 'partner.invoices', 'icon' => 'feather-file-text', 'desc' => 'My Billings', 'color' => 'info'],
                                    ['name' => 'My Patients', 'route' => 'partner.patients', 'icon' => 'feather-users', 'desc' => 'Patient Registry', 'color' => 'purple'],
                                    ['name' => 'Settlements', 'route' => 'partner.settlements', 'icon' => 'feather-dollar-sign', 'desc' => 'Commissions', 'color' => 'cyan'],
                                    ['name' => 'My Profile', 'route' => 'partner.profile', 'icon' => 'feather-user', 'desc' => 'Account Info', 'color' => 'dark'],
                                ];
                            }
                        @endphp

                        @foreach($navItems as $item)
                            <div class="col-md-4 nav-search-item animated fadeInUp">
                                <a href="{{ route($item['route']) }}" wire:navigate
                                    class="nav-card d-flex align-items-center p-3 rounded-4 border bg-white shadow-sm transition-all h-100">
                                    <div
                                        class="avatar-text avatar-md bg-soft-{{ $item['color'] }} text-{{ $item['color'] }} rounded-3 me-3 flex-shrink-0 card-icon">
                                        <i class="{{ $item['icon'] }} fs-5"></i>
                                    </div>
                                    <div class="overflow-hidden flex-grow-1">
                                        <h6 class="mb-0 fs-13 fw-bold text-dark text-truncate nav-title">{{ $item['name'] }}
                                        </h6>
                                        <small class="text-muted fs-11 text-truncate d-block">{{ $item['desc'] }}</small>
                                    </div>
                                    <i
                                        class="feather-chevron-right fs-11 text-muted opacity-0 arrow-indicator transition-all"></i>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .modal-backdrop {
            z-index: 9998 !important;
        }

        .modal {
            z-index: 9999 !important;
        }

        /* Fix background scroll when modal is open */
        body:has(.modal.show), 
        body.modal-open {
            overflow: hidden !important;
            padding-right: 0 !important;
        }

        /* BRUTE FORCE MODAL SCROLL FIX */
        .modal.show {
            display: flex !important;
            align-items: center;
            justify-content: center;
            background: rgba(0,0,0,0.5);
            padding: 1rem;
        }

        .modal.show .modal-dialog {
            max-height: 95vh;
            display: flex;
            align-items: center;
            width: 100%;
            margin: auto;
        }

        .modal.show .modal-content {
            max-height: 95vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            width: 100%;
        }

        /* If there's a form directly inside modal-content (common in Livewire) */
        .modal.show .modal-content > form {
            display: flex;
            flex-direction: column;
            overflow: hidden;
            height: 100%;
        }

        .modal.show .modal-body {
            overflow-y: auto !important;
            flex: 1 1 auto;
        }

        .modal.show .modal-footer {
            flex-shrink: 0;
        }

        /* THE ULTIMATE BLUR KILLER */
        body:has(.modal.show) > *:not(.modal):not(.modal-backdrop),
        body.modal-open > *:not(.modal):not(.modal-backdrop) {
            filter: none !important;
            backdrop-filter: none !important;
        }

        body:has(.modal.show) .nxl-container,
        body:has(.modal.show) .nxl-navigation,
        body:has(.modal.show) .nxl-header,
        body.modal-open .nxl-container,
        body.modal-open .nxl-navigation,
        body.modal-open .nxl-header {
            filter: none !important;
            backdrop-filter: none !important;
        }

        .search-modal-input-group:focus-within {
            background: white !important;
            border: 1px solid var(--bs-primary) !important;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05) !important;
        }

        .nav-card:hover {
            border-color: var(--bs-primary) !important;
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1) !important;
        }

        .nav-card:hover .card-icon {
            transform: scale(1.1);
        }

        .nav-card:hover .arrow-indicator {
            opacity: 1 !important;
            transform: translateX(3px);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translate3d(0, 20px, 0);
            }

            to {
                opacity: 1;
                transform: translate3d(0, 0, 0);
            }
        }

        .animated.fadeInUp {
            animation-name: fadeInUp;
            animation-duration: 0.4s;
            animation-fill-mode: both;
        }
    </style>

    {{-- ============================================================ --}}
    {{-- MASTER LAYOUT SCRIPT: Theme persistence, navigation, events  --}}
    {{-- ============================================================ --}}
    <script data-navigate-once>
        (function() {
            // ── Helper: sync both localStorage keys + cookie ──
            function syncThemeState() {
                var isDark = document.documentElement.classList.contains('app-skin-dark');
                // Keep both localStorage keys in sync (customizer uses 'app-skin', header toggle uses 'app-skin-dark')
                localStorage.setItem('app-skin', isDark ? 'app-skin-dark' : 'app-skin-light');
                localStorage.setItem('app-skin-dark', isDark ? 'app-skin-dark' : 'app-skin-light');
                // Sync cookie for server-side rendering
                document.cookie = "nxl_theme=" + (isDark ? 'dark' : 'light') + "; path=/; max-age=31536000; SameSite=Lax";

                // Sync header button visibility
                if (isDark) {
                    document.querySelectorAll('.dark-button').forEach(function(el) { el.style.display = 'none'; });
                    document.querySelectorAll('.light-button').forEach(function(el) { el.style.display = ''; });
                } else {
                    document.querySelectorAll('.dark-button').forEach(function(el) { el.style.display = ''; });
                    document.querySelectorAll('.light-button').forEach(function(el) { el.style.display = 'none'; });
                }
            }

            // ── Helper: restore theme after Livewire SPA navigation ──
            function restoreThemeAfterNavigation() {
                var skinCustomizer = localStorage.getItem('app-skin') || '';
                var skinToggle = localStorage.getItem('app-skin-dark') || '';
                var isDark = skinCustomizer === 'app-skin-dark' || skinToggle === 'app-skin-dark';

                if (isDark) {
                    document.documentElement.classList.add('app-skin-dark');
                } else {
                    document.documentElement.classList.remove('app-skin-dark');
                }

                // Restore other theme classes
                var nav = localStorage.getItem('app-navigation');
                if (nav) {
                    document.documentElement.classList.remove('app-navigation-light', 'app-navigation-dark');
                    document.documentElement.classList.add(nav);
                }
                var header = localStorage.getItem('app-header');
                if (header) {
                    document.documentElement.classList.remove('app-header-light', 'app-header-dark');
                    document.documentElement.classList.add(header);
                }
                var font = localStorage.getItem('font-family');
                if (font) {
                    // Remove all possible font classes first
                    document.documentElement.classList.remove('app-font-family-lato', 'app-font-family-rubik', 'app-font-family-inter', 'app-font-family-cinzel', 'app-font-family-poppins', 'app-font-family-montserrat', 'app-font-family-roboto', 'app-font-family-nunito');
                    document.documentElement.classList.add(font);
                }
                var color = localStorage.getItem('app-color');
                if (color) {
                    // Remove all possible color classes first
                    document.documentElement.classList.remove('theme-color-blue', 'theme-color-teal', 'theme-color-purple', 'theme-color-green', 'theme-color-orange', 'theme-color-red');
                    document.documentElement.classList.add(color);
                }

                // Sync button states
                syncThemeState();
                // Sync Customizer UI radios
                syncCustomizerUI();
            }

            // ── Helper: Sync Customizer UI Radio Buttons with LocalStorage ──
            function syncCustomizerUI() {
                const settings = {
                    'app-navigation': localStorage.getItem('app-navigation'),
                    'app-header': localStorage.getItem('app-header'),
                    'app-skin': localStorage.getItem('app-skin'),
                    'font-family': localStorage.getItem('font-family'),
                    'app-color': localStorage.getItem('app-color')
                };

                for (const [name, value] of Object.entries(settings)) {
                    if (value) {
                        const radio = document.querySelector(`input[name="${name}"][data-${name}="${value}"]`);
                        if (radio) radio.checked = true;
                    }
                }
            }

            // ── Helper: Global Event Delegation for Theme Controls ──
            function initThemeDelegation() {
                document.addEventListener('click', function(e) {
                    // 1. Dark/Light Mode Toggles (Header)
                    const darkBtn = e.target.closest('.dark-button');
                    const lightBtn = e.target.closest('.light-button');
                    
                    if (darkBtn || lightBtn) {
                        e.preventDefault();
                        const isTurningDark = !!darkBtn;
                        if (isTurningDark) {
                            document.documentElement.classList.add('app-skin-dark');
                        } else {
                            document.documentElement.classList.remove('app-skin-dark');
                        }
                        syncThemeState();
                        syncCustomizerUI();
                        return;
                    }

                    // 2. Customizer Open/Close
                    const openBtn = e.target.closest('.cutomizer-open-trigger');
                    const closeBtn = e.target.closest('.cutomizer-close-trigger');
                    if (openBtn) {
                        e.preventDefault();
                        document.querySelector('.theme-customizer')?.classList.add('theme-customizer-open');
                        return;
                    }
                    if (closeBtn) {
                        e.preventDefault();
                        document.querySelector('.theme-customizer')?.classList.remove('theme-customizer-open');
                        return;
                    }

                    // 3. Reset All
                    const resetBtn = e.target.closest('[data-style="reset-all-common-style"]');
                    if (resetBtn) {
                        e.preventDefault();
                        localStorage.clear();
                        window.location.reload();
                        return;
                    }
                });

                document.addEventListener('change', function(e) {
                    const radio = e.target.closest('.theme-options-set input[type="radio"]');
                    if (radio) {
                        const name = radio.name;
                        const value = radio.getAttribute(`data-${name}`);
                        
                        if (name === 'app-skin') {
                            if (value === 'app-skin-dark') {
                                document.documentElement.classList.add('app-skin-dark');
                            } else {
                                document.documentElement.classList.remove('app-skin-dark');
                            }
                        } else if (name === 'font-family') {
                            // Remove existing font classes
                            document.documentElement.classList.remove('app-font-family-lato', 'app-font-family-rubik', 'app-font-family-inter', 'app-font-family-cinzel', 'app-font-family-poppins', 'app-font-family-montserrat', 'app-font-family-roboto', 'app-font-family-nunito');
                            document.documentElement.classList.add(value);
                        } else if (name === 'app-color') {
                            document.documentElement.classList.remove('theme-color-blue', 'theme-color-teal', 'theme-color-purple', 'theme-color-green', 'theme-color-orange', 'theme-color-red');
                            document.documentElement.classList.add(value);
                        } else {
                            // Navigation and Header styles
                            const prefix = name === 'app-navigation' ? 'app-navigation-' : 'app-header-';
                            document.documentElement.classList.remove(prefix + 'light', prefix + 'dark');
                            document.documentElement.classList.add(value);
                        }

                        localStorage.setItem(name, value);
                        syncThemeState();
                    }
                });
            }

            // ── 1. After every Livewire SPA navigation ──
            document.addEventListener('livewire:navigated', function() {
                // Restore theme instantly (before paint if possible)
                restoreThemeAfterNavigation();

                // Modal cleanup
                var modalElement = document.getElementById('searchModal');
                if (modalElement && typeof bootstrap !== 'undefined') {
                    var modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) modal.hide();
                }
                document.querySelectorAll('.modal-backdrop').forEach(function(b) { b.remove(); });
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';

                // Restore Sidebar Scrollbar and Accordion interactions after Livewire Morph
                setTimeout(function() {
                    if (typeof window.addscroller === 'function') {
                        window.addscroller();
                    }
                    // Ensure actively selected menus stay visible
                    if (typeof jQuery !== 'undefined') {
                        jQuery('.nxl-hasmenu.nxl-trigger > .nxl-submenu').css('display', 'block');
                    }
                }, 10);
            });

            // ── 2. Before Livewire starts navigating (prevent flash) ──
            document.addEventListener('livewire:navigating', function() {
                // Ensure the dark class stays on <html> during the swap
                var isDark = localStorage.getItem('app-skin') === 'app-skin-dark' ||
                             localStorage.getItem('app-skin-dark') === 'app-skin-dark';
                if (isDark) {
                    document.documentElement.classList.add('app-skin-dark');
                }
            });

            // ── 3. Watch for class changes on <html> to keep cookie in sync ──
            var observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.attributeName === 'class') {
                        syncThemeState();
                    }
                });
            });
            observer.observe(document.documentElement, { attributes: true });

            // ── 4. Global Search ──
            document.addEventListener('DOMContentLoaded', function() {
                var searchInput = document.getElementById('globalSearchInput');
                var navItems = document.querySelectorAll('.nav-search-item');
                if (searchInput) {
                    searchInput.addEventListener('input', function() {
                        var query = this.value.toLowerCase().trim();
                        navItems.forEach(function(item) {
                            var title = item.querySelector('.nav-title').textContent.toLowerCase();
                            item.classList.toggle('d-none', !title.includes(query));
                        });
                    });
                    var searchModal = document.getElementById('searchModal');
                    if (searchModal) {
                        searchModal.addEventListener('shown.bs.modal', function() { searchInput.focus(); });
                    }
                }
            });

            // ── 5. Livewire Event Listeners & Global Delegation (Strict Once) ──
            if (!window.pathologyListenersAdded) {
                document.addEventListener('livewire:init', function() {
                    if (window.pathologyListenersAdded) return;



                    // Listeners start here
                    window.pathologyListenersAdded = true;

                    Livewire.on('notify', function(data) {
                        var info = Array.isArray(data) ? data[0] : data;
                        
                        const toast = document.createElement('div');
                        toast.style.position = 'fixed';
                        toast.style.top = '30px';
                        toast.style.right = '30px';
                        toast.style.zIndex = '10000';
                        toast.style.minWidth = '350px';
                        toast.style.padding = '16px 20px';
                        toast.style.borderRadius = '16px';
                        toast.style.backgroundColor = '#ffffff';
                        toast.style.boxShadow = '0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)';
                        toast.style.border = `1px solid ${info.type === 'error' ? '#fee2e2' : '#dcfce7'}`;
                        toast.style.display = 'flex';
                        toast.style.alignItems = 'center';
                        toast.style.gap = '15px';
                        toast.style.transition = 'all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
                        toast.style.transform = 'translateX(100px)';
                        toast.style.opacity = '0';
                        
                        const accentColor = info.type === 'error' ? '#ef4444' : '#10b981';
                        
                        toast.innerHTML = `
                            <div style="background: ${accentColor}; color: white; width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                                <i class="feather-${info.type === 'error' ? 'alert-octagon' : 'check-circle'}" style="font-size: 20px;"></i>
                            </div>
                            <div style="flex: 1;">
                                <div style="color: #111827; font-weight: 700; font-size: 15px; margin-bottom: 2px;">${info.type === 'error' ? 'Attention Required' : 'Action Successful'}</div>
                                <div style="color: #6b7280; font-size: 13px; font-weight: 500;">${info.message}</div>
                            </div>
                            <button onclick="this.parentElement.remove()" style="background: none; border: none; color: #9ca3af; cursor: pointer; padding: 5px;">
                                <i class="feather-x"></i>
                            </button>
                        `;
                        
                        document.body.appendChild(toast);
                        
                        // Force reflow
                        toast.offsetHeight;
                        
                        // Animate in
                        toast.style.transform = 'translateX(0)';
                        toast.style.opacity = '1';
                        
                        // Auto-remove
                        setTimeout(() => {
                            if (toast.parentElement) {
                                toast.style.transform = 'translateX(100px)';
                                toast.style.opacity = '0';
                                setTimeout(() => toast.remove(), 500);
                            }
                        }, 5000);
                    });

                    Livewire.on('print-window', function() {
                        window.print();
                    });

                    Livewire.on('open-new-tab', (event) => {
                        const data = Array.isArray(event) ? event[0] : event;
                        if (data && data.url) {
                            window.open(data.url, '_blank');
                        }
                    });

                    window.pathologyListenersAdded = true;
                });
            }

            // ── 6. Initial sync and delegation on page load ──
            syncThemeState();
            syncCustomizerUI();
            initThemeDelegation();
            
            // Re-init specialized sidebar behavior on load
            setTimeout(function() {
                if (typeof jQuery !== 'undefined') {
                    jQuery('.nxl-hasmenu.active').addClass('nxl-trigger').find('.nxl-submenu').show();
                }
            }, 100);
        })();
    </script>
</body>

</html>