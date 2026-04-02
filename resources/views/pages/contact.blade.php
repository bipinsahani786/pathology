<x-landing-layout>
    <x-slot name="title">Contact SWS Pathology - Support & Enterprise Sales</x-slot>

    <!-- Background Grid & Accents -->
    <div class="fixed inset-0 z-[-1] bg-grid opacity-30"></div>
    <div class="fixed top-1/4 right-0 w-96 h-96 bg-brand-500/10 blur-[120px] rounded-full z-[-1]"></div>

    <!-- 1. Hero -->
    <section class="pt-32 pb-20 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center animate-fade-in-up">
            <div class="inline-block px-4 py-1.5 rounded-full bg-brand-100 dark:bg-brand-900/30 text-brand-700 dark:text-brand-300 text-[10px] font-bold uppercase tracking-widest mb-6">Global Resilience Support</div>
            <h1 class="text-5xl md:text-7xl font-bold tracking-tight mb-8 italic italic tracking-tight">Connect With Our <span class="text-brand-600 underline decoration-indigo-500/20 underline-offset-16 italic">Core HUB</span>.</h1>
            <p class="text-xl text-zinc-500 max-w-2xl mx-auto leading-relaxed">Whether you're a single clinic or a multi-continent diagnostic group, our experts are ready to calibrate your digital future.</p>
        </div>
    </section>

    <!-- 2. Contact Grid (Form + Info) -->
    <section class="pb-32 bg-white dark:bg-zinc-950 border-y border-zinc-100 dark:border-zinc-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-2 gap-20 py-24">
            
            <!-- Info Side -->
            <div class="space-y-12 animate-fade-in-up reveal-delay-1">
                <div>
                    <h3 class="text-3xl font-bold mb-6 italic tracking-tight">Where Innovation <span class="text-brand-600 underline decoration-brand-500/20 underline-offset-12 italic">Meets Diagnostics</span>.</h3>
                    <p class="text-lg text-zinc-500 leading-relaxed font-medium mb-10 italic">"Our headquarters isn't just an office; it's the digital heartbeat of modern pathology."</p>
                </div>

                <div class="grid sm:grid-cols-2 gap-8">
                    <!-- 3. Tech Support -->
                    <div class="glass p-8 rounded-[2.5rem] border-white/5 transition-all hover:border-brand-500/20">
                        <div class="w-10 h-10 rounded-xl bg-brand-500/10 text-brand-600 flex items-center justify-center mb-6"><i class="feather-headphones"></i></div>
                        <h5 class="font-bold mb-2 italic tracking-tight">Global Support</h5>
                        <p class="text-xs text-zinc-400 font-medium">Available 24/7 for critical LIS emergencies.</p>
                        <p class="text-brand-600 font-bold text-sm mt-4 italic">support@sws.com</p>
                    </div>
                    <!-- 4. Sales -->
                    <div class="glass p-8 rounded-[2.5rem] border-white/5 transition-all hover:border-brand-500/20">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center mb-6"><i class="feather-briefcase"></i></div>
                        <h5 class="font-bold mb-2 italic tracking-tight">Enterprise Sales</h5>
                        <p class="text-xs text-zinc-400 font-medium">Custom deployment & hospital pricing.</p>
                        <p class="text-emerald-600 font-bold text-sm mt-4 italic">sales@sws.com</p>
                    </div>
                </div>

                <div class="pt-8 border-t border-zinc-100 dark:border-zinc-800">
                    <p class="text-xs text-zinc-400 font-bold uppercase tracking-[0.2em] mb-4 italic">Social Channels</p>
                    <div class="flex gap-6 opacity-40 hover:opacity-100 transition-opacity">
                        <a href="#"><i class="feather-linkedin text-2xl"></i></a><a href="#"><i class="feather-twitter text-2xl"></i></a><a href="#"><i class="feather-github text-2xl"></i></a>
                    </div>
                </div>
            </div>

            <!-- Form Side -->
            <div class="animate-fade-in-up reveal-delay-2">
                <div class="glass p-12 rounded-[3.5rem] border-white/40 shadow-2xl shadow-brand-500/10 relative overflow-hidden group">
                    <div class="absolute inset-0 bg-linear-to-br from-brand-600/5 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
                    
                    <h3 class="text-2xl font-bold text-zinc-900 dark:text-white mb-8 tracking-tight italic">Initiate <span class="text-brand-600 underline decoration-indigo-500/20 underline-offset-12 italic tracking-tight">Consultation</span></h3>
                    
                    <form class="space-y-6 relative z-10">
                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest ml-1">Your Full Name</label>
                                <input type="text" class="block w-full px-6 py-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl focus:outline-hidden focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all font-medium text-sm text-zinc-900 dark:text-white" required>
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest ml-1">Clinic / Lab Identity</label>
                                <input type="text" class="block w-full px-6 py-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl focus:outline-hidden focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all font-medium text-sm text-zinc-900 dark:text-white" required>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest ml-1">Official Email Address</label>
                            <input type="email" class="block w-full px-6 py-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl focus:outline-hidden focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all font-medium text-sm text-zinc-900 dark:text-white" required>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest ml-1">Brief Description of Needs</label>
                            <textarea rows="4" class="block w-full px-6 py-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl focus:outline-hidden focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all font-medium text-sm text-zinc-900 dark:text-white italic resize-none"></textarea>
                        </div>

                        <button type="button" class="w-full bg-brand-600 text-white font-bold py-5 rounded-[2rem] shadow-xl shadow-brand-600/30 hover:bg-brand-500 hover:-translate-y-1 transition-all overflow-hidden group relative">
                            <div class="absolute inset-0 bg-linear-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                            Signal Our HUB <i class="feather-send ml-2 group-hover:rotate-12 transition-transform"></i>
                        </button>
                    </form>
                </div>
            </div>
            
        </div>
    </section>

    <!-- 5. Location Stats (Final Section) -->
    <section class="py-24 bg-zinc-50 dark:bg-zinc-950/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-12 text-center opacity-40">
                <div><p class="text-3xl font-bold text-zinc-900 dark:text-white italic">Silicon Hub</p><p class="text-xs font-bold uppercase tracking-widest text-zinc-500">Tech Center</p></div>
                <div><p class="text-3xl font-bold text-zinc-900 dark:text-white italic">London</p><p class="text-xs font-bold uppercase tracking-widest text-zinc-500">Euro Admin</p></div>
                <div><p class="text-3xl font-bold text-zinc-900 dark:text-white italic">Singapore</p><p class="text-xs font-bold uppercase tracking-widest text-zinc-500">APAC Node</p></div>
                <div><p class="text-3xl font-bold text-zinc-900 dark:text-white italic">New York</p><p class="text-xs font-bold uppercase tracking-widest text-zinc-500">Global Sales</p></div>
            </div>
        </div>
    </section>

</x-landing-layout>
