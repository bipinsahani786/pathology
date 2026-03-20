<?php

namespace App\Livewire\Lab;

use Livewire\Component;
use Livewire\WithPagination;
use App\Services\LabTestService;
use App\Models\Department;

class LabTestManager extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    // List Filters
    public $searchTerm = '';
    public $filterCategory = '';

    // UI States
    public $isImportModalOpen = false;
    public $globalSearch = '';
    public $globalLimit = 15;

    public function updatingSearchTerm() { $this->resetPage(); }
    public function updatingFilterCategory() { $this->resetPage(); }

    public function openImportModal()
    {
        $this->globalSearch = '';
        $this->globalLimit = 15;
        $this->isImportModalOpen = true;
    }

    public function loadMoreGlobalTests()
    {
        $this->globalLimit += 15;
    }

    public function updatedGlobalSearch()
    {
        $this->globalLimit = 15;
    }

    public function importGlobalTest($globalTestId)
    {
        $labTestService = new LabTestService();
        try {
            $newTest = $labTestService->importFromGlobal($globalTestId, auth()->user()->company_id);
            $this->isImportModalOpen = false;
            
            session()->flash('message', 'Global test imported successfully. Please set your pricing and verify parameters.');
            return redirect()->route('lab.tests.edit', $newTest->id);
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error importing test: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $labTestService = new LabTestService();
        $labTestService->deleteTest($id);
        session()->flash('message', 'Lab test deleted successfully.');
    }

    public function toggleStatus($id)
    {
        $labTestService = new LabTestService();
        $labTestService->toggleStatus($id);
    }

    public function closeModal()
    {
        $this->isImportModalOpen = false;
    }

    public function render()
    {
        $labTestService = new LabTestService();
        $tests = $labTestService->getPaginatedTests($this->searchTerm, $this->filterCategory, 12);
        $globalTests = $labTestService->searchGlobalTests($this->globalSearch, $this->globalLimit);
        
        $departments = Department::forCompany(auth()->user()->company_id)
            ->where('is_active', true)
            ->orderBy('is_system', 'desc')
            ->orderBy('name')
            ->get();

        return view('livewire.lab.lab-test-manager', [
            'tests' => $tests,
            'globalTests' => $globalTests,
            'departments' => $departments
        ])->layout('layouts.app');
    }
}