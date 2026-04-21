@php
    $siteName = \App\Models\SiteSetting::get('site_name', 'SWS Pathology');
    $siteTagline = \App\Models\SiteSetting::get('site_tagline', 'Precision Diagnostics & Lab Intelligence');
    $contactEmail = \App\Models\SiteSetting::get('contact_email', 'support@swspathology.com');
    $contactPhone = \App\Models\SiteSetting::get('contact_phone', '+91 98765 43210');
    $socialTwitter = \App\Models\SiteSetting::get('social_twitter', '#');
    $socialFacebook = \App\Models\SiteSetting::get('social_facebook', '#');
    $socialLinkedin = \App\Models\SiteSetting::get('social_linkedin', '#');
    $socialInstagram = \App\Models\SiteSetting::get('social_instagram', '#');
    $siteLogo = \App\Models\SiteSetting::get('site_logo');
@endphp


<footer
    class="bg-zinc-950 text-zinc-400 pt-24 pb-8 relative overflow-hidden selection:bg-brand-500/30 selection:text-white">
    <div
        class="absolute top-0 right-0 w-[600px] h-[600px] bg-brand-600/10 blur-[120px] rounded-full -translate-y-1/2 pointer-events-none">
    </div>
    <div
        class="absolute bottom-0 left-[-10%] w-[500px] h-[500px] bg-indigo-500/10 blur-[150px] rounded-full translate-y-1/2 pointer-events-none">
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-12 lg:gap-8 mb-20">

            <div class="lg:col-span-4 pr-0 lg:pr-8">
                <a href="/" class="flex items-center gap-3 mb-6 group inline-flex">
                    @if($siteLogo)
                        <img src="{{ asset('storage/' . $siteLogo) }}" alt="{{ $siteName }}"
                            class="h-9 w-auto brightness-0 invert opacity-90 group-hover:opacity-100 transition-opacity">
                    @else
                        <div
                            class="w-10 h-10 bg-brand-500 rounded-xl flex items-center justify-center shadow-lg shadow-brand-500/20">
                            <i class="feather-activity text-white text-lg"></i>
                        </div>
                    @endif
                    <span class="font-display font-bold text-2xl tracking-tight text-white">{{ $siteName }}</span>
                </a>
                <p class="leading-relaxed mb-8 text-sm text-zinc-400/90 font-medium">{{ $siteTagline }}</p>

                <div class="flex gap-3">
                    @foreach([['icon' => 'feather-twitter', 'url' => $socialTwitter ?? '#'], ['icon' => 'feather-facebook', 'url' => $socialFacebook ?? '#'], ['icon' => 'feather-linkedin', 'url' => $socialLinkedin ?? '#'], ['icon' => 'feather-instagram', 'url' => $socialInstagram ?? '#']] as $social)
                        <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer"
                            class="w-10 h-10 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-zinc-400 hover:bg-brand-500 hover:text-white hover:border-brand-500 transition-all duration-300 hover:-translate-y-1 focus:ring-2 focus:ring-brand-500/50 focus:outline-none">
                            <i class="{{ $social['icon'] }} text-sm"></i>
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="lg:col-span-2 lg:col-start-6">
                <h4 class="text-sm font-semibold text-white mb-6">Product</h4>
                <ul class="space-y-4">
                    @foreach([['label' => 'Lab Automation', 'url' => route('features')], ['label' => 'Smart Reporting', 'url' => route('features')], ['label' => 'Partner Portal', 'url' => route('features')], ['label' => 'Pricing Plans', 'url' => route('pricing')]] as $link)
                        <li>
                            <a href="{{ $link['url'] }}"
                                class="text-sm group flex items-center gap-2 hover:text-white transition-colors duration-300">
                                <span>{{ $link['label'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="lg:col-span-2">
                <h4 class="text-sm font-semibold text-white mb-6">Company</h4>
                <ul class="space-y-4">
                    @foreach([['label' => 'About Us', 'url' => route('about')], ['label' => 'How It Works', 'url' => route('how-it-works')], ['label' => 'Contact Sales', 'url' => route('contact')], ['label' => 'Request Demo', 'url' => route('enquiry')]] as $link)
                        <li>
                            <a href="{{ $link['url'] }}"
                                class="text-sm hover:text-white transition-colors duration-300">{{ $link['label'] }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="lg:col-span-2">
                <h4 class="text-sm font-semibold text-white mb-6">Support</h4>
                <ul class="space-y-4">
                    <li><a href="{{ route('portal.login') }}"
                            class="text-sm font-bold text-brand-500 hover:text-brand-400 transition-colors duration-300">Download
                            Report</a></li>
                    <li><a href="{{ route('faq') }}"
                            class="text-sm hover:text-white transition-colors duration-300">FAQ</a></li>
                    <li><a href="{{ route('terms') }}"
                            class="text-sm hover:text-white transition-colors duration-300">Terms of Service</a></li>
                    <li><a href="{{ route('privacy') }}"
                            class="text-sm hover:text-white transition-colors duration-300">Privacy Policy</a></li>
                    <li><a href="mailto:{{ $contactEmail }}"
                            class="text-sm text-brand-400 hover:text-brand-300 transition-colors duration-300">{{ $contactEmail }}</a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="border-t border-white/10 pt-8 flex flex-col md:flex-row justify-between items-center gap-6">
            <p class="text-sm text-zinc-500 text-center md:text-left">
                &copy; {{ date('Y') }} {{ $siteName }}. All rights reserved.
            </p>
            <div class="flex items-center gap-3 bg-white/5 px-4 py-2 rounded-full border border-white/5">
                <div class="relative flex h-2.5 w-2.5">
                    <span
                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                </div>
                <span class="text-xs font-medium text-emerald-400/90 uppercase tracking-wider">All Systems
                    Operational</span>
            </div>
        </div>
    </div>
</footer>