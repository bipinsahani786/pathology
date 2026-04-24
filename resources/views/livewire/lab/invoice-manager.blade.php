<div>
    <style>
        .dropdown-menu { border: 1px solid #e0e0e0; box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important; z-index: 2000; background: #fff !important; min-width: 160px; }
        .grayscale { filter: grayscale(1); }
        .opacity-50 { opacity: 0.5; }
        .btn-status { border-bottom: 2px solid transparent; }
        .dropdown-item:hover { background-color: #f8f9fa; }
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
            @can('create pos')
                <a href="{{ route('lab.pos') }}" wire:navigate class="btn btn-primary"><i class="feather-plus me-1"></i>New Bill</a>
            @endcan
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
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-light">
                            <tr class="fs-11 fw-bold text-uppercase text-muted">
                                <th class="ps-3" style="width:50px;">#</th>
                                <th>Invoice #</th>
                                <th>Patient</th>
                                <th>Tests</th>
                                <th class="text-end">Amount</th>
                                <th class="text-end">Paid</th>
                                <th class="text-end">Due</th>
                                <th class="text-center">Status</th>
                                <th>Date</th>
                                <th class="text-center">Sample Status</th>
                                <th class="text-center" style="width:120px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoices as $i => $inv)
                                <tr class="fs-12 {{ $inv->status === 'Cancelled' ? 'opacity-50 grayscale' : '' }}">
                                    <td class="ps-3 text-muted">{{ $invoices->firstItem() + $i }}</td>
                                    <td>
                                        <span class="fw-bold text-primary">{{ $inv->invoice_number }}</span>
                                        @if($inv->status === 'Cancelled')
                                            <span class="badge bg-danger fs-9 ms-1">CANCELLED</span>
                                        @endif
                                        <div class="fs-11 fw-bold text-dark"><i
                                                class="feather-hash me-1 text-muted fs-10"></i>{{ $inv->barcode }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $inv->patient->name }}</div>
                                        <div class="badge bg-soft-info text-info fs-9 fw-bold p-1">{{ $inv->patient->formatted_id }}</div>
                                        <div class="fs-11 text-muted"><i class="feather-phone me-1 fs-10"></i>{{ $inv->patient->phone }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark rounded-pill fs-10">{{ $inv->items->count() }}
                                            tests</span>
                                        <div class="fs-10 text-muted">
                                            {{ $inv->items->pluck('test_name')->take(2)->implode(', ') }}{{ $inv->items->count() > 2 ? '...' : '' }}
                                        </div>
                                    </td>
                                    <td class="text-end fw-bold">₹{{ number_format($inv->total_amount, 0) }}</td>
                                    <td class="text-end fw-bold" style="color:#198754;">
                                        ₹{{ number_format($inv->paid_amount, 0) }}</td>
                                    <td class="text-end fw-bold {{ $inv->due_amount > 0 ? 'text-danger' : '' }}">
                                        {{ $inv->due_amount > 0 ? '₹' . number_format($inv->due_amount, 0) : '—' }}
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
                                        <span class="badge {{ $s['bg'] }} rounded-pill fs-10 px-2">{{ $s['icon'] }}
                                            {{ $inv->payment_status }}</span>
                                        <div class="fs-9 text-muted">{{ $inv->collection_type ?? '' }}</div>
                                    </td>
                                    <td>
                                        <div class="fs-11">{{ $inv->invoice_date->format('d M Y') }}</div>
                                        <div class="fs-10 text-muted">{{ $inv->invoice_date->format('h:i A') }}</div>
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
                                                type="button" data-bs-toggle="dropdown" @cannot('edit invoices') disabled @endcannot>
                                                {{ $inv->sample_status ?? 'Pending' }}
                                            </button>
                                            @can('edit invoices')
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
                                            @endcan
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            @can('edit invoices')
                                                <a href="{{ route('lab.invoice.edit', $inv->id) }}" wire:navigate
                                                    class="btn btn-sm btn-outline-warning px-2" title="Edit Invoice">
                                                    <i class="feather-edit-2 fs-12"></i>
                                                </a>
                                                <a href="{{ route('lab.pos.summary', $inv->id) }}" wire:navigate
                                                    class="btn btn-sm btn-outline-info px-2" title="View Summary">
                                                    <i class="feather-eye fs-12"></i>
                                                </a>
                                                <a href="{{ route('lab.reports.entry', $inv->id) }}" wire:navigate
                                                    class="btn btn-sm btn-outline-success px-2" title="Enter Results">
                                                    <i class="feather-edit-3 fs-12"></i>
                                                </a>
                                            @endcan
                                                <button class="btn btn-sm btn-outline-success dropdown-toggle px-2"
                                                    type="button" data-bs-toggle="dropdown" aria-expanded="false" @if(!$inv->patient->phone) disabled title="Phone missing" @endif>
                                                    <i class="bi bi-whatsapp fs-12"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                                    <li><a class="dropdown-item fs-11" href="{{ $inv->getWhatsappLink('invoice') }}" target="_blank"><i class="feather-file-text me-2 text-success"></i>Share Invoice</a></li>
                                                    @if($inv->status === 'Completed' || $inv->sample_status === 'Ready')
                                                        <li><a class="dropdown-item fs-11" href="{{ $inv->getWhatsappLink('report') }}" target="_blank"><i class="feather-check-circle me-2 text-success"></i>Share Report</a></li>
                                                    @endif
                                                </ul>
                                            </div>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-primary dropdown-toggle px-2"
                                                    type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="feather-printer fs-12"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                                    <li>
                                                        <a class="dropdown-item fs-11" href="javascript:void(0)" wire:click="printInvoice({{ $inv->id }}, 1)">
                                                            <i class="feather-file-text me-2 text-primary"></i>📄 PDF (With Header)
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item fs-11" href="javascript:void(0)" wire:click="printInvoice({{ $inv->id }}, 0)">
                                                            <i class="feather-minimize me-2 text-warning"></i>📋 PDF (Without Header)
                                                            <div class="fs-9 text-muted ms-4">For letterpad printing</div>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item fs-11 fw-bold text-primary"
                                                            href="{{ route('lab.invoice.barcode.stickers', $inv->id) }}"
                                                            target="_blank">
                                                            <i class="feather-maximize me-2"></i>🏷️ Barcode Stickers
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                            @if($inv->status !== 'Cancelled' && !in_array($inv->sample_status, ['Processing', 'Ready']))
                                                @can('delete invoices')
                                                    <button 
                                                        onclick="confirm('Are you sure you want to CANCEL this invoice? This action will VOID the invoice and REVERSE all credited commissions in Doctor/Agent wallets.') || event.stopImmediatePropagation()"
                                                        wire:click="cancelInvoice({{ $inv->id }})"
                                                        class="btn btn-sm btn-outline-danger px-2" title="Cancel Invoice">
                                                        <i class="feather-x-circle fs-12"></i>
                                                    </button>
                                                @endcan
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-5">
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