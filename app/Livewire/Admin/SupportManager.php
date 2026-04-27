<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\SupportTicket;
use App\Models\TicketMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SupportManager extends Component
{
    use WithPagination, WithFileUploads;

    public $view = 'list';
    public $selectedTicket;
    
    public $filterStatus = '';
    public $filterPriority = '';
    public $search = '';

    public $message, $replyAttachment;

    protected $paginationTheme = 'bootstrap';

    public function showList()
    {
        $this->view = 'list';
        $this->selectedTicket = null;
    }

    public function viewTicket($id)
    {
        $this->selectedTicket = SupportTicket::with(['messages.user', 'user', 'company'])->findOrFail($id);
        $this->view = 'view';
        $this->reset(['message', 'replyAttachment']);
    }

    public function sendReply()
    {
        $this->validate([
            'message' => 'required|string',
            'replyAttachment' => 'nullable|image|max:2048',
        ]);

        $path = null;
        if ($this->replyAttachment) {
            $path = $this->replyAttachment->store('tickets', 'r2');
        }

        TicketMessage::create([
            'support_ticket_id' => $this->selectedTicket->id,
            'user_id' => Auth::id(),
            'message' => $this->message,
            'attachment' => $path,
            'is_admin_reply' => true,
        ]);

        if ($this->selectedTicket->status === 'Open') {
            $this->selectedTicket->update(['status' => 'In Progress']);
        }

        $this->viewTicket($this->selectedTicket->id);
        $this->reset(['message', 'replyAttachment']);
    }

    public function updateStatus($status)
    {
        $this->selectedTicket->update(['status' => $status]);
        session()->flash('message', 'Ticket status updated.');
    }

    public function render()
    {
        $query = SupportTicket::with(['user', 'company'])
            ->where('is_system_ticket', true);

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }
        if ($this->filterPriority) {
            $query->where('priority', $this->filterPriority);
        }
        if ($this->search) {
            $s = $this->search;
            $query->where(function($q) use ($s) {
                $q->where('ticket_id', 'like', "%{$s}%")
                  ->orWhere('subject', 'like', "%{$s}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$s}%"))
                  ->orWhereHas('company', fn($c) => $c->where('name', 'like', "%{$s}%"));
            });
        }

        $tickets = $query->latest()->paginate(15);

        return view('livewire.admin.support-manager', [
            'tickets' => $tickets
        ])->layout('layouts.admin', ['title' => 'System Support Tickets']);
    }
}
