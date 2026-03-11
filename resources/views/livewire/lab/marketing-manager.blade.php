<div>
    <div class="page-header d-flex flex-wrap align-items-center justify-content-between gap-2 gap-md-3">
        <div class="page-header-left d-flex align-items-center flex-wrap">
            <div class="page-header-title">
                <h5 class="m-b-10 text-dark fw-bold">Marketing & Offers</h5>
                {{-- <p class="fs-13 text-muted mb-0">Create subscription plans (Health Cards) or generate discount codes (Promo Codes) to boost patient retention.</p> --}}
            </div>
            <ul class="breadcrumb d-none d-md-flex mb-0 ms-3">
                <li class="breadcrumb-item"><a href="{{ route('lab.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Marketing</li>
            </ul>
        </div>
        <div class="page-header-right d-flex gap-2">
            @if($activeTab === 'memberships')
                <button wire:click="createMembership" class="btn btn-primary btn-sm shadow-sm d-flex align-items-center">
                    <i class="feather-plus me-1"></i> New Membership
                </button>
            @else
                <button wire:click="createVoucher" class="btn btn-primary btn-sm shadow-sm d-flex align-items-center">
                    <i class="feather-plus me-1"></i> New Promo Code
                </button>
            @endif
        </div>
    </div>

    <div class="main-content mt-4">
        @if (session()->has('message'))
            <div class="alert alert-success border-0 shadow-sm rounded-3 d-flex align-items-center py-3">
                <i class="feather-check-circle fs-4 me-2"></i>
                <strong>{{ session('message') }}</strong>
            </div>
        @endif

        <div class="nav nav-pills bg-white p-2 rounded-pill shadow-sm mb-4 d-inline-flex gap-2 border">
            <button wire:click="switchTab('memberships')" class="nav-link rounded-pill px-4 fw-semibold d-flex align-items-center {{ $activeTab === 'memberships' ? 'active' : 'text-dark' }}">
                <i class="feather-credit-card me-2"></i> Health Cards
            </button>
            <button wire:click="switchTab('vouchers')" class="nav-link rounded-pill px-4 fw-semibold d-flex align-items-center {{ $activeTab === 'vouchers' ? 'active' : 'text-dark' }}">
                <i class="feather-gift me-2"></i> Promo Codes
            </button>
        </div>

        <div class="card stretch stretch-full border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-0">
                
                @if($activeTab === 'memberships')
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light fs-11 fw-bold text-uppercase text-muted">
                            <tr>
                                <th class="ps-4 py-3">Card Name</th>
                                <th class="py-3">Benefits</th>
                                <th class="py-3">Validity</th>
                                <th class="py-3">Status</th>
                                <th class="text-center py-3" style="width: 120px;">Actions</th> </tr>
                        </thead>
                        <tbody>
                            @forelse($memberships as $m)
                                <tr class="border-bottom border-light">
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <div style="width: 40px; height: 28px; background-color: {{ $m->color_code }}; border-radius: 6px;"></div>
                                            <div>
                                                <div class="fw-bold text-dark fs-14">{{ $m->name }}</div>
                                                <small class="text-muted fw-medium">Price: ₹{{ $m->price }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-soft-success text-success fs-12 px-2 py-1">{{ $m->discount_percentage }}% OFF</span>
                                        <div class="fs-11 text-muted mt-1">{{ Str::limit($m->description, 30) }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $m->validity_days }} Days</div>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input" type="checkbox" role="switch" wire:click="toggleMembershipStatus({{ $m->id }})" {{ $m->is_active ? 'checked' : '' }} style="cursor: pointer;">
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <button wire:click="editMembership({{ $m->id }})" class="btn btn-sm btn-light border text-primary shadow-sm rounded align-center-btn transition-all hover-primary" data-bs-toggle="tooltip" title="Edit">
                                                <i class="feather-edit-2"></i>
                                            </button>
                                            <button wire:click="deleteMembership({{ $m->id }})" wire:confirm="Delete this membership?" class="btn btn-sm btn-light border text-danger shadow-sm rounded align-center-btn transition-all hover-danger" data-bs-toggle="tooltip" title="Delete">
                                                <i class="feather-trash-2"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center py-5 text-muted">No Health Cards found. Create one to offer discounts to patients.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-3 border-top border-light">{{ $memberships->links() }}</div>
                @endif

                @if($activeTab === 'vouchers')
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light fs-11 fw-bold text-uppercase text-muted">
                            <tr>
                                <th class="ps-4 py-3">Promo Code</th>
                                <th class="py-3">Discount</th>
                                <th class="py-3">Limits & Rules</th>
                                <th class="py-3">Validity</th>
                                <th class="py-3">Status</th>
                                <th class="text-center py-3" style="width: 120px;">Actions</th> </tr>
                        </thead>
                        <tbody>
                            @forelse($vouchers as $v)
                                <tr class="border-bottom border-light">
                                    <td class="ps-4">
                                        <span class="badge border border-primary text-primary fs-13 px-3 py-2 bg-soft-primary" style="border-style: dashed !important; letter-spacing: 1px;">
                                            {{ $v->code }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark fs-14">
                                            {{ $v->discount_type === 'percentage' ? $v->discount_value.'%' : '₹'.$v->discount_value }} OFF
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fs-11 text-muted">Min Bill: <span class="text-dark fw-medium">₹{{ $v->min_bill_amount }}</span></div>
                                        @if($v->max_discount_amount)
                                            <div class="fs-11 text-muted">Max Disc: <span class="text-danger fw-medium">₹{{ $v->max_discount_amount }}</span></div>
                                        @endif
                                        <div class="fs-11 text-muted">Usage: <span class="text-info fw-medium">{{ $v->used_count }} / {{ $v->usage_limit ?: 'Unlimited' }}</span></div>
                                    </td>
                                    <td>
                                        <div class="fs-12 fw-medium text-dark">
                                            {{ $v->valid_until ? \Carbon\Carbon::parse($v->valid_until)->format('d M, Y') : 'Lifetime' }}
                                        </div>
                                        @if(!$v->isValid()) <span class="badge bg-soft-danger text-danger fs-10 mt-1">Expired/Limit Reached</span> @endif
                                    </td>
                                    <td>
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input" type="checkbox" role="switch" wire:click="toggleVoucherStatus({{ $v->id }})" {{ $v->is_active ? 'checked' : '' }} style="cursor: pointer;">
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <button wire:click="editVoucher({{ $v->id }})" class="btn btn-sm btn-light border text-primary shadow-sm rounded align-center-btn transition-all hover-primary" data-bs-toggle="tooltip" title="Edit">
                                                <i class="feather-edit-2"></i>
                                            </button>
                                            <button wire:click="deleteVoucher({{ $v->id }})" wire:confirm="Delete this promo code?" class="btn btn-sm btn-light border text-danger shadow-sm rounded align-center-btn transition-all hover-danger" data-bs-toggle="tooltip" title="Delete">
                                                <i class="feather-trash-2"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center py-5 text-muted">No Promo Codes found. Generate codes for festivals or special offers.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-3 border-top border-light">{{ $vouchers->links() }}</div>
                @endif

            </div>
        </div>
    </div>

    @if ($isMembershipModalOpen)
        <div class="modal-backdrop fade show" style="z-index: 1040;"></div>
        <div class="modal fade show d-block" tabindex="-1" style="z-index: 1050;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header bg-light border-bottom p-4">
                        <h5 class="modal-title fw-bold text-dark"><i class="feather-credit-card text-primary me-2"></i>{{ $membership_id ? 'Edit' : 'New' }} Health Card</h5>
                        <button type="button" wire:click="resetMembershipFields(); $set('isMembershipModalOpen', false)" class="btn-close shadow-none"></button>
                    </div>
                    <form wire:submit.prevent="storeMembership">
                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label fs-12 fw-bold text-muted text-uppercase">Card Name *</label>
                                    <input type="text" class="form-control" wire:model="m_name" placeholder="e.g. Gold Care Card">
                                    <div class="form-text fs-11 text-muted">The name of the membership plan visible to patients.</div>
                                    @error('m_name') <span class="text-danger fs-11">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-12 fw-bold text-muted text-uppercase">Selling Price (₹) *</label>
                                    <input type="number" class="form-control" wire:model="m_price">
                                    <div class="form-text fs-11 text-muted">How much the patient pays to buy this card.</div>
                                    @error('m_price') <span class="text-danger fs-11">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-12 fw-bold text-muted text-uppercase">Discount on Tests (%) *</label>
                                    <input type="number" step="0.01" class="form-control text-success fw-bold bg-soft-success" wire:model="m_discount_percentage">
                                    <div class="form-text fs-11 text-muted">Flat discount applied to their bills.</div>
                                    @error('m_discount_percentage') <span class="text-danger fs-11">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-12 fw-bold text-muted text-uppercase">Validity (Days) *</label>
                                    <input type="number" class="form-control" wire:model="m_validity_days">
                                    <div class="form-text fs-11 text-muted">Card expires after these many days (e.g. 365).</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-12 fw-bold text-muted text-uppercase">Card Color</label>
                                    <input type="color" class="form-control form-control-color w-100 px-2" wire:model="m_color_code">
                                    <div class="form-text fs-11 text-muted">Color theme for the digital card.</div>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fs-12 fw-bold text-muted text-uppercase">Benefits Description</label>
                                    <textarea class="form-control" wire:model="m_description" rows="2" placeholder="List benefits here..."></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-light border-top p-3">
                            <button type="submit" class="btn btn-primary px-5 shadow-sm d-flex align-items-center">
                                <i class="feather-save me-2"></i> Save Card
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @if ($isVoucherModalOpen)
        <div class="modal-backdrop fade show" style="z-index: 1040;"></div>
        <div class="modal fade show d-block" tabindex="-1" style="z-index: 1050;">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header bg-light border-bottom p-4">
                        <h5 class="modal-title fw-bold text-dark"><i class="feather-gift text-primary me-2"></i>{{ $voucher_id ? 'Edit' : 'New' }} Promo Code</h5>
                        <button type="button" wire:click="resetVoucherFields(); $set('isVoucherModalOpen', false)" class="btn-close shadow-none"></button>
                    </div>
                    <form wire:submit.prevent="storeVoucher">
                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fs-12 fw-bold text-muted text-uppercase">Promo Code *</label>
                                    <input type="text" class="form-control text-uppercase fw-bold text-primary bg-soft-primary" wire:model="v_code" placeholder="e.g. DIWALI50">
                                    <div class="form-text fs-11 text-muted">The secret text to enter at billing.</div>
                                    @error('v_code') <span class="text-danger fs-11">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fs-12 fw-bold text-muted text-uppercase">Discount Type *</label>
                                    <select class="form-select" wire:model="v_discount_type">
                                        <option value="percentage">Percentage (%)</option>
                                        <option value="flat">Flat Amount (₹)</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fs-12 fw-bold text-muted text-uppercase">Discount Value *</label>
                                    <input type="number" step="0.01" class="form-control" wire:model="v_discount_value">
                                    <div class="form-text fs-11 text-muted">e.g. Enter 50 for 50% or ₹50 off.</div>
                                    @error('v_discount_value') <span class="text-danger fs-11">{{ $message }}</span> @enderror
                                </div>
                                
                                <div class="col-md-12 my-2"><hr class="text-light"></div>

                                <div class="col-md-4">
                                    <label class="form-label fs-12 fw-bold text-muted text-uppercase">Min Bill Amount (₹)</label>
                                    <input type="number" step="0.01" class="form-control" wire:model="v_min_bill_amount" placeholder="0 = No limit">
                                    <div class="form-text fs-11 text-muted">Cart must reach this amount.</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fs-12 fw-bold text-muted text-uppercase">Max Discount (₹)</label>
                                    <input type="number" step="0.01" class="form-control" wire:model="v_max_discount_amount" placeholder="Leave empty for none">
                                    <div class="form-text fs-11 text-danger">Safety limit for % discounts.</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fs-12 fw-bold text-muted text-uppercase">Max Total Usages</label>
                                    <input type="number" class="form-control" wire:model="v_usage_limit" placeholder="e.g. 100">
                                    <div class="form-text fs-11 text-muted">Code expires after these many uses.</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fs-12 fw-bold text-muted text-uppercase">Valid From</label>
                                    <input type="date" class="form-control" wire:model="v_valid_from">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-12 fw-bold text-muted text-uppercase">Valid Until</label>
                                    <input type="date" class="form-control" wire:model="v_valid_until">
                                    <div class="form-text fs-11 text-muted">Automatically expires after this date.</div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-light border-top p-3">
                            <button type="submit" class="btn btn-primary px-5 shadow-sm d-flex align-items-center">
                                <i class="feather-save me-2"></i> Save Promo Code
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
    
    <style>
        .nav-pills .nav-link.active { background-color: #3b71ca; color: #fff !important; border-color: #3b71ca !important; }
        .bg-soft-primary { background-color: rgba(59, 113, 202, 0.08) !important; }
        .bg-soft-success { background-color: rgba(25, 135, 84, 0.08) !important; }
        .bg-soft-danger { background-color: rgba(220, 53, 69, 0.08) !important; }
        .text-primary { color: #3b71ca !important; }
        
        /* Fixed Button Icons Alignment */
        .align-center-btn {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 32px;
            height: 32px;
            padding: 0 !important;
        }

        /* Hover effects */
        .transition-all { transition: all 0.2s ease-in-out; }
        .hover-primary:hover { background-color: #3b71ca !important; color: #fff !important; border-color: #3b71ca !important; }
        .hover-danger:hover { background-color: #dc3545 !important; color: #fff !important; border-color: #dc3545 !important; }
    </style>
</div>