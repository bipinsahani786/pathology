<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Services\GlobalTestService;
use Illuminate\Validation\Rule;

class GlobalTestManager extends Component
{
    use WithPagination;
    
    protected $paginationTheme = 'bootstrap';

    // Form Fields
    public $test_id, $test_code, $name, $category, $suggested_price, $description, $interpretation;
    public $isModalOpen = false;
    
    // Dynamic array to store test parameters
    public array $parameters = []; 

    // Search & Filter Properties
    public $searchTerm = '';
    public $filterCategory = '';

    public function updatingSearchTerm()
    {
        $this->resetPage();
    }

    public function updatingFilterCategory()
    {
        $this->resetPage();
    }

    /**
     * Add a new parameter row with ALL required keys (Matching Lab Side)
     */
    public function addParameter()
    {
        $this->parameters[] = [
            'name' => '', 
            'unit' => '', 
            'range_type' => 'general', 
            'general_range' => '',
            'male_range' => '', 
            'female_range' => '',
            'normal_value' => '',
            // NEW ADDED: For Calculation Support
            'short_code' => '',
            'input_type' => 'numeric',
            'formula' => ''
        ];
    }

    public function removeParameter($index)
    {
        unset($this->parameters[$index]);
        $this->parameters = array_values($this->parameters); 
    }

    public function create()
    {
        $this->resetFields();
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetFields();
    }

    public function resetFields()
    {
        $this->reset(['test_id', 'test_code', 'name', 'category', 'suggested_price', 'description', 'interpretation']);
        $this->parameters = []; 
        $this->resetValidation(); 
    }

    public function store(GlobalTestService $testService)
    {
        $validatedData = $this->validate([
            'test_code' => ['required', 'string', 'max:50', Rule::unique('global_tests', 'test_code')->ignore($this->test_id)],
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'suggested_price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'interpretation' => 'nullable|string',
            
            // Parameter validation
            'parameters' => 'nullable|array',
            'parameters.*.name' => 'required|string|max:255',
            'parameters.*.short_code' => 'nullable|string|max:50', // NEW
            'parameters.*.input_type' => 'required|in:numeric,text,calculated', // NEW
            'parameters.*.formula' => 'nullable|string|max:255', // NEW
            'parameters.*.unit' => 'nullable|string|max:50',
            'parameters.*.range_type' => 'required|in:general,gender,value',
            'parameters.*.general_range' => 'nullable|string|max:255',
            'parameters.*.male_range' => 'nullable|string|max:255',
            'parameters.*.female_range' => 'nullable|string|max:255',
            'parameters.*.normal_value' => 'nullable|string|max:255',
        ]);

        // Clean up unnecessary data
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
            
            // If not calculated, clear formula
            if (($param['input_type'] ?? 'numeric') !== 'calculated') {
                $this->parameters[$key]['formula'] = null;
            }
        }

        $validatedData['default_parameters'] = $this->parameters;
        unset($validatedData['parameters']); 

        $testService->saveTest($validatedData, $this->test_id);

        session()->flash('message', $this->test_id ? 'Test Updated Successfully.' : 'Test Created Successfully.');
        $this->closeModal();
    }

    public function edit($id, GlobalTestService $testService)
    {
        $test = $testService->getTestById($id);

        $this->test_id = $id;
        $this->test_code = $test->test_code;
        $this->name = $test->name;
        $this->category = $test->category;
        $this->suggested_price = $test->suggested_price;
        $this->description = $test->description;
        $this->interpretation = $test->interpretation;
        
        $this->parameters = $test->default_parameters ?? [];
        $this->isModalOpen = true;
    }

    public function delete($id, GlobalTestService $testService)
    {
        $testService->deleteTest($id);
        session()->flash('message', 'Test Deleted Successfully.');
    }

    public function render(GlobalTestService $testService)
    {
        $tests = $testService->getPaginatedTests(10, $this->searchTerm, $this->filterCategory);

        return view('livewire.admin.global-test-manager', [
            'tests' => $tests
        ])->layout('layouts.app', ['title' => 'Global Test Library']);
    }
}