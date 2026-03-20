<?php

namespace App\Livewire\Lab;

use App\Models\Department;
use Livewire\Component;
use Livewire\WithPagination;

class DepartmentManager extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        $this->authorize('manage lab_tests');
    }

    public $name, $department_id;
    public $is_active = true;
    public $searchTerm = '';
    public $isModalOpen = false;

    public function render()
    {
        // Fetch both system departments and lab-specific departments
        $departments = Department::forCompany(auth()->user()->company_id)
            ->where('name', 'ilike', '%' . $this->searchTerm . '%')
            ->orderBy('is_system', 'desc')
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.lab.department-manager', [
            'departments' => $departments
        ])->layout('layouts.app');
    }

    public function create()
    {
        $this->resetFields();
        $this->isModalOpen = true;
    }

    public function edit($id)
    {
        $department = Department::findOrFail($id);
        
        // Prevent editing system departments
        if ($department->is_system) {
            session()->flash('error', 'System departments cannot be modified.');
            return;
        }

        $this->department_id = $id;
        $this->name = $department->name;
        $this->is_active = $department->is_active;
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetFields();
    }

    public function resetFields()
    {
        $this->reset(['name', 'department_id', 'is_active']);
        $this->resetValidation();
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:255',
        ]);

        Department::updateOrCreate(
            ['id' => $this->department_id],
            [
                'company_id' => auth()->user()->company_id,
                'name' => $this->name,
                'is_active' => $this->is_active,
                'is_system' => false
            ]
        );

        session()->flash('success', $this->department_id ? 'Department updated.' : 'New department created.');
        $this->closeModal();
    }

    public function toggleStatus($id)
    {
        $department = Department::findOrFail($id);
        if ($department->is_system) return; // Protect system depts
        
        $department->update(['is_active' => !$department->is_active]);
    }

    public function delete($id)
    {
        $department = Department::findOrFail($id);
        if ($department->is_system) {
            session()->flash('error', 'System departments cannot be deleted.');
            return;
        }
        
        $department->delete();
        session()->flash('success', 'Department deleted.');
    }
}
