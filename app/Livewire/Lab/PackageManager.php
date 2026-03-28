<?php

namespace App\Livewire\Lab;

use Livewire\Component;
use Livewire\WithPagination;
use App\Services\LabTestService;

class PackageManager extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        $this->authorize('view test_packages');
    }

    public $searchTerm = '';

    public function updatingSearchTerm() { $this->resetPage(); }

    public function delete($id)
    {
        $this->authorize('delete test_packages');
        $labTestService = new LabTestService();
        $labTestService->deleteTest($id);
        session()->flash('message', 'Package deleted successfully.');
    }

    public function toggleStatus($id)
    {
        $this->authorize('edit test_packages');
        $labTestService = new LabTestService();
        $labTestService->toggleStatus($id);
    }

    public function render()
    {
        $labTestService = new LabTestService();
        $packages = $labTestService->getPaginatedPackages($this->searchTerm, 10);

        return view('livewire.lab.package-manager', [
            'packages' => $packages
        ])->layout('layouts.app', ['title' => 'Test Packages']);
    }
}