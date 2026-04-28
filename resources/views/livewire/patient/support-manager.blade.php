<div class="main-content">
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">Help & Support</h4>
            <p class="text-muted small mb-0">Need help? Raise a ticket with our support team.</p>
        </div>
        @if($view !== 'list')
            <button wire:click="showList" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="feather-arrow-left me-1"></i> Back to List
            </button>
        @else
            <button wire:click="showCreate" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="feather-plus me-1"></i> New Ticket
            </button>
        @endif
    </div>

    @if(session()->has('message'))
        <div class="alert alert-success border-0 rounded-4 shadow-sm mb-4">
            {{ session('message') }}
        </div>
    @endif

    @if($view === 'list')
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light fs-11 fw-bold text-uppercase text-muted">
                            <tr>
                                <th class="ps-4 py-3">Ticket ID</th>
                                <th class="py-3">Subject</th>
                                <th class="py-3 text-center">Status</th>
                                <th class="py-3 text-center">Last Update</th>
                                <th class="pe-4 text-end py-3">Action</th>
                            </tr>
                        </thead>
                        <tbody class="fs-13">
                            @forelse($tickets as $ticket)
                                <tr class="border-bottom border-light">
                                    <td class="ps-4 py-3 fw-bold text-primary">{{ $ticket->ticket_id }}</td>
                                    <td class="py-3">
                                        <div class="fw-bold text-dark">{{ $ticket->subject }}</div>
                                        <div class="fs-11 text-muted">{{ $ticket->category }}</div>
                                    </td>
                                    <td class="py-3 text-center">
                                        <span class="badge bg-soft-primary text-primary rounded-pill px-2 fs-10">{{ $ticket->status }}</span>
                                    </td>
                                    <td class="py-3 text-center text-muted fs-12">{{ $ticket->updated_at->diffForHumans() }}</td>
                                    <td class="pe-4 text-end py-3">
                                        <button wire:click="viewTicket({{ $ticket->id }})" class="btn btn-sm btn-outline-primary rounded-pill px-3">View</button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center py-5 text-muted">No tickets found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($tickets && $tickets->hasPages())
                <div class="card-footer bg-white border-0 p-3">
                    {{ $tickets->links() }}
                </div>
            @endif
        </div>

    @elseif($view === 'create')
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <form wire:submit.prevent="createTicket">
                            <div class="mb-3">
                                <label class="form-label fw-bold small">Subject</label>
                                <input type="text" wire:model="subject" class="form-control" placeholder="e.g. Issue with report download">
                                @error('subject') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">Category</label>
                                    <select wire:model="category" class="form-select">
                                        <option value="">Select Category</option>
                                        <option value="Report Issue">Report Issue</option>
                                        <option value="Billing">Billing / Payment</option>
                                        <option value="Profile">Profile Update</option>
                                        <option value="Other">Other</option>
                                    </select>
                                    @error('category') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">Priority</label>
                                    <select wire:model="priority" class="form-select">
                                        <option value="Low">Low</option>
                                        <option value="Medium">Medium</option>
                                        <option value="High">High</option>
                                        <option value="Urgent">Urgent</option>
                                    </select>
                                    @error('priority') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold small">Description</label>
                                <textarea wire:model="description" class="form-control" rows="4" placeholder="Describe your issue in detail..."></textarea>
                                @error('description') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold small">Attachment (Optional)</label>
                                <input type="file" wire:model="attachment" class="form-control">
                                <div wire:loading wire:target="attachment" class="mt-2 text-primary small"><i class="feather-loader animate-spin me-1"></i> Uploading...</div>
                                @error('attachment') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-bold shadow-sm">Submit Ticket</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    @elseif($view === 'view' && $selectedTicket)
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <div class="badge bg-soft-primary text-primary mb-3">{{ $selectedTicket->status }}</div>
                        <h5 class="fw-bold text-dark mb-3">{{ $selectedTicket->subject }}</h5>
                        <p class="text-muted fs-13 mb-4">{{ $selectedTicket->description }}</p>
                        @if($selectedTicket->attachment)
                            <a href="{{ Storage::disk('r2')->url($selectedTicket->attachment) }}" target="_blank" class="btn btn-sm btn-light w-100 rounded-pill mb-4">
                                <i class="feather-paperclip me-1"></i> View Attachment
                            </a>
                        @endif
                        <hr class="opacity-10 mb-4">
                        <div class="d-flex flex-column gap-2 fs-12">
                            <div class="d-flex justify-content-between"><span class="text-muted">Ticket ID:</span> <span class="fw-bold">{{ $selectedTicket->ticket_id }}</span></div>
                            <div class="d-flex justify-content-between"><span class="text-muted">Category:</span> <span>{{ $selectedTicket->category }}</span></div>
                            <div class="d-flex justify-content-between"><span class="text-muted">Created:</span> <span>{{ $selectedTicket->created_at->format('M d, Y') }}</span></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-bottom p-4">
                        <h6 class="fw-bold mb-0">Conversation</h6>
                    </div>
                    <div class="card-body p-4" style="max-height: 400px; overflow-y: auto;">
                        <div class="d-flex flex-column gap-3">
                            @forelse($selectedTicket->messages as $msg)
                                <div class="d-flex {{ $msg->user_id === auth()->id() ? 'justify-content-end' : 'justify-content-start' }}">
                                    <div class="p-3 rounded-4 {{ $msg->user_id === auth()->id() ? 'bg-primary text-white' : 'bg-light text-dark' }}" style="max-width: 80%;">
                                        <div class="fs-13">{{ $msg->message }}</div>
                                        @if($msg->attachment)
                                            <div class="mt-2 pt-1 border-top {{ $msg->user_id === auth()->id() ? 'border-white opacity-50' : 'border-light' }}">
                                                <a href="{{ Storage::disk('r2')->url($msg->attachment) }}" target="_blank" class="{{ $msg->user_id === auth()->id() ? 'text-white' : 'text-primary' }} fs-10 fw-bold">View File</a>
                                            </div>
                                        @endif
                                        <div class="fs-10 opacity-75 mt-1 text-end">{{ $msg->created_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4 text-muted">No messages yet.</div>
                            @endforelse
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top p-3">
                        <form wire:submit.prevent="sendReply">
                            <textarea wire:model="message" class="form-control mb-2" rows="2" placeholder="Write your reply..."></textarea>
                            <div class="d-flex justify-content-between align-items-center">
                                <input type="file" wire:model="replyAttachment" class="form-control form-control-sm w-auto border-0 fs-10">
                                <button class="btn btn-primary rounded-pill px-4 fw-bold">Send</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
