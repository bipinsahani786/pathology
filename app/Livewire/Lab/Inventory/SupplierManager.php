<?php

namespace App\Livewire\Lab\Inventory;

use App\Models\InventorySupplier;
use Livewire\Component;
use Livewire\WithPagination;

class SupplierManager extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $name, $contact_person, $phone, $email, $address, $gst_number, $supplier_id;
    public $is_active = true;
    public $searchTerm = '';
    public $isModalOpen = false;

    public function render()
    {
        $suppliers = InventorySupplier::where('company_id', auth()->user()->company_id)
            ->where(function($query) {
                $query->where('name', 'ilike', '%' . $this->searchTerm . '%')
                      ->orWhere('contact_person', 'ilike', '%' . $this->searchTerm . '%');
            })
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.lab.inventory.supplier-manager', [
            'suppliers' => $suppliers
        ])->layout('layouts.app');
    }

    public function create()
    {
        $this->resetFields();
        $this->isModalOpen = true;
    }

    public function edit($id)
    {
        $supplier = InventorySupplier::where('company_id', auth()->user()->company_id)->findOrFail($id);
        
        $this->supplier_id = $id;
        $this->name = $supplier->name;
        $this->contact_person = $supplier->contact_person;
        $this->phone = $supplier->phone;
        $this->email = $supplier->email;
        $this->address = $supplier->address;
        $this->gst_number = $supplier->gst_number;
        $this->is_active = $supplier->is_active;
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetFields();
    }

    public function resetFields()
    {
        $this->reset(['name', 'contact_person', 'phone', 'email', 'address', 'gst_number', 'supplier_id', 'is_active']);
        $this->resetValidation();
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        InventorySupplier::updateOrCreate(
            ['id' => $this->supplier_id],
            [
                'company_id' => auth()->user()->company_id,
                'name' => $this->name,
                'contact_person' => $this->contact_person,
                'phone' => $this->phone,
                'email' => $this->email,
                'address' => $this->address,
                'gst_number' => $this->gst_number,
                'is_active' => $this->is_active,
            ]
        );

        session()->flash('success', $this->supplier_id ? 'Supplier updated.' : 'New supplier created.');
        $this->closeModal();
    }

    public function toggleStatus($id)
    {
        $supplier = InventorySupplier::where('company_id', auth()->user()->company_id)->findOrFail($id);
        $supplier->update(['is_active' => !$supplier->is_active]);
    }
}
