<!DOCTYPE html>
@php
    $savedSkin = $_COOKIE['nxl-skin'] ?? 'app-skin-light';
@endphp
<html lang="en" class="{{ $savedSkin }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $title ?? 'Pathology SaaS' }}</title>
    <script>
        (function() {
            try {
                var skin = localStorage.getItem('nxl-skin') || localStorage.getItem('app-skin') || 'app-skin-light';
                if (skin.includes('dark')) {
                    document.documentElement.classList.add('app-skin-dark');
                    document.documentElement.style.background = '#111827'; 
                }
            } catch (e) {}
        })();
    </script>
    <style>
        html.app-skin-dark, 
        html.app-skin-dark body,
        html.app-skin-dark .auth-minimal-wrapper {
            background-color: #111827 !important;
            color: #e5e7eb !important;
        }
    </style>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/vendors.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/theme.min.css') }}" />
    @livewireStyles
</head>

<body>
    <main class="auth-minimal-wrapper">
        {{ $slot }}
    </main>
    @livewireScripts
</body>

</html>
