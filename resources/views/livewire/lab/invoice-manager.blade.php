<div>
    <style>
        .dropdown-menu { border: 1px solid #e0e0e0; box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important; z-index: 2000; background: #fff !important; min-width: 160px; }
        .grayscale { filter: grayscale(1); }
        .opacity-50 { opacity: 0.5; }
        .btn-status { border-bottom: 2px solid transparent; }
        .dropdown-item:hover { background-color: #f8f9fa; }

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
                <h5 class="text-dark fw-bold">Invoices</h5>
            </div>
            <ul class="breadcrumb d-none d-md-flex ms-3">
                <li class="breadcrumb-item"><a href="{{ route('lab.dashboard') }}" wire:navigate
                        class="text-muted">Home</a></li>
                <li class="breadcrumb-item text-primary fw-medium">Billing</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            @if(auth()->user()->can('create pos') || auth()->user()->collection_center_id)
                <a href="{{ route('lab.pos') }}" wire:navigate class="btn btn-primary"><i class="feather-plus me-1"></i>New Bill</a>
            @endif
        </div>
    </div>

    {{-- ======================== MAIN CONTENT ======================== --}}
    <div class="main-content">

        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                <i class="feather-check-circle me-2"></i> {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                <i class="feather-alert-triangle me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- ═══════ Stats Cards ═══════ --}}
        <div class="row g-3 mb-4">
            <div class="col-md-3 col-6">
                <div class="card mb-0 shadow-sm border-0">
                    <div class="card-body py-3 d-flex align-items-center gap-3">
                        <div class="avatar-text avatar-lg rounded-3 bg-soft-primary text-primary"><i
                                class="feather-file-text" style="font-size:22px;"></i></div>
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
                        <div class="avatar-text avatar-lg rounded-3 bg-soft-success text-success"><i
                                class="feather-calendar" style="font-size:22px;"></i></div>
                        <div>
                            <div class="fs-10 fw-bold text-muted text-uppercase">Today's Collection</div>
                            <div class="fs-4 fw-bold text-success">₹{{ number_format($stats['todayRevenue'], 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card mb-0 shadow-sm border-0"
                    style="background: linear-gradient(135deg, #3b71ca 0%, #1d4ed8 100%);">
                    <div class="card-body py-3 d-flex align-items-center gap-3">
                        <div class="avatar-text avatar-lg rounded-3 bg-white text-primary"><i
                                class="feather-trending-up" style="font-size:22px;"></i></div>
                        <div>
                            <div class="fs-10 fw-bold text-white-50 text-uppercase">Total Revenue</div>
                            <div class="fs-4 fw-bold text-white">₹{{ number_format($stats['totalRevenue'], 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card mb-0 shadow-sm border-0">
                    <div class="card-body py-3 d-flex align-items-center gap-3">
                        <div class="avatar-text avatar-lg rounded-3 bg-soft-danger text-danger"><i
                                class="feather-alert-circle" style="font-size:22px;"></i></div>
                        <div>
                            <div class="fs-10 fw-bold text-muted text-uppercase">Total Outstanding</div>
                            <div class="fs-4 fw-bold text-danger">₹{{ number_format($stats['due'], 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════ Filters & Search ═══════ --}}
        <div class="card mb-3">
            <div class="card-body py-3">
                <div class="row g-3 align-items-center">
                    <div class="col-md-4">
                        <div class="input-group search-group shadow-sm">
                            <span class="input-group-text"><i class="feather-search text-primary"></i></span>
                            <input type="text" class="form-control" wire:model.live.debounce.300ms="search"
                                placeholder="Search by Invoice, Patient, or Phone...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select border-0 shadow-sm" wire:model.live="filterCC">
                            <option value="">All Centers</option>
                            @foreach($collectionCenters as $cc)
                                <option value="{{ $cc->id }}">🏥 {{ \Illuminate\Support\Str::limit($cc->name, 12) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text bg-light fs-10 fw-bold">FROM</span>
                            <input type="date" class="form-control" wire:model.live="filterDateFrom">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text bg-light fs-10 fw-bold">TO</span>
                            <input type="date" class="form-control" wire:model.live="filterDateTo">
                        </div>
                    </div>
                </div>
                <div class="row g-3 align-items-center mt-1">
                    <div class="col-md-2">
                        <select class="form-select" wire:model.live="filterStatus">
                            <option value="">All Payments</option>
                            <option value="Paid">✅ Paid</option>
                            <option value="Partial">⚠️ Partial</option>
                            <option value="Unpaid">❌ Unpaid</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" wire:model.live="filterInvoiceStatus">
                            <option value="">All Active/Cancelled</option>
                            <option value="Active">🔓 Active</option>
                            <option value="Cancelled">🚫 Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" wire:model.live="filterSampleStatus">
                            <option value="">All Sample Status</option>
                            @foreach(['Pending', 'Collected', 'Dispatched', 'Received', 'Processing', 'Ready'] as $st)
                                <option value="{{ $st }}">{{ $st }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" wire:model.live="filterDoctor">
                            <option value="">All Doctors</option>
                            @foreach($doctors as $dr)
                                <option value="{{ $dr->user_id }}">👨‍⚕️ {{ $dr->user->name ?? 'Doctor' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" wire:model.live="filterAgent">
                            <option value="">All Agents</option>
                            @foreach($agents as $ag)
                                <option value="{{ $ag->user_id }}">🤝 {{ $ag->user->name ?? 'Agent' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button wire:click="clearFilters" class="btn btn-outline-secondary w-100" title="Clear Filters">
                            <i class="feather-refresh-cw me-1"></i> Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════ Invoice Table ═══════ --}}
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive shadow-sm">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-light">
                            <tr class="fs-11 fw-bold text-uppercase text-muted">
                                <th class="ps-3" style="width:130px;">Invoice #</th>
                                <th style="width:150px;">Patient</th>
                                <th style="width:180px;">Tests</th>
                                <th class="text-center" style="width:90px;">Billing</th>
                                <th class="text-center" style="width:95px;">Payment</th>
                                <th class="text-center" style="width:100px;">Sample</th>
                                <th class="text-center" style="width:220px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoices as $i => $inv)
                                <tr class="fs-12 {{ $inv->status === 'Cancelled' ? 'opacity-50 grayscale' : '' }}">
                                    <td class="ps-3">
                                        <span class="fw-bold text-primary">{{ $inv->invoice_number }}</span>
                                        @if($inv->status === 'Cancelled')
                                            <span class="badge bg-danger fs-9 ms-1">CANCELLED</span>
                                        @endif
                                        <div class="fs-10 text-muted mt-1"><i class="feather-hash me-1"></i>{{ $inv->barcode }}</div>
                                        <div class="fs-10 text-muted"><i class="feather-clock me-1"></i>{{ $inv->invoice_date->format('d M y, h:i A') }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $inv->patient->name }}</div>
                                        <div class="badge bg-soft-info text-info fs-9 fw-bold p-1">{{ $inv->patient->formatted_id }}</div>
                                        <div class="fs-11 text-muted"><i class="feather-phone me-1 fs-10"></i>{{ $inv->patient->phone }}</div>
                                    </td>
                                    <td style="padding: 10px 8px;">
                                        <div class="d-flex flex-column gap-1">
                                            @foreach($inv->items->take(4) as $item)
                                                <div style="
                                                    font-size: 11.5px;
                                                    font-weight: 600;
                                                    color: #1e3a5f;
                                                    background: #eef4ff;
                                                    border-radius: 4px;
                                                    padding: 3px 8px;
                                                    white-space: normal;
                                                    line-height: 1.4;
                                                    max-width: 190px;
                                                ">
                                                    {{ $item->test_name }}
                                                </div>
                                            @endforeach
                                            @if($inv->items->count() > 4)
                                                <div style="font-size: 11px; color: #6c757d; font-weight: 600; padding-left: 3px;">
                                                    +{{ $inv->items->count() - 4 }} more tests
                                                </div>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="text-center">
                                        <div class="fw-bold text-dark fs-13">₹{{ number_format($inv->total_amount, 0) }}</div>
                                        <div class="fs-10 text-success fw-bold">↑ ₹{{ number_format($inv->paid_amount, 0) }}</div>
                                        @if($inv->due_amount > 0)
                                            <div class="fs-10 text-danger fw-bold">↓ ₹{{ number_format($inv->due_amount, 0) }}</div>
                                        @else
                                            <div class="fs-10 text-muted">—</div>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $statusMap = [
                                                'Paid' => ['bg' => 'bg-success', 'icon' => '✅'],
                                                'Partial' => ['bg' => 'bg-warning', 'icon' => '⚠️'],
                                                'Unpaid' => ['bg' => 'bg-danger', 'icon' => '❌'],
                                            ];
                                            $s = $statusMap[$inv->payment_status] ?? ['bg' => 'bg-secondary', 'icon' => ''];
                                        @endphp
                                        <span class="badge {{ $s['bg'] }} rounded-pill fs-10 px-2">{{ $s['icon'] }} {{ $inv->payment_status }}</span>
                                        @if($inv->collection_type)
                                            <div class="fs-9 text-muted mt-1">{{ $inv->collection_type }}</div>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="dropdown">
                                            @php
                                                $sampleStatusColors = [
                                                    'Pending' => 'bg-soft-secondary text-secondary',
                                                    'Collected' => 'bg-soft-info text-info',
                                                    'Dispatched' => 'bg-soft-warning text-warning',
                                                    'Received' => 'bg-soft-primary text-primary',
                                                    'Processing' => 'bg-soft-danger text-danger',
                                                    'Ready' => 'bg-soft-success text-success',
                                                ];
                                                $c = $sampleStatusColors[$inv->sample_status] ?? 'bg-soft-secondary text-secondary';
                                            @endphp
                                            <button class="btn btn-sm dropdown-toggle py-0 px-2 fw-bold fs-10 {{ $c }}"
                                                type="button" data-bs-toggle="dropdown" data-bs-boundary="viewport" @if(!auth()->user()->can('edit invoices') && !auth()->user()->collection_center_id) disabled @endif>
                                                {{ $inv->sample_status ?? 'Pending' }}
                                            </button>
                                            @if(auth()->user()->can('edit invoices') || auth()->user()->collection_center_id)
                                                <ul class="dropdown-menu shadow-lg border-0 fs-11 p-1">
                                                    <li class="px-2 py-1 border-bottom mb-1 bg-light rounded-top"><small class="fw-bold text-muted text-uppercase">Update Sample Status</small></li>
                                                    @foreach(['Pending', 'Collected', 'Dispatched', 'Received', 'Processing', 'Ready'] as $st)
                                                        <li><a class="dropdown-item rounded-2 py-2 d-flex align-items-center gap-2 {{ ($inv->sample_status ?? 'Pending') == $st ? 'bg-primary text-white' : '' }}"
                                                                href="javascript:void(0)"
                                                                wire:click="updateSampleStatus({{ $inv->id }}, '{{ $st }}')">
                                                                @php
                                                                    $icons = ['Pending'=>'🕒','Collected'=>'💉','Dispatched'=>'🚚','Received'=>'🔬','Processing'=>'⚖️','Ready'=>'✅'];
                                                                @endphp
                                                                <span class="fs-14">{{ $icons[$st] ?? '' }}</span>
                                                                <span class="fw-bold">{{ $st }}</span>
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            @if(auth()->user()->can('edit invoices') || auth()->user()->collection_center_id)
                                                <a href="{{ route('lab.invoice.edit', $inv->id) }}" wire:navigate
                                                    class="action-btn action-btn-warning" title="Edit Invoice">
                                                    <i class="feather-edit-2"></i>
                                                </a>
                                                <a href="{{ route('lab.pos.summary', $inv->id) }}" wire:navigate
                                                    class="action-btn action-btn-info" title="View Summary">
                                                    <i class="feather-eye"></i>
                                                </a>
                                                <a href="{{ route('lab.reports.entry', $inv->id) }}" wire:navigate
                                                    class="action-btn action-btn-success" title="Enter Results">
                                                    <i class="feather-edit-3"></i>
                                                </a>
                                            @endif
                                            <div class="dropdown">
                                                <button class="action-btn action-btn-whatsapp dropdown-toggle"
                                                    type="button" data-bs-toggle="dropdown" data-bs-boundary="viewport" aria-expanded="false" @if(!$inv->patient->phone) disabled title="Phone missing" @endif>
                                                    <i class="bi bi-whatsapp"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                                    <li><a class="dropdown-item fs-11" href="{{ $inv->getWhatsappLink('invoice') }}" target="_blank"><i class="feather-file-text me-2 text-success"></i>Share Invoice</a></li>
                                                    @if($inv->status === 'Completed' || $inv->sample_status === 'Ready')
                                                        <li><a class="dropdown-item fs-11" href="{{ $inv->getWhatsappLink('report') }}" target="_blank"><i class="feather-check-circle me-2 text-success"></i>Share Report</a></li>
                                                    @endif
                                                </ul>
                                            </div>
                                            <div class="dropdown">
                                                <button class="action-btn action-btn-primary dropdown-toggle"
                                                    type="button" data-bs-toggle="dropdown" data-bs-boundary="viewport" aria-expanded="false">
                                                    <i class="feather-printer"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm p-1" style="min-width: 180px;">
                                                    <li>
                                                        <a class="dropdown-item fs-12 py-1 text-nowrap" href="javascript:void(0)" wire:click="printInvoice({{ $inv->id }}, 1)">
                                                            <i class="feather-file-text me-2 text-primary"></i> With Header
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item fs-12 py-1 text-nowrap" href="javascript:void(0)" wire:click="printInvoice({{ $inv->id }}, 0)">
                                                            <i class="feather-file me-2 text-warning"></i> Without Header
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider my-1"></li>
                                                    <li>
                                                        <a class="dropdown-item fs-12 py-1 fw-bold text-primary text-nowrap"
                                                            href="{{ route('lab.invoice.barcode.stickers', $inv->id) }}"
                                                            target="_blank">
                                                            <i class="feather-maximize me-2"></i> Barcode Stickers
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                            @if($inv->status !== 'Cancelled' && !in_array($inv->sample_status, ['Processing', 'Ready']))
                                                @if(auth()->user()->can('delete invoices') || auth()->user()->collection_center_id)
                                                    <button 
                                                        onclick="confirm('Are you sure you want to CANCEL this invoice? This action will VOID the invoice and REVERSE all credited commissions in Doctor/Agent wallets.') || event.stopImmediatePropagation()"
                                                        wire:click="cancelInvoice({{ $inv->id }})"
                                                        class="action-btn action-btn-danger" title="Cancel Invoice">
                                                        <i class="feather-x-circle"></i>
                                                    </button>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="feather-inbox text-muted" style="font-size:48px;"></i>
                                        <div class="text-muted fs-13 mt-2">No invoices found</div>
                                        <a href="{{ route('lab.pos') }}" wire:navigate
                                            class="btn btn-sm btn-primary mt-2"><i class="feather-plus me-1"></i>Create
                                            First Bill</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Footer --}}
                <div class="d-flex justify-content-between align-items-center px-3 py-3 border-top bg-light">
                    <div class="d-flex align-items-center gap-2">
                        <span class="fs-11 text-muted">Show</span>
                        <select class="form-select form-select-sm fw-bold" wire:model.live="perPage"
                            style="width:70px;">
                            <option value="10">10</option>
                            <option value="15">15</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                        <span class="fs-11 text-muted">
                            of <strong>{{ $invoices->total() }}</strong> invoices
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
                                    <li class="page-item disabled"><span class="page-link border-0 bg-transparent"><i
                                                class="feather-chevron-left fs-12"></i></span></li>
                                @else
                                    <li class="page-item"><button wire:click="previousPage"
                                            class="page-link border-0 bg-transparent"><i
                                                class="feather-chevron-left fs-12"></i></button></li>
                                @endif

                                {{-- Page Numbers --}}
                                @php
                                    $currentPage = $invoices->currentPage();
                                    $lastPage = $invoices->lastPage();
                                    $start = max(1, $currentPage - 2);
                                    $end = min($lastPage, $currentPage + 2);
                                @endphp

                                @if($start > 1)
                                    <li class="page-item"><button wire:click="gotoPage(1)"
                                            class="page-link border rounded-2 fs-11 fw-bold" style="min-width:32px;">1</button>
                                    </li>
                                    @if($start > 2)
                                        <li class="page-item disabled"><span
                                                class="page-link border-0 bg-transparent fs-11">…</span></li>
                                    @endif
                                @endif

                                @for($p = $start; $p <= $end; $p++)
                                    <li class="page-item {{ $p == $currentPage ? 'active' : '' }}">
                                        <button wire:click="gotoPage({{ $p }})"
                                            class="page-link border rounded-2 fs-11 fw-bold {{ $p == $currentPage ? 'bg-primary text-white border-primary' : '' }}"
                                            style="min-width:32px;">{{ $p }}</button>
                                    </li>
                                @endfor

                                @if($end < $lastPage)
                                    @if($end < $lastPage - 1)
                                        <li class="page-item disabled"><span
                                                class="page-link border-0 bg-transparent fs-11">…</span></li>
                                    @endif
                                    <li class="page-item"><button wire:click="gotoPage({{ $lastPage }})"
                                            class="page-link border rounded-2 fs-11 fw-bold"
                                            style="min-width:32px;">{{ $lastPage }}</button></li>
                                @endif

                                {{-- Next --}}
                                @if ($invoices->hasMorePages())
                                    <li class="page-item"><button wire:click="nextPage"
                                            class="page-link border-0 bg-transparent"><i
                                                class="feather-chevron-right fs-12"></i></button></li>
                                @else
                                    <li class="page-item disabled"><span class="page-link border-0 bg-transparent"><i
                                                class="feather-chevron-right fs-12"></i></span></li>
                                @endif
                            </ul>
                        </nav>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>