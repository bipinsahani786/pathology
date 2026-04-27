<div class="main-content">
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">Help & Support</h4>
            <p class="text-muted small mb-0">Raise a ticket and track our response.</p>
        </div>
        @if($view !== 'create')
            <button wire:click="showCreate" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="feather-plus me-1"></i> New Ticket
            </button>
        @else
            <button wire:click="showList" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="feather-arrow-left me-1"></i> Back to List
            </button>
        @endif
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success border-0 shadow-sm mb-4">
            <i class="feather-check-circle me-2"></i> {{ session('message') }}
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
                                <th class="py-3 text-center">Priority</th>
                                <th class="py-3 text-center">Status</th>
                                <th class="py-3">Created At</th>
                                <th class="pe-4 text-end py-3">Action</th>
                            </tr>
                        </thead>
                        <tbody class="fs-13">
                            @forelse($tickets as $ticket)
                                <tr class="border-bottom border-light">
                                    <td class="ps-4 py-3 fw-bold text-primary">{{ $ticket->ticket_id }}</td>
                                    <td class="py-3">
                                        <div class="fw-medium text-dark">{{ $ticket->subject }}</div>
                                        <div class="fs-11 text-muted">{{ $ticket->category }}</div>
                                    </td>
                                    <td class="py-3 text-center">
                                        @php
                                            $pColors = [
                                                'Low' => 'bg-soft-info text-info',
                                                'Medium' => 'bg-soft-warning text-warning',
                                                'High' => 'bg-soft-danger text-danger',
                                                'Urgent' => 'bg-danger text-white',
                                            ];
                                        @endphp
                                        <span class="badge {{ $pColors[$ticket->priority] ?? 'bg-soft-secondary' }} rounded-pill px-2 fs-10">
                                            {{ $ticket->priority }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-center">
                                        @php
                                            $sColors = [
                                                'Open' => 'bg-soft-primary text-primary',
                                                'In Progress' => 'bg-soft-warning text-warning',
                                                'Resolved' => 'bg-soft-success text-success',
                                                'Closed' => 'bg-soft-secondary text-secondary',
                                            ];
                                        @endphp
                                        <span class="badge {{ $sColors[$ticket->status] ?? 'bg-soft-secondary' }} rounded-pill px-2 fs-10">
                                            {{ $ticket->status }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-muted">
                                        {{ $ticket->created_at->format('d M, Y') }}
                                        <div class="fs-10">{{ $ticket->created_at->diffForHumans() }}</div>
                                    </td>
                                    <td class="pe-4 text-end py-3">
                                        <button wire:click="viewTicket({{ $ticket->id }})" class="btn btn-sm btn-soft-primary rounded-pill px-3 fs-11 fw-bold">
                                            View Chat
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="feather-inbox fs-1 mb-3 d-block opacity-25"></i>
                                        No tickets found. Need help? Raise a new ticket.
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

    @elseif($view === 'create')
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <form wire:submit.prevent="createTicket">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label fw-bold text-dark fs-12">Subject</label>
                                    <input type="text" wire:model="subject" class="form-control rounded-3" placeholder="Briefly describe the issue">
                                    @error('subject') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-dark fs-12">Category</label>
                                    <select wire:model="category" class="form-select rounded-3">
                                        <option value="">Select Category</option>
                                        <option value="Technical Support">Technical Support</option>
                                        <option value="Billing & Payments">Billing & Payments</option>
                                        <option value="Report Correction">Report Correction</option>
                                        <option value="Account Settings">Account Settings</option>
                                        <option value="Other">Other</option>
                                    </select>
                                    @error('category') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-bold text-dark fs-12">Priority</label>
                                    <div class="d-flex gap-2">
                                        @foreach(['Low', 'Medium', 'High', 'Urgent'] as $p)
                                            <input type="radio" class="btn-check" wire:model="priority" value="{{ $p }}" id="p_{{ $p }}">
                                            <label class="btn btn-outline-{{ $p == 'Urgent' ? 'danger' : ($p == 'High' ? 'warning' : 'primary') }} rounded-pill px-3 py-1 fs-11" for="p_{{ $p }}">{{ $p }}</label>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-bold text-dark fs-12">Detailed Description</label>
                                    <textarea wire:model="description" class="form-control rounded-3" rows="5" placeholder="Explain the problem in detail..."></textarea>
                                    @error('description') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-bold text-dark fs-12">Attachment (Screenshot)</label>
                                    <input type="file" wire:model="attachment" class="form-control rounded-3">
                                    <div class="fs-10 text-muted mt-1">Images only, max 2MB.</div>
                                    @error('attachment') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-12 mt-4">
                                    <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-bold shadow-sm">
                                        Submit Support Ticket
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    @elseif($view === 'view' && $selectedTicket)
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="badge bg-soft-primary text-primary rounded-pill px-3 py-1 mb-3 fs-10 fw-bold">{{ $selectedTicket->ticket_id }}</div>
                        <h5 class="fw-bold text-dark mb-3">{{ $selectedTicket->subject }}</h5>
                        <div class="mb-4">
                            <p class="text-muted fs-13 mb-3">{{ $selectedTicket->description }}</p>
                            @if($selectedTicket->attachment)
                                <a href="{{ Storage::disk('r2')->url($selectedTicket->attachment) }}" target="_blank" class="btn btn-sm btn-light border rounded-3 fs-11">
                                    <i class="feather-image me-1"></i> View Attachment
                                </a>
                            @endif
                        </div>
                        <hr class="my-4 border-light">
                        <div class="d-flex flex-column gap-3">
                            <div class="d-flex justify-content-between fs-12">
                                <span class="text-muted">Status</span>
                                <span class="fw-bold text-primary">{{ $selectedTicket->status }}</span>
                            </div>
                            <div class="d-flex justify-content-between fs-12">
                                <span class="text-muted">Priority</span>
                                <span class="fw-bold text-danger">{{ $selectedTicket->priority }}</span>
                            </div>
                            <div class="d-flex justify-content-between fs-12">
                                <span class="text-muted">Category</span>
                                <span class="fw-bold text-dark">{{ $selectedTicket->category }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-bottom py-3 px-4">
                        <h6 class="mb-0 fw-bold text-dark"><i class="feather-message-circle me-2 text-primary"></i>Conversation Thread</h6>
                    </div>
                    <div class="card-body p-4" style="max-height: 500px; overflow-y: auto;">
                        <div class="d-flex flex-column gap-4">
                            @foreach($selectedTicket->messages as $msg)
                                <div class="d-flex {{ $msg->is_admin_reply ? 'justify-content-start' : 'justify-content-end' }}">
                                    <div class="max-w-75">
                                        <div class="p-3 rounded-4 {{ $msg->is_admin_reply ? 'bg-light text-dark' : 'bg-primary text-white shadow-sm' }}" style="border-radius: {{ $msg->is_admin_reply ? '0 20px 20px 20px' : '20px 0 20px 20px' }} !important;">
                                            <div class="fs-13">{{ $msg->message }}</div>
                                            @if($msg->attachment)
                                                <div class="mt-2 pt-2 border-top {{ $msg->is_admin_reply ? 'border-secondary opacity-25' : 'border-white opacity-50' }}">
                                                    <a href="{{ Storage::disk('r2')->url($msg->attachment) }}" target="_blank" class="text-inherit fs-11 fw-bold">
                                                        <i class="feather-paperclip me-1"></i> Attachment
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="fs-10 text-muted mt-1 {{ $msg->is_admin_reply ? '' : 'text-end' }}">
                                            {{ $msg->user->name }} · {{ $msg->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top p-4">
                        <form wire:submit.prevent="sendReply">
                            <div class="input-group">
                                <textarea wire:model="message" class="form-control border-0 bg-light rounded-start-4 fs-13" placeholder="Type your reply..." rows="2"></textarea>
                                <button type="submit" class="btn btn-primary px-4 rounded-end-4 shadow-sm">
                                    <i class="feather-send fs-5"></i>
                                </button>
                            </div>
                            <div class="mt-2 d-flex justify-content-between align-items-center">
                                <input type="file" wire:model="replyAttachment" class="form-control form-control-sm border-0 fs-10" style="width: 200px;">
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
        .bg-soft-info { background-color: rgba(6, 182, 212, 0.1); }
        .bg-soft-warning { background-color: rgba(245, 158, 11, 0.1); }
        .bg-soft-danger { background-color: rgba(220, 53, 69, 0.1); }
        .bg-soft-success { background-color: rgba(16, 185, 129, 0.1); }
        .bg-soft-secondary { background-color: rgba(108, 117, 125, 0.1); }
        .text-inherit { color: inherit; text-decoration: none; }
        .max-w-75 { max-width: 75%; }
    </style>
</div>
