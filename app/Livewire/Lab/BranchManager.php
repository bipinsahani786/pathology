<?php

namespace App\Livewire\Lab;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Branch;

class BranchManager extends Component
{
    use WithPagination;
    
    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        $this->authorize('manage branches');
    }

    // State variables
    public $searchTerm = '';
    public $branch_id = null; // Explicitly null to prevent PostgreSQL errors
    public $name;
    public $type = 'main_lab';
    public $contact_number;
    public $address;
    public $is_active = true;
    public $isModalOpen = false;

    /**
     * Reset pagination when searching
     */
    public function updatingSearchTerm()
    {
        $this->resetPage();
    }

    /**
     * Open modal to create a new branch
     */
    public function create()
    {
        $this->resetFields();
        $this->isModalOpen = true;
    }

    /**
     * Load existing data and open modal for editing
     */
    public function edit($id)
    {
        $this->resetFields();
        $branch = Branch::findOrFail($id);
        
        $this->branch_id = $branch->id;
        $this->name = $branch->name;
        $this->type = $branch->type;
        $this->contact_number = $branch->contact_number;
        $this->address = $branch->address;
        $this->is_active = $branch->is_active;

        $this->isModalOpen = true;
    }

    /**
     * Validate and save the data to the database
     */
    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:30',
            'contact_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        // Explicitly separate Create and Update to prevent PostgreSQL 'null id' error
        if ($this->branch_id) {
            Branch::where('id', $this->branch_id)->update([
                'name' => $this->name,
                'type' => $this->type,
                'contact_number' => $this->contact_number,
                'address' => $this->address,
                'is_active' => $this->is_active,
            ]);
            session()->flash('message', 'Branch updated successfully.');
        } else {
            Branch::create([
                'company_id' => auth()->user()->company_id,
                'name' => $this->name,
                'type' => $this->type,
                'contact_number' => $this->contact_number,
                'address' => $this->address,
                'is_active' => $this->is_active,
            ]);
            session()->flash('message', 'Branch created successfully.');
        }

        $this->closeModal();
    }

    /**
     * Quick toggle for active/inactive status
     */
    public function toggleStatus($id)
    {
        $branch = Branch::findOrFail($id);
        $branch->update(['is_active' => !$branch->is_active]);
        
        session()->flash('message', 'Branch status updated successfully.');
    }

    /**
     * Delete a branch
     */
    public function delete($id)
    {
        Branch::findOrFail($id)->delete();
        session()->flash('message', 'Branch deleted successfully.');
    }

    /**
     * Reset form fields
     */
    public function resetFields()
    {
        $this->reset(['branch_id', 'name', 'contact_number', 'address']);
        $this->type = 'main_lab';
        $this->is_active = true;
        $this->resetValidation();
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetFields();
    }

    public function render()
    {
        $branches = Branch::where('company_id', auth()->user()->company_id)
            ->where(function($q) {
                $q->where('name', 'ilike', '%' . $this->searchTerm . '%')
                  ->orWhere('contact_number', 'ilike', '%' . $this->searchTerm . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.lab.branch-manager', [
            'branches' => $branches
        ])->layout('layouts.app', ['title' => 'Manage Branches']);
    }
}