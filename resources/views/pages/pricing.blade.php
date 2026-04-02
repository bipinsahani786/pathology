<x-landing-layout>
    <x-slot name="title">Pricing Plans - SWS Pathology Advanced Tiering</x-slot>

    <!-- Background Grid & Accents -->
    <div class="fixed inset-0 z-[-1] bg-grid opacity-30"></div>
    <div class="fixed top-0 left-1/4 w-96 h-96 bg-brand-500/10 blur-[120px] rounded-full z-[-1]"></div>
    <div class="fixed bottom-0 right-1/4 w-96 h-96 bg-indigo-500/10 blur-[120px] rounded-full z-[-1]"></div>

    <!-- 1. Hero -->
    <section class="pt-32 pb-20 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center animate-fade-in-up">
            <div class="inline-block px-4 py-1.5 rounded-full bg-brand-100 dark:bg-brand-900/30 text-brand-700 dark:text-brand-300 text-[10px] font-bold uppercase tracking-widest mb-6">Scale with Confidence</div>
            <h1 class="text-5xl md:text-7xl font-bold tracking-tight mb-8 italic">Choose Your <span class="text-brand-600 underline decoration-brand-500/20 underline-offset-16">Diagnostic</span> Power.</h1>
            <p class="text-xl text-zinc-500 max-w-2xl mx-auto leading-relaxed">Predictable pricing for every stage of your laboratory's growth. No hidden fees, just pure innovation.</p>
        </div>
    </section>

    <!-- 2. Main Pricing Grid -->
    <section class="pb-32">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-3 gap-10 items-stretch">
                
                <!-- Starter Plan -->
                <div class="glass p-10 rounded-[3rem] border-white/40 flex flex-col h-full hover:border-brand-500/30 transition-all duration-500 group animate-fade-in-up reveal-delay-1">
                    <div class="mb-8">
                        <h5 class="text-sm font-bold text-zinc-400 uppercase tracking-widest mb-4">Starter Nucleus</h5>
                        <div class="text-4xl font-bold text-zinc-900 dark:text-white italic tracking-tight">Free <span class="text-sm font-normal text-zinc-400 not-italic tracking-normal">forever</span></div>
                    </div>
                    <p class="text-sm text-zinc-500 mb-10 leading-relaxed font-medium">Ideal for independent startup clinics and neighborhood diagnostic nodes.</p>
                    
                    <ul class="space-y-5 mb-12 flex-1">
                        <li class="flex items-center gap-3 text-sm font-medium text-zinc-600 dark:text-zinc-400"><div class="w-5 h-5 rounded-full bg-emerald-500/10 text-emerald-600 flex items-center justify-center shrink-0"><i class="feather-check text-[10px] font-bold"></i></div> 100 Reports / Month</li>
                        <li class="flex items-center gap-3 text-sm font-medium text-zinc-600 dark:text-zinc-400"><div class="w-5 h-5 rounded-full bg-emerald-500/10 text-emerald-600 flex items-center justify-center shrink-0"><i class="feather-check text-[10px] font-bold"></i></div> Basic LIS Interface</li>
                        <li class="flex items-center gap-3 text-sm font-medium text-zinc-600 dark:text-zinc-400"><div class="w-5 h-5 rounded-full bg-emerald-500/10 text-emerald-600 flex items-center justify-center shrink-0"><i class="feather-check text-[10px] font-bold"></i></div> Standard Report PDFs</li>
                        <li class="flex items-center gap-3 text-sm font-medium text-zinc-600 dark:text-zinc-400"><div class="w-5 h-5 rounded-full bg-emerald-500/10 text-emerald-600 flex items-center justify-center shrink-0"><i class="feather-check text-[10px] font-bold"></i></div> Community Support</li>
                    </ul>

                    <a href="{{ route('register.lab') }}" class="block w-full py-5 text-center rounded-[2rem] bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 font-bold text-sm shadow-xl shadow-zinc-900/10 hover:-translate-y-1 transition-all">Start Your Journey</a>
                </div>

                <!-- Professional Plan -->
                <div class="bg-zinc-900 p-12 rounded-[3.5rem] text-white flex flex-col h-full shadow-[0_50px_100px_-20px_rgba(2,132,199,0.3)] relative overflow-hidden group animate-fade-in-up reveal-delay-2 ring-1 ring-white/10 scale-105 z-10">
                    <div class="absolute inset-0 bg-linear-to-br from-brand-600/20 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
                    <div class="absolute -top-6 left-1/2 -translate-x-1/2 bg-brand-500 text-[10px] font-bold uppercase tracking-widest px-6 py-2 rounded-full shadow-2xl shadow-brand-500/50 z-20">Recommended</div>
                    
                    <div class="relative z-10 mb-8">
                        <h5 class="text-sm font-bold text-brand-400 uppercase tracking-widest mb-4">Professional Labs</h5>
                        <div class="text-6xl font-bold italic tracking-tight">$49 <span class="text-sm font-normal text-zinc-500 not-italic tracking-normal">/ month</span></div>
                    </div>
                    <p class="relative z-10 text-sm text-zinc-400 mb-10 leading-relaxed font-medium">Enterprise-grade intelligence for established diagnostic chains and modern centers.</p>
                    
                    <ul class="relative z-10 space-y-5 mb-12 flex-1">
                        <li class="flex items-center gap-3 text-sm font-semibold"><div class="w-6 h-6 rounded-full bg-brand-500/20 text-brand-400 flex items-center justify-center shrink-0"><i class="feather-check text-xs"></i></div> Unlimited Digital Reports</li>
                        <li class="flex items-center gap-3 text-sm font-semibold"><div class="w-6 h-6 rounded-full bg-brand-500/20 text-brand-400 flex items-center justify-center shrink-0"><i class="feather-check text-xs"></i></div> Machine Data Integration</li>
                        <li class="flex items-center gap-3 text-sm font-semibold"><div class="w-6 h-6 rounded-full bg-brand-500/20 text-brand-400 flex items-center justify-center shrink-0"><i class="feather-check text-xs"></i></div> Partner Portal (Doctors/Agents)</li>
                        <li class="flex items-center gap-3 text-sm font-semibold text-brand-300 italic"><div class="w-6 h-6 rounded-full bg-brand-500 text-white flex items-center justify-center shrink-0 shadow-lg shadow-brand-500/50"><i class="feather-zap text-xs"></i></div> Quantum Priority Support</li>
                    </ul>

                    <a href="{{ route('register.lab') }}" class="relative z-10 block w-full py-6 text-center rounded-[2.5rem] bg-brand-600 text-white font-bold text-lg shadow-2xl shadow-brand-600/40 hover:bg-brand-500 hover:-translate-y-1 transition-all overflow-hidden group/btn">
                        <div class="absolute inset-0 bg-linear-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover/btn:translate-x-full transition-transform duration-1000"></div>
                        Deploy Pro Now
                    </a>
                </div>

                <!-- Enterprise Plan -->
                <div class="glass p-10 rounded-[3rem] border-white/40 flex flex-col h-full hover:border-indigo-500/30 transition-all duration-500 group animate-fade-in-up reveal-delay-3">
                    <div class="mb-8">
                        <h5 class="text-sm font-bold text-zinc-400 uppercase tracking-widest mb-4">Enterprise Grid</h5>
                        <div class="text-4xl font-bold text-zinc-900 dark:text-white italic tracking-tight">Custom</div>
                    </div>
                    <p class="text-sm text-zinc-500 mb-10 leading-relaxed font-medium">For hospital networks, multi-continent labs, and heavy pathology infrastructure.</p>
                    
                    <ul class="space-y-5 mb-12 flex-1">
                        <li class="flex items-center gap-3 text-sm font-medium text-zinc-600 dark:text-zinc-400"><div class="w-5 h-5 rounded-full bg-indigo-500/10 text-indigo-600 flex items-center justify-center shrink-0"><i class="feather-check text-[10px] font-bold"></i></div> Unified Multi-Branch Sync</li>
                        <li class="flex items-center gap-3 text-sm font-medium text-zinc-600 dark:text-zinc-400"><div class="w-5 h-5 rounded-full bg-indigo-500/10 text-indigo-600 flex items-center justify-center shrink-0"><i class="feather-check text-[10px] font-bold"></i></div> Custom API Architecture</li>
                        <li class="flex items-center gap-3 text-sm font-medium text-zinc-600 dark:text-zinc-400"><div class="w-5 h-5 rounded-full bg-indigo-500/10 text-indigo-600 flex items-center justify-center shrink-0"><i class="feather-check text-[10px] font-bold"></i></div> Local Server Deployment</li>
                        <li class="flex items-center gap-3 text-sm font-medium text-zinc-600 dark:text-zinc-400"><div class="w-5 h-5 rounded-full bg-indigo-500/10 text-indigo-600 flex items-center justify-center shrink-0"><i class="feather-check text-[10px] font-bold"></i></div> Dedicated Data Officer</li>
                    </ul>

                    <a href="{{ route('contact') }}" class="block w-full py-5 text-center rounded-[2rem] border border-zinc-200 dark:border-zinc-800 font-bold text-sm hover:bg-zinc-100 dark:hover:bg-zinc-900 transition-all">Consult Our Experts</a>
                </div>

            </div>
        </div>
    </section>

    <!-- 3. Comparison Table -->
    <section class="py-24 relative">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <h3 class="text-3xl font-bold text-center mb-20 italic">Deep <span class="text-brand-600 underline decoration-brand-200 underline-offset-12">Capability</span> Matrix</h3>
            
            <div class="overflow-hidden rounded-[3rem] border border-zinc-200 dark:border-zinc-800 shadow-2xl glass">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-zinc-900 text-white">
                            <th class="p-8 font-bold text-xs uppercase tracking-[0.2em] opacity-40">Operational Feature</th>
                            <th class="p-8 font-bold text-sm italic">Starter</th>
                            <th class="p-8 font-bold text-sm italic text-brand-400">Professional</th>
                            <th class="p-8 font-bold text-sm italic">Enterprise</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800 text-sm font-medium text-zinc-600 dark:text-zinc-400">
                        <tr class="hover:bg-brand-500/5 transition-colors">
                            <td class="p-8 border-r border-zinc-100 dark:border-zinc-800">Analyzer Auto-Sync</td><td class="p-8">No</td><td class="p-8 text-brand-600 font-bold italic">Included</td><td class="p-8">Full Cluster</td>
                        </tr>
                        <tr class="hover:bg-brand-500/5 transition-colors">
                            <td class="p-8 border-r border-zinc-100 dark:border-zinc-800">Doc/Agent Settlements</td><td class="p-8 opacity-30">Manual</td><td class="p-8 text-brand-600 font-bold italic">Automated</td><td class="p-8">Real-time RTGS</td>
                        </tr>
                        <tr class="hover:bg-brand-500/5 transition-colors">
                            <td class="p-8 border-r border-zinc-100 dark:border-zinc-800">Audit Logs (HIPAA)</td><td class="p-8 italic">Basic</td><td class="p-8 text-brand-600 font-bold italic">Full History</td><td class="p-8">Immutable Ledger</td>
                        </tr>
                        <tr class="hover:bg-brand-500/5 transition-colors">
                            <td class="p-8 border-r border-zinc-100 dark:border-zinc-800">Cloud Storage</td><td class="p-8">2 GB</td><td class="p-8 text-brand-600 font-bold italic">Unlimited</td><td class="p-8">Encryption At-Rest</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- 4. ROI & Final CTA -->
    <section class="py-32 bg-zinc-900 relative overflow-hidden">
        <div class="absolute inset-0 bg-grid opacity-5"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <h2 class="text-5xl md:text-6xl font-bold text-white mb-12 italic">Upgrade Your <br><span class="text-brand-500 underline decoration-white/10 underline-offset-16">Diagnostic Intelligence</span>.</h2>
            <p class="text-zinc-400 max-w-2xl mx-auto mb-16 text-lg">Join the medical revolution. Our 14-day Professional Trial starts instantly with no card required.</p>
            
            <div class="flex flex-wrap justify-center gap-8">
                <a href="{{ route('register.lab') }}" class="px-16 py-6 bg-brand-600 text-white rounded-[2.5rem] font-bold text-lg shadow-2xl shadow-brand-600/40 hover:-translate-y-1 transition-all">Start Your 14-Day Pro Trial</a>
            </div>
            
            <div class="mt-20 flex justify-center gap-16 grayscale opacity-30">
                <i class="feather-shield text-4xl text-white"></i>
                <i class="feather-lock text-4xl text-white"></i>
                <i class="feather-cpu text-4xl text-white"></i>
            </div>
        </div>
    </section>

</x-landing-layout>
