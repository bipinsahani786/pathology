<x-landing-layout>
    <x-slot name="title">About SWS Pathology - The Digital Heart of Diagnostics</x-slot>

    <!-- Background Grid & Accents -->
    <div class="fixed inset-0 z-[-1] bg-grid opacity-30"></div>
    <div class="fixed top-0 left-1/2 w-96 h-96 bg-brand-500/10 blur-[150px] rounded-full z-[-1] -translate-x-1/2"></div>

    <!-- 1. Hero -->
    <section class="py-32 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center animate-fade-in-up">
            <div class="inline-block px-4 py-1.5 rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 text-[10px] font-bold uppercase tracking-widest mb-6 italic tracking-tight">Our Global Mission</div>
            <h1 class="text-5xl md:text-7xl font-bold tracking-tight mb-8 leading-tight italic">Precision in Every <span class="text-brand-600 underline decoration-brand-500/20 underline-offset-16 italic tracking-tight">Diagnostic Pulse</span>.</h1>
            <p class="text-xl text-zinc-500 max-w-2xl mx-auto leading-relaxed">We aren't just building software; we're building the nervous system for the next generation of diagnostic medicine.</p>
        </div>
    </section>

    <!-- 2. Heritage Section (Split Image/Text) -->
    <section class="py-24 border-y border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-2 gap-20 items-center">
            <div class="relative group animate-fade-in-up reveal-delay-1">
                <div class="absolute -inset-6 bg-linear-to-tr from-brand-600/20 to-indigo-600/20 blur-3xl rounded-[3rem] group-hover:scale-105 transition-transform duration-700"></div>
                <div class="relative rounded-[3rem] bg-zinc-900 h-96 overflow-hidden border border-white/10 shadow-2xl">
                    <img src="{{ asset('hero_pathology_modern_1775107463115.png') }}" class="w-full h-full object-cover opacity-30 grayscale group-hover:grayscale-0 transition-all duration-1000 blur-xs group-hover:blur-0">
                    <div class="absolute inset-0 bg-linear-to-t from-zinc-950 via-transparent to-transparent"></div>
                    <div class="absolute bottom-10 left-10 text-white italic tracking-tight font-bold text-2xl">Founded 2024.</div>
                </div>
            </div>
            
            <div class="animate-fade-in-up reveal-delay-2">
                <h3 class="text-4xl font-bold mb-8 italic tracking-tight">Built by <span class="text-brand-600 underline decoration-zinc-100 dark:decoration-zinc-800 underline-offset-12 italic">Experts</span>, for Professionals.</h3>
                <p class="text-lg text-zinc-500 mb-10 leading-relaxed font-medium">SWS Pathology emerged from a collaboration between veteran pathologists and software engineers who were tired of legacy systems slowing down critical medical decisions. Our goal was to create a zero-friction, automated LIS that works as fast as a diagnostic team thinks.</p>
                
                <div class="flex items-center gap-10">
                    <div>
                        <p class="text-3xl font-bold text-zinc-900 dark:text-white font-display italic">500+</p>
                        <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mt-1">Labs Integrated</p>
                    </div>
                    <div class="w-px h-10 bg-zinc-200 dark:bg-zinc-800"></div>
                    <div>
                        <p class="text-3xl font-bold text-zinc-900 dark:text-white font-display italic">99.9%</p>
                        <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mt-1">Uptime SLA</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. Our Values (Six Sections) -->
    <section class="py-32">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h3 class="text-3xl font-bold mb-20 text-center uppercase tracking-[0.3em] text-zinc-400 opacity-30 italic reveal-delay-1">Our Core Pillars</h3>
            <div class="grid md:grid-cols-3 gap-10">
                <!-- Precision -->
                <div class="glass p-10 rounded-[2.5rem] border-white/5 transition-all group hover:bg-zinc-900 hover:text-white hover:scale-105 duration-500 reveal-delay-1 animate-fade-in-up">
                    <div class="w-12 h-12 rounded-xl bg-brand-500/10 text-brand-600 flex items-center justify-center mb-6 group-hover:bg-brand-500 group-hover:text-white transition-all"><i class="feather-target"></i></div>
                    <h5 class="text-xl font-bold italic mb-4">Absolute Precision</h5>
                    <p class="text-xs text-zinc-500 group-hover:text-zinc-400 font-medium leading-relaxed italic">Every data point verified through triple-layer integrity checks before being transmitted to clinical reports.</p>
                </div>
                <!-- Innovation -->
                <div class="glass p-10 rounded-[2.5rem] border-white/5 transition-all group hover:bg-zinc-900 hover:text-white hover:scale-105 duration-500 reveal-delay-2 animate-fade-in-up">
                    <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center mb-6 group-hover:bg-emerald-500 group-hover:text-white transition-all"><i class="feather-zap"></i></div>
                    <h5 class="text-xl font-bold italic mb-4">Deep Innovation</h5>
                    <p class="text-xs text-zinc-500 group-hover:text-zinc-400 font-medium leading-relaxed italic">Constantly pushing the boundaries of what a Diagnostic OS can achieve with AI and bi-directional sync.</p>
                </div>
                <!-- Empathy -->
                <div class="glass p-10 rounded-[2.5rem] border-white/5 transition-all group hover:bg-zinc-900 hover:text-white hover:scale-105 duration-500 reveal-delay-3 animate-fade-in-up">
                    <div class="w-12 h-12 rounded-xl bg-rose-500/10 text-rose-600 flex items-center justify-center mb-6 group-hover:bg-rose-500 group-hover:text-white transition-all"><i class="feather-heart"></i></div>
                    <h5 class="text-xl font-bold italic mb-4">Human Empathy</h5>
                    <p class="text-xs text-zinc-500 group-hover:text-zinc-400 font-medium leading-relaxed italic">Designed with a deep understanding of the high-pressure environment of diagnostics and patient care.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. Timeline Grid (Mini Sections) -->
    <section class="py-24 bg-zinc-900 relative overflow-hidden border-zinc-800">
        <div class="absolute inset-0 bg-grid opacity-5"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-white">
            <h3 class="text-2xl font-bold mb-16 text-center italic tracking-tight underline decoration-brand-600 underline-offset-16">The Road to <span class="text-brand-500">2026</span></h3>
            <div class="grid md:grid-cols-4 gap-12 text-center opacity-70 group hover:opacity-100 transition-opacity">
                <div class="space-y-4">
                    <h6 class="text-[10px] font-bold text-brand-400 uppercase tracking-widest italic font-bold">Mar 2024</h6>
                    <p class="font-bold text-lg italic tracking-tight">Ecosystem Reveal</p>
                    <p class="text-[10px] text-zinc-500 italic">Initial launch with Core LIS engine.</p>
                </div>
                <div class="space-y-4">
                    <h6 class="text-[10px] font-bold text-brand-400 uppercase tracking-widest italic font-bold">Aug 2024</h6>
                    <p class="font-bold text-lg italic tracking-tight">100 Hub Milestone</p>
                    <p class="text-[10px] text-zinc-500 italic">100 labs integrated across 12 countries.</p>
                </div>
                <div class="space-y-4">
                    <h6 class="text-[10px] font-bold text-brand-400 uppercase tracking-widest italic font-bold">Feb 2025</h6>
                    <p class="font-bold text-lg italic tracking-tight">AI-Core Release</p>
                    <p class="text-[10px] text-zinc-500 italic">Automated result sanity-checks live.</p>
                </div>
                <div class="space-y-4">
                    <h6 class="text-[10px] font-bold text-brand-400 uppercase tracking-widest italic font-bold">Current</h6>
                    <p class="font-bold text-lg italic tracking-tight text-white underline decoration-brand-500 underline-offset-8">Scale Unbound</p>
                    <p class="text-[10px] text-zinc-500 italic">Global diagnostic infrastructure node.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 5. Final Vision -->
    <section class="py-32 text-center">
        <div class="max-w-3xl mx-auto px-4">
            <h2 class="text-5xl font-bold mb-12 tracking-tight italic">Diagnostics as a <span class="text-brand-600 underline underline-offset-16 decoration-brand-500/20 italic">Global Nerve System</span>.</h2>
            <p class="text-zinc-400 italic mb-16 text-lg leading-relaxed">"We aren't creating just another laboratory management tool. We're building the technology that ensures nobody has to wait for a life-altering medical truth."</p>
            <div class="w-12 h-px bg-zinc-200 dark:bg-zinc-800 mx-auto mb-8"></div>
            <p class="text-xs font-bold uppercase tracking-[0.3em] text-zinc-500 italic tracking-tight">SWS PATHOLOGY ARCHITECTURE TEAM</p>
        </div>
    </section>

</x-landing-layout>
