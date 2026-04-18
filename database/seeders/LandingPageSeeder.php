<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use App\Models\LandingFeature;
use App\Models\LandingTestimonial;
use App\Models\LandingFaq;
use Illuminate\Database\Seeder;

class LandingPageSeeder extends Seeder
{
    public function run(): void
    {
        // ── Site Settings ──
        $settings = [
            // Branding
            ['setting_key' => 'site_name', 'setting_value' => 'SWS Pathology', 'setting_group' => 'branding'],
            ['setting_key' => 'site_tagline', 'setting_value' => 'Precision Diagnostics & Lab Intelligence', 'setting_group' => 'branding'],
            ['setting_key' => 'site_logo', 'setting_value' => null, 'setting_group' => 'branding'],
            ['setting_key' => 'site_logo_dark', 'setting_value' => null, 'setting_group' => 'branding'],
            ['setting_key' => 'site_favicon', 'setting_value' => null, 'setting_group' => 'branding'],
            ['setting_key' => 'primary_color', 'setting_value' => '#0284c7', 'setting_group' => 'branding'],

            // Home
            ['setting_key' => 'hero_title', 'setting_value' => 'Intelligence for Modern Laboratories', 'setting_group' => 'home'],
            ['setting_key' => 'hero_subtitle', 'setting_value' => 'Streamline your entire diagnostic workflow from sample collection to automated reporting with our secure, enterprise-grade cloud ecosystem.', 'setting_group' => 'home'],
            ['setting_key' => 'hero_image', 'setting_value' => null, 'setting_group' => 'home'],
            ['setting_key' => 'hero_cta_text', 'setting_value' => 'Start Free Trial', 'setting_group' => 'home'],
            ['setting_key' => 'hero_cta_url', 'setting_value' => '/register', 'setting_group' => 'home'],

            // About
            ['setting_key' => 'about_title', 'setting_value' => 'Precision in Every Diagnostic Pulse', 'setting_group' => 'about'],
            ['setting_key' => 'about_description', 'setting_value' => 'SWS Pathology emerged from a collaboration between veteran pathologists and software engineers who were tired of legacy systems slowing down critical medical decisions. Our goal was to create a zero-friction, automated LIS that works as fast as a diagnostic team thinks.', 'setting_group' => 'about'],
            ['setting_key' => 'about_image', 'setting_value' => null, 'setting_group' => 'about'],
            ['setting_key' => 'about_stat_labs', 'setting_value' => '500+', 'setting_group' => 'about'],
            ['setting_key' => 'about_stat_labs_label', 'setting_value' => 'Labs Integrated', 'setting_group' => 'about'],
            ['setting_key' => 'about_stat_uptime', 'setting_value' => '99.9%', 'setting_group' => 'about'],
            ['setting_key' => 'about_stat_uptime_label', 'setting_value' => 'Uptime SLA', 'setting_group' => 'about'],
            ['setting_key' => 'about_stat_reports', 'setting_value' => '1M+', 'setting_group' => 'about'],
            ['setting_key' => 'about_stat_reports_label', 'setting_value' => 'Reports Monthly', 'setting_group' => 'about'],

            // Contact
            ['setting_key' => 'contact_email', 'setting_value' => 'support@swspathology.com', 'setting_group' => 'contact'],
            ['setting_key' => 'contact_phone', 'setting_value' => '+91 98765 43210', 'setting_group' => 'contact'],
            ['setting_key' => 'contact_address', 'setting_value' => 'New Delhi, India', 'setting_group' => 'contact'],
            ['setting_key' => 'contact_whatsapp', 'setting_value' => '+91 98765 43210', 'setting_group' => 'contact'],

            // Social
            ['setting_key' => 'social_twitter', 'setting_value' => '#', 'setting_group' => 'social'],
            ['setting_key' => 'social_facebook', 'setting_value' => '#', 'setting_group' => 'social'],
            ['setting_key' => 'social_linkedin', 'setting_value' => '#', 'setting_group' => 'social'],
            ['setting_key' => 'social_instagram', 'setting_value' => '#', 'setting_group' => 'social'],

            // SEO
            ['setting_key' => 'meta_title', 'setting_value' => 'SWS Pathology - Advanced Diagnostic Solutions', 'setting_group' => 'seo'],
            ['setting_key' => 'meta_description', 'setting_value' => 'Leading pathology management SaaS platform for modern laboratories. Automate reporting, billing, and partner management.', 'setting_group' => 'seo'],
        ];

        foreach ($settings as $setting) {
            SiteSetting::updateOrCreate(
                ['setting_key' => $setting['setting_key']],
                $setting
            );
        }

        // ── Landing Features ──
        $features = [
            ['title' => 'Smart POS Billing', 'description' => 'Lightning-fast point-of-sale system with test search, auto-discount application, membership pricing, and instant invoice generation.', 'icon' => 'feather-zap', 'color' => 'primary', 'sort_order' => 1],
            ['title' => 'Automated Reporting', 'description' => 'Generate professional diagnostic reports with QR verification, digital signatures, dynamic templates, and one-click PDF delivery.', 'icon' => 'feather-file-text', 'color' => 'success', 'sort_order' => 2],
            ['title' => 'Patient Management', 'description' => 'Complete patient lifecycle management with history tracking, demographic records, and automated SMS/WhatsApp notifications.', 'icon' => 'feather-users', 'color' => 'info', 'sort_order' => 3],
            ['title' => 'Partner Portal', 'description' => 'Dedicated dashboards for referring doctors, agents, and collection centers with real-time referral tracking and commission management.', 'icon' => 'feather-briefcase', 'color' => 'warning', 'sort_order' => 4],
            ['title' => 'Multi-Branch Sync', 'description' => 'Seamlessly manage multiple lab locations and collection centers from a unified cloud dashboard with branch-specific pricing.', 'icon' => 'feather-git-branch', 'color' => 'danger', 'sort_order' => 5],
            ['title' => 'Financial Analytics', 'description' => 'Deep revenue insights, settlement automation, partner commission tracking, and comprehensive business intelligence dashboards.', 'icon' => 'feather-trending-up', 'color' => 'purple', 'sort_order' => 6],
            ['title' => 'Test Packages', 'description' => 'Create bundled health check-up packages with custom pricing, validity periods, and promotional offers for patient retention.', 'icon' => 'feather-package', 'color' => 'teal', 'sort_order' => 7],
            ['title' => 'WhatsApp Integration', 'description' => 'Instant report delivery via WhatsApp and SMS with secure download links. Patients receive reports the moment they are approved.', 'icon' => 'feather-message-circle', 'color' => 'success', 'sort_order' => 8],
            ['title' => 'Bank-Grade Security', 'description' => 'AES-256 encryption, role-based access control, audit trails, and HIPAA-compliant data handling for complete peace of mind.', 'icon' => 'feather-shield', 'color' => 'dark', 'sort_order' => 9],
        ];

        foreach ($features as $feature) {
            LandingFeature::updateOrCreate(['title' => $feature['title']], $feature);
        }

        // ── Testimonials ──
        $testimonials = [
            ['author_name' => 'Dr. Rajesh Khanna', 'author_role' => 'Chief Pathologist', 'author_company' => 'KH Labs', 'quote' => 'Since switching to SWS, our turnaround time dropped by 40%. The automated reporting is a lifesaver for our high-volume center.', 'rating' => 5, 'sort_order' => 1],
            ['author_name' => 'Anu Mishra', 'author_role' => 'Director', 'author_company' => 'Apex Diagnostics', 'quote' => 'The partner portal changed how we work with agents. Transparency in billing is incredible now. Our referral network has grown 3x.', 'rating' => 5, 'sort_order' => 2],
            ['author_name' => 'Dr. Priya Sharma', 'author_role' => 'Lab Administrator', 'author_company' => 'LifeCare Diagnostics', 'quote' => 'Managing 5 collection centers was a nightmare before SWS. Now everything syncs automatically and our settlement process takes minutes, not days.', 'rating' => 5, 'sort_order' => 3],
        ];

        foreach ($testimonials as $testimonial) {
            LandingTestimonial::updateOrCreate(['author_name' => $testimonial['author_name']], $testimonial);
        }

        // ── FAQs ──
        $faqs = [
            ['question' => 'Can I use my existing laboratory equipment?', 'answer' => 'Yes, our platform is equipment-agnostic and supports integration with 200+ diagnostic analyzers from major manufacturers including Siemens, Roche, Abbott, and more.', 'category' => 'general', 'sort_order' => 1],
            ['question' => 'Is there a mobile app for sample collectors?', 'answer' => 'Yes, field collectors can use our mobile-optimized web app to register patients, track sample pickups, and manage logistics on the go.', 'category' => 'general', 'sort_order' => 2],
            ['question' => 'Where is my data stored?', 'answer' => 'Your data is stored in regionally compliant cloud servers with end-to-end encryption. We support AWS and Azure deployments with 99.9% uptime SLA.', 'category' => 'general', 'sort_order' => 3],
            ['question' => 'How long does setup take?', 'answer' => 'Most labs are fully operational within 24-48 hours. Our concierge onboarding team handles data migration, test catalog setup, and staff training.', 'category' => 'general', 'sort_order' => 4],
            ['question' => 'Can I manage multiple branches?', 'answer' => 'Absolutely. Our multi-branch architecture supports unlimited locations, collection centers, and branch-specific pricing from a single unified dashboard.', 'category' => 'general', 'sort_order' => 5],
            ['question' => 'Do you offer a free trial?', 'answer' => 'Yes, we offer a generous free trial period with full access to all features. No credit card required. Start your journey today.', 'category' => 'pricing', 'sort_order' => 6],
            ['question' => 'Can I upgrade or downgrade my plan anytime?', 'answer' => 'Yes, plan changes take effect immediately. If you upgrade, you only pay the prorated difference. Downgrades apply at the next billing cycle.', 'category' => 'pricing', 'sort_order' => 7],
            ['question' => 'Is the partner portal included in all plans?', 'answer' => 'The Partner Portal is available in Professional and Enterprise plans. It includes dedicated dashboards for referring doctors, agents, and collection centers.', 'category' => 'pricing', 'sort_order' => 8],
        ];

        foreach ($faqs as $faq) {
            LandingFaq::updateOrCreate(['question' => $faq['question']], $faq);
        }
    }
}
