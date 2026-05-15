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
    
    // Home Extra Text
    public ?string $home_stats_title = '';
    public ?string $home_friction_title = '';
    public ?string $home_friction_desc = '';
    public ?string $home_features_title = '';
    public ?string $home_features_desc = '';
    public ?string $home_steps_title = '';
    public ?string $home_connect_title = '';
    public ?string $home_testimonial_title = '';
    public ?string $home_pricing_title = '';
    public ?string $home_pricing_desc = '';
    public ?string $home_faq_title = '';
    public ?string $home_contact_title = '';
    public ?string $home_contact_desc = '';
    public ?string $home_cta_title = '';
    public ?string $home_cta_desc = '';
    public ?string $home_stats_logos = '';
    public ?string $home_steps_list = '';
    public ?string $home_connect_list = '';

    public ?string $home_hero_stat_1_val = '';
    public ?string $home_hero_stat_1_label = '';
    public ?string $home_hero_stat_2_val = '';
    public ?string $home_hero_stat_2_label = '';
    public ?string $home_hero_stat_3_val = '';
    public ?string $home_hero_stat_3_label = '';
    public ?string $home_hero_stat_4_val = '';
    public ?string $home_hero_stat_4_label = '';

    public ?string $home_friction_old_1 = '';
    public ?string $home_friction_new_1 = '';
    public ?string $home_friction_old_2 = '';
    public ?string $home_friction_new_2 = '';
    public ?string $home_friction_old_3 = '';
    public ?string $home_friction_new_3 = '';

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

    // About Extra Text
    public ?string $about_story_title = '';
    public ?string $about_story_desc = '';
    public ?string $about_heritage_subtitle = '';
    public ?string $about_heritage_title = '';
    public ?string $about_values_subtitle = '';
    public ?string $about_values_title = '';
    public ?string $about_roadmap_title = '';
    public ?string $about_cta_title = '';
    public ?string $about_cta_desc = '';

    public ?string $about_milestone_date_1 = '';
    public ?string $about_milestone_title_1 = '';
    public ?string $about_milestone_desc_1 = '';
    public ?string $about_milestone_date_2 = '';
    public ?string $about_milestone_title_2 = '';
    public ?string $about_milestone_desc_2 = '';
    public ?string $about_milestone_date_3 = '';
    public ?string $about_milestone_title_3 = '';
    public ?string $about_milestone_desc_3 = '';
    public ?string $about_milestone_date_4 = '';
    public ?string $about_milestone_title_4 = '';
    public ?string $about_milestone_desc_4 = '';

    // Features
    public ?string $features_hero_title = '';
    public ?string $features_hero_desc = '';

    // How It Works
    public ?string $how_hero_title = '';
    public ?string $how_hero_desc = '';

    // Pricing
    public ?string $pricing_hero_subtitle = '';
    public ?string $pricing_hero_title = '';
    public ?string $pricing_hero_desc = '';
    public ?string $pricing_faq_title = '';
    public ?string $pricing_cta_title = '';
    public ?string $pricing_cta_desc = '';

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
    public array $seoSettings = [];
    public array $seoImages = []; // Temporarily hold uploaded images
    public array $pages = [
        'home' => 'Home Page',
        'about' => 'About Us',
        'features' => 'Features',
        'how-it-works' => 'How It Works',
        'pricing' => 'Pricing',
        'contact' => 'Contact Us',
        'enquiry' => 'Enquiry',
        'faq' => 'FAQ',
        'terms' => 'Terms of Service',
        'privacy' => 'Privacy Policy'
    ];

    public function mount()
    {
        $this->loadSettings();
    }

    protected function loadSettings()
    {
        $fields = [
            'site_name', 'site_tagline', 'primary_color', 'site_logo', 'site_logo_dark', 'site_logo_sm', 'site_favicon',
            'hero_title', 'hero_subtitle', 'hero_cta_text', 'hero_cta_url',
            'home_stats_title', 'home_friction_title', 'home_friction_desc', 'home_features_title', 'home_features_desc',
            'home_steps_title', 'home_connect_title', 'home_testimonial_title', 'home_pricing_title', 'home_pricing_desc',
            'home_faq_title', 'home_contact_title', 'home_contact_desc', 'home_cta_title', 'home_cta_desc',
            'home_stats_logos', 'home_steps_list', 'home_connect_list',
            'home_hero_stat_1_val', 'home_hero_stat_1_label', 'home_hero_stat_2_val', 'home_hero_stat_2_label',
            'home_hero_stat_3_val', 'home_hero_stat_3_label', 'home_hero_stat_4_val', 'home_hero_stat_4_label',
            'home_friction_old_1', 'home_friction_new_1', 'home_friction_old_2', 'home_friction_new_2', 'home_friction_old_3', 'home_friction_new_3',
            'about_title', 'about_description', 'about_stat_labs', 'about_stat_labs_label',
            'about_stat_uptime', 'about_stat_uptime_label', 'about_stat_reports', 'about_stat_reports_label',
            'about_story_title', 'about_story_desc', 'about_heritage_subtitle', 'about_heritage_title',
            'about_values_subtitle', 'about_values_title', 'about_roadmap_title', 'about_cta_title', 'about_cta_desc',
            'about_milestone_date_1', 'about_milestone_title_1', 'about_milestone_desc_1',
            'about_milestone_date_2', 'about_milestone_title_2', 'about_milestone_desc_2',
            'about_milestone_date_3', 'about_milestone_title_3', 'about_milestone_desc_3',
            'about_milestone_date_4', 'about_milestone_title_4', 'about_milestone_desc_4',
            'features_hero_title', 'features_hero_desc',
            'how_hero_title', 'how_hero_desc',
            'pricing_hero_subtitle', 'pricing_hero_title', 'pricing_hero_desc', 'pricing_faq_title', 'pricing_cta_title', 'pricing_cta_desc',
            'contact_email', 'contact_phone', 'contact_address', 'contact_whatsapp',
            'social_twitter', 'social_facebook', 'social_linkedin', 'social_instagram',
        ];

        foreach ($fields as $field) {
            $this->$field = SiteSetting::get($field, '') ?? '';
        }

        // Load SEO Settings
        foreach ($this->pages as $pageKey => $pageName) {
            $this->seoSettings[$pageKey] = [
                'title' => SiteSetting::get("seo_{$pageKey}_title", ''),
                'description' => SiteSetting::get("seo_{$pageKey}_description", ''),
                'keywords' => SiteSetting::get("seo_{$pageKey}_keywords", ''),
                'canonical' => SiteSetting::get("seo_{$pageKey}_canonical", ''),
                'og_image' => SiteSetting::get("seo_{$pageKey}_og_image", ''),
            ];
        }
    }

    public function save()
    {
        // Save text fields
        $textFields = [
            'site_name' => 'branding', 'site_tagline' => 'branding', 'primary_color' => 'branding',
            'hero_title' => 'home', 'hero_subtitle' => 'home', 'hero_cta_text' => 'home', 'hero_cta_url' => 'home',
            'home_stats_title' => 'home', 'home_friction_title' => 'home', 'home_friction_desc' => 'home', 'home_features_title' => 'home', 'home_features_desc' => 'home',
            'home_steps_title' => 'home', 'home_connect_title' => 'home', 'home_testimonial_title' => 'home', 'home_pricing_title' => 'home', 'home_pricing_desc' => 'home',
            'home_faq_title' => 'home', 'home_contact_title' => 'home', 'home_contact_desc' => 'home', 'home_cta_title' => 'home', 'home_cta_desc' => 'home',
            'home_stats_logos' => 'home', 'home_steps_list' => 'home', 'home_connect_list' => 'home',
            'home_hero_stat_1_val' => 'home', 'home_hero_stat_1_label' => 'home', 'home_hero_stat_2_val' => 'home', 'home_hero_stat_2_label' => 'home',
            'home_hero_stat_3_val' => 'home', 'home_hero_stat_3_label' => 'home', 'home_hero_stat_4_val' => 'home', 'home_hero_stat_4_label' => 'home',
            'home_friction_old_1' => 'home', 'home_friction_new_1' => 'home', 'home_friction_old_2' => 'home', 'home_friction_new_2' => 'home', 'home_friction_old_3' => 'home', 'home_friction_new_3' => 'home',
            'about_title' => 'about', 'about_description' => 'about',
            'about_stat_labs' => 'about', 'about_stat_labs_label' => 'about',
            'about_stat_uptime' => 'about', 'about_stat_uptime_label' => 'about',
            'about_stat_reports' => 'about', 'about_stat_reports_label' => 'about',
            'about_story_title' => 'about', 'about_story_desc' => 'about', 'about_heritage_subtitle' => 'about', 'about_heritage_title' => 'about',
            'about_values_subtitle' => 'about', 'about_values_title' => 'about', 'about_roadmap_title' => 'about', 'about_cta_title' => 'about', 'about_cta_desc' => 'about',
            'about_milestone_date_1' => 'about', 'about_milestone_title_1' => 'about', 'about_milestone_desc_1' => 'about',
            'about_milestone_date_2' => 'about', 'about_milestone_title_2' => 'about', 'about_milestone_desc_2' => 'about',
            'about_milestone_date_3' => 'about', 'about_milestone_title_3' => 'about', 'about_milestone_desc_3' => 'about',
            'about_milestone_date_4' => 'about', 'about_milestone_title_4' => 'about', 'about_milestone_desc_4' => 'about',
            'features_hero_title' => 'features', 'features_hero_desc' => 'features',
            'how_hero_title' => 'how', 'how_hero_desc' => 'how',
            'pricing_hero_subtitle' => 'pricing', 'pricing_hero_title' => 'pricing', 'pricing_hero_desc' => 'pricing', 'pricing_faq_title' => 'pricing', 'pricing_cta_title' => 'pricing', 'pricing_cta_desc' => 'pricing',
            'contact_email' => 'contact', 'contact_phone' => 'contact', 'contact_address' => 'contact', 'contact_whatsapp' => 'contact',
            'social_twitter' => 'social', 'social_facebook' => 'social', 'social_linkedin' => 'social', 'social_instagram' => 'social',
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

        // Save SEO Settings
        foreach ($this->seoSettings as $pageKey => $seoData) {
            SiteSetting::set("seo_{$pageKey}_title", $seoData['title'] ?? '', 'seo');
            SiteSetting::set("seo_{$pageKey}_description", $seoData['description'] ?? '', 'seo');
            SiteSetting::set("seo_{$pageKey}_keywords", $seoData['keywords'] ?? '', 'seo');
            SiteSetting::set("seo_{$pageKey}_canonical", $seoData['canonical'] ?? '', 'seo');
        }

        // Save SEO Images
        if (!empty($this->seoImages)) {
            foreach ($this->seoImages as $pageKey => $image) {
                if ($image && !is_string($image)) {
                    $path = $image->store('site');
                    SiteSetting::set("seo_{$pageKey}_og_image", $path, 'seo');
                    $this->seoSettings[$pageKey]['og_image'] = $path; // Update local array to reflect new image immediately
                }
            }
            $this->seoImages = [];
        }

        session()->flash('success', 'Settings saved successfully!');
    }

    protected function getGroupForField(string $field): string
    {
        return match(true) {
            str_starts_with($field, 'site_') => 'branding',
            str_starts_with($field, 'hero_') => 'home',
            str_starts_with($field, 'home_') => 'home',
            str_starts_with($field, 'about_') => 'about',
            str_starts_with($field, 'features_') => 'features',
            str_starts_with($field, 'how_') => 'how',
            str_starts_with($field, 'pricing_') => 'pricing',
            default => 'general',
        };
    }

    public function render()
    {
        return view('livewire.admin.site-settings-manager')->layout('layouts.app');
    }
}
