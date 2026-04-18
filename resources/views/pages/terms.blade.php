<x-landing-layout>
    <x-slot name="title">Terms of Service - {{ \App\Models\SiteSetting::get('site_name', 'SWS Pathology') }}</x-slot>

    <!-- Hero -->
    <section class="pt-32 pb-16 border-b border-zinc-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center reveal">
            <span class="inline-block px-4 py-1.5 rounded-full bg-zinc-100 text-zinc-600 text-[11px] font-bold uppercase tracking-wider mb-6">Legal</span>
            <h1 class="font-display text-4xl md:text-6xl font-extrabold tracking-tight mb-4">Terms of <span class="gradient-text">Service</span></h1>
            <p class="text-sm text-zinc-400 font-bold uppercase tracking-wider">Last Revised: April 2026</p>
        </div>
    </section>

    <!-- Content -->
    <section class="py-24">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="space-y-16">
                @foreach([
                    ['num' => '01', 'title' => 'Acceptance of Agreement', 'text' => 'By accessing or using the SWS Pathology SaaS platform, you agree to be bound by these Terms. If you do not agree, please cease all activity on the platform immediately.'],
                    ['num' => '02', 'title' => 'Eligibility & Registration', 'text' => 'Services are available only to registered laboratory entities and medical professionals. You must provide accurate, current, and complete information during registration.'],
                    ['num' => '03', 'title' => 'Identity Protection', 'text' => 'You are responsible for maintaining the confidentiality of your credentials. SWS Pathology is not liable for unauthorized access resulting from user negligence.'],
                    ['num' => '04', 'title' => 'License to Use', 'text' => 'We grant you a limited, non-exclusive, non-transferable license to access the LIS dashboards for internal diagnostic operations only.'],
                    ['num' => '05', 'title' => 'Financial Commitments', 'text' => 'Subscription fees are billed in advance. All payments are non-refundable unless specified otherwise in your Plan Addendum.'],
                    ['num' => '06', 'title' => 'Data Ownership', 'text' => 'The laboratory retains 100% ownership of patient records. SWS acts as a data processor under your explicit instructions.'],
                ] as $i => $section)
                    <div class="reveal delay-{{ ($i % 3) + 1 }}">
                        <h3 class="text-2xl font-bold mb-4 flex items-center gap-4 text-zinc-900">
                            <span class="gradient-text font-display text-sm font-bold">{{ $section['num'] }}</span>
                            {{ $section['title'] }}
                        </h3>
                        <p class="text-zinc-500 leading-relaxed">{{ $section['text'] }}</p>
                    </div>
                @endforeach

                <div class="reveal grid md:grid-cols-2 gap-8 border-t border-zinc-100 pt-16">
                    @foreach([
                        ['title' => '07 HIPAA Compliance', 'text' => 'We maintain state-of-the-art encryption standards consistent with HIPAA and GDPR mandates.'],
                        ['title' => '08 Third-Party Tools', 'text' => 'Integration with external LIS or analyzers is subject to the respective manufacturer\'s API terms.'],
                        ['title' => '09 Intellectual Property', 'text' => 'The platform\'s logic, AI models, and design remain the sole property of SWS Pathology.'],
                        ['title' => '10 Service Availability', 'text' => 'We aim for 99.9% uptime but do not guarantee uninterrupted access during critical maintenance.'],
                    ] as $sub)
                        <div>
                            <h4 class="font-bold text-zinc-900 mb-3">{{ $sub['title'] }}</h4>
                            <p class="text-sm text-zinc-500 leading-relaxed">{{ $sub['text'] }}</p>
                        </div>
                    @endforeach
                </div>

                <div class="reveal bg-amber-50 p-8 rounded-2xl border border-amber-200">
                    <h4 class="text-xl font-bold mb-4 flex items-center gap-2"><i class="feather-alert-triangle text-amber-500"></i> Limitation of Liability</h4>
                    <p class="text-sm text-zinc-600 leading-relaxed">SWS Pathology shall not be liable for medical diagnostic errors or data loss resulting from incorrect manual entry or hardware failure at the subscriber's location.</p>
                </div>

                <div class="text-center pt-8 reveal">
                    <p class="text-zinc-400 text-sm mb-8">Questions? Contact legal@swspathology.com</p>
                    <a href="/" class="inline-block px-8 py-4 bg-gradient-to-r from-brand-500 to-brand-700 text-white rounded-2xl font-bold shadow-lg shadow-brand-500/25 hover:-translate-y-1 transition-all duration-300">Back to Home</a>
                </div>
            </div>
        </div>
    </section>
</x-landing-layout>
