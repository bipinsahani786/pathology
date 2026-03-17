<div>
    {{-- ======================== PAGE HEADER ======================== --}}
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Invoices</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                <li class="breadcrumb-item">Lab</li>
                <li class="breadcrumb-item">Invoices</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <a href="{{ route('lab.pos') }}" wire:navigate class="btn btn-primary"><i class="feather-plus me-1"></i>New Bill</a>
        </div>
    </div>

    {{-- ======================== MAIN CONTENT ======================== --}}
    <div class="main-content">

        {{-- ═══════ Stats Cards ═══════ --}}
        <div class="row g-3 mb-4">
            <div class="col-md-3 col-6">
                <div class="card mb-0">
                    <div class="card-body py-3 d-flex align-items-center gap-3">
                        <div class="avatar-text avatar-lg rounded-3" style="background:rgba(59,113,202,0.1);"><i class="feather-file-text text-primary" style="font-size:22px;"></i></div>
                        <div>
                            <div class="fs-10 fw-bold text-muted text-uppercase">Total Bills</div>
                            <div class="fs-3 fw-bold text-primary">{{ number_format($stats['total']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card mb-0">
                    <div class="card-body py-3 d-flex align-items-center gap-3">
                        <div class="avatar-text avatar-lg rounded-3" style="background:rgba(25,135,84,0.1);"><i class="feather-calendar text-success" style="font-size:22px;"></i></div>
                        <div>
                            <div class="fs-10 fw-bold text-muted text-uppercase">Today's Bills</div>
                            <div class="fs-3 fw-bold text-success">{{ number_format($stats['today']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card mb-0">
                    <div class="card-body py-3 d-flex align-items-center gap-3">
                        <div class="avatar-text avatar-lg rounded-3" style="background:rgba(13,110,253,0.1);"><i class="feather-trending-up" style="font-size:22px;color:#0d6efd;"></i></div>
                        <div>
                            <div class="fs-10 fw-bold text-muted text-uppercase">Today Revenue</div>
                            <div class="fs-3 fw-bold" style="color:#0d6efd;">₹{{ number_format($stats['todayRevenue'], 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card mb-0">
                    <div class="card-body py-3 d-flex align-items-center gap-3">
                        <div class="avatar-text avatar-lg rounded-3" style="background:rgba(220,53,69,0.1);"><i class="feather-alert-circle text-danger" style="font-size:22px;"></i></div>
                        <div>
                            <div class="fs-10 fw-bold text-muted text-uppercase">Total Due</div>
                            <div class="fs-3 fw-bold text-danger">₹{{ number_format($stats['due'], 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════ Filters & Search ═══════ --}}
        <div class="card mb-3">
            <div class="card-body py-3">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-bold fs-10 text-muted text-uppercase mb-1">Search</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="feather-search"></i></span>
                            <input type="text" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Invoice#, Patient Name, Phone...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold fs-10 text-muted text-uppercase mb-1">Payment Status</label>
                        <select class="form-select form-select-sm" wire:model.live="filterStatus">
                            <option value="">All</option>
                            <option value="Paid">✅ Paid</option>
                            <option value="Partial">⚠️ Partial</option>
                            <option value="Unpaid">❌ Unpaid</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold fs-10 text-muted text-uppercase mb-1">From Date</label>
                        <input type="date" class="form-control form-control-sm" wire:model.live="filterDateFrom">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold fs-10 text-muted text-uppercase mb-1">To Date</label>
                        <input type="date" class="form-control form-control-sm" wire:model.live="filterDateTo">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold fs-10 text-muted text-uppercase mb-1">Collection</label>
                        <select class="form-select form-select-sm" wire:model.live="filterCollectionType">
                            <option value="">All</option>
                            <option value="Center">🏥 Center</option>
                            <option value="Home Collection">🏠 Home</option>
                            <option value="Hospital">🏨 Hospital</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button wire:click="clearFilters" class="btn btn-sm btn-outline-secondary w-100" title="Clear Filters">
                            <i class="feather-x"></i>
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
                                <th class="text-center" style="width:120px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoices as $i => $inv)
                                <tr class="fs-12">
                                    <td class="ps-3 text-muted">{{ $invoices->firstItem() + $i }}</td>
                                    <td>
                                        <span class="fw-bold text-primary">{{ $inv->invoice_number }}</span>
                                        <div class="fs-10 text-muted">{{ $inv->barcode }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $inv->patient->name ?? 'N/A' }}</div>
                                        <div class="fs-10 text-muted">📞 {{ $inv->patient->phone ?? '' }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark rounded-pill fs-10">{{ $inv->items->count() }} tests</span>
                                        <div class="fs-10 text-muted">{{ $inv->items->pluck('test_name')->take(2)->implode(', ') }}{{ $inv->items->count() > 2 ? '...' : '' }}</div>
                                    </td>
                                    <td class="text-end fw-bold">₹{{ number_format($inv->total_amount, 0) }}</td>
                                    <td class="text-end fw-bold" style="color:#198754;">₹{{ number_format($inv->paid_amount, 0) }}</td>
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
                                        <span class="badge {{ $s['bg'] }} rounded-pill fs-10 px-2">{{ $s['icon'] }} {{ $inv->payment_status }}</span>
                                        <div class="fs-9 text-muted">{{ $inv->collection_type ?? '' }}</div>
                                    </td>
                                    <td>
                                        <div class="fs-11">{{ $inv->invoice_date->format('d M Y') }}</div>
                                        <div class="fs-10 text-muted">{{ $inv->invoice_date->format('h:i A') }}</div>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <a href="{{ route('lab.invoice.edit', $inv->id) }}" wire:navigate class="btn btn-sm btn-outline-warning px-2" title="Edit Invoice">
                                                <i class="feather-edit-2 fs-12"></i>
                                            </a>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-primary dropdown-toggle px-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="feather-printer fs-12"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                                    <li>
                                                        <a class="dropdown-item fs-11" href="{{ route('lab.invoice.pdf', $inv->id) }}" target="_blank">
                                                            <i class="feather-file-text me-2 text-primary"></i>📄 PDF (With Header)
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item fs-11" href="{{ route('lab.invoice.pdf.plain', $inv->id) }}" target="_blank">
                                                            <i class="feather-minimize me-2 text-warning"></i>📋 PDF (Without Header)
                                                            <div class="fs-9 text-muted ms-4">For letterpad printing</div>
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item fs-11" href="{{ route('lab.invoice.print', $inv->id) }}" target="_blank">
                                                            <i class="feather-monitor me-2 text-info"></i>🖥️ Browser Print
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-5">
                                        <i class="feather-inbox text-muted" style="font-size:48px;"></i>
                                        <div class="text-muted fs-13 mt-2">No invoices found</div>
                                        <a href="{{ route('lab.pos') }}" wire:navigate class="btn btn-sm btn-primary mt-2"><i class="feather-plus me-1"></i>Create First Bill</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Footer --}}
                <div class="d-flex justify-content-between align-items-center px-3 py-3 border-top bg-gray-50">
                    <div class="d-flex align-items-center gap-2">
                        <span class="fs-11 text-muted">Show</span>
                        <select class="form-select form-select-sm fw-bold" wire:model.live="perPage" style="width:70px;">
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
