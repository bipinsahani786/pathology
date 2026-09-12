<div>
    {{-- Flash messages --}}
    @if(session()->has('message'))
    <div class="alert alert-success border-0 shadow-sm rounded-3 mt-2 d-flex align-items-center alert-dismissible fade show">
        <i class="feather-check-circle fs-5 me-2"></i> {{ session('message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- ======================================================== --}}
    {{-- TAB 1: VISITS (Active Runs)                              --}}
    {{-- ======================================================== --}}
    @if($activeTab === 'visits')
        {{-- Date navigation --}}
        <div class="card border-0 shadow-sm rounded-4 p-2 mb-3 bg-white">
            <div class="d-flex align-items-center justify-content-between gap-2">
                <button wire:click="$set('filterDate', \Carbon\Carbon::parse($filterDate)->subDay()->format('Y-m-d'))"
                    class="btn btn-sm btn-light border-0 rounded-circle p-0 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                    <i class="feather-chevron-left fs-14"></i>
                </button>
                <div class="d-flex align-items-center gap-2 flex-grow-1 justify-content-center">
                    <i class="feather-calendar text-primary fs-14"></i>
                    <input type="date" wire:model.live="filterDate" class="form-control form-control-sm text-center fw-bold border-0 bg-light rounded-3 py-1" style="max-width:150px; font-size: 13px;">
                </div>
                <button wire:click="$set('filterDate', \Carbon\Carbon::parse($filterDate)->addDay()->format('Y-m-d'))"
                    class="btn btn-sm btn-light border-0 rounded-circle p-0 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                    <i class="feather-chevron-right fs-14"></i>
                </button>
                <button wire:click="$set('filterDate', now()->format('Y-m-d'))"
                    class="btn btn-sm {{ $filterDate === now()->format('Y-m-d') ? 'btn-primary' : 'btn-outline-primary' }} rounded-pill px-3 py-1 fs-12 fw-semibold">
                    Today
                </button>
            </div>
        </div>

        {{-- Summary chips --}}
        <div class="row g-2 mb-3">
            <div class="col-6">
                <div class="card border-0 shadow-sm rounded-4 p-2 px-3 bg-white d-flex flex-row align-items-center gap-3">
                    <div class="rounded-3 bg-soft-primary text-primary p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="feather-list fs-16"></i>
                    </div>
                    <div>
                        <div class="fs-11 text-muted text-uppercase fw-semibold">Assigned</div>
                        <div class="fs-16 fw-bold text-dark lh-1">{{ $todayCount }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card border-0 shadow-sm rounded-4 p-2 px-3 bg-white d-flex flex-row align-items-center gap-3">
                    <div class="rounded-3 bg-soft-success text-success p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="feather-check-circle fs-16"></i>
                    </div>
                    <div>
                        <div class="fs-11 text-muted text-uppercase fw-semibold">Collected</div>
                        <div class="fs-16 fw-bold text-success lh-1">{{ $doneToday }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Visit Cards --}}
        @forelse($visits as $visit)
        @php
            $statusColors = ['Assigned'=>'primary','En Route'=>'info','Arrived'=>'warning','Collected'=>'success'];
            $c = $statusColors[$visit->status] ?? 'secondary';
            $patient = $visit->invoice->patient;
            $items = $visit->invoice->items ?? collect();
            $dueAmount = $visit->invoice->due_amount ?? 0;
        @endphp
        <div class="visit-card card border-0 shadow-sm mb-3" wire:key="vc-{{ $visit->id }}">
            {{-- Card Header --}}
            <div class="card-header bg-soft-{{ $c }} border-0 rounded-top-4 py-2 px-3 d-flex justify-content-between align-items-center">
                <div>
                    <span class="fw-bold text-{{ $c }} fs-13">
                        <i class="feather-clock me-1"></i>
                        @if($visit->scheduled_slot_start)
                            {{ date('h:i A', strtotime($visit->scheduled_slot_start)) }}
                            @if($visit->scheduled_slot_end) – {{ date('h:i A', strtotime($visit->scheduled_slot_end)) }} @endif
                        @else
                            {{ $visit->scheduled_date?->format('d M Y') }}
                        @endif
                    </span>
                </div>
                <span class="badge bg-{{ $c }} text-white rounded-pill fs-11">{{ $visit->status }}</span>
            </div>

            {{-- Card Body --}}
            <div class="card-body px-3 py-3">
                {{-- Patient --}}
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <div class="fw-bold text-dark fs-15">{{ $patient->name ?? '—' }}</div>
                        <div class="text-muted fs-12">Invoice #{{ $visit->invoice->invoice_number ?? $visit->invoice_id }}</div>
                    </div>
                    <div class="d-flex gap-2">
                        @if($patient?->phone)
                        <a href="tel:{{ $patient->phone }}" class="btn btn-sm btn-soft-success rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;" title="Call">
                            <i class="feather-phone fs-13"></i>
                        </a>
                        <a href="https://wa.me/91{{ preg_replace('/[^0-9]/', '', $patient->phone) }}" target="_blank" class="btn btn-sm btn-soft-success rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;" title="WhatsApp">
                            <i class="feather-message-circle fs-13"></i>
                        </a>
                        @endif
                    </div>
                </div>

                {{-- Address --}}
                <div class="bg-light rounded-3 p-2 mb-3">
                    <div class="fs-13 text-dark mb-1">
                        <i class="feather-map-pin text-danger me-1"></i>
                        {{ $visit->collection_address }}
                    </div>
                    @if($visit->collection_landmark)
                    <div class="fs-12 text-muted"><i class="feather-flag me-1"></i>{{ $visit->collection_landmark }}</div>
                    @endif
                    <a href="{{ $visit->navigation_link }}" target="_blank"
                       class="btn btn-sm btn-success mt-2 w-100 rounded-3 fw-semibold">
                        <i class="feather-navigation me-2"></i>Navigate to Patient
                    </a>
                </div>

                {{-- Tests --}}
                <div class="mb-3">
                    <div class="fs-11 text-muted fw-semibold text-uppercase mb-1">Tests</div>
                    <div class="d-flex flex-wrap gap-1">
                        @foreach($items as $item)
                        <span class="badge bg-soft-primary text-primary rounded-pill fs-11 px-2 py-1">{{ $item->test_name ?? $item->item_name }}</span>
                        @endforeach
                        @if($items->isEmpty())
                        <span class="text-muted fs-12">No tests</span>
                        @endif
                    </div>
                </div>

                {{-- Due Amount --}}
                @if($dueAmount > 0)
                <div class="d-flex justify-content-between align-items-center bg-soft-warning rounded-3 px-3 py-2 mb-3">
                    <span class="text-warning fw-semibold fs-13">
                        <i class="feather-alert-circle me-1"></i> Due Amount
                    </span>
                    <span class="fw-bold text-danger fs-15">₹{{ number_format($dueAmount, 2) }}</span>
                </div>
                @endif

                {{-- Action Buttons Row --}}
                <div class="d-flex gap-2 mb-3 flex-wrap">
                    {{-- Print Barcode --}}
                    <a href="{{ route('phlebotomist.invoice.barcode.stickers', $visit->invoice_id) }}" target="_blank"
                       class="btn btn-sm btn-outline-secondary rounded-3 flex-fill status-btn d-flex align-items-center justify-content-center gap-1">
                        <i class="feather-tag"></i><span>Print Barcode</span>
                    </a>
                    {{-- Add Test --}}
                    @can('add tests to home_collection')
                    @if(!in_array($visit->status, ['Collected','Dispatched','Received','Cancelled']))
                    <button wire:click="openAddTestModal({{ $visit->id }})"
                        class="btn btn-sm btn-outline-primary rounded-3 flex-fill status-btn d-flex align-items-center justify-content-center gap-1">
                        <i class="feather-plus"></i><span>Add Test</span>
                    </button>
                    @endif
                    @endcan
                    {{-- Collect Payment --}}
                    @if($dueAmount > 0 && $visit->status === 'Collected')
                    <button wire:click="openPaymentModal({{ $visit->id }})"
                        class="btn btn-sm btn-outline-warning rounded-3 flex-fill status-btn d-flex align-items-center justify-content-center gap-1">
                        <i class="feather-credit-card"></i><span>Collect Payment</span>
                    </button>
                    @endif
                </div>

                {{-- Status Update Buttons --}}
                <div class="d-grid gap-2">
                    @if($visit->status === 'Assigned')
                    <button onclick="captureAndUpdateStatus(@this, {{ $visit->id }}, 'En Route')"
                        class="btn btn-info text-white status-btn fw-bold py-2">
                        <i class="feather-navigation me-2"></i>I'm On My Way (En Route)
                    </button>
                    @elseif($visit->status === 'En Route')
                    <button onclick="captureAndUpdateStatus(@this, {{ $visit->id }}, 'Arrived')"
                        class="btn btn-warning text-white status-btn fw-bold py-2">
                        <i class="feather-home me-2"></i>I've Arrived
                    </button>
                    @elseif($visit->status === 'Arrived')
                    <button onclick="captureAndUpdateStatus(@this, {{ $visit->id }}, 'Collected')"
                        class="btn btn-success status-btn fw-bold py-2">
                        <i class="feather-check-circle me-2"></i>Sample Collected ✓
                    </button>
                    @elseif($visit->status === 'Collected')
                    <button onclick="captureAndUpdateStatus(@this, {{ $visit->id }}, 'Dispatched')"
                        class="btn btn-outline-success status-btn fw-bold py-2">
                        <i class="feather-truck me-2"></i>Dispatched to Lab
                    </button>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="card border-0 shadow-sm rounded-4 text-center py-5 px-4 bg-white mt-2">
            <div class="rounded-circle bg-light d-inline-flex p-3 mx-auto mb-3" style="width: 72px; height: 72px; align-items: center; justify-content: center;">
                <i class="feather-calendar text-muted fs-1" style="opacity: 0.4;"></i>
            </div>
            <h6 class="fw-bold text-dark mb-1">No Visits Scheduled</h6>
            <p class="text-muted fs-13 mb-3">There are no sample collection visits assigned to you for {{ \Carbon\Carbon::parse($filterDate)->format('d M, Y') }}.</p>
            <button wire:click="$set('filterDate', now()->format('Y-m-d'))" class="btn btn-sm btn-outline-primary rounded-pill px-4 mx-auto fw-semibold">
                <i class="feather-rotate-ccw me-1"></i> Switch to Today
            </button>
        </div>
        @endforelse

    {{-- ======================================================== --}}
    {{-- TAB 2: HISTORY (Past Sample Collections)                  --}}
    {{-- ======================================================== --}}
    @elseif($activeTab === 'history')
        <div class="card border-0 shadow-sm rounded-4 p-3 mb-3 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <h5 class="fw-bold text-dark mb-0 fs-16">Collection History</h5>
                    <div class="text-muted fs-12">All your completed & past sample runs</div>
                </div>
                <span class="badge bg-soft-primary text-primary rounded-pill px-3 py-1 fs-12 fw-bold">
                    {{ count($historyVisits) }} Visits
                </span>
            </div>

            {{-- Search Bar --}}
            <div class="position-relative mb-2">
                <input type="text"
                    wire:model.live.debounce.300ms="historySearch"
                    class="form-control rounded-pill ps-4 py-2 bg-light border-0 fs-13"
                    placeholder="Search patient, phone, invoice, barcode...">
                @if($historySearch)
                <button wire:click="$set('historySearch', '')" class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-muted p-0 me-3" style="text-decoration:none;">
                    <i class="feather-x fs-14"></i>
                </button>
                @endif
            </div>

            {{-- Quick Filter Pills --}}
            <div class="d-flex gap-1 overflow-auto pb-1">
                @foreach(['all' => 'All Past', 'today' => 'Today', 'week' => 'This Week', 'month' => 'This Month'] as $key => $label)
                <button wire:click="$set('historyFilter', '{{ $key }}')"
                    class="btn btn-sm rounded-pill px-3 py-1 fs-11 fw-semibold flex-shrink-0 {{ $historyFilter === $key ? 'btn-primary' : 'btn-light text-muted' }}">
                    {{ $label }}
                </button>
                @endforeach
            </div>
        </div>

        {{-- History List --}}
        @forelse($historyVisits as $hVisit)
        @php
            $statusColors = [
                'Collected'  => 'success',
                'Dispatched' => 'teal',
                'Received'   => 'primary',
                'Cancelled'  => 'danger',
                'Assigned'   => 'secondary',
                'En Route'   => 'info',
                'Arrived'    => 'warning',
            ];
            $hc = $statusColors[$hVisit->status] ?? 'secondary';
            $hPatient = $hVisit->invoice->patient;
            $hItems = $hVisit->invoice->items ?? collect();
        @endphp
        <div class="card border-0 shadow-sm rounded-4 mb-3 bg-white overflow-hidden" wire:key="hist-{{ $hVisit->id }}">
            <div class="p-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <div class="fw-bold text-dark fs-14">{{ $hPatient->name ?? '—' }}</div>
                        <div class="text-muted fs-11">
                            Invoice #{{ $hVisit->invoice->invoice_number ?? $hVisit->invoice_id }}
                            • {{ $hVisit->scheduled_date?->format('d M Y') }}
                        </div>
                    </div>
                    <span class="badge bg-soft-{{ $hc }} text-{{ $hc }} rounded-pill px-2 py-1 fs-11 fw-bold">
                        {{ $hVisit->status }}
                    </span>
                </div>

                {{-- Address snippet --}}
                <div class="fs-12 text-muted mb-2">
                    <i class="feather-map-pin text-danger me-1"></i>
                    {{ \Illuminate\Support\Str::limit($hVisit->collection_address, 70) }}
                </div>

                {{-- Tests list --}}
                <div class="mb-2">
                    <div class="d-flex flex-wrap gap-1">
                        @foreach($hItems as $item)
                        <span class="badge bg-light text-dark border rounded-pill px-2 py-1 fs-10">
                            {{ $item->test_name ?? $item->item_name }}
                        </span>
                        @endforeach
                    </div>
                </div>

                {{-- Footer info --}}
                <div class="d-flex justify-content-between align-items-center pt-2 border-top border-light mt-2">
                    <div class="d-flex align-items-center gap-2">
                        @if($hVisit->collected_at)
                        <span class="fs-11 text-success fw-semibold">
                            <i class="feather-check me-1"></i>Collected {{ \Carbon\Carbon::parse($hVisit->collected_at)->format('h:i A') }}
                        </span>
                        @endif

                        @if($hVisit->collected_lat && $hVisit->collected_lng)
                        <a href="https://maps.google.com/?q={{ $hVisit->collected_lat }},{{ $hVisit->collected_lng }}"
                           target="_blank"
                           class="badge bg-soft-info text-info rounded-pill px-2 py-1 fs-10 text-decoration-none">
                            <i class="feather-map-pin me-1"></i>GPS Map
                        </a>
                        @endif
                    </div>

                    <a href="{{ route('phlebotomist.invoice.barcode.stickers', $hVisit->invoice_id) }}" target="_blank"
                       class="btn btn-xs btn-outline-secondary rounded-pill px-2 py-1 fs-11 d-flex align-items-center gap-1">
                        <i class="feather-tag fs-10"></i> Barcode
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="card border-0 shadow-sm rounded-4 text-center py-5 px-4 bg-white mt-2">
            <i class="feather-archive text-muted fs-1 mb-2 opacity-25"></i>
            <h6 class="fw-bold text-dark mb-1">No Collection Records Found</h6>
            <p class="text-muted fs-13 mb-3">No matching sample collections in this date range or search filter.</p>
            @if($historySearch || $historyFilter !== 'all')
            <button wire:click="$set('historySearch', ''); $set('historyFilter', 'all');" class="btn btn-sm btn-outline-primary rounded-pill px-4 mx-auto fw-semibold">
                Reset Filters
            </button>
            @endif
        </div>
        @endforelse

    {{-- ======================================================== --}}
    {{-- TAB 3: EARNINGS & CASH RECONCILIATION                     --}}
    {{-- ======================================================== --}}
    @elseif($activeTab === 'earnings')
        {{-- Rate Banner --}}
        <div class="card border-0 shadow-sm rounded-4 p-3 mb-3 text-white" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="fs-11 text-white-50 text-uppercase fw-bold">Commission Rate</span>
                    <h3 class="fw-extrabold text-white mb-0">₹{{ number_format($commissionPerVisit, 2) }} <span class="fs-12 fw-normal opacity-75">/ visit</span></h3>
                </div>
                <div class="rounded-circle bg-white bg-opacity-20 p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="feather-award fs-20 text-white"></i>
                </div>
            </div>
        </div>

        {{-- Commission KPI Cards --}}
        <div class="row g-2 mb-3">
            <div class="col-4">
                <div class="card border-0 shadow-sm rounded-4 p-2 px-2 text-center bg-white">
                    <div class="fs-10 text-muted text-uppercase fw-semibold">Today</div>
                    <div class="fs-15 fw-bold text-success mt-1">₹{{ number_format($todayCommission, 0) }}</div>
                    <div class="fs-10 text-muted">{{ $todayCollectedCount }} visits</div>
                </div>
            </div>
            <div class="col-4">
                <div class="card border-0 shadow-sm rounded-4 p-2 px-2 text-center bg-white">
                    <div class="fs-10 text-muted text-uppercase fw-semibold">This Month</div>
                    <div class="fs-15 fw-bold text-primary mt-1">₹{{ number_format($monthCommission, 0) }}</div>
                    <div class="fs-10 text-muted">{{ $monthCollectedCount }} visits</div>
                </div>
            </div>
            <div class="col-4">
                <div class="card border-0 shadow-sm rounded-4 p-2 px-2 text-center bg-white">
                    <div class="fs-10 text-muted text-uppercase fw-semibold">All Time</div>
                    <div class="fs-15 fw-bold text-dark mt-1">₹{{ number_format($totalCommission, 0) }}</div>
                    <div class="fs-10 text-muted">{{ $totalCollectedCount }} visits</div>
                </div>
            </div>
        </div>

        {{-- Commission Payout Status Snapshot --}}
        <div class="card border-0 shadow-sm rounded-4 p-3 mb-3 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 bg-soft-primary text-primary p-2 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                        <i class="feather-gift fs-16"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0 fs-13">Commission Payout Status</h6>
                        <span class="text-muted fs-10">Lab settlement to your account</span>
                    </div>
                </div>
                @if($totalCommissionPending > 0)
                <span class="badge bg-soft-warning text-warning rounded-pill px-3 py-1 fs-11 fw-bold">
                    ₹{{ number_format($totalCommissionPending, 0) }} Unpaid
                </span>
                @else
                <span class="badge bg-soft-success text-success rounded-pill px-3 py-1 fs-11 fw-bold">
                    ✓ All Paid
                </span>
                @endif
            </div>

            <div class="d-flex justify-content-between fs-12 pt-2 border-top border-light">
                <span class="text-muted">Total Commission Earned:</span>
                <strong class="text-dark">₹{{ number_format($totalCommission, 2) }}</strong>
            </div>
            <div class="d-flex justify-content-between fs-12 mt-1">
                <span class="text-muted">Paid to You (Received):</span>
                <strong class="text-success">₹{{ number_format($totalCommissionPaid, 2) }}</strong>
            </div>
            <div class="d-flex justify-content-between fs-12 mt-1">
                <span class="text-muted">Pending Payout from Lab:</span>
                <strong class="{{ $totalCommissionPending > 0 ? 'text-primary' : 'text-muted' }}">₹{{ number_format($totalCommissionPending, 2) }}</strong>
            </div>
        </div>

        {{-- Cash Handover Reconciliation Card --}}
        <div class="card border-0 shadow-sm rounded-4 p-3 mb-3 bg-white">
            <div class="d-flex align-items-center gap-2 mb-3">
                <div class="rounded-3 bg-soft-warning text-warning p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                    <i class="feather-dollar-sign fs-18"></i>
                </div>
                <div>
                    <h6 class="fw-bold text-dark mb-0 fs-14">Daily Cash Handover Tracker</h6>
                    <div class="text-muted fs-11">Physical cash collected to deposit at reception</div>
                </div>
            </div>

            {{-- Handover Highlight Box --}}
            <div class="rounded-3 p-3 mb-3 border {{ $todayPendingCash > 0 ? 'border-warning' : ($todayPendingHandovers > 0 ? 'border-info' : 'border-success') }}" style="background: {{ $todayPendingCash > 0 ? '#fffdf5' : ($todayPendingHandovers > 0 ? '#f0f9ff' : '#f0fdf4') }};">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fs-11 text-uppercase fw-bold {{ $todayPendingCash > 0 ? 'text-warning-emphasis' : ($todayPendingHandovers > 0 ? 'text-info' : 'text-success') }}">
                            {{ $todayPendingCash > 0 ? 'Cash in Hand (To Deposit)' : ($todayPendingHandovers > 0 ? 'Handover Awaiting Approval' : 'Cash in Hand (Settled)') }}
                        </div>
                        <div class="fs-22 fw-extrabold {{ $todayPendingCash > 0 ? 'text-danger' : ($todayPendingHandovers > 0 ? 'text-info' : 'text-success') }} mt-1">
                            ₹{{ number_format($todayPendingCash > 0 ? $todayPendingCash : ($todayPendingHandovers > 0 ? $todayPendingHandovers : 0), 2) }}
                        </div>
                    </div>
                    @if($todayPendingCash > 0)
                    <span class="badge bg-warning text-dark rounded-pill px-3 py-1 fs-11 fw-bold">
                        Pending Deposit
                    </span>
                    @elseif($todayPendingHandovers > 0)
                    <span class="badge bg-info text-white rounded-pill px-3 py-1 fs-11 fw-bold d-flex align-items-center gap-1">
                        <span class="spinner-grow spinner-grow-sm text-white" role="status" style="width:6px;height:6px;"></span>
                        Awaiting Lab Approval
                    </span>
                    @else
                    <span class="badge bg-success text-white rounded-pill px-3 py-1 fs-11 fw-bold">
                        ✓ All Cash Settled
                    </span>
                    @endif
                </div>

                {{-- Approval Pending Banner --}}
                @if($todayPendingHandovers > 0)
                <div class="p-2 rounded-3 bg-white border border-info border-opacity-25 mt-2 d-flex align-items-center gap-2">
                    <i class="feather-clock text-info fs-15"></i>
                    <div class="fs-11 text-dark">
                        Handover of <strong>₹{{ number_format($todayPendingHandovers, 2) }}</strong> submitted. Awaiting Lab Admin / Cashier verification.
                    </div>
                </div>
                @endif

                @if($todayPendingCash > 0)
                <button wire:click="openHandoverModal" class="btn btn-sm btn-primary text-white rounded-pill w-100 fw-bold py-2 mt-3 shadow-sm d-flex align-items-center justify-content-center gap-1">
                    <i class="feather-send fs-13"></i>
                    <span>Submit Cash Handover (₹{{ number_format($todayPendingCash, 2) }}) for Lab Approval</span>
                </button>
                @endif

                <div class="d-flex justify-content-between pt-2 mt-2 border-top border-light fs-12">
                    <span class="text-muted">Approved & Settled by Lab:</span>
                    <span class="fw-bold text-success">₹{{ number_format($todayApprovedHandovers, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between fs-12 mt-1">
                    <span class="text-muted">Digital / UPI Collected:</span>
                    <span class="fw-bold text-dark">₹{{ number_format($todayDigitalCollected, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between fs-12 mt-1">
                    <span class="text-muted">Total Collections Today:</span>
                    <span class="fw-bold text-primary">₹{{ number_format($todayTotalCollected, 2) }}</span>
                </div>
            </div>

            {{-- Payments Breakdown List --}}
            <div class="fs-11 text-uppercase fw-bold text-muted mb-2">Today's Collected Payments</div>
            <div class="d-flex flex-column gap-2">
                @forelse($todayPayments as $payment)
                @php
                    $modeName = $payment->paymentMode?->name ?? 'Cash';
                    $isCash = str_contains(strtolower($modeName), 'cash');
                    $isDeposited = str_contains($payment->remarks ?? '', '[Deposited at Reception');
                @endphp
                <div class="d-flex justify-content-between align-items-center p-2 rounded-3 bg-light" wire:key="pay-{{ $payment->id }}">
                    <div>
                        <div class="fw-bold text-dark fs-12">{{ $payment->invoice?->patient?->name ?? 'Patient' }}</div>
                        <div class="text-muted fs-10">
                            Inv #{{ $payment->invoice?->invoice_number ?? $payment->invoice_id }}
                            • {{ $payment->created_at->format('h:i A') }}
                        </div>
                        @if($isDeposited)
                        <div class="text-success fs-10 fw-semibold mt-1">
                            <i class="feather-check-circle me-1"></i>Deposited at Reception
                        </div>
                        @endif
                    </div>
                    <div class="text-end">
                        <div class="fw-bold text-dark fs-13">₹{{ number_format($payment->amount, 2) }}</div>
                        <div class="d-flex align-items-center gap-1 justify-content-end mt-1">
                            <span class="badge {{ $isCash ? 'bg-soft-danger text-danger' : 'bg-soft-info text-info' }} rounded-pill px-2 py-0 fs-10">
                                {{ $modeName }}
                            </span>
                            @if($isCash)
                                @if($isDeposited)
                                <span class="badge bg-soft-success text-success rounded-pill px-2 py-0 fs-9">Settled</span>
                                @else
                                <span class="badge bg-soft-warning text-warning rounded-pill px-2 py-0 fs-9">In Hand</span>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-3 text-muted fs-12">
                    <i class="feather-info me-1"></i> No payments collected from patients today yet.
                </div>
                @endforelse
            </div>
        </div>

        {{-- Commission Ledger --}}
        <div class="card border-0 shadow-sm rounded-4 p-3 mb-3 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <h6 class="fw-bold text-dark mb-0 fs-14">Recent Completed Visits Ledger</h6>
                <span class="badge bg-soft-success text-success rounded-pill px-2 py-1 fs-11 fw-bold">
                    +₹{{ number_format($commissionPerVisit, 2) }} / visit
                </span>
            </div>
            <div class="text-muted fs-11 mb-3">Itemized breakdown of completed sample collections and earned commissions</div>

            <div class="d-flex flex-column gap-2">
                @forelse($commissionLedger as $cVisit)
                @php
                    $cPatient = $cVisit->invoice?->patient;
                    $cItems = $cVisit->invoice?->items ?? collect();
                @endphp
                <div class="card border rounded-3 p-3 bg-white shadow-none" wire:key="cledger-{{ $cVisit->id }}" style="border-color: #e2e8f0 !important;">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <div class="fw-bold text-dark fs-14">{{ $cPatient->name ?? 'Patient' }}</div>
                            <div class="text-muted fs-11">
                                Inv #{{ $cVisit->invoice?->invoice_number ?? $cVisit->invoice_id }}
                                • {{ $cVisit->scheduled_date?->format('d M Y') }}
                                @if($cVisit->collected_at) • {{ \Carbon\Carbon::parse($cVisit->collected_at)->format('h:i A') }} @endif
                            </div>
                        </div>
                        <div class="text-end">
                            @if($cVisit->is_commission_settled)
                            <span class="badge bg-success text-white rounded-pill px-3 py-1 fs-12 fw-bold">
                                ✓ Paid (+₹{{ number_format($commissionPerVisit, 2) }})
                            </span>
                            <div class="fs-10 text-success mt-1">Paid on {{ $cVisit->commission_settled_at?->format('d M') }} via {{ $cVisit->commissionSettlement?->payment_mode ?? 'Cash' }}</div>
                            @else
                            <span class="badge bg-soft-warning text-warning rounded-pill px-3 py-1 fs-12 fw-bold">
                                ⏳ Unpaid (+₹{{ number_format($commissionPerVisit, 2) }})
                            </span>
                            <div class="fs-10 text-muted mt-1">Awaiting Lab Payout</div>
                            @endif
                        </div>
                    </div>

                    {{-- Tests collected in this visit --}}
                    <div class="mt-2 pt-2 border-top border-light">
                        <div class="fs-10 text-uppercase fw-bold text-muted mb-1">Tests Collected:</div>
                        <div class="d-flex flex-wrap gap-1">
                            @forelse($cItems as $cItem)
                            <span class="badge bg-light text-dark border rounded-pill px-2 py-1 fs-11">
                                <i class="feather-check text-success me-1"></i>{{ $cItem->test_name ?? $cItem->item_name }}
                            </span>
                            @empty
                            <span class="text-muted fs-11">No specific test records attached</span>
                            @endforelse
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-muted fs-12">
                    No completed collections on record yet.
                </div>
                @endforelse
            </div>
        </div>

        {{-- Commission Payout Receipts / Settlement History --}}
        @if(isset($commissionPayouts) && $commissionPayouts->isNotEmpty())
        <div class="card border-0 shadow-sm rounded-4 p-3 mb-3 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="fw-bold text-dark mb-0 fs-14"><i class="feather-file-text me-1 text-success"></i>Commission Payout Receipts</h6>
                <span class="badge bg-soft-success text-success rounded-pill px-2 py-0 fs-11">
                    {{ $commissionPayouts->count() }} Payouts
                </span>
            </div>
            <div class="d-flex flex-column gap-2 mt-2">
                @foreach($commissionPayouts as $payout)
                <div class="p-2 rounded-3 bg-light d-flex justify-content-between align-items-center" wire:key="payout-{{ $payout->id }}">
                    <div>
                        <div class="fw-bold text-dark fs-12">Paid via {{ $payout->payment_mode }}</div>
                        <div class="text-muted fs-10">
                            {{ $payout->payment_date?->format('d M Y, h:i A') }}
                            @if($payout->reference_no) • Ref: {{ $payout->reference_no }} @endif
                            @if($payout->settledBy) • By {{ $payout->settledBy->name }} @endif
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold text-success fs-14">+₹{{ number_format($payout->amount, 2) }}</div>
                        <span class="badge bg-soft-success text-success rounded-pill px-2 py-0 fs-9">Received</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    {{-- ======================================================== --}}
    {{-- TAB 4: PROFILE & DUTY SETTINGS                            --}}
    {{-- ======================================================== --}}
    @elseif($activeTab === 'profile')
        {{-- Profile Header Card --}}
        <div class="card border-0 shadow-sm rounded-4 p-3 mb-3 bg-white">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-primary text-white fw-extrabold rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 56px; height: 56px; font-size: 22px;">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="flex-grow-1">
                    <h5 class="fw-bold text-dark mb-0 fs-16">{{ auth()->user()->name }}</h5>
                    <div class="text-muted fs-12"><i class="feather-phone fs-11 me-1"></i>{{ auth()->user()->phone ?? 'No phone' }}</div>
                    <div class="text-muted fs-12"><i class="feather-mail fs-11 me-1"></i>{{ auth()->user()->email }}</div>
                </div>
            </div>
        </div>

        {{-- Duty Status Card --}}
        <div class="card border-0 shadow-sm rounded-4 p-3 mb-3 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <h6 class="fw-bold text-dark mb-0 fs-14">Duty Status</h6>
                    <div class="text-muted fs-11">Control whether lab admin can assign you visits</div>
                </div>
                @if($profile->is_available)
                <span class="badge bg-soft-success text-success rounded-pill px-3 py-1 fs-12 fw-bold d-flex align-items-center gap-1">
                    <span class="spinner-grow spinner-grow-sm text-success" role="status" style="width:7px;height:7px;"></span>
                    ON DUTY
                </span>
                @else
                <span class="badge bg-soft-secondary text-secondary rounded-pill px-3 py-1 fs-12 fw-bold">
                    OFF DUTY
                </span>
                @endif
            </div>

            <p class="fs-12 text-muted mb-3">
                @if($profile->is_available)
                You are currently marked as available to accept home collection tasks.
                @else
                You are marked off-duty. The lab dispatch system will not assign you new visits until you toggle back.
                @endif
            </p>

            <button wire:click="toggleDutyStatus"
                wire:loading.attr="disabled"
                class="btn {{ $profile->is_available ? 'btn-outline-danger' : 'btn-success text-white' }} rounded-3 fw-bold py-2 w-100 d-flex align-items-center justify-content-center gap-2">
                <span wire:loading wire:target="toggleDutyStatus" class="spinner-border spinner-border-sm"></span>
                <i class="feather-power fs-14"></i>
                <span>{{ $profile->is_available ? 'Switch to OFF DUTY' : 'Switch to ON DUTY' }}</span>
            </button>
        </div>

        {{-- Vehicle & Assignment Details --}}
        <div class="card border-0 shadow-sm rounded-4 p-3 mb-3 bg-white">
            <h6 class="fw-bold text-dark mb-3 fs-14">Vehicle & Working Details</h6>
            <div class="row g-2">
                <div class="col-6">
                    <div class="p-2 rounded-3 bg-light">
                        <div class="fs-10 text-muted text-uppercase fw-semibold">Vehicle Type</div>
                        <div class="fs-13 fw-bold text-dark mt-1">
                            <i class="feather-truck text-primary me-1"></i>{{ $profile->vehicle_type ?? 'Two-Wheeler (Bike)' }}
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-2 rounded-3 bg-light">
                        <div class="fs-10 text-muted text-uppercase fw-semibold">Vehicle Number</div>
                        <div class="fs-13 fw-bold text-dark mt-1">
                            {{ $profile->vehicle_number ?: 'Not Registered' }}
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="p-2 rounded-3 bg-light">
                        <div class="fs-10 text-muted text-uppercase fw-semibold">Working Hours</div>
                        <div class="fs-13 fw-bold text-dark mt-1">
                            <i class="feather-clock text-warning me-1"></i>
                            @if($profile->working_hours_start)
                                {{ date('h:i A', strtotime($profile->working_hours_start)) }} – {{ date('h:i A', strtotime($profile->working_hours_end)) }}
                            @else
                                Standard Lab Hours
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Lab Helpline & Support --}}
        <div class="card border-0 shadow-sm rounded-4 p-3 mb-3 bg-white">
            <h6 class="fw-bold text-dark mb-1 fs-14">Lab Reception Helpline</h6>
            <div class="text-muted fs-11 mb-3">Reach out if you encounter any issues on the road</div>

            <div class="d-flex align-items-center justify-content-between p-2 rounded-3 bg-light mb-2">
                <div>
                    <div class="fw-bold text-dark fs-13">{{ $company->name ?? 'Central Pathology Lab' }}</div>
                    <div class="text-muted fs-11"><i class="feather-phone me-1"></i>{{ $company->phone ?? 'Contact Reception' }}</div>
                </div>
                @if($company?->phone)
                <a href="tel:{{ $company->phone }}" class="btn btn-sm btn-primary rounded-pill px-3 py-1 d-flex align-items-center gap-1">
                    <i class="feather-phone-call fs-12"></i> Call Lab
                </a>
                @endif
            </div>
        </div>

        {{-- Change Password Form --}}
        <div class="card border-0 shadow-sm rounded-4 p-3 mb-3 bg-white">
            <h6 class="fw-bold text-dark mb-1 fs-14">Change Password</h6>
            <div class="text-muted fs-11 mb-3">Keep your mobile access secure</div>

            <form wire:submit.prevent="updatePassword">
                <div class="mb-2">
                    <label class="form-label fs-12 fw-semibold mb-1">Current Password</label>
                    <input type="password" wire:model="current_password" class="form-control form-control-sm rounded-3 @error('current_password') is-invalid @enderror">
                    @error('current_password') <div class="invalid-feedback fs-11">{{ $message }}</div> @enderror
                </div>
                <div class="mb-2">
                    <label class="form-label fs-12 fw-semibold mb-1">New Password</label>
                    <input type="password" wire:model="new_password" class="form-control form-control-sm rounded-3 @error('new_password') is-invalid @enderror">
                    @error('new_password') <div class="invalid-feedback fs-11">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label fs-12 fw-semibold mb-1">Confirm New Password</label>
                    <input type="password" wire:model="new_password_confirmation" class="form-control form-control-sm rounded-3">
                </div>
                <button type="submit" wire:loading.attr="disabled" class="btn btn-sm btn-outline-primary rounded-3 w-100 fw-bold py-2">
                    <span wire:loading wire:target="updatePassword" class="spinner-border spinner-border-sm me-1"></span>
                    Update Password
                </button>
            </form>
        </div>

        {{-- Logout Button --}}
        <div class="mb-4">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-light text-danger border rounded-4 w-100 fw-bold py-2 d-flex align-items-center justify-content-center gap-2">
                    <i class="feather-log-out fs-14"></i>
                    <span>Log Out from Device</span>
                </button>
            </form>
        </div>
    @endif

    {{-- ======================================================== --}}
    {{-- FIXED MOBILE BOTTOM NAVIGATION                           --}}
    {{-- ======================================================== --}}
    <div class="phlebo-bottom-nav">
        <button wire:click="setTab('visits')" type="button" class="phlebo-nav-btn {{ $activeTab === 'visits' ? 'active' : '' }}">
            <i class="feather-activity"></i>
            <span>Visits</span>
        </button>
        <button wire:click="setTab('history')" type="button" class="phlebo-nav-btn {{ $activeTab === 'history' ? 'active' : '' }}">
            <i class="feather-clock"></i>
            <span>History</span>
        </button>
        <button wire:click="setTab('earnings')" type="button" class="phlebo-nav-btn {{ $activeTab === 'earnings' ? 'active' : '' }}">
            <i class="feather-dollar-sign"></i>
            <span>Earnings</span>
        </button>
        <button wire:click="setTab('profile')" type="button" class="phlebo-nav-btn {{ $activeTab === 'profile' ? 'active' : '' }}">
            <i class="feather-user"></i>
            <span>Profile</span>
        </button>
    </div>

    {{-- ======================================================== --}}
    {{-- MODALS                                                   --}}
    {{-- ======================================================== --}}

    {{-- CONFIRM STATUS MODAL --}}
    @if($isConfirmOpen)
    <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">Confirm Status Update</h5>
                    <button wire:click="$set('isConfirmOpen', false)" class="btn-close"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="feather-refresh-cw fs-1 text-primary mb-3 d-block"></i>
                    <p class="fs-15 mb-0">Update status to <strong class="text-primary">{{ $confirmStatus }}</strong>?</p>
                    @if($capturedLat)
                    <div class="mt-2 fs-12 text-success"><i class="feather-map-pin me-1"></i>GPS captured</div>
                    @endif
                </div>
                <div class="modal-footer border-0 gap-2">
                    <button wire:click="$set('isConfirmOpen', false)" class="btn btn-outline-secondary flex-fill">Cancel</button>
                    <button wire:click="confirmStatusUpdate" wire:loading.attr="disabled" class="btn btn-primary flex-fill">
                        <span wire:loading wire:target="confirmStatusUpdate" class="spinner-border spinner-border-sm me-1"></span>
                        Confirm
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- PAYMENT MODAL --}}
    @if($isPaymentModalOpen)
    <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold"><i class="feather-credit-card me-2 text-warning"></i>Collect Payment</h5>
                    <button wire:click="$set('isPaymentModalOpen', false)" class="btn-close"></button>
                </div>
                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Amount to Collect (₹) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" wire:model="paymentAmount"
                               class="form-control form-control-lg fw-bold @error('paymentAmount') is-invalid @enderror">
                        @error('paymentAmount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Payment Mode</label>
                        <select wire:model="paymentMode" class="form-select">
                            <option value="Cash">Cash (Physical Currency)</option>
                            <option value="UPI">UPI / QR Code</option>
                            <option value="Card">Card / POS Machine</option>
                            <option value="Bank Transfer">Bank Transfer / NEFT</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 gap-2">
                    <button wire:click="$set('isPaymentModalOpen', false)" class="btn btn-outline-secondary flex-fill">Cancel</button>
                    <button wire:click="recordPayment" wire:loading.attr="disabled" class="btn btn-warning text-dark fw-bold flex-fill">
                        <span wire:loading wire:target="recordPayment" class="spinner-border spinner-border-sm me-1"></span>
                        Record Payment
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ADD TEST MODAL --}}
    @if($isAddTestModalOpen)
    <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,.6); backdrop-filter: blur(2px);">
        <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 pb-0">
                    <div>
                        <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                            <i class="feather-plus-circle text-primary"></i> Add Test on Site
                        </h5>
                        <div class="text-muted fs-12 mt-1">Search active lab tests & packages</div>
                    </div>
                    <button wire:click="$set('isAddTestModalOpen', false)" class="btn-close"></button>
                </div>
                <div class="modal-body px-4 pt-3">
                    {{-- Search Input --}}
                    <div class="position-relative mb-3">
                        <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden border">
                            <span class="input-group-text bg-white border-0 text-muted ps-3">
                                <i class="feather-search fs-14"></i>
                            </span>
                            <input type="text"
                                wire:model.live.debounce.300ms="testSearchQuery"
                                class="form-control border-0 fs-14 px-2"
                                placeholder="Search test name, code, dept..."
                                autofocus>
                            @if($testSearchQuery)
                            <button wire:click="$set('testSearchQuery', '')" class="btn btn-white border-0 text-muted pe-3" type="button">
                                <i class="feather-x fs-14"></i>
                            </button>
                            @endif
                        </div>
                        <div wire:loading wire:target="testSearchQuery" class="position-absolute end-0 top-100 mt-1 pe-2">
                            <span class="spinner-border spinner-border-sm text-primary" role="status"></span>
                        </div>
                    </div>

                    @error('testSearchQuery')
                    <div class="alert alert-danger py-2 px-3 fs-12 rounded-3 mb-3">{{ $message }}</div>
                    @enderror

                    {{-- Section title --}}
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fs-11 text-uppercase fw-bold text-muted">
                            {{ $testSearchQuery ? 'Search Results' : 'Available Tests' }}
                        </span>
                        <span class="badge bg-light text-muted border rounded-pill fs-11">{{ count($testSearchResults) }} found</span>
                    </div>

                    {{-- Tests List --}}
                    <div class="d-flex flex-column gap-2" style="max-height: 380px; overflow-y: auto;">
                        @forelse($testSearchResults as $t)
                        <div class="card border rounded-3 p-3 transition-all bg-white shadow-none" wire:key="add-test-{{ $t['id'] }}" style="border-color: #e2e8f0 !important;">
                            <div class="d-flex justify-content-between align-items-center gap-2">
                                <div class="flex-grow-1">
                                    <div class="fw-bold text-dark fs-14 lh-sm mb-1">{{ $t['name'] }}</div>
                                    <div class="d-flex flex-wrap align-items-center gap-1">
                                        @if(!empty($t['test_code']))
                                        <span class="badge bg-light text-secondary border rounded-pill px-2 py-0 fs-10 fw-semibold">
                                            #{{ $t['test_code'] }}
                                        </span>
                                        @endif
                                        @if(!empty($t['department']))
                                        <span class="badge bg-soft-info text-info rounded-pill px-2 py-0 fs-10">
                                            {{ $t['department'] }}
                                        </span>
                                        @endif
                                        @if(!empty($t['sample_type']))
                                        <span class="badge bg-soft-secondary text-dark rounded-pill px-2 py-0 fs-10">
                                            <i class="feather-droplet fs-9 me-1 text-danger"></i>{{ $t['sample_type'] }}
                                        </span>
                                        @endif
                                        @if(!empty($t['is_package']))
                                        <span class="badge bg-soft-purple text-purple rounded-pill px-2 py-0 fs-10">
                                            Package
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-end d-flex flex-column align-items-end gap-1 flex-shrink-0">
                                    <div class="fw-bold text-success fs-15">₹{{ number_format($t['price'], 2) }}</div>
                                    <button wire:click="addTestToVisit({{ $t['id'] }})"
                                        wire:loading.attr="disabled"
                                        class="btn btn-sm btn-primary rounded-pill px-3 py-1 fs-12 fw-semibold d-flex align-items-center gap-1 shadow-sm">
                                        <i class="feather-plus fs-12"></i>
                                        <span>Add</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4 text-muted">
                            <i class="feather-inbox fs-2 d-block mb-2 opacity-25"></i>
                            <div class="fs-13 fw-medium">No tests found for "{{ $testSearchQuery }}"</div>
                            <div class="fs-11 text-muted">Try searching another test name or code</div>
                        </div>
                        @endforelse
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 px-4 pb-4">
                    <button wire:click="$set('isAddTestModalOpen', false)" class="btn btn-light rounded-pill w-100 fw-semibold py-2">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- CASH HANDOVER MODAL --}}
    @if($isHandoverModalOpen)
    <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,.6); backdrop-filter: blur(2px); z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-3 bg-soft-primary text-primary p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="feather-send fs-20"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0 fs-16">Submit Cash Handover to Lab</h5>
                            <div class="text-muted fs-12">Submit physical currency for Lab Admin approval</div>
                        </div>
                    </div>
                    <button wire:click="$set('isHandoverModalOpen', false)" class="btn-close"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <div class="alert alert-soft-warning border-0 rounded-3 p-3 mb-3">
                        <div class="fs-11 text-muted text-uppercase fw-bold">Physical Cash to Hand Over</div>
                        <div class="fs-24 fw-extrabold text-danger mt-1">₹{{ number_format($todayPendingCash, 2) }}</div>
                        <div class="fs-11 text-muted mt-1">
                            <i class="feather-shield text-warning me-1"></i>
                            After submitting, the Lab Admin or Receptionist will count and verify the physical cash before marking it settled.
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold fs-12 mb-1">Handover Notes (optional)</label>
                        <textarea wire:model="handoverNotes" class="form-control rounded-3 fs-13" rows="2" placeholder="e.g. Handed over physical cash at main reception desk..."></textarea>
                        @error('handoverNotes') <div class="invalid-feedback fs-11 d-block">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="modal-footer border-0 gap-2 px-4 pb-4">
                    <button wire:click="$set('isHandoverModalOpen', false)" class="btn btn-light rounded-pill flex-fill fw-semibold py-2">
                        Cancel
                    </button>
                    <button wire:click="requestCashHandover" wire:loading.attr="disabled" class="btn btn-primary text-white rounded-pill flex-fill fw-bold py-2 shadow-sm">
                        <span wire:loading wire:target="requestCashHandover" class="spinner-border spinner-border-sm me-1"></span>
                        Submit for Lab Approval
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
