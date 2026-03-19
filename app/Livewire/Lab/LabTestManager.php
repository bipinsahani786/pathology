<?php

namespace App\Livewire\Lab;

use Livewire\Component;
use Livewire\WithPagination;
use App\Services\LabTestService; // Import the new service

class LabTestManager extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    // List Filters
    public $searchTerm = '';
    public $filterCategory = '';

    // Form Fields
    public $test_id, $test_code, $name, $department, $mrp, $b2b_price, $sample_type;
    public $tat_hours = 24;
    public $is_active = true;
    public $description;
    public $interpretation;
    public array $parameters = []; 

    // UI States
    public $isModalOpen = false;
    public $isImportModalOpen = false;
    public $globalSearch = '';

    public function updatingSearchTerm() { $this->resetPage(); }
    public function updatingFilterCategory() { $this->resetPage(); }

    public function create()
    {
        $this->resetFields();
        $this->addParameter(); 
        $this->isModalOpen = true;
    }

    public function openImportModal()
    {
        $this->globalSearch = '';
        $this->isImportModalOpen = true;
    }

    /**
     * Inject LabTestService directly into the method
     */
    public function importGlobalTest($globalTestId, LabTestService $labTestService)
    {
        $newTest = $labTestService->importFromGlobal($globalTestId, auth()->user()->company_id);

        $this->isImportModalOpen = false;
        
        // Open edit modal directly for the new test
        $this->edit($newTest->id, $labTestService);
        
        session()->flash('message', 'Global test imported successfully. Please set your pricing and short codes.');
    }

    public function edit($id, LabTestService $labTestService)
    {
        $this->resetFields();
        $test = $labTestService->getTestById($id);
        
        $this->test_id = $test->id;
        $this->name = $test->name;
        $this->test_code = $test->test_code;
        $this->mrp = $test->mrp;
        $this->b2b_price = $test->b2b_price;
        $this->department = $test->department;
        $this->sample_type = $test->sample_type;
        $this->tat_hours = $test->tat_hours;
        $this->description = $test->description; 
        $this->interpretation = $test->interpretation;
        $this->is_active = $test->is_active;
        
        $this->parameters = is_array($test->parameters) ? $test->parameters : [];
        $this->isModalOpen = true;
    }

    public function delete($id, LabTestService $labTestService)
    {
        $labTestService->deleteTest($id);
        session()->flash('message', 'Lab test deleted successfully.');
    }

    public function toggleStatus($id, LabTestService $labTestService)
    {
        $labTestService->toggleStatus($id);
    }

    public function addParameter()
    {
        $this->parameters[] = [
            'name' => '', 'unit' => '', 'range_type' => 'general',
            'general_range' => '', 'male_range' => '', 'female_range' => '',
            'normal_value' => '', 'short_code' => '', 'input_type' => 'numeric', 'formula' => ''
        ];
    }

    public function removeParameter($index)
    {
        unset($this->parameters[$index]);
        $this->parameters = array_values($this->parameters);
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->isImportModalOpen = false;
        $this->resetFields();
    }

    public function resetFields()
    {
        $this->reset(['test_id', 'test_code', 'name', 'department', 'mrp', 'b2b_price', 'sample_type', 'tat_hours', 'parameters', 'is_active', 'description', 'interpretation']);
        $this->resetValidation();
    }

    public function store(LabTestService $labTestService)
    {
        $validatedData = $this->validate([
            'name' => 'required|string|max:255',
            'mrp' => 'required|numeric|min:0',
            'parameters.*.name' => 'required',
        ], [
            'parameters.*.name.required' => 'Parameter name is required.'
        ]);

        // Bundle data for service
        $data = [
            'name' => $this->name,
            'test_code' => $this->test_code,
            'department' => $this->department,
            'description' => $this->description,
            'interpretation' => $this->interpretation,
            'mrp' => $this->mrp,
            'b2b_price' => $this->b2b_price,
            'sample_type' => $this->sample_type,
            'tat_hours' => $this->tat_hours,
            'parameters' => $this->parameters,
            'is_active' => $this->is_active,
        ];

        $labTestService->saveTest($data, $this->test_id);

        session()->flash('message', $this->test_id ? 'Test updated successfully.' : 'New test created.');
        $this->closeModal();
    }

    public function render(LabTestService $labTestService)
    {
        $tests = $labTestService->getPaginatedTests($this->searchTerm, $this->filterCategory, 10);
        $globalTests = $labTestService->searchGlobalTests($this->globalSearch, 15);

        return view('livewire.lab.lab-test-manager', [
            'tests' => $tests,
            'globalTests' => $globalTests
        ])->layout('layouts.app');
    }
}