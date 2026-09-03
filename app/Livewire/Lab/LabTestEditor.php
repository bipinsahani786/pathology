<?php

namespace App\Livewire\Lab;

use App\Models\Department;
use App\Models\InventoryItem;
use App\Models\LabTestConsumable;
use App\Services\LabTestService;
use Livewire\Component;

class LabTestEditor extends Component
{
    public $test_id;

    public $test_code;

    public $name;

    public $method;

    public $department_id;

    public $mrp;

    public $b2b_price;

    public $sample_type;

    public $tat_hours = 24;

    public $is_active = true;

    public $description;

    public $interpretation;

    public $show_method_on_report = true;

    public $show_interpretation_on_report = true;

    public $show_note_on_report = true;

    public array $parameters = [];

    public array $consumables = []; // [{inventory_item_id, quantity_per_test}]

    public $editingParamIndex = null;

    public $isRangeModalOpen = false;

    public function mount($id = null)
    {
        $this->authorize('view lab_tests');
        $labTestService = new LabTestService;
        if ($id) {
            $test = $labTestService->getTestById($id);
            $this->test_id = $test->id;
            $this->name = $test->name;
            $this->method = $test->method;
            $this->test_code = $test->test_code;
            $this->mrp = $test->mrp;
            $this->b2b_price = $test->b2b_price;
            $this->department_id = $test->department_id;
            $this->sample_type = $test->sample_type;
            $this->tat_hours = $test->tat_hours;
            $this->description = $test->description;
            $this->interpretation = $test->interpretation;
            $this->show_method_on_report = (bool) ($test->show_method_on_report ?? true);
            $this->show_interpretation_on_report = (bool) ($test->show_interpretation_on_report ?? true);
            $this->show_note_on_report = (bool) ($test->show_note_on_report ?? true);
            $this->is_active = $test->is_active;
            $this->parameters = is_array($test->parameters) ? $test->parameters : [];

            // Load existing consumables
            $this->consumables = $test->consumables->map(function ($c) {
                return [
                    'inventory_item_id' => $c->inventory_item_id,
                    'quantity_per_test' => $c->quantity_per_test,
                ];
            })->toArray();
        } else {
            $this->show_method_on_report = true;
            $this->show_interpretation_on_report = true;
            $this->show_note_on_report = true;
            $this->addParameter();
        }
    }

    public function addParameter()
    {
        $this->parameters[] = [
            'name' => '', 'unit' => '', 'range_type' => 'flexible',
            'options' => [],
            'ranges' => [
                [
                    'gender' => 'Both',
                    'age_min' => 0,
                    'age_max' => 120,
                    'age_unit' => 'Years',
                    'min_val' => '',
                    'max_val' => '',
                    'display_range' => '',
                    'normal_value' => '',
                    'is_critical' => false,
                ],
            ],
            'short_code' => '', 'input_type' => 'numeric', 'formula' => '', 'method' => '',
        ];
    }

    public function addHeading()
    {
        $this->parameters[] = [
            'name' => '', 'unit' => '', 'range_type' => 'flexible',
            'options' => [], 'ranges' => [],
            'short_code' => '', 'input_type' => 'heading', 'formula' => '', 'method' => '',
        ];
    }

    public function openRangeModal($index)
    {
        $this->editingParamIndex = $index;
        if (! isset($this->parameters[$index]['ranges'])) {
            $this->parameters[$index]['ranges'] = [];
        }
        if (empty($this->parameters[$index]['ranges'])) {
            $this->addRange();
        }
        $this->isRangeModalOpen = true;
    }

    public function addRange()
    {
        if ($this->editingParamIndex !== null && isset($this->parameters[$this->editingParamIndex])) {
            $this->parameters[$this->editingParamIndex]['ranges'][] = [
                'gender' => 'Both',
                'age_min' => 0,
                'age_max' => 120,
                'age_unit' => 'Years',
                'min_val' => '',
                'max_val' => '',
                'display_range' => '',
                'normal_value' => '',
                'is_critical' => false,
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

    public function moveParameterUp($index)
    {
        if ($index > 0) {
            $prevIndex = $index - 1;
            $temp = $this->parameters[$prevIndex];
            $this->parameters[$prevIndex] = $this->parameters[$index];
            $this->parameters[$index] = $temp;
        }
    }

    public function moveParameterDown($index)
    {
        if ($index < count($this->parameters) - 1) {
            $nextIndex = $index + 1;
            $temp = $this->parameters[$nextIndex];
            $this->parameters[$nextIndex] = $this->parameters[$index];
            $this->parameters[$index] = $temp;
        }
    }

    public function reorderParameters($orderedIds)
    {
        $newParameters = [];
        foreach ($orderedIds as $index) {
            if (isset($this->parameters[$index])) {
                $newParameters[] = $this->parameters[$index];
            }
        }
        $this->parameters = $newParameters;
    }

    public function save()
    {
        $labTestService = new LabTestService;
        // Temporarily set a placeholder name for heading rows so they pass 'required' validation,
        // then clear it back if user left it blank — headings can have empty names.
        $this->validate([
            'name' => 'required|string|max:255',
            'method' => 'nullable|string|max:100',
            'mrp' => 'required|numeric|min:0',
            'department_id' => 'required|exists:departments,id',
            'parameters.*.name' => [
                'string', 'max:255',
                function ($attribute, $value, $fail) {
                    // Extract index from attribute like 'parameters.0.name'
                    $parts = explode('.', $attribute);
                    $index = $parts[1] ?? null;
                    $inputType = $this->parameters[$index]['input_type'] ?? 'numeric';
                    if ($inputType !== 'heading' && (is_null($value) || trim($value) === '')) {
                        $fail('Parameter name is required.');
                    }
                },
            ],
            'parameters.*.input_type' => 'required|in:numeric,text,calculated,selection,culture_sensitivity,heading',
            'parameters.*.method' => 'nullable|string|max:100',
        ], [
            'parameters.*.name.required' => 'Parameter name is required.',
        ]);

        try {
            $data = [
                'name' => $this->name,
                'method' => $this->method,
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
                'show_method_on_report' => (bool) $this->show_method_on_report,
                'show_interpretation_on_report' => (bool) $this->show_interpretation_on_report,
                'show_note_on_report' => (bool) $this->show_note_on_report,
            ];

            $test = $labTestService->saveTest($data, $this->test_id);

            // Sync consumables
            LabTestConsumable::where('lab_test_id', $test->id)->delete();
            foreach ($this->consumables as $c) {
                if (!empty($c['inventory_item_id']) && ($c['quantity_per_test'] ?? 0) > 0) {
                    LabTestConsumable::create([
                        'lab_test_id'       => $test->id,
                        'inventory_item_id' => $c['inventory_item_id'],
                        'quantity_per_test' => $c['quantity_per_test'],
                    ]);
                }
            }

            session()->flash('message', $this->test_id ? 'Test updated successfully.' : 'New test created.');

            return redirect()->route('lab.tests');
        } catch (\Exception $e) {
            session()->flash('error', 'Error saving test: '.$e->getMessage());
        }
    }

    public function addConsumable()
    {
        $this->consumables[] = ['inventory_item_id' => '', 'quantity_per_test' => 1];
    }

    public function removeConsumable($index)
    {
        unset($this->consumables[$index]);
        $this->consumables = array_values($this->consumables);
    }

    public function render()
    {
        $departments = Department::forCompany(auth()->user()->company_id)
            ->where('is_active', true)
            ->orderBy('is_system', 'desc')
            ->orderBy('name')
            ->get();

        $inventoryItems = InventoryItem::where('company_id', auth()->user()->company_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('livewire.lab.lab-test-editor', [
            'departments' => $departments,
            'inventoryItems' => $inventoryItems,
        ])->layout('layouts.app', ['title' => $this->test_id ? 'Edit Lab Test' : 'New Lab Test']);
    }
}
