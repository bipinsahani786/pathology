<div>
    <div class="main-content">
        <div class="container-fluid">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h2 class="fs-20 fw-bolder mb-0">Site Settings</h2>
                    <p class="text-muted mb-0 fs-12">Manage your landing page content and branding</p>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Tabs -->
            <ul class="nav nav-tabs mb-4">
                @foreach(['branding' => 'Branding', 'home' => 'Home Page', 'about' => 'About Page', 'features' => 'Features Page', 'how' => 'How It Works', 'pricing' => 'Pricing Page', 'contact' => 'Contact Info', 'social' => 'Social Links', 'seo' => 'SEO'] as $key => $label)
                    <li class="nav-item">
                        <a class="nav-link {{ $activeTab === $key ? 'active' : '' }}" href="#" wire:click.prevent="$set('activeTab', '{{ $key }}')">{{ $label }}</a>
                    </li>
                @endforeach
            </ul>

            <form wire:submit="save">
                <div class="card">
                    <div class="card-body">
                        <!-- BRANDING -->
                        @if($activeTab === 'branding')
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Site Name</label>
                                    <input type="text" wire:model="site_name" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Tagline</label>
                                    <input type="text" wire:model="site_tagline" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Primary Color</label>
                                    <input type="color" wire:model="primary_color" class="form-control form-control-color" style="height: 45px; width: 100%;">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Site Favicon</label>
                                    <input type="file" wire:model="site_favicon" class="form-control" accept="image/*">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Logo (Collapsed / Small)</label>
                                    <input type="file" wire:model="site_logo_sm" class="form-control" accept="image/*">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Logo (Light)</label>
                                    <input type="file" wire:model="site_logo" class="form-control" accept="image/*">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Logo (Dark)</label>
                                    <input type="file" wire:model="site_logo_dark" class="form-control" accept="image/*">
                                </div>
                            </div>
                        @endif

                        <!-- HOME -->
                        @if($activeTab === 'home')
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-bold">Hero Title</label>
                                    <input type="text" wire:model="hero_title" class="form-control">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold">Hero Subtitle</label>
                                    <textarea wire:model="hero_subtitle" class="form-control" rows="3"></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">CTA Button Text</label>
                                    <input type="text" wire:model="hero_cta_text" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Hero Background Image</label>
                                    <input type="file" wire:model="hero_image" class="form-control" accept="image/*">
                                </div>
                                <div class="col-md-3"><label class="form-label fw-bold">Hero Stat 1 Value</label><input type="text" wire:model="home_hero_stat_1_val" class="form-control" placeholder="10M+"></div>
                                <div class="col-md-3"><label class="form-label fw-bold">Hero Stat 1 Label</label><input type="text" wire:model="home_hero_stat_1_label" class="form-control" placeholder="Reports Generated"></div>
                                <div class="col-md-3"><label class="form-label fw-bold">Hero Stat 2 Value</label><input type="text" wire:model="home_hero_stat_2_val" class="form-control" placeholder="99.9%"></div>
                                <div class="col-md-3"><label class="form-label fw-bold">Hero Stat 2 Label</label><input type="text" wire:model="home_hero_stat_2_label" class="form-control" placeholder="System Uptime"></div>
                                <div class="col-md-3"><label class="form-label fw-bold">Hero Stat 3 Value</label><input type="text" wire:model="home_hero_stat_3_val" class="form-control" placeholder="500+"></div>
                                <div class="col-md-3"><label class="form-label fw-bold">Hero Stat 3 Label</label><input type="text" wire:model="home_hero_stat_3_label" class="form-control" placeholder="Active Labs"></div>
                                <div class="col-md-3"><label class="form-label fw-bold">Hero Stat 4 Value</label><input type="text" wire:model="home_hero_stat_4_val" class="form-control" placeholder="0"></div>
                                <div class="col-md-3"><label class="form-label fw-bold">Hero Stat 4 Label</label><input type="text" wire:model="home_hero_stat_4_label" class="form-control" placeholder="Data Breaches"></div>

                                <div class="col-12 mt-4"><hr></div>
                                <div class="col-md-6"><label class="form-label fw-bold">Stats Title</label><input type="text" wire:model="home_stats_title" class="form-control" placeholder="Powering 500+ Diagnostic Centers"></div>
                                <div class="col-md-6"><label class="form-label fw-bold">Stats Logos (Comma separated)</label><input type="text" wire:model="home_stats_logos" class="form-control" placeholder="METROLAB, QUANTUM DIAG, COREPATH, APEXVUE, LIFEBLOOM"></div>
                                
                                <div class="col-12 mt-4"><hr></div>
                                <div class="col-md-6"><label class="form-label fw-bold">Friction Title</label><input type="text" wire:model="home_friction_title" class="form-control" placeholder="Tired of Paper Friction?"></div>
                                <div class="col-12"><label class="form-label fw-bold">Friction Description</label><input type="text" wire:model="home_friction_desc" class="form-control" placeholder="Legacy systems..."></div>
                                <div class="col-md-6"><label class="form-label fw-bold">Friction Item 1 (Old)</label><input type="text" wire:model="home_friction_old_1" class="form-control" placeholder="Manual Data Entry"></div>
                                <div class="col-md-6"><label class="form-label fw-bold">Friction Item 1 (New)</label><input type="text" wire:model="home_friction_new_1" class="form-control" placeholder="Auto-synced Machine Results"></div>
                                <div class="col-md-6"><label class="form-label fw-bold">Friction Item 2 (Old)</label><input type="text" wire:model="home_friction_old_2" class="form-control" placeholder="Delayed B2B Settlements"></div>
                                <div class="col-md-6"><label class="form-label fw-bold">Friction Item 2 (New)</label><input type="text" wire:model="home_friction_new_2" class="form-control" placeholder="Real-time Partner Payouts"></div>
                                <div class="col-md-6"><label class="form-label fw-bold">Friction Item 3 (Old)</label><input type="text" wire:model="home_friction_old_3" class="form-control" placeholder="No Patient Tracking"></div>
                                <div class="col-md-6"><label class="form-label fw-bold">Friction Item 3 (New)</label><input type="text" wire:model="home_friction_new_3" class="form-control" placeholder="Automated WhatsApp Tracking"></div>

                                <div class="col-12 mt-4"><hr></div>
                                <div class="col-md-6"><label class="form-label fw-bold">Features Title</label><input type="text" wire:model="home_features_title" class="form-control" placeholder="Everything your lab needs."></div>
                                <div class="col-md-6"><label class="form-label fw-bold">Features Description</label><input type="text" wire:model="home_features_desc" class="form-control" placeholder="A comprehensive LIS platform..."></div>
                                <div class="col-md-6"><label class="form-label fw-bold">Steps Title</label><input type="text" wire:model="home_steps_title" class="form-control" placeholder="Four Steps to Automation"></div>
                                <div class="col-md-6"><label class="form-label fw-bold">Steps List (Comma separated)</label><input type="text" wire:model="home_steps_list" class="form-control" placeholder="Register Patient, Process Sample, Verify Results, Auto-Deliver WhatsApp"></div>
                                <div class="col-md-6"><label class="form-label fw-bold">Connect Title</label><input type="text" wire:model="home_connect_title" class="form-control" placeholder="Seamlessly Connects With"></div>
                                <div class="col-md-6"><label class="form-label fw-bold">Connect List (Comma separated)</label><input type="text" wire:model="home_connect_list" class="form-control" placeholder="WhatsApp API, Razorpay, Stripe, Sysmex Analyzers, Erba Analyzers, AWS Cloud"></div>
                                <div class="col-md-6"><label class="form-label fw-bold">Testimonial Title</label><input type="text" wire:model="home_testimonial_title" class="form-control" placeholder="Trusted by Professionals"></div>
                                <div class="col-md-6"><label class="form-label fw-bold">Pricing Title</label><input type="text" wire:model="home_pricing_title" class="form-control" placeholder="Transparent Pricing"></div>
                                <div class="col-12"><label class="form-label fw-bold">Pricing Description</label><input type="text" wire:model="home_pricing_desc" class="form-control" placeholder="Simple plans..."></div>
                            </div>
                        @endif

                        <!-- ABOUT -->
                        @if($activeTab === 'about')
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-bold">About Title</label>
                                    <input type="text" wire:model="about_title" class="form-control">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold">About Description</label>
                                    <textarea wire:model="about_description" class="form-control" rows="4"></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">About Image</label>
                                    <input type="file" wire:model="about_image" class="form-control" accept="image/*">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Stat 1 Value</label>
                                    <input type="text" wire:model="about_stat_labs" class="form-control" placeholder="500+">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Stat 1 Label</label>
                                    <input type="text" wire:model="about_stat_labs_label" class="form-control" placeholder="Labs Integrated">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Stat 2 Value</label>
                                    <input type="text" wire:model="about_stat_uptime" class="form-control" placeholder="99.9%">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Stat 2 Label</label>
                                    <input type="text" wire:model="about_stat_uptime_label" class="form-control" placeholder="Uptime SLA">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Stat 3 Value</label>
                                    <input type="text" wire:model="about_stat_reports" class="form-control" placeholder="1M+">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Stat 3 Label</label>
                                    <input type="text" wire:model="about_stat_reports_label" class="form-control" placeholder="Reports Monthly">
                                </div>
                                <div class="col-12 mt-4"><hr></div>
                                <div class="col-md-6"><label class="form-label fw-bold">Story Title</label><input type="text" wire:model="about_story_title" class="form-control" placeholder="Our Story"></div>
                                <div class="col-12"><label class="form-label fw-bold">Story Description</label><textarea wire:model="about_story_desc" class="form-control" rows="3"></textarea></div>
                                <div class="col-md-6"><label class="form-label fw-bold">Heritage Subtitle</label><input type="text" wire:model="about_heritage_subtitle" class="form-control" placeholder="Heritage"></div>
                                <div class="col-md-6"><label class="form-label fw-bold">Heritage Title</label><input type="text" wire:model="about_heritage_title" class="form-control" placeholder="Built by Experts, for Professionals."></div>
                                <div class="col-md-6"><label class="form-label fw-bold">Values Subtitle</label><input type="text" wire:model="about_values_subtitle" class="form-control" placeholder="Our Values"></div>
                                <div class="col-md-6"><label class="form-label fw-bold">Values Title</label><input type="text" wire:model="about_values_title" class="form-control" placeholder="Our Core Pillars"></div>
                                <div class="col-md-6"><label class="form-label fw-bold">Roadmap Title</label><input type="text" wire:model="about_roadmap_title" class="form-control" placeholder="The Road to 2026"></div>
                                <div class="col-12 mt-4"><hr></div>
                                <div class="col-md-4"><label class="form-label fw-bold">Milestone 1 Date</label><input type="text" wire:model="about_milestone_date_1" class="form-control" placeholder="Mar 2024"></div>
                                <div class="col-md-4"><label class="form-label fw-bold">Milestone 1 Title</label><input type="text" wire:model="about_milestone_title_1" class="form-control" placeholder="Platform Launch"></div>
                                <div class="col-md-4"><label class="form-label fw-bold">Milestone 1 Desc</label><input type="text" wire:model="about_milestone_desc_1" class="form-control" placeholder="Core LIS engine goes live"></div>
                                <div class="col-md-4"><label class="form-label fw-bold">Milestone 2 Date</label><input type="text" wire:model="about_milestone_date_2" class="form-control" placeholder="Aug 2024"></div>
                                <div class="col-md-4"><label class="form-label fw-bold">Milestone 2 Title</label><input type="text" wire:model="about_milestone_title_2" class="form-control" placeholder="100 Lab Milestone"></div>
                                <div class="col-md-4"><label class="form-label fw-bold">Milestone 2 Desc</label><input type="text" wire:model="about_milestone_desc_2" class="form-control" placeholder="100 diagnostic centers onboarded"></div>
                                <div class="col-md-4"><label class="form-label fw-bold">Milestone 3 Date</label><input type="text" wire:model="about_milestone_date_3" class="form-control" placeholder="Feb 2025"></div>
                                <div class="col-md-4"><label class="form-label fw-bold">Milestone 3 Title</label><input type="text" wire:model="about_milestone_title_3" class="form-control" placeholder="Partner Portal"></div>
                                <div class="col-md-4"><label class="form-label fw-bold">Milestone 3 Desc</label><input type="text" wire:model="about_milestone_desc_3" class="form-control" placeholder="Doctor & Agent referral system"></div>
                                <div class="col-md-4"><label class="form-label fw-bold">Milestone 4 Date</label><input type="text" wire:model="about_milestone_date_4" class="form-control" placeholder="Current"></div>
                                <div class="col-md-4"><label class="form-label fw-bold">Milestone 4 Title</label><input type="text" wire:model="about_milestone_title_4" class="form-control" placeholder="Scale Unbound"></div>
                                <div class="col-md-4"><label class="form-label fw-bold">Milestone 4 Desc</label><input type="text" wire:model="about_milestone_desc_4" class="form-control" placeholder="Multi-branch, multi-city expansion"></div>
                                <div class="col-12 mt-4"><hr></div>
                                <div class="col-md-6"><label class="form-label fw-bold">CTA Title</label><input type="text" wire:model="about_cta_title" class="form-control" placeholder="Ready to Modernize?"></div>
                                <div class="col-12"><label class="form-label fw-bold">CTA Description</label><input type="text" wire:model="about_cta_desc" class="form-control" placeholder="Join the growing network..."></div>
                            </div>
                        @endif

                        <!-- FEATURES -->
                        @if($activeTab === 'features')
                            <div class="row g-3">
                                <div class="col-md-6"><label class="form-label fw-bold">Hero Title</label><input type="text" wire:model="features_hero_title" class="form-control" placeholder="Everything You Need to Run Your Lab"></div>
                                <div class="col-12"><label class="form-label fw-bold">Hero Description</label><textarea wire:model="features_hero_desc" class="form-control" rows="2"></textarea></div>
                            </div>
                        @endif

                        <!-- HOW IT WORKS -->
                        @if($activeTab === 'how')
                            <div class="row g-3">
                                <div class="col-md-6"><label class="form-label fw-bold">Hero Title</label><input type="text" wire:model="how_hero_title" class="form-control" placeholder="How SWS Automates Your Lab"></div>
                                <div class="col-12"><label class="form-label fw-bold">Hero Description</label><textarea wire:model="how_hero_desc" class="form-control" rows="2"></textarea></div>
                            </div>
                        @endif

                        <!-- PRICING -->
                        @if($activeTab === 'pricing')
                            <div class="row g-3">
                                <div class="col-md-6"><label class="form-label fw-bold">Hero Subtitle</label><input type="text" wire:model="pricing_hero_subtitle" class="form-control" placeholder="Scale with Confidence"></div>
                                <div class="col-md-6"><label class="form-label fw-bold">Hero Title</label><input type="text" wire:model="pricing_hero_title" class="form-control" placeholder="Choose Your Growth Plan"></div>
                                <div class="col-12"><label class="form-label fw-bold">Hero Description</label><textarea wire:model="pricing_hero_desc" class="form-control" rows="2"></textarea></div>
                                <div class="col-md-6"><label class="form-label fw-bold">FAQ Title</label><input type="text" wire:model="pricing_faq_title" class="form-control" placeholder="Pricing Questions"></div>
                                <div class="col-md-6"><label class="form-label fw-bold">CTA Title</label><input type="text" wire:model="pricing_cta_title" class="form-control" placeholder="Upgrade Your Diagnostic Intelligence"></div>
                                <div class="col-12"><label class="form-label fw-bold">CTA Description</label><textarea wire:model="pricing_cta_desc" class="form-control" rows="2"></textarea></div>
                            </div>
                        @endif

                        <!-- CONTACT -->
                        @if($activeTab === 'contact')
                            <div class="row g-3">
                                <div class="col-md-6"><label class="form-label fw-bold">Email</label><input type="email" wire:model="contact_email" class="form-control"></div>
                                <div class="col-md-6"><label class="form-label fw-bold">Phone</label><input type="text" wire:model="contact_phone" class="form-control"></div>
                                <div class="col-md-6"><label class="form-label fw-bold">Address</label><input type="text" wire:model="contact_address" class="form-control"></div>
                                <div class="col-md-6"><label class="form-label fw-bold">WhatsApp</label><input type="text" wire:model="contact_whatsapp" class="form-control"></div>
                            </div>
                        @endif

                        <!-- SOCIAL -->
                        @if($activeTab === 'social')
                            <div class="row g-3">
                                <div class="col-md-6"><label class="form-label fw-bold">Twitter / X</label><input type="url" wire:model="social_twitter" class="form-control"></div>
                                <div class="col-md-6"><label class="form-label fw-bold">Facebook</label><input type="url" wire:model="social_facebook" class="form-control"></div>
                                <div class="col-md-6"><label class="form-label fw-bold">LinkedIn</label><input type="url" wire:model="social_linkedin" class="form-control"></div>
                                <div class="col-md-6"><label class="form-label fw-bold">Instagram</label><input type="url" wire:model="social_instagram" class="form-control"></div>
                            </div>
                        @endif

                        <!-- SEO -->
                        @if($activeTab === 'seo')
                            <div class="row g-3">
                                <div class="col-12"><label class="form-label fw-bold">Meta Title</label><input type="text" wire:model="meta_title" class="form-control"></div>
                                <div class="col-12"><label class="form-label fw-bold">Meta Description</label><textarea wire:model="meta_description" class="form-control" rows="3"></textarea></div>
                            </div>
                        @endif
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="feather-save me-1"></i> Save Settings
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
