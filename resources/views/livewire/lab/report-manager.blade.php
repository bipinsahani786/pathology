<div>
    {{-- ======================== PAGE HEADER ======================== --}}
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Lab Reports</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                <li class="breadcrumb-item">Lab</li>
                <li class="breadcrumb-item">Reports</li>
            </ul>
        </div>
    </div>

    {{-- ======================== MAIN CONTENT ======================== --}}
    <div class="main-content">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0"><i class="feather-flask-conical me-2 text-primary"></i>Test Results & Reports</h6>
            </div>
            <div class="card-body">
                
                {{-- Filters --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="feather-search text-muted"></i></span>
                            <input type="text" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Search by Invoice, Patient Name, Phone...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" wire:model.live="statusFilter">
                            <option value="all">All Statuses</option>
                            <option value="pending">Pending Entry</option>
                            <option value="draft">Draft (Saved)</option>
                            <option value="approved">Approved</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" wire:model.live="dateRange">
                            <option value="all">All Time</option>
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                        </select>
                    </div>
                </div>

                {{-- Reports Table --}}
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Invoice Details</th>
                                <th>Patient Info</th>
                                <th>Total Tests</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoices as $invoice)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-primary">{{ $invoice->invoice_number }}</div>
                                        <div class="fs-11 text-muted">{{ $invoice->created_at->format('d M, Y h:i A') }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $invoice->patient->name }}</div>
                                        <div class="fs-12 text-muted">
                                            {{ $invoice->patient->patientProfile->age ?? '--' }} {{ $invoice->patient->patientProfile->age_type ?? 'Y' }} | {{ $invoice->patient->patientProfile->gender ?? '--' }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-soft-primary text-primary fs-12 rounded-pill px-3">{{ $invoice->items->count() }} Tests</span>
                                    </td>
                                    <td>
                                        @if(!$invoice->testReport)
                                            <span class="badge bg-soft-warning text-warning"><i class="feather-clock me-1"></i> Pending Entry</span>
                                        @elseif($invoice->testReport->status === 'Draft')
                                            <span class="badge bg-soft-info text-info"><i class="feather-edit-2 me-1"></i> Draft</span>
                                        @elseif($invoice->testReport->status === 'Approved')
                                            <span class="badge bg-soft-success text-success"><i class="feather-check-circle me-1"></i> Approved</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if(!$invoice->testReport || $invoice->testReport->status !== 'Approved')
                                            <a href="{{ route('lab.reports.entry', $invoice->id) }}" class="btn btn-sm btn-primary">
                                                <i class="feather-edit me-1"></i> Enter Results
                                            </a>
                                        @else
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    <i class="feather-printer me-1"></i> Print / Edit
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li><a class="dropdown-item" href="{{ route('lab.reports.entry', $invoice->id) }}"><i class="feather-edit me-2"></i> Edit Results</a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item text-primary fw-bold" href="{{ route('lab.reports.print', [$invoice->id, 'modern']) }}" target="_blank"><i class="feather-file-text me-2"></i> Print Report (Modern)</a></li>
                                                </ul>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="avatar-text avatar-xl rounded-circle bg-soft-secondary mx-auto mb-3">
                                            <i class="feather-file-text fs-2"></i>
                                        </div>
                                        <h6 class="fw-bold">No Records Found</h6>
                                        <p class="text-muted fs-12">Try adjusting your filters.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Footer --}}
                <div class="d-flex justify-content-between align-items-center px-0 py-3 mt-3 border-top bg-white">
                    <div class="d-flex align-items-center gap-2">
                        <span class="fs-11 text-muted">Show</span>
                        <select class="form-select form-select-sm fw-bold" wire:model.live="perPage" style="width:70px;">
                            <option value="10">10</option>
                            <option value="15">15</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                        <span class="fs-11 text-muted">
                            of <strong>{{ $invoices->total() }}</strong> reports
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
                                        <button wire:click="gotoPage({{ $p }})" class="page-link border rounded-2 fs-11 fw-bold {{ $p == $currentPage ? 'bg-primary text-white border-primary' : '' }}" style="min-width:32px;">{{ $p }}</button>
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
</div>
