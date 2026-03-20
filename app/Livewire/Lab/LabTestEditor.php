<?php

namespace App\Livewire\Lab;

use Livewire\Component;
use App\Services\LabTestService;
use App\Models\Department;
use Illuminate\Validation\Rule;

class LabTestEditor extends Component
{
    public $test_id, $test_code, $name, $department_id, $mrp, $b2b_price, $sample_type;
    public $tat_hours = 24;
    public $is_active = true;
    public $description;
    public $interpretation;
    public array $parameters = [];

    public function mount($id = null)
    {
        $labTestService = new LabTestService();
        if ($id) {
            $test = $labTestService->getTestById($id);
            $this->test_id = $test->id;
            $this->name = $test->name;
            $this->test_code = $test->test_code;
            $this->mrp = $test->mrp;
            $this->b2b_price = $test->b2b_price;
            $this->department_id = $test->department_id;
            $this->sample_type = $test->sample_type;
            $this->tat_hours = $test->tat_hours;
            $this->description = $test->description;
            $this->interpretation = $test->interpretation;
            $this->is_active = $test->is_active;
            $this->parameters = is_array($test->parameters) ? $test->parameters : [];
        } else {
            $this->addParameter();
        }
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

    public function save()
    {
        $labTestService = new LabTestService();
        $this->validate([
            'name' => 'required|string|max:255',
            'mrp' => 'required|numeric|min:0',
            'department_id' => 'required|exists:departments,id',
            'parameters.*.name' => 'required',
        ], [
            'parameters.*.name.required' => 'Parameter name is required.'
        ]);

        try {
            $data = [
                'name' => $this->name,
                'test_code' => $this->test_code,
                'department_id' => $this->department_id,
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
            return redirect()->route('lab.tests');
        } catch (\Exception $e) {
            session()->flash('error', 'Error saving test: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $departments = Department::forCompany(auth()->user()->company_id)
            ->where('is_active', true)
            ->orderBy('is_system', 'desc')
            ->orderBy('name')
            ->get();

        return view('livewire.lab.lab-test-editor', [
            'departments' => $departments
        ])->layout('layouts.app', ['title' => $this->test_id ? 'Edit Lab Test' : 'New Lab Test']);
    }
}
