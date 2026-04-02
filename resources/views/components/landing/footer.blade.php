<footer class="bg-zinc-50 dark:bg-zinc-950 border-t border-zinc-200 dark:border-zinc-900 pt-20 pb-10 relative overflow-hidden transition-colors duration-300">
    <!-- Decorative background elements -->
    <div class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/4 w-96 h-96 bg-brand-500/5 blur-3xl rounded-full"></div>
    <div class="absolute bottom-0 left-0 translate-y-1/2 -translate-x-1/4 w-96 h-96 bg-brand-600/5 blur-3xl rounded-full"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-12 mb-20">
            
            <!-- Branding Column -->
            <div class="col-span-2 lg:col-span-2">
                <a href="/" class="flex items-center gap-2 mb-6 group transition-all duration-300 hover:scale-105">
                    <x-app-logo-icon class="h-8 w-8 text-brand-600" />
                    <span class="font-display font-bold text-2xl tracking-tight text-zinc-900 dark:text-white uppercase transition-all duration-300">
                        SWS <span class="text-brand-600">Pathology</span>
                    </span>
                </a>
                <p class="text-zinc-600 dark:text-zinc-400 leading-relaxed max-w-sm mb-8 text-base">
                    Empowering modern laboratories with precision diagnostics, automated reporting, and secure patient data management. The future of pathology management is here.
                </p>
                <div class="flex space-x-5">
                    <a href="#" class="w-10 h-10 rounded-full glass flex items-center justify-center text-zinc-600 dark:text-zinc-400 hover:bg-brand-600 hover:text-white transition-all duration-300 border border-zinc-200 dark:border-zinc-800 shadow-sm"><i class="feather-twitter"></i></a>
                    <a href="#" class="w-10 h-10 rounded-full glass flex items-center justify-center text-zinc-600 dark:text-zinc-400 hover:bg-brand-600 hover:text-white transition-all duration-300 border border-zinc-200 dark:border-zinc-800 shadow-sm"><i class="feather-facebook"></i></a>
                    <a href="#" class="w-10 h-10 rounded-full glass flex items-center justify-center text-zinc-600 dark:text-zinc-400 hover:bg-brand-600 hover:text-white transition-all duration-300 border border-zinc-200 dark:border-zinc-800 shadow-sm"><i class="feather-linkedin"></i></a>
                    <a href="#" class="w-10 h-10 rounded-full glass flex items-center justify-center text-zinc-600 dark:text-zinc-400 hover:bg-brand-600 hover:text-white transition-all duration-300 border border-zinc-200 dark:border-zinc-800 shadow-sm"><i class="feather-instagram"></i></a>
                </div>
            </div>

            <!-- Product Column -->
            <div class="ps-10">
                <h4 class="text-sm font-bold text-zinc-900 dark:text-white uppercase tracking-widest mb-6">Product</h4>
                <ul class="space-y-4">
                    <li><a href="{{ route('features') }}" class="text-zinc-600 dark:text-zinc-400 hover:text-brand-600 dark:hover:text-brand-500 transition-colors">Lab Automation</a></li>
                    <li><a href="{{ route('features') }}" class="text-zinc-600 dark:text-zinc-400 hover:text-brand-600 dark:hover:text-brand-500 transition-colors">Smart Reporting</a></li>
                    <li><a href="{{ route('features') }}" class="text-zinc-600 dark:text-zinc-400 hover:text-brand-600 dark:hover:text-brand-500 transition-colors">Patient Portal</a></li>
                    <li><a href="{{ route('pricing') }}" class="text-zinc-600 dark:text-zinc-400 hover:text-brand-600 dark:hover:text-brand-500 transition-colors">Pricing Plans</a></li>
                    <li><a href="#" class="text-zinc-600 dark:text-zinc-400 hover:text-brand-600 dark:hover:text-brand-500 transition-colors font-medium text-sm flex items-center gap-1">Changelog <span class="bg-brand-100 dark:bg-brand-900/50 text-brand-600 dark:text-brand-400 text-[10px] px-1.5 py-0.5 rounded-full font-bold">NEW</span></a></li>
                </ul>
            </div>

            <!-- Company Column -->
            <div class="ps-4">
                <h4 class="text-sm font-bold text-zinc-900 dark:text-white uppercase tracking-widest mb-6">Company</h4>
                <ul class="space-y-4">
                    <li><a href="{{ route('about') }}" class="text-zinc-600 dark:text-zinc-400 hover:text-brand-600 dark:hover:text-brand-500 transition-colors">About Us</a></li>
                    <li><a href="#" class="text-zinc-600 dark:text-zinc-400 hover:text-brand-600 dark:hover:text-brand-500 transition-colors">Success Stories</a></li>
                    <li><a href="#" class="text-zinc-600 dark:text-zinc-400 hover:text-brand-600 dark:hover:text-brand-500 transition-colors">Lab Network</a></li>
                    <li><a href="{{ route('contact') }}" class="text-zinc-600 dark:text-zinc-400 hover:text-brand-600 dark:hover:text-brand-500 transition-colors">Contact Sales</a></li>
                    <li><a href="#" class="text-zinc-600 dark:text-zinc-400 hover:text-brand-600 dark:hover:text-brand-500 transition-colors font-medium text-sm">Join Our Team</a></li>
                </ul>
            </div>

            <!-- Support Column -->
            <div>
                <h4 class="text-sm font-bold text-zinc-900 dark:text-white uppercase tracking-widest mb-6">Support</h4>
                <ul class="space-y-4">
                    <li><a href="#" class="text-zinc-600 dark:text-zinc-400 hover:text-brand-600 dark:hover:text-brand-500 transition-colors">Help Center</a></li>
                    <li><a href="#" class="text-zinc-600 dark:text-zinc-400 hover:text-brand-600 dark:hover:text-brand-500 transition-colors font-medium text-sm underline decoration-brand-500/30 underline-offset-4">Developer API</a></li>
                    <li><a href="{{ route('terms') }}" class="text-zinc-600 dark:text-zinc-400 hover:text-brand-600 dark:hover:text-brand-500 transition-colors">Terms of Service</a></li>
                    <li><a href="{{ route('privacy') }}" class="text-zinc-600 dark:text-zinc-400 hover:text-brand-600 dark:hover:text-brand-500 transition-colors font-medium text-sm">Privacy Policy</a></li>
                    <li><a href="#" class="text-zinc-600 dark:text-zinc-400 hover:text-brand-600 dark:hover:text-brand-500 transition-colors">Security Details</a></li>
                </ul>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="border-t border-zinc-200 dark:border-zinc-800 pt-10 flex flex-col md:flex-row justify-between items-center gap-6">
            <p class="text-sm text-zinc-500 dark:text-zinc-500 font-medium">
                &copy; {{ date('Y') }} SWS Pathology SaaS. Crafted with passion for precision diagnostics.
            </p>
            <div class="flex items-center gap-3">
                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Platform Status:</span>
                <div class="flex items-center gap-1.5 bg-emerald-50 dark:bg-emerald-900/20 px-2.5 py-1 rounded-full border border-emerald-200/50 dark:border-emerald-800/50">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-tighter">Operational</span>
                </div>
            </div>
        </div>
    </div>
</footer>
