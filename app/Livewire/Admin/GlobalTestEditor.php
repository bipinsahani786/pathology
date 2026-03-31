<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Services\GlobalTestService;
use App\Models\Department;
use Illuminate\Validation\Rule;

class GlobalTestEditor extends Component
{
    public $test_id, $test_code, $name, $method, $department_id, $suggested_price, $description, $interpretation;
    public array $parameters = [];
    public $editingParamIndex = null;
    public $isRangeModalOpen = false;

    public function mount($id = null)
    {
        $testService = new GlobalTestService();
        if ($id) {
            $test = $testService->getTestById($id);
            $this->test_id = $test->id;
            $this->test_code = $test->test_code;
            $this->name = $test->name;
            $this->method = $test->method;
            $this->department_id = $test->department_id;
            $this->suggested_price = $test->suggested_price;
            $this->description = $test->description;
            $this->interpretation = $test->interpretation;
            $this->parameters = $test->default_parameters ?? [];
        } else {
            // Initial parameter for new test
            $this->addParameter();
        }
    }

    public function addParameter()
    {
        $this->parameters[] = [
            'name' => '',
            'short_code' => '',
            'unit' => '',
            'input_type' => 'numeric',
            'options' => [], // For selection type
            'range_type' => 'flexible', // Default to the new flexible system
            'method' => '', // Analytical method for this specific parameter
            'ranges' => [
                [
                    'gender' => 'Both',
                    'age_min' => 0,
                    'age_max' => 100,
                    'age_unit' => 'Years',
                    'min_val' => '',
                    'max_val' => '',
                    'display_range' => '',
                    'normal_value' => '', // For qualitative
                    'is_critical' => false
                ]
            ],
            'formula' => ''
        ];
    }

    public function openRangeModal($index)
    {
        $this->editingParamIndex = $index;
        // Ensure ranges array exists
        if (!isset($this->parameters[$index]['ranges'])) {
            $this->parameters[$index]['ranges'] = [];
        }
        if (empty($this->parameters[$index]['ranges'])) {
            $this->addRange();
        }
        $this->isRangeModalOpen = true;
    }

    public function addRange()
    {
        if ($this->editingParamIndex !== null) {
            $this->parameters[$this->editingParamIndex]['ranges'][] = [
                'gender' => 'Both',
                'age_min' => 0,
                'age_max' => 100,
                'age_unit' => 'Years',
                'min_val' => '',
                'max_val' => '',
                'display_range' => '',
                'normal_value' => '',
                'is_critical' => false
            ];
        }
    }

    public function removeRange($rangeIndex)
    {
        if ($this->editingParamIndex !== null) {
            unset($this->parameters[$this->editingParamIndex]['ranges'][$rangeIndex]);
            $this->parameters[$this->editingParamIndex]['ranges'] = array_values($this->parameters[$this->editingParamIndex]['ranges']);
        }
    }

    public function closeRangeModal()
    {
        $this->isRangeModalOpen = false;
        $this->editingParamIndex = null;
    }

    public function addOption()
    {
        if ($this->editingParamIndex !== null) {
            $this->parameters[$this->editingParamIndex]['options'][] = '';
        }
    }

    public function removeOption($optionIndex)
    {
        if ($this->editingParamIndex !== null) {
            unset($this->parameters[$this->editingParamIndex]['options'][$optionIndex]);
            $this->parameters[$this->editingParamIndex]['options'] = array_values($this->parameters[$this->editingParamIndex]['options']);
        }
    }

    public function removeParameter($index)
    {
        unset($this->parameters[$index]);
        $this->parameters = array_values($this->parameters);
    }

    public function save()
    {
        $testService = new GlobalTestService();
        $validatedData = $this->validate([
            'test_code' => ['required', 'string', 'max:50', Rule::unique('global_tests', 'test_code')->ignore($this->test_id)],
            'name' => 'required|string|max:255',
            'method' => 'nullable|string|max:100',
            'department_id' => 'required|exists:departments,id',
            'suggested_price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'interpretation' => 'nullable|string',
            'parameters' => 'nullable|array',
            'parameters.*.name' => 'required|string|max:255',
            'parameters.*.short_code' => 'nullable|string|max:50',
            'parameters.*.input_type' => 'required|in:numeric,text,calculated,selection',
            'parameters.*.range_type' => 'required|in:general,gender,value,flexible',
            'parameters.*.unit' => 'nullable|string|max:50',
            'parameters.*.method' => 'nullable|string|max:100',
        ]);

        // Basic cleanup for non-calculated types
        foreach ($this->parameters as $key => $param) {
            if ($param['input_type'] !== 'calculated') {
                $this->parameters[$key]['formula'] = null;
            }
        }

        $saveData = [
            'test_code' => $this->test_code,
            'name' => $this->name,
            'method' => $this->method,
            'department_id' => $this->department_id,
            'suggested_price' => $this->suggested_price,
            'description' => $this->description,
            'interpretation' => $this->interpretation,
            'default_parameters' => $this->parameters,
        ];

        $testService->saveTest($saveData, $this->test_id);

        session()->flash('message', $this->test_id ? 'Global Test updated successfully.' : 'Global Test created successfully.');
        return redirect()->route('admin.global-tests');
    }

    public function render()
    {
        $departments = Department::where('is_system', true)->get();
        return view('livewire.admin.global-test-editor', [
            'departments' => $departments
        ])->layout('layouts.app', ['title' => $this->test_id ? 'Edit Master Test' : 'New Master Test']);
    }
}
