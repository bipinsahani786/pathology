<div>
    <div class="main-content">
        <div class="container-fluid">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h2 class="fs-20 fw-bolder mb-0">Enquiries <span class="badge bg-danger ms-2">{{ $newCount }} new</span></h2>
                    <p class="text-muted mb-0 fs-12">Manage contact and enquiry form submissions</p>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show"><i class="feather-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body py-3">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Search by name, email, lab...">
                        </div>
                        <div class="col-md-3">
                            <select wire:model.live="filterStatus" class="form-select">
                                <option value="">All Status</option>
                                <option value="new">🟢 New</option>
                                <option value="contacted">🟡 Contacted</option>
                                <option value="converted">🔵 Converted</option>
                                <option value="closed">⚫ Closed</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select wire:model.live="filterType" class="form-select">
                                <option value="">All Types</option>
                                <option value="contact">Contact Form</option>
                                <option value="enquiry">Enquiry Form</option>
                                <option value="demo_request">Demo Request</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="card">
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Contact</th>
                                <th>Lab / City</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($enquiries as $e)
                                <tr class="{{ $e->status === 'new' ? 'table-warning' : '' }}">
                                    <td><strong>{{ $e->name }}</strong></td>
                                    <td>
                                        <small>{{ $e->email }}</small>
                                        @if($e->phone)<br><small class="text-muted">{{ $e->phone }}</small>@endif
                                    </td>
                                    <td>
                                        @if($e->lab_name) {{ $e->lab_name }} @endif
                                        @if($e->lab_city) <br><small class="text-muted">{{ $e->lab_city }}</small> @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $e->enquiry_type === 'contact' ? 'bg-soft-primary text-primary' : ($e->enquiry_type === 'enquiry' ? 'bg-soft-warning text-warning' : 'bg-soft-info text-info') }}">
                                            {{ ucfirst($e->enquiry_type) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $e->status === 'new' ? 'bg-success' : ($e->status === 'contacted' ? 'bg-warning' : ($e->status === 'converted' ? 'bg-primary' : 'bg-secondary')) }}">
                                            {{ ucfirst($e->status) }}
                                        </span>
                                    </td>
                                    <td><small>{{ $e->created_at->format('d M Y') }}</small><br><small class="text-muted">{{ $e->created_at->diffForHumans() }}</small></td>
                                    <td>
                                        <button wire:click="viewEnquiry({{ $e->id }})" class="btn btn-sm btn-outline-primary"><i class="feather-eye"></i></button>
                                        <button wire:click="deleteEnquiry({{ $e->id }})" wire:confirm="Delete this enquiry?" class="btn btn-sm btn-outline-danger"><i class="feather-trash-2"></i></button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center py-4 text-muted">No enquiries found</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">{{ $enquiries->links() }}</div>
            </div>

            <!-- Detail Modal -->
            @if($viewingId)
                @php $e = \App\Models\Enquiry::find($viewingId); @endphp
                @if($e)
                    <div class="modal show d-block" style="background: rgba(0,0,0,0.5);">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Enquiry from {{ $e->name }}</h5>
                                    <button type="button" class="btn-close" wire:click="$set('viewingId', null)"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row g-3 mb-4">
                                        <div class="col-md-4"><strong>Name:</strong><br>{{ $e->name }}</div>
                                        <div class="col-md-4"><strong>Email:</strong><br>{{ $e->email }}</div>
                                        <div class="col-md-4"><strong>Phone:</strong><br>{{ $e->phone ?? 'N/A' }}</div>
                                        @if($e->lab_name)<div class="col-md-4"><strong>Lab:</strong><br>{{ $e->lab_name }}</div>@endif
                                        @if($e->lab_city)<div class="col-md-4"><strong>City:</strong><br>{{ $e->lab_city }}</div>@endif
                                        <div class="col-md-4"><strong>Type:</strong><br><span class="badge bg-primary">{{ ucfirst($e->enquiry_type) }}</span></div>
                                    </div>
                                    @if($e->message)
                                        <div class="mb-4">
                                            <strong>Message:</strong>
                                            <div class="bg-light p-3 rounded mt-2">{!! nl2br(e($e->message)) !!}</div>
                                        </div>
                                    @endif
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">Status</label>
                                            <select wire:model="statusUpdate" class="form-select">
                                                <option value="new">New</option>
                                                <option value="contacted">Contacted</option>
                                                <option value="converted">Converted</option>
                                                <option value="closed">Closed</option>
                                            </select>
                                        </div>
                                        <div class="col-md-8">
                                            <label class="form-label fw-bold">Admin Notes</label>
                                            <textarea wire:model="adminNotes" class="form-control" rows="3" placeholder="Internal notes..."></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button wire:click="updateEnquiry" class="btn btn-primary"><i class="feather-save me-1"></i> Update</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
