<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\SiteSetting;

class SiteSettingsManager extends Component
{
    use WithFileUploads;

    public string $activeTab = 'branding';

    // Branding
    public ?string $site_name = '';
    public ?string $site_tagline = '';
    public ?string $primary_color = '';
    public $site_logo;
    public $site_logo_dark;
    public $site_logo_sm;
    public $site_favicon;

    // Home
    public ?string $hero_title = '';
    public ?string $hero_subtitle = '';
    public ?string $hero_cta_text = '';
    public ?string $hero_cta_url = '';
    public $hero_image;

    // About
    public ?string $about_title = '';
    public ?string $about_description = '';
    public ?string $about_stat_labs = '';
    public ?string $about_stat_labs_label = '';
    public ?string $about_stat_uptime = '';
    public ?string $about_stat_uptime_label = '';
    public ?string $about_stat_reports = '';
    public ?string $about_stat_reports_label = '';
    public $about_image;

    // Contact
    public ?string $contact_email = '';
    public ?string $contact_phone = '';
    public ?string $contact_address = '';
    public ?string $contact_whatsapp = '';

    // Social
    public ?string $social_twitter = '';
    public ?string $social_facebook = '';
    public ?string $social_linkedin = '';
    public ?string $social_instagram = '';

    // SEO
    public ?string $meta_title = '';
    public ?string $meta_description = '';

    public function mount()
    {
        $this->loadSettings();
    }

    protected function loadSettings()
    {
        $fields = [
            'site_name', 'site_tagline', 'primary_color', 'site_logo', 'site_logo_dark', 'site_logo_sm', 'site_favicon',
            'hero_title', 'hero_subtitle', 'hero_cta_text', 'hero_cta_url',
            'about_title', 'about_description', 'about_stat_labs', 'about_stat_labs_label',
            'about_stat_uptime', 'about_stat_uptime_label', 'about_stat_reports', 'about_stat_reports_label',
            'contact_email', 'contact_phone', 'contact_address', 'contact_whatsapp',
            'social_twitter', 'social_facebook', 'social_linkedin', 'social_instagram',
            'meta_title', 'meta_description',
        ];

        foreach ($fields as $field) {
            $this->$field = SiteSetting::get($field, '') ?? '';
        }
    }

    public function save()
    {
        // Save text fields
        $textFields = [
            'site_name' => 'branding', 'site_tagline' => 'branding', 'primary_color' => 'branding',
            'hero_title' => 'home', 'hero_subtitle' => 'home', 'hero_cta_text' => 'home', 'hero_cta_url' => 'home',
            'about_title' => 'about', 'about_description' => 'about',
            'about_stat_labs' => 'about', 'about_stat_labs_label' => 'about',
            'about_stat_uptime' => 'about', 'about_stat_uptime_label' => 'about',
            'about_stat_reports' => 'about', 'about_stat_reports_label' => 'about',
            'contact_email' => 'contact', 'contact_phone' => 'contact', 'contact_address' => 'contact', 'contact_whatsapp' => 'contact',
            'social_twitter' => 'social', 'social_facebook' => 'social', 'social_linkedin' => 'social', 'social_instagram' => 'social',
            'meta_title' => 'seo', 'meta_description' => 'seo',
        ];

        foreach ($textFields as $key => $group) {
            SiteSetting::set($key, $this->$key, $group);
        }

        // Handle file uploads
        $fileFields = ['site_logo', 'site_logo_dark', 'site_logo_sm', 'site_favicon', 'hero_image', 'about_image'];
        foreach ($fileFields as $field) {
            if ($this->$field && !is_string($this->$field)) {
                $path = $this->$field->store('site');
                SiteSetting::set($field, $path, $this->getGroupForField($field));
                $this->$field = null;
            }
        }

        session()->flash('success', 'Settings saved successfully!');
    }

    protected function getGroupForField(string $field): string
    {
        return match(true) {
            str_starts_with($field, 'site_') => 'branding',
            str_starts_with($field, 'hero_') => 'home',
            str_starts_with($field, 'about_') => 'about',
            default => 'general',
        };
    }

    public function render()
    {
        return view('livewire.admin.site-settings-manager')->layout('layouts.app');
    }
}
