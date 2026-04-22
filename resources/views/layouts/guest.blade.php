<!DOCTYPE html>
@php
    $themeCookie = $_COOKIE['nxl_theme'] ?? '';
    $isDark = str_contains($themeCookie, 'dark');
    $skinClass = $isDark ? 'app-skin-dark' : '';
@endphp
<html lang="en" class="{{ $skinClass }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $title ?? 'Pathology SaaS' }}</title>
    <script>
        (function () {
            try {
                var skinCustomizer = localStorage.getItem('app-skin') || '';
                var skinToggle = localStorage.getItem('app-skin-dark') || '';
                var isDark = skinCustomizer === 'app-skin-dark' || skinToggle === 'app-skin-dark';
                if (isDark) {
                    document.documentElement.classList.add('app-skin-dark');
                } else {
                    document.documentElement.classList.remove('app-skin-dark');
                }
                document.cookie = "nxl_theme=" + (isDark ? 'dark' : 'light') + "; path=/; max-age=31536000; SameSite=Lax";
            } catch (e) { }
        })();
    </script>
    <style>
        html.app-skin-dark,
        html.app-skin-dark body,
        html.app-skin-dark .auth-minimal-wrapper {
            background-color: #1a1d29 !important;
        }
    </style>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/vendors.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/theme.min.css') }}" />

    <!-- Tailwind CDN for login page Tailwind classes -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Outfit:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>

    @livewireStyles
</head>

<body>
    <main class="auth-minimal-wrapper">
        {{ $slot }}
    </main>
    @livewireScripts
</body>

</html>