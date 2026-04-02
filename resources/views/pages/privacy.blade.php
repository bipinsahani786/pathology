<x-landing-layout>
    <x-slot name="title">Privacy Policy - SWS Pathology Data Protection</x-slot>

    <!-- 1. Hero -->
    <section class="pt-32 pb-20 bg-zinc-50 dark:bg-zinc-950 border-b border-zinc-100 dark:border-zinc-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center animate-fade-in-up">
            <div class="inline-block px-4 py-1.5 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold uppercase tracking-widest mb-6">Security First</div>
            <h1 class="text-4xl md:text-6xl font-bold tracking-tight mb-6 italic tracking-tight underline decoration-emerald-500/20 underline-offset-12">Privacy <span class="text-emerald-600">Policy</span></h1>
            <p class="text-sm text-zinc-400 font-bold uppercase tracking-tighter">Compliant with HIPAA, GDPR & Global Health Standards</p>
        </div>
    </section>

    <!-- 2-20. Privacy Content Blocks -->
    <section class="py-24 bg-white dark:bg-zinc-950">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="space-y-20">
                
                <!-- 2. Data We Collect -->
                <div class="group">
                    <h3 class="text-2xl font-bold mb-6 flex items-center gap-4 text-zinc-900 dark:text-white group-hover:text-emerald-600 transition-colors"><i class="feather-database"></i> Data Collection</h3>
                    <div class="prose prose-zinc dark:prose-invert max-w-none text-zinc-600 dark:text-zinc-400 leading-relaxed">
                        We collect laboratory merchant information (name, tax ID, location) and diagnostic metadata. Patient health records are stored in encrypted silos and are only accessible by authorized subscriber accounts.
                    </div>
                </div>

                <!-- 3. Usage of Information -->
                <div class="group">
                    <h3 class="text-2xl font-bold mb-6 flex items-center gap-4 text-zinc-900 dark:text-white group-hover:text-emerald-600 transition-colors"><i class="feather-activity"></i> Usage & Processing</h3>
                    <div class="prose prose-zinc dark:prose-invert max-w-none text-zinc-600 dark:text-zinc-400 leading-relaxed">
                        Information is processed solely to generate diagnostic reports, facilitate billing, and provide technical support. We never sell medical data to third-party aggregators.
                    </div>
                </div>

                <!-- 4. Encryption Architecture -->
                <div class="bg-zinc-900 rounded-[2.5rem] p-12 text-white relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-emerald-500/10 blur-3xl"></div>
                    <h3 class="text-2xl font-bold mb-6 flex items-center gap-4 italic"><i class="feather-lock text-emerald-400"></i> Zero-Knowledge Encryption</h3>
                    <p class="text-zinc-400 leading-relaxed mb-6">Patient reports are encrypted at rest using AES-256 standards. Our database administrators cannot view sensitive clinical results without explicit cryptographic keys managed by your lab.</p>
                    <div class="flex gap-4 opacity-50"><i class="feather-shield"></i><i class="feather-command"></i><i class="feather-cpu"></i></div>
                </div>

                <!-- 5-10. Detailed Sub-Sections -->
                <div class="grid md:grid-cols-2 gap-12 pt-10 border-t border-zinc-50 dark:border-zinc-900">
                    <!-- 5 -->
                    <div>
                        <h4 class="font-bold text-zinc-900 dark:text-white mb-4">Cookies & Tracking</h4>
                        <p class="text-sm text-zinc-500 italic leading-relaxed">We use essential cookies for session management. Analytics are anonymized and focused on platform performance.</p>
                    </div>
                    <!-- 6 -->
                    <div>
                        <h4 class="font-bold text-zinc-900 dark:text-white mb-4">Third-Party Disclosure</h4>
                        <p class="text-sm text-zinc-500 italic leading-relaxed">Data is only shared with authorized sub-processors (AWS, Azure) for hosting purposes, or when legally mandated by medical regulatory bodies.</p>
                    </div>
                    <!-- 7 -->
                    <div>
                        <h4 class="font-bold text-zinc-900 dark:text-white mb-4">Data Retention</h4>
                        <p class="text-sm text-zinc-500 italic leading-relaxed">We retain records as long as your subscription is active, or as required by local medical record retention laws (typically 7-10 years).</p>
                    </div>
                    <!-- 8 -->
                    <div>
                        <h4 class="font-bold text-zinc-900 dark:text-white mb-4">International Transfers</h4>
                        <p class="text-sm text-zinc-500 italic leading-relaxed">Cross-border transfers are conducted under Standard Contractual Clauses (SCCs) to ensure equivalent protection.</p>
                    </div>
                </div>

                <!-- 9-20 Global Commitment Block (Rich UI section) -->
                <div class="py-20 text-center glass rounded-[3rem] border-emerald-500/20 relative overflow-hidden">
                    <div class="absolute inset-0 bg-emerald-500/5 pulse"></div>
                    <div class="relative z-10 max-w-2xl mx-auto px-6">
                        <h3 class="text-3xl font-bold mb-8 italic">Your Patients' Data, <br><span class="text-emerald-600 underline">Protected</span> for life.</h3>
                        <p class="text-zinc-500 mb-10 leading-relaxed text-sm">We undergo semi-annual security audits by independent firms to ensure our infrastructure remains the safest in the diagnostic industry.</p>
                        <div class="flex justify-center flex-wrap gap-8 grayscale opacity-40">
                            <span class="text-[10px] font-bold uppercase tracking-widest">ISO 27001</span>
                            <span class="text-[10px] font-bold uppercase tracking-widest">SOC2 TYPE II</span>
                            <span class="text-[10px] font-bold uppercase tracking-widest">HIPAA READY</span>
                        </div>
                    </div>
                </div>

                <div class="text-center pt-20">
                    <p class="text-zinc-400 text-sm font-medium mb-10 italic">Data protection officer: privacy@swspathology.com</p>
                    <a href="/" class="inline-block px-12 py-5 bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 rounded-[2rem] font-bold shadow-xl shadow-zinc-900/30 hover:bg-zinc-800 transition-all transition-transform hover:-translate-y-1">Return to Main Cloud</a>
                </div>

            </div>
        </div>
    </section>

</x-landing-layout>
