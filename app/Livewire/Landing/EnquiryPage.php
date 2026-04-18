<?php

namespace App\Livewire\Landing;

use Livewire\Component;
use App\Models\Enquiry;

class EnquiryPage extends Component
{
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $lab_name = '';
    public string $lab_city = '';
    public string $tests_per_month = '';
    public string $branches = '';
    public string $message = '';
    public bool $submitted = false;

    protected $rules = [
        'name' => 'required|min:2|max:100',
        'email' => 'required|email|max:100',
        'phone' => 'required|max:20',
        'lab_name' => 'required|min:2|max:200',
        'lab_city' => 'required|max:100',
        'tests_per_month' => 'nullable|max:50',
        'branches' => 'nullable|max:50',
        'message' => 'nullable|max:2000',
    ];

    public function submit()
    {
        $this->validate();

        Enquiry::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'lab_name' => $this->lab_name,
            'lab_city' => $this->lab_city,
            'message' => "Tests/month: {$this->tests_per_month}\nBranches: {$this->branches}\n\n{$this->message}",
            'enquiry_type' => 'enquiry',
            'status' => 'new',
        ]);

        $this->submitted = true;
        $this->reset(['name', 'email', 'phone', 'lab_name', 'lab_city', 'tests_per_month', 'branches', 'message']);
    }

    public function render()
    {
        return view('livewire.landing.enquiry-page')
            ->layout('components.landing-layout', ['title' => 'Request Demo - ' . \App\Models\SiteSetting::get('site_name', 'SWS Pathology')]);
    }
}
