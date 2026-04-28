<?php

namespace App\Livewire\Patient;

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

    public $view = 'list'; // list, create, view
    public $selectedTicket;
    
    // New Ticket Form
    public $subject, $category, $priority = 'Medium', $description, $attachment;
    
    // Reply Form
    public $message, $replyAttachment;

    protected $paginationTheme = 'bootstrap';

    public function showCreate()
    {
        $this->reset(['subject', 'category', 'priority', 'description', 'attachment']);
        $this->view = 'create';
    }

    public function showList()
    {
        $this->view = 'list';
        $this->selectedTicket = null;
    }

    public function createTicket()
    {
        $this->validate([
            'subject' => 'required|string|max:255',
            'category' => 'required|string',
            'priority' => 'required|in:Low,Medium,High,Urgent',
            'description' => 'required|string',
            'attachment' => 'nullable|image|max:2048',
        ]);

        $user = Auth::user();
        $ticketId = 'PAT-' . strtoupper(bin2hex(random_bytes(3)));

        $path = null;
        if ($this->attachment) {
            $path = $this->attachment->store('tickets', 'r2');
        }

        $ticket = SupportTicket::create([
            'ticket_id' => $ticketId,
            'user_id' => $user->id,
            'company_id' => $user->company_id,
            'subject' => $this->subject,
            'category' => $this->category,
            'priority' => $this->priority,
            'status' => 'Open',
            'description' => $this->description,
            'attachment' => $path,
            'is_system_ticket' => false, // Patient tickets go to their Lab
        ]);

        session()->flash('message', 'Ticket ' . $ticketId . ' created successfully.');
        $this->viewTicket($ticket->id);
    }

    public function viewTicket($id)
    {
        $this->selectedTicket = SupportTicket::with('messages.user')->where('user_id', Auth::id())->findOrFail($id);
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
            'is_admin_reply' => false,
        ]);

        if ($this->selectedTicket->status === 'Resolved' || $this->selectedTicket->status === 'Closed') {
            $this->selectedTicket->update(['status' => 'Open']);
        }

        $this->viewTicket($this->selectedTicket->id);
        $this->reset(['message', 'replyAttachment']);
    }

    public function render()
    {
        $tickets = [];
        if ($this->view === 'list') {
            $tickets = SupportTicket::where('user_id', Auth::id())
                ->latest()
                ->paginate(10);
        }

        return view('livewire.patient.support-manager', [
            'tickets' => $tickets
        ])->layout('layouts.app', ['title' => 'Support & Help']);
    }
}
