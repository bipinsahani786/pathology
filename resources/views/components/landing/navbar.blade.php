@props(['transparent' => false])

<nav x-data="{ open: false, scrolled: false }" 
     @scroll.window="scrolled = (window.pageYOffset > 20)"
     :class="{ 'glass bg-white/70 dark:bg-zinc-900/70 border-b border-zinc-200 dark:border-zinc-800 shadow-sm py-2': scrolled, 'bg-transparent py-4': !scrolled && {{ $transparent ? 'true' : 'false' }}, 'bg-white dark:bg-zinc-950 border-b border-zinc-200 dark:border-zinc-800 py-2': !scrolled && !{{ $transparent ? 'true' : 'false' }} }"
     class="fixed top-0 w-full z-50 transition-all duration-300">
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center">
            
            <!-- Logo -->
            <div class="flex-shrink-0 flex items-center">
                <a href="/" class="flex items-center gap-2 group transition-transform duration-300 hover:scale-105">
                    <div class="bg-brand-600 rounded-xl p-2 shadow-lg shadow-brand-500/20">
                        <x-app-logo-icon class="h-6 w-6 text-white" />
                    </div>
                    <span class="font-display font-bold text-xl tracking-tight text-zinc-900 dark:text-white">
                        SWS <span class="text-brand-600">Pathology</span>
                    </span>
                </a>
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="{{ route('about') }}" class="text-sm font-medium text-zinc-600 dark:text-zinc-400 hover:text-brand-600 dark:hover:text-brand-500 transition-colors uppercase tracking-wider">About</a>
                <a href="{{ route('features') }}" class="text-sm font-medium text-zinc-600 dark:text-zinc-400 hover:text-brand-600 dark:hover:text-brand-500 transition-colors uppercase tracking-wider">Features</a>
                <a href="{{ route('pricing') }}" class="text-sm font-medium text-zinc-600 dark:text-zinc-400 hover:text-brand-600 dark:hover:text-brand-500 transition-colors uppercase tracking-wider">Pricing</a>
                <a href="{{ route('contact') }}" class="text-sm font-medium text-zinc-600 dark:text-zinc-400 hover:text-brand-600 dark:hover:text-brand-500 transition-colors uppercase tracking-wider">Contact</a>
            </div>

            <!-- Right: Auth Actions -->
            <div class="hidden md:flex items-center space-x-4">
                @auth
                    <a href="{{ url('/dashboard') }}" class="text-sm font-semibold text-zinc-900 dark:text-white px-6 py-2.5 rounded-full glass hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all duration-300 border border-zinc-200 dark:border-zinc-700">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 hover:text-brand-600 dark:hover:text-brand-500 px-4 transition-colors">Log In</a>
                    <a href="{{ route('register.lab') }}" class="text-sm font-semibold text-white bg-brand-600 px-6 py-2.5 rounded-full shadow-lg shadow-brand-600/30 hover:bg-brand-700 hover:shadow-brand-700/40 hover:-translate-y-0.5 transition-all duration-300">Start Free Trial</a>
                @endauth
            </div>

            <!-- Mobile Menu Button -->
            <div class="md:hidden flex items-center">
                <button @click="open = !open" class="text-zinc-600 dark:text-zinc-400 p-2 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all duration-300">
                    <svg x-show="!open" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg x-show="open" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Overlay -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-4"
         class="md:hidden glass border-t border-zinc-200 dark:border-zinc-800">
        <div class="px-4 pt-2 pb-6 space-y-2">
            <a href="{{ route('about') }}" class="block px-3 py-3 rounded-xl text-base font-medium text-zinc-900 dark:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all duration-300">About</a>
            <a href="{{ route('features') }}" class="block px-3 py-3 rounded-xl text-base font-medium text-zinc-900 dark:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all duration-300">Features</a>
            <a href="{{ route('pricing') }}" class="block px-3 py-3 rounded-xl text-base font-medium text-zinc-900 dark:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all duration-300">Pricing</a>
            <a href="{{ route('contact') }}" class="block px-3 py-3 rounded-xl text-base font-medium text-zinc-900 dark:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all duration-300">Contact</a>
            
            <div class="pt-4 border-t border-zinc-200 dark:border-zinc-800 flex flex-col gap-3">
                <a href="{{ route('login') }}" class="w-full text-center py-4 rounded-xl font-semibold text-zinc-900 dark:text-white glass border border-zinc-200 dark:border-zinc-700">Log In</a>
                <a href="{{ route('register.lab') }}" class="w-full text-center py-4 rounded-xl font-semibold text-white bg-brand-600 shadow-xl shadow-brand-600/30">Start Free Trial</a>
            </div>
        </div>
    </div>
</nav>
