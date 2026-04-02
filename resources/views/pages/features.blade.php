<x-landing-layout>
    <x-slot name="title">SWS Pathology - Advanced Laboratory Feature Ecosystem</x-slot>

    <!-- Background Grid & Accents -->
    <div class="fixed inset-0 z-[-1] bg-grid opacity-30"></div>

    <!-- 1. Hero -->
    <section class="pt-32 pb-24 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center animate-fade-in-up">
            <div class="inline-block px-4 py-1.5 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 text-[10px] font-bold uppercase tracking-widest mb-6">Cutting-Edge LIS</div>
            <h1 class="text-5xl md:text-7xl font-bold tracking-tight mb-8 italic">Engineered for <span class="text-brand-600 underline decoration-indigo-500/20 underline-offset-16">Diagnostic Precision</span>.</h1>
            <p class="text-xl text-zinc-500 max-w-2xl mx-auto leading-relaxed">The most comprehensive suite of laboratory intelligence tools ever built for pathology management and cloud analytics.</p>
        </div>
    </section>

    <!-- 2. Section: Automated LIS with Mockup -->
    <section class="py-24 border-zinc-100 dark:border-zinc-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-2 gap-20 items-center">
            <div class="animate-fade-in-up reveal-delay-1">
                <div class="inline-flex items-center gap-2 mb-6 text-brand-600 font-bold uppercase tracking-tighter text-sm">
                    <div class="w-2 h-2 rounded-full bg-brand-500 pulse"></div> Automation Core
                </div>
                <h3 class="text-4xl font-bold mb-8 italic tracking-tight">Direct Bi-Directional <br><span class="text-brand-600">Analyzer Integration</span></h3>
                <p class="text-lg text-zinc-500 mb-10 leading-relaxed font-medium">Our bi-directional interface connects directly to your laboratory equipment, grabbing results the moment they are ready. No human intervention required, reducing error rates by 99%.</p>
                
                <div class="space-y-6">
                    <div class="flex items-center gap-4 text-emerald-600 font-bold group">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center group-hover:scale-110 transition-transform"><i class="feather-check-circle"></i></div>
                        <span>Supports 200+ Manufacturers (Siemens, Roche, Abbott)</span>
                    </div>
                    <div class="flex items-center gap-4 text-brand-600 font-bold group">
                        <div class="w-10 h-10 rounded-xl bg-brand-500/10 flex items-center justify-center group-hover:scale-110 transition-transform"><i class="feather-activity"></i></div>
                        <span>Real-time Hardware Synchronization</span>
                    </div>
                </div>
            </div>
            
            <div class="relative group animate-fade-in-up reveal-delay-2">
                <div class="absolute -inset-6 bg-linear-to-tr from-brand-500/20 to-indigo-500/20 blur-3xl rounded-[3rem] group-hover:scale-105 transition-transform duration-700"></div>
                <img src="{{ asset('pathology_reports_mockup_1775107484267.png') }}" class="relative rounded-[2.5rem] shadow-[0_50px_100px_-20px_rgba(0,0,0,0.2)] border border-white/20 dark:border-white/5" alt="LIS Dashboard Mockup">
                <div class="absolute -bottom-10 -right-10 glass p-8 rounded-[2rem] shadow-2xl animate-float">
                    <div class="text-xs font-bold text-zinc-400 uppercase tracking-widest mb-2">Sync Status</div>
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full bg-emerald-500 pulse"></div>
                        <span class="font-bold text-zinc-900 dark:text-white italic">Hematology (Active)</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. Smart Reporting Features -->
    <section class="py-32 bg-zinc-900 overflow-hidden relative border-y border-white/5">
        <div class="absolute inset-0 bg-grid opacity-5"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <h3 class="text-4xl font-bold mb-20 text-white italic tracking-tight">Intelligent <span class="text-brand-500 underline decoration-white/10 underline-offset-16 italic">Reporting Engine</span></h3>
            
            <div class="grid md:grid-cols-4 gap-12">
                <!-- 4. QR Verification -->
                <div class="space-y-6 group p-8 rounded-[2.5rem] bg-white/5 hover:bg-white/10 transition-all border border-white/5">
                    <div class="w-16 h-16 bg-brand-600 rounded-2xl mx-auto flex items-center justify-center text-white text-2xl group-hover:rotate-12 transition-transform shadow-xl shadow-brand-600/30 font-bold"><i class="feather-maximize"></i></div>
                    <h5 class="font-bold text-white italic">QR Verified</h5>
                    <p class="text-xs text-zinc-500 leading-relaxed font-medium">Instant authenticity verification for patients and physicians via secure cloud-hashes.</p>
                </div>
                <!-- 5. Digital Signatures -->
                <div class="space-y-6 group p-8 rounded-[2.5rem] bg-white/5 hover:bg-white/10 transition-all border border-white/5">
                    <div class="w-16 h-16 bg-indigo-600 rounded-2xl mx-auto flex items-center justify-center text-white text-2xl group-hover:rotate-12 transition-transform shadow-xl shadow-indigo-600/30 font-bold"><i class="feather-edit-3"></i></div>
                    <h5 class="font-bold text-white italic">E-Signatures</h5>
                    <p class="text-xs text-zinc-500 leading-relaxed font-medium">Pathologists can sign reports from anywhere securely via encrypted digital tokens.</p>
                </div>
                <!-- 6. Multilingual -->
                <div class="space-y-6 group p-8 rounded-[2.5rem] bg-white/5 hover:bg-white/10 transition-all border border-white/5">
                    <div class="w-16 h-16 bg-emerald-600 rounded-2xl mx-auto flex items-center justify-center text-white text-2xl group-hover:rotate-12 transition-transform shadow-xl shadow-emerald-600/30 font-bold"><i class="feather-globe"></i></div>
                    <h5 class="font-bold text-white italic">Global Translation</h5>
                    <p class="text-xs text-zinc-500 leading-relaxed font-medium">Generate reports in English, Hindi, and regional languages at the click of a button.</p>
                </div>
                <!-- 7. Whitelabeling -->
                <div class="space-y-6 group p-8 rounded-[2.5rem] bg-white/5 hover:bg-white/10 transition-all border border-white/5">
                    <div class="w-16 h-16 bg-amber-600 rounded-2xl mx-auto flex items-center justify-center text-white text-2xl group-hover:rotate-12 transition-transform shadow-xl shadow-amber-600/30 font-bold"><i class="feather-layers"></i></div>
                    <h5 class="font-bold text-white italic">White-labeling</h5>
                    <p class="text-xs text-zinc-500 leading-relaxed font-medium">Full branding control. Your lab identity remains front and center on every report.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 8. Patient Portal (Self Service) -->
    <section class="py-24 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-2 gap-20 items-center">
            
            <div class="relative order-2 lg:order-1 animate-fade-in-up reveal-delay-2">
                <div class="absolute -inset-10 bg-linear-to-bl from-blue-500/10 to-indigo-500/10 blur-[100px] rounded-full"></div>
                <!-- Smartphone Mockup Styled -->
                <div class="relative w-72 h-[600px] bg-zinc-900 rounded-[3rem] mx-auto border-[10px] border-zinc-800 shadow-2xl flex flex-col items-center justify-center text-white overflow-hidden ring-1 ring-white/10">
                    <div class="absolute top-0 w-1/2 h-6 bg-zinc-800 rounded-b-2xl"></div>
                    <div class="p-8 text-center">
                        <i class="feather-smartphone text-5xl mb-6 text-blue-400"></i>
                        <p class="text-xs uppercase tracking-widest font-bold opacity-40 mb-4 italic">Patient Access</p>
                        <h4 class="text-2xl font-bold italic mb-6 tracking-tight font-display">Download <br>My Report</h4>
                        <div class="w-full h-12 bg-blue-600 rounded-xl mb-4 font-bold flex items-center justify-center text-sm shadow-xl shadow-blue-600/30">Verify QR</div>
                        <div class="w-full h-12 bg-white/5 rounded-xl border border-white/10 flex items-center justify-center text-xs font-bold opacity-30 italic leading-relaxed">Health History</div>
                    </div>
                </div>
            </div>

            <div class="order-1 lg:order-2 animate-fade-in-up reveal-delay-1">
                <div class="inline-flex items-center gap-2 mb-6 text-blue-600 font-bold uppercase tracking-tighter text-sm">
                    <div class="w-2 h-2 rounded-full bg-blue-500 pulse"></div> Ecosystem Integration
                </div>
                <h3 class="text-4xl font-bold mb-8 italic tracking-tight">Patient <span class="text-blue-500 underline decoration-blue-500/20 underline-offset-16 italic">Self-Service Portal</span></h3>
                <p class="text-lg text-zinc-500 mb-10 leading-relaxed font-medium">Empower your patients with a secure digital doorstep. Reduced front-desk friction by 40% with automated report downloads and health history tracking.</p>
                
                <div class="grid grid-cols-2 gap-6">
                    <div class="glass p-6 rounded-2xl border-white/5 transition-all hover:-translate-y-1">
                        <h5 class="text-zinc-900 dark:text-white font-bold italic mb-2">Zero Logins</h5>
                        <p class="text-[10px] text-zinc-500 font-medium">OTP-based secure access for patients. No passwords to remember.</p>
                    </div>
                    <div class="glass p-6 rounded-2xl border-white/5 transition-all hover:-translate-y-1">
                        <h5 class="text-zinc-900 dark:text-white font-bold italic mb-2">Health Radar</h5>
                        <p class="text-[10px] text-zinc-500 font-medium">Comparative analysis of past vs current reports.</p>
                    </div>
                </div>
            </div>
            
        </div>
    </section>

    <!-- 10-15 Additional Mini-Features -->
    <section class="py-24 bg-zinc-50 dark:bg-zinc-950/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h3 class="text-3xl font-bold text-zinc-400 mb-16 uppercase tracking-widest opacity-30 text-center italic">The Full Feature Matrix</h3>
            <div class="grid grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Audit Logs -->
                <div class="glass p-8 rounded-[2rem] border-white/5 flex flex-col gap-4 group hover:bg-zinc-900 hover:text-white transition-all duration-500">
                    <div class="w-10 h-10 rounded-xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-500 group-hover:bg-brand-500 group-hover:text-white transition-all"><i class="feather-database"></i></div>
                    <h5 class="font-bold text-sm italic italic tracking-tight">Immutable Audit Logs</h5>
                    <p class="text-[10px] text-zinc-500 leading-relaxed font-medium">Every action, from billing to report signing, is logged with precise timestamps for compliance.</p>
                </div>
                <!-- WhatsApp -->
                <div class="glass p-8 rounded-[2rem] border-white/5 flex flex-col gap-4 group hover:bg-zinc-900 hover:text-white transition-all duration-500">
                    <div class="w-10 h-10 rounded-xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-emerald-500 group-hover:bg-emerald-500 group-hover:text-white transition-all"><i class="feather-phone-call"></i></div>
                    <h5 class="font-bold text-sm italic italic tracking-tight">WhatsApp / SMS Blasts</h5>
                    <p class="text-[10px] text-zinc-500 leading-relaxed font-medium">Notify patients the second their report is ready with secure download bridges.</p>
                </div>
                <!-- Inventory -->
                <div class="glass p-8 rounded-[2rem] border-white/5 flex flex-col gap-4 group hover:bg-zinc-900 hover:text-white transition-all duration-500">
                    <div class="w-10 h-10 rounded-xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-amber-500 group-hover:bg-amber-500 group-hover:text-white transition-all"><i class="feather-package"></i></div>
                    <h5 class="font-bold text-sm italic italic tracking-tight">Reagent Inventory</h5>
                    <p class="text-[10px] text-zinc-500 leading-relaxed font-medium">Intelligent stock tracking with automatic expiration alerts and consumption analytics.</p>
                </div>
                <!-- API -->
                <div class="glass p-8 rounded-[2rem] border-white/5 flex flex-col gap-4 group hover:bg-zinc-900 hover:text-white transition-all duration-500">
                    <div class="w-10 h-10 rounded-xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-brand-500 group-hover:bg-brand-500 group-hover:text-white transition-all"><i class="feather-command"></i></div>
                    <h5 class="font-bold text-sm italic italic tracking-tight">Open Webhooks</h5>
                    <p class="text-[10px] text-zinc-500 leading-relaxed font-medium">Connect your lab to third-party hospital systems via our secure JSON API layer.</p>
                </div>
                <!-- Performance -->
                <div class="glass p-8 rounded-[2rem] border-white/5 flex flex-col gap-4 group hover:bg-zinc-900 hover:text-white transition-all duration-500">
                    <div class="w-10 h-10 rounded-xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-rose-500 group-hover:bg-rose-500 group-hover:text-white transition-all"><i class="feather-trending-up"></i></div>
                    <h5 class="font-bold text-sm italic italic tracking-tight">Revenue Analytics</h5>
                    <p class="text-[10px] text-zinc-500 leading-relaxed font-medium">Deep insights into lab performance, partner collections, and test bottlenecks.</p>
                </div>
                <!-- Support -->
                <div class="glass p-8 rounded-[2rem] border-white/5 flex flex-col gap-4 group hover:bg-zinc-900 hover:text-white transition-all duration-500">
                    <div class="w-10 h-10 rounded-xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-indigo-500 group-hover:bg-indigo-500 group-hover:text-white transition-all"><i class="feather-headphones"></i></div>
                    <h5 class="font-bold text-sm italic italic tracking-tight">Concierge Onboarding</h5>
                    <p class="text-[10px] text-zinc-500 leading-relaxed font-medium">We don't just sell software; we train your entire team and setup your first branch.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 20. CTA -->
    <section class="py-32 bg-white dark:bg-zinc-950 text-center">
        <h2 class="text-5xl md:text-6xl font-bold mb-12 tracking-tight italic reveal-delay-2 underline underline-offset-16 decoration-brand-500/20 italic">Modernize Your <span class="text-brand-600">DNA</span>.</h2>
        <a href="{{ route('register.lab') }}" class="inline-block px-16 py-6 bg-brand-600 text-white rounded-[2.5rem] font-bold text-xl shadow-2xl shadow-brand-600/40 hover:-translate-y-1 transition-all">Launch Your Digital Lab</a>
    </section>

</x-landing-layout>
