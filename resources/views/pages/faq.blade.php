@php
    $faqs = \App\Models\LandingFaq::active()->get();
    $categories = $faqs->pluck('category')->unique()->filter();
@endphp

<x-landing-layout>
    <x-slot name="title">FAQ - {{ \App\Models\SiteSetting::get('site_name', 'SWS Pathology') }}</x-slot>

    <div class="bg-white text-zinc-700 selection:bg-brand-500/10 selection:text-brand-700 font-sans overflow-hidden">

        <section class="pt-32 pb-20 relative overflow-hidden bg-zinc-50 border-b border-zinc-100">
            <div class="absolute inset-0 z-0 pointer-events-none">
                <div
                    class="absolute top-0 right-1/4 w-[600px] h-[600px] bg-brand-400/10 blur-[150px] rounded-full translate-x-1/2 -translate-y-1/4">
                </div>
                <div
                    class="absolute bottom-0 left-1/4 w-[500px] h-[500px] bg-indigo-400/5 blur-[150px] rounded-full -translate-x-1/2 translate-y-1/4">
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10 reveal">
                <span
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-zinc-200 shadow-sm text-brand-600 text-xs font-bold uppercase tracking-widest mb-8">
                    <i class="feather-life-buoy text-sm"></i> Help Center
                </span>

                <h1
                    class="font-display text-5xl md:text-6xl lg:text-7xl font-extrabold tracking-tight mb-6 text-zinc-900 leading-[1.1]">
                    Frequently Asked <br class="hidden md:block" />
                    <span
                        class="text-transparent bg-clip-text bg-gradient-to-r from-brand-600 to-indigo-600">Questions</span>
                </h1>

                <p class="text-xl text-zinc-600 max-w-2xl mx-auto leading-relaxed font-medium mb-10">
                    Everything you need to know about our platform, billing, and technical integrations. Can't find an
                    answer? We're here to help.
                </p>
            </div>
        </section>

        <section class="py-24 bg-white relative z-10">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

                @if($categories->count() > 1)
                    <div class="flex flex-wrap items-center justify-center gap-2 mb-12 reveal">
                        <span
                            class="px-5 py-2 rounded-full bg-zinc-900 text-white text-sm font-semibold shadow-md cursor-default">All</span>
                        @foreach($categories as $category)
                            <span
                                class="px-5 py-2 rounded-full bg-zinc-50 border border-zinc-200 text-zinc-600 hover:bg-zinc-100 text-sm font-medium transition-colors cursor-pointer">{{ $category }}</span>
                        @endforeach
                    </div>
                @endif

                <div class="space-y-3" x-data="{ active: null }">
                    @foreach($faqs as $i => $faq)
                        <div class="reveal delay-{{ ($i % 6) + 1 }} bg-white rounded-2xl border border-zinc-200 overflow-hidden transition-all duration-300 hover:border-brand-300 hover:shadow-md"
                            :class="active === {{ $faq->id }} ? 'ring-1 ring-brand-500/20 shadow-md border-brand-300' : ''">
                            <button @click="active = active === {{ $faq->id }} ? null : {{ $faq->id }}"
                                class="w-full flex items-center justify-between p-6 text-left focus:outline-none group">

                                <h6
                                    class="font-bold text-zinc-900 pr-4 text-lg group-hover:text-brand-600 transition-colors duration-300">
                                    {{ $faq->question }}
                                </h6>

                                <div class="w-10 h-10 rounded-full bg-zinc-50 border border-zinc-100 flex items-center justify-center shrink-0 transition-all duration-300 group-hover:bg-brand-50 group-hover:border-brand-100"
                                    :class="active === {{ $faq->id }} ? 'bg-brand-50 border-brand-100' : ''">
                                    <i class="feather-chevron-down text-zinc-400 text-sm transition-transform duration-300 group-hover:text-brand-600"
                                        :class="active === {{ $faq->id }} ? 'rotate-180 text-brand-600' : ''"></i>
                                </div>
                            </button>

                            <div x-show="active === {{ $faq->id }}" x-collapse>
                                <div class="px-6 pb-6 pt-2">
                                    <div class="h-px w-full bg-gradient-to-r from-zinc-100 via-zinc-200 to-zinc-100 mb-6">
                                    </div>
                                    <div class="text-base text-zinc-600 leading-relaxed font-medium">
                                        {{ $faq->answer }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="py-24 bg-zinc-50/50 border-t border-zinc-100">
            <div class="max-w-4xl mx-auto px-4 text-center reveal">
                <div
                    class="relative bg-white rounded-[2.5rem] p-12 border border-zinc-200 shadow-xl overflow-hidden group hover:border-brand-200 transition-colors duration-500">

                    <div class="absolute inset-0 bg-gradient-to-b from-transparent to-zinc-50/80 pointer-events-none">
                    </div>
                    <div
                        class="absolute -top-24 -right-24 w-64 h-64 bg-brand-500/5 blur-[80px] rounded-full group-hover:bg-brand-500/10 transition-colors duration-500">
                    </div>

                    <div class="relative z-10">
                        <div
                            class="w-20 h-20 bg-brand-50 border border-brand-100 rounded-3xl flex items-center justify-center mx-auto mb-8 shadow-inner">
                            <div class="relative">
                                <span class="absolute -right-1 -top-1 flex h-3 w-3">
                                    <span
                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span
                                        class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500 border-2 border-white"></span>
                                </span>
                                <i class="feather-message-circle text-brand-600 text-3xl"></i>
                            </div>
                        </div>

                        <h3 class="font-display text-3xl md:text-4xl font-extrabold text-zinc-900 mb-4 tracking-tight">
                            Still Have Questions?</h3>
                        <p class="text-zinc-600 mb-10 text-lg font-medium max-w-lg mx-auto">Our specialized support team
                            is always available to help you set up and optimize your laboratory operations.</p>

                        <div class="flex flex-col sm:flex-row justify-center gap-4">
                            <a href="{{ route('contact') }}"
                                class="inline-flex justify-center items-center px-8 py-4 bg-zinc-900 hover:bg-brand-600 text-white rounded-full font-bold shadow-lg shadow-zinc-900/10 hover:shadow-brand-500/25 transition-all duration-300 transform hover:-translate-y-0.5">
                                Contact Support
                            </a>
                            <a href="mailto:{{ \App\Models\SiteSetting::get('contact_email', 'support@swspathology.com') }}"
                                class="inline-flex justify-center items-center px-8 py-4 bg-white text-zinc-700 rounded-full font-bold border border-zinc-200 hover:bg-zinc-50 hover:text-brand-600 transition-all duration-300">
                                Email Us directly
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>
</x-landing-layout>