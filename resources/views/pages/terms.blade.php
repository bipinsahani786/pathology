<x-landing-layout>
    <x-slot name="title">Terms of Service - SWS Pathology SaaS</x-slot>

    <!-- 1. Hero -->
    <section class="pt-32 pb-20 bg-zinc-50 dark:bg-zinc-950 border-b border-zinc-100 dark:border-zinc-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center animate-fade-in-up">
            <div class="inline-block px-4 py-1.5 rounded-full bg-zinc-200 dark:bg-zinc-800 text-[10px] font-bold uppercase tracking-widest mb-6">Legal Framework</div>
            <h1 class="text-4xl md:text-6xl font-bold tracking-tight mb-6 italic">Terms of <span class="text-brand-600">Service</span></h1>
            <p class="text-sm text-zinc-400 font-bold uppercase tracking-tighter">Last Revised: April 2026</p>
        </div>
    </section>

    <!-- 2-20. Dense Legal Content (Modular approach) -->
    <section class="py-24 bg-white dark:bg-zinc-950">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="space-y-20">
                
                <!-- 2. Acceptance -->
                <div class="group">
                    <h3 class="text-2xl font-bold mb-6 flex items-center gap-4 text-zinc-900 dark:text-white"><span class="text-brand-600 font-display">01</span> Acceptance of Agreement</h3>
                    <div class="prose prose-zinc dark:prose-invert max-w-none text-zinc-600 dark:text-zinc-400 leading-relaxed">
                        By accessing or using the SWS Pathology SaaS platform, you agree to be bound by these Terms. If you do not agree, please cease all activity on the platform immediately.
                    </div>
                </div>

                <!-- 3. Eligibility -->
                <div class="group">
                    <h3 class="text-2xl font-bold mb-6 flex items-center gap-4 text-zinc-900 dark:text-white"><span class="text-brand-600 font-display">02</span> Eligibility & Registration</h3>
                    <div class="prose prose-zinc dark:prose-invert max-w-none text-zinc-600 dark:text-zinc-400 leading-relaxed">
                        Services are available only to registered laboratory entities and medical professionals. You must provide accurate, current, and complete information during registration.
                    </div>
                </div>

                <!-- 4. Account Security -->
                <div class="group">
                    <h3 class="text-2xl font-bold mb-6 flex items-center gap-4 text-zinc-900 dark:text-white"><span class="text-brand-600 font-display">03</span> Identity Protection</h3>
                    <div class="prose prose-zinc dark:prose-invert max-w-none text-zinc-600 dark:text-zinc-400 leading-relaxed">
                        You are responsible for maintaining the confidentiality of your credentials. SWS Pathology is not liable for unauthorized access resulting from user negligence.
                    </div>
                </div>

                <!-- 5. Scope of Service -->
                <div class="group">
                    <h3 class="text-2xl font-bold mb-6 flex items-center gap-4 text-zinc-900 dark:text-white"><span class="text-brand-600 font-display">04</span> License to Use</h3>
                    <div class="prose prose-zinc dark:prose-invert max-w-none text-zinc-600 dark:text-zinc-400 leading-relaxed">
                        We grant you a limited, non-exclusive, non-transferable license to access the LIS dashboards for internal diagnostic operations only.
                    </div>
                </div>

                <!-- 6. Billing & Payments -->
                <div class="group">
                    <h3 class="text-2xl font-bold mb-6 flex items-center gap-4 text-zinc-900 dark:text-white"><span class="text-brand-600 font-display">05</span> Financial Commitments</h3>
                    <div class="prose prose-zinc dark:prose-invert max-w-none text-zinc-600 dark:text-zinc-400 leading-relaxed">
                        Subscription fees are billed in advance. All payments are non-refundable unless specified otherwise in your Plan Addendum.
                    </div>
                </div>

                <!-- 7. Data Sovereignty -->
                <div class="group">
                    <h3 class="text-2xl font-bold mb-6 flex items-center gap-4 text-zinc-900 dark:text-white"><span class="text-brand-600 font-display">06</span> Data Ownership</h3>
                    <div class="prose prose-zinc dark:prose-invert max-w-none text-zinc-600 dark:text-zinc-400 leading-relaxed">
                        The laboratory retains 100% ownership of patient records. SWS acts as a data processor under your explicit instructions.
                    </div>
                </div>

                <!-- 8-15 Other Legal Clauses (Condensed for UI brevity but distinct sections) -->
                <div class="grid md:grid-cols-2 gap-12 border-t border-zinc-100 dark:border-zinc-800 pt-20">
                    <div>
                        <h4 class="font-bold text-zinc-900 dark:text-white mb-4 italic">07 HIPAA Compliance</h4>
                        <p class="text-sm text-zinc-500">We maintain state-of-the-art encryption standards consistent with HIPAA and GDPR mandates.</p>
                    </div>
                    <div>
                        <h4 class="font-bold text-zinc-900 dark:text-white mb-4 italic">08 Third-Party Tools</h4>
                        <p class="text-sm text-zinc-500">Integration with external LIS or analyzers is subject to the respective manufacturer's API terms.</p>
                    </div>
                    <div>
                        <h4 class="font-bold text-zinc-900 dark:text-white mb-4 italic">09 Intellectual Property</h4>
                        <p class="text-sm text-zinc-500">The platform's logic, AI models, and design remain the sole property of SWS Pathology.</p>
                    </div>
                    <div>
                        <h4 class="font-bold text-zinc-900 dark:text-white mb-4 italic">10 Service Availability</h4>
                        <p class="text-sm text-zinc-500">We aim for 99.9% uptime but do not guarantee uninterrupted access during critical maintenance.</p>
                    </div>
                </div>

                <!-- 16-20 Closing sections -->
                <div class="bg-zinc-50 dark:bg-zinc-900/50 p-12 rounded-3xl border border-zinc-200 dark:border-zinc-800">
                    <h4 class="text-xl font-bold mb-6 flex items-center gap-2"><i class="feather-alert-triangle text-amber-500"></i> Limitation of Liability</h4>
                    <p class="text-sm text-zinc-500 leading-relaxed">SWS Pathology shall not be liable for medical diagnostic errors or data loss resulting from incorrect manual entry or hardware failure at the subscriber's location.</p>
                </div>

                <div class="text-center pt-20">
                    <p class="text-zinc-400 text-sm font-medium mb-10 italic">Questions regarding these terms? contact legal@swspathology.com</p>
                    <a href="{{ route('contact') }}" class="inline-block px-12 py-5 bg-brand-600 text-white rounded-[2rem] font-bold shadow-xl shadow-brand-600/30 hover:bg-brand-700 transition-all">I Understand. Take me Home.</a>
                </div>

            </div>
        </div>
    </section>

</x-landing-layout>
