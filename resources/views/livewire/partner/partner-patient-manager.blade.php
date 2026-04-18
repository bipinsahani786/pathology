<div>
    {{-- ======================== PAGE HEADER ======================== --}}
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="text-dark fw-bold">Referrals & Patient History</h5>
                <p class="fs-13 text-muted mb-0 font-medium">Track your patient referrals and sample logistics.</p>
            </div>
            <ul class="breadcrumb d-none d-md-flex ms-3">
                <li class="breadcrumb-item"><a href="{{ route('partner.dashboard') }}" wire:navigate class="text-muted">Home</a></li>
                <li class="breadcrumb-item text-primary fw-medium">Referrals</li>
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
                        <div class="avatar-text avatar-lg rounded-3 bg-soft-primary text-primary"><i class="feather-users" style="font-size:22px;"></i></div>
                        <div>
                            <div class="fs-10 fw-bold text-muted text-uppercase">Total Patients</div>
                            <div class="fs-4 fw-bold text-dark">{{ number_format($stats['total_patients']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card mb-0 shadow-sm border-0">
                    <div class="card-body py-3 d-flex align-items-center gap-3">
                        <div class="avatar-text avatar-lg rounded-3 bg-soft-success text-success"><i class="feather-check-circle" style="font-size:22px;"></i></div>
                        <div>
                            <div class="fs-10 fw-bold text-muted text-uppercase">Collected Today</div>
                            <div class="fs-4 fw-bold text-success">{{ number_format($stats['collected_today']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card mb-0 shadow-sm border-0" style="background: linear-gradient(135deg, #3b71ca 0%, #1d4ed8 100%);">
                    <div class="card-body py-3 d-flex align-items-center gap-3">
                        <div class="avatar-text avatar-lg rounded-3 bg-white text-primary"><i class="feather-box" style="font-size:22px;"></i></div>
                        <div>
                            <div class="fs-10 fw-bold text-white-50 text-uppercase">Awaiting Pickup</div>
                            <div class="fs-4 fw-bold text-white">{{ number_format($stats['awaiting_pickup']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card mb-0 shadow-sm border-0">
                    <div class="card-body py-3 d-flex align-items-center gap-3">
                        <div class="avatar-text avatar-lg rounded-3 bg-soft-warning text-warning"><i class="feather-activity" style="font-size:22px;"></i></div>
                        <div>
                            <div class="fs-10 fw-bold text-muted text-uppercase">In Processing</div>
                            <div class="fs-4 fw-bold text-warning">{{ number_format($stats['processing']) }}</div>
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
                                placeholder="Search by Patient Name or Phone...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select border shadow-sm rounded-3 fs-12 fw-bold text-dark" wire:model.live="filterStatus">
                            <option value="">All Sample Status</option>
                            <option value="Pending">🕒 Pending</option>
                            <option value="Collected">💉 Collected</option>
                            <option value="Dispatched">🚚 Dispatched</option>
                            <option value="Received">🔬 Received</option>
                            <option value="Processing">⚖️ Processing</option>
                            <option value="Ready">✅ Ready</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group shadow-sm border rounded-3 overflow-hidden">
                            <span class="input-group-text bg-light fs-10 fw-bold border-0">FROM</span>
                            <input type="date" class="form-control border-0 shadow-none fs-12 text-dark fw-medium" wire:model.live="filterDateFrom">
                        </div>
                    </div>
                    <div class="col-md-2">
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

        {{-- ═══════ Referral Table ═══════ --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="card-body p-0">
                <div class="table-responsive border-0" style="min-height: 400px; overflow: visible !important;">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light fs-11 fw-bold text-uppercase text-muted border-bottom">
                            <tr>
                                <th class="ps-4 py-3">Patient Details</th>
                                <th>Test Information</th>
                                <th>Billing & Date</th>
                                <th class="text-center">Sample Logistics</th>
                                <th class="text-center">Report Status</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="fs-13">
                            @forelse($invoices as $inv)
                                <tr class="border-bottom border-light" wire:key="inv-{{ $inv->id }}">
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar-text bg-soft-primary text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold fs-5 shadow-sm" style="width: 45px; height: 45px;">
                                                {{ strtoupper(substr($inv->patient->name ?? 'P', 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark fs-14">{{ $inv->patient->name ?? 'N/A' }}</div>
                                                <div class="fs-11 text-muted mt-1"><i class="feather-phone me-1 fs-10"></i>{{ $inv->patient->phone ?? 'N/A' }}</div>
                                                <span class="badge bg-soft-info text-info fs-9 fw-bold mt-1 px-2">{{ $inv->invoice_number }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-dark fw-medium">{{ $inv->items->count() }} Referred Tests</div>
                                        <div class="fs-11 text-muted text-truncate" style="max-width: 200px;">
                                            @foreach($inv->items as $item)
                                                <span class="badge bg-light text-dark fs-10 fw-normal border">{{ $item->labTest->name ?? $item->test_name }}</span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-dark fw-bold">₹{{ number_format($inv->total_amount, 0) }}</div>
                                        <div class="fs-11 text-muted">{{ $inv->invoice_date->format('d M, Y') }}</div>
                                        <div class="fs-10 text-muted">{{ $inv->invoice_date->format('h:i A') }}</div>
                                    </td>
                                    <td class="text-center">
                                        <div class="dropdown">
                                            @php
                                                $statusColors = [
                                                    'Pending' => 'bg-soft-secondary text-secondary',
                                                    'Collected' => 'bg-soft-info text-info',
                                                    'Dispatched' => 'bg-soft-warning text-warning',
                                                    'Received' => 'bg-soft-primary text-primary',
                                                    'Processing' => 'bg-soft-danger text-danger',
                                                    'Ready' => 'bg-soft-success text-success',
                                                ];
                                                $statusIcons = ['Pending' => '🕒', 'Collected' => '💉', 'Dispatched' => '🚚', 'Received' => '🔬', 'Processing' => '⚖️', 'Ready' => '✅'];
                                                $c = $statusColors[$inv->sample_status ?? 'Pending'] ?? 'bg-soft-secondary text-secondary';
                                            @endphp
                                            <button class="btn btn-sm dropdown-toggle py-1 px-3 fw-bold fs-11 rounded-pill {{ $c }} shadow-sm border-0 d-inline-flex align-items-center gap-1"
                                                type="button" data-bs-toggle="dropdown" data-bs-boundary="viewport">
                                                <span>{{ $statusIcons[$inv->sample_status ?? 'Pending'] }}</span>
                                                {{ $inv->sample_status ?? 'Pending' }}
                                            </button>
                                            <ul class="dropdown-menu shadow-lg border-0 fs-12 p-1">
                                                <li class="px-2 py-1 border-bottom mb-1 bg-light rounded-top"><small class="fw-bold text-muted text-uppercase fs-9">Update Status</small></li>
                                                @php
                                                    $statuses = ['Pending', 'Collected', 'Dispatched'];
                                                    if($this->role !== 'Collection Center') {
                                                        $statuses = ['Pending', 'Collected', 'Dispatched', 'Received', 'Processing', 'Ready'];
                                                    }
                                                @endphp
                                                @foreach($statuses as $st)
                                                    <li>
                                                        <a class="dropdown-item rounded-2 py-2 d-flex align-items-center gap-2 {{ ($inv->sample_status ?? 'Pending') == $st ? 'bg-primary text-white shadow-sm' : 'text-dark' }}" 
                                                           href="javascript:void(0)" 
                                                           wire:click="updateSampleStatus({{ $inv->id }}, '{{ $st }}')">
                                                            <span class="fs-14">{{ $statusIcons[$st] }}</span>
                                                            <span class="fw-bold">{{ $st }}</span>
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        @if($inv->sample_collected_at)
                                            <div class="fs-9 text-muted mt-1"><i class="feather-clock me-1"></i>{{ $inv->sample_collected_at->format('d/m h:i A') }}</div>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($inv->sample_status == 'Ready')
                                            <span class="badge bg-soft-success text-success border border-success border-opacity-10 px-3 py-1 fs-11 fw-bold animated pulse shadow-sm">
                                                <i class="feather-check-circle me-1"></i>Report Ready
                                            </span>
                                        @else
                                            <span class="badge bg-soft-warning text-warning border border-warning border-opacity-10 px-3 py-1 fs-11 fw-bold shadow-sm d-inline-flex align-items-center gap-1">
                                                <span class="spinner-border spinner-border-sm" style="width: 10px; height: 10px;" role="status"></span>
                                                In Progress
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="d-flex justify-content-end gap-1">
                                            <a href="{{ route('lab.pos.summary', $inv->id) }}" wire:navigate class="btn btn-sm btn-light border text-primary shadow-sm rounded-circle p-0 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="View Case Summary">
                                                <i class="feather-eye fs-14"></i>
                                            </a>
                                            @if($inv->sample_status == 'Ready')
                                                <a href="{{ route('partner.reports.print', $inv->id) }}" target="_blank" class="btn btn-sm btn-light border text-success shadow-sm rounded-circle p-0 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="View / Print Report">
                                                    <i class="feather-printer fs-14"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="text-muted mb-3"><i class="feather-users" style="font-size: 3.5rem; opacity: 0.15;"></i></div>
                                        <h6 class="fw-bold text-dark">No Referrals Found</h6>
                                        <p class="text-muted fs-13">Start referring patients to see them here.</p>
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
                            of <strong>{{ $invoices->total() }}</strong> Patients
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
        .bg-soft-info { background-color: rgba(13, 202, 240, 0.08) !important; }
        .bg-soft-warning { background-color: rgba(255, 193, 7, 0.08) !important; }
        .bg-soft-danger { background-color: rgba(220, 53, 69, 0.08) !important; }
        .bg-soft-secondary { background-color: rgba(108, 117, 125, 0.08) !important; }
        .avatar-text.avatar-lg { width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
        .shadowed { box-shadow: 0 4px 10px rgba(59, 113, 202, 0.3) !important; }
        .dropdown-item.active { background-color: #3b71ca !important; color: #fff !important; }
        .dropdown-item:active { background-color: #3b71ca !important; }
        .dropdown-menu { min-width: 160px; }
    </style>
</div>
