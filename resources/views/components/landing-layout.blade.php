<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? \App\Models\SiteSetting::get('meta_title', 'SWS Pathology - Advanced Diagnostic Solutions') }}
    </title>
    <meta name="description"
        content="{{ \App\Models\SiteSetting::get('meta_description', 'Leading pathology management platform') }}">
    @php
        $siteFavicon = \App\Models\SiteSetting::get('site_favicon');
    @endphp
    <link rel="shortcut icon" type="image/x-icon" href="{{ $siteFavicon ? asset('storage/' . $siteFavicon) : asset('assets/images/icon.webp') }}" />

    <!-- Fonts: Outfit for display, Inter for body -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Feather Icons (same as dashboard) -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/vendors.min.css') }}" />

    <!-- Tailwind Play CDN -->
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>

    @php
        $brandColor = \App\Models\SiteSetting::get('primary_color', '#0284c7');
        $siteName = \App\Models\SiteSetting::get('site_name', 'SWS Pathology');
    @endphp

    <style type="text/tailwindcss">
        @theme {
            --font-sans: 'Inter', ui-sans-serif, system-ui, sans-serif;
            --font-display: 'Outfit', sans-serif;

            --color-brand-50: color-mix(in srgb, {{ $brandColor }} 10%, white);
            --color-brand-100: color-mix(in srgb, {{ $brandColor }} 20%, white);
            --color-brand-200: color-mix(in srgb, {{ $brandColor }} 40%, white);
            --color-brand-300: color-mix(in srgb, {{ $brandColor }} 60%, white);
            --color-brand-400: color-mix(in srgb, {{ $brandColor }} 80%, white);
            --color-brand-500: {{ $brandColor }};
            --color-brand-600: color-mix(in srgb, {{ $brandColor }} 90%, black);
            --color-brand-700: color-mix(in srgb, {{ $brandColor }} 75%, black);
            --color-brand-800: color-mix(in srgb, {{ $brandColor }} 60%, black);
            --color-brand-900: color-mix(in srgb, {{ $brandColor }} 45%, black);
            --color-brand-950: color-mix(in srgb, {{ $brandColor }} 30%, black);

            --color-accent: var(--color-brand-600);

            --animate-float: float 6s ease-in-out infinite;
            --animate-fade-in-up: fadeInUp 0.8s ease-out forwards;
            --animate-shimmer: shimmer 2.5s linear infinite;
            --animate-pulse-soft: pulseSoft 3s ease-in-out infinite;
            --animate-slide-in-left: slideInLeft 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            --animate-slide-in-right: slideInRight 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            --animate-scale-in: scaleIn 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            --animate-count-up: countUp 2s ease-out forwards;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        @keyframes pulseSoft {
            0%, 100% { opacity: 0.4; transform: scale(1); }
            50% { opacity: 0.8; transform: scale(1.05); }
        }
        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-60px); }
            to { opacity: 1; transform: translateX(0); }
        }
        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(60px); }
            to { opacity: 1; transform: translateX(0); }
        }
        @keyframes scaleIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }
        @keyframes gradientFlow {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        @keyframes borderGlow {
            0%, 100% { border-color: oklch(0.58 0.18 220 / 0.2); }
            50% { border-color: oklch(0.58 0.18 220 / 0.5); }
        }

        /* Utility Classes */
        .glass {
            @apply bg-white/60 backdrop-blur-xl border border-white/30 shadow-xl;
        }
        .glass-dark {
            @apply bg-zinc-900/60 backdrop-blur-xl border border-white/10 shadow-2xl;
        }
        .bg-grid {
            background-size: 50px 50px;
            background-image:
                linear-gradient(to right, rgba(0,0,0,0.03) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(0,0,0,0.03) 1px, transparent 1px);
        }
        .gradient-text {
            @apply text-transparent bg-clip-text;
            background-image: linear-gradient(135deg, oklch(0.58 0.18 220) 0%, oklch(0.50 0.16 280) 100%);
        }
        .gradient-border {
            background: linear-gradient(white, white) padding-box,
                        linear-gradient(135deg, oklch(0.58 0.18 220), oklch(0.50 0.16 280)) border-box;
            border: 2px solid transparent;
        }
        .hover-lift {
            @apply transition-all duration-500;
        }
        .hover-lift:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 60px -12px rgba(0,0,0,0.15);
        }
        .shimmer-bg {
            background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.1) 50%, transparent 100%);
            background-size: 200% 100%;
        }

        /* Scroll-reveal classes */
        .reveal { opacity: 0; transform: translateY(30px); transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1); }
        .reveal.revealed { opacity: 1; transform: translateY(0); }
        .reveal-left { opacity: 0; transform: translateX(-40px); transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1); }
        .reveal-left.revealed { opacity: 1; transform: translateX(0); }
        .reveal-right { opacity: 0; transform: translateX(40px); transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1); }
        .reveal-right.revealed { opacity: 1; transform: translateX(0); }
        .reveal-scale { opacity: 0; transform: scale(0.9); transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1); }
        .reveal-scale.revealed { opacity: 1; transform: scale(1); }

        /* Stagger delays */
        .delay-1 { transition-delay: 0.1s; }
        .delay-2 { transition-delay: 0.2s; }
        .delay-3 { transition-delay: 0.3s; }
        .delay-4 { transition-delay: 0.4s; }
        .delay-5 { transition-delay: 0.5s; }
        .delay-6 { transition-delay: 0.6s; }

        body {
            @apply bg-white text-zinc-900 font-sans antialiased;
        }

        /* Smooth section transitions */
        section {
            @apply relative;
        }
    </style>

    @livewireStyles
</head>

<body class="antialiased selection:bg-brand-500 selection:text-white">
    <!-- Navbar -->
    <x-landing.navbar />

    <!-- Content -->
    <main>
        {{ $slot }}
    </main>

    <!-- Footer -->
    <x-landing.footer />

    @livewireScripts

    <!-- Scroll Reveal Animation Engine -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('revealed');
                    }
                });
            }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

            document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-scale').forEach(el => {
                observer.observe(el);
            });
        });
    </script>
</body>

</html>