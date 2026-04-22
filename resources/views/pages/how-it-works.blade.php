<x-landing-layout>
    <x-slot name="title">How It Works - {{ \App\Models\SiteSetting::get('site_name', 'SWS Pathology') }}</x-slot>

    <!-- Hero -->
    <section class="pt-32 pb-20 relative overflow-hidden">
        <div class="absolute top-0 left-1/2 w-[800px] h-[600px] bg-brand-500/10 blur-[200px] rounded-full -translate-x-1/2 z-0"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10 reveal">
            <span class="inline-block px-4 py-1.5 rounded-full bg-brand-50 text-brand-600 text-[11px] font-bold uppercase tracking-wider mb-6">Workflow</span>
            <h1 class="font-display text-5xl md:text-7xl font-extrabold tracking-tight mb-8">From Sample to <span class="gradient-text">Report in Minutes</span></h1>
            <p class="text-xl text-zinc-500 max-w-2xl mx-auto leading-relaxed">Our streamlined 4-step process eliminates manual bottlenecks and delivers results faster than ever.</p>
        </div>
    </section>

    <!-- Steps -->
    <section class="py-24">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            @foreach([
                ['step' => '01', 'title' => 'Patient Registration & Billing', 'desc' => 'Register patients in seconds with our Smart POS. Auto-generate barcoded samples, apply test packages and membership discounts, and create instant invoices with multiple payment options.', 'icon' => 'feather-user-plus', 'color' => 'brand', 'features' => ['Barcode generation', 'Multi-payment options', 'Membership pricing', 'Test packages']],
                ['step' => '02', 'title' => 'Sample Processing & Entry', 'desc' => 'Track samples through your lab workflow. Enter results manually or sync directly from analyzers. Smart reference range flagging alerts pathologists to abnormal values instantly.', 'icon' => 'feather-cpu', 'color' => 'indigo', 'features' => ['Sample tracking', 'Analyzer integration', 'Auto reference ranges', 'Quality checks']],
                ['step' => '03', 'title' => 'Verification & Approval', 'desc' => 'Pathologists review flagged results, add clinical notes, and digitally sign reports with a single click. Multi-level approval workflows ensure quality compliance.', 'icon' => 'feather-check-circle', 'color' => 'emerald', 'features' => ['Digital signatures', 'Multi-level approval', 'Clinical annotations', 'QR verification']],
                ['step' => '04', 'title' => 'Report Delivery & Follow-up', 'desc' => 'Approved reports are instantly delivered via SMS, WhatsApp, email, or the patient portal. Patients can download verified PDFs with QR authentication.', 'icon' => 'feather-send', 'color' => 'amber', 'features' => ['WhatsApp delivery', 'SMS notifications', 'Patient portal', 'PDF download']],
            ] as $i => $step)
                <div class="reveal grid md:grid-cols-2 gap-12 items-center mb-20 {{ $i % 2 !== 0 ? 'md:grid-flow-dense' : '' }}">
                    <div class="{{ $i % 2 !== 0 ? 'md:col-start-2' : '' }}">
                        <div class="flex items-center gap-4 mb-6">
                            <span class="w-12 h-12 bg-{{ $step['color'] }}-50 rounded-2xl flex items-center justify-center text-{{ $step['color'] }}-600 font-display font-bold text-lg">{{ $step['step'] }}</span>
                            <div class="h-px flex-1 bg-{{ $step['color'] }}-100"></div>
                        </div>
                        <h3 class="font-display text-3xl font-bold mb-4 tracking-tight">{{ $step['title'] }}</h3>
                        <p class="text-zinc-500 leading-relaxed mb-6">{{ $step['desc'] }}</p>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach($step['features'] as $feat)
                                <div class="flex items-center gap-2 text-sm text-zinc-600">
                                    <i class="feather-check text-{{ $step['color'] }}-500 text-xs"></i>
                                    <span>{{ $feat }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="{{ $i % 2 !== 0 ? 'md:col-start-1' : '' }}">
                        <div class="bg-{{ $step['color'] }}-50/50 rounded-3xl p-8 flex items-center justify-center min-h-[280px] border border-{{ $step['color'] }}-100">
                            <div class="text-center">
                                <div class="w-20 h-20 bg-{{ $step['color'] }}-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                    <i class="{{ $step['icon'] }} text-{{ $step['color'] }}-600 text-3xl"></i>
                                </div>
                                <p class="text-sm font-bold text-{{ $step['color'] }}-600">Step {{ $step['step'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- CTA -->
    <section class="py-28 text-center">
        <div class="max-w-3xl mx-auto px-4 reveal">
            <h2 class="font-display text-4xl font-bold mb-8 tracking-tight">Ready to <span class="gradient-text">Get Started?</span></h2>
            <p class="text-zinc-500 mb-10 text-lg">See how simple it is to transform your lab operations.</p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="#contact" class="px-8 py-4 bg-gradient-to-r from-brand-500 to-brand-700 text-white rounded-2xl font-bold shadow-xl shadow-brand-500/25 hover:-translate-y-1 transition-all duration-300">Contact Us</a>
                <a href="{{ route('enquiry') }}" class="px-8 py-4 bg-white text-zinc-700 rounded-2xl font-bold border border-zinc-200 hover:border-brand-300 hover:text-brand-600 transition-all duration-300">Request Demo</a>
            </div>
        </div>
    </section>
</x-landing-layout>
