<?php

namespace App\Livewire\Lab;

use App\Models\LabTest;
use App\Services\LabTestService;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class PackageEditor extends Component
{
    public $package_id;

    public $test_code;

    public $name;

    public $department = 'Profiles/Packages';

    public $mrp;

    public $b2b_price;

    public $sample_type;

    public $tat_hours = 24;

    public $description;

    public $is_active = true;

    public array $selectedTests = [];

    public $testSearchTerm = '';

    public function mount($id = null)
    {
        $this->authorize('view test_packages');
        $labTestService = new LabTestService;
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

            if (! empty($package->linked_test_ids)) {
                $tests = $labTestService->getTestsByIds($package->linked_test_ids);
                foreach ($tests as $t) {
                    $this->selectedTests[] = [
                        'id' => (int) $t->id,
                        'name' => (string) $t->name,
                        'department' => (string) ($t->dept?->name ?? $t->department ?? ''),
                        'mrp' => (float) $t->mrp,
                    ];
                }
            }
        }
    }

    public function addTestToPackage($testId, $testName, $testDept, $testMrp)
    {
        $testId = (int) $testId;
        $existingIds = array_map('intval', array_column($this->selectedTests, 'id'));
        if (! in_array($testId, $existingIds)) {
            $this->selectedTests[] = [
                'id' => $testId,
                'name' => (string) $testName,
                'department' => (string) $testDept,
                'mrp' => (float) $testMrp,
            ];
        }
        $this->testSearchTerm = '';
    }

    public function removeTestFromPackage($testId)
    {
        $testId = (int) $testId;
        $this->selectedTests = array_values(array_filter(
            $this->selectedTests,
            fn ($t) => (int) $t['id'] !== $testId
        ));
    }

    public function moveTestUp($index)
    {
        $index = (int) $index;
        if ($index > 0 && isset($this->selectedTests[$index])) {
            $prev = $index - 1;
            $temp = $this->selectedTests[$prev];
            $this->selectedTests[$prev] = $this->selectedTests[$index];
            $this->selectedTests[$index] = $temp;
            $this->selectedTests = array_values($this->selectedTests);
        }
    }

    public function moveTestDown($index)
    {
        $index = (int) $index;
        if ($index < count($this->selectedTests) - 1 && isset($this->selectedTests[$index])) {
            $next = $index + 1;
            $temp = $this->selectedTests[$next];
            $this->selectedTests[$next] = $this->selectedTests[$index];
            $this->selectedTests[$index] = $temp;
            $this->selectedTests = array_values($this->selectedTests);
        }
    }

    public function reorderTests($orderedIds)
    {
        $lookup = [];
        foreach ($this->selectedTests as $test) {
            $lookup[(int) $test['id']] = $test;
        }

        $reordered = [];
        foreach ($orderedIds as $id) {
            $id = (int) $id;
            if (isset($lookup[$id])) {
                $reordered[] = $lookup[$id];
                unset($lookup[$id]);
            }
        }
        foreach ($lookup as $test) {
            $reordered[] = $test;
        }

        $this->selectedTests = array_values($reordered);
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'mrp' => 'required|numeric|min:0',
            'selectedTests' => 'required|array|min:1',
        ], [
            'selectedTests.required' => 'Please add at least one test to this package.',
            'selectedTests.min' => 'Please add at least one test to this package.',
        ]);

        try {
            $linked_ids = array_values(array_map('intval', array_column($this->selectedTests, 'id')));

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
            Log::error('Error saving package: '.$e->getMessage());
            session()->flash('error', 'Database Error: '.$e->getMessage());
        }
    }

    public function render()
    {
        $labTestService = new LabTestService;
        $searchResultTests = $labTestService->searchSingleTestsForPackage($this->testSearchTerm, 10);
        $selectedTestIds = array_map('intval', array_column($this->selectedTests, 'id'));

        return view('livewire.lab.package-editor', [
            'searchResultTests' => $searchResultTests,
            'selectedTestIds'   => $selectedTestIds,
        ])->layout('layouts.app', ['title' => $this->package_id ? 'Edit Package' : 'New Package']);
    }
}
