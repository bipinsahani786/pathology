<x-landing-layout>
    <x-slot name="title">SWS Pathology - Precision Diagnostics & Lab Intelligence</x-slot>

    <!-- SECTION 1: Hero -->
    <section class="relative min-h-[90vh] flex items-center pt-20 overflow-hidden">
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('hero_pathology_modern_1775107463115.png') }}" class="w-full h-full object-cover opacity-20 dark:opacity-10 scale-105 animate-slow-zoom" alt="Professional Lab">
            <div class="absolute inset-0 bg-linear-to-b from-zinc-50 via-zinc-50/80 to-zinc-50 dark:from-zinc-950 dark:via-zinc-950/80 dark:to-zinc-950"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="max-w-3xl animate-fade-in-up">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-100 dark:bg-brand-900/30 border border-brand-200 dark:border-brand-800 mb-6 group cursor-default">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-brand-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-brand-500"></span>
                    </span>
                    <span class="text-[10px] font-bold text-brand-700 dark:text-brand-400 uppercase tracking-widest transition-all group-hover:tracking-wider">Next-Gen LIS Platform</span>
                </div>
                <h1 class="font-display text-5xl md:text-7xl font-bold text-zinc-900 dark:text-white leading-[1.1] mb-6 tracking-tight">
                    Intelligence for <span class="text-transparent bg-clip-text bg-linear-to-r from-brand-600 to-indigo-600">Modern Laboratories</span>
                </h1>
                <p class="text-xl text-zinc-600 dark:text-zinc-400 leading-relaxed mb-10 max-w-2xl">
                    Streamline your entire diagnostic workflow from sample collection to automated reporting with our secure, enterprise-grade cloud ecosystem.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('register.lab') }}" class="px-8 py-4 bg-brand-600 text-white rounded-2xl font-bold shadow-xl shadow-brand-600/30 hover:bg-brand-700 hover:-translate-y-1 transition-all duration-300">Get Started for Free</a>
                    <a href="#features" class="px-8 py-4 glass text-zinc-900 dark:text-white rounded-2xl font-bold hover:bg-white/50 dark:hover:bg-zinc-800 transition-all duration-300 border border-zinc-200 dark:border-zinc-800">Watch Demo</a>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 2: Trusted By (Logo Cloud) -->
    <section class="py-20 border-y border-zinc-200/50 dark:border-zinc-800/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-widest mb-10">Powering 500+ Diagnostic Centers Nationwide</p>
            <div class="flex flex-wrap justify-center items-center gap-12 opacity-50 grayscale hover:grayscale-0 transition-all duration-500">
                <span class="text-2xl font-bold text-zinc-900 dark:text-white">METROLAB</span>
                <span class="text-2xl font-bold text-zinc-900 dark:text-white">QUANTUM DIAG</span>
                <span class="text-2xl font-bold text-zinc-900 dark:text-white">COREPATH</span>
                <span class="text-2xl font-bold text-zinc-900 dark:text-white">APEXVUE</span>
                <span class="text-2xl font-bold text-zinc-900 dark:text-white">LIFEBLOOM</span>
            </div>
        </div>
    </section>

    <!-- SECTION 3: Key Feature - Automation -->
    <section id="features" class="py-32 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-20 items-center">
                <div class="relative group">
                    <div class="absolute -inset-4 bg-linear-to-tr from-brand-500/20 to-indigo-500/20 blur-2xl rounded-3xl group-hover:scale-105 transition-transform duration-500"></div>
                    <img src="{{ asset('pathology_reports_mockup_1775107484267.png') }}" class="relative rounded-3xl shadow-2xl border border-white/20" alt="Automation Dashboard">
                    <div class="absolute -bottom-10 -right-10 glass p-6 rounded-2xl shadow-xl animate-float">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-emerald-500 rounded-xl flex items-center justify-center text-white"><i class="feather-check-circle"></i></div>
                            <div>
                                <p class="text-sm font-bold">Report Verified</p>
                                <p class="text-[10px] text-zinc-500 font-medium">Verified by AI Engine</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-brand-600 uppercase tracking-[0.3em] mb-4">Precision Workflow</h2>
                    <h3 class="text-4xl md:text-5xl font-bold text-zinc-900 dark:text-white mb-8 tracking-tight">Automate the <span class="text-brand-600">Uninteresting</span>. Focus on Diagnostics.</h3>
                    <p class="text-lg text-zinc-600 dark:text-zinc-400 mb-10 leading-relaxed">
                        Say goodbye to manual data entry errors. Our smart acquisition system syncs directly with clinical analyzers to populate results in real-time.
                    </p>
                    <ul class="space-y-6">
                        <li class="flex items-center gap-4">
                            <div class="w-6 h-6 rounded-full bg-brand-100 dark:bg-brand-900/30 flex items-center justify-center text-brand-600"><i class="feather-zap text-[12px]"></i></div>
                            <span class="font-medium">Direct LIS-Analyzer Handshake</span>
                        </li>
                        <li class="flex items-center gap-4">
                            <div class="w-6 h-6 rounded-full bg-brand-100 dark:bg-brand-900/30 flex items-center justify-center text-brand-600"><i class="feather-zap text-[12px]"></i></div>
                            <span class="font-medium">Smart Reference Range Mapping</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 4: Feature - Reports -->
    <section class="py-32 bg-white dark:bg-zinc-900/20 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h3 class="text-4xl font-bold mb-16 tracking-tight">Beautifully Structured Reports</h3>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="p-8 rounded-3xl glass transition-all hover:-translate-y-2">
                    <div class="w-14 h-14 bg-indigo-100 dark:bg-indigo-900/30 rounded-2xl flex items-center justify-center text-indigo-600 mb-6"><i class="feather-file-text"></i></div>
                    <h4 class="text-xl font-bold mb-4">Dynamic Templates</h4>
                    <p class="text-zinc-500">Customizable layouts for every test type, from blood chemistry to histopathology.</p>
                </div>
                <!-- Section 5: Patient Portal (part of this grid) -->
                <div class="p-8 rounded-3xl glass transition-all hover:-translate-y-2 border-brand-500/20">
                    <div class="w-14 h-14 bg-brand-100 dark:bg-brand-900/30 rounded-2xl flex items-center justify-center text-brand-600 mb-6"><i class="feather-smartphone"></i></div>
                    <h4 class="text-xl font-bold mb-4">Mobile Access</h4>
                    <p class="text-zinc-500">Patients receive encrypted links to view reports instantly on their smartphones.</p>
                </div>
                <!-- Section 6: Analytics -->
                <div class="p-8 rounded-3xl glass transition-all hover:-translate-y-2">
                    <div class="w-14 h-14 bg-emerald-100 dark:bg-emerald-900/30 rounded-2xl flex items-center justify-center text-emerald-600 mb-6"><i class="feather-trending-up"></i></div>
                    <h4 class="text-xl font-bold mb-4">Trend Tracking</h4>
                    <p class="text-zinc-500">Automatic visualization of historical data for long-term clinical monitoring.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 7: Problem Statement -->
    <section class="py-32 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-zinc-900 dark:bg-zinc-900 rounded-[3rem] p-12 md:p-20 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-brand-500/20 blur-3xl opacity-50"></div>
                <div class="max-w-2xl relative z-10 text-white">
                    <h2 class="text-4xl md:text-5xl font-bold mb-8 tracking-tight">Tired of <br><span class="text-zinc-500">Paper Friction?</span></h2>
                    <p class="text-xl text-zinc-400 leading-relaxed mb-10">
                        Legacy systems slow you down. Manual invoicing, lost samples, and delayed reports cost your lab reputation and revenue. We fixed that.
                    </p>
                    <div class="flex items-center gap-6">
                        <div class="flex -space-x-3">
                            <span class="w-10 h-10 rounded-full bg-zinc-800 border-2 border-zinc-900 flex items-center justify-center text-[10px]">LB</span>
                            <span class="w-10 h-10 rounded-full bg-zinc-700 border-2 border-zinc-900 flex items-center justify-center text-[10px]">MD</span>
                            <span class="w-10 h-10 rounded-full bg-brand-500 border-2 border-zinc-900 flex items-center justify-center text-[10px] font-bold">+12</span>
                        </div>
                        <p class="text-sm font-medium text-zinc-400">Join forward-thinking pathologists</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 8: Solution Summary -->
    <section class="py-32">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-20">
                <h2 class="text-4xl font-bold mb-6 tracking-tight">The All-in-One Operating System</h2>
                <p class="text-zinc-600 dark:text-zinc-400">Everything you need to run a high-volume lab enterprise.</p>
            </div>
            <!-- SECTION 9: Interactive Dashboard Preview (already covered by img intro) -->
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-12">
                <!-- SECTION 10: Smart Billing -->
                <div class="space-y-4">
                    <h4 class="font-bold flex items-center gap-3"><i class="feather-credit-card text-brand-600"></i> Smart Billing</h4>
                    <p class="text-sm text-zinc-500">Integrated POS for instant billing, taxes, and membership discounts.</p>
                </div>
                <!-- SECTION 11: Finance -->
                <div class="space-y-4">
                    <h4 class="font-bold flex items-center gap-3"><i class="feather-dollar-sign text-emerald-600"></i> Partner Portal</h4>
                    <p class="text-sm text-zinc-500">Enable Doctors and Collection Centers to track their referrals and settlements.</p>
                </div>
                <!-- SECTION 12: Inventory (Extra) -->
                <div class="space-y-4">
                    <h4 class="font-bold flex items-center gap-3"><i class="feather-package text-amber-600"></i> Inventory Hub</h4>
                    <p class="text-sm text-zinc-500">Track reagents and kits with automated shelf-life alerts.</p>
                </div>
                <!-- SECTION 13: Stats -->
                <div class="space-y-4">
                    <h4 class="font-bold flex items-center gap-3"><i class="feather-activity text-indigo-600"></i> Metrics</h4>
                    <p class="text-sm text-zinc-500">Visual analytics for sample turnaround time and business growth.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 14: How it Works -->
    <section class="py-32 bg-zinc-50 dark:bg-zinc-950/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-12 text-center">
                <div class="space-y-6">
                    <div class="w-16 h-16 rounded-2xl bg-white dark:bg-zinc-900 shadow-xl mx-auto flex items-center justify-center font-bold text-2xl text-brand-600">1</div>
                    <h5 class="font-bold uppercase text-[10px] tracking-widest text-zinc-400">Register</h5>
                    <p class="text-sm font-medium">Patient onboarding & barcodes</p>
                </div>
                <div class="space-y-6">
                    <div class="w-16 h-16 rounded-2xl bg-white dark:bg-zinc-900 shadow-xl mx-auto flex items-center justify-center font-bold text-2xl text-brand-600">2</div>
                    <h5 class="font-bold uppercase text-[10px] tracking-widest text-zinc-400">Process</h5>
                    <p class="text-sm font-medium">Sample tracking & entry</p>
                </div>
                <div class="space-y-6">
                    <div class="w-16 h-16 rounded-2xl bg-white dark:bg-zinc-900 shadow-xl mx-auto flex items-center justify-center font-bold text-2xl text-brand-600">3</div>
                    <h5 class="font-bold uppercase text-[10px] tracking-widest text-zinc-400">Verify</h5>
                    <p class="text-sm font-medium">Pathologist digital signature</p>
                </div>
                <div class="space-y-6">
                    <div class="w-16 h-16 rounded-2xl bg-white dark:bg-zinc-900 shadow-xl mx-auto flex items-center justify-center font-bold text-2xl text-brand-600">4</div>
                    <h5 class="font-bold uppercase text-[10px] tracking-widest text-zinc-400">Deliver</h5>
                    <p class="text-sm font-medium">SMS, WhatsApp & Portal</p>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 15: Security -->
    <section class="py-32">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center gap-20">
                <div class="flex-1">
                    <h3 class="text-4xl font-bold mb-8 tracking-tight">Bank-Grade <span class="text-blue-500">Security</span></h3>
                    <p class="text-lg text-zinc-600 dark:text-zinc-400 mb-8 leading-relaxed">
                        Data privacy is not a feature; it's a foundation. Every report is encrypted with AES-256 and stored across redundant global servers.
                    </p>
                    <div class="grid grid-cols-2 gap-8">
                        <div>
                            <h6 class="font-bold mb-2 uppercase text-[10px] tracking-widest text-brand-600">HIPAA Compliant</h6>
                            <p class="text-xs text-zinc-500">Global standards for health data.</p>
                        </div>
                        <div>
                            <h6 class="font-bold mb-2 uppercase text-[10px] tracking-widest text-emerald-600">ISO 27001</h6>
                            <p class="text-xs text-zinc-500">Certified information security.</p>
                        </div>
                    </div>
                </div>
                <div class="flex-1 w-full flex justify-center">
                    <div class="w-72 h-72 border-4 border-zinc-200 dark:border-zinc-800 rounded-full flex items-center justify-center relative">
                        <div class="w-56 h-56 border-4 border-brand-500 rounded-full animate-spin-slow animate-duration-[10s]"></div>
                        <div class="absolute inset-0 flex items-center justify-center"><i class="feather-lock text-5xl text-brand-600"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 16: Testimonials -->
    <section class="py-32 bg-brand-600">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
            <h3 class="text-3xl font-bold mb-16">Trusted by the Professionals</h3>
            <div class="grid md:grid-cols-2 gap-12">
                <div class="bg-white/10 p-10 rounded-[2rem] text-left border border-white/20 backdrop-blur-sm">
                    <p class="text-xl italic mb-8">"Since switching to SWS, our turnaround time dropped by 40%. The automated reporting is a lifesaver."</p>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center font-bold">DR</div>
                        <div>
                            <p class="font-bold">Dr. Rajesh Khanna</p>
                            <p class="text-xs opacity-70">Chief Pathologist, KH Labs</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white/10 p-10 rounded-[2rem] text-left border border-white/20 backdrop-blur-sm">
                    <p class="text-xl italic mb-8">"The partner portal changed how we work with agents. Transparency in billing is incredible now."</p>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center font-bold">AM</div>
                        <div>
                            <p class="font-bold">Anu Mishra</p>
                            <p class="text-xs opacity-70">Director, Apex Diagnostics</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 17: Pricing -->
    <section id="pricing" class="py-32">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h3 class="text-4xl font-bold mb-16">Transparent Pricing</h3>
            <div class="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                <div class="p-10 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 space-y-8">
                    <h5 class="font-bold underline decoration-brand-500/30 underline-offset-8">Starter</h5>
                    <div class="text-4xl font-bold">Free</div>
                    <p class="text-sm text-zinc-500">Up to 100 reports/month</p>
                    <a href="{{ route('register.lab') }}" class="block w-full py-3 rounded-xl border border-zinc-200 dark:border-zinc-800 font-bold hover:bg-zinc-50 transition-colors">Start Trial</a>
                </div>
                <div class="p-10 rounded-[2rem] bg-zinc-900 text-white scale-105 shadow-2xl space-y-8">
                    <h5 class="font-bold uppercase text-[10px] tracking-[0.3em] text-brand-400">Professional</h5>
                    <div class="text-4xl font-bold">$49<span class="text-sm font-medium text-zinc-500">/mo</span></div>
                    <p class="text-sm text-zinc-400">Unlimited reports + Partner Portal</p>
                    <a href="{{ route('register.lab') }}" class="block w-full py-3 rounded-xl bg-brand-600 font-bold hover:bg-brand-700 transition-colors shadow-lg shadow-brand-600/30">Select Plan</a>
                </div>
                <div class="p-10 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 space-y-8">
                    <h5 class="font-bold tracking-widest text-zinc-400">Enterprise</h5>
                    <div class="text-4xl font-bold">Custom</div>
                    <p class="text-sm text-zinc-500">Multi-branch + Custom API</p>
                    <a href="{{ route('contact') }}" class="block w-full py-3 rounded-xl border border-zinc-200 dark:border-zinc-800 font-bold hover:bg-zinc-50 transition-colors">Contact Us</a>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 18: FAQ -->
    <section class="py-32 bg-zinc-50 dark:bg-zinc-950/20">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <h3 class="text-3xl font-bold mb-12 text-center uppercase tracking-widest">General Questions</h3>
            <div class="space-y-4">
                <div class="glass p-6 rounded-2xl">
                    <h6 class="font-bold mb-2">Can I use my existing laboratory equipment?</h6>
                    <p class="text-sm text-zinc-500">Yes, we support integration with over 200+ diagnostic analyzers.</p>
                </div>
                <div class="glass p-6 rounded-2xl">
                    <h6 class="font-bold mb-2">Is there a mobile app for collectors?</h6>
                    <p class="text-sm text-zinc-500">Yes, collectors can use our field app to book samples on the fly.</p>
                </div>
                <div class="glass p-6 rounded-2xl">
                    <h6 class="font-bold mb-2">Where is my data stored?</h6>
                    <p class="text-sm text-zinc-500">Data is stored in regionally compliant cloud nodes (AWS/Azure) for zero latency.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 19: Final CTA -->
    <section class="py-32 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <h2 class="text-5xl md:text-6xl font-bold mb-12 tracking-tight">Elevate Your Lab <br><span class="text-brand-600">Experience Today.</span></h2>
            <div class="flex justify-center flex-wrap gap-6">
                <a href="{{ route('register.lab') }}" class="px-12 py-5 bg-brand-600 text-white rounded-[2rem] font-bold text-lg shadow-2xl hover:bg-brand-700 transition-all">Create Free Account</a>
                <a href="{{ route('contact') }}" class="px-12 py-5 glass text-zinc-900 dark:text-white rounded-[2rem] font-bold text-lg border border-zinc-200 dark:border-zinc-800 transition-all">Talk to an Expert</a>
            </div>
        </div>
    </section>

    <!-- SECTION 20: Newsletter Header (The last transition) -->
    <section class="bg-zinc-100 dark:bg-zinc-900/50 py-12 border-t border-zinc-200 dark:border-zinc-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-8">
            <h4 class="text-xl font-bold">Join 2,000+ Pathologists</h4>
            <div class="flex w-full md:w-auto gap-4">
                <input type="email" placeholder="Enter your email" class="flex-1 md:w-80 px-6 py-3 rounded-xl border border-zinc-200 dark:border-zinc-800 focus:outline-hidden focus:ring-2 focus:ring-brand-500 bg-white dark:bg-zinc-950">
                <button class="px-6 py-3 bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 rounded-xl font-bold">Subscribe</button>
            </div>
        </div>
    </section>

</x-landing-layout>
