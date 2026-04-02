<div>
    {{-- ======================== PAGE HEADER ======================== --}}
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="text-dark fw-bold">Settlement History</h5>
                <p class="fs-13 text-muted mb-0 font-medium">Manage your payments and track outstanding dues.</p>
            </div>
            <ul class="breadcrumb d-none d-md-flex ms-3">
                <li class="breadcrumb-item"><a href="{{ route('partner.dashboard') }}" wire:navigate class="text-muted small">Home</a></li>
                <li class="breadcrumb-item text-primary fw-medium small">Settlements</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            @if(auth()->user()->hasRole('collection_center'))
                <button wire:click="openModal" class="btn btn-primary shadow-sm fw-bold border-0 px-4">
                    <i class="feather-plus me-2"></i> Record New Payment
                </button>
            @endif
        </div>
    </div>

    {{-- ======================== MAIN CONTENT ======================== --}}
    <div class="main-content">

        @if (session()->has('message'))
            <div class="alert alert-success border-0 shadow-sm rounded-3 d-flex align-items-center py-3 alert-dismissible fade show mb-4">
                <i class="feather-check-circle fs-4 me-2"></i>
                <strong>{{ session('message') }}</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- ═══════ Stats Cards ═══════ --}}
        <div class="row g-3 mb-4">
            <div class="col-md-3 col-6">
                <div class="card mb-0 shadow-sm border-0">
                    <div class="card-body py-3 d-flex align-items-center gap-3">
                        <div class="avatar-text avatar-lg rounded-3 bg-soft-primary text-primary"><i class="feather-file-text" style="font-size:22px;"></i></div>
                        <div>
                            <div class="fs-10 fw-bold text-muted text-uppercase">Total Dues</div>
                            <div class="fs-4 fw-bold text-dark">₹{{ number_format($stats['total_dues'], 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card mb-0 shadow-sm border-0">
                    <div class="card-body py-3 d-flex align-items-center gap-3">
                        <div class="avatar-text avatar-lg rounded-3 bg-soft-success text-success"><i class="feather-check-circle" style="font-size:22px;"></i></div>
                        <div>
                            <div class="fs-10 fw-bold text-muted text-uppercase">Paid (Confirmed)</div>
                            <div class="fs-4 fw-bold text-success">₹{{ number_format($stats['paid_confirmed'], 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card mb-0 shadow-sm border-0" style="background: linear-gradient(135deg, #3b71ca 0%, #1d4ed8 100%);">
                    <div class="card-body py-3 d-flex align-items-center gap-3">
                        <div class="avatar-text avatar-lg rounded-3 bg-white text-primary"><i class="feather-clock" style="font-size:22px;"></i></div>
                        <div>
                            <div class="fs-10 fw-bold text-white-50 text-uppercase">Wait Verification</div>
                            <div class="fs-4 fw-bold text-white">₹{{ number_format($stats['awaiting_verification'], 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card mb-0 shadow-sm border-0">
                    <div class="card-body py-3 d-flex align-items-center gap-3">
                        <div class="avatar-text avatar-lg rounded-3 bg-soft-danger text-danger"><i class="feather-alert-triangle" style="font-size:22px;"></i></div>
                        <div>
                            <div class="fs-10 fw-bold text-muted text-uppercase">Unpaid Balance</div>
                            <div class="fs-4 fw-bold text-danger">₹{{ number_format($stats['balance'], 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════ Filters Card ═══════ --}}
        <div class="card mb-3 shadow-sm border-0">
            <div class="card-body py-3">
                <div class="row g-3 align-items-center">
                    <div class="col-md-4">
                        <div class="input-group search-group shadow-sm border rounded-3 overflow-hidden">
                            <span class="input-group-text bg-white border-0"><i class="feather-search text-primary"></i></span>
                            <input type="text" class="form-control border-0 shadow-none fs-13" wire:model.live.debounce.300ms="search"
                                placeholder="Search by Reference No...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group shadow-sm border rounded-3 overflow-hidden">
                            <span class="input-group-text bg-light fs-10 fw-bold border-0">FROM</span>
                            <input type="date" class="form-control border-0 shadow-none fs-12 text-dark fw-medium" wire:model.live="filterDateFrom">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group shadow-sm border rounded-3 overflow-hidden">
                            <span class="input-group-text bg-light fs-10 fw-bold border-0">TO</span>
                            <input type="date" class="form-control border-0 shadow-none fs-12 text-dark fw-medium" wire:model.live="filterDateTo">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button wire:click="clearFilters" class="btn btn-outline-secondary w-100 fs-12 fw-bold shadow-sm rounded-3">
                            <i class="feather-refresh-cw me-1"></i> Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════ Table Card ═══════ --}}
        <div class="card stretch stretch-full border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="card-body p-0">
                <div class="table-responsive border-0" style="min-height: 400px; overflow: visible !important;">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light fs-11 fw-bold text-uppercase text-muted border-bottom">
                            <tr>
                                <th class="ps-4 py-3 border-0">Settlement Date</th>
                                <th class="py-3 border-0">Amount (₹)</th>
                                <th class="py-3 border-0">Payment Mode</th>
                                <th class="py-3 border-0">Reference No.</th>
                                <th class="text-center pe-4 py-3 border-0">Status</th>
                            </tr>
                        </thead>
                        <tbody class="fs-13 text-dark">
                            @forelse($settlements as $s)
                                <tr class="border-bottom border-light">
                                    <td class="ps-4 py-3">
                                        <div class="fw-bold fs-14">{{ $s->payment_date->format('d M, Y') }}</div>
                                        <div class="fs-10 text-muted">{{ $s->payment_date->format('h:i A') }}</div>
                                    </td>
                                    <td class="fw-bold fs-15 text-primary">₹{{ number_format($s->amount, 2) }}</td>
                                    <td>
                                        <span class="badge bg-soft-info text-info border border-info border-opacity-10 px-2 py-1 fs-11 fw-bold">
                                            <i class="feather-credit-card me-1 fs-10"></i>{{ $s->payment_mode }}
                                        </span>
                                    </td>
                                    <td class="fw-medium">{{ $s->reference_no ?: 'N/A' }}</td>
                                    <td class="text-center pe-4">
                                        @php
                                            $stClass = [
                                                'Approved' => 'bg-success',
                                                'Pending' => 'bg-warning',
                                                'Rejected' => 'bg-danger'
                                            ][$s->status] ?? 'bg-secondary';
                                        @endphp
                                        <span class="badge {{ $stClass }} rounded-pill px-3 py-1 fs-10 fw-bold shadow-sm">{{ $s->status }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="text-muted mb-3"><i class="feather-credit-card" style="font-size: 3.5rem; opacity: 0.15;"></i></div>
                                        <h6 class="fw-bold text-dark">No Settlements Recorded</h6>
                                        <p class="text-muted fs-13">Your payment history will appear here once recorded.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Custom Pagination Footer --}}
                <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top bg-light">
                    <div class="d-flex align-items-center gap-3">
                        <span class="fs-11 text-muted">Show</span>
                        <select class="form-select form-select-sm fw-bold shadow-sm" wire:model.live="perPage" style="width:70px; border-radius: 6px;">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                        <span class="fs-11 text-muted">
                            of <strong>{{ $settlements->total() }}</strong> Records
                        </span>
                    </div>

                    @if($settlements->hasPages())
                        <nav>
                            <ul class="pagination pagination-sm mb-0 gap-1">
                                {{-- Previous --}}
                                @if ($settlements->onFirstPage())
                                    <li class="page-item disabled"><span class="page-link border-0 bg-transparent"><i class="feather-chevron-left fs-12"></i></span></li>
                                @else
                                    <li class="page-item"><button wire:click="previousPage" class="page-link border-0 bg-transparent"><i class="feather-chevron-left fs-12"></i></button></li>
                                @endif

                                {{-- Page Numbers --}}
                                @php
                                    $currentPage = $settlements->currentPage();
                                    $lastPage = $settlements->lastPage();
                                    $start = max(1, $currentPage - 2);
                                    $end = min($lastPage, $currentPage + 2);
                                @endphp

                                @for($p = $start; $p <= $end; $p++)
                                    <li class="page-item {{ $p == $currentPage ? 'active' : '' }}">
                                        <button wire:click="gotoPage({{ $p }})"
                                            class="page-link border rounded-2 fs-11 fw-bold {{ $p == $currentPage ? 'bg-primary text-white border-primary shadowed' : 'bg-white text-dark' }}" style="min-width:32px;">{{ $p }}</button>
                                    </li>
                                @endfor

                                {{-- Next --}}
                                @if ($settlements->hasMorePages())
                                    <li class="page-item"><button wire:click="nextPage" class="page-link border-0 bg-transparent"><i class="feather-chevron-right fs-12"></i></button></li>
                                @else
                                    <li class="page-item disabled"><span class="page-link border-0 bg-transparent"><i class="feather-chevron-right fs-12"></i></span></li>
                                @endif
                            </ul>
                        </nav>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Payment Recording Modal --}}
    @if ($isModalOpen)
        <div class="modal-backdrop fade show" style="z-index: 1040;"></div>
        <div class="modal fade show d-block" tabindex="-1" style="z-index: 1050;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="modal-header bg-light border-0 p-4">
                        <h5 class="modal-title fw-bold text-dark d-flex align-items-center">
                            <i class="feather-credit-card me-2 text-primary fs-3"></i> Record Payment
                        </h5>
                        <button type="button" wire:click="$set('isModalOpen', false)" class="btn-close shadow-none"></button>
                    </div>
                    <form wire:submit.prevent="recordPayment">
                        <div class="modal-body p-4 bg-white">
                            <div class="row g-4">
                                <div class="col-md-12">
                                    <label class="form-label fs-11 fw-bold text-muted text-uppercase">Payment Amount (₹) *</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light fw-bold border-end-0">₹</span>
                                        <input type="number" class="form-control border-start-0 fw-bold fs-5 text-dark" wire:model="amount" placeholder="0.00" step="0.01">
                                    </div>
                                    @error('amount') <span class="text-danger fs-11 fw-bold">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-11 fw-bold text-muted text-uppercase">Payment Date *</label>
                                    <input type="date" class="form-control" wire:model="payment_date">
                                    @error('payment_date') <span class="text-danger fs-11 fw-bold">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-11 fw-bold text-muted text-uppercase">Payment Mode *</label>
                                    <select class="form-select fw-bold" wire:model="payment_mode">
                                        <option value="UPI">UPI / GPay / PhonePe</option>
                                        <option value="Cash">Cash</option>
                                        <option value="Bank Transfer">Bank Transfer (IMPS/NEFT)</option>
                                        <option value="Cheque">Cheque</option>
                                    </select>
                                    @error('payment_mode') <span class="text-danger fs-11 fw-bold">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fs-11 fw-bold text-muted text-uppercase">Reference No. / UTR</label>
                                    <input type="text" class="form-control fw-medium" wire:model="reference_no" placeholder="Optional reference number">
                                    @error('reference_no') <span class="text-danger fs-11 fw-bold">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fs-11 fw-bold text-muted text-uppercase">Notes</label>
                                    <textarea class="form-control fs-12" wire:model="notes" rows="2" placeholder="Any additional details..."></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-light border-0 p-3">
                            <button type="button" wire:click="$set('isModalOpen', false)" class="btn btn-light px-4 fw-bold">Cancel</button>
                            <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm d-flex align-items-center">
                                <span wire:loading.remove wire:target="recordPayment">Send for Approval</span>
                                <span wire:loading wire:target="recordPayment" class="spinner-border spinner-border-sm me-2"></span>
                                <span wire:loading wire:target="recordPayment">Processing...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <style>
        .bg-soft-primary { background-color: rgba(59, 113, 202, 0.08) !important; }
        .bg-soft-success { background-color: rgba(25, 135, 84, 0.08) !important; }
        .bg-soft-info { background-color: rgba(13, 202, 240, 0.08) !important; }
        .bg-soft-warning { background-color: rgba(255, 193, 7, 0.08) !important; }
        .bg-soft-danger { background-color: rgba(220, 53, 69, 0.08) !important; }
        .avatar-text.avatar-lg { width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; }
        .shadowed { box-shadow: 0 4px 10px rgba(59, 113, 202, 0.3) !important; }
    </style>
</div>
