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
                @foreach(['branding' => 'Branding', 'home' => 'Home Page', 'about' => 'About Page', 'contact' => 'Contact Info', 'social' => 'Social Links', 'seo' => 'SEO'] as $key => $label)
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
