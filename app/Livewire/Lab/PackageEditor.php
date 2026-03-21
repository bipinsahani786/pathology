<?php

namespace App\Livewire\Lab;

use Livewire\Component;
use App\Services\LabTestService;
use App\Models\LabTest;
use Illuminate\Support\Facades\Log;

class PackageEditor extends Component
{
    public $package_id, $test_code, $name, $department = 'Profiles/Packages';
    public $mrp, $b2b_price, $sample_type, $tat_hours = 24, $description, $is_active = true;
    
    public array $selectedTests = []; 
    public $testSearchTerm = '';

    public function mount($id = null)
    {
        $this->authorize('manage test_packages');
        $labTestService = new LabTestService();
        if ($id) {
            $package = $labTestService->getTestById($id);
            $this->package_id = $package->id;
            $this->test_code = $package->test_code;
            $this->name = $package->name;
            $this->department = $package->department;
            $this->mrp = $package->mrp;
            $this->b2b_price = $package->b2b_price;
            $this->sample_type = $package->sample_type;
            $this->tat_hours = $package->tat_hours;
            $this->description = $package->description;
            $this->is_active = $package->is_active;

            if (!empty($package->linked_test_ids)) {
                $tests = $labTestService->getTestsByIds($package->linked_test_ids);
                foreach ($tests as $t) {
                    $this->selectedTests[$t->id] = [
                        'id' => (int) $t->id, 
                        'name' => (string) $t->name, 
                        'department' => (string) $t->department,
                        'mrp' => (float) $t->mrp
                    ];
                }
            }
        }
    }

    public function addTestToPackage($testId, $testName, $testDept, $testMrp)
    {
        $this->selectedTests[$testId] = [
            'id' => (int) $testId, 
            'name' => (string) $testName, 
            'department' => (string) $testDept,
            'mrp' => (float) $testMrp
        ];
        $this->testSearchTerm = ''; 
    }

    public function removeTestFromPackage($testId)
    {
        unset($this->selectedTests[$testId]);
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'mrp' => 'required|numeric|min:0',
            'selectedTests' => 'required|array|min:1',
        ], [
            'selectedTests.required' => 'Please add at least one test to this package.',
            'selectedTests.min' => 'Please add at least one test to this package.'
        ]);

        try {
            $linked_ids = array_values(array_map('intval', array_keys($this->selectedTests)));

            LabTest::updateOrCreate(
                ['id' => $this->package_id],
                [
                    'company_id' => auth()->user()->company_id,
                    'name' => $this->name,
                    'test_code' => $this->test_code,
                    'department' => $this->department,
                    'description' => $this->description,
                    'mrp' => $this->mrp,
                    'b2b_price' => $this->b2b_price ?: 0,
                    'sample_type' => $this->sample_type,
                    'tat_hours' => $this->tat_hours ?: null,
                    'is_package' => true,
                    'linked_test_ids' => $linked_ids,
                    'is_active' => $this->is_active,
                ]
            );

            session()->flash('message', $this->package_id ? 'Package updated successfully.' : 'Package created successfully.');
            return redirect()->route('lab.packages');
            
        } catch (\Exception $e) {
            Log::error('Error saving package: ' . $e->getMessage());
            session()->flash('error', 'Database Error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $labTestService = new LabTestService();
        $searchResultTests = $labTestService->searchSingleTestsForPackage($this->testSearchTerm, 10);

        return view('livewire.lab.package-editor', [
            'searchResultTests' => $searchResultTests
        ])->layout('layouts.app', ['title' => $this->package_id ? 'Edit Package' : 'New Package']);
    }
}
