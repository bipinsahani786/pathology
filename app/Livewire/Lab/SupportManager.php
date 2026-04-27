<?php

namespace App\Livewire\Lab;

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

    public $view = 'list'; // list, view, create_system
    public $tab = 'partners'; // partners, system
    public $selectedTicket;
    
    // Filters
    public $filterStatus = '';
    public $filterPriority = '';
    public $search = '';

    // Reply Form
    public $message, $replyAttachment;

    // Create System Ticket Form
    public $subject, $category, $priority = 'Medium', $description, $attachment;

    protected $paginationTheme = 'bootstrap';

    public function switchTab($tab)
    {
        $this->tab = $tab;
        $this->view = 'list';
        $this->selectedTicket = null;
        $this->resetPage();
    }

    public function showList()
    {
        $this->view = 'list';
        $this->selectedTicket = null;
    }

    public function showCreateSystem()
    {
        $this->view = 'create_system';
        $this->reset(['subject', 'category', 'priority', 'description', 'attachment']);
    }

    public function viewTicket($id)
    {
        $this->selectedTicket = SupportTicket::with(['messages.user', 'user'])->findOrFail($id);
        $this->view = 'view';
        $this->reset(['message', 'replyAttachment']);
    }

    public function createSystemTicket()
    {
        $this->validate([
            'subject' => 'required|string|max:255',
            'category' => 'required|string',
            'description' => 'required|string',
        ]);

        $ticketId = 'SYS-' . strtoupper(bin2hex(random_bytes(3)));

        $path = null;
        if ($this->attachment) {
            $path = $this->attachment->store('tickets', 'r2');
        }

        $ticket = SupportTicket::create([
            'ticket_id' => $ticketId,
            'user_id' => Auth::id(),
            'company_id' => Auth::user()->company_id,
            'subject' => $this->subject,
            'category' => $this->category,
            'priority' => $this->priority,
            'status' => 'Open',
            'is_system_ticket' => true,
            'description' => $this->description,
            'attachment' => $path,
        ]);

        session()->flash('message', 'Platform support ticket created.');
        $this->viewTicket($ticket->id);
    }

    public function sendReply()
    {
        $this->validate(['message' => 'required|string']);

        $path = null;
        if ($this->replyAttachment) {
            $path = $this->replyAttachment->store('tickets', 'r2');
        }

        // Logic depends on if we are responding to a partner OR responding to superadmin
        $isAdminReply = ($this->tab === 'partners'); 

        TicketMessage::create([
            'support_ticket_id' => $this->selectedTicket->id,
            'user_id' => Auth::id(),
            'message' => $this->message,
            'attachment' => $path,
            'is_admin_reply' => $isAdminReply,
        ]);

        if ($this->tab === 'partners' && $this->selectedTicket->status === 'Open') {
            $this->selectedTicket->update(['status' => 'In Progress']);
        }

        $this->viewTicket($this->selectedTicket->id);
        $this->reset(['message', 'replyAttachment']);
    }

    public function updateStatus($status)
    {
        $this->selectedTicket->update(['status' => $status]);
    }

    public function render()
    {
        $query = SupportTicket::with('user');

        if ($this->tab === 'partners') {
            $query->where('company_id', Auth::user()->company_id)->where('is_system_ticket', false);
        } else {
            $query->where('user_id', Auth::id())->where('is_system_ticket', true);
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }
        if ($this->search) {
            $s = $this->search;
            $query->where(function($q) use ($s) {
                $q->where('ticket_id', 'like', "%{$s}%")->orWhere('subject', 'like', "%{$s}%");
            });
        }

        return view('livewire.lab.support-manager', [
            'tickets' => $query->latest()->paginate(15)
        ])->layout('layouts.app', ['title' => 'Support Tickets']);
    }
}
