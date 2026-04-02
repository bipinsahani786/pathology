<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'SWS Pathology - Secure Access' }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- Tailwind Play CDN -->
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>

    <style type="text/tailwindcss">
        @theme {
            --font-sans: 'Outfit', 'Inter', ui-sans-serif, system-ui, sans-serif;
            --font-display: 'Outfit', serif;

            --color-brand-50: #f0f9ff;
            --color-brand-100: #e0f2fe;
            --color-brand-200: #bae6fd;
            --color-brand-500: #0ea5e9;
            --color-brand-600: #0284c7;
            --color-brand-700: #0369a1;
            --color-brand-900: #0c4a6e;
        }

        body {
            @apply bg-zinc-50 text-zinc-900 font-sans selection:bg-brand-500 selection:text-white;
        }
    </style>

    @livewireStyles
</head>
<body class="h-full antialiased transition-colors duration-300">
    
    {{ $slot }}

    @livewireScripts
</body>
</html>
