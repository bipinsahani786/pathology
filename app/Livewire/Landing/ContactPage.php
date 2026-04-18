<?php

namespace App\Livewire\Landing;

use Livewire\Component;
use App\Models\Enquiry;

class ContactPage extends Component
{
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $message = '';
    public bool $submitted = false;

    protected $rules = [
        'name' => 'required|min:2|max:100',
        'email' => 'required|email|max:100',
        'phone' => 'nullable|max:20',
        'message' => 'required|min:10|max:2000',
    ];

    public function submit()
    {
        $this->validate();

        Enquiry::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'message' => $this->message,
            'enquiry_type' => 'contact',
            'status' => 'new',
        ]);

        $this->submitted = true;
        $this->reset(['name', 'email', 'phone', 'message']);
    }

    public function render()
    {
        return view('livewire.landing.contact-page')
            ->layout('components.landing-layout', ['title' => 'Contact Us - ' . \App\Models\SiteSetting::get('site_name', 'SWS Pathology')]);
    }
}
