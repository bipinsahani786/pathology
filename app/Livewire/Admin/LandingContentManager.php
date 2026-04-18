<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\LandingFeature;
use App\Models\LandingTestimonial;
use App\Models\LandingFaq;

class LandingContentManager extends Component
{
    public string $activeTab = 'features';

    // Feature form
    public ?int $editingFeatureId = null;
    public string $featureTitle = '';
    public string $featureDescription = '';
    public string $featureIcon = 'feather-zap';
    public string $featureColor = 'primary';
    public bool $showFeatureModal = false;

    // Testimonial form
    public ?int $editingTestimonialId = null;
    public string $testimonialName = '';
    public string $testimonialRole = '';
    public string $testimonialCompany = '';
    public string $testimonialQuote = '';
    public int $testimonialRating = 5;
    public bool $showTestimonialModal = false;

    // FAQ form
    public ?int $editingFaqId = null;
    public string $faqQuestion = '';
    public string $faqAnswer = '';
    public string $faqCategory = 'general';
    public bool $showFaqModal = false;

    // ── FEATURES ──
    public function createFeature()
    {
        $this->resetFeatureForm();
        $this->showFeatureModal = true;
    }

    public function editFeature($id)
    {
        $feature = LandingFeature::findOrFail($id);
        $this->editingFeatureId = $feature->id;
        $this->featureTitle = $feature->title;
        $this->featureDescription = $feature->description;
        $this->featureIcon = $feature->icon;
        $this->featureColor = $feature->color;
        $this->showFeatureModal = true;
    }

    public function saveFeature()
    {
        $this->validate([
            'featureTitle' => 'required|min:2',
            'featureDescription' => 'required|min:10',
            'featureIcon' => 'required',
        ]);

        $data = [
            'title' => $this->featureTitle,
            'description' => $this->featureDescription,
            'icon' => $this->featureIcon,
            'color' => $this->featureColor,
        ];

        if ($this->editingFeatureId) {
            LandingFeature::find($this->editingFeatureId)->update($data);
        } else {
            $data['sort_order'] = LandingFeature::max('sort_order') + 1;
            LandingFeature::create($data);
        }

        $this->showFeatureModal = false;
        $this->resetFeatureForm();
        session()->flash('success', 'Feature saved!');
    }

    public function toggleFeature($id)
    {
        $feature = LandingFeature::findOrFail($id);
        $feature->update(['is_active' => !$feature->is_active]);
    }

    public function deleteFeature($id)
    {
        LandingFeature::destroy($id);
        session()->flash('success', 'Feature deleted!');
    }

    protected function resetFeatureForm()
    {
        $this->editingFeatureId = null;
        $this->featureTitle = '';
        $this->featureDescription = '';
        $this->featureIcon = 'feather-zap';
        $this->featureColor = 'primary';
    }

    // ── TESTIMONIALS ──
    public function createTestimonial()
    {
        $this->resetTestimonialForm();
        $this->showTestimonialModal = true;
    }

    public function editTestimonial($id)
    {
        $t = LandingTestimonial::findOrFail($id);
        $this->editingTestimonialId = $t->id;
        $this->testimonialName = $t->author_name;
        $this->testimonialRole = $t->author_role ?? '';
        $this->testimonialCompany = $t->author_company ?? '';
        $this->testimonialQuote = $t->quote;
        $this->testimonialRating = $t->rating;
        $this->showTestimonialModal = true;
    }

    public function saveTestimonial()
    {
        $this->validate([
            'testimonialName' => 'required|min:2',
            'testimonialQuote' => 'required|min:10',
        ]);

        $data = [
            'author_name' => $this->testimonialName,
            'author_role' => $this->testimonialRole,
            'author_company' => $this->testimonialCompany,
            'quote' => $this->testimonialQuote,
            'rating' => $this->testimonialRating,
        ];

        if ($this->editingTestimonialId) {
            LandingTestimonial::find($this->editingTestimonialId)->update($data);
        } else {
            $data['sort_order'] = LandingTestimonial::max('sort_order') + 1;
            LandingTestimonial::create($data);
        }

        $this->showTestimonialModal = false;
        $this->resetTestimonialForm();
        session()->flash('success', 'Testimonial saved!');
    }

    public function deleteTestimonial($id)
    {
        LandingTestimonial::destroy($id);
        session()->flash('success', 'Testimonial deleted!');
    }

    protected function resetTestimonialForm()
    {
        $this->editingTestimonialId = null;
        $this->testimonialName = '';
        $this->testimonialRole = '';
        $this->testimonialCompany = '';
        $this->testimonialQuote = '';
        $this->testimonialRating = 5;
    }

    // ── FAQs ──
    public function createFaq()
    {
        $this->resetFaqForm();
        $this->showFaqModal = true;
    }

    public function editFaq($id)
    {
        $faq = LandingFaq::findOrFail($id);
        $this->editingFaqId = $faq->id;
        $this->faqQuestion = $faq->question;
        $this->faqAnswer = $faq->answer;
        $this->faqCategory = $faq->category ?? 'general';
        $this->showFaqModal = true;
    }

    public function saveFaq()
    {
        $this->validate([
            'faqQuestion' => 'required|min:5',
            'faqAnswer' => 'required|min:10',
        ]);

        $data = [
            'question' => $this->faqQuestion,
            'answer' => $this->faqAnswer,
            'category' => $this->faqCategory,
        ];

        if ($this->editingFaqId) {
            LandingFaq::find($this->editingFaqId)->update($data);
        } else {
            $data['sort_order'] = LandingFaq::max('sort_order') + 1;
            LandingFaq::create($data);
        }

        $this->showFaqModal = false;
        $this->resetFaqForm();
        session()->flash('success', 'FAQ saved!');
    }

    public function deleteFaq($id)
    {
        LandingFaq::destroy($id);
        session()->flash('success', 'FAQ deleted!');
    }

    protected function resetFaqForm()
    {
        $this->editingFaqId = null;
        $this->faqQuestion = '';
        $this->faqAnswer = '';
        $this->faqCategory = 'general';
    }

    public function render()
    {
        return view('livewire.admin.landing-content-manager', [
            'features' => LandingFeature::orderBy('sort_order')->get(),
            'testimonials' => LandingTestimonial::orderBy('sort_order')->get(),
            'faqs' => LandingFaq::orderBy('sort_order')->get(),
        ])->layout('layouts.app');
    }
}
