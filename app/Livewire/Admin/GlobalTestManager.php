<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Services\GlobalTestService;
use App\Models\Department;

class GlobalTestManager extends Component
{
    use WithPagination;
    
    protected $paginationTheme = 'bootstrap';

    // Search & Filter Properties
    public $searchTerm = '';
    public $filterDepartment = '';

    public function updatingSearchTerm()
    {
        $this->resetPage();
    }

    public function updatingFilterDepartment()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        $testService = new GlobalTestService();
        $testService->deleteTest($id);
        session()->flash('message', 'Test Deleted Successfully.');
    }

    public function render()
    {
        $testService = new GlobalTestService();
        $tests = $testService->getPaginatedTests(10, $this->searchTerm, $this->filterDepartment);
        $departments = Department::where('is_system', true)->get();

        return view('livewire.admin.global-test-manager', [
            'tests' => $tests,
            'departments' => $departments
        ])->layout('layouts.app', ['title' => 'Global Test Library']);
    }
}