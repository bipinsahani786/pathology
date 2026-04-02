<div>
    {{-- ======================== PAGE HEADER ======================== --}}
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="text-dark fw-bold">Lab Reports</h5>
            </div>
            <ul class="breadcrumb d-none d-md-flex ms-3">
                <li class="breadcrumb-item"><a href="{{ route('lab.dashboard') }}" wire:navigate class="text-muted">Home</a></li>
                <li class="breadcrumb-item text-primary fw-medium">Reports</li>
            </ul>
        </div>
    </div>

    {{-- ======================== MAIN CONTENT ======================== --}}
    <div class="main-content">
        <div class="card mb-4" style="overflow: visible !important;">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0"><i class="feather-flask me-2 text-primary"></i>Test Results & Reports</h6>
            </div>
            <div class="card-body">
                
                {{-- Filters --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="input-group search-group shadow-sm">
                            <span class="input-group-text">
                                <i class="feather-search text-primary"></i>
                            </span>
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
                            <option value="custom">Custom Date</option>
                        </select>
                    </div>
                </div>

                {{-- Row 2 Filters --}}
                <div class="row g-3 mb-4">
                    @if($dateRange === 'custom')
                        <div class="col-md-2">
                            <input type="date" class="form-control" wire:model.live="filterDateFrom">
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control" wire:model.live="filterDateTo">
                        </div>
                    @endif
                    <div class="col-md-3">
                        <select class="form-select" wire:model.live="filterDoctor">
                            <option value="">All Doctors</option>
                            @foreach($doctors as $doc)
                                <option value="{{ $doc->user_id }}">{{ $doc->user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" wire:model.live="filterAgent">
                            <option value="">All Agents</option>
                            @foreach($agents as $agent)
                                <option value="{{ $agent->user_id }}">{{ $agent->user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" wire:model.live="filterCC">
                            <option value="">All Centers</option>
                            @foreach($centers as $center)
                                <option value="{{ $center->id }}">{{ $center->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Reports Table --}}
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width:140px;">Invoice & Br.</th>
                                <th>Patient Info</th>
                                <th>Doctor / Agent</th>
                                <th>Center</th>
                                <th>Test List</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoices as $invoice)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-primary">{{ $invoice->invoice_number }}</div>
                                        <div class="fs-11 fw-bold text-dark mb-1">{{ $invoice->barcode }}</div>
                                        <div class="fs-10 text-muted">{{ $invoice->created_at->format('d/m/y h:i A') }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-bold fs-13">{{ $invoice->patient->name }}</div>
                                        <div class="badge bg-soft-info text-info fs-10 fw-bold px-2 py-1 mb-1">{{ $invoice->patient->formatted_id }}</div>
                                        <div class="fs-10 text-muted">
                                            {{ $invoice->patient->patientProfile->age ?? '--' }} {{ $invoice->patient->patientProfile->age_type ?? 'Y' }} | {{ $invoice->patient->patientProfile->gender ?? '--' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fs-11">
                                            <div class="fw-semibold text-dark"><i class="feather-user me-1 fs-10"></i>{{ $invoice->doctor->name ?? 'Self' }}</div>
                                            @if($invoice->agent)
                                                <div class="text-muted"><i class="feather-user-check me-1 fs-10"></i>{{ $invoice->agent->name }}</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="badge bg-light text-dark fw-normal fs-10">{{ $invoice->collectionCenter->name ?? 'Main Lab' }}</div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($invoice->items as $item)
                                                @if($item->lab_test_id)
                                                    @php
                                                        $isComplete = $item->status === 'Completed';
                                                    @endphp
                                                    <div class="form-check form-check-inline m-0 p-0">
                                                        <input class="form-check-input ms-0 me-1" type="checkbox" 
                                                            wire:model.live="selectedTests" 
                                                            value="{{ $item->id }}"
                                                            {{ !$isComplete ? 'disabled' : '' }}>
                                                        <span class="badge {{ $isComplete ? 'bg-soft-success text-success' : 'bg-soft-danger text-danger' }} border fs-9 fw-normal" title="{{ $isComplete ? 'Result Entered' : 'Pending' }}">
                                                            {{ $item->labTest->name }}
                                                        </span>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </td>
                                    <td>
                                        @if(!$invoice->testReport)
                                            <span class="badge bg-soft-warning text-warning fs-10"><i class="feather-clock me-1"></i> Pending Entry</span>
                                        @elseif($invoice->testReport->status === 'Draft')
                                            <span class="badge bg-soft-info text-info fs-10"><i class="feather-edit-2 me-1"></i> Draft</span>
                                        @elseif($invoice->testReport->status === 'Approved')
                                            <span class="badge bg-soft-success text-success fs-10"><i class="feather-check-circle me-1"></i> Approved</span>
                                        @endif
                                    </td>
                                     <td class="text-end">
                                        @if(!$invoice->testReport || $invoice->testReport->status !== 'Approved')
                                            <div class="d-flex justify-content-end gap-1">
                                                @can('edit reports')
                                                    <a href="{{ route('lab.reports.entry', $invoice->id) }}" class="btn btn-sm btn-primary py-1 px-2" title="Enter Results">
                                                        <i class="feather-edit fs-12"></i>
                                                    </a>
                                                @endcan
                                                @can('edit invoices')
                                                    <a href="{{ route('lab.invoice.edit', $invoice->id) }}" wire:navigate class="btn btn-sm btn-outline-warning py-1 px-2" title="Modify Invoice">
                                                        <i class="feather-edit-3 fs-12"></i>
                                                    </a>
                                                @endcan
                                            </div>
                                        @else
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-success dropdown-toggle fs-11" type="button" data-bs-toggle="dropdown">
                                                    <i class="feather-printer me-1"></i> Print / Edit
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                                    @can('edit reports')
                                                        <li><a class="dropdown-item fs-12" href="{{ route('lab.reports.entry', $invoice->id) }}"><i class="feather-edit me-2 text-info"></i> Edit Results</a></li>
                                                    @endcan
                                                    @can('edit invoices')
                                                        <li><a class="dropdown-item fs-12" href="{{ route('lab.invoice.edit', $invoice->id) }}" wire:navigate><i class="feather-edit-3 me-2 text-warning"></i> Modify Invoice</a></li>
                                                    @endcan
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><button type="button" class="dropdown-item fs-12 text-success fw-bold" wire:click="printSelected({{ $invoice->id }})"><i class="feather-check-square me-2"></i> Print Selected Tests</button></li>
                                                    <li><a class="dropdown-item fs-12 text-primary" href="{{ route('lab.reports.print', [$invoice->id, 'modern']) }}" target="_blank"><i class="feather-file-text me-2"></i> Print All (Modern)</a></li>
                                                </ul>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
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
