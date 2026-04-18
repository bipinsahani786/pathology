@php
    $plans = \App\Models\Plan::landing()->get();
    $faqs = \App\Models\LandingFaq::active()->where('category', 'pricing')->get();
@endphp

<x-landing-layout>
    <x-slot name="title">Pricing - {{ \App\Models\SiteSetting::get('site_name', 'SWS Pathology') }}</x-slot>

    <!-- Hero -->
    <section class="pt-32 pb-20 relative overflow-hidden">
        <div class="absolute top-0 left-1/4 w-[600px] h-[600px] bg-brand-500/10 blur-[200px] rounded-full z-0"></div>
        <div class="absolute bottom-0 right-1/4 w-[500px] h-[500px] bg-indigo-500/10 blur-[150px] rounded-full z-0">
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10 reveal">
            <span
                class="inline-block px-4 py-1.5 rounded-full bg-brand-50 text-brand-600 text-[11px] font-bold uppercase tracking-wider mb-6">Scale
                with Confidence</span>
            <h1 class="font-display text-5xl md:text-7xl font-extrabold tracking-tight mb-8">Choose Your <span
                    class="gradient-text">Growth Plan</span></h1>
            <p class="text-xl text-zinc-500 max-w-2xl mx-auto leading-relaxed">Predictable pricing for every stage of
                your laboratory's growth. No hidden fees.</p>
        </div>
    </section>

    <!-- Plan Cards -->
    @if($plans->count())
        <section class="pb-28">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-{{ min($plans->count(), 3) }} gap-8 max-w-5xl mx-auto items-stretch">
                    @foreach($plans as $i => $plan)
                        @php $isPopular = $plan->landing_badge !== null; @endphp
                        <div class="reveal delay-{{ $i + 1 }} relative {{ $isPopular ? 'lg:scale-105 z-10' : '' }}">
                            @if($isPopular)
                                <div
                                    class="absolute -top-4 left-1/2 -translate-x-1/2 bg-gradient-to-r from-brand-500 to-brand-700 text-[10px] font-bold uppercase tracking-wider px-5 py-1.5 rounded-full text-white shadow-lg shadow-brand-500/30 z-20">
                                    {{ $plan->landing_badge }}</div>
                            @endif
                            <div
                                class="{{ $isPopular ? 'bg-zinc-950 text-white ring-1 ring-white/10 shadow-2xl shadow-brand-500/20' : 'bg-white border border-zinc-200 hover:border-brand-200' }} rounded-3xl p-10 flex flex-col h-full hover-lift">
                                <div class="mb-8">
                                    <h5
                                        class="text-sm font-bold {{ $isPopular ? 'text-brand-400' : 'text-zinc-400' }} uppercase tracking-widest mb-4">
                                        {{ $plan->name }}</h5>
                                    <div
                                        class="text-5xl font-bold font-display {{ $isPopular ? 'text-white' : 'text-zinc-900' }}">
                                        @if($plan->price == 0) Free <span
                                            class="text-sm font-normal {{ $isPopular ? 'text-zinc-500' : 'text-zinc-400' }}">forever</span>
                                        @else ₹{{ number_format($plan->price) }}<span
                                            class="text-sm font-normal {{ $isPopular ? 'text-zinc-500' : 'text-zinc-400' }}">/mo</span>
                                        @endif
                                    </div>
                                    @if($plan->landing_subtitle)
                                        <p class="text-sm {{ $isPopular ? 'text-zinc-400' : 'text-zinc-500' }} mt-2">
                                            {{ $plan->landing_subtitle }}</p>
                                    @endif
                                </div>

                                @if($plan->landing_features)
                                    <ul class="space-y-4 mb-10 flex-1">
                                        @foreach($plan->landing_features as $feat)
                                            <li
                                                class="flex items-center gap-3 text-sm {{ $isPopular ? 'text-zinc-300' : 'text-zinc-600' }}">
                                                <div
                                                    class="w-5 h-5 rounded-full {{ $isPopular ? 'bg-brand-500/20 text-brand-400' : 'bg-emerald-50 text-emerald-500' }} flex items-center justify-center shrink-0">
                                                    <i class="feather-check text-[10px]"></i>
                                                </div>
                                                {{ $feat }}
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif

                                <a href="{{ route('register.lab') }}"
                                    class="block w-full py-4 text-center rounded-2xl font-bold text-sm {{ $isPopular ? 'bg-gradient-to-r from-brand-500 to-brand-700 text-white shadow-lg shadow-brand-500/30 hover:shadow-brand-500/50' : 'bg-zinc-900 text-white hover:bg-zinc-800' }} transition-all duration-300 hover:-translate-y-0.5">
                                    {{ $plan->landing_cta_text ?? 'Get Started' }}
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- Pricing FAQ -->
    @if($faqs->count())
        <section class="py-24 bg-zinc-50/50">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16 reveal">
                    <h3 class="font-display text-3xl font-bold text-zinc-900">Pricing <span
                            class="gradient-text">Questions</span></h3>
                </div>
                <div class="space-y-4" x-data="{ active: null }">
                    @foreach($faqs as $faq)
                        <div class="reveal bg-white rounded-2xl border border-zinc-100 overflow-hidden" x-data>
                            <button @click="active = active === {{ $faq->id }} ? null : {{ $faq->id }}"
                                class="w-full flex items-center justify-between p-6 text-left">
                                <h6 class="font-bold text-zinc-900 pr-4">{{ $faq->question }}</h6>
                                <i class="feather-chevron-down text-zinc-400 transition-transform duration-300 shrink-0"
                                    :class="active === {{ $faq->id }} ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="active === {{ $faq->id }}" x-collapse>
                                <div class="px-6 pb-6 text-sm text-zinc-500 leading-relaxed">{{ $faq->answer }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- CTA -->
    <section class="py-28 bg-zinc-950 text-center relative overflow-hidden">
        <div class="absolute inset-0 bg-grid opacity-5"></div>
        <div class="relative z-10 reveal">
            <h2 class="font-display text-5xl font-bold text-white mb-6 tracking-tight">Upgrade Your <span
                    class="text-brand-400">Diagnostic Intelligence</span></h2>
            <p class="text-zinc-400 max-w-2xl mx-auto mb-12 text-lg">Start your free trial today. No credit card
                required.</p>
            <a href="{{ route('register.lab') }}"
                class="inline-block px-14 py-5 bg-gradient-to-r from-brand-500 to-brand-700 text-white rounded-2xl font-bold text-lg shadow-2xl shadow-brand-500/30 hover:-translate-y-1 transition-all duration-300">
                Start Free Trial
            </a>
        </div>
    </section>
</x-landing-layout>