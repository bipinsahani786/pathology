<div>
    {{-- ======================== PAGE HEADER ======================== --}}
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="text-dark fw-bold">Invoices</h5>
                <p class="fs-13 text-muted mb-0 font-medium">View and manage your referral billing.</p>
            </div>
            <ul class="breadcrumb d-none d-md-flex ms-3">
                <li class="breadcrumb-item"><a href="{{ route('partner.dashboard') }}" wire:navigate class="text-muted">Home</a></li>
                <li class="breadcrumb-item text-primary fw-medium">Billing</li>
            </ul>
        </div>
    </div>

    {{-- ======================== MAIN CONTENT ======================== --}}
    <div class="main-content">

        {{-- ═══════ Stats Cards ═══════ --}}
        <div class="row g-3 mb-4">
            <div class="col-md-3 col-6">
                <div class="card mb-0 shadow-sm border-0">
                    <div class="card-body py-3 d-flex align-items-center gap-3">
                        <div class="avatar-text avatar-lg rounded-3 bg-soft-primary text-primary"><i class="feather-file-text" style="font-size:22px;"></i></div>
                        <div>
                            <div class="fs-10 fw-bold text-muted text-uppercase">Total Bills</div>
                            <div class="fs-4 fw-bold text-dark">{{ number_format($stats['total']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card mb-0 shadow-sm border-0">
                    <div class="card-body py-3 d-flex align-items-center gap-3">
                        <div class="avatar-text avatar-lg rounded-3 bg-soft-success text-success"><i class="feather-dollar-sign" style="font-size:22px;"></i></div>
                        <div>
                            <div class="fs-10 fw-bold text-muted text-uppercase">My Total Profit</div>
                            <div class="fs-4 fw-bold text-success">₹{{ number_format($stats['total_profit'], 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card mb-0 shadow-sm border-0" style="background: linear-gradient(135deg, #3b71ca 0%, #1d4ed8 100%);">
                    <div class="card-body py-3 d-flex align-items-center gap-3">
                        <div class="avatar-text avatar-lg rounded-3 bg-white text-primary"><i class="feather-trending-up" style="font-size:22px;"></i></div>
                        <div>
                            <div class="fs-10 fw-bold text-white-50 text-uppercase">Today's Profit</div>
                            <div class="fs-4 fw-bold text-white">₹{{ number_format($stats['today_profit'], 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card mb-0 shadow-sm border-0">
                    <div class="card-body py-3 d-flex align-items-center gap-3">
                        <div class="avatar-text avatar-lg rounded-3 bg-soft-danger text-danger"><i class="feather-alert-circle" style="font-size:22px;"></i></div>
                        <div>
                            <div class="fs-10 fw-bold text-muted text-uppercase">Outstanding Dues</div>
                            <div class="fs-4 fw-bold text-danger">₹{{ number_format($stats['due'], 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════ Filters & Search ═══════ --}}
        <div class="card mb-3 shadow-sm border-0">
            <div class="card-body py-3">
                <div class="row g-3 align-items-center">
                    <div class="col-md-4">
                        <div class="input-group search-group shadow-sm border rounded-3 overflow-hidden">
                            <span class="input-group-text bg-white border-0"><i class="feather-search text-primary"></i></span>
                            <input type="text" class="form-control border-0 shadow-none fs-13" wire:model.live.debounce.300ms="search"
                                placeholder="Search by Invoice # or Patient Name...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group shadow-sm border rounded-3 overflow-hidden">
                            <span class="input-group-text bg-light fs-10 fw-bold border-0">FROM</span>
                            <input type="date" class="form-control border-0 shadow-none fs-12" wire:model.live="filterDateFrom">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group shadow-sm border rounded-3 overflow-hidden">
                            <span class="input-group-text bg-light fs-10 fw-bold border-0">TO</span>
                            <input type="date" class="form-control border-0 shadow-none fs-12" wire:model.live="filterDateTo">
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

        {{-- ═══════ Invoice Table ═══════ --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="card-body p-0">
                <div class="table-responsive border-0" style="min-height: 400px; overflow: visible !important;">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light fs-11 fw-bold text-uppercase text-muted border-bottom">
                            <tr>
                                <th class="ps-4 py-3" style="width:120px;">Invoice #</th>
                                <th>Patient Details</th>
                                <th>Date & Time</th>
                                <th class="text-end">Bill Amount</th>
                                <th class="text-end text-success">My Profit</th>
                                <th class="text-end text-danger">Due</th>
                                <th class="text-center">Status</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="fs-13">
                            @forelse($invoices as $inv)
                                <tr class="border-bottom border-light">
                                    <td class="ps-4">
                                        <div class="fw-bold text-primary">{{ $inv->invoice_number }}</div>
                                        <div class="fs-10 text-muted">{{ $inv->barcode }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark fs-14">{{ $inv->patient->name ?? 'N/A' }}</div>
                                        <div class="fs-11 text-muted"><i class="feather-phone me-1 fs-10"></i>{{ $inv->patient->phone ?? 'N/A' }}</div>
                                    </td>
                                    <td>
                                        <div class="text-dark">{{ $inv->invoice_date->format('d M, Y') }}</div>
                                        <div class="fs-11 text-muted">{{ $inv->invoice_date->format('h:i A') }}</div>
                                    </td>
                                    <td class="text-end fw-bold text-dark">₹{{ number_format($inv->total_amount, 2) }}</td>
                                    <td class="text-end fw-bold text-success">₹{{ number_format($this->role === 'Collection Center' ? $inv->cc_profit_amount : ($this->role === 'Doctor' ? $inv->doctor_commission_amount : $inv->agent_commission_amount), 2) }}</td>
                                    <td class="text-end fw-bold text-danger">₹{{ number_format($inv->due_amount, 2) }}</td>
                                    <td class="text-center">
                                        @php
                                            $stClass = [
                                                'Paid' => 'bg-success',
                                                'Partial' => 'bg-warning',
                                                'Unpaid' => 'bg-danger'
                                            ][$inv->payment_status] ?? 'bg-secondary';
                                        @endphp
                                        <span class="badge {{ $stClass }} rounded-pill px-2 py-1 fs-10 fw-bold">{{ $inv->payment_status }}</span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="d-flex justify-content-end gap-1">
                                            <a href="{{ route('lab.pos.summary', $inv->id) }}" wire:navigate class="btn btn-sm btn-light border text-primary shadow-sm rounded-circle p-0 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="View Details">
                                                <i class="feather-eye fs-14"></i>
                                            </a>
                                            @if($inv->status == 'Completed')
                                                <a href="{{ route('partner.reports.print', $inv->id) }}" target="_blank" class="btn btn-sm btn-light border text-success shadow-sm rounded-circle p-0 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Print Report">
                                                    <i class="feather-printer fs-14"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="text-muted mb-3"><i class="feather-inbox" style="font-size: 3.5rem; opacity: 0.15;"></i></div>
                                        <h6 class="fw-bold text-dark">No Invoices Found</h6>
                                        <p class="text-muted fs-13">Try adjusting your filters or search terms.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Footer - Exactly like Lab Admin --}}
                <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top bg-light">
                    <div class="d-flex align-items-center gap-3">
                        <span class="fs-11 text-muted">Show</span>
                        <select class="form-select form-select-sm fw-bold shadow-sm" wire:model.live="perPage" style="width:70px; border-radius: 6px;">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <span class="fs-11 text-muted">
                            of <strong>{{ $invoices->total() }}</strong> Invoices
                            @if($invoices->total() > 0)
                                · Showing {{ $invoices->firstItem() }}–{{ $invoices->lastItem() }}
                            @endif
                        </span>
                    </div>

                    @if($invoices->hasPages())
                        <nav>
                            <ul class="pagination pagination-sm mb-0 gap-1">
                                {{-- Previous --}}
                                @if ($invoices->onFirstPage())
                                    <li class="page-item disabled"><span class="page-link border-0 bg-transparent"><i class="feather-chevron-left fs-12"></i></span></li>
                                @else
                                    <li class="page-item"><button wire:click="previousPage" class="page-link border-0 bg-transparent"><i class="feather-chevron-left fs-12"></i></button></li>
                                @endif

                                {{-- Page Numbers --}}
                                @php
                                    $currentPage = $invoices->currentPage();
                                    $lastPage = $invoices->lastPage();
                                    $start = max(1, $currentPage - 2);
                                    $end = min($lastPage, $currentPage + 2);
                                @endphp

                                @if($start > 1)
                                    <li class="page-item"><button wire:click="gotoPage(1)" class="page-link border rounded-2 fs-11 fw-bold" style="min-width:32px;">1</button></li>
                                    @if($start > 2)
                                        <li class="page-item disabled"><span class="page-link border-0 bg-transparent fs-11">…</span></li>
                                    @endif
                                @endif

                                @for($p = $start; $p <= $end; $p++)
                                    <li class="page-item {{ $p == $currentPage ? 'active' : '' }}">
                                        <button wire:click="gotoPage({{ $p }})"
                                            class="page-link border rounded-2 fs-11 fw-bold {{ $p == $currentPage ? 'bg-primary text-white border-primary shadowed' : 'bg-white text-dark' }}"
                                            style="min-width:32px;">{{ $p }}</button>
                                    </li>
                                @endfor

                                @if($end < $lastPage)
                                    @if($end < $lastPage - 1)
                                        <li class="page-item disabled"><span class="page-link border-0 bg-transparent fs-11">…</span></li>
                                    @endif
                                    <li class="page-item"><button wire:click="gotoPage({{ $lastPage }})" class="page-link border rounded-2 fs-11 fw-bold" style="min-width:32px;">{{ $lastPage }}</button></li>
                                @endif

                                {{-- Next --}}
                                @if ($invoices->hasMorePages())
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

    <style>
        .bg-soft-primary { background-color: rgba(59, 113, 202, 0.08) !important; }
        .bg-soft-success { background-color: rgba(25, 135, 84, 0.08) !important; }
        .bg-soft-danger { background-color: rgba(220, 53, 69, 0.08) !important; }
        .avatar-text.avatar-lg { width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
        .shadowed { box-shadow: 0 4px 10px rgba(59, 113, 202, 0.3) !important; }
        .page-link:hover { background-color: #f8f9fa; color: #3b71ca; }
        .active .page-link:hover { color: #fff; }
    </style>
</div>
