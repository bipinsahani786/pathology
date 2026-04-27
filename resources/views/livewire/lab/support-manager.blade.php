<div class="main-content">
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">Help & Support</h4>
            <p class="text-muted small mb-0">Manage partner issues or contact platform support.</p>
        </div>
        <div class="d-flex gap-2">
            @if($tab === 'system' && $view === 'list')
                <button wire:click="showCreateSystem" class="btn btn-primary rounded-pill px-4 shadow-sm">
                    <i class="feather-plus me-1"></i> New System Ticket
                </button>
            @endif
            @if($view !== 'list')
                <button wire:click="showList" class="btn btn-outline-secondary rounded-pill px-4">
                    <i class="feather-arrow-left me-1"></i> Back to List
                </button>
            @endif
        </div>
    </div>

    @if($view === 'list')
        {{-- Tabs --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="d-flex border-bottom">
                    <button wire:click="switchTab('partners')" class="btn flex-grow-1 py-3 border-0 rounded-0 fs-13 fw-bold {{ $tab === 'partners' ? 'bg-primary text-white' : 'bg-white text-muted' }}">
                        <i class="feather-users me-2"></i> Partner Tickets
                    </button>
                    <button wire:click="switchTab('system')" class="btn flex-grow-1 py-3 border-0 rounded-0 fs-13 fw-bold {{ $tab === 'system' ? 'bg-danger text-white' : 'bg-white text-muted' }}">
                        <i class="feather-shield me-2"></i> Platform Support (Contact Superadmin)
                    </button>
                </div>
                <div class="p-3">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Search tickets...">
                        </div>
                        <div class="col-md-3">
                            <select wire:model.live="filterStatus" class="form-select">
                                <option value="">All Status</option>
                                <option value="Open">Open</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Resolved">Resolved</option>
                                <option value="Closed">Closed</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light fs-11 fw-bold text-uppercase text-muted">
                            <tr>
                                <th class="ps-4 py-3">Ticket</th>
                                @if($tab === 'partners') <th class="py-3">Requester</th> @endif
                                <th class="py-3 text-center">Priority</th>
                                <th class="py-3 text-center">Status</th>
                                <th class="py-3">Last Activity</th>
                                <th class="pe-4 text-end py-3">Action</th>
                            </tr>
                        </thead>
                        <tbody class="fs-13">
                            @forelse($tickets as $ticket)
                                <tr class="border-bottom border-light">
                                    <td class="ps-4 py-3">
                                        <div class="fw-bold text-primary">{{ $ticket->ticket_id }}</div>
                                        <div class="fw-medium text-dark">{{ $ticket->subject }}</div>
                                    </td>
                                    @if($tab === 'partners')
                                        <td class="py-3">
                                            <div class="fw-bold text-dark">{{ $ticket->user->name }}</div>
                                            <div class="fs-11 text-muted">{{ $ticket->category }}</div>
                                        </td>
                                    @endif
                                    <td class="py-3 text-center">
                                        <span class="badge bg-soft-warning text-warning rounded-pill px-2 fs-10">{{ $ticket->priority }}</span>
                                    </td>
                                    <td class="py-3 text-center">
                                        <span class="badge bg-soft-primary text-primary rounded-pill px-2 fs-10">{{ $ticket->status }}</span>
                                    </td>
                                    <td class="py-3 text-muted">{{ $ticket->updated_at->diffForHumans() }}</td>
                                    <td class="pe-4 text-end py-3">
                                        <button wire:click="viewTicket({{ $ticket->id }})" class="btn btn-sm btn-soft-primary rounded-pill px-3 fs-11 fw-bold">
                                            {{ $tab === 'partners' ? 'Resolve' : 'View Thread' }}
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        No tickets found in this section.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-top">
                    {{ $tickets->links() }}
                </div>
            </div>
        </div>

    @elseif($view === 'create_system')
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-bottom py-3 px-4">
                        <h6 class="mb-0 fw-bold text-dark">Contact Platform Support</h6>
                    </div>
                    <div class="card-body p-4">
                        <form wire:submit.prevent="createSystemTicket">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label fs-12 fw-bold text-muted text-uppercase">Subject</label>
                                    <input type="text" wire:model="subject" class="form-control" placeholder="What is the issue?">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-12 fw-bold text-muted text-uppercase">Category</label>
                                    <select wire:model="category" class="form-select">
                                        <option value="">Select Category</option>
                                        <option value="Bug Report">Bug Report</option>
                                        <option value="Feature Request">Feature Request</option>
                                        <option value="Billing Issue">Billing Issue</option>
                                        <option value="Account Access">Account Access</option>
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fs-12 fw-bold text-muted text-uppercase">Description</label>
                                    <textarea wire:model="description" class="form-control" rows="4" placeholder="Explain the issue in detail..."></textarea>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fs-12 fw-bold text-muted text-uppercase">Attachment (Screenshot)</label>
                                    <input type="file" wire:model="attachment" class="form-control">
                                    <div wire:loading wire:target="attachment" class="fs-10 text-muted">Uploading...</div>
                                </div>
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-bold">Submit Ticket to Superadmin</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    @elseif($view === 'view' && $selectedTicket)
        {{-- Reusing the chat interface from before but with tab logic --}}
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <div class="badge bg-soft-primary text-primary rounded-pill px-3 py-1 mb-3 fs-10 fw-bold">{{ $selectedTicket->ticket_id }}</div>
                        <h5 class="fw-bold text-dark mb-3">{{ $selectedTicket->subject }}</h5>
                        <p class="text-muted fs-13">{{ $selectedTicket->description }}</p>
                        <hr class="my-4 border-light">
                        <div class="d-flex flex-column gap-2 fs-12">
                            <div class="d-flex justify-content-between"><span class="text-muted">Status:</span> <span class="fw-bold text-primary">{{ $selectedTicket->status }}</span></div>
                            <div class="d-flex justify-content-between"><span class="text-muted">Priority:</span> <span class="fw-bold text-danger">{{ $selectedTicket->priority }}</span></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4" style="height: 450px; overflow-y: auto;">
                        <div class="d-flex flex-column gap-3">
                            @foreach($selectedTicket->messages as $msg)
                                <div class="d-flex {{ $msg->is_admin_reply ? ($tab === 'partners' ? 'justify-content-end' : 'justify-content-start') : ($tab === 'partners' ? 'justify-content-start' : 'justify-content-end') }}">
                                    <div class="p-3 rounded-4 {{ ($msg->is_admin_reply && $tab === 'partners') || (!$msg->is_admin_reply && $tab === 'system') ? 'bg-primary text-white shadow-sm' : 'bg-light text-dark' }}" style="max-width: 80%;">
                                        <div class="fs-13">{{ $msg->message }}</div>
                                        @if($msg->attachment)
                                            <div class="mt-2 pt-1 border-top border-light opacity-50">
                                                <a href="{{ Storage::disk('r2')->url($msg->attachment) }}" target="_blank" class="text-white fs-10 fw-bold">
                                                    <i class="feather-image me-1"></i> Attachment
                                                </a>
                                            </div>
                                        @endif
                                        <div class="fs-10 opacity-75 mt-1">{{ $msg->user->name }} · {{ $msg->created_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top p-3">
                        <form wire:submit.prevent="sendReply">
                            <div class="input-group">
                                <textarea wire:model="message" class="form-control border-0 bg-light rounded-start-4 fs-13" placeholder="Type your reply..." rows="2"></textarea>
                                <button type="submit" class="btn btn-primary px-4 rounded-end-4 shadow-sm">
                                    <i class="feather-send"></i>
                                </button>
                            </div>
                            <div class="mt-2">
                                <input type="file" wire:model="replyAttachment" class="form-control form-control-sm border-0 fs-10" style="width: 250px;">
                                <div wire:loading wire:target="replyAttachment" class="fs-10 text-muted">Uploading...</div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <style>
        .bg-soft-primary { background-color: rgba(59, 113, 202, 0.1); }
        .bg-soft-warning { background-color: rgba(245, 158, 11, 0.1); }
    </style>
</div>
