<div>
    <style>
        .action-btn-group {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .action-btn {
            width: 35px !important;
            height: 35px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 0 !important;
            border-radius: 8px !important;
            transition: all 0.2s ease-in-out !important;
            border: 1.5px solid transparent !important;
            background-color: #f8f9fa !important;
        }
        .action-btn i, .action-btn span {
            font-size: 18px !important;
            line-height: 1 !important;
            display: inline-block !important;
        }
        .action-btn-warning {
            color: #d97706 !important;
            background-color: #fef3c7 !important;
            border-color: #fde68a !important;
        }
        .action-btn-warning:hover {
            color: #ffffff !important;
            background-color: #d97706 !important;
            border-color: #d97706 !important;
        }
        .action-btn-info {
            color: #0891b2 !important;
            background-color: #ecfeff !important;
            border-color: #cffafe !important;
        }
        .action-btn-info:hover {
            color: #ffffff !important;
            background-color: #0891b2 !important;
            border-color: #0891b2 !important;
        }
        .action-btn-success {
            color: #16a34a !important;
            background-color: #f0fdf4 !important;
            border-color: #bbf7d0 !important;
        }
        .action-btn-success:hover {
            color: #ffffff !important;
            background-color: #16a34a !important;
            border-color: #16a34a !important;
        }
        .action-btn-whatsapp {
            color: #25d366 !important;
            background-color: #e8f9ee !important;
            border-color: #c3f2d2 !important;
        }
        .action-btn-whatsapp:hover {
            color: #ffffff !important;
            background-color: #25d366 !important;
            border-color: #25d366 !important;
        }
        .action-btn-primary {
            color: #2563eb !important;
            background-color: #eff6ff !important;
            border-color: #bfdbfe !important;
        }
        .action-btn-primary:hover {
            color: #ffffff !important;
            background-color: #2563eb !important;
            border-color: #2563eb !important;
        }
        .action-btn-danger {
            color: #dc2626 !important;
            background-color: #fef2f2 !important;
            border-color: #fee2e2 !important;
        }
        .action-btn-danger:hover {
            color: #ffffff !important;
            background-color: #dc2626 !important;
            border-color: #dc2626 !important;
        }
        .action-btn.dropdown-toggle::after {
            display: none !important;
        }
        .action-btn-muted {
            color: #6b7280 !important;
            background-color: #f3f4f6 !important;
            border-color: #e5e7eb !important;
        }
        .action-btn-muted:hover {
            color: #ffffff !important;
            background-color: #6b7280 !important;
            border-color: #6b7280 !important;
        }

        .action-btn-text {
            height: 35px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 0 14px !important;
            font-size: 12px !important;
            font-weight: 600 !important;
            border-radius: 8px !important;
            transition: all 0.2s ease-in-out !important;
            border: 1.5px solid #bbf7d0 !important;
            color: #16a34a !important;
            background-color: #f0fdf4 !important;
        }
        .action-btn-text:hover {
            color: #ffffff !important;
            background-color: #16a34a !important;
            border-color: #16a34a !important;
        }
        .action-btn-text i {
            font-size: 16px !important;
            margin-right: 5px !important;
        }
    </style>
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
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0"><i class="feather-flask me-2 text-primary"></i>Test Results & Reports</h6>
            </div>
            <div class="card-body">
                @if(session()->has('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3">
                        <i class="feather-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session()->has('error'))
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3">
                        <i class="feather-alert-octagon me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
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
                <div class="table-responsive shadow-sm rounded-3">
                    <table class="table table-hover table-bordered align-middle mb-0">
                        <thead class="table-light">
                            <tr class="fs-11 fw-bold text-uppercase text-muted">
                                <th style="width:120px;">Invoice</th>
                                <th style="width:140px;">Patient</th>
                                <th style="width:140px;">Referred By / Center</th>
                                <th style="width:210px;">Test List</th>
                                <th style="width:95px;">Status</th>
                                <th class="text-end" style="width:190px;">Action</th>
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
                                        <div class="fw-bold fs-14">{{ $invoice->patient->name }}</div>
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
                                            <div class="badge bg-light text-dark fw-normal fs-10 mt-1"><i class="feather-home me-1"></i>{{ $invoice->collectionCenter->name ?? 'Main Lab' }}</div>
                                        </div>
                                    </td>
                                    <td style="padding: 8px 8px;">
                                        <div class="d-flex flex-column gap-1">
                                            @php
                                                $displayTests = collect();
                                                foreach($invoice->items as $item) {
                                                    if($item->labTest) {
                                                        if($item->labTest->is_package && !empty($item->labTest->linked_test_ids)) {
                                                            $innerTests = \App\Models\LabTest::whereIn('id', $item->labTest->linked_test_ids)->get();
                                                            foreach($innerTests as $inner) {
                                                                $displayTests->push(['item' => $item, 'inner' => $inner, 'is_package' => true]);
                                                            }
                                                        } else {
                                                            $displayTests->push(['item' => $item, 'inner' => null, 'is_package' => false]);
                                                        }
                                                    }
                                                }
                                            @endphp
                                            
                                            @foreach($displayTests->take(4) as $dt)
                                                @php
                                                    $item = $dt['item'];
                                                    if ($dt['is_package']) {
                                                        $inner = $dt['inner'];
                                                        // Check if ANY parameter in this inner test has a non-empty result
                                                        $isComplete = \App\Models\ReportResult::where('invoice_item_id', $item->id)
                                                            ->where('lab_test_id', $inner->id)
                                                            ->where(function($q) {
                                                                $q->whereNotNull('result_value')->where('result_value', '!=', '');
                                                            })
                                                            ->exists();
                                                        $checkboxValue = $item->id . '_' . $inner->id;
                                                        $testName = $inner->name;
                                                    } else {
                                                        $isComplete = $item->status === 'Completed';
                                                        $checkboxValue = $item->id;
                                                        $testName = $item->labTest->name;
                                                    }
                                                @endphp
                                                <div class="d-flex align-items-start gap-1">
                                                    <input class="form-check-input mt-1 flex-shrink-0" type="checkbox"
                                                        wire:model.live="selectedTests"
                                                        value="{{ $checkboxValue }}"
                                                        {{ !$isComplete ? 'disabled' : '' }}>
                                                    <div style="
                                                        font-size: 11px;
                                                        font-weight: 600;
                                                        color: {{ $isComplete ? '#155724' : '#721c24' }};
                                                        background: {{ $isComplete ? '#d4edda' : '#f8d7da' }};
                                                        border-radius: 4px;
                                                        padding: 2px 7px;
                                                        white-space: normal;
                                                        line-height: 1.4;
                                                    " title="{{ $testName }} ({{ $isComplete ? 'Result Entered' : 'Pending' }})">
                                                        {{ $testName }}
                                                    </div>
                                                </div>
                                            @endforeach
                                            
                                            @if($displayTests->count() > 4)
                                                <div style="font-size: 11px; color: #6c757d; font-weight: 600; padding-left: 18px;">
                                                    +{{ $displayTests->count() - 4 }} more
                                                </div>
                                            @endif
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
                                         <div class="d-flex justify-content-end gap-2">
                                              @if(!$invoice->testReport || $invoice->testReport->status !== 'Approved')
                                                 <div class="d-flex gap-2">
                                                     @can('edit reports')
                                                         <a href="{{ route('lab.reports.entry', $invoice->id) }}" class="action-btn action-btn-success" title="Enter Results">
                                                             <i class="feather-edit"></i>
                                                         </a>
                                                         
                                                         @if(auth()->user()->company->plan?->features['enable_outsourcing'] ?? false)
                                                             <button type="button" wire:click="openOutsourcedModal({{ $invoice->id }})" class="action-btn action-btn-warning" title="Upload Outsourced PDF">
                                                                 <i class="feather-upload-cloud"></i>
                                                             </button>
                                                         @endif

                                                         <button type="button" wire:click="openReorderModal({{ $invoice->id }})" class="action-btn action-btn-info" title="Reorder Tests">
                                                             <i class="feather-move"></i>
                                                         </button>
                                                     @endcan
                                                     
                                                      @if($invoice->testReport && $invoice->testReport->status === 'Draft')
                                                         <div class="dropdown">
                                                             <button class="action-btn action-btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-boundary="viewport">
                                                                 <i class="feather-printer"></i>
                                                             </button>
                                                             <ul class="dropdown-menu dropdown-menu-end shadow border-0 p-1" style="min-width: 240px; width: max-content !important;">
                                                                 <li class="dropdown-header fw-bold fs-10 text-uppercase text-muted px-3 py-2">Draft Options</li>
                                                                 <li><button type="button" class="dropdown-item fs-12 py-2 text-nowrap" wire:click="printCompleted({{ $invoice->id }}, 1)"><i class="feather-file-text me-2 text-primary"></i> Print All Completed</button></li>
                                                                 <li><hr class="dropdown-divider my-1"></li>
                                                                 <li class="dropdown-header fw-bold fs-10 text-uppercase text-muted px-3">Print Selected</li>
                                                                 <li><button type="button" class="dropdown-item fs-12 text-success py-2 text-nowrap" wire:click="printSelected({{ $invoice->id }}, 1)"><i class="feather-check-square me-2"></i> With Header</button></li>
                                                                 <li><button type="button" class="dropdown-item fs-12 text-dark py-2 text-nowrap" wire:click="printSelected({{ $invoice->id }}, 0)"><i class="feather-check-square me-2"></i> Without Header</button></li>
                                                             </ul>
                                                         </div>
                                                     @endif

                                                     @if(auth()->user()->can('edit invoices') || (auth()->user()->collection_center_id && $invoice->collection_center_id === auth()->user()->collection_center_id))
                                                         <a href="{{ route('lab.invoice.edit', $invoice->id) }}" wire:navigate class="action-btn action-btn-warning" title="Modify Invoice">
                                                             <i class="feather-edit-3"></i>
                                                         </a>
                                                     @endif
                                                 </div>
                                             @else
                                                 <div class="dropdown">
                                                     <button class="action-btn-text dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-boundary="viewport">
                                                         <i class="feather-printer"></i> Print / Edit
                                                     </button>
                                                     <ul class="dropdown-menu dropdown-menu-end shadow border-0 p-1" style="min-width: 240px; width: max-content !important;">
                                                         @can('edit reports')
                                                             <li><a class="dropdown-item fs-12 py-2 text-nowrap" href="{{ route('lab.reports.entry', $invoice->id) }}"><i class="feather-edit me-2 text-info"></i> Edit Results</a></li>
                                                             @if(auth()->user()->company->plan?->features['enable_outsourcing'] ?? false)
                                                                 <li><button type="button" class="dropdown-item fs-12 py-2 text-nowrap" wire:click="openOutsourcedModal({{ $invoice->id }})"><i class="{{ !empty($invoice->testReport->outsourced_pdf_path) ? 'feather-crop' : 'feather-upload-cloud' }} me-2 text-warning"></i> {{ !empty($invoice->testReport->outsourced_pdf_path) ? 'Edit Outsourced PDF' : 'Upload Outsourced PDF' }}</button></li>
                                                             @endif
                                                             <li><button type="button" class="dropdown-item fs-12 py-2 text-nowrap" wire:click="openReorderModal({{ $invoice->id }})"><i class="feather-move me-2 text-info"></i> Reorder Tests</button></li>
                                                         @endcan
                                                         @if(auth()->user()->can('edit invoices') || (auth()->user()->collection_center_id && $invoice->collection_center_id === auth()->user()->collection_center_id))
                                                             <li><a class="dropdown-item fs-12 py-2 text-nowrap" href="{{ route('lab.invoice.edit', $invoice->id) }}" wire:navigate><i class="feather-edit-3 me-2 text-warning"></i> Modify Invoice</a></li>
                                                         @endif
                                                         <li><hr class="dropdown-divider my-1"></li>
                                                         <li class="dropdown-header fw-bold fs-10 text-uppercase text-muted px-3 py-2">Print All Tests</li>
                                                         <li><button type="button" class="dropdown-item fs-12 text-primary py-2 text-nowrap" wire:click="printReport({{ $invoice->id }}, 1)"><i class="feather-file-text me-2"></i> With Header</button></li>
                                                         <li><button type="button" class="dropdown-item fs-12 text-secondary py-2 text-nowrap" wire:click="printReport({{ $invoice->id }}, 0)"><i class="feather-file me-2"></i> Without Header</button></li>
                                                         <li><hr class="dropdown-divider my-1"></li>
                                                         <li class="dropdown-header fw-bold fs-10 text-uppercase text-muted px-3 py-2">Print Selected Tests</li>
                                                         <li><button type="button" class="dropdown-item fs-12 text-success py-2 text-nowrap" wire:click="printSelected({{ $invoice->id }}, 1)"><i class="feather-check-square me-2"></i> With Header</button></li>
                                                         <li><button type="button" class="dropdown-item fs-12 text-dark py-2 text-nowrap" wire:click="printSelected({{ $invoice->id }}, 0)"><i class="feather-check-square me-2"></i> Without Header</button></li>
                                                     </ul>
                                                 </div>
                                             @endif

                                             {{-- WhatsApp Share --}}
                                             <div class="dropdown">
                                                 @if($invoice->patient->phone)
                                                     <button class="action-btn action-btn-whatsapp dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-boundary="viewport">
                                                         <i class="bi bi-whatsapp"></i>
                                                     </button>
                                                     <ul class="dropdown-menu dropdown-menu-end shadow border-0 p-1">
                                                         <li><a class="dropdown-item fs-11 rounded-2 py-2" href="{{ $invoice->getWhatsappLink('invoice') }}" target="_blank"><i class="feather-file-text me-2 text-success"></i> Share Invoice</a></li>
                                                         @if($invoice->testReport && $invoice->testReport->status === 'Approved')
                                                             <li><a class="dropdown-item fs-11 rounded-2 py-2" href="{{ $invoice->getWhatsappLink('report') }}" target="_blank"><i class="feather-check-circle me-2 text-success"></i> Share Report</a></li>
                                                         @else
                                                             <li><a class="dropdown-item fs-11 rounded-2 py-2 disabled text-muted" href="javascript:void(0)"><i class="feather-clock me-2"></i> Report Pending</a></li>
                                                         @endif
                                                     </ul>
                                                 @else
                                                     <button class="action-btn action-btn-muted" type="button" wire:click="notifyMissingPhone" title="Phone number missing">
                                                         <i class="bi bi-whatsapp"></i>
                                                     </button>
                                                 @endif
                                             </div>
                                         </div>
                                     </td>
                                </tr>
                            @empty
                                <tr>
                                     <td colspan="6" class="text-center py-5">
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
    {{-- Outsourced Report Modal --}}
    @if($isOutsourcedModalOpen)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header border-bottom-0 pb-0">
                        <h5 class="modal-title fw-bold fs-16"><i class="feather-upload-cloud text-warning me-2"></i>Upload Outsourced PDF</h5>
                        <button type="button" class="btn-close" wire:click="closeOutsourcedModal"></button>
                    </div>
                    <div class="modal-body p-4">
                        @if($outsourcedPdfMode === 'crop_to_image')
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="form-label fw-bold text-muted text-uppercase fs-10">Crop Top (%)</label>
                                    <input type="number" step="1" class="form-control" wire:model.defer="outsourcedCropTop" title="Percentage of the top of the PDF to crop out">
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-bold text-muted text-uppercase fs-10">Crop Bottom (%)</label>
                                    <input type="number" step="1" class="form-control" wire:model.defer="outsourcedCropBottom" title="Percentage of the bottom of the PDF to crop out">
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info py-2 fs-12 mb-3">
                                <i class="feather-info me-1"></i> PDF will be merged with your lab's letterhead (Header/Footer) automatically. No cropping needed.
                            </div>
                        @endif
                        
                        <div x-data="{ isUploading: false, progress: 0 }"
                             x-on:livewire-upload-start="isUploading = true"
                             x-on:livewire-upload-finish="isUploading = false"
                             x-on:livewire-upload-error="isUploading = false"
                             x-on:livewire-upload-progress="progress = $event.detail.progress"
                             class="w-100">
                             
                            @if(count($uploadedOutsourcedPdfs) > 0)
                                <div class="mb-4">
                                    <h6 class="fs-12 fw-bold text-muted text-uppercase mb-2">Uploaded Reports</h6>
                                    <ul class="list-group list-group-sm">
                                        @foreach($uploadedOutsourcedPdfs as $index => $path)
                                            <li class="list-group-item d-flex justify-content-between align-items-center bg-light border-0 mb-1 rounded-3">
                                                <div class="d-flex align-items-center gap-2 text-truncate">
                                                    <i class="feather-file-text text-primary"></i>
                                                    <span class="fs-12 text-dark text-truncate" title="{{ basename($path) }}">Part #{{ $index + 1 }} - {{ basename($path) }}</span>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-light text-danger p-1 border-0" wire:click="deleteOutsourcedPdf({{ $index }})" title="Delete" wire:confirm="Are you sure you want to delete this report part?">
                                                    <i class="feather-trash-2"></i>
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            
                            <label class="btn btn-outline-primary border-dashed w-100 py-4 d-flex flex-column align-items-center justify-content-center mb-0" style="cursor: pointer; border-style: dashed;">
                                <i class="feather-upload fs-1 mb-2"></i>
                                <span class="fw-bold fs-14">{{ count($uploadedOutsourcedPdfs) > 0 ? 'Click to Upload Another PDF' : 'Click to Select PDF' }}</span>
                                <span class="text-muted fs-11 mt-1">(Max 10MB)</span>
                                <input type="file" class="d-none" wire:model="outsourcedPdf" accept=".pdf">
                            </label>
                            
                            <div x-show="isUploading" class="mt-3">
                                <div class="progress" style="height: 10px;">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" x-bind:style="'width: ' + progress + '%'"></div>
                                </div>
                                <span class="text-muted fs-11 mt-1 d-block text-center">Uploading... <span x-text="progress"></span>%</span>
                            </div>
                            
                            @if($outsourcedPdf)
                                <div class="mt-3 p-2 bg-soft-success text-success rounded-3 fs-12 fw-bold text-center">
                                    <i class="feather-check-circle me-1"></i> PDF Selected
                                </div>
                            @endif
                            @error('outsourcedPdf') <span class="text-danger fs-11 mt-1 d-block text-center">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-light fw-bold" wire:click="closeOutsourcedModal">Cancel</button>
                        <button type="button" class="btn btn-primary fw-bold" wire:click="saveOutsourcedReport" wire:loading.attr="disabled" {{ (!$outsourcedPdf && !$hasExistingOutsourcedPdf) ? 'disabled' : '' }}>
                            <span wire:loading.remove wire:target="saveOutsourcedReport"><i class="feather-check me-2"></i>Generate & Download</span>
                            <span wire:loading wire:target="saveOutsourcedReport"><i class="spinner-border spinner-border-sm me-2"></i>Processing...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Reorder Tests Modal --}}
    @if($isReorderModalOpen)
        <div class="modal fade show" tabindex="-1" style="display: block; background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg overflow-hidden">
                    <div class="modal-header bg-soft-info border-bottom-0 pb-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-white p-2 rounded-3 shadow-sm me-3">
                                <i class="feather-move text-info fs-4"></i>
                            </div>
                            <div>
                                <h5 class="modal-title fw-bold text-dark mb-1">Reorder Tests</h5>
                                <p class="text-muted fs-12 mb-0">Change the print order of tests for this invoice.</p>
                            </div>
                        </div>
                        <button type="button" class="btn-close shadow-none" wire:click="closeReorderModal"></button>
                    </div>
                    <div class="modal-body bg-light p-3">
                        @if(count($reorderItems) > 0)
                            <div class="list-group list-group-flush shadow-sm rounded-3">
                                @foreach($reorderItems as $index => $item)
                                    <div class="list-group-item d-flex align-items-center justify-content-between p-2">
                                        <div class="d-flex align-items-center">
                                            <i class="feather-hash text-muted me-2 fs-12"></i>
                                            <span class="fw-bold fs-12 text-dark">{{ $item['lab_test']['name'] ?? 'Unknown Test' }}</span>
                                        </div>
                                        <div class="d-flex flex-column bg-light rounded-1" style="line-height: 1;">
                                            <button type="button" class="btn btn-link p-0 text-muted" wire:click="moveReorderItemUp({{ $index }})" {{ $index === 0 ? 'disabled' : '' }}>
                                                <i class="feather-chevron-up fs-11"></i>
                                            </button>
                                            <button type="button" class="btn btn-link p-0 text-muted" wire:click="moveReorderItemDown({{ $index }})" {{ $index === count($reorderItems) - 1 ? 'disabled' : '' }}>
                                                <i class="feather-chevron-down fs-11"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center text-muted p-4">
                                <i class="feather-inbox fs-1 mb-2 d-block"></i>
                                <p class="mb-0 fs-12">No tests found for this invoice.</p>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer border-top-0 pt-0 bg-light">
                        <button type="button" class="btn btn-primary fw-bold w-100" wire:click="closeReorderModal">Done</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
