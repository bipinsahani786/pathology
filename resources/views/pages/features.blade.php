@php
    $features = \App\Models\LandingFeature::active()->get();
    $colors = [
        'primary' => ['bg' => 'bg-brand-500', 'light' => 'bg-brand-50', 'text' => 'text-brand-600', 'shadow' => 'shadow-brand-500/20'],
        'success' => ['bg' => 'bg-emerald-500', 'light' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'shadow' => 'shadow-emerald-500/20'],
        'info' => ['bg' => 'bg-blue-500', 'light' => 'bg-blue-50', 'text' => 'text-blue-600', 'shadow' => 'shadow-blue-500/20'],
        'warning' => ['bg' => 'bg-amber-500', 'light' => 'bg-amber-50', 'text' => 'text-amber-600', 'shadow' => 'shadow-amber-500/20'],
        'danger' => ['bg' => 'bg-rose-500', 'light' => 'bg-rose-50', 'text' => 'text-rose-600', 'shadow' => 'shadow-rose-500/20'],
        'purple' => ['bg' => 'bg-purple-500', 'light' => 'bg-purple-50', 'text' => 'text-purple-600', 'shadow' => 'shadow-purple-500/20'],
        'teal' => ['bg' => 'bg-teal-500', 'light' => 'bg-teal-50', 'text' => 'text-teal-600', 'shadow' => 'shadow-teal-500/20'],
        'dark' => ['bg' => 'bg-zinc-800', 'light' => 'bg-zinc-100', 'text' => 'text-zinc-700', 'shadow' => 'shadow-zinc-800/20'],
    ];
@endphp

<x-landing-layout>
    <x-slot name="title">Features - {{ \App\Models\SiteSetting::get('site_name', 'SWS Pathology') }}</x-slot>

    <div class="bg-white text-zinc-700 selection:bg-brand-500/10 selection:text-brand-700 font-sans overflow-hidden">

        <section class="pt-32 pb-24 relative overflow-hidden bg-zinc-50 border-b border-zinc-100">
            <div class="absolute inset-0 z-0 pointer-events-none">
                <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-emerald-400/10 blur-[150px] rounded-full translate-x-1/4 -translate-y-1/4"></div>
                <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-brand-400/10 blur-[150px] rounded-full -translate-x-1/4 translate-y-1/4"></div>
            </div>
            
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10 reveal">
                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-emerald-50 border border-emerald-100 shadow-sm text-emerald-700 text-xs font-bold uppercase tracking-widest mb-8">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Cutting-Edge LIS
                </span>
                
                <h1 class="font-display text-5xl md:text-6xl lg:text-7xl font-extrabold tracking-tight mb-6 text-zinc-900 leading-[1.1]">
                    Engineered for <br class="hidden md:block" />
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 to-teal-600">Diagnostic Precision</span>
                </h1>
                
                <p class="text-xl text-zinc-600 max-w-2xl mx-auto leading-relaxed font-medium mb-10">
                    The most comprehensive suite of laboratory intelligence tools, designed to automate your workflow from sample collection to final report.
                </p>

                <div class="w-full max-w-5xl mx-auto rounded-[2rem] border border-zinc-200 shadow-2xl overflow-hidden bg-white p-2">
                     <img src="https://images.unsplash.com/photo-1579154204601-01588f351e67?auto=format&fit=crop&q=80&w=1200" alt="Lab Workstation" class="w-full h-[400px] object-cover rounded-xl opacity-90 hover:opacity-100 transition-opacity duration-500">
                </div>
            </div>
        </section>

        @if($features->count() > 0)
            <section class="py-24 bg-white relative">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-2 gap-16 items-center">
                    
                    <div class="reveal-left">
                        <span class="inline-flex items-center gap-2 mb-4 text-brand-600 font-bold uppercase tracking-widest text-xs">
                            <i class="feather-cpu"></i> Automation Core
                        </span>
                        
                        <h3 class="font-display text-4xl md:text-5xl font-extrabold mb-6 tracking-tight text-zinc-900">
                            {{ $features->first()->title }}
                        </h3>
                        
                        <p class="text-lg text-zinc-600 mb-10 leading-relaxed font-medium">
                            {{ $features->first()->description }}
                        </p>
                        
                        <div class="space-y-5">
                            @foreach(['Supports 200+ Analyzer Manufacturers', 'Real-time Hardware Synchronization', 'Zero Manual Data Entry Errors'] as $point)
                                <div class="flex items-center gap-4 group">
                                    <div class="w-10 h-10 rounded-full bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 group-hover:bg-emerald-500 group-hover:text-white transition-all duration-300 shadow-sm">
                                        <i class="feather-check text-sm"></i>
                                    </div>
                                    <span class="text-zinc-700 font-semibold text-sm">{{ $point }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="reveal-right">
                        <div class="relative">
                            <div class="absolute -inset-4 bg-gradient-to-tr from-brand-400/20 to-teal-400/20 blur-2xl rounded-[3rem] opacity-70"></div>
                            
                            <div class="relative bg-zinc-950 rounded-3xl p-6 shadow-2xl border border-zinc-800 ring-1 ring-white/10">
                                <div class="flex items-center gap-2 mb-6 border-b border-zinc-800 pb-4">
                                    <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                                    <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                                    <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                                    <div class="ml-4 px-3 py-1 bg-zinc-900 rounded-md text-xs text-zinc-500 font-mono border border-zinc-800">
                                        analyzer-sync-module
                                    </div>
                                </div>
                                
                                <div class="space-y-3">
                                    <div class="text-xs text-zinc-500 uppercase tracking-widest font-bold mb-4">Incoming Data Streams</div>
                                    @foreach(['CBC Complete', 'Lipid Profile', 'Thyroid Panel', 'HbA1c'] as $test)
                                        <div class="flex items-center justify-between bg-zinc-900 border border-zinc-800 rounded-xl p-4 hover:border-brand-500/50 transition-colors cursor-default group">
                                            <div class="flex items-center gap-3">
                                                <i class="feather-activity text-zinc-600 group-hover:text-brand-400 transition-colors"></i>
                                                <span class="text-sm text-zinc-200 font-medium">{{ $test }}</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="relative flex h-2 w-2">
                                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                                </span>
                                                <span class="text-[10px] text-emerald-400 font-bold bg-emerald-500/10 border border-emerald-500/20 px-2 py-1 rounded-md tracking-wider">
                                                    AUTO-SYNCED
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif

        <section class="py-24 bg-zinc-50/50 border-t border-zinc-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16 reveal">
                    <span class="text-xs font-bold text-brand-600 uppercase tracking-widest mb-3 block">Complete Suite</span>
                    <h3 class="font-display text-4xl font-extrabold text-zinc-900 tracking-tight">The Full Feature <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-600 to-indigo-600">Matrix</span></h3>
                </div>
                
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 auto-rows-fr">
                    @foreach($features as $i => $feature)
                        @php 
                            // Fallback if color doesn't exist in array
                            $c = $colors[$feature->color] ?? $colors['primary']; 
                        @endphp
                        <div class="reveal delay-{{ ($i % 6) + 1 }} group relative bg-white rounded-[2rem] p-8 border border-zinc-200 shadow-sm hover:shadow-xl hover:border-{{ explode('-', $c['text'])[1] ?? 'brand' }}-300 transition-all duration-300 hover:-translate-y-1 flex flex-col">
                            <div class="w-12 h-12 {{ $c['light'] }} rounded-xl flex items-center justify-center {{ $c['text'] }} mb-6 group-hover:scale-110 transition-transform duration-300 border border-white/50">
                                <i class="{{ $feature->icon }} text-xl"></i>
                            </div>
                            <h5 class="text-xl font-bold text-zinc-900 mb-3">{{ $feature->title }}</h5>
                            <p class="text-sm text-zinc-600 leading-relaxed font-medium mb-auto">{{ $feature->description }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="py-32 bg-brand-600 text-center relative overflow-hidden">
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-white/10 blur-[120px] rounded-full"></div>
            </div>
            
            <div class="relative z-10 reveal max-w-3xl mx-auto px-4">
                <h2 class="font-display text-5xl md:text-6xl font-extrabold text-white mb-6 tracking-tight">
                    Modernize Your <span class="text-brand-200">Lab DNA</span>
                </h2>
                <p class="text-xl text-brand-100 mb-10 font-medium">
                    Stop managing paper. Start managing health outcomes.
                </p>
                
                <a href="#contact" class="inline-flex justify-center items-center gap-2 px-10 py-5 bg-white text-brand-600 rounded-full font-bold text-lg shadow-xl shadow-brand-900/20 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                    Contact Us Today <i class="feather-arrow-right"></i>
                </a>
            </div>
        </section>

    </div>
</x-landing-layout>