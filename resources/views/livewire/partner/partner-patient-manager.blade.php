<div class="row g-4">
    <div class="col-12">
        <div class="page-header mb-4">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10 fw-bold text-dark">Patient & Billing History</h5>
                    <p class="fs-13 text-muted mb-0 font-medium">Track your referrals, logistics, and earnings.</p>
                </div>
                <ul class="breadcrumb d-none d-md-flex ms-3">
                    <li class="breadcrumb-item"><a href="{{ route('partner.dashboard') }}" wire:navigate class="text-muted small">Home</a></li>
                    <li class="breadcrumb-item text-primary fw-medium small">Referrals</li>
                </ul>
            </div>
            <div class="page-header-right d-flex align-items-center gap-2">
                @if (session()->has('message'))
                    <span class="badge bg-soft-success text-success border border-success border-opacity-10 px-3 py-2 fs-11 fw-bold animated pulse-once shadow-sm me-2">
                         <i class="feather-check-circle me-1"></i>{{ session('message') }}
                    </span>
                @endif
                <div class="input-group shadow-sm border rounded-3 overflow-hidden bg-white" style="width: 250px;">
                    <span class="input-group-text bg-white border-0 pe-1">
                        <i class="feather-search text-primary fs-12"></i>
                    </span>
                    <input type="text" wire:model.live.debounce.300ms="search" 
                        class="form-control border-0 shadow-none fs-12 fw-medium" 
                        placeholder="Search patient...">
                </div>
                <div class="shadow-sm border rounded-3 bg-white px-2">
                    <select wire:model.live="dateRange" class="form-select border-0 shadow-none fs-12 fw-bold text-dark cursor-pointer py-1">
                        <option value="all">📅 All Time</option>
                        <option value="today">📅 Today</option>
                        <option value="week">📅 This Week</option>
                        <option value="month">📅 This Month</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 border-bottom border-light d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-bold text-dark"><i class="feather-list me-2 text-primary"></i>Recent Referrals</h6>
                <div class="ms-auto d-flex gap-2">
                    <span class="badge bg-soft-primary text-primary rounded-pill px-3 py-1 fs-11 fw-bold">{{ $invoices->total() }} Total</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="min-height: 200px;">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-soft-light-gray fs-11 fw-bold text-uppercase text-muted border-bottom">
                            <tr>
                                <th class="ps-4 py-3 border-0">Patient & Invoice #</th>
                                <th class="py-3 border-0">Referred Tests</th>
                                <th class="py-3 border-0">Billing & Profit</th>
                                <th class="py-3 border-0 text-center">Logistics</th>
                                <th class="py-3 border-0">Status</th>
                                <th class="text-end pe-4 py-3 border-0">Report</th>
                            </tr>
                        </thead>
                        <tbody class="fs-13">
                            @forelse($invoices as $inv)
                                <tr class="border-bottom border-light">
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar-text bg-soft-primary text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold fs-5 shadow-sm" style="width: 42px; height: 42px;">
                                                {{ strtoupper(substr($inv->patient->name ?? 'P', 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark fs-14">{{ $inv->patient->name ?? 'N/A' }}</div>
                                                <span class="badge bg-light text-primary border border-primary border-opacity-10 fs-10 fw-bold">{{ $inv->invoice_number }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1" style="max-width: 250px;">
                                            @foreach($inv->items->take(2) as $item)
                                                <span class="badge bg-soft-secondary text-dark border-0 rounded-pill px-2 py-1 fw-medium fs-10">{{ $item->labTest->name }}</span>
                                            @endforeach
                                            @if($inv->items->count() > 2)
                                                <span class="badge bg-light text-muted border-0 rounded-pill px-2 py-1 fw-medium fs-10">+{{ $inv->items->count() - 2 }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">₹{{ number_format($inv->total_amount, 2) }}</div>
                                        @if(Auth::user()->hasRole('collection_center'))
                                            <div class="fs-11 text-success fw-bold"><i class="feather-trending-up me-1"></i>Profit: ₹{{ number_format($inv->cc_profit_amount, 2) }}</div>
                                        @endif
                                        <div class="fs-10 text-muted mt-1">{{ $inv->invoice_date->format('d M, Y h:i A') }}</div>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm p-0 border-0 bg-transparent dropdown-toggle shadow-none no-caret" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                @php
                                                    $sampleStatusClass = [
                                                        'Pending' => 'bg-soft-secondary text-muted',
                                                        'Collected' => 'bg-soft-primary text-primary',
                                                        'Dispatched' => 'bg-soft-info text-info',
                                                        'Received' => 'bg-soft-success text-success',
                                                        'Processing' => 'bg-soft-warning text-warning',
                                                        'Ready' => 'bg-soft-success text-success shadow-sm',
                                                    ][$inv->sample_status ?? 'Pending'] ?? 'bg-soft-secondary text-secondary';
                                                @endphp
                                                <span class="badge {{ $sampleStatusClass }} rounded-pill px-2 py-1 fs-10 fw-bold border border-light">
                                                    {{ $inv->sample_status ?? 'Pending' }} <i class="feather-chevron-down fs-8 ms-1"></i>
                                                </span>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 fs-12 p-1 rounded-3">
                                                @foreach(['Pending', 'Collected', 'Dispatched', 'Received', 'Processing', 'Ready'] as $st)
                                                    <li>
                                                        <a class="dropdown-item rounded-2 py-2 {{ ($inv->sample_status ?? 'Pending') == $st ? 'bg-primary text-white fw-bold' : '' }}" 
                                                           href="javascript:void(0)" 
                                                           wire:click="updateSampleStatus({{ $inv->id }}, '{{ $st }}')">
                                                           {{ $st }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        @if($inv->sample_collected_at)
                                            <div class="fs-9 text-muted mt-1 text-center"><i class="feather-clock fs-8 me-1"></i>{{ $inv->sample_collected_at->format('d/m h:i A') }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $statusClass = [
                                                'Pending' => 'bg-soft-warning text-warning',
                                                'Completed' => 'bg-soft-success text-success',
                                                'Partial' => 'bg-soft-info text-info',
                                                'Cancelled' => 'bg-soft-danger text-danger'
                                            ][$inv->status] ?? 'bg-soft-secondary text-secondary';
                                        @endphp
                                        <span class="badge {{ $statusClass }} rounded-pill px-3 py-1 font-bold ls-1 fs-10 text-uppercase">
                                            {{ $inv->status }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        @if($inv->status == 'Completed')
                                            <a href="{{ route('partner.reports.print', $inv->id) }}" target="_blank" class="btn btn-sm btn-soft-primary px-3 rounded-pill fw-bold">
                                                <i class="feather-download me-1"></i> Report
                                            </a>
                                        @else
                                            <span class="badge bg-soft-info text-info fs-10 px-2 rounded-pill">Processing</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="text-muted mb-3"><i class="feather-users" style="font-size: 3.5rem; opacity: 0.15;"></i></div>
                                        <h6 class="fw-bold text-dark">No Patients Found</h6>
                                        <p class="text-muted fs-13 mb-0">You haven't referred any patients in this range.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($invoices->hasPages())
                <div class="card-footer bg-white border-top py-3 d-flex justify-content-center">
                    {{ $invoices->links() }}
                </div>
            @endif
        </div>
    </div>

    <style>
        .ls-1 { letter-spacing: 0.5px; }
        .font-medium { font-weight: 500; }
        .bg-soft-primary { background-color: rgba(59, 113, 202, 0.08) !important; }
        .bg-soft-light-gray { background-color: #f8f9fa !important; }
        .bg-soft-success { background-color: rgba(16, 185, 129, 0.08) !important; }
        .bg-soft-warning { background-color: rgba(245, 158, 11, 0.08) !important; }
        .bg-soft-info { background-color: rgba(6, 182, 212, 0.08) !important; }
        .bg-soft-secondary { background-color: #f1f5f9 !important; }
        .text-primary { color: #3b71ca !important; }
        .text-success { color: #10b981 !important; }
        .text-warning { color: #f59e0b !important; }
        
        .transition-all { transition: all 0.2s ease-in-out; }
        .hover-primary:hover { background-color: #3b71ca !important; color: #fff !important; border-color: #3b71ca !important; }
        
        .stretch { height: calc(100% - 30px); }
        .stretch-full { height: 100%; }
        
        .no-caret::after { display: none !important; }
        .dropdown-item:active { background-color: #3b71ca !important; }

        input[type="date"]::-webkit-calendar-picker-indicator { cursor: pointer; filter: invert(0.4); }
    </style>
</div>
