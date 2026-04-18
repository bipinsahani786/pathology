<div
    class="min-h-screen bg-zinc-50 dark:bg-zinc-950 flex flex-col lg:flex-row font-sans selection:bg-brand-500 selection:text-white transition-colors duration-300">
    <!-- Left: Branding & Visual (Desktop Only) -->
    <div class="hidden lg:flex lg:w-1/2 p-2 relative overflow-hidden">
        <div class="w-full h-full rounded-[2.5rem] bg-zinc-900 border border-white/10 relative overflow-hidden group">
            <!-- Decorative BG -->
            <img src="/hero_pathology_modern_1775107463115.png"
                class="absolute inset-0 w-full h-full object-cover opacity-30 scale-105 group-hover:scale-110 transition-transform duration-[10s] blur-sm">
            <div class="absolute inset-0 bg-linear-to-br from-brand-600/50 via-zinc-900/90 to-zinc-900"></div>

            <div class="relative z-10 w-full h-full p-20 flex flex-col justify-between">
                <div class="flex items-center gap-3">
                    <div class="bg-white/10 backdrop-blur-md p-3 rounded-2xl border border-white/20">
                        <x-app-logo-icon class="h-8 w-8 text-white" />
                    </div>
                    <span
                        class="font-display font-bold text-2xl tracking-tight text-white uppercase transition-all duration-300">
                        SWS <span class="text-brand-400">Pathology</span>
                    </span>
                </div>

                <div class="max-w-md">
                    <div
                        class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-500/20 border border-brand-500/30 mb-8">
                        <span class="text-[10px] font-bold text-brand-400 uppercase tracking-widest">Enterprise
                            Ready</span>
                    </div>
                    <h2 class="text-5xl font-bold text-white mb-6 leading-tight tracking-tight">Intelligence at the
                        <span class="text-brand-400">Core</span> of Diagnostics.</h2>
                    <p class="text-xl text-zinc-400 leading-relaxed">Securely manage laboratory reports, patient
                        demographics, and partner settlements in one unified cloud ecosystem.</p>
                </div>

                <div class="flex items-center gap-12 border-t border-white/10 pt-12">
                    <div>
                        <p class="text-3xl font-bold text-white font-display">500+</p>
                        <p class="text-xs text-zinc-500 font-bold uppercase tracking-widest mt-1">Labs Integrated</p>
                    </div>
                    <div>
                        <p class="text-3xl font-bold text-white font-display">1M+</p>
                        <p class="text-xs text-zinc-500 font-bold uppercase tracking-widest mt-1">Reports Monthly</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right: Login Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 md:p-12">
        <div class="w-full max-w-lg space-y-12">
            <!-- Mobile Logo -->
            <div class="flex items-center gap-3 lg:hidden mb-12">
                <div class="bg-brand-600 p-2.5 rounded-xl">
                    <x-app-logo-icon class="h-6 w-6 text-white" />
                </div>
                <span
                    class="font-display font-bold text-xl tracking-tight text-zinc-900 dark:text-white uppercase transition-all duration-300">
                    SWS <span class="text-brand-600">Pathology</span>
                </span>
            </div>

            <div>
                <h1 class="text-4xl font-bold text-zinc-900 dark:text-white tracking-tight mb-4">Welcome Back</h1>
                <p class="text-zinc-500 font-medium">Please enter your credentials to access the laboratory dashboard.
                </p>
            </div>

            <form wire:submit.prevent="login" class="space-y-6">
                <!-- Email or Phone -->
                <div class="space-y-2">
                    <label class="text-xs font-bold text-zinc-400 uppercase tracking-widest ml-1">Identity</label>
                    <div class="relative group">
                        <div
                            class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none group-focus-within:text-brand-600 transition-colors text-zinc-400">
                            <i class="feather-user"></i>
                        </div>
                        <input type="text" wire:model="email" placeholder="Email or 10-digit Phone"
                            class="block w-full pl-14 pr-5 py-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl focus:outline-hidden focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all font-medium text-zinc-900 dark:text-white shadow-xs"
                            required>
                    </div>
                    @error('email') <p
                        class="text-[10px] font-bold text-red-500 ml-1 uppercase tracking-wider animate-shake">
                    {{ $message }}</p> @enderror
                </div>

                <!-- Password -->
                <div class="space-y-2">
                    <div class="flex justify-between items-center px-1">
                        <label class="text-xs font-bold text-zinc-400 uppercase tracking-widest">Secret</label>
                        <a href="#"
                            class="text-xs font-bold text-brand-600 hover:text-brand-700 transition-colors">Forgot
                            Password?</a>
                    </div>
                    <div class="relative group">
                        <div
                            class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none group-focus-within:text-brand-600 transition-colors text-zinc-400">
                            <i class="feather-lock"></i>
                        </div>
                        <input type="password" wire:model="password" placeholder="••••••••"
                            class="block w-full pl-14 pr-5 py-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl focus:outline-hidden focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all font-medium text-zinc-900 dark:text-white shadow-xs"
                            required>
                    </div>
                    @error('password') <p
                        class="text-[10px] font-bold text-red-500 ml-1 uppercase tracking-wider animate-shake">
                    {{ $message }}</p> @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center px-1 justify-between">
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <div class="relative flex items-center">
                            <input type="checkbox" wire:model="remember" class="peer hidden">
                            <div
                                class="w-5 h-5 border-2 border-zinc-300 dark:border-zinc-700 rounded-md peer-checked:bg-brand-600 peer-checked:border-brand-600 transition-all">
                            </div>
                            <svg class="absolute w-3 h-3 text-white opacity-0 peer-checked:opacity-100 transition-opacity left-1"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <span
                            class="text-sm font-semibold text-zinc-600 dark:text-zinc-400 group-hover:text-zinc-900 dark:group-hover:text-white transition-colors">Remember
                            my account</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full bg-brand-600 hover:bg-brand-700 text-white font-bold py-5 rounded-2xl shadow-xl shadow-brand-600/30 hover:shadow-brand-700/40 hover:-translate-y-1 transition-all flex justify-center items-center gap-3 group">
                    <span wire:loading.remove>Sign into Dashboard</span>
                    <i wire:loading.remove
                        class="feather-arrow-right group-hover:translate-x-1 transition-transform"></i>
                    <div wire:loading class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin">
                    </div>
                    <span wire:loading>Authenticating...</span>
                </button>
            </form>

            <div class="pt-8 border-t border-zinc-200 dark:border-zinc-800 text-center">
                <p class="text-zinc-500 font-medium">New to the platform? <a href="{{ route('register.lab') }}"
                        wire:navigate
                        class="text-brand-600 hover:text-brand-700 font-bold border-b-2 border-brand-500/20 hover:border-brand-600 transition-all pb-1">Create
                        Lab Merchant Account</a></p>
            </div>
        </div>
    </div>
</div>