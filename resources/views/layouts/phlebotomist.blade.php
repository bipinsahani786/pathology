<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>{{ $title ?? 'Phlebotomist Portal' }} - {{ config('app.name', 'Pathology SaaS') }}</title>

    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/images/icon.webp') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/vendors.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/theme.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/custom-theme.css') }}?v={{ time() }}" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', 'Outfit', sans-serif;
            background-color: #f1f5f9;
            color: #1e293b;
            min-height: 100vh;
            padding-bottom: 90px;
        }
        .phlebo-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }
        .visit-card {
            border-radius: 18px;
            transition: all 0.2s ease;
            overflow: hidden;
            background: #ffffff;
        }
        .visit-card:hover {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08) !important;
        }
        .status-btn {
            border-radius: 12px;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.2s ease;
        }
        .status-btn:hover {
            transform: translateY(-1px);
        }
        .date-nav-btn {
            border-radius: 10px;
        }
        .impersonation-banner {
            background: linear-gradient(135deg, #e11d48, #be123c);
            color: #ffffff;
            padding: 10px 16px;
            position: sticky;
            top: 0;
            z-index: 1090;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 15px rgba(225, 29, 72, 0.35);
        }
        /* Fixed Bottom Navigation Bar */
        .phlebo-bottom-nav {
            position: fixed;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            max-width: 600px;
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
            box-shadow: 0 -4px 25px rgba(15, 23, 42, 0.08);
            z-index: 1050;
            display: flex;
            justify-content: space-around;
            padding: 8px 10px 10px;
        }
        .phlebo-nav-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: none;
            border: none;
            padding: 6px 14px;
            border-radius: 14px;
            color: #64748b;
            font-size: 11px;
            font-weight: 600;
            transition: all 0.2s ease;
            position: relative;
            cursor: pointer;
            text-decoration: none;
            min-width: 68px;
        }
        .phlebo-nav-btn i {
            font-size: 19px;
            margin-bottom: 2px;
            transition: transform 0.2s ease;
        }
        .phlebo-nav-btn.active {
            color: #2563eb;
            font-weight: 700;
            background: rgba(37, 99, 235, 0.08);
        }
        .phlebo-nav-btn.active i {
            transform: scale(1.15);
            stroke-width: 2.5px;
        }
        .phlebo-nav-btn.active::after {
            content: '';
            position: absolute;
            bottom: 2px;
            width: 18px;
            height: 3px;
            background: #2563eb;
            border-radius: 4px;
        }
    </style>
    @livewireStyles
</head>
<body>

    {{-- Impersonation Banner --}}
    @if(config('features.impersonation', true) && session()->has('impersonate_original_id'))
        <div class="impersonation-banner">
            <div class="d-flex align-items-center gap-2">
                <span class="spinner-grow spinner-grow-sm text-white" role="status" style="width: 10px; height: 10px;"></span>
                <span class="fw-bold fs-12 text-white">
                    IMPERSONATION: <span class="badge bg-white text-danger fw-bold ms-1 px-2 py-1 fs-11">{{ auth()->user()->name }}</span>
                </span>
            </div>
            <a href="{{ route('impersonate.stop') }}" class="btn btn-sm btn-light text-danger fw-bold rounded-pill px-3 py-1 shadow-sm d-flex align-items-center gap-1" style="font-size: 11px;">
                <i class="feather-log-out fs-11"></i>
                <span>RETURN TO ADMIN</span>
            </a>
        </div>
    @endif

    {{-- Top Header --}}
    <nav class="navbar phlebo-header sticky-top shadow-sm py-2">
        <div class="container-fluid px-3 d-flex justify-content-between align-items-center" style="max-width: 600px;">
            <div class="d-flex align-items-center gap-2">
                <div class="bg-primary bg-opacity-25 text-white fw-bold rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px; font-size: 16px;">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div>
                    <div class="text-white fw-bold fs-14 lh-1 mb-1">{{ auth()->user()->name }}</div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-success-subtle text-success rounded-pill px-2 py-0 fs-10 fw-semibold">
                            ● Active Phlebotomist
                        </span>
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="text-white-50 fs-11 d-none d-sm-inline">{{ now()->format('D, d M') }}</span>
                <form method="POST" action="{{ route('logout') }}" class="d-inline m-0">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-light rounded-pill px-3 py-1 fs-12 d-flex align-items-center gap-1" title="Logout">
                        <i class="feather-log-out fs-11"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    {{-- Main Mobile Container --}}
    <main class="container py-3" style="max-width: 600px;">
        {{ $slot }}
    </main>

    {{-- Vendor Scripts --}}
    <script src="{{ asset('assets/vendors/js/vendors.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/js/bootstrap.min.js') }}"></script>

    @livewireScripts

    <script>
        // Initialize Feather icons after render & Livewire updates
        function refreshFeather() {
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        }
        document.addEventListener('DOMContentLoaded', refreshFeather);
        document.addEventListener('livewire:navigated', refreshFeather);
        document.addEventListener('livewire:initialized', () => {
            Livewire.hook('morph.updated', refreshFeather);
        });
        window.addEventListener('refresh-feather', refreshFeather);

        /**
         * Capture browser GPS and set Livewire properties before calling status update.
         */
        window.captureAndUpdateStatus = function(component, visitId, newStatus) {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(pos) {
                        component.set('capturedLat', pos.coords.latitude.toFixed(7));
                        component.set('capturedLng', pos.coords.longitude.toFixed(7));
                        component.call('openConfirm', visitId, newStatus);
                    },
                    function() {
                        component.set('capturedLat', null);
                        component.set('capturedLng', null);
                        component.call('openConfirm', visitId, newStatus);
                    },
                    { timeout: 5000, enableHighAccuracy: true }
                );
            } else {
                component.call('openConfirm', visitId, newStatus);
            }
        };
    </script>
</body>
</html>
