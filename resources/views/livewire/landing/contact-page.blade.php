@php
    $contactEmail = \App\Models\SiteSetting::get('contact_email', 'support@swspathology.com');
    $contactPhone = \App\Models\SiteSetting::get('contact_phone', '+91 98765 43210');
    $contactAddress = \App\Models\SiteSetting::get('contact_address', 'New Delhi, India');
    $contactWhatsapp = \App\Models\SiteSetting::get('contact_whatsapp', '+91 98765 43210');
@endphp

<div class="bg-white text-zinc-700 selection:bg-brand-500/10 selection:text-brand-700 font-sans overflow-hidden">

    <!-- Hero -->
    <section class="pt-32 pb-20 relative overflow-hidden bg-zinc-50 border-b border-zinc-100">
        <div class="absolute inset-0 z-0 pointer-events-none">
            <div class="absolute top-0 right-1/4 w-[600px] h-[600px] bg-brand-400/10 blur-[150px] rounded-full translate-x-1/2 -translate-y-1/4"></div>
            <div class="absolute bottom-0 left-1/4 w-[500px] h-[500px] bg-emerald-400/5 blur-[150px] rounded-full -translate-x-1/2 translate-y-1/4"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10 reveal">
            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-zinc-200 shadow-sm text-brand-600 text-xs font-bold uppercase tracking-widest mb-8">
                <i class="feather-mail text-sm"></i> Get In Touch
            </span>

            <h1 class="font-display text-5xl md:text-6xl lg:text-7xl font-extrabold tracking-tight mb-6 text-zinc-900 leading-[1.1]">
                Contact <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-600 to-indigo-600">Our Team</span>
            </h1>

            <p class="text-xl text-zinc-600 max-w-2xl mx-auto leading-relaxed font-medium mb-10">
                Whether you're a single clinic or a multi-city diagnostic chain, we're here to help you scale your operations.
            </p>
        </div>
    </section>

    <!-- Contact Grid -->
    <section class="py-24 relative bg-white z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-12 gap-16">

            <!-- Info Side -->
            <div class="lg:col-span-5 reveal-left space-y-10">
                <div>
                    <h3 class="font-display text-3xl md:text-4xl font-extrabold mb-4 tracking-tight text-zinc-900">
                        We'd Love to <br><span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-600 to-indigo-600">Hear From You</span>
                    </h3>
                    <p class="text-zinc-600 leading-relaxed font-medium">Fill out the form or reach us directly through any of the channels below. Our team typically responds within 2 hours.</p>
                </div>

                <div class="grid gap-4">
                    @foreach([
                        ['icon' => 'feather-mail', 'label' => 'Email Support', 'value' => $contactEmail, 'color' => 'brand', 'link' => 'mailto:'.$contactEmail],
                        ['icon' => 'feather-phone', 'label' => 'Call Us', 'value' => $contactPhone, 'color' => 'indigo', 'link' => 'tel:'.$contactPhone],
                        ['icon' => 'feather-message-circle', 'label' => 'WhatsApp', 'value' => $contactWhatsapp, 'color' => 'emerald', 'link' => 'https://wa.me/'.preg_replace('/[^0-9]/', '', $contactWhatsapp)],
                        ['icon' => 'feather-map-pin', 'label' => 'Headquarters', 'value' => $contactAddress, 'color' => 'amber', 'link' => '#'],
                    ] as $info)
                        <a href="{{ $info['link'] }}" class="bg-white rounded-2xl p-5 border border-zinc-200 hover:border-{{ $info['color'] }}-300 hover:shadow-lg hover:shadow-{{ $info['color'] }}-500/5 transition-all duration-300 flex items-center gap-5 group hover:-translate-y-1">
                            <div class="w-14 h-14 rounded-xl bg-{{ $info['color'] }}-50 text-{{ $info['color'] }}-600 flex items-center justify-center group-hover:bg-{{ $info['color'] }}-500 group-hover:text-white transition-colors duration-300">
                                <i class="{{ $info['icon'] }} text-xl"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mb-0.5">{{ $info['label'] }}</p>
                                <p class="text-sm font-bold text-zinc-900 group-hover:text-{{ $info['color'] }}-600 transition-colors">{{ $info['value'] }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Form Side -->
            <div class="lg:col-span-7 reveal-right">
                <div class="bg-white rounded-[2.5rem] p-8 md:p-12 border border-zinc-200 shadow-2xl shadow-zinc-200/50 relative overflow-hidden h-full flex flex-col justify-center">

                    <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-brand-500 to-indigo-500"></div>

                    @if($submitted)
                        <div class="text-center py-10 px-4 animate-fade-in-up">
                            <div class="w-24 h-24 bg-emerald-50 border-8 border-emerald-50/50 rounded-full flex items-center justify-center mx-auto mb-8 relative">
                                <div class="absolute inset-0 rounded-full bg-emerald-100 animate-ping opacity-50"></div>
                                <i class="feather-check text-4xl text-emerald-500 relative z-10"></i>
                            </div>

                            <h3 class="font-display text-3xl font-bold text-zinc-900 mb-4">Message Sent!</h3>
                            <p class="text-zinc-600 font-medium mb-10 max-w-sm mx-auto">Thank you for reaching out. Our support team has received your message and will get back to you shortly.</p>

                            <button wire:click="$set('submitted', false)" class="inline-flex items-center gap-2 px-8 py-3.5 bg-zinc-100 hover:bg-zinc-200 text-zinc-900 rounded-full font-bold transition-colors duration-300">
                                <i class="feather-refresh-cw text-sm"></i> Send Another Message
                            </button>
                        </div>
                    @else
                        <div class="relative z-10">
                            <div class="mb-8">
                                <h3 class="font-display text-2xl font-bold text-zinc-900 mb-2">Send a Message</h3>
                                <p class="text-sm text-zinc-500 font-medium">Fill in your details below and we'll connect with you.</p>
                            </div>

                            <form wire:submit="submit" class="space-y-6">
                                <div class="grid md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="text-[11px] font-bold text-zinc-500 uppercase tracking-widest mb-2 block">Full Name <span class="text-rose-500">*</span></label>
                                        <input type="text" wire:model="name" class="w-full px-5 py-4 bg-zinc-50 hover:bg-white border border-zinc-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all text-sm font-medium text-zinc-900 placeholder:text-zinc-400 shadow-sm" placeholder="Dr. Rahul Verma" required>
                                        @error('name') <span class="text-xs text-rose-500 mt-1.5 flex items-center gap-1"><i class="feather-alert-circle"></i> {{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="text-[11px] font-bold text-zinc-500 uppercase tracking-widest mb-2 block">Phone Number</label>
                                        <input type="tel" wire:model="phone" class="w-full px-5 py-4 bg-zinc-50 hover:bg-white border border-zinc-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all text-sm font-medium text-zinc-900 placeholder:text-zinc-400 shadow-sm" placeholder="+91 XXXXX XXXXX">
                                        @error('phone') <span class="text-xs text-rose-500 mt-1.5 flex items-center gap-1"><i class="feather-alert-circle"></i> {{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div>
                                    <label class="text-[11px] font-bold text-zinc-500 uppercase tracking-widest mb-2 block">Email Address <span class="text-rose-500">*</span></label>
                                    <input type="email" wire:model="email" class="w-full px-5 py-4 bg-zinc-50 hover:bg-white border border-zinc-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all text-sm font-medium text-zinc-900 placeholder:text-zinc-400 shadow-sm" placeholder="doctor@clinic.com" required>
                                    @error('email') <span class="text-xs text-rose-500 mt-1.5 flex items-center gap-1"><i class="feather-alert-circle"></i> {{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="text-[11px] font-bold text-zinc-500 uppercase tracking-widest mb-2 block">Your Message <span class="text-rose-500">*</span></label>
                                    <textarea wire:model="message" rows="4" class="w-full px-5 py-4 bg-zinc-50 hover:bg-white border border-zinc-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all text-sm font-medium text-zinc-900 placeholder:text-zinc-400 resize-none shadow-sm" placeholder="Tell us about your laboratory requirements..." required></textarea>
                                    @error('message') <span class="text-xs text-rose-500 mt-1.5 flex items-center gap-1"><i class="feather-alert-circle"></i> {{ $message }}</span> @enderror
                                </div>

                                <button type="submit" class="w-full py-4 bg-zinc-900 hover:bg-brand-600 text-white rounded-xl font-bold shadow-lg shadow-zinc-900/10 hover:shadow-brand-500/25 transition-all duration-300 flex items-center justify-center gap-2 transform hover:-translate-y-0.5" wire:loading.attr="disabled" wire:target="submit">
                                    <span wire:loading.remove wire:target="submit" class="flex items-center gap-2">
                                        Send Message <i class="feather-send text-sm"></i>
                                    </span>
                                    <span wire:loading wire:target="submit" class="flex items-center gap-2">
                                        <i class="feather-loader animate-spin text-sm"></i> Sending...
                                    </span>
                                </button>
                            </form>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </section>
</div>