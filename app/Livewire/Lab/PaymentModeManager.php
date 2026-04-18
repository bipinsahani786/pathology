<?php

namespace App\Livewire\Lab;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PaymentMode;

class PaymentModeManager extends Component
{
    use WithPagination;
    
    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        $this->authorize('view payment_modes');
    }

    // State variables
    public $searchTerm = '';
    public $mode_id = null; // Ensure this is null by default
    public $name;
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
     * Open modal to create a new payment mode
     */
    public function create()
    {
        $this->authorize('create payment_modes');
        $this->resetFields();
        $this->isModalOpen = true;
    }

    /**
     * Load existing data and open modal for editing
     */
    public function edit($id)
    {
        $this->authorize('edit payment_modes');
        $this->resetFields();
        $mode = PaymentMode::findOrFail($id);
        
        $this->mode_id = $mode->id;
        $this->name = $mode->name;
        $this->is_active = $mode->is_active;

        $this->isModalOpen = true;
    }

    /**
     * Validate and save the data to the database
     */
    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:255',
        ]);

        // FIX: Explicitly separate Create and Update to prevent PostgreSQL 'null id' error
        if ($this->mode_id) {
            $this->authorize('edit payment_modes');
            // Update existing record
            PaymentMode::where('id', $this->mode_id)->update([
                'name' => $this->name,
                'is_active' => $this->is_active,
            ]);
            session()->flash('message', 'Payment Mode updated successfully.');
        } else {
            $this->authorize('create payment_modes');
            // Create new record
            PaymentMode::create([
                'company_id' => auth()->user()->company_id, // Link to current lab
                'name' => $this->name,
                'is_active' => $this->is_active,
            ]);
            session()->flash('message', 'Payment Mode created successfully.');
        }

        $this->closeModal();
    }

    /**
     * Quick toggle for active/inactive status directly from the table
     */
    public function toggleStatus($id)
    {
        $this->authorize('edit payment_modes');
        $mode = PaymentMode::findOrFail($id);
        $mode->update(['is_active' => !$mode->is_active]);
        
        session()->flash('message', 'Status updated successfully.');
    }

    /**
     * Delete a payment mode
     */
    public function delete($id)
    {
        $this->authorize('delete payment_modes');
        PaymentMode::findOrFail($id)->delete();
        session()->flash('message', 'Payment Mode deleted successfully.');
    }

    /**
     * Reset form fields and validation errors
     */
    public function resetFields()
    {
        $this->reset(['mode_id', 'name']);
        $this->is_active = true;
        $this->resetValidation();
    }

    /**
     * Close the modal
     */
    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetFields();
    }

    public function render()
    {
        $paymentModes = PaymentMode::where('company_id', auth()->user()->company_id)
            ->where('name', 'like', '%' . $this->searchTerm . '%')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.lab.payment-mode-manager', [
            'paymentModes' => $paymentModes
        ])->layout('layouts.app', ['title' => 'Manage Payment Modes']);
    }
}