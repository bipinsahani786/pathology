<div>
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="text-dark fw-bold">Sales Agents</h5>
            </div>
            <ul class="breadcrumb d-none d-md-flex ms-3">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" wire:navigate class="text-muted">Home</a></li>
                <li class="breadcrumb-item text-primary fw-medium">Sales Network</li>
            </ul>
        </div>
        <div class="page-header-right">
            <button class="btn btn-primary px-4 shadow-sm" wire:click="openModal">
                <i class="feather-plus me-2"></i>Add New Agent
            </button>
        </div>
    </div>

    <div class="main-content mt-4">
        @if (session()->has('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-3 d-flex align-items-center py-3 mb-4">
                <i class="feather-check-circle fs-4 text-success me-3"></i>
                <div class="fw-bold">{{ session('success') }}</div>
                <button type="button" class="btn-close ms-auto shadow-none" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Filter Card -->
        <div class="card stretch stretch-full border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 border-bottom-0">
                <div class="row g-3 align-items-center">
                    <div class="col-md-6">
                        <div class="input-group search-group shadow-sm" style="height: 48px;">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="feather-search text-primary"></i>
                            </span>
                            <input type="text" wire:model.live.debounce.300ms="searchTerm" 
                                class="form-control border-start-0 ps-0 shadow-none" 
                                placeholder="Search by name, email or mobile...">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="card-body p-0">
                <div class="table-responsive border-top">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead class="bg-light fs-11 text-uppercase text-muted fw-bold">
                            <tr>
                                <th class="ps-4">Agent Name</th>
                                <th>Contact Info</th>
                                <th class="text-center">Commission</th>
                                <th class="text-center">Earnings</th>
                                <th class="text-center">Paid</th>
                                <th class="text-center">Balance</th>
                                <th class="text-center">Active Labs</th>
                                <th class="text-center">Status</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($agents as $agent)
                                <tr class="border-bottom border-light">
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-text avatar-md bg-soft-info text-info me-3 rounded-circle fw-bold text-center" style="width: 42px; height: 42px; line-height: 42px;">
                                                {{ substr($agent->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark fs-14 mb-0">{{ $agent->name }}</div>
                                                <div class="text-muted fs-11">ID: #{{ $agent->id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fs-13 text-dark fw-medium">{{ $agent->email ?? 'No Email' }}</div>
                                        <div class="text-muted fs-11">{{ $agent->phone ?? 'No Phone' }}</div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-soft-success text-success px-3 py-1 fs-12">{{ $agent->commission_rate }}%</span>
                                    </td>
                                    <td class="text-center fw-bold text-dark">₹{{ number_format($agent->total_earnings, 2) }}</td>
                                    <td class="text-center text-danger">₹{{ number_format($agent->total_paid, 2) }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $agent->balance > 0 ? 'soft-warning text-warning' : 'light text-muted' }} px-3 py-1">₹{{ number_format($agent->balance, 2) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-bold text-dark">{{ $agent->companies_count }} Labs</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="form-check form-switch d-inline-block">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                wire:click="toggleStatus({{ $agent->id }})" 
                                                {{ $agent->status === 'active' ? 'checked' : '' }} style="cursor: pointer;">
                                        </div>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="hstack gap-2 justify-content-end">
                                            @if($agent->balance > 0)
                                            <button class="btn btn-sm btn-icon btn-soft-success" wire:click="openPayoutModal({{ $agent->id }})" title="Record Payout">
                                                <i class="feather-dollar-sign"></i>
                                            </button>
                                            @endif
                                            <button class="btn btn-sm btn-icon btn-soft-info" wire:click="editAgent({{ $agent->id }})" title="Edit Agent">
                                                <i class="feather-edit-2"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5 text-muted bg-light/30">No agents found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="card-footer bg-white border-top border-light py-3 px-4">
                {{ $agents->links() }}
            </div>
        </div>
    </div>

    <!-- Agent Modal -->
    @if($isModalOpen)
    <div class="modal fade show d-block" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">{{ $agentId ? 'Edit Sales Agent' : 'Add New Sales Agent' }}</h5>
                    <button type="button" class="btn-close" wire:click="$set('isModalOpen', false)"></button>
                </div>
                <div class="modal-body p-4">
                    <form wire:submit.prevent="saveAgent">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model="name" placeholder="E.g. Rajesh Kumar">
                            @error('name') <span class="text-danger fs-11">{{ $message }}</span> @enderror
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Email Address</label>
                                <input type="email" class="form-control" wire:model="email" placeholder="agent@example.com">
                                @error('email') <span class="text-danger fs-11">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Phone Number</label>
                                <input type="text" class="form-control" wire:model="phone" placeholder="9876543210">
                                @error('phone') <span class="text-danger fs-11">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Commission Rate (%)</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control" wire:model="commissionRate">
                                    <span class="input-group-text">%</span>
                                </div>
                                @error('commissionRate') <span class="text-danger fs-11">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Status</label>
                                <select class="form-select" wire:model="status">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                @error('status') <span class="text-danger fs-11">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                            <button type="button" class="btn btn-light" wire:click="$set('isModalOpen', false)">Cancel</button>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="feather-save me-2"></i>{{ $agentId ? 'Update Agent' : 'Save Agent' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Payout Modal -->
    @if($isPayoutModalOpen)
    <div class="modal fade show d-block" style="background: rgba(0,0,0,0.5); z-index: 1052;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Record Payout</h5>
                    <button type="button" class="btn-close" wire:click="$set('isPayoutModalOpen', false)"></button>
                </div>
                <div class="modal-body p-4">
                    <form wire:submit.prevent="savePayout">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Amount to Pay (INR) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control" wire:model="payoutAmount">
                            @error('payoutAmount') <span class="text-danger fs-11">{{ $message }}</span> @enderror
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Payment Method</label>
                                <select class="form-select" wire:model="payoutMethod">
                                    <option value="Bank Transfer">Bank Transfer</option>
                                    <option value="Cash">Cash</option>
                                    <option value="UPI">UPI</option>
                                    <option value="Cheque">Cheque</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Reference ID</label>
                                <input type="text" class="form-control" wire:model="payoutReference" placeholder="TXN-123456">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Notes</label>
                            <textarea class="form-control" wire:model="payoutNotes" rows="2" placeholder="Any additional details..."></textarea>
                        </div>
                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                            <button type="button" class="btn btn-light" wire:click="$set('isPayoutModalOpen', false)">Cancel</button>
                            <button type="submit" class="btn btn-primary px-4">Confirm Payout</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

    <style>
        .bg-soft-info { background-color: rgba(63, 194, 255, 0.1); }
        .bg-soft-success { background-color: rgba(25, 135, 84, 0.1); }
        .fs-11 { font-size: 11px; }
        .fs-14 { font-size: 14px; }
    </style>
</div>
