<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=AW-18263037225"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'AW-18263037225');
    </script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php
        $routeName = request()->route()?->getName() ?? 'home';
        
        // Map current route to page key, fallback to 'home'
        $pageKeys = ['home', 'about', 'features', 'how-it-works', 'pricing', 'contact', 'enquiry', 'faq', 'terms', 'privacy'];
        $pageKey = in_array($routeName, $pageKeys) ? $routeName : 'home';

        // Global Fallbacks (from 'home')
        $globalTitle = \App\Models\SiteSetting::get('seo_home_title', 'SWS Pathology - Advanced Diagnostic Solutions');
        $globalDesc = \App\Models\SiteSetting::get('seo_home_description', 'Leading pathology management platform');
        $globalKeywords = \App\Models\SiteSetting::get('seo_home_keywords', 'pathology, lab management, software');
        $globalImage = \App\Models\SiteSetting::get('seo_home_og_image', \App\Models\SiteSetting::get('site_logo'));

        // Specific Page SEO
        $pageTitle = \App\Models\SiteSetting::get("seo_{$pageKey}_title") ?: $globalTitle;
        $pageDesc = \App\Models\SiteSetting::get("seo_{$pageKey}_description") ?: $globalDesc;
        $pageKeywords = \App\Models\SiteSetting::get("seo_{$pageKey}_keywords") ?: $globalKeywords;
        $pageCanonical = \App\Models\SiteSetting::get("seo_{$pageKey}_canonical") ?: url()->current();
        
        $rawOgImage = \App\Models\SiteSetting::get("seo_{$pageKey}_og_image") ?: $globalImage;
        $ogImage = $rawOgImage ? secure_storage_url($rawOgImage) : asset('assets/images/icon.webp');
        
        $siteFavicon = \App\Models\SiteSetting::get('site_favicon');
        $brandColor = \App\Models\SiteSetting::get('primary_color', '#0284c7');
        $siteName = \App\Models\SiteSetting::get('site_name', 'SWS Pathology');
    @endphp

    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDesc }}">
    <meta name="keywords" content="{{ $pageKeywords }}">
    <link rel="canonical" href="{{ $pageCanonical }}">

    <!-- Open Graph / Social -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDesc }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:site_name" content="{{ $siteName }}">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDesc }}">
    <meta name="twitter:image" content="{{ $ogImage }}">

    <link rel="shortcut icon" type="image/x-icon" href="{{ $siteFavicon ? secure_storage_url($siteFavicon) : asset('assets/images/icon.webp') }}" />

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

    <!-- Settings fetched above -->

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
    <!-- Floating Action Buttons -->
    @php
        $floatPhone = \App\Models\SiteSetting::get('contact_phone', '07479499718');
        $floatWhatsapp = \App\Models\SiteSetting::get('contact_whatsapp', '07479499718');
    @endphp
    <div class="fixed bottom-6 right-6 z-50 flex flex-col gap-3">
        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $floatWhatsapp) }}" target="_blank" rel="noopener noreferrer" class="w-14 h-14 bg-[#25D366] text-white rounded-full flex items-center justify-center shadow-[0_4px_14px_0_rgba(37,211,102,0.39)] hover:-translate-y-1 hover:shadow-[0_6px_20px_rgba(37,211,102,0.23)] hover:bg-[#128C7E] transition-all duration-300">
            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/></svg>
        </a>
        <a href="tel:{{ $floatPhone }}" class="w-14 h-14 bg-brand-600 text-white rounded-full flex items-center justify-center shadow-lg hover:-translate-y-1 hover:bg-brand-700 transition-all duration-300">
            <i class="feather-phone text-2xl"></i>
        </a>
    </div>
</body>

</html>