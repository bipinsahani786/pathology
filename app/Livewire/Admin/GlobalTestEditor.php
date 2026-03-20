<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Services\GlobalTestService;
use App\Models\Department;
use Illuminate\Validation\Rule;

class GlobalTestEditor extends Component
{
    public $test_id, $test_code, $name, $department_id, $suggested_price, $description, $interpretation;
    public array $parameters = [];

    public function mount($id = null)
    {
        $testService = new GlobalTestService();
        if ($id) {
            $test = $testService->getTestById($id);
            $this->test_id = $test->id;
            $this->test_code = $test->test_code;
            $this->name = $test->name;
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
            'range_type' => 'general',
            'general_range' => '',
            'male_range' => '',
            'female_range' => '',
            'normal_value' => '',
            'formula' => ''
        ];
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
            'department_id' => 'required|exists:departments,id',
            'suggested_price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'interpretation' => 'nullable|string',
            'parameters' => 'nullable|array',
            'parameters.*.name' => 'required|string|max:255',
            'parameters.*.short_code' => 'nullable|string|max:50',
            'parameters.*.input_type' => 'required|in:numeric,text,calculated',
            'parameters.*.range_type' => 'required|in:general,gender,value',
            'parameters.*.unit' => 'nullable|string|max:50',
        ]);

        // Logic for range cleanup based on range_type
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
            if ($param['input_type'] !== 'calculated') {
                $this->parameters[$key]['formula'] = null;
            }
        }

        $saveData = [
            'test_code' => $this->test_code,
            'name' => $this->name,
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
