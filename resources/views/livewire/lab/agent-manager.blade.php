<div>
    @php
        $dashboardRoute = auth()->user()->hasRole('collection_center') ? 'partner.dashboard' : 'lab.dashboard';
    @endphp
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="text-dark fw-bold">Referral Agents & Partners</h5>
                <p class="fs-13 text-muted mb-0">Manage third-party agents.</p>
            </div>
            <ul class="breadcrumb d-none d-md-flex ms-3">
                <li class="breadcrumb-item"><a href="{{ route($dashboardRoute) }}" wire:navigate class="text-muted">Home</a></li>
                <li class="breadcrumb-item text-primary fw-medium">Agents</li>
            </ul>
        </div>
        <div class="page-header-right d-flex gap-2">
            @can('create agents')
                <button wire:click="create" class="btn btn-primary btn-sm shadow-sm d-flex align-items-center transition-all hover-lift">
                    <i class="feather-user-plus me-1"></i> Add New Agent
                </button>
            @endcan
        </div>
    </div>

    <div class="main-content">
        
        @if (session()->has('message'))
            <div class="alert alert-success border-0 shadow-sm rounded-3 d-flex align-items-center py-3 alert-dismissible fade show">
                <i class="feather-check-circle fs-4 me-2"></i>
                <strong>{{ session('message') }}</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-danger border-0 shadow-sm rounded-3 d-flex align-items-center py-3 alert-dismissible fade show">
                <i class="feather-alert-triangle fs-4 me-2"></i>
                <strong>{{ session('error') }}</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card stretch stretch-full border-0 shadow-sm rounded-4 overflow-hidden">
            
            <div class="card-header bg-white py-3 border-bottom-0">
                <div class="row g-3">
                    <div class="col-md-10">
                        <div class="input-group search-group shadow-sm">
                            <span class="input-group-text">
                                <i class="feather-search text-primary"></i>
                            </span>
                            <input type="text" wire:model.live.debounce.300ms="searchTerm" 
                                class="form-control" 
                                placeholder="Search by name, phone, or agency...">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive border-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light fs-11 fw-bold text-uppercase text-muted">
                            <tr>
                                <th class="ps-4 py-3">Agent Name</th>
                                <th class="py-3">Agency & Contact</th>
                                <th class="py-3">Commission Cut</th>
                                <th class="text-center py-3">Status</th>
                                <th class="text-center pe-4 py-3" style="width: 120px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($agents as $agent)
                                <tr wire:key="agent-{{ $agent->id }}" class="border-bottom border-light">
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="bg-soft-warning text-warning rounded-circle d-flex align-items-center justify-content-center fw-bold fs-5 shadow-sm" style="width: 45px; height: 45px;">
                                                <i class="feather-briefcase"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark fs-14">{{ $agent->name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fs-13 text-dark fw-bold mb-1"><i class="feather-home me-1 text-muted"></i>{{ $agent->agentProfile->agency_name ?? 'Individual Agent' }}</div>
                                        <div class="fs-12 text-muted">
                                            <i class="feather-phone me-1"></i>{{ $agent->phone }}
                                        </div>
                                    </td>
                                    <td>
                                        @if($agent->agentProfile->commission_percentage > 0)
                                            <span class="badge bg-soft-success text-success border border-success px-3 py-2 fs-12 shadow-sm">
                                                <i class="feather-percent me-1"></i>{{ number_format($agent->agentProfile->commission_percentage, 1) }} %
                                            </span>
                                        @else
                                            <span class="badge bg-soft-secondary text-secondary border px-3 py-2 fs-12">No Commission</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="form-check form-switch d-flex justify-content-center p-0">
                                            <input class="form-check-input ms-0 cursor-pointer" type="checkbox" role="switch" 
                                                wire:click="toggleStatus({{ $agent->id }})" 
                                                {{ $agent->is_active ? 'checked' : '' }} 
                                                style="width: 40px; height: 20px;"
                                                @cannot('edit agents') disabled @endcannot>
                                        </div>
                                        <span class="fs-10 fw-bold text-uppercase ls-1 {{ $agent->is_active ? 'text-success' : 'text-danger' }}">
                                            {{ $agent->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="text-center pe-4">
                                        <div class="d-flex justify-content-center gap-2">
                                            @can('edit agents')
                                                <button wire:click="edit({{ $agent->id }})" class="btn btn-sm btn-light border text-primary shadow-sm rounded align-center-btn transition-all hover-primary" title="Edit Agent">
                                                    <i class="feather-edit-2 fs-14"></i>
                                                </button>
                                            @endcan
                                            @can('delete agents')
                                                <button wire:click="delete({{ $agent->id }})" wire:confirm="Delete this agent? This will also remove their profile." class="btn btn-sm btn-light border text-danger shadow-sm rounded align-center-btn transition-all hover-danger" title="Delete Agent">
                                                    <i class="feather-trash-2 fs-14"></i>
                                                </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div class="text-muted mb-3"><i class="feather-briefcase" style="font-size: 3.5rem; opacity: 0.5;"></i></div>
                                        <h6 class="fw-bold text-dark">No Agents Found</h6>
                                        <p class="text-muted fs-13">Add B2B partners and external agents to manage their payouts.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white border-top border-light py-3">
                {{ $agents->links() }}
            </div>
        </div>
    </div>

    @if ($isModalOpen)
        <div class="modal-backdrop fade show" style="z-index: 1040;"></div>
        <div class="modal fade show d-block" tabindex="-1" style="z-index: 1050;">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg rounded-4">

                    <div class="modal-header bg-light border-bottom p-4">
                        <h5 class="modal-title fw-bold text-dark d-flex align-items-center">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                                <i class="feather-briefcase fs-5"></i>
                            </div>
                            {{ $user_id ? 'Update Agent Profile' : 'Register New Agent' }}
                        </h5>
                        <button type="button" wire:click="closeModal" class="btn-close shadow-none"></button>
                    </div>

                    <form wire:submit.prevent="store">
                        <div class="modal-body p-4 bg-white" style="max-height: 70vh; overflow-y: auto;">
                            <!-- Login Instructions Alert -->
                            <div class="alert alert-soft-warning border-warning shadow-sm rounded-3 mb-4">
                                <div class="d-flex gap-2">
                                    <i class="feather-info flex-shrink-0 mt-1"></i>
                                    <div>
                                        <h6 class="fw-bold mb-1">Login Instructions</h6>
                                        <p class="fs-12 mb-0 opacity-75">
                                            • <strong>Username:</strong> Mobile, Email, or Name (if both missing).<br>
                                            • <strong>Default Password:</strong> Mobile number, or <code>password123</code> if mobile is missing.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-4">
                            <div class="col-12"><h6 class="fw-bold text-primary mb-0 border-bottom pb-2">Agent Information</h6></div>
                            
                            <div class="col-md-6">
                                <label class="form-label fs-12 fw-bold text-muted text-uppercase">Agent Name *</label>
                                <input type="text" class="form-control fw-medium text-dark" wire:model="name" placeholder="e.g. Amit Singh">
                                @error('name') <span class="text-danger fs-11 fw-bold">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fs-12 fw-bold text-muted text-uppercase">Mobile Number</label>
                                <input type="number" class="form-control" wire:model="phone" placeholder="10-digit mobile number">
                                @error('phone') <span class="text-danger fs-11 fw-bold">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fs-12 fw-bold text-muted text-uppercase">Email Address</label>
                                <input type="email" class="form-control" wire:model="email" placeholder="agent@example.com">
                                @error('email') <span class="text-danger fs-11 fw-bold">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fs-12 fw-bold text-muted text-uppercase">Agency Name</label>
                                <input type="text" class="form-control" wire:model="agency_name" placeholder="e.g. Life Care Agency">
                                @error('agency_name') <span class="text-danger fs-11 fw-bold">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fs-12 fw-bold text-muted text-uppercase">{{ $user_id ? 'Update Password' : 'Login Password' }}</label>
                                <input type="text" class="form-control border-warning bg-soft-warning" wire:model="password" placeholder="{{ $user_id ? 'Leave blank to keep current' : 'Default is mobile number' }}">
                                @error('password') <span class="text-danger fs-11 fw-bold">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-12 mt-4"><h6 class="fw-bold text-primary mb-0 border-bottom pb-2">Business & Payout</h6></div>

                            <div class="col-md-6">
                                <label class="form-label fs-12 fw-bold text-muted text-uppercase">Referral Commission (%) *</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control border-primary bg-soft-primary fw-bold text-primary" wire:model="commission_percentage" placeholder="e.g. 15">
                                    <span class="input-group-text bg-light">%</span>
                                </div>
                                <div class="form-text fs-11 text-muted">Leave as 0 if this agent operates without a cut.</div>
                                @error('commission_percentage') <span class="text-danger fs-11 fw-bold">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light border-top p-3 d-flex justify-content-end gap-2">
                        <button type="button" wire:click="closeModal" class="btn btn-light border px-4 fw-medium shadow-sm">Cancel</button>
                        <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm d-flex align-items-center transition-all hover-lift">
                            <div wire:loading.remove wire:target="store"><i class="feather-save me-2"></i> Save Agent</div>
                            <div wire:loading wire:target="store"><span class="spinner-border spinner-border-sm me-2" role="status"></span> Saving...</div>
                        </button>
                    </div>
                </form>

                </div>
            </div>
        </div>
    @endif

    <style>
        .bg-soft-primary { background-color: rgba(59, 113, 202, 0.08) !important; }
        .bg-soft-warning { background-color: rgba(255, 193, 7, 0.08) !important; }
        .bg-soft-success { background-color: rgba(25, 135, 84, 0.12) !important; }
        .text-primary { color: #3b71ca !important; }
        
        .transition-all { transition: all 0.2s ease-in-out; }
        .hover-lift:hover { transform: translateY(-1px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; }
        
        input.form-control:focus, select.form-select:focus {
            background-color: #ffffff !important;
            border-color: #3b71ca !important;
            box-shadow: 0 4px 15px rgba(59, 113, 202, 0.08), 0 0 0 0.25rem rgba(59, 113, 202, 0.15) !important;
        }

        .hover-primary:hover { background-color: #3b71ca !important; color: #fff !important; border-color: #3b71ca !important; }
        .hover-danger:hover { background-color: #dc3545 !important; color: #fff !important; border-color: #dc3545 !important; }

        .align-center-btn {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 32px; height: 32px; padding: 0 !important;
        }
    </style>
</div>