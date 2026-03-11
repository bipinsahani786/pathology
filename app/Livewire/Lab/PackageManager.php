<?php

namespace App\Livewire\Lab;

use Livewire\Component;
use Livewire\WithPagination;
use App\Services\LabTestService;
use App\Models\LabTest;
use Illuminate\Support\Facades\Log;

class PackageManager extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $searchTerm = '';
    
    public $package_id, $test_code, $name, $department = 'Profiles/Packages';
    public $mrp, $b2b_price, $sample_type, $tat_hours = 24, $description, $is_active = true;
    
    public array $selectedTests = []; 
    public $testSearchTerm = '';
    public $isModalOpen = false;

    public function updatingSearchTerm() { $this->resetPage(); }

    public function create()
    {
        $this->resetFields();
        $this->isModalOpen = true;
    }

    public function edit($id, LabTestService $labTestService)
    {
        $this->resetFields();
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

        $this->selectedTests = [];
        if (!empty($package->linked_test_ids)) {
            $tests = $labTestService->getTestsByIds($package->linked_test_ids);
            foreach ($tests as $t) {
                // FIX: Using Test ID as Array Key
                $this->selectedTests[$t->id] = [
                    'id' => (int) $t->id, 
                    'name' => (string) $t->name, 
                    'department' => (string) $t->department,
                    'mrp' => (float) $t->mrp
                ];
            }
        }
        $this->isModalOpen = true;
    }

    public function addTestToPackage($testId, $testName, $testDept, $testMrp)
    {
        // FIX: Directly assigning using ID as key prevents duplicates and Livewire array corruption
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
        // FIX: Unset using exact ID
        unset($this->selectedTests[$testId]);
    }

    public function delete($id, LabTestService $labTestService)
    {
        $labTestService->deleteTest($id);
        session()->flash('message', 'Package deleted successfully.');
    }

    public function toggleStatus($id, LabTestService $labTestService)
    {
        $labTestService->toggleStatus($id);
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetFields();
    }

    public function resetFields()
    {
        $this->reset(['package_id', 'test_code', 'name', 'mrp', 'b2b_price', 'sample_type', 'tat_hours', 'description', 'is_active', 'selectedTests', 'testSearchTerm']);
        $this->department = 'Profiles/Packages';
        $this->resetValidation();
    }

    public function store()
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
            // Extra safe mapping of IDs
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
            $this->closeModal();
            
        } catch (\Exception $e) {
            Log::error('Error saving package: ' . $e->getMessage());
            session()->flash('error', 'Database Error: ' . $e->getMessage());
        }
    }

    public function render(LabTestService $labTestService)
    {
        $packages = $labTestService->getPaginatedPackages($this->searchTerm, 10);
        $searchResultTests = $labTestService->searchSingleTestsForPackage($this->testSearchTerm, 10);

        return view('livewire.lab.package-manager', [
            'packages' => $packages,
            'searchResultTests' => $searchResultTests
        ])->layout('layouts.app', ['title' => 'Test Packages']);
    }
}