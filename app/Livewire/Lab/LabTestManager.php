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

    public function mount()
    {
        $this->authorize('view lab_tests');
    }

    // List Filters
    public $searchTerm = '';
    public $filterCategory = '';

    // UI States
    public $isImportModalOpen = false;
    public $globalSearch = '';
    public $globalLimit = 15;
    public $selectedGlobalTests = []; // For bulk import

    public function updatingSearchTerm() { $this->resetPage(); }
    public function updatingFilterCategory() { $this->resetPage(); }
    public function updatedGlobalSearch() 
    { 
        $this->resetPage(); 
        $this->selectedGlobalTests = []; 
        $this->globalLimit = 15; 
    }

    public function resetGlobalSelection()
    {
        $this->selectedGlobalTests = [];
    }

    public function bulkImport()
    {
        $this->authorize('create lab_tests');
        
        if (empty($this->selectedGlobalTests)) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Please select at least one test.']);
            return;
        }

        $labTestService = new LabTestService();
        $count = 0;
        $errors = 0;

        foreach ($this->selectedGlobalTests as $globalTestId) {
            try {
                $labTestService->importFromGlobal($globalTestId, auth()->user()->company_id);
                $count++;
            } catch (\Exception $e) {
                $errors++;
            }
        }

        $this->selectedGlobalTests = [];
        $this->isImportModalOpen = false;

        if ($errors > 0) {
            session()->flash('message', "Imported $count tests. $errors tests failed (maybe already exists).");
        } else {
            session()->flash('message', "Successfully imported $count tests.");
        }
        
        $this->resetPage();
    }

    public function openImportModal()
    {
        $this->authorize('create lab_tests');
        $this->globalSearch = '';
        $this->globalLimit = 15;
        $this->isImportModalOpen = true;
    }

    public function loadMoreGlobalTests()
    {
        $this->globalLimit += 15;
    }



    public function importGlobalTest($globalTestId)
    {
        $this->authorize('create lab_tests');
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
        $this->authorize('delete lab_tests');
        $labTestService = new LabTestService();
        $labTestService->deleteTest($id);
        session()->flash('message', 'Lab test deleted successfully.');
    }

    public function toggleStatus($id)
    {
        $this->authorize('edit lab_tests');
        $labTestService = new LabTestService();
        $labTestService->toggleStatus($id);
    }

    public function updateMrp($id, $value)
    {
        $this->authorize('edit lab_tests');
        $test = \App\Models\LabTest::where('company_id', auth()->user()->company_id)->findOrFail($id);
        $test->update(['mrp' => is_numeric($value) ? $value : 0]);
        $this->dispatch('notify', ['type' => 'success', 'message' => 'MRP updated.']);
    }

    public function updateB2BPrice($id, $value)
    {
        $this->authorize('edit lab_tests');
        $test = \App\Models\LabTest::where('company_id', auth()->user()->company_id)->findOrFail($id);
        $test->update(['b2b_price' => is_numeric($value) ? $value : 0]);
        $this->dispatch('notify', ['type' => 'success', 'message' => 'B2B Price updated.']);
    }

    public function closeModal()
    {
        $this->isImportModalOpen = false;
    }

    public function render()
    {
        $companyId = auth()->user()->company_id;
        $labTestService = new LabTestService();
        $tests = $labTestService->getPaginatedTests($this->searchTerm, $this->filterCategory, 12);
        $globalTests = $labTestService->searchGlobalTests($this->globalSearch, $this->globalLimit);
        
        $departments = \Illuminate\Support\Facades\Cache::remember("departments_{$companyId}", 3600, function() use ($companyId) {
            return Department::forCompany($companyId)
                ->where('is_active', true)
                ->orderBy('is_system', 'desc')
                ->orderBy('name')
                ->get();
        });

        $importedGlobalTestIds = \App\Models\LabTest::where('company_id', auth()->user()->company_id)
            ->whereNotNull('global_test_id')
            ->pluck('global_test_id')
            ->toArray();

        return view('livewire.lab.lab-test-manager', [
            'tests' => $tests,
            'globalTests' => $globalTests,
            'departments' => $departments,
            'importedGlobalTestIds' => $importedGlobalTestIds
        ])->layout('layouts.app');
    }
}