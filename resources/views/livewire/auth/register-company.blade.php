<div
    class="min-h-screen bg-zinc-50 dark:bg-zinc-950 flex flex-col lg:flex-row font-sans selection:bg-brand-500 selection:text-white transition-colors duration-300">

    <!-- Left: Branding & Value Proposition (Desktop) -->
    <div class="hidden lg:flex lg:w-5/12 p-2 relative overflow-hidden">
        <div class="w-full h-full rounded-[2.5rem] bg-zinc-900 border border-white/10 relative overflow-hidden group">
            <!-- Background Image -->
            <img src="{{ asset('hero_pathology_modern_1775107463115.png') }}"
                class="absolute inset-0 w-full h-full object-cover opacity-20 scale-105 group-hover:scale-110 transition-transform duration-[10s] blur-xs">
            <div class="absolute inset-0 bg-linear-to-br from-brand-600/40 via-zinc-900/90 to-zinc-900"></div>

            <div class="relative z-10 w-full h-full p-16 flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-12">
                        <div class="bg-white/10 backdrop-blur-md p-3 rounded-2xl border border-white/20">
                            <x-app-logo-icon class="h-8 w-8 text-white" />
                        </div>
                        <span
                            class="font-display font-bold text-2xl tracking-tight text-white uppercase transition-all duration-300">
                            SWS <span class="text-brand-400">Pathology</span>
                        </span>
                    </div>

                    <div class="space-y-8 mt-20">
                        <div class="animate-fade-in-up" style="animation-delay: 0.1s">
                            <h2 class="text-4xl font-bold text-white mb-4 leading-tight tracking-tight">The Future of
                                <span class="text-brand-400 italic">Lab Intelligence</span>.
                            </h2>
                            <p class="text-lg text-zinc-400 leading-relaxed max-w-sm">Modernize your diagnostic workflow
                                in minutes. Zero hardware, infinite scale.</p>
                        </div>

                        <div class="space-y-6 pt-10">
                            <div class="flex items-start gap-4 animate-fade-in-up" style="animation-delay: 0.2s">
                                <div
                                    class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-brand-400 shrink-0">
                                    <i class="feather-zap"></i>
                                </div>
                                <div>
                                    <h4 class="text-white font-bold text-sm">Instant Setup</h4>
                                    <p class="text-xs text-zinc-500 mt-1">Get your digital lab live in less than 5
                                        minutes.</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4 animate-fade-in-up" style="animation-delay: 0.3s">
                                <div
                                    class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-emerald-400 shrink-0">
                                    <i class="feather-activity"></i>
                                </div>
                                <div>
                                    <h4 class="text-white font-bold text-sm">Smart Diagnostics</h4>
                                    <p class="text-xs text-zinc-500 mt-1">Automated result entries for 200+ machines.
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4 animate-fade-in-up" style="animation-delay: 0.4s">
                                <div
                                    class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-indigo-400 shrink-0">
                                    <i class="feather-shield"></i>
                                </div>
                                <div>
                                    <h4 class="text-white font-bold text-sm">HIPAA Compliant</h4>
                                    <p class="text-xs text-zinc-500 mt-1">Enterprise-grade security for patient data.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-10 border-t border-white/10">
                    <p class="text-xs text-zinc-500 font-bold uppercase tracking-widest italic">Join 500+ clinics
                        digitizing their future.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Right: Registration Form -->
    <div class="w-full lg:w-7/12 flex items-center justify-center p-6 md:p-16 overflow-y-auto">
        <div class="w-full max-w-2xl space-y-12 py-10">
            <!-- Mobile Header -->
            <div class="flex items-center justify-between lg:hidden mb-10">
                <div class="flex items-center gap-3">
                    <div class="bg-brand-600 p-2.5 rounded-xl">
                        <x-app-logo-icon class="h-6 w-6 text-white" />
                    </div>
                    <span
                        class="font-display font-bold text-xl tracking-tight text-zinc-900 dark:text-white uppercase transition-all duration-300">
                        SWS <span class="text-brand-600">Pathology</span>
                    </span>
                </div>
                <a href="{{ route('login') }}" class="text-sm font-bold text-brand-600">Log In</a>
            </div>

            <div>
                <h1
                    class="text-4xl font-bold text-zinc-900 dark:text-white tracking-tight mb-4 italic underline decoration-brand-500/20 underline-offset-12">
                    Register <span class="text-brand-600">Company</span></h1>
                <p class="text-zinc-500 font-medium">Empower your laboratory with the world's most advanced SaaS LIS.
                </p>
            </div>

            <form wire:submit.prevent="register" class="space-y-8">
                <div class="grid md:grid-cols-2 gap-8">
                    <!-- Lab Name -->
                    <div class="md:col-span-2 space-y-2">
                        <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest ml-1">Laboratory /
                            Clinic Identity</label>
                        <div class="relative group">
                            <div
                                class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none group-focus-within:text-brand-600 transition-colors text-zinc-400">
                                <i class="feather-briefcase"></i>
                            </div>
                            <input type="text" wire:model="lab_name" placeholder="e.g. Apollo Diagnostics Hub"
                                class="block w-full pl-14 pr-5 py-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl focus:outline-hidden focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all font-medium text-zinc-900 dark:text-white shadow-xs"
                                required>
                        </div>
                        @error('lab_name') <p class="text-[10px] font-bold text-red-500 ml-1 uppercase tracking-wider">
                            {{ $message }}
                        </p> @enderror
                    </div>

                    <!-- Owner Name -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest ml-1">Administrator
                            Name</label>
                        <div class="relative group">
                            <div
                                class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none group-focus-within:text-brand-600 transition-colors text-zinc-400">
                                <i class="feather-user"></i>
                            </div>
                            <input type="text" wire:model="owner_name" placeholder="John Smith"
                                class="block w-full pl-14 pr-5 py-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl focus:outline-hidden focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all font-medium text-zinc-900 dark:text-white shadow-xs"
                                required>
                        </div>
                        @error('owner_name') <p
                            class="text-[10px] font-bold text-red-500 ml-1 uppercase tracking-wider">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone Number -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest ml-1">Contact
                            Phone</label>
                        <div class="relative group">
                            <div
                                class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none group-focus-within:text-brand-600 transition-colors text-zinc-400">
                                <i class="feather-phone"></i>
                            </div>
                            <input type="text" wire:model="phone" placeholder="9876543210"
                                class="block w-full pl-14 pr-5 py-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl focus:outline-hidden focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all font-medium text-zinc-900 dark:text-white shadow-xs"
                                required>
                        </div>
                        @error('phone') <p class="text-[10px] font-bold text-red-500 ml-1 uppercase tracking-wider">
                            {{ $message }}
                        </p> @enderror
                    </div>

                    <!-- Email -->
                    <div class="md:col-span-2 space-y-2">
                        <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest ml-1">Official Email
                            Address</label>
                        <div class="relative group">
                            <div
                                class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none group-focus-within:text-brand-600 transition-colors text-zinc-400">
                                <i class="feather-mail"></i>
                            </div>
                            <input type="email" wire:model="email" placeholder="admin@lab.com"
                                class="block w-full pl-14 pr-5 py-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl focus:outline-hidden focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all font-medium text-zinc-900 dark:text-white shadow-xs"
                                required>
                        </div>
                        @error('email') <p class="text-[10px] font-bold text-red-500 ml-1 uppercase tracking-wider">
                            {{ $message }}
                        </p> @enderror
                    </div>

                    <!-- Password -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest ml-1">Security
                            Code</label>
                        <div class="relative group">
                            <div
                                class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none group-focus-within:text-brand-600 transition-colors text-zinc-400">
                                <i class="feather-lock"></i>
                            </div>
                            <input type="password" wire:model="password" placeholder="••••••••"
                                class="block w-full pl-14 pr-5 py-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl focus:outline-hidden focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all font-medium text-zinc-900 dark:text-white shadow-xs"
                                required>
                        </div>
                        @error('password') <p class="text-[10px] font-bold text-red-500 ml-1 uppercase tracking-wider">
                            {{ $message }}
                        </p> @enderror
                    </div>

                    <!-- Password Confirm -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest ml-1">Confirm
                            Secret</label>
                        <div class="relative group">
                            <div
                                class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none group-focus-within:text-brand-600 transition-colors text-zinc-400">
                                <i class="feather-shield"></i>
                            </div>
                            <input type="password" wire:model="password_confirmation" placeholder="••••••••"
                                class="block w-full pl-14 pr-5 py-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl focus:outline-hidden focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all font-medium text-zinc-900 dark:text-white shadow-xs"
                                required>
                        </div>
                    </div>
                </div>

                <!-- Terms -->
                <div
                    class="flex items-start gap-4 p-4 rounded-2xl bg-zinc-100 dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800 transition-all hover:border-brand-500/20">
                    <label class="relative flex items-center cursor-pointer mt-1">
                        <input type="checkbox" wire:model="agree_terms" class="peer hidden">
                        <div
                            class="w-5 h-5 border-2 border-zinc-300 dark:border-zinc-700 rounded-md peer-checked:bg-emerald-600 peer-checked:border-emerald-600 transition-all">
                        </div>
                        <svg class="absolute w-3 h-3 text-white opacity-0 peer-checked:opacity-100 transition-opacity left-1"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                        </svg>
                    </label>
                    <p class="text-xs text-zinc-500 leading-relaxed font-medium">I understand and agree to the <a
                            href="{{ route('terms') }}" class="text-brand-600 font-bold hover:underline">Terms of
                            Service</a> and the <a href="{{ route('privacy') }}"
                            class="text-brand-600 font-bold hover:underline">Secure Data Policy</a>. My lab records will
                        be encrypted by default.</p>
                </div>
                @error('agree_terms') <p class="text-[10px] font-bold text-red-500 ml-1 uppercase tracking-wider">
                    {{ $message }}
                </p> @enderror

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full bg-brand-600 hover:bg-brand-700 text-white font-bold py-5 rounded-2xl shadow-xl shadow-brand-600/30 hover:shadow-brand-700/40 hover:-translate-y-1 transition-all flex justify-center items-center gap-3 group relative overflow-hidden">
                    <div
                        class="absolute inset-0 bg-linear-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000">
                    </div>
                    <span wire:loading.remove wire:target="register">Initialize Laboratory Portal</span>
                    <i wire:loading.remove wire:target="register"
                        class="feather-compass group-hover:rotate-12 transition-transform"></i>
                    <div wire:loading wire:target="register"
                        class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                    <span wire:loading wire:target="register">Setting up Cloud Infrastructure...</span>
                </button>
            </form>

            <div class="pt-8 border-t border-zinc-200 dark:border-zinc-800 text-center">
                <p class="text-zinc-500 font-medium">Already managing a lab? <a href="{{ route('login') }}"
                        wire:navigate
                        class="text-brand-600 hover:text-brand-700 font-bold border-b-2 border-brand-500/20 hover:border-brand-600 transition-all pb-1">Enter
                        your Workspace</a></p>
            </div>
        </div>
    </div>
</div>