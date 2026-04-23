@php
    $heroTitle = \App\Models\SiteSetting::get('hero_title', 'Intelligence for Modern Laboratories');
    $heroSubtitle = \App\Models\SiteSetting::get('hero_subtitle', 'Streamline your diagnostic workflow from collection to automated reporting with our secure cloud ecosystem.');
    $heroCta = \App\Models\SiteSetting::get('hero_cta_text', 'Start Free Trial');
    $heroImage = \App\Models\SiteSetting::get('hero_image');
    $features = \App\Models\LandingFeature::active()->get();
    $testimonials = \App\Models\LandingTestimonial::active()->get();
    $faqs = \App\Models\LandingFaq::active()->take(5)->get();
    $plans = \App\Models\Plan::landing()->get();
@endphp

<x-landing-layout>
    <x-slot name="title">{{ \App\Models\SiteSetting::get('meta_title', 'SWS Pathology - Precision Diagnostics') }}</x-slot>

    <div class="bg-white text-zinc-700 selection:bg-brand-500/10 selection:text-brand-700 font-sans">

        <section class="relative min-h-[100svh] flex items-center pt-24 pb-12 overflow-hidden border-b border-zinc-100">
            <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
                <div class="absolute -top-1/4 -right-1/4 w-[800px] h-[800px] bg-brand-500/5 rounded-full blur-[120px]"></div>
                <div class="absolute bottom-0 left-1/4 w-[600px] h-[600px] bg-indigo-500/5 rounded-full blur-[120px]"></div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full">
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <div class="reveal">
                        <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-full bg-brand-50 border border-brand-100 shadow-sm mb-8">
                            <span class="relative flex h-2.5 w-2.5">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-brand-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-brand-500"></span>
                            </span>
                            <span class="text-xs font-bold text-brand-700 tracking-wide">Next-Gen LIS Platform 2.0</span>
                        </div>

                        <h1 class="font-display text-5xl md:text-6xl lg:text-7xl font-extrabold text-zinc-900 leading-[1.1] mb-6 tracking-tight">
                            {!! str_replace(['Modern', 'Laboratories'], ['<span class="bg-clip-text text-transparent bg-gradient-to-r from-brand-600 to-indigo-600">Modern</span>', '<span class="bg-clip-text text-transparent bg-gradient-to-r from-brand-600 to-indigo-600">Laboratories</span>'], e($heroTitle)) !!}
                        </h1>

                        <p class="text-lg text-zinc-600 leading-relaxed mb-10 max-w-xl">
                            {{ $heroSubtitle }}
                        </p>

                        <div class="flex flex-col sm:flex-row gap-4">
                            <a href="#contact" class="inline-flex justify-center items-center gap-2 px-8 py-4 bg-zinc-900 hover:bg-brand-600 text-white rounded-full font-semibold text-[15px] shadow-lg shadow-zinc-900/10 transition-all duration-300 transform hover:-translate-y-0.5">
                                {{ $heroCta }} <i class="feather-arrow-right"></i>
                            </a>
                            <a href="{{ route('portal.login') }}" class="inline-flex justify-center items-center gap-2 px-8 py-4 bg-white hover:bg-zinc-50 text-zinc-700 rounded-full font-semibold text-[15px] border border-zinc-200 transition-all duration-300 shadow-sm hover:shadow-md">
                                <i class="feather-download-cloud text-brand-600"></i> Download Report
                            </a>
                        </div>
                    </div>

                    <div class="reveal delay-2 hidden lg:block relative">
                        @if($heroImage && \Illuminate\Support\Facades\Storage::exists($heroImage))
                            <img src="{{ secure_storage_url($heroImage) }}" alt="Lab Background" class="absolute inset-0 w-full h-full object-cover opacity-10 mix-blend-multiply">
                        @endif
                        <div class="relative w-full aspect-[4/3] bg-zinc-100 rounded-[2rem] border border-zinc-200 shadow-2xl overflow-hidden p-2">
                             <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&q=80&w=1200" alt="SWS Pathology Dashboard Mockup" class="w-full h-full object-cover rounded-xl grayscale-[20%]">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-10 border-b border-zinc-100 bg-zinc-50">
            <div class="max-w-7xl mx-auto px-4 text-center">
                <p class="text-xs font-semibold text-zinc-500 uppercase tracking-widest mb-6">Powering 500+ Diagnostic Centers</p>
                <div class="flex flex-wrap justify-center gap-x-12 gap-y-6 opacity-60 grayscale">
                    @foreach(['METROLAB', 'QUANTUM DIAG', 'COREPATH', 'APEXVUE', 'LIFEBLOOM'] as $logo)
                        <span class="text-xl font-black font-display text-zinc-900 tracking-tight">{{ $logo }}</span>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="py-20 border-b border-zinc-100 relative overflow-hidden">
            <div class="max-w-7xl mx-auto px-4">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                    @foreach([
                        ['val' => '10M+', 'label' => 'Reports Generated'],
                        ['val' => '99.9%', 'label' => 'System Uptime'],
                        ['val' => '500+', 'label' => 'Active Labs'],
                        ['val' => '0', 'label' => 'Data Breaches'],
                    ] as $stat)
                        <div class="text-center reveal">
                            <div class="text-4xl md:text-5xl font-extrabold text-zinc-900 mb-2 font-display">{{ $stat['val'] }}</div>
                            <div class="text-sm text-zinc-500 uppercase tracking-wider">{{ $stat['label'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="py-24 border-b border-zinc-100">
            <div class="max-w-7xl mx-auto px-4">
                <div class="bg-zinc-50 rounded-[2.5rem] p-10 md:p-16 border border-zinc-100 relative overflow-hidden">
                    <div class="grid lg:grid-cols-2 gap-16 items-center relative z-10">
                        <div>
                            <h2 class="text-4xl md:text-5xl font-extrabold text-zinc-900 mb-6 font-display">Tired of <span class="text-zinc-400 line-through">Paper Friction?</span></h2>
                            <p class="text-lg text-zinc-600 mb-8">Legacy systems and manual entry lead to lost samples, delayed reports, and frustrated partners.</p>
                        </div>
                        <div class="space-y-4">
                            @foreach([
                                ['old' => 'Manual Data Entry', 'new' => 'Auto-synced Machine Results'],
                                ['old' => 'Delayed B2B Settlements', 'new' => 'Real-time Partner Payouts'],
                                ['old' => 'No Patient Tracking', 'new' => 'Automated WhatsApp Tracking'],
                            ] as $item)
                                <div class="flex items-center gap-4 bg-white p-4 rounded-2xl border border-zinc-100 shadow-sm">
                                    <div class="flex-1 text-sm text-zinc-500 line-through">{{ $item['old'] }}</div>
                                    <i class="feather-arrow-right text-zinc-400"></i>
                                    <div class="flex-1 text-sm text-emerald-600 font-semibold">{{ $item['new'] }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-24 border-b border-zinc-100" id="features">
            <div class="max-w-7xl mx-auto px-4">
                <div class="text-center max-w-3xl mx-auto mb-16 reveal">
                    <span class="text-xs font-bold text-brand-600 uppercase tracking-widest mb-3 block">Operating System</span>
                    <h2 class="font-display text-4xl font-extrabold text-zinc-900 tracking-tight mb-4">Everything your lab needs.</h2>
                    <p class="text-lg text-zinc-600">A comprehensive LIS platform to run your diagnostic business.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 auto-rows-fr">
                    @foreach($features->take(6) as $i => $feature)
                        @php $isLarge = ($i === 0 || $i === 3); @endphp
                        <div class="reveal delay-{{ $i + 1 }} group bg-white border border-zinc-100 p-8 rounded-3xl hover:border-brand-300 shadow-sm hover:shadow-xl hover:shadow-brand-500/5 transition-all duration-300 {{ $isLarge ? 'md:col-span-2' : '' }}">
                            <div class="w-12 h-12 bg-zinc-100 group-hover:bg-brand-50 rounded-xl flex items-center justify-center text-zinc-600 group-hover:text-brand-600 mb-6 transition-colors duration-300">
                                <i class="{{ $feature->icon }} text-xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-zinc-900 mb-3">{{ $feature->title }}</h3>
                            <p class="text-sm text-zinc-600 leading-relaxed mb-auto">{{ $feature->description }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        @php
            $deepDives = [
                ['title' => 'Smart Machine Interfacing', 'desc' => 'Connect Cell Counters, Biochemistry analyzers directly to the cloud. Zero manual typing. Results sync instantly.', 'img' => 'https://images.unsplash.com/photo-1579154204601-01588f351e67?auto=format&fit=crop&q=80&w=1200'],
                ['title' => 'Automated Smart Reporting', 'desc' => 'Generate beautiful, QR-coded PDF reports. Auto-highlight abnormal values based on patient age and gender.', 'img' => 'https://images.unsplash.com/photo-1584432810601-6c7f27d2362b?auto=format&fit=crop&q=80&w=1200'],
                ['title' => 'B2B Franchise Portal', 'desc' => 'Give collection centers a dedicated dashboard. Track wallet balances and sample statuses in real-time.', 'img' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&q=80&w=1200'],
                ['title' => 'Reagent & Inventory Tracking', 'desc' => 'Never run out of essential kits. Predictive alerts tell you exactly when to reorder.', 'img' => 'https://images.unsplash.com/photo-1583324113626-70df0f4deaab?auto=format&fit=crop&q=80&w=1200'],
            ];
        @endphp

        @foreach($deepDives as $index => $dive)
            <section class="py-20 border-b border-zinc-100 overflow-hidden">
                <div class="max-w-7xl mx-auto px-4">
                    <div class="grid lg:grid-cols-2 gap-16 items-center">
                        <div class="{{ $index % 2 == 1 ? 'lg:order-2' : '' }}">
                            <div class="text-brand-600 text-sm font-bold tracking-widest uppercase mb-2">Feature {{ $index + 1 }}</div>
                            <h3 class="text-3xl font-extrabold text-zinc-900 mb-4 font-display">{{ $dive['title'] }}</h3>
                            <p class="text-lg text-zinc-600 leading-relaxed">{{ $dive['desc'] }}</p>
                            <ul class="mt-6 space-y-3">
                                <li class="flex items-center gap-3 text-sm text-zinc-600 font-medium"><i class="feather-check-circle text-brand-500"></i> Cloud Synced</li>
                                <li class="flex items-center gap-3 text-sm text-zinc-600 font-medium"><i class="feather-check-circle text-brand-500"></i> Real-time Updates</li>
                            </ul>
                        </div>
                        <div class="relative {{ $index % 2 == 1 ? 'lg:order-1' : '' }}">
                            <div class="aspect-video bg-zinc-100 rounded-2xl border border-zinc-200 shadow-2xl p-2">
                                <img src="{{ $dive['img'] }}" alt="{{ $dive['title'] }} Mockup" class="w-full h-full object-cover rounded-xl">
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endforeach

        <section class="py-24 border-b border-zinc-100 bg-zinc-50">
            <div class="max-w-7xl mx-auto px-4 text-center">
                <h2 class="font-display text-4xl font-extrabold text-zinc-900 mb-16">Four Steps to Automation</h2>
                <div class="grid md:grid-cols-4 gap-8">
                    @foreach(['Register Patient', 'Process Sample', 'Verify Results', 'Auto-Deliver WhatsApp'] as $i => $step)
                        <div class="relative z-10 bg-white p-8 rounded-2xl border border-zinc-100 text-center shadow-sm">
                            <div class="w-12 h-12 mx-auto bg-brand-50 text-brand-600 rounded-full flex items-center justify-center font-bold text-xl mb-4 border border-brand-100">{{ $i + 1 }}</div>
                            <h5 class="font-bold text-zinc-900">{{ $step }}</h5>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="py-20 border-b border-zinc-100">
            <div class="max-w-7xl mx-auto px-4 text-center">
                <h2 class="text-2xl font-bold text-zinc-900 mb-8 font-display">Seamlessly Connects With</h2>
                <div class="flex flex-wrap justify-center gap-4">
                    @foreach(['WhatsApp API', 'Razorpay', 'Stripe', 'Sysmex Analyzers', 'Erba Analyzers', 'AWS Cloud'] as $tech)
                        <div class="px-6 py-3 bg-white rounded-full border border-zinc-200 text-sm font-semibold text-zinc-600 shadow-sm">{{ $tech }}</div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="py-20 border-b border-zinc-100 bg-zinc-50">
            <div class="max-w-7xl mx-auto px-4">
                <div class="grid md:grid-cols-3 gap-8 text-center">
                    <div class="p-6 bg-white rounded-2xl border border-zinc-100 shadow-sm">
                        <i class="feather-lock text-3xl text-brand-600 mb-4 block"></i>
                        <h4 class="text-lg font-bold text-zinc-900 mb-2 font-display">AES-256 Encryption</h4>
                        <p class="text-sm text-zinc-600">Bank-grade security for patient data.</p>
                    </div>
                    <div class="p-6 bg-white rounded-2xl border border-zinc-100 shadow-sm">
                        <i class="feather-shield text-3xl text-brand-600 mb-4 block"></i>
                        <h4 class="text-lg font-bold text-zinc-900 mb-2 font-display">HIPAA Ready</h4>
                        <p class="text-sm text-zinc-600">Built for healthcare privacy standards.</p>
                    </div>
                    <div class="p-6 bg-white rounded-2xl border border-zinc-100 shadow-sm">
                        <i class="feather-server text-3xl text-brand-600 mb-4 block"></i>
                        <h4 class="text-lg font-bold text-zinc-900 mb-2 font-display">Automated Backups</h4>
                        <p class="text-sm text-zinc-600">Hourly cloud backups ensure zero data loss.</p>
                    </div>
                </div>
            </div>
        </section>

        @if($testimonials->count())
        <section class="py-24 border-b border-zinc-100">
            <div class="max-w-7xl mx-auto px-4">
                <h2 class="text-center font-display text-4xl font-extrabold text-zinc-900 mb-16">Trusted by Professionals</h2>
                <div class="grid md:grid-cols-3 gap-6">
                    @foreach($testimonials->take(3) as $testimonial)
                        <div class="bg-white p-8 rounded-3xl border border-zinc-100 shadow-sm flex flex-col hover:border-brand-200 transition-colors">
                            <p class="text-zinc-600 italic mb-6 leading-relaxed flex-1">"{{ $testimonial->quote }}"</p>
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-brand-50 text-brand-700 flex items-center justify-center font-bold border border-brand-100">
                                    {{ substr($testimonial->author_name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-bold text-zinc-900 text-sm">{{ $testimonial->author_name }}</p>
                                    <p class="text-xs text-zinc-500">{{ $testimonial->author_role }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        @if($plans->count())
        <section class="py-24 border-b border-zinc-100 bg-zinc-50" id="pricing">
            <div class="max-w-7xl mx-auto px-4">
                <div class="text-center mb-16 max-w-2xl mx-auto">
                    <h2 class="font-display text-4xl font-extrabold text-zinc-900 mb-4">Transparent Pricing</h2>
                    <p class="text-lg text-zinc-600">Simple plans designed to scale with your laboratory's growth.</p>
                </div>
                <div class="grid lg:grid-cols-3 gap-8 max-w-5xl mx-auto items-stretch">
                    @foreach($plans as $plan)
                        @php $isPopular = $plan->landing_badge !== null; @endphp
                        <div class="bg-white border {{ $isPopular ? 'border-brand-500 shadow-2xl shadow-brand-500/10' : 'border-zinc-200 shadow-sm' }} rounded-3xl p-8 flex flex-col relative overflow-hidden transition-all hover:-translate-y-1">
                            @if($isPopular)
                                <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-brand-500 to-indigo-500"></div>
                                <div class="absolute top-6 right-6 bg-brand-50 text-brand-700 text-xs font-bold px-3 py-1 rounded-full border border-brand-100">
                                    {{ $plan->landing_badge }}
                                </div>
                            @endif

                            <div class="mb-8">
                                <h5 class="text-sm font-bold uppercase tracking-widest mb-4 {{ $isPopular ? 'text-brand-700' : 'text-zinc-500' }}">{{ $plan->name }}</h5>
                                <div class="flex items-baseline gap-1">
                                    <span class="text-5xl font-display font-extrabold text-zinc-900">
                                        @if($plan->price == 0) Free @else ₹{{ number_format($plan->price) }} @endif
                                    </span>
                                    @if($plan->price > 0)<span class="text-sm font-medium text-zinc-500">/month</span>@endif
                                </div>
                            </div>

                            @if($plan->landing_features)
                                <ul class="space-y-4 mb-8 flex-1">
                                    @foreach($plan->landing_features as $feat)
                                        <li class="flex items-start gap-3 text-sm text-zinc-700 font-medium">
                                            <div class="mt-0.5 w-5 h-5 rounded-full bg-brand-50 text-brand-600 flex items-center justify-center shrink-0 border border-brand-100">
                                                <i class="feather-check text-[10px]"></i>
                                            </div>
                                            {{ $feat }}
                                        </li>
                                    @endforeach
                                </ul>
                            @endif

                            <a href="#contact" class="block w-full py-4 text-center rounded-xl font-semibold text-sm transition-all duration-300 {{ $isPopular ? 'bg-zinc-900 text-white shadow-lg hover:bg-black' : 'bg-brand-50 text-brand-700 border border-brand-100 hover:bg-brand-100' }}">
                                {{ $plan->landing_cta_text ?? 'Contact Us' }}
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        @if($faqs->count())
        <section class="py-24 border-b border-zinc-100 bg-white">
            <div class="max-w-3xl mx-auto px-4">
                <h2 class="text-center font-display text-4xl font-extrabold text-zinc-900 mb-12">FAQs</h2>
                <div class="space-y-3" x-data="{ active: null }">
                    @foreach($faqs as $faq)
                        <div class="bg-white rounded-2xl border border-zinc-200 overflow-hidden transition-colors hover:border-brand-200">
                            <button @click="active = active === {{ $faq->id }} ? null : {{ $faq->id }}" class="w-full flex justify-between p-6 text-left focus:outline-none">
                                <span class="font-semibold text-zinc-900 pr-4">{{ $faq->question }}</span>
                                <div class="w-8 h-8 rounded-full bg-zinc-50 flex items-center justify-center shrink-0 transition-transform duration-300" :class="active === {{ $faq->id }} ? 'rotate-180 bg-brand-50 text-brand-600' : 'text-zinc-400'">
                                    <i class="feather-chevron-down text-sm"></i>
                                </div>
                            </button>
                            <div x-show="active === {{ $faq->id }}" x-collapse>
                                <div class="px-6 pb-6 text-sm text-zinc-600 leading-relaxed font-medium">{{ $faq->answer }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        <section class="py-24 bg-zinc-50 border-b border-zinc-100" id="contact">
            <div class="max-w-7xl mx-auto px-4">
                <div class="bg-white rounded-[2rem] border border-zinc-100 p-8 md:p-12 shadow-xl hover:border-brand-100 transition-colors">
                    <div class="grid lg:grid-cols-2 gap-16">
                        <div>
                            <h2 class="text-3xl font-extrabold text-zinc-900 mb-6 font-display">Let's talk about your lab.</h2>
                            <p class="text-zinc-600 mb-10 leading-relaxed">Whether you process 50 or 5000 samples a day, we have a solution tailored for you. Drop us a line and our experts will get back to you.</p>
                            
                            <div class="space-y-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-zinc-100 rounded-xl flex items-center justify-center text-brand-600 border border-zinc-200"><i class="feather-mail"></i></div>
                                    <div>
                                        <p class="text-sm font-bold text-zinc-900">Email Us</p>
                                        <p class="text-sm text-zinc-500">support@zytrixon.com</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-zinc-100 rounded-xl flex items-center justify-center text-brand-600 border border-zinc-200"><i class="feather-phone"></i></div>
                                    <div>
                                        <p class="text-sm font-bold text-zinc-900">Call Us</p>
                                        <p class="text-sm text-zinc-500">+91 98765 43210</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white p-8 rounded-2xl border border-zinc-100 shadow-inner">
                            <form action="{{ route('contact.submit') ?? '#' }}" method="POST" class="space-y-5">
                                @csrf
                                <div class="grid grid-cols-2 gap-5">
                                    <div>
                                        <label class="block text-xs font-bold text-zinc-500 uppercase tracking-wider mb-2">First Name</label>
                                        <input type="text" name="first_name" class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-4 py-3 text-zinc-900 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-colors" placeholder="Rahul">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-zinc-500 uppercase tracking-wider mb-2">Last Name</label>
                                        <input type="text" name="last_name" class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-4 py-3 text-zinc-900 focus:outline-none focus:border-brand-500 transition-colors" placeholder="Verma">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-zinc-500 uppercase tracking-wider mb-2">Lab Name</label>
                                    <input type="text" name="lab_name" class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-4 py-3 text-zinc-900 focus:outline-none focus:border-brand-500 transition-colors" placeholder="City Diagnostics">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-zinc-500 uppercase tracking-wider mb-2">Email</label>
                                    <input type="email" name="email" class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-4 py-3 text-zinc-900 focus:outline-none focus:border-brand-500 transition-colors" placeholder="rahul@example.com">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-zinc-500 uppercase tracking-wider mb-2">Message</label>
                                    <textarea name="message" rows="4" class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-4 py-3 text-zinc-900 focus:outline-none focus:border-brand-500 transition-colors resize-none" placeholder="Tell us about your requirements..."></textarea>
                                </div>
                                <button type="submit" class="w-full py-4 bg-zinc-900 hover:bg-black text-white rounded-xl font-bold shadow-lg shadow-zinc-900/10 transition-all">
                                    Send Message
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-32 relative overflow-hidden bg-brand-600">
            <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[1000px] h-[1000px] bg-white/10 blur-[120px] rounded-full pointer-events-none"></div>
            </div>
            <div class="max-w-4xl mx-auto px-4 relative z-10 text-center">
                <h2 class="font-display text-5xl md:text-6xl font-extrabold text-white mb-6 tracking-tight">Ready to Modernize?</h2>
                <p class="text-xl text-brand-100 mb-12 max-w-2xl mx-auto font-medium">Join top laboratories automating their workflow, reducing errors, and scaling revenue.</p>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="#contact" class="inline-flex justify-center items-center px-10 py-5 bg-white text-brand-600 rounded-full font-bold text-lg shadow-2xl transition-transform hover:-translate-y-1">
                        Get Started Today
                    </a>
                    <a href="#contact" class="inline-flex justify-center items-center px-10 py-5 bg-brand-700 hover:bg-brand-800 text-white rounded-full font-bold text-lg border border-brand-500 transition-all">
                        Request a Demo
                    </a>
                </div>
            </div>
        </section>

    </div>
</x-landing-layout>