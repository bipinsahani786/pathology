<?php

namespace App\Livewire\Lab;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CollectionCenter;

class CollectionCenterManager extends Component
{
    use WithPagination;
    
    protected $paginationTheme = 'bootstrap';

    // State variables
    public $searchTerm = '';
    public $center_id = null; // Explicitly null to prevent DB errors
    public $name;
    public $center_code;
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
     * Open modal to create a new center
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
        $center = CollectionCenter::findOrFail($id);
        
        $this->center_id = $center->id;
        $this->name = $center->name;
        $this->center_code = $center->center_code;
        $this->address = $center->address;
        $this->is_active = $center->is_active;

        $this->isModalOpen = true;
    }

    /**
     * Validate and save the data to the database
     */
    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'center_code' => 'nullable|string|max:50',
            'address' => 'nullable|string',
        ]);

        if ($this->center_id) {
            // Update existing record
            CollectionCenter::where('id', $this->center_id)->update([
                'name' => $this->name,
                'center_code' => $this->center_code,
                'address' => $this->address,
                'is_active' => $this->is_active,
            ]);
            session()->flash('message', 'Collection Center updated successfully.');
        } else {
            // Create new record
            CollectionCenter::create([
                'company_id' => auth()->user()->company_id,
                'name' => $this->name,
                'center_code' => $this->center_code,
                'address' => $this->address,
                'is_active' => $this->is_active,
                // 'is_main_lab' is ignored here since we use Branches for labs now
            ]);
            session()->flash('message', 'Collection Center created successfully.');
        }

        $this->closeModal();
    }

    /**
     * Quick toggle for active/inactive status
     */
    public function toggleStatus($id)
    {
        $center = CollectionCenter::findOrFail($id);
        $center->update(['is_active' => !$center->is_active]);
        
        session()->flash('message', 'Status updated successfully.');
    }

    /**
     * Delete a center
     */
    public function delete($id)
    {
        CollectionCenter::findOrFail($id)->delete();
        session()->flash('message', 'Collection Center deleted successfully.');
    }

    /**
     * Reset form fields
     */
    public function resetFields()
    {
        $this->reset(['center_id', 'name', 'center_code', 'address']);
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
        $centers = CollectionCenter::where('company_id', auth()->user()->company_id)
            ->where(function($q) {
                $q->where('name', 'ilike', '%' . $this->searchTerm . '%')
                  ->orWhere('center_code', 'ilike', '%' . $this->searchTerm . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.lab.collection-center-manager', [
            'centers' => $centers
        ])->layout('layouts.app', ['title' => 'Manage Collection Centers']);
    }
}