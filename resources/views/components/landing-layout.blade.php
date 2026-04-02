<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'SWS Pathology - Advanced Diagnostic Solutions' }}</title>
    
    <!-- Fonts: Outfit for display, Inter for body -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- Tailwind Play CDN (For instant viewing without build errors) -->
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>

    <style type="text/tailwindcss">
        @theme {
            --font-sans: 'Outfit', 'Inter', ui-sans-serif, system-ui, sans-serif;
            --font-display: 'Instrument Sans', serif;

            --color-brand-50: #f0f9ff;
            --color-brand-100: #e0f2fe;
            --color-brand-200: #bae6fd;
            --color-brand-500: #0ea5e9;
            --color-brand-600: #0284c7;
            --color-brand-700: #0369a1;
            --color-brand-900: #0c4a6e;

            --color-accent: var(--color-brand-600);
            
            /* Animation Tokens */
            --animate-float: float 6s ease-in-out infinite;
            --animate-fade-in-up: fadeInUp 0.8s ease-out forwards;
            --animate-reveal: reveal 1s cubic-bezier(0.77, 0, 0.175, 1) forwards;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes reveal {
            from { clip-path: inset(100% 0 0 0); }
            to { clip-path: inset(0 0 0 0); }
        }

        .glass {
            @apply bg-white/70 backdrop-blur-md border border-white/20 shadow-xl;
        }
        .dark .glass {
            @apply bg-zinc-900/70 backdrop-blur-md border border-white/10 shadow-2xl;
        }

        body {
            @apply bg-zinc-50 text-zinc-900 font-sans selection:bg-brand-500 selection:text-white transition-opacity duration-500;
        }

        .bg-grid {
            background-size: 40px 40px;
            background-image: 
                linear-gradient(to right, rgba(0,0,0,0.03) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(0,0,0,0.03) 1px, transparent 1px);
        }
        .dark .bg-grid {
            background-image: 
                linear-gradient(to right, rgba(255,255,255,0.03) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255,255,255,0.03) 1px, transparent 1px);
        }

        .reveal-delay-1 { animation-delay: 0.1s; }
        .reveal-delay-2 { animation-delay: 0.2s; }
        .reveal-delay-3 { animation-delay: 0.3s; }
    </style>

    @livewireStyles
</head>
<body class="antialiased selection:bg-brand-500 selection:text-white transition-colors duration-300">
    <!-- Navbar -->
    <x-landing.navbar />

    <!-- Content slot -->
    <main>
        {{ $slot }}
    </main>

    <!-- Footer -->
    <x-landing.footer />

    @livewireScripts
</body>
</html>
