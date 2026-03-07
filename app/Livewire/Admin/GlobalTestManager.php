<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Services\GlobalTestService;
use Illuminate\Validation\Rule;

class GlobalTestManager extends Component
{
    use WithPagination;
    
    // Use Bootstrap pagination theme
    protected $paginationTheme = 'bootstrap';

    // Form Fields
    public $test_id, $test_code, $name, $category, $suggested_price, $description;
    public $isModalOpen = false;
    
    // Dynamic array to store test parameters
    public array $parameters = []; 

    // --- NEW: Search & Filter Properties ---
    public $searchTerm = '';
    public $filterCategory = '';

    /**
     * Reset pagination when search term changes
     */
    public function updatingSearchTerm()
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when category filter changes
     */
    public function updatingFilterCategory()
    {
        $this->resetPage();
    }
    /**
     * Add a new parameter row with a default type of 'general'
     */
    public function addParameter()
    {
        $this->parameters[] = [
            'name' => '', 
            'unit' => '', 
            'range_type' => 'general', // 'general', 'gender', 'value'
            'general_range' => '',
            'male_range' => '', 
            'female_range' => '',
            'normal_value' => ''
        ];
    }

    /**
     * Remove a specific parameter row
     * * @param int $index
     */
    public function removeParameter($index)
    {
        unset($this->parameters[$index]);
        // Re-index array to prevent Livewire rendering issues
        $this->parameters = array_values($this->parameters); 
    }

    /**
     * Open modal to create a new test
     */
    public function create()
    {
        $this->resetFields();
        $this->isModalOpen = true;
    }

    /**
     * Close modal and clear fields
     */
    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetFields();
    }

    /**
     * Reset all component properties
     */
    public function resetFields()
    {
        $this->reset(['test_id', 'test_code', 'name', 'category', 'suggested_price', 'description']);
        $this->parameters = []; 
        $this->resetValidation(); 
    }

    /**
     * Validate and save the test to the database
     * * @param GlobalTestService $testService
     */
    public function store(GlobalTestService $testService)
    {
        // Validation rules for all test types
        $validatedData = $this->validate([
            'test_code' => ['required', 'string', 'max:50', Rule::unique('global_tests', 'test_code')->ignore($this->test_id)],
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'suggested_price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            
            // Parameter validation
            'parameters' => 'nullable|array',
            'parameters.*.name' => 'required|string|max:255',
            'parameters.*.unit' => 'nullable|string|max:50',
            'parameters.*.range_type' => 'required|in:general,gender,value',
            'parameters.*.general_range' => 'nullable|string|max:255',
            'parameters.*.male_range' => 'nullable|string|max:255',
            'parameters.*.female_range' => 'nullable|string|max:255',
            'parameters.*.normal_value' => 'nullable|string|max:255',
        ]);

        // Clean up data based on range_type to save DB space
        foreach ($this->parameters as $key => $param) {
            if ($param['range_type'] === 'general') {
                $this->parameters[$key]['male_range'] = null;
                $this->parameters[$key]['female_range'] = null;
                $this->parameters[$key]['normal_value'] = null;
            } elseif ($param['range_type'] === 'gender') {
                $this->parameters[$key]['general_range'] = null;
                $this->parameters[$key]['normal_value'] = null;
            } elseif ($param['range_type'] === 'value') {
                $this->parameters[$key]['general_range'] = null;
                $this->parameters[$key]['male_range'] = null;
                $this->parameters[$key]['female_range'] = null;
            }
        }

        // Format for JSONB column
        $validatedData['default_parameters'] = $this->parameters;
        unset($validatedData['parameters']); 

        // Save via Service
        $testService->saveTest($validatedData, $this->test_id);

        session()->flash('message', $this->test_id ? 'Test Updated Successfully.' : 'Test Created Successfully.');
        $this->closeModal();
    }

    /**
     * Fetch test and open edit modal
     * * @param int $id
     * @param GlobalTestService $testService
     */
    public function edit($id, GlobalTestService $testService)
    {
        $test = $testService->getTestById($id);

        $this->test_id = $id;
        $this->test_code = $test->test_code;
        $this->name = $test->name;
        $this->category = $test->category;
        $this->suggested_price = $test->suggested_price;
        $this->description = $test->description;
        
        // Decode JSONB back to array
        $this->parameters = $test->default_parameters ?? [];
        
        $this->isModalOpen = true;
    }

    /**
     * Delete test
     * * @param int $id
     * @param GlobalTestService $testService
     */
    public function delete($id, GlobalTestService $testService)
    {
        $testService->deleteTest($id);
        session()->flash('message', 'Test Deleted Successfully.');
    }

    public function render(GlobalTestService $testService)
    {
        // Pass search and filter values to the service
        $tests = $testService->getPaginatedTests(10, $this->searchTerm, $this->filterCategory);

        return view('livewire.admin.global-test-manager', [
            'tests' => $tests
        ])->layout('layouts.app', ['title' => 'Global Test Library']);
    }
}