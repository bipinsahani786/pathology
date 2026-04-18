<?php

namespace App\Livewire\Admin;

use App\Models\Department;
use Livewire\Component;
use Livewire\WithPagination;

class DepartmentManager extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $name, $department_id;
    public $is_active = true;
    public $searchTerm = '';
    public $isModalOpen = false;

    public function render()
    {
        // System departments only for super admin
        $departments = Department::system()
            ->withCount('globalTests as globalTestsCount')
            ->where('name', 'like', '%' . $this->searchTerm . '%')
            ->orderBy('name', 'asc')
            ->paginate(10);

        return view('livewire.admin.department-manager', [
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
                'name' => $this->name,
                'is_active' => $this->is_active,
                'is_system' => true,
                'company_id' => null
            ]
        );

        session()->flash('success', $this->department_id ? 'System Department updated.' : 'System Department created.');
        $this->closeModal();
    }

    public function toggleStatus($id)
    {
        $department = Department::findOrFail($id);
        $department->update(['is_active' => !$department->is_active]);
    }
    
    public function delete($id)
    {
        Department::destroy($id);
        session()->flash('success', 'System Department deleted.');
    }
}
