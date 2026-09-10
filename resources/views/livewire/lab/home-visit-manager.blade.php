<div>
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="text-dark fw-bold">Home Visits</h5>
                <p class="fs-13 text-muted mb-0">Track & manage home sample collection visits.</p>
            </div>
            <ul class="breadcrumb d-none d-md-flex ms-3">
                <li class="breadcrumb-item"><a href="{{ route('lab.dashboard') }}" wire:navigate class="text-muted">Home</a></li>
                <li class="breadcrumb-item text-primary fw-medium">Home Visits</li>
            </ul>
        </div>
    </div>

    <div class="main-content">
        @if(session()->has('message'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 d-flex align-items-center py-3 alert-dismissible fade show">
            <i class="feather-check-circle fs-4 me-2"></i> <strong>{{ session('message') }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        {{-- TAB SWITCHER --}}
        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
            <ul class="nav nav-pills gap-2">
                <li class="nav-item">
                    <button wire:click="setTab('visits')" 
                            class="btn rounded-pill px-4 py-2 fw-bold d-flex align-items-center gap-2 {{ $activeTab === 'visits' ? 'btn-primary text-white shadow' : 'btn-white bg-white text-secondary shadow-sm border' }}">
                        <i class="feather-map-pin"></i>
                        <span>Home Visits Operations</span>
                        <span class="badge {{ $activeTab === 'visits' ? 'bg-white text-primary' : 'bg-primary text-white' }} rounded-pill ms-1">{{ $visits->total() }}</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button wire:click="setTab('analytics')" 
                            class="btn rounded-pill px-4 py-2 fw-bold d-flex align-items-center gap-2 {{ $activeTab === 'analytics' ? 'btn-primary text-white shadow' : 'btn-white bg-white text-secondary shadow-sm border' }}">
                        <i class="feather-pie-chart"></i>
                        <span>Phlebotomist Accounts & Dues</span>
                        @if($totalPendingCashAll > 0)
                        <span class="badge bg-danger text-white rounded-pill ms-1">₹{{ number_format($totalPendingCashAll, 0) }} Due to Lab</span>
                        @else
                        <span class="badge bg-success text-white rounded-pill ms-1">✓ All Cleared</span>
                        @endif
                    </button>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-2">
                <div class="bg-white border rounded-pill px-3 py-1 shadow-sm d-flex align-items-center gap-2 fs-12">
                    <span class="text-muted">Field Cash Outstanding:</span>
                    <strong class="{{ $totalPendingCashAll > 0 ? 'text-danger' : 'text-success' }}">₹{{ number_format($totalPendingCashAll, 2) }}</strong>
                </div>
                <div class="bg-white border rounded-pill px-3 py-1 shadow-sm d-flex align-items-center gap-2 fs-12">
                    <span class="text-muted">Total Commissions:</span>
                    <strong class="text-primary">₹{{ number_format($totalCommissionEarnedAll, 2) }}</strong>
                </div>
            </div>
        </div>

        @if($activeTab === 'visits')
        {{-- Status Summary Cards --}}
        <div class="row g-3 mb-4">
            @php
                $cards = [
                    'Pending'   => ['warning','clock', $statusCounts['Pending'] ?? 0],
                    'Assigned'  => ['primary','user-check', $statusCounts['Assigned'] ?? 0],
                    'En Route'  => ['info','navigation', $statusCounts['En Route'] ?? 0],
                    'Completed' => ['success','check-circle', $completedTodayCount],
                    'Cancelled' => ['danger','x-circle', $statusCounts['Cancelled'] ?? 0],
                ];
            @endphp
            @foreach($cards as $s => [$color, $icon, $cnt])
            <div class="col-6 col-md-2">
                <div class="card border-0 shadow-sm rounded-4 text-center p-3">
                    <i class="feather-{{ $icon }} fs-3 text-{{ $color }} mb-1"></i>
                    <div class="fw-bold fs-4 text-{{ $color }}">{{ $cnt }}</div>
                    <div class="text-muted fs-11">{{ $s }}</div>
                </div>
            </div>
            @endforeach
            <div class="col-6 col-md-2">
                <div class="card border-0 shadow-sm rounded-4 text-center p-3" style="background: {{ $totalPendingCashAll > 0 ? '#fffdf0' : '#f8fafc' }};">
                    <i class="feather-dollar-sign fs-3 {{ $totalPendingCashAll > 0 ? 'text-danger' : 'text-success' }} mb-1"></i>
                    <div class="fw-bold fs-4 {{ $totalPendingCashAll > 0 ? 'text-danger' : 'text-success' }}">₹{{ number_format($totalPendingCashAll, 0) }}</div>
                    <div class="text-muted fs-11">{{ $totalPendingCashAll > 0 ? 'Field Cash Due' : 'Cash Cleared' }}</div>
                </div>
            </div>
        </div>

        {{-- Pending Phlebotomist Cash Handover Approvals --}}
        @if(isset($pendingHandovers) && $pendingHandovers->isNotEmpty())
        <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: linear-gradient(135deg, #fffdf0 0%, #fef3c7 100%); border-left: 5px solid #f59e0b !important;">
            <div class="card-body p-3 p-md-4">
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle bg-warning bg-opacity-25 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="feather-dollar-sign text-warning fs-5"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-15">Phlebotomist Cash Handover Verification ({{ $pendingHandovers->count() }})</h6>
                            <span class="text-muted fs-12">Phlebotomists have deposited physical collection cash. Verify the currency note & click approve to settle.</span>
                        </div>
                    </div>
                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fs-12 fw-bold">
                        <i class="feather-alert-circle me-1"></i> Admin Action Required
                    </span>
                </div>

                <div class="row g-3">
                    @foreach($pendingHandovers as $handover)
                    <div class="col-md-6 col-lg-4" wire:key="pending-handover-{{ $handover->id }}">
                        <div class="card border-0 shadow-sm rounded-3 bg-white p-3 h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <div class="fw-bold text-dark fs-14">{{ $handover->phlebotomist->name ?? 'Phlebotomist' }}</div>
                                        <div class="text-muted fs-11"><i class="feather-phone me-1"></i>{{ $handover->phlebotomist->phone ?? 'N/A' }}</div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fs-5 fw-extrabold text-success">₹{{ number_format($handover->amount, 2) }}</div>
                                        <div class="text-muted fs-10">{{ $handover->created_at->format('d M, h:i A') }}</div>
                                    </div>
                                </div>
                                @if($handover->notes)
                                <div class="bg-light rounded-2 p-2 mb-3 fs-11 text-secondary">
                                    <i class="feather-message-square me-1 text-muted"></i> "{{ $handover->notes }}"
                                </div>
                                @else
                                <div class="mb-2"></div>
                                @endif
                            </div>
                            <div class="d-flex gap-2 pt-2 border-top">
                                <button wire:click="approveCashHandover({{ $handover->id }})" 
                                        wire:loading.attr="disabled"
                                        class="btn btn-success btn-sm w-100 rounded-pill fw-bold py-1 fs-12">
                                    <i class="feather-check-circle me-1"></i> Accept & Settle Cash
                                </button>
                                <button wire:click="rejectCashHandover({{ $handover->id }})" 
                                        wire:confirm="Are you sure you want to reject this cash handover request?"
                                        wire:loading.attr="disabled"
                                        class="btn btn-outline-danger btn-sm rounded-pill px-3 py-1 fs-12"
                                        title="Reject Handover">
                                    <i class="feather-x"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- Filters --}}
        <div class="card border-0 shadow-sm rounded-4 mb-3">
            <div class="card-body py-3">
                <div class="row g-3 align-items-center">
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="feather-search text-primary"></i></span>
                            <input type="text" wire:model.live.debounce.300ms="searchTerm" class="form-control" placeholder="Search patient, address...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <input type="date" wire:model.live="filterDate" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <select wire:model.live="filterStatus" class="form-select">
                            <option value="">All Status</option>
                            @foreach(['Pending','Assigned','En Route','Arrived','Collected','Dispatched','Received','Cancelled'] as $s)
                            <option value="{{ $s }}">{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select wire:model.live="filterPhlebotomist" class="form-select">
                            <option value="">All Phlebotomists</option>
                            @foreach($phlebotomists as $ph)
                            <option value="{{ $ph->id }}">{{ $ph->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button wire:click="$set('filterDate', '')" class="btn btn-outline-secondary w-100">
                            <i class="feather-x me-1"></i> Clear
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Visits Table --}}
        <div class="card stretch stretch-full border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light fs-11 fw-bold text-uppercase text-muted">
                            <tr>
                                <th class="ps-4 py-3">Invoice / Patient</th>
                                <th class="py-3">Address & Slot</th>
                                <th class="py-3">Phlebotomist</th>
                                <th class="text-center py-3">Status</th>
                                <th class="text-center pe-4 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($visits as $visit)
                            @php
                                $statusColors = [
                                    'Pending'    => 'warning',
                                    'Assigned'   => 'primary',
                                    'En Route'   => 'info',
                                    'Arrived'    => 'indigo',
                                    'Collected'  => 'success',
                                    'Dispatched' => 'teal',
                                    'Received'   => 'success',
                                    'Cancelled'  => 'danger',
                                ];
                                $c = $statusColors[$visit->status] ?? 'secondary';
                            @endphp
                            <tr wire:key="visit-{{ $visit->id }}" class="border-bottom border-light">
                                <td class="ps-4 py-3">
                                    <div class="fw-semibold text-dark">#{{ $visit->invoice->invoice_number ?? $visit->invoice_id }}</div>
                                    <div class="text-muted fs-12">{{ $visit->invoice->patient->name ?? '—' }}</div>
                                    <div class="text-muted fs-12">
                                        <i class="feather-phone fs-11 me-1"></i>
                                        {{ $visit->invoice->patient->phone ?? '—' }}
                                    </div>
                                </td>
                                <td class="py-3" style="max-width:220px;">
                                    <div class="text-dark fs-12 text-truncate" title="{{ $visit->collection_address }}">
                                        <i class="feather-map-pin text-danger me-1"></i>{{ $visit->collection_address }}
                                    </div>
                                    @if($visit->collection_landmark)
                                    <div class="text-muted fs-11">{{ $visit->collection_landmark }}</div>
                                    @endif
                                    <div class="text-primary fw-semibold fs-12 mt-1">
                                        <i class="feather-clock me-1"></i>
                                        {{ $visit->scheduled_date?->format('d M') }}
                                        @if($visit->scheduled_slot_start)
                                        {{ date('h:i A', strtotime($visit->scheduled_slot_start)) }}
                                        @endif
                                    </div>
                                    @if($visit->collection_lat && $visit->collection_lng)
                                    <a href="{{ $visit->google_maps_link }}" target="_blank" class="text-success fs-11">
                                        <i class="feather-map me-1"></i>Maps
                                    </a>
                                    @endif
                                </td>
                                <td class="py-3">
                                    @if($visit->phlebotomist)
                                    <div class="d-flex align-items-center gap-1">
                                        <span class="fw-semibold text-dark fs-13">{{ $visit->phlebotomist->name }}</span>
                                        @if(config('features.impersonation', true) && (auth()->user()->hasAnyRole(['super_admin', 'lab_admin', 'branch_admin']) || auth()->user()->can('edit phlebotomists') || auth()->user()->can('view phlebotomists')))
                                        <a href="{{ route('impersonate.start', $visit->phlebotomist_id) }}" class="badge bg-soft-dark text-dark border-0 ms-1" title="Login As {{ $visit->phlebotomist->name }}">
                                            <i class="feather-user-check fs-11"></i>
                                        </a>
                                        @endif
                                    </div>
                                    <div class="text-muted fs-12">{{ $visit->phlebotomist->phone }}</div>
                                    @else
                                    <span class="text-warning fs-12 fw-semibold">⚠ Unassigned</span>
                                    @endif
                                </td>
                                <td class="text-center py-3">
                                    <span class="badge bg-soft-{{ $c }} text-{{ $c }} rounded-pill px-3 py-2">
                                        {{ $visit->status }}
                                    </span>
                                </td>
                                <td class="text-center pe-4 py-3">
                                    <div class="d-flex gap-2 justify-content-center">
                                        @can('assign home_collections')
                                        @if(in_array($visit->status, ['Pending','Assigned']))
                                        <button wire:click="openAssignModal({{ $visit->id }})" class="btn btn-sm btn-outline-primary rounded-3" title="Assign Phlebotomist">
                                            <i class="feather-user-check"></i>
                                        </button>
                                        @endif
                                        <button wire:click="openStatusModal({{ $visit->id }}, '{{ $visit->status }}')" class="btn btn-sm btn-outline-info rounded-3" title="Update Status">
                                            <i class="feather-refresh-cw"></i>
                                        </button>
                                        @endcan
                                        <button wire:click="openTrackingModal({{ $visit->id }})" class="btn btn-sm btn-outline-success rounded-3" title="Live Tracking & GPS Audit Trail">
                                            <i class="feather-map-pin"></i>
                                        </button>
                                        <a href="{{ route('lab.invoice.barcode.stickers', $visit->invoice_id) }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-3" title="Print Barcode">
                                            <i class="feather-tag"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="feather-home fs-1 d-block mb-2 opacity-25"></i>
                                    No home visits found for selected filters.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-4 py-3">{{ $visits->links() }}</div>
            </div>
        </div>
        @else
        {{-- ========================================================================= --}}
        {{-- PHLEBOTOMIST FINANCIAL ANALYTICS & COLLECTION ACCOUNTS LEDGER           --}}
        {{-- ========================================================================= --}}

        {{-- Period Filter Toolbar --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="fs-12 fw-bold text-muted text-uppercase me-2"><i class="feather-calendar me-1"></i>Report Period:</span>
                        <div class="btn-group btn-group-sm" role="group">
                            @foreach(['all' => 'All Time', 'today' => 'Today', 'this_week' => 'This Week', 'this_month' => 'This Month'] as $pkey => $plabel)
                            <button type="button" 
                                    wire:click="setAnalyticsPeriod('{{ $pkey }}')"
                                    class="btn rounded-pill px-3 py-1 fs-12 fw-semibold {{ $analyticsPeriod === $pkey ? 'btn-primary' : 'btn-outline-secondary' }}">
                                {{ $plabel }}
                            </button>
                            @endforeach
                        </div>
                    </div>
                    <div class="text-muted fs-12">
                        <i class="feather-info me-1 text-primary"></i> Data reflects physical collections, cash deposits & completed visits.
                    </div>
                </div>
            </div>
        </div>

        {{-- Top 4 KPI Metrics (Compact & Clean) --}}
        <div class="row g-3 mb-4">
            {{-- 1. Field Cash Due (Kisse Kitna Lena Hai) --}}
            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fs-12 text-uppercase fw-bold text-muted">Field Cash Due</span>
                        <span class="badge {{ $totalPendingCashAll > 0 ? 'bg-danger text-white' : 'bg-soft-success text-success' }} rounded-pill px-2 py-1 fs-10 fw-bold">
                            {{ $totalPendingCashAll > 0 ? 'Lab Ko Lena Hai' : 'Reconciled' }}
                        </span>
                    </div>
                    <div class="fs-22 fw-extrabold {{ $totalPendingCashAll > 0 ? 'text-danger' : 'text-dark' }}">
                        ₹{{ number_format($totalPendingCashAll, 2) }}
                    </div>
                    <div class="text-muted fs-11 mt-1">Pending from phlebotomists</div>
                </div>
            </div>

            {{-- 2. Cash Settled & Approved --}}
            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fs-12 text-uppercase fw-bold text-muted">Cash Settled</span>
                        <span class="badge bg-soft-success text-success rounded-pill px-2 py-1 fs-10 fw-bold">
                            Verified
                        </span>
                    </div>
                    <div class="fs-22 fw-extrabold text-success">
                        ₹{{ number_format($totalCashSettledAll, 2) }}
                    </div>
                    <div class="text-muted fs-11 mt-1">Deposited at reception</div>
                </div>
            </div>

            {{-- 3. Total Phlebotomist Commissions --}}
            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fs-12 text-uppercase fw-bold text-muted">Commissions</span>
                        <span class="badge bg-soft-primary text-primary rounded-pill px-2 py-1 fs-10 fw-bold">
                            Payable
                        </span>
                    </div>
                    <div class="fs-22 fw-extrabold text-primary">
                        ₹{{ number_format($totalCommissionEarnedAll, 2) }}
                    </div>
                    <div class="text-muted fs-11 mt-1">Earned for completed visits</div>
                </div>
            </div>

            {{-- 4. Completed Collections --}}
            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fs-12 text-uppercase fw-bold text-muted">Collections</span>
                        <span class="badge bg-soft-info text-info rounded-pill px-2 py-1 fs-10 fw-bold">
                            Field Activity
                        </span>
                    </div>
                    <div class="fs-22 fw-extrabold text-dark">
                        {{ $totalCompletedVisitsAll }} <span class="fs-13 fw-normal text-muted">Visits</span>
                    </div>
                    <div class="text-muted fs-11 mt-1">Doorstep samples collected</div>
                </div>
            </div>
        </div>

        {{-- Phlebotomist Accounts & Dues Ledger Table --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h6 class="fw-bold text-dark mb-0 fs-15"><i class="feather-users me-2 text-primary"></i>Phlebotomist Financial Accounts & Collection Ledger</h6>
                    <span class="text-muted fs-12">Detailed breakdown of physical cash collected, pending dues to lab, and commission payout for each phlebotomist.</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light fs-11 fw-bold text-uppercase text-muted">
                            <tr>
                                <th class="ps-4 py-3">Phlebotomist</th>
                                <th class="text-center py-3">Completed Visits</th>
                                <th class="py-3">Cash Collected</th>
                                <th class="py-3">Settled / Deposited</th>
                                <th class="py-3">Cash in Hand (Lab Ko Lena Hai)</th>
                                <th class="py-3">Commission Rate</th>
                                <th class="py-3">Commission Earned</th>
                                <th class="text-center pe-4 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($phlebotomistAnalytics as $item)
                            <tr wire:key="phlebo-acc-{{ $item['user']->id }}">
                                {{-- Phlebotomist Info --}}
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar avatar-md rounded-circle bg-soft-primary text-primary fw-bold d-flex align-items-center justify-content-center" style="width:40px;height:40px;font-size:14px;">
                                            {{ strtoupper(substr($item['user']->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark fs-14 d-flex align-items-center gap-1">
                                                {{ $item['user']->name }}
                                                @if(config('features.impersonation', true) && (auth()->user()->hasAnyRole(['super_admin', 'lab_admin', 'branch_admin']) || auth()->user()->can('edit phlebotomists') || auth()->user()->can('view phlebotomists')))
                                                <a href="{{ route('impersonate.start', $item['user']->id) }}" class="badge bg-soft-dark text-dark border-0 ms-1" title="Login As {{ $item['user']->name }}">
                                                    <i class="feather-user-check fs-11"></i>
                                                </a>
                                                @endif
                                            </div>
                                            <div class="text-muted fs-12">
                                                <i class="feather-phone fs-11 me-1"></i>{{ $item['user']->phone ?? 'N/A' }}
                                                @if($item['profile']?->vehicle_number)
                                                • <i class="feather-truck fs-11 me-1"></i>{{ $item['profile']->vehicle_type ?? 'Vehicle' }}: {{ $item['profile']->vehicle_number }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Completed Visits --}}
                                <td class="text-center py-3">
                                    <span class="badge bg-soft-primary text-primary rounded-pill px-3 py-1 fs-12 fw-bold">
                                        {{ $item['completed_visits'] }} Visits
                                    </span>
                                </td>

                                {{-- Total Cash Collected --}}
                                <td class="py-3">
                                    <div class="fw-bold text-dark fs-14">₹{{ number_format($item['total_cash_collected'], 2) }}</div>
                                    @if($item['digital_collected'] > 0)
                                    <div class="text-muted fs-11">+ ₹{{ number_format($item['digital_collected'], 2) }} (UPI/Online)</div>
                                    @endif
                                </td>

                                {{-- Cash Settled --}}
                                <td class="py-3">
                                    <div class="fw-bold text-success fs-14">
                                        ₹{{ number_format($item['approved_handovers'], 2) }}
                                    </div>
                                    <span class="badge bg-soft-success text-success rounded-pill px-2 py-0 fs-10">
                                        <i class="feather-check me-1"></i>Deposited
                                    </span>
                                </td>

                                {{-- Cash in Hand (Lab Ko Lena Hai) --}}
                                <td class="py-3">
                                    @if($item['cash_in_hand_due'] > 0)
                                    <div class="d-inline-flex flex-column">
                                        <span class="badge bg-danger rounded-pill px-3 py-1 fs-12 fw-bold text-white shadow-sm">
                                            <i class="feather-alert-triangle me-1"></i>₹{{ number_format($item['cash_in_hand_due'], 2) }} Due to Lab
                                        </span>
                                        @if($item['pending_handovers'] > 0)
                                        <span class="text-warning fs-11 fw-semibold mt-1">
                                            <i class="feather-clock me-1"></i>₹{{ number_format($item['pending_handovers'], 2) }} pending approval
                                        </span>
                                        @endif
                                    </div>
                                    @else
                                    <span class="badge bg-soft-success text-success rounded-pill px-3 py-1 fs-12 fw-semibold">
                                        <i class="feather-check-circle me-1"></i>All Clear (₹0.00)
                                    </span>
                                    @endif
                                </td>

                                {{-- Commission Rate --}}
                                <td class="py-3">
                                    <div class="d-flex align-items-center gap-1">
                                        <span class="fw-bold text-dark fs-13">₹{{ number_format($item['commission_rate'], 2) }}</span>
                                        <span class="text-muted fs-11">/ visit</span>
                                        @can('assign home_collections')
                                        <button wire:click="openCommissionModal({{ $item['user']->id }})" 
                                                class="btn btn-xs btn-outline-secondary rounded-circle ms-1 p-0 d-flex align-items-center justify-content-center" 
                                                style="width:20px;height:20px;"
                                                title="Change Commission Rate">
                                            <i class="feather-edit-2 fs-10"></i>
                                        </button>
                                        @endcan
                                    </div>
                                </td>

                                {{-- Commission Earned ("Kisko Kitna Comission Gaya Hai") --}}
                                <td class="py-3">
                                    <div class="fs-15 fw-extrabold text-primary">
                                        ₹{{ number_format($item['commission_earned'], 2) }}
                                    </div>
                                    <div class="d-flex align-items-center gap-1 mt-1">
                                        @if($item['commission_payable'] > 0)
                                        <span class="badge bg-warning text-dark rounded-pill px-2 py-0 fs-10 fw-bold">
                                            ₹{{ number_format($item['commission_payable'], 2) }} Unpaid
                                        </span>
                                        @else
                                        <span class="badge bg-soft-success text-success rounded-pill px-2 py-0 fs-10">
                                            ✓ Fully Paid
                                        </span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Actions --}}
                                <td class="text-center pe-4 py-3">
                                    <div class="d-flex gap-2 justify-content-center">
                                        <button wire:click="openLedgerModal({{ $item['user']->id }})" 
                                                class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fs-12 fw-semibold d-flex align-items-center gap-1 shadow-sm">
                                            <i class="feather-file-text fs-12"></i>
                                            <span>Detailed Ledger</span>
                                        </button>
                                        @if($item['commission_payable'] > 0)
                                        <button wire:click="openPayoutModal({{ $item['user']->id }}, {{ $item['commission_payable'] }})" 
                                                class="btn btn-sm btn-primary text-white rounded-pill px-3 py-1 fs-12 fw-bold d-flex align-items-center gap-1 shadow-sm">
                                            <i class="feather-gift fs-12"></i>
                                            <span>Pay Commission</span>
                                        </button>
                                        @endif
                                        @if($item['cash_in_hand_due'] > 0)
                                        <button wire:click="openDirectSettleModal({{ $item['user']->id }}, {{ $item['cash_in_hand_due'] }})" 
                                                class="btn btn-sm btn-success text-white rounded-pill px-3 py-1 fs-12 fw-bold d-flex align-items-center gap-1 shadow-sm">
                                            <i class="feather-check-circle fs-12"></i>
                                            <span>Settle Cash</span>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="feather-users fs-1 d-block mb-2 opacity-25"></i>
                                    No registered phlebotomists found in this lab.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- ASSIGN PHLEBOTOMIST MODAL --}}
    @if($isAssignModalOpen)
    <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold"><i class="feather-user-check me-2 text-primary"></i>Assign Phlebotomist</h5>
                    <button wire:click="$set('isAssignModalOpen', false)" class="btn-close"></button>
                </div>
                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Select Phlebotomist <span class="text-danger">*</span></label>
                        <select wire:model="phlebotomist_id" class="form-select @error('phlebotomist_id') is-invalid @enderror">
                            <option value="">Choose...</option>
                            @foreach($phlebotomists as $ph)
                            <option value="{{ $ph->id }}">{{ $ph->name }} — {{ $ph->phone }}</option>
                            @endforeach
                        </select>
                        @error('phlebotomist_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Notes (optional)</label>
                        <textarea wire:model="assignNotes" class="form-control" rows="2" placeholder="Any special instructions..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button wire:click="$set('isAssignModalOpen', false)" class="btn btn-outline-secondary px-4">Cancel</button>
                    <button wire:click="assignPhlebotomist" wire:loading.attr="disabled" class="btn btn-primary px-5">
                        <span wire:loading wire:target="assignPhlebotomist" class="spinner-border spinner-border-sm me-2"></span>
                        Assign
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- UPDATE STATUS MODAL --}}
    @if($isStatusModalOpen)
    <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold"><i class="feather-refresh-cw me-2 text-info"></i>Update Visit Status</h5>
                    <button wire:click="$set('isStatusModalOpen', false)" class="btn-close"></button>
                </div>
                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">New Status <span class="text-danger">*</span></label>
                        <select wire:model="newStatus" class="form-select @error('newStatus') is-invalid @enderror">
                            @foreach(['Pending','Assigned','En Route','Arrived','Collected','Dispatched','Received','Cancelled'] as $s)
                            <option value="{{ $s }}">{{ $s }}</option>
                            @endforeach
                        </select>
                        @error('newStatus') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Notes</label>
                        <textarea wire:model="statusNotes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button wire:click="$set('isStatusModalOpen', false)" class="btn btn-outline-secondary px-4">Cancel</button>
                    <button wire:click="updateStatus" wire:loading.attr="disabled" class="btn btn-info text-white px-5">
                        <span wire:loading wire:target="updateStatus" class="spinner-border spinner-border-sm me-2"></span>
                        Update
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- LIVE TRACKING & GPS AUDIT MODAL --}}
    @if($isTrackingModalOpen && $trackingVisit)
    <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,.6); backdrop-filter: blur(2px); z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable" style="max-width: 1100px;">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header border-0 bg-light py-3 px-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-3 bg-soft-success text-success p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="feather-map-pin fs-18"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0 fs-16">Live Tracking & GPS Audit Trail</h5>
                            <div class="text-muted fs-12">
                                Visit #{{ $trackingVisit->invoice?->invoice_number ?? $trackingVisit->invoice_id }} • Patient: <strong>{{ $trackingVisit->invoice?->patient?->name ?? '—' }}</strong>
                            </div>
                        </div>
                    </div>
                    <button wire:click="closeTrackingModal" class="btn-close"></button>
                </div>
                
                <div class="modal-body px-4 py-3">
                    {{-- Summary Overview Row --}}
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 bg-light border border-light h-100">
                                <div class="fs-11 text-muted text-uppercase fw-bold mb-1">Collection Destination</div>
                                <div class="fs-13 text-dark fw-semibold mb-1">
                                    <i class="feather-map-pin text-danger me-1"></i>{{ $trackingVisit->collection_address }}
                                </div>
                                @if($trackingVisit->collection_landmark)
                                <div class="text-muted fs-12 mb-2"><i class="feather-flag me-1"></i>{{ $trackingVisit->collection_landmark }}</div>
                                @endif
                                @if($trackingVisit->google_maps_link)
                                <a href="{{ $trackingVisit->google_maps_link }}" target="_blank" class="btn btn-xs btn-outline-primary rounded-pill px-2 py-1 fs-11 mt-1">
                                    <i class="feather-external-link me-1"></i>View Destination on Google Maps
                                </a>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 bg-light border border-light h-100">
                                <div class="fs-11 text-muted text-uppercase fw-bold mb-1">Assigned Phlebotomist</div>
                                @if($trackingVisit->phlebotomist)
                                <div class="fs-14 fw-bold text-dark">{{ $trackingVisit->phlebotomist->name }}</div>
                                <div class="text-muted fs-12"><i class="feather-phone me-1"></i>{{ $trackingVisit->phlebotomist->phone }}</div>
                                <div class="fs-11 text-muted mt-1">
                                    Vehicle: <span class="fw-semibold text-dark">{{ $trackingVisit->phlebotomist->phlebotomistProfile?->vehicle_type ?? 'Bike' }} ({{ $trackingVisit->phlebotomist->phlebotomistProfile?->vehicle_number ?? 'N/A' }})</span>
                                </div>
                                @else
                                <div class="text-warning fs-13 fw-bold">⚠ Not Assigned Yet</div>
                                @endif
                                <div class="mt-2">
                                    <span class="badge bg-soft-primary text-primary rounded-pill px-3 py-1 fs-11 fw-bold">
                                        Current Status: {{ $trackingVisit->status }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Lifecycle Stepper --}}
                    @php
                        $steps = ['Assigned', 'En Route', 'Arrived', 'Collected'];
                        $curIdx = array_search($trackingVisit->status, $steps);
                        if ($curIdx === false) {
                            if (in_array($trackingVisit->status, ['Dispatched', 'Received'])) $curIdx = 4;
                            else $curIdx = -1;
                        }
                    @endphp
                    <div class="card border border-light shadow-none rounded-3 p-3 mb-3 bg-white">
                        <div class="fs-11 text-muted text-uppercase fw-bold mb-3">Visit Progression Lifecycle</div>
                        <div class="d-flex align-items-center justify-content-between position-relative px-2">
                            <div class="position-absolute start-0 end-0 bg-light" style="height: 4px; top: 18px; z-index: 1;"></div>
                            <div class="position-absolute start-0 bg-success" style="height: 4px; top: 18px; z-index: 1; width: {{ max(0, min(100, $curIdx * 33.3)) }}%;"></div>
                            
                            @foreach($steps as $idx => $st)
                            @php
                                $isPassed = ($curIdx >= $idx);
                                $isCurrent = ($trackingVisit->status === $st);
                            @endphp
                            <div class="d-flex flex-column align-items-center position-relative" style="z-index: 2;">
                                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold fs-12 {{ $isPassed ? 'bg-success text-white' : 'bg-light text-muted border' }}"
                                     style="width: 36px; height: 36px;">
                                    @if($isPassed)
                                    <i class="feather-check fs-14"></i>
                                    @else
                                    {{ $idx + 1 }}
                                    @endif
                                </div>
                                <div class="fs-11 mt-1 text-center {{ $isCurrent ? 'fw-bold text-success' : 'text-muted' }}">{{ $st }}</div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- GPS & Status Transition Audit Table --}}
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="fs-12 text-uppercase fw-bold text-dark">
                            <i class="feather-compass text-primary me-1"></i>Status Audit Log & Field GPS Coordinates
                        </div>
                        <span class="badge bg-light text-muted border rounded-pill fs-11">
                            {{ count($trackingVisit->statusLogs) }} Transitions Recorded
                        </span>
                    </div>

                    <div class="table-responsive border rounded-3" style="overflow-x: auto;">
                        <table class="table table-hover align-middle mb-0 fs-13" style="min-width: 780px;">
                            <thead class="bg-light text-muted">
                                <tr>
                                    <th class="ps-3 py-2" style="width: 22%;">Timestamp</th>
                                    <th class="py-2" style="width: 24%;">Status Transition</th>
                                    <th class="py-2" style="width: 18%;">Actor</th>
                                    <th class="pe-3 py-2" style="width: 36%;">Captured Field GPS & Google Maps</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($trackingVisit->statusLogs as $log)
                                <tr>
                                    <td class="ps-3 py-2">
                                        <div class="fw-semibold text-dark">{{ $log->created_at?->format('d M Y, h:i:s A') }}</div>
                                        <div class="text-muted fs-11">{{ $log->created_at?->diffForHumans() }}</div>
                                    </td>
                                    <td class="py-2">
                                        <div class="d-flex align-items-center gap-1 flex-wrap">
                                            @if($log->from_status)
                                            <span class="badge bg-light text-muted border px-2 py-1 fs-11">{{ $log->from_status }}</span>
                                            <i class="feather-arrow-right fs-11 text-muted"></i>
                                            @endif
                                            @php
                                                $stColors = [
                                                    'Pending'    => 'warning',
                                                    'Assigned'   => 'primary',
                                                    'En Route'   => 'info',
                                                    'Arrived'    => 'warning',
                                                    'Collected'  => 'success',
                                                    'Dispatched' => 'teal',
                                                    'Received'   => 'success',
                                                    'Cancelled'  => 'danger',
                                                ];
                                                $stc = $stColors[$log->to_status] ?? 'primary';
                                            @endphp
                                            <span class="badge bg-soft-{{ $stc }} text-{{ $stc }} px-2 py-1 fs-11 fw-bold">{{ $log->to_status }}</span>
                                        </div>
                                        @if($log->notes)
                                        <div class="text-muted fs-11 mt-1"><i class="feather-message-square me-1"></i>{{ $log->notes }}</div>
                                        @endif
                                    </td>
                                    <td class="py-2">
                                        <div class="fw-semibold text-dark">{{ $log->changedBy?->name ?? 'System' }}</div>
                                        <div class="text-muted fs-11">{{ $log->changedBy?->roles?->first()?->name ?? 'Staff' }}</div>
                                    </td>
                                    <td class="pe-3 py-2">
                                        @if($log->latitude && $log->longitude)
                                        <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap bg-soft-light p-1 px-2 rounded-2 border border-light">
                                            <div>
                                                <div class="text-success fw-bold font-monospace fs-12">
                                                    <i class="feather-check-circle fs-12 text-success me-1"></i>{{ number_format($log->latitude, 6) }}, {{ number_format($log->longitude, 6) }}
                                                </div>
                                                <span class="badge bg-soft-success text-success rounded-pill px-2 py-0 fs-10 fw-semibold">
                                                    ● Field GPS Verified
                                                </span>
                                            </div>
                                            <a href="https://maps.google.com/?q={{ $log->latitude }},{{ $log->longitude }}"
                                               target="_blank"
                                               class="btn btn-xs btn-outline-primary rounded-pill px-3 py-1 fs-11 fw-semibold d-inline-flex align-items-center gap-1 shadow-sm">
                                                <i class="feather-external-link fs-11"></i>
                                                <span>Open in Maps</span>
                                            </a>
                                        </div>
                                        @else
                                        <span class="badge bg-light text-muted border rounded-pill px-2 py-1 fs-11">
                                            No GPS (Manual Update)
                                        </span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <i class="feather-activity fs-2 d-block mb-1 opacity-25"></i>
                                        No status transition events logged yet for this visit.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Sample Collection Final Details (if collected) --}}
                    @if($trackingVisit->collected_at)
                    <div class="mt-3 p-3 rounded-3 bg-soft-success border border-success border-opacity-25">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <div class="fs-12 text-success fw-bold text-uppercase">
                                    <i class="feather-check-circle me-1"></i>Sample Successfully Collected
                                </div>
                                <div class="text-dark fs-12 mt-1">
                                    Time: <strong>{{ \Carbon\Carbon::parse($trackingVisit->collected_at)->format('d M Y, h:i A') }}</strong>
                                    @if($trackingVisit->collected_lat && $trackingVisit->collected_lng)
                                    • At GPS: <span class="font-monospace fw-bold">{{ number_format($trackingVisit->collected_lat, 6) }}, {{ number_format($trackingVisit->collected_lng, 6) }}</span>
                                    @endif
                                </div>
                            </div>
                            @if($trackingVisit->collected_lat && $trackingVisit->collected_lng)
                            <a href="https://maps.google.com/?q={{ $trackingVisit->collected_lat }},{{ $trackingVisit->collected_lng }}"
                               target="_blank"
                               class="btn btn-sm btn-success text-white rounded-pill px-3 py-1 fs-12 fw-semibold">
                                <i class="feather-map me-1"></i>View Collection Spot
                            </a>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>

                <div class="modal-footer border-0 bg-light py-2 px-4">
                    <button wire:click="closeTrackingModal" class="btn btn-secondary rounded-pill px-4 py-2 fs-13">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- PHLEBOTOMIST DETAILED LEDGER MODAL --}}
    @if($isLedgerModalOpen && $selectedPhleboData)
    <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,.6); backdrop-filter: blur(4px); z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable" style="max-width: 1100px;">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header border-0 bg-light py-3 px-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar avatar-md rounded-circle bg-primary text-white fw-bold d-flex align-items-center justify-content-center" style="width:42px;height:42px;">
                            {{ strtoupper(substr($selectedPhleboData['user']->name, 0, 2)) }}
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0">
                                {{ $selectedPhleboData['user']->name }} — Collection & Commission Ledger
                            </h5>
                            <span class="text-muted fs-12">
                                <i class="feather-phone me-1"></i>{{ $selectedPhleboData['user']->phone ?? 'N/A' }}
                                • Commission Rate: <strong class="text-primary">₹{{ number_format($selectedPhleboData['user']->phlebotomistProfile?->commission_per_visit ?? 50, 2) }} / visit</strong>
                            </span>
                        </div>
                    </div>
                    <button wire:click="closeLedgerModal" class="btn-close"></button>
                </div>

                <div class="modal-body p-4" style="max-height: 75vh; overflow-y: auto;">
                    {{-- Financial Summary Snapshot --}}
                    @php
                        $phCash = $selectedPhleboData['payments']->filter(function($p) {
                            $m = strtolower($p->paymentMode?->name ?? $p->remarks ?? '');
                            return str_contains($m, 'cash');
                        })->sum('amount');

                        $phDigital = $selectedPhleboData['payments']->filter(function($p) {
                            $m = strtolower($p->paymentMode?->name ?? $p->remarks ?? '');
                            return !str_contains($m, 'cash');
                        })->sum('amount');

                        $phSettled = $selectedPhleboData['handovers']->where('status', 'approved')->sum('amount');
                        $phDue = max(0, $phCash - $phSettled);
                        $rate = (float) ($selectedPhleboData['user']->phlebotomistProfile?->commission_per_visit ?? 50.00);
                        $phCommission = $selectedPhleboData['visits']->count() * $rate;
                        $phCommissionSettled = $selectedPhleboData['commission_settlements']->sum('amount');
                        $phCommissionPayable = max(0, $phCommission - $phCommissionSettled);
                    @endphp

                    <div class="row g-3 mb-4">
                        <div class="col-6 col-md-3">
                            <div class="p-3 rounded-3 bg-light text-center border">
                                <div class="text-muted fs-11 text-uppercase fw-bold">Completed Visits</div>
                                <div class="fs-4 fw-extrabold text-dark mt-1">{{ $selectedPhleboData['visits']->count() }}</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-3 rounded-3 bg-light text-center border">
                                <div class="text-muted fs-11 text-uppercase fw-bold">Total Cash Collected</div>
                                <div class="fs-4 fw-extrabold text-dark mt-1">₹{{ number_format($phCash, 2) }}</div>
                                @if($phDigital > 0)
                                <div class="text-muted fs-10">+ ₹{{ number_format($phDigital, 2) }} UPI</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-3 rounded-3 bg-light text-center border">
                                <div class="text-muted fs-11 text-uppercase fw-bold">Cash Due to Lab</div>
                                <div class="fs-4 fw-extrabold {{ $phDue > 0 ? 'text-danger' : 'text-success' }} mt-1">
                                    ₹{{ number_format($phDue, 2) }}
                                </div>
                                <div class="fs-10 mt-1 {{ $phDue > 0 ? 'text-danger fw-bold' : 'text-success fw-bold' }}">
                                    {{ $phDue > 0 ? 'Pending Handover' : '✓ All Settled' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-3 rounded-3 bg-light text-center border">
                                <div class="text-muted fs-11 text-uppercase fw-bold">Commission Balance</div>
                                <div class="fs-4 fw-extrabold {{ $phCommissionPayable > 0 ? 'text-warning' : 'text-success' }} mt-1">
                                    ₹{{ number_format($phCommissionPayable, 2) }}
                                </div>
                                <div class="fs-10 mt-1 text-muted">
                                    Earned: ₹{{ number_format($phCommission, 2) }} • Paid: ₹{{ number_format($phCommissionSettled, 2) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 1. Completed Home Visits & Collections --}}
                    <div class="card border rounded-3 mb-4 overflow-hidden">
                        <div class="card-header bg-white border-bottom py-2 px-3">
                            <h6 class="fw-bold text-dark mb-0 fs-13"><i class="feather-check-circle me-1 text-success"></i>Completed Home Visits & Invoices ({{ $selectedPhleboData['visits']->count() }})</h6>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0 fs-12">
                                <thead class="bg-light text-muted fs-11 fw-bold text-uppercase">
                                    <tr>
                                        <th class="ps-3 py-2">Invoice / Patient</th>
                                        <th class="py-2">Scheduled / Collected</th>
                                        <th class="py-2">Tests Collected</th>
                                        <th class="py-2">Payment Status</th>
                                        <th class="text-end pe-3 py-2">Commission Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($selectedPhleboData['visits'] as $v)
                                    <tr>
                                        <td class="ps-3 py-2">
                                            <div class="fw-bold text-dark">#{{ $v->invoice?->invoice_number ?? $v->invoice_id }}</div>
                                            <div class="text-muted fs-11">{{ $v->invoice?->patient?->name ?? 'Patient' }}</div>
                                        </td>
                                        <td class="py-2">
                                            <div class="text-dark">{{ $v->scheduled_date ? \Carbon\Carbon::parse($v->scheduled_date)->format('d M Y') : 'N/A' }}</div>
                                            <div class="text-muted fs-10">{{ $v->collected_at ? \Carbon\Carbon::parse($v->collected_at)->format('h:i A') : 'Completed' }}</div>
                                        </td>
                                        <td class="py-2">
                                            <div class="d-flex flex-wrap gap-1">
                                                @forelse($v->invoice?->items ?? [] as $it)
                                                <span class="badge bg-light text-dark border px-2 py-0 fs-10">{{ $it->labTest?->name ?? 'Test' }}</span>
                                                @empty
                                                <span class="text-muted fs-11">—</span>
                                                @endforelse
                                            </div>
                                        </td>
                                        <td class="py-2">
                                            <span class="badge {{ $v->invoice?->payment_status === 'Paid' ? 'bg-soft-success text-success' : 'bg-soft-warning text-warning' }} rounded-pill px-2 py-0 fs-10">
                                                {{ $v->invoice?->payment_status ?? 'Pending' }} (₹{{ number_format($v->invoice?->total_amount ?? 0, 2) }})
                                            </span>
                                        </td>
                                        <td class="text-end pe-3 py-2">
                                            @if($v->is_commission_settled)
                                            <span class="badge bg-soft-success text-success fw-bold fs-11">
                                                <i class="feather-check me-1"></i>Paid (+₹{{ number_format($rate, 2) }})
                                            </span>
                                            @if($v->commission_settled_at)
                                            <div class="text-muted fs-10">{{ \Carbon\Carbon::parse($v->commission_settled_at)->format('d M Y') }}</div>
                                            @endif
                                            @else
                                            <span class="badge bg-warning text-dark fw-bold fs-11">
                                                ⏳ ₹{{ number_format($rate, 2) }} Unpaid
                                            </span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">No completed collections on record yet.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- 2. Cash Handover & Deposit History --}}
                    <div class="card border rounded-3 mb-4 overflow-hidden">
                        <div class="card-header bg-white border-bottom py-2 px-3">
                            <h6 class="fw-bold text-dark mb-0 fs-13"><i class="feather-dollar-sign me-1 text-warning"></i>Cash Handover Deposit Logs ({{ $selectedPhleboData['handovers']->count() }})</h6>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0 fs-12">
                                <thead class="bg-light text-muted fs-11 fw-bold text-uppercase">
                                    <tr>
                                        <th class="ps-3 py-2">Date & Time</th>
                                        <th class="py-2">Amount</th>
                                        <th class="py-2">Status</th>
                                        <th class="py-2">Verified By</th>
                                        <th class="pe-3 py-2">Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($selectedPhleboData['handovers'] as $h)
                                    <tr>
                                        <td class="ps-3 py-2 text-dark">{{ $h->created_at->format('d M Y, h:i A') }}</td>
                                        <td class="py-2 fw-bold text-dark">₹{{ number_format($h->amount, 2) }}</td>
                                        <td class="py-2">
                                            <span class="badge {{ $h->status === 'approved' ? 'bg-success' : ($h->status === 'pending' ? 'bg-warning text-dark' : 'bg-danger') }} rounded-pill px-2 py-0 fs-10">
                                                {{ ucfirst($h->status) }}
                                            </span>
                                        </td>
                                        <td class="py-2 text-muted">{{ $h->approvedBy?->name ?? '—' }}</td>
                                        <td class="pe-3 py-2 text-muted fs-11">{{ $h->notes ?? '—' }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">No cash handovers recorded yet.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- 3. Commission Payout Receipts --}}
                    <div class="card border rounded-3 overflow-hidden">
                        <div class="card-header bg-white border-bottom py-2 px-3 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold text-dark mb-0 fs-13">
                                <i class="feather-gift me-1 text-primary"></i>Commission Payout Receipts & Vouchers ({{ $selectedPhleboData['commission_settlements']->count() }})
                            </h6>
                            @if($phCommissionPayable > 0)
                            <button wire:click="openPayoutModal({{ $selectedPhleboData['user']->id }}, {{ $phCommissionPayable }})"
                                    class="btn btn-sm btn-primary rounded-pill px-3 py-1 fw-bold fs-11">
                                <i class="feather-gift me-1"></i>Pay Pending Commission (₹{{ number_format($phCommissionPayable, 2) }})
                            </button>
                            @endif
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0 fs-12">
                                <thead class="bg-light text-muted fs-11 fw-bold text-uppercase">
                                    <tr>
                                        <th class="ps-3 py-2">Payout Date</th>
                                        <th class="py-2">Amount Paid</th>
                                        <th class="py-2">Payment Mode</th>
                                        <th class="py-2">Ref / UTR No</th>
                                        <th class="py-2">Settled By</th>
                                        <th class="pe-3 py-2">Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($selectedPhleboData['commission_settlements'] as $settle)
                                    <tr>
                                        <td class="ps-3 py-2 text-dark">{{ $settle->payment_date ? $settle->payment_date->format('d M Y, h:i A') : $settle->created_at->format('d M Y, h:i A') }}</td>
                                        <td class="py-2 fw-bold text-success">₹{{ number_format($settle->amount, 2) }}</td>
                                        <td class="py-2">
                                            <span class="badge bg-light text-dark border rounded-pill px-2 py-0 fs-10">
                                                {{ $settle->payment_mode ?? 'Cash' }}
                                            </span>
                                        </td>
                                        <td class="py-2 font-monospace fs-11 text-muted">{{ $settle->reference_no ?? '—' }}</td>
                                        <td class="py-2 text-muted">{{ $settle->settledBy?->name ?? 'Admin' }}</td>
                                        <td class="pe-3 py-2 text-muted fs-11">{{ $settle->notes ?? 'Commission payout' }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">No commission payout receipts recorded yet.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 bg-light py-2 px-4 d-flex justify-content-between">
                    <div>
                        @if($phDue > 0)
                        <button wire:click="openDirectSettleModal({{ $selectedPhleboData['user']->id }}, {{ $phDue }})" class="btn btn-sm btn-success text-white rounded-pill px-3 py-1 fs-12 fw-bold me-2">
                            <i class="feather-check-circle me-1"></i>Settle Cash Handover (₹{{ number_format($phDue, 2) }})
                        </button>
                        @endif
                        @if($phCommissionPayable > 0)
                        <button wire:click="openPayoutModal({{ $selectedPhleboData['user']->id }}, {{ $phCommissionPayable }})" class="btn btn-sm btn-primary rounded-pill px-3 py-1 fs-12 fw-bold">
                            <i class="feather-gift me-1"></i>Pay Commission (₹{{ number_format($phCommissionPayable, 2) }})
                        </button>
                        @endif
                    </div>
                    <button wire:click="closeLedgerModal" class="btn btn-secondary rounded-pill px-4 py-2 fs-13">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- EDIT COMMISSION RATE MODAL --}}
    @if($isCommissionModalOpen)
    <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,.5); z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0">
                    <h6 class="modal-title fw-bold"><i class="feather-edit-2 me-1 text-primary"></i>Edit Commission Rate</h6>
                    <button wire:click="$set('isCommissionModalOpen', false)" class="btn-close"></button>
                </div>
                <div class="modal-body py-2">
                    <label class="form-label fs-12 fw-bold text-muted">Commission Per Completed Visit (₹)</label>
                    <div class="input-group">
                        <span class="input-group-text">₹</span>
                        <input type="number" step="0.01" min="0" wire:model="editCommissionRate" class="form-control" placeholder="50.00">
                    </div>
                    @error('editCommissionRate') <span class="text-danger fs-11">{{ $message }}</span> @enderror
                </div>
                <div class="modal-footer border-0">
                    <button wire:click="$set('isCommissionModalOpen', false)" class="btn btn-sm btn-light rounded-pill px-3">Cancel</button>
                    <button wire:click="saveCommissionRate" class="btn btn-sm btn-primary rounded-pill px-4 fw-bold">Save Rate</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- DIRECT CASH SETTLEMENT MODAL --}}
    @if($isDirectSettleModalOpen)
    <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,.5); z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold"><i class="feather-check-circle me-2 text-success"></i>Direct Cash Settlement</h5>
                    <button wire:click="$set('isDirectSettleModalOpen', false)" class="btn-close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info border-0 rounded-3 fs-12 mb-3">
                        Use this to settle cash directly if the phlebotomist delivers physical money at the reception counter.
                    </div>
                    <div class="mb-3">
                        <label class="form-label fs-12 fw-bold text-muted">Settlement Amount (₹)</label>
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input type="number" step="0.01" min="0.01" wire:model="directSettleAmount" class="form-control fw-bold fs-16">
                        </div>
                        @error('directSettleAmount') <span class="text-danger fs-11">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fs-12 fw-bold text-muted">Settlement Remarks / Receipt Notes</label>
                        <input type="text" wire:model="directSettleNotes" class="form-control" placeholder="Physical cash verified & deposited at desk">
                        @error('directSettleNotes') <span class="text-danger fs-11">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button wire:click="$set('isDirectSettleModalOpen', false)" class="btn btn-light rounded-pill px-3">Cancel</button>
                    <button wire:click="recordDirectSettlement" class="btn btn-success text-white rounded-pill px-4 fw-bold">
                        <i class="feather-check-circle me-1"></i>Confirm & Settle Cash
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- COMMISSION PAYOUT MODAL --}}
    @if($isPayoutModalOpen)
    <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,.5); z-index: 1070;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header border-0 bg-light py-3 px-4">
                    <h5 class="modal-title fw-bold text-dark mb-0">
                        <i class="feather-gift me-2 text-primary"></i>Pay Phlebotomist Commission
                    </h5>
                    <button wire:click="$set('isPayoutModalOpen', false)" class="btn-close"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <div class="alert alert-primary border-0 rounded-3 fs-12 mb-3">
                        <i class="feather-info me-1"></i>
                        Record a commission payment to the phlebotomist. Their pending completed visits will automatically be marked as settled.
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fs-12 fw-bold text-muted">Payout Amount (₹) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input type="number" step="0.01" min="0.01" wire:model="payoutAmount" class="form-control fw-bold fs-16 text-primary">
                        </div>
                        @error('payoutAmount') <span class="text-danger fs-11">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fs-12 fw-bold text-muted">Payment Mode <span class="text-danger">*</span></label>
                        <select wire:model="payoutMode" class="form-select fs-13">
                            <option value="Cash">Cash</option>
                            <option value="UPI">UPI / GPay / PhonePe</option>
                            <option value="Bank Transfer">Bank Transfer / IMPS / NEFT</option>
                            <option value="Cheque">Cheque</option>
                        </select>
                        @error('payoutMode') <span class="text-danger fs-11">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fs-12 fw-bold text-muted">Transaction ID / UTR / Reference No (optional)</label>
                        <input type="text" wire:model="payoutReference" class="form-control fs-13" placeholder="e.g. UPI-202609100012 or Cheque #123456">
                        @error('payoutReference') <span class="text-danger fs-11">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fs-12 fw-bold text-muted">Notes / Remarks</label>
                        <input type="text" wire:model="payoutNotes" class="form-control fs-13" placeholder="Commission payout for completed visits">
                        @error('payoutNotes') <span class="text-danger fs-11">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light py-2 px-4">
                    <button wire:click="$set('isPayoutModalOpen', false)" class="btn btn-light rounded-pill px-3">Cancel</button>
                    <button wire:click="recordCommissionPayout" wire:loading.attr="disabled" class="btn btn-primary rounded-pill px-4 fw-bold">
                        <span wire:loading wire:target="recordCommissionPayout" class="spinner-border spinner-border-sm me-2"></span>
                        <i class="feather-check-circle me-1"></i>Confirm & Pay Commission
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
