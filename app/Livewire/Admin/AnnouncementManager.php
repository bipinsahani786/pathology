<?php

namespace App\Livewire\Admin;

use App\Models\Announcement;
use App\Models\AnnouncementDismissal;
use Livewire\Component;
use Livewire\WithPagination;

class AnnouncementManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Form fields
    public $announcement_id;
    public $title = '';
    public $message = '';
    public $type = 'info';
    public $icon = 'feather-bell';
    public $action_url = '';
    public $action_label = '';
    public $is_active = true;
    public $is_dismissible = true;
    public $priority = 0;
    public $starts_at;
    public $expires_at;

    // UI state
    public $isModalOpen = false;
    public $deleteConfirmId = null;
    public $search = '';
    public $filterType = '';
    public $filterStatus = '';

    protected function rules()
    {
        return [
            'title'          => 'required|string|max:255',
            'message'        => 'required|string',
            'type'           => 'required|in:info,warning,success,danger',
            'icon'           => 'nullable|string|max:50',
            'action_url'     => 'nullable|url|max:255',
            'action_label'   => 'nullable|string|max:60',
            'is_active'      => 'boolean',
            'is_dismissible' => 'boolean',
            'priority'       => 'nullable|integer',
            'starts_at'      => 'nullable|date',
            'expires_at'     => 'nullable|date|after_or_equal:starts_at',
        ];
    }

    public function mount()
    {
        if (! auth()->user()->hasRole('super_admin')) {
            abort(403, 'Unauthorized access to Superadmin Cabinet.');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterType()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->resetFields();
        $this->isModalOpen = true;
    }

    public function edit($id)
    {
        $announcement = Announcement::findOrFail($id);

        $this->announcement_id = $announcement->id;
        $this->title          = $announcement->title;
        $this->message        = $announcement->message;
        $this->type           = $announcement->type;
        $this->icon           = $announcement->icon ?? 'feather-bell';
        $this->action_url     = $announcement->action_url ?? '';
        $this->action_label   = $announcement->action_label ?? '';
        $this->is_active      = (bool) $announcement->is_active;
        $this->is_dismissible = (bool) $announcement->is_dismissible;
        $this->priority       = $announcement->priority ?? 0;
        $this->starts_at      = $announcement->starts_at?->format('Y-m-d\TH:i');
        $this->expires_at     = $announcement->expires_at?->format('Y-m-d\TH:i');

        $this->isModalOpen = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'title'          => trim($this->title),
            'message'        => trim($this->message),
            'type'           => $this->type,
            'icon'           => ! empty($this->icon) ? trim($this->icon) : null,
            'action_url'     => ! empty($this->action_url) ? trim($this->action_url) : null,
            'action_label'   => ! empty($this->action_label) ? trim($this->action_label) : null,
            'is_active'      => (bool) $this->is_active,
            'is_dismissible' => (bool) $this->is_dismissible,
            'priority'       => (int) ($this->priority ?: 0),
            'starts_at'      => ! empty($this->starts_at) ? $this->starts_at : null,
            'expires_at'     => ! empty($this->expires_at) ? $this->expires_at : null,
            'created_by'     => auth()->id(),
        ];

        if ($this->announcement_id) {
            $announcement = Announcement::findOrFail($this->announcement_id);
            $announcement->update($data);
            session()->flash('message', 'Announcement updated successfully.');
        } else {
            Announcement::create($data);
            session()->flash('message', 'New announcement broadcasted successfully.');
        }

        $this->closeModal();
    }

    public function toggleStatus($id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->is_active = ! $announcement->is_active;
        $announcement->save();

        session()->flash('message', "Announcement status changed to " . ($announcement->is_active ? 'Active' : 'Inactive') . ".");
    }

    public function confirmDelete($id)
    {
        $this->deleteConfirmId = $id;
    }

    public function cancelDelete()
    {
        $this->deleteConfirmId = null;
    }

    public function delete()
    {
        if ($this->deleteConfirmId) {
            $announcement = Announcement::findOrFail($this->deleteConfirmId);
            $announcement->delete();
            $this->deleteConfirmId = null;
            session()->flash('message', 'Announcement deleted successfully.');
        }
    }

    public function closeModal()
    {
        $this->resetFields();
        $this->isModalOpen = false;
        $this->resetValidation();
    }

    public function resetFields()
    {
        $this->announcement_id = null;
        $this->title          = '';
        $this->message        = '';
        $this->type           = 'info';
        $this->icon           = 'feather-bell';
        $this->action_url     = '';
        $this->action_label   = '';
        $this->is_active      = true;
        $this->is_dismissible = true;
        $this->priority       = 0;
        $this->starts_at      = null;
        $this->expires_at     = null;
    }

    public function render()
    {
        $query = Announcement::with(['creator', 'dismissals']);

        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('message', 'like', '%' . $this->search . '%');
            });
        }

        if (! empty($this->filterType)) {
            $query->where('type', $this->filterType);
        }

        if ($this->filterStatus === 'active') {
            $query->active();
        } elseif ($this->filterStatus === 'inactive') {
            $query->where('is_active', false);
        } elseif ($this->filterStatus === 'expired') {
            $query->where('expires_at', '<', now());
        }

        $announcements = $query->orderByDesc('priority')
                               ->orderByDesc('created_at')
                               ->paginate(10);

        // Compute metrics
        $stats = [
            'total'       => Announcement::count(),
            'active'      => Announcement::active()->count(),
            'dismissals'  => AnnouncementDismissal::count(),
            'expired'     => Announcement::where('expires_at', '<', now())->count(),
        ];

        return view('livewire.admin.announcement-manager', [
            'announcements' => $announcements,
            'stats'         => $stats,
        ])->layout('layouts.app', ['title' => 'System Announcements']);
    }
}
