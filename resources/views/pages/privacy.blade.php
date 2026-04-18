<x-landing-layout>
    <x-slot name="title">Privacy Policy - {{ \App\Models\SiteSetting::get('site_name', 'SWS Pathology') }}</x-slot>

    <!-- Hero -->
    <section class="pt-32 pb-16 border-b border-zinc-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center reveal">
            <span class="inline-block px-4 py-1.5 rounded-full bg-zinc-100 text-zinc-600 text-[11px] font-bold uppercase tracking-wider mb-6">Legal</span>
            <h1 class="font-display text-4xl md:text-6xl font-extrabold tracking-tight mb-4">Privacy <span class="gradient-text">Policy</span></h1>
            <p class="text-sm text-zinc-400 font-bold uppercase tracking-wider">Last Revised: April 2026</p>
        </div>
    </section>

    <!-- Content -->
    <section class="py-24">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="space-y-16">
                @foreach([
                    ['num' => '01', 'title' => 'Information We Collect', 'text' => 'We collect information you provide directly (name, email, lab details) and diagnostic data processed through the platform. We also collect usage analytics to improve our services.'],
                    ['num' => '02', 'title' => 'How We Use Your Data', 'text' => 'Your data is used solely for providing diagnostic platform services, improving user experience, and communicating service updates. We never sell your data to third parties.'],
                    ['num' => '03', 'title' => 'Data Security', 'text' => 'All patient data is encrypted using AES-256 at rest and TLS 1.3 in transit. We maintain HIPAA-compliant infrastructure with regular security audits.'],
                    ['num' => '04', 'title' => 'Data Retention', 'text' => 'Diagnostic data is retained for the duration of your subscription plus 30 days. Upon account termination, you can request a full data export before deletion.'],
                    ['num' => '05', 'title' => 'Your Rights', 'text' => 'You have the right to access, correct, export, or delete your personal data at any time. Contact our data protection officer for any privacy-related requests.'],
                    ['num' => '06', 'title' => 'Cookie Policy', 'text' => 'We use essential cookies for platform functionality and optional analytics cookies. You can manage your cookie preferences at any time through your browser settings.'],
                ] as $i => $section)
                    <div class="reveal delay-{{ ($i % 3) + 1 }}">
                        <h3 class="text-2xl font-bold mb-4 flex items-center gap-4 text-zinc-900">
                            <span class="gradient-text font-display text-sm font-bold">{{ $section['num'] }}</span>
                            {{ $section['title'] }}
                        </h3>
                        <p class="text-zinc-500 leading-relaxed">{{ $section['text'] }}</p>
                    </div>
                @endforeach

                <div class="reveal bg-blue-50 p-8 rounded-2xl border border-blue-200">
                    <h4 class="text-xl font-bold mb-4 flex items-center gap-2"><i class="feather-shield text-blue-500"></i> HIPAA & GDPR Compliance</h4>
                    <p class="text-sm text-zinc-600 leading-relaxed">SWS Pathology maintains full compliance with HIPAA regulations for health data protection and GDPR standards for users in the European Union. Our infrastructure is regularly audited by independent security firms.</p>
                </div>

                <div class="text-center pt-8 reveal">
                    <p class="text-zinc-400 text-sm mb-8">Privacy questions? Contact privacy@swspathology.com</p>
                    <a href="/" class="inline-block px-8 py-4 bg-gradient-to-r from-brand-500 to-brand-700 text-white rounded-2xl font-bold shadow-lg shadow-brand-500/25 hover:-translate-y-1 transition-all duration-300">Back to Home</a>
                </div>
            </div>
        </div>
    </section>
</x-landing-layout>
