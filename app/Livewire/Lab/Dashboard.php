<?php

namespace App\Livewire\Lab;

use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        
        return view('livewire.lab.dashboard')
               ->layout('layouts.app', ['title' => 'Lab Dashboard - Pathology SaaS']); 
    }
}