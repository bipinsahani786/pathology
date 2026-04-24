@php
    $siteName = \App\Models\SiteSetting::get('site_name', 'SWS Pathology');
    $siteLogo = \App\Models\SiteSetting::get('site_logo');
    $primaryColor = \App\Models\SiteSetting::get('primary_color', '#0284c7');

    // Split name for styling
    $nameParts = explode(' ', $siteName);
    $firstName = $nameParts[0] ?? 'SWS';
    $lastName = implode(' ', array_slice($nameParts, 1)) ?: 'Pathology';
@endphp

<div class="min-h-screen bg-zinc-50 dark:bg-zinc-950 flex flex-col lg:flex-row font-sans selection:bg-brand-500 selection:text-white transition-colors duration-300">
    <style type="text/tailwindcss">
        @theme {
            --color-brand-50: color-mix(in srgb, {{ $primaryColor }}, white 95%);
            --color-brand-100: color-mix(in srgb, {{ $primaryColor }}, white 90%);
            --color-brand-200: color-mix(in srgb, {{ $primaryColor }}, white 80%);
            --color-brand-400: color-mix(in srgb, {{ $primaryColor }}, white 40%);
            --color-brand-500: {{ $primaryColor }};
            --color-brand-600: color-mix(in srgb, {{ $primaryColor }}, black 10%);
            --color-brand-700: color-mix(in srgb, {{ $primaryColor }}, black 20%);
        }
    </style>

    <!-- Left: Branding & Visual -->
    <div class="hidden lg:flex lg:w-1/2 p-2 relative overflow-hidden">
        <div class="w-full h-full rounded-[2.5rem] bg-zinc-900 border border-white/10 relative overflow-hidden group">
            <img src="/hero_pathology_modern_1775107463115.png"
                class="absolute inset-0 w-full h-full object-cover opacity-30 scale-105 group-hover:scale-110 transition-transform duration-[10s] blur-sm">
            <div class="absolute inset-0 bg-linear-to-br from-brand-600/50 via-zinc-900/90 to-zinc-900"></div>

            <div class="relative z-10 w-full h-full p-20 flex flex-col justify-between">
                <div class="flex items-center gap-3">
                    <div class="bg-white/10 backdrop-blur-md p-2 rounded-2xl border border-white/20">
                        @if($siteLogo)
                            <img src="{{ secure_storage_url($siteLogo) }}" alt="{{ $siteName }}" class="h-10 w-auto">
                        @else
                            <x-app-logo-icon class="h-8 w-8 text-white" />
                        @endif
                    </div>
                    <span
                        class="font-display font-bold text-2xl tracking-tight text-white uppercase tracking-tight">
                        {{ $firstName }} <span class="text-brand-400">{{ $lastName }}</span>
                    </span>
                </div>

                <div class="max-w-md">
                    <div
                        class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-500/20 border border-brand-500/30 mb-8">
                        <span class="text-[10px] font-bold text-brand-400 uppercase tracking-widest">Final Step</span>
                    </div>
                    <h2 class="text-5xl font-bold text-white mb-6 leading-tight tracking-tight">Protect Your
                        <span class="text-brand-400">Account</span> with a new password.
                    </h2>
                    <p class="text-xl text-zinc-400 leading-relaxed">Choose a strong, unique password to ensure your data remains secure and accessible only to you.</p>
                </div>

                <div class="flex items-center gap-12 border-t border-white/10 pt-12">
                    <div class="flex -space-x-3">
                        <div class="w-10 h-10 rounded-full border-2 border-zinc-900 bg-brand-500 flex items-center justify-center text-white"><i class="feather-shield"></i></div>
                        <div class="w-10 h-10 rounded-full border-2 border-zinc-900 bg-brand-600 flex items-center justify-center text-white"><i class="feather-lock"></i></div>
                        <div class="w-10 h-10 rounded-full border-2 border-zinc-900 bg-brand-700 flex items-center justify-center text-white"><i class="feather-key"></i></div>
                    </div>
                    <p class="text-xs text-zinc-500 font-bold uppercase tracking-widest mt-1">Multi-Layer Security</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Right: Reset Password Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 md:p-12">
        <div class="w-full max-w-lg space-y-12">
            <!-- Mobile Logo -->
            <div class="flex items-center gap-3 lg:hidden mb-12">
                <div class="bg-brand-600 p-2.5 rounded-xl">
                    @if($siteLogo)
                        <img src="{{ secure_storage_url($siteLogo) }}" alt="{{ $siteName }}"
                            class="h-8 w-auto brightness-0 invert">
                    @else
                        <x-app-logo-icon class="h-6 w-6 text-white" />
                    @endif
                </div>
                <span
                    class="font-display font-bold text-xl tracking-tight text-zinc-900 dark:text-white uppercase transition-all duration-300">
                    {{ $firstName }} <span class="text-brand-600">{{ $lastName }}</span>
                </span>
            </div>

            <div>
                <h1 class="text-4xl font-bold text-zinc-900 dark:text-white tracking-tight mb-4">Reset Password</h1>
                <p class="text-zinc-500 font-medium">Please create a new secure password for your account.</p>
            </div>

            <form wire:submit.prevent="resetPassword" class="space-y-6">
                <input type="hidden" wire:model="token">

                <!-- Email (Read-only ideally) -->
                <div class="space-y-2">
                    <label class="text-xs font-bold text-zinc-400 uppercase tracking-widest ml-1">Email Address</label>
                    <div class="relative group">
                        <div
                            class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none group-focus-within:text-brand-600 transition-colors text-zinc-400">
                            <i class="feather-mail"></i>
                        </div>
                        <input type="email" wire:model="email" 
                            class="block w-full pl-14 pr-5 py-4 bg-zinc-100 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl focus:outline-hidden font-medium text-zinc-500 cursor-not-allowed shadow-xs"
                            readonly>
                    </div>
                    @error('email') <p class="text-[10px] font-bold text-red-500 ml-1 uppercase tracking-wider animate-shake">{{ $message }}</p> @enderror
                </div>

                <!-- New Password -->
                <div class="space-y-2">
                    <label class="text-xs font-bold text-zinc-400 uppercase tracking-widest ml-1">New Password</label>
                    <div class="relative group">
                        <div
                            class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none group-focus-within:text-brand-600 transition-colors text-zinc-400">
                            <i class="feather-lock"></i>
                        </div>
                        <input type="password" wire:model="password" placeholder="••••••••••••"
                            class="block w-full pl-14 pr-5 py-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl focus:outline-hidden focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all font-medium text-zinc-900 dark:text-white shadow-xs"
                            required>
                    </div>
                    @error('password') <p class="text-[10px] font-bold text-red-500 ml-1 uppercase tracking-wider animate-shake">{{ $message }}</p> @enderror
                </div>

                <!-- Confirm Password -->
                <div class="space-y-2">
                    <label class="text-xs font-bold text-zinc-400 uppercase tracking-widest ml-1">Confirm Password</label>
                    <div class="relative group">
                        <div
                            class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none group-focus-within:text-brand-600 transition-colors text-zinc-400">
                            <i class="feather-check-square"></i>
                        </div>
                        <input type="password" wire:model="password_confirmation" placeholder="••••••••••••"
                            class="block w-full pl-14 pr-5 py-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl focus:outline-hidden focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all font-medium text-zinc-900 dark:text-white shadow-xs"
                            required>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full bg-brand-600 hover:bg-brand-700 text-white font-bold py-5 rounded-2xl shadow-xl shadow-brand-600/30 hover:shadow-brand-700/40 hover:-translate-y-1 transition-all flex justify-center items-center gap-3 group">
                    <span wire:loading.remove>Update Password</span>
                    <i wire:loading.remove
                        class="feather-shield group-hover:scale-110 transition-transform"></i>
                    <div wire:loading class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin">
                    </div>
                    <span wire:loading>Securing Account...</span>
                </button>
            </form>
        </div>
    </div>
</div>
