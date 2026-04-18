<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Enquiry;

class EnquiryManager extends Component
{
    public string $filterStatus = '';
    public string $filterType = '';
    public string $search = '';

    public ?int $viewingId = null;
    public string $adminNotes = '';
    public string $statusUpdate = '';

    public function viewEnquiry($id)
    {
        $enquiry = Enquiry::findOrFail($id);
        $this->viewingId = $enquiry->id;
        $this->adminNotes = $enquiry->admin_notes ?? '';
        $this->statusUpdate = $enquiry->status;
    }

    public function updateEnquiry()
    {
        $enquiry = Enquiry::findOrFail($this->viewingId);
        $enquiry->update([
            'status' => $this->statusUpdate,
            'admin_notes' => $this->adminNotes,
        ]);
        $this->viewingId = null;
        session()->flash('success', 'Enquiry updated!');
    }

    public function deleteEnquiry($id)
    {
        Enquiry::destroy($id);
        session()->flash('success', 'Enquiry deleted!');
    }

    public function render()
    {
        $enquiries = Enquiry::query()
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterType, fn($q) => $q->where('enquiry_type', $this->filterType))
            ->when($this->search, fn($q) => $q->where(function($q) {
                $q->where('name', 'ilike', "%{$this->search}%")
                  ->orWhere('email', 'ilike', "%{$this->search}%")
                  ->orWhere('lab_name', 'ilike', "%{$this->search}%");
            }))
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $newCount = Enquiry::new()->count();

        return view('livewire.admin.enquiry-manager', [
            'enquiries' => $enquiries,
            'newCount' => $newCount,
        ])->layout('layouts.app');
    }
}
