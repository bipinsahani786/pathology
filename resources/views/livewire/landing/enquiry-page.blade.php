<div>
    <!-- Hero -->
    <section class="pt-32 pb-20 relative overflow-hidden">
        <div class="absolute top-0 left-0 w-[600px] h-[600px] bg-indigo-500/10 blur-[200px] rounded-full z-0"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10 reveal">
            <span
                class="inline-block px-4 py-1.5 rounded-full bg-indigo-50 text-indigo-600 text-[11px] font-bold uppercase tracking-wider mb-6">Request
                a Demo</span>
            <h1 class="font-display text-5xl md:text-7xl font-extrabold tracking-tight mb-8">Let's Build Your <span
                    class="gradient-text">Digital Lab</span></h1>
            <p class="text-xl text-zinc-500 max-w-2xl mx-auto leading-relaxed">Tell us about your lab and we'll create a
                customized demo for your specific needs.</p>
        </div>
    </section>

    <!-- Enquiry Form -->
    <section class="pb-28">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl p-8 md:p-10 border border-zinc-100 shadow-xl relative overflow-hidden group reveal">
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>

                @if($submitted)
                    <div class="mb-8 bg-emerald-50 rounded-2xl p-6 border border-emerald-100 flex items-start gap-4 relative z-10 animate-fade-in-up">
                        <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center shrink-0">
                            <i class="feather-check text-emerald-600 text-lg"></i>
                        </div>
                        <div>
                            <h4 class="text-emerald-800 font-bold mb-1">Enquiry Received Successfully!</h4>
                            <p class="text-sm text-emerald-600 font-medium">Our team will contact you within 24 hours to schedule your personalized demo.</p>
                        </div>
                        <button wire:click="$set('submitted', false)" class="absolute top-4 right-4 text-emerald-400 hover:text-emerald-600">
                            <i class="feather-x"></i>
                        </button>
                    </div>
                @endif

                <h3 class="font-display text-2xl font-bold text-zinc-900 mb-2 relative z-10">Tell Us About Your Lab</h3>
                <p class="text-sm text-zinc-500 mb-8 relative z-10">All fields marked with * are required.</p>

                <form wire:submit="submit" class="space-y-6 relative z-10">
                    <div class="grid md:grid-cols-2 gap-5">
                        <div>
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mb-1.5 block">Full Name *</label>
                            <input type="text" wire:model="name" class="w-full px-4 py-3.5 bg-zinc-50 border border-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all text-sm font-medium" required>
                            @error('name') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mb-1.5 block">Email *</label>
                            <input type="email" wire:model="email" class="w-full px-4 py-3.5 bg-zinc-50 border border-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all text-sm font-medium" required>
                            @error('email') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-5">
                        <div>
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mb-1.5 block">Phone *</label>
                            <input type="tel" wire:model="phone" class="w-full px-4 py-3.5 bg-zinc-50 border border-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all text-sm font-medium" required>
                            @error('phone') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mb-1.5 block">Lab / Clinic Name *</label>
                            <input type="text" wire:model="lab_name" class="w-full px-4 py-3.5 bg-zinc-50 border border-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all text-sm font-medium" required>
                            @error('lab_name') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid md:grid-cols-3 gap-5">
                        <div>
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mb-1.5 block">City *</label>
                            <input type="text" wire:model="lab_city" class="w-full px-4 py-3.5 bg-zinc-50 border border-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all text-sm font-medium" required>
                            @error('lab_city') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mb-1.5 block">Tests / Month</label>
                            <select wire:model="tests_per_month" class="w-full px-4 py-3.5 bg-zinc-50 border border-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all text-sm font-medium">
                                <option value="">Select</option>
                                <option value="< 500">Less than 500</option>
                                <option value="500-2000">500 - 2,000</option>
                                <option value="2000-5000">2,000 - 5,000</option>
                                <option value="5000+">5,000+</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mb-1.5 block">Branches</label>
                            <select wire:model="branches" class="w-full px-4 py-3.5 bg-zinc-50 border border-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all text-sm font-medium">
                                <option value="">Select</option>
                                <option value="1">Single Location</option>
                                <option value="2-5">2 - 5 Branches</option>
                                <option value="5-10">5 - 10 Branches</option>
                                <option value="10+">10+ Branches</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mb-1.5 block">Additional Requirements</label>
                        <textarea wire:model="message" rows="4" class="w-full px-4 py-3.5 bg-zinc-50 border border-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all text-sm font-medium resize-none" placeholder="Tell us about any specific features you need..."></textarea>
                    </div>

                    <button type="submit" class="w-full py-4 bg-gradient-to-r from-brand-500 to-brand-700 text-white rounded-xl font-bold shadow-lg shadow-brand-500/25 hover:shadow-brand-500/40 hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-center gap-2">
                        <span wire:loading.remove>Submit Enquiry</span>
                        <span wire:loading>Submitting...</span>
                        <i class="feather-send text-sm" wire:loading.remove></i>
                    </button>
                </form>
            </div>
        </div>
    </section>
</div>