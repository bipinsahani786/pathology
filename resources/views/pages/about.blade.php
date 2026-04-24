@php
    $aboutTitle = \App\Models\SiteSetting::get('about_title', 'Precision in Every Diagnostic Pulse');
    $aboutDesc = \App\Models\SiteSetting::get('about_description', 'SWS Pathology emerged from a collaboration between veteran pathologists and software engineers to bridge the gap between clinical accuracy and digital efficiency.');
    $aboutImage = \App\Models\SiteSetting::get('about_image');
    $statLabs = \App\Models\SiteSetting::get('about_stat_labs', '500+');
    $statLabsLabel = \App\Models\SiteSetting::get('about_stat_labs_label', 'Labs Integrated');
    $statUptime = \App\Models\SiteSetting::get('about_stat_uptime', '99.9%');
    $statUptimeLabel = \App\Models\SiteSetting::get('about_stat_uptime_label', 'Uptime SLA');
    $statReports = \App\Models\SiteSetting::get('about_stat_reports', '1M+');
    $statReportsLabel = \App\Models\SiteSetting::get('about_stat_reports_label', 'Reports Monthly');
@endphp

<x-landing-layout>
    <x-slot name="title">About Us - {{ \App\Models\SiteSetting::get('site_name', 'SWS Pathology') }}</x-slot>

    <div class="bg-white text-zinc-700 selection:bg-brand-500/10 selection:text-brand-700 font-sans overflow-hidden">

        <section class="pt-32 pb-20 relative overflow-hidden bg-zinc-50 border-b border-zinc-100">
            <div class="absolute inset-0 z-0 pointer-events-none">
                <div class="absolute top-0 left-1/2 w-[800px] h-[600px] bg-brand-400/10 blur-[150px] rounded-full -translate-x-1/2 -translate-y-1/2"></div>
                <div class="absolute bottom-0 right-0 w-[600px] h-[600px] bg-indigo-400/5 blur-[150px] rounded-full translate-y-1/4"></div>
            </div>
            
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center reveal">
                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-zinc-200 shadow-sm text-brand-600 text-xs font-bold uppercase tracking-widest mb-8">
                    <span class="w-2 h-2 rounded-full bg-brand-500 animate-pulse"></span> Our Story
                </span>
                
                <h1 class="font-display text-5xl md:text-6xl lg:text-7xl font-extrabold tracking-tight mb-8 text-zinc-900 leading-[1.1]">
                    {!! str_replace(['Precision', 'Diagnostic'], ['<span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-600 to-indigo-600">Precision</span>', '<span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-600 to-indigo-600">Diagnostic</span>'], e($aboutTitle)) !!}
                </h1>
                
                <p class="text-xl text-zinc-600 max-w-2xl mx-auto leading-relaxed font-medium">
                    We're building the nervous system for the next generation of diagnostic medicine, making healthcare faster, safer, and smarter.
                </p>
            </div>
        </section>

        <section class="py-24 bg-white relative">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-12 gap-16 items-center">
                
                <div class="lg:col-span-5 reveal-left">
                    <div class="relative group">
                        <div class="absolute -inset-4 bg-gradient-to-tr from-brand-100 to-indigo-50 blur-2xl rounded-[3rem] group-hover:scale-105 transition-transform duration-700 opacity-70"></div>
                        
                        <div class="relative rounded-[2.5rem] bg-zinc-100 aspect-[4/5] overflow-hidden border border-zinc-200 shadow-2xl">
                            @if($aboutImage && \Illuminate\Support\Facades\Storage::exists($aboutImage))
                                <img src="{{ secure_storage_url($aboutImage) }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                            @else
                                <img src="https://images.unsplash.com/photo-1579684385127-1ef15d508118?auto=format&fit=crop&q=80&w=1200" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" alt="Our Medical Team">
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-zinc-900/60 via-zinc-900/0 to-transparent"></div>
                            
                            <div class="absolute bottom-8 left-8 right-8 bg-white/90 backdrop-blur-md p-5 rounded-2xl border border-white shadow-lg">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-xl bg-brand-50 flex items-center justify-center text-brand-600">
                                        <i class="feather-award text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-zinc-900">Founded 2024</div>
                                        <div class="text-xs text-zinc-500 font-medium">Reimagining Pathology</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-7 reveal-right lg:pl-8">
                    <span class="text-xs font-bold text-brand-600 uppercase tracking-widest mb-4 block">Heritage</span>
                    <h3 class="font-display text-4xl md:text-5xl font-extrabold mb-8 tracking-tight text-zinc-900">
                        Built by <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-600 to-indigo-600">Experts</span>,<br>for Professionals.
                    </h3>
                    <p class="text-lg text-zinc-600 mb-12 leading-relaxed font-medium">
                        {{ $aboutDesc }}
                    </p>

                    <div class="grid grid-cols-3 gap-6 pt-8 border-t border-zinc-100">
                        @foreach([
                            ['value' => $statLabs, 'label' => $statLabsLabel],
                            ['value' => $statUptime, 'label' => $statUptimeLabel],
                            ['value' => $statReports, 'label' => $statReportsLabel],
                        ] as $stat)
                            <div>
                                <p class="text-4xl font-extrabold text-zinc-900 font-display mb-2">{{ $stat['value'] }}</p>
                                <p class="text-xs font-bold text-zinc-500 uppercase tracking-wider">{{ $stat['label'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <section class="py-24 bg-zinc-50/50 border-y border-zinc-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16 reveal">
                    <span class="text-xs font-bold text-brand-600 uppercase tracking-widest mb-3 block">Our Values</span>
                    <h3 class="font-display text-4xl font-extrabold text-zinc-900">Our Core <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-600 to-indigo-600">Pillars</span></h3>
                </div>
                
                <div class="grid md:grid-cols-3 gap-8">
                    @foreach([
                        ['icon' => 'feather-target', 'title' => 'Absolute Precision', 'desc' => 'Every data point is verified through multi-layer integrity checks before being transmitted to clinical reports.', 'color' => 'brand'],
                        ['icon' => 'feather-zap', 'title' => 'Deep Innovation', 'desc' => 'We continuously push the boundaries of what a Diagnostic OS can achieve with automation and smart analytics.', 'color' => 'indigo'],
                        ['icon' => 'feather-heart', 'title' => 'Human Empathy', 'desc' => 'Designed with a deep understanding of the high-pressure environment of diagnostics and patient care.', 'color' => 'emerald'],
                    ] as $i => $value)
                        <div class="reveal delay-{{ $i + 1 }} group bg-white rounded-3xl p-10 border border-zinc-200 shadow-sm hover:shadow-xl hover:border-{{ $value['color'] }}-200 transition-all duration-300 hover:-translate-y-1">
                            <div class="w-14 h-14 rounded-2xl bg-{{ $value['color'] }}-50 text-{{ $value['color'] }}-600 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                                <i class="{{ $value['icon'] }} text-2xl"></i>
                            </div>
                            <h5 class="text-xl font-bold text-zinc-900 mb-3">{{ $value['title'] }}</h5>
                            <p class="text-sm text-zinc-600 leading-relaxed font-medium">{{ $value['desc'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="py-24 bg-zinc-950 relative overflow-hidden shadow-[inset_0_4px_24px_rgba(0,0,0,0.1)]">
            <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 32px 32px;"></div>
            
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="text-center mb-20 reveal">
                    <h3 class="font-display text-4xl font-extrabold text-white">The Road to <span class="text-brand-400">2026</span></h3>
                </div>
                
                <div class="relative">
                    <div class="hidden md:block absolute top-6 left-[10%] right-[10%] h-0.5 bg-gradient-to-r from-zinc-800 via-brand-500/50 to-zinc-800 z-0"></div>
                    
                    <div class="grid md:grid-cols-4 gap-12 text-center relative z-10">
                        @foreach([
                            ['date' => 'Mar 2024', 'title' => 'Platform Launch', 'desc' => 'Core LIS engine goes live'],
                            ['date' => 'Aug 2024', 'title' => '100 Lab Milestone', 'desc' => '100 diagnostic centers onboarded'],
                            ['date' => 'Feb 2025', 'title' => 'Partner Portal', 'desc' => 'Doctor & Agent referral system'],
                            ['date' => 'Current', 'title' => 'Scale Unbound', 'desc' => 'Multi-branch, multi-city expansion'],
                        ] as $i => $milestone)
                            <div class="reveal delay-{{ $i + 1 }} group">
                                <div class="w-12 h-12 mx-auto bg-zinc-900 border-4 border-zinc-950 rounded-full flex items-center justify-center mb-6 relative group-hover:scale-110 transition-transform duration-300">
                                    <div class="w-3 h-3 rounded-full bg-brand-400 shadow-[0_0_15px_rgba(99,102,241,0.5)]"></div>
                                </div>
                                <span class="text-[11px] font-bold text-brand-400 uppercase tracking-widest block mb-2">{{ $milestone['date'] }}</span>
                                <p class="font-bold text-lg text-white mb-2">{{ $milestone['title'] }}</p>
                                <p class="text-sm text-zinc-400 font-medium">{{ $milestone['desc'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <section class="py-32 relative overflow-hidden bg-white">
            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-brand-50/50 blur-[120px] rounded-full pointer-events-none"></div>
            
            <div class="max-w-3xl mx-auto px-4 text-center relative z-10 reveal">
                <h2 class="font-display text-5xl md:text-6xl font-extrabold mb-8 tracking-tight text-zinc-900">
                    Ready to <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-600 to-indigo-600">Modernize?</span>
                </h2>
                <p class="text-zinc-600 mb-12 text-xl font-medium">Join the growing network of modern diagnostic labs experiencing zero downtime and automated workflows.</p>
                
                <a href="{{ route('enquiry') }}" class="inline-flex justify-center items-center gap-2 px-10 py-5 bg-zinc-900 hover:bg-brand-600 text-white rounded-full font-bold text-lg shadow-xl shadow-zinc-900/10 hover:shadow-brand-500/25 transition-all duration-300 transform hover:-translate-y-1">
                    Request a Demo <i class="feather-arrow-right"></i>
                </a>
            </div>
        </section>

    </div>
</x-landing-layout>