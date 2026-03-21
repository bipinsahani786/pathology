<?php

namespace App\Livewire\Admin;

use App\Models\Company;
use Livewire\Component;
use Livewire\WithPagination;

class LabManager extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $searchTerm = '';

    public function render()
    {
        $labs = Company::where('name', 'ilike', '%' . $this->searchTerm . '%')
            ->orWhere('email', 'ilike', '%' . $this->searchTerm . '%')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.lab-manager', [
            'labs' => $labs
        ])->layout('layouts.app');
    }

    public function toggleStatus($id)
    {
        $lab = Company::findOrFail($id);
        $lab->update(['is_active' => !$lab->is_active]);
        session()->flash('success', 'Lab status updated successfully.');
    }
}
