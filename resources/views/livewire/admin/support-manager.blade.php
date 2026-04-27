<div class="main-content">
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">System Support</h4>
            <p class="text-muted small mb-0">Manage tickets from Lab Administrators.</p>
        </div>
        @if($view === 'view')
            <button wire:click="showList" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="feather-arrow-left me-1"></i> Back to List
            </button>
        @endif
    </div>

    @if($view === 'list')
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-3">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="input-group search-group">
                            <span class="input-group-text bg-white border-end-0"><i class="feather-search text-muted"></i></span>
                            <input type="text" wire:model.live.debounce.300ms="search" class="form-control border-start-0" placeholder="Search ID, Lab, or Subject...">
                        </div>
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

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light fs-11 fw-bold text-uppercase text-muted">
                            <tr>
                                <th class="ps-4 py-3">Ticket</th>
                                <th class="py-3">Lab / Admin</th>
                                <th class="py-3 text-center">Priority</th>
                                <th class="py-3 text-center">Status</th>
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
                                    <td class="py-3">
                                        <div class="fw-bold text-dark">{{ $ticket->company->name ?? 'N/A' }}</div>
                                        <div class="fs-11 text-muted">{{ $ticket->user->name }}</div>
                                    </td>
                                    <td class="py-3 text-center">
                                        <span class="badge bg-soft-warning text-warning rounded-pill px-2 fs-10">{{ $ticket->priority }}</span>
                                    </td>
                                    <td class="py-3 text-center">
                                        <span class="badge bg-soft-primary text-primary rounded-pill px-2 fs-10">{{ $ticket->status }}</span>
                                    </td>
                                    <td class="pe-4 text-end py-3">
                                        <button wire:click="viewTicket({{ $ticket->id }})" class="btn btn-sm btn-primary rounded-pill px-3">Reply</button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center py-5">No system tickets.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    @elseif($view === 'view' && $selectedTicket)
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-muted text-uppercase fs-10 mb-3">Ticket Information</h6>
                        <h5 class="fw-bold text-dark mb-3">{{ $selectedTicket->subject }}</h5>
                        <div class="p-3 bg-light rounded-3 mb-4 fs-13">{{ $selectedTicket->description }}</div>
                        <div class="d-flex flex-column gap-2 fs-12">
                            <div class="d-flex justify-content-between"><span class="text-muted">Lab:</span> <span class="fw-bold">{{ $selectedTicket->company->name ?? 'N/A' }}</span></div>
                            <div class="d-flex justify-content-between"><span class="text-muted">Admin:</span> <span>{{ $selectedTicket->user->name }}</span></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4" style="height: 400px; overflow-y: auto;">
                        <div class="d-flex flex-column gap-3">
                            @foreach($selectedTicket->messages as $msg)
                                <div class="d-flex {{ $msg->is_admin_reply ? 'justify-content-end' : 'justify-content-start' }}">
                                    <div class="p-3 rounded-4 {{ $msg->is_admin_reply ? 'bg-primary text-white' : 'bg-light text-dark' }}" style="max-width: 80%;">
                                        <div class="fs-13">{{ $msg->message }}</div>
                                        @if($msg->attachment)
                                            <div class="mt-2 pt-1 border-top {{ $msg->is_admin_reply ? 'border-white opacity-50' : 'border-light opacity-50' }}">
                                                <a href="{{ Storage::disk('r2')->url($msg->attachment) }}" target="_blank" class="{{ $msg->is_admin_reply ? 'text-white' : 'text-primary' }} fs-10 fw-bold text-decoration-none">
                                                    <i class="feather-image me-1"></i> View Attachment
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
                            <textarea wire:model="message" class="form-control mb-2" rows="2" placeholder="Write a response..."></textarea>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <input type="file" wire:model="replyAttachment" class="form-control form-control-sm w-auto border-0 fs-10">
                                    <div wire:loading wire:target="replyAttachment" class="spinner-border spinner-border-sm text-primary ms-2" role="status"></div>
                                </div>
                                <button class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">Send Reply</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
