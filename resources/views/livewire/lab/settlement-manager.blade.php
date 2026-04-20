<div>
    <div class="page-header d-flex flex-wrap align-items-center justify-content-between gap-2 gap-md-3">
        <div class="page-header-left d-flex align-items-center flex-wrap">
            <div class="page-header-title">
                <h5 class="m-b-10">Partner Settlements</h5>
            </div>
            <ul class="breadcrumb d-none d-md-flex mb-0">
                <li class="breadcrumb-item"><a href="{{ route('lab.dashboard') }}" wire:navigate>Home</a></li>
                <li class="breadcrumb-item text-primary">Settlements</li>
            </ul>
        </div>
        <div class="page-header-right d-flex gap-2">
            <div class="btn-group p-1  border rounded shadow-sm">
                <button wire:click="$set('partnerType', 'Doctor')" class="btn btn-sm {{ $partnerType == 'Doctor' ? 'btn-primary shadow-sm' : 'btn-light border-0 text-muted' }} px-3 fw-bold transition-all">
                    <i class="feather-user me-2"></i>Doctors
                </button>
                <button wire:click="$set('partnerType', 'Agent')" class="btn btn-sm {{ $partnerType == 'Agent' ? 'btn-primary shadow-sm' : 'btn-light border-0 text-muted' }} px-3 fw-bold transition-all">
                    <i class="feather-briefcase me-2"></i>Agents
                </button>
                <button wire:click="$set('partnerType', 'Collection Center')" class="btn btn-sm {{ $partnerType == 'Collection Center' ? 'btn-primary shadow-sm' : 'btn-light border-0 text-muted' }} px-3 fw-bold transition-all">
                    <i class="feather-map-pin me-2"></i>Centers
                </button>
            </div>
        </div>
    </div>

    <div class="main-content mt-4">
        @if (session()->has('message'))
            <div class="alert alert-success border-0 shadow-sm rounded-3 d-flex align-items-center py-3 mb-4">
                <i class="feather-check-circle fs-4 me-2"></i>
                <strong>{{ session('message') }}</strong>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-danger border-0 shadow-sm rounded-3 d-flex align-items-center py-3 mb-4">
                <i class="feather-alert-octagon fs-4 me-2"></i>
                <strong>{{ session('error') }}</strong>
            </div>
        @endif

        @if($viewMode === 'list')
            {{-- Analytics & Stats --}}
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4  h-100">
                        <div class="card-body p-4 d-flex align-items-center gap-3">
                            <div class="avatar-text avatar-lg bg-soft-warning rounded-circle flex-shrink-0">
                                <i class="feather-clock text-warning fs-4"></i>
                            </div>
                            <div>
                                <h3 class="fw-bolder mb-1 text-dark">₹{{ number_format($stats['total_pending'], 2) }}</h3>
                                <p class="text-muted small mb-0 fw-bold text-uppercase ls-1 fs-10">{{ $partnerType === 'Collection Center' ? 'Pending from Centers' : 'Total Outstanding' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4  h-100">
                        <div class="card-body p-4 d-flex align-items-center gap-3">
                            <div class="avatar-text avatar-lg bg-soft-success rounded-circle flex-shrink-0">
                                <i class="feather-check-circle text-success fs-4"></i>
                            </div>
                            <div>
                                <h3 class="fw-bolder mb-1 text-dark">₹{{ number_format($stats['settled_today'], 2) }}</h3>
                                <p class="text-muted small mb-0 fw-bold text-uppercase ls-1 fs-10">Settled Today</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4  h-100">
                        <div class="card-body p-4 d-flex align-items-center gap-3">
                            <div class="avatar-text avatar-lg bg-soft-primary rounded-circle flex-shrink-0">
                                <i class="feather-users text-primary fs-4"></i>
                            </div>
                            <div>
                                <h3 class="fw-bolder mb-1 text-dark">{{ $stats['partners_with_pending'] }}</h3>
                                <p class="text-muted small mb-0 fw-bold text-uppercase ls-1 fs-10">Active Partners</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                {{-- Partner Listing Table --}}
                <div class="col-xl-8">
                    <div class="card stretch stretch-full border-0 shadow-sm rounded-4 overflow-hidden ">
                        <div class="card-header  border-bottom-0 py-4">
                            <div class="row g-3 align-items-center">
                                <div class="col-sm-6">
                                    <h5 class="fw-bold mb-0 text-dark">
                                        <i class="feather-list text-primary me-2"></i> {{ $partnerType }} Dues List
                                    </h5>
                                </div>
                                <div class="col-sm-6">
                                    <div class="input-group search-group shadow-sm">
                                        <span class="input-group-text ">
                                            <i class="feather-search text-primary"></i>
                                        </span>
                                        <input type="text" class="form-control" wire:model.live.debounce.300ms="searchPartner" placeholder="Search by name or phone...">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive border-top">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light fs-11 text-uppercase text-muted">
                                        <tr>
                                            <th class="ps-4 py-3">Partner Details</th>
                                            <th class="py-3 text-center">Invoices</th>
                                            <th class="py-3 text-end">Outstanding Dues</th>
                                            <th class="py-3 text-end pe-4">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="fs-13">
                                        @forelse($partners as $p)
                                            <tr wire:key="partner-{{ $p->id }}" class="border-bottom border-light">
                                                <td class="ps-4 py-3">
                                                    <div class="d-flex align-items-center gap-3">
                                                        <div class="avatar-text avatar-md {{ $p->pending_amount > 200 ? 'bg-soft-danger text-danger' : ($p->pending_amount > 0 ? 'bg-soft-warning text-warning' : 'bg-soft-primary text-primary') }} rounded-circle fw-bolder">
                                                            {{ substr($p->name ?? '?', 0, 1) }}
                                                        </div>
                                                        <div>
                                                            <div class="fw-bold text-dark fs-14">{{ $p->name ?? 'Unknown' }}</div>
                                                            @if($partnerType === 'Collection Center')
                                                                <div class="text-muted fs-11">
                                                                    <i class="feather-tag fs-10 me-1"></i>{{ $p->center_code ?? 'No Code' }}
                                                                    @if(!$p->user_id)
                                                                        <span class="ms-2 badge bg-soft-danger text-danger fs-9 border-0 p-0" title="No user linked for settlement">! NO USER</span>
                                                                    @endif
                                                                </div>
                                                            @else
                                                                <div class="text-muted fs-11"><i class="feather-phone fs-10 me-1"></i>{{ $p->phone ?? 'N/A' }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-soft-secondary text-secondary rounded-pill px-3 fw-bold">{{ $p->invoice_count ?? 0 }}</span>
                                                </td>
                                                <td class="text-end fw-bolder text-dark fs-15">
                                                    <span class="{{ $p->pending_amount > 0 ? 'text-danger' : 'text-muted opacity-50' }}">
                                                        ₹{{ number_format($p->pending_amount ?? 0, 2) }}
                                                    </span>
                                                </td>
                                                <td class="text-end pe-4">
                                                    <div class="hstack gap-2 justify-content-end">
                                                        <button wire:click="selectPartner({{ $p->id }}, 'insights')" class="btn btn-sm btn-icon btn-outline-info rounded-pill" title="Insights">
                                                            <i class="feather-pie-chart"></i>
                                                        </button>
                                                        @can('create settlements')
                                                            <button wire:click="selectPartner({{ $p->id }}, 'process')" class="btn btn-sm {{ $partnerType === 'Collection Center' ? 'btn-success' : 'btn-primary' }} rounded-pill px-3 fw-bold shadow-sm" {{ ($p->pending_amount ?? 0) <= 0 ? 'disabled' : '' }}>
                                                                {{ $partnerType === 'Collection Center' ? 'Collect' : 'Settle' }} <i class="feather-arrow-right ms-1"></i>
                                                            </button>
                                                        @endcan
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-5">
                                                    <div class="mb-3 text-muted"><i class="feather-inbox fs-1"></i></div>
                                                    <h6 class="fw-bold text-dark">No {{ $partnerType }}s found</h6>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @if($partners->hasPages())
                            <div class="card-footer  py-3 border-top border-light d-flex justify-content-end pagination-sm">
                                {{ $partners->links('livewire::bootstrap') }}
                            </div>
                        @endif
                    </div>
                </div>

                {{-- History Sidebar --}}
                <div class="col-xl-4">
                    <div class="card stretch stretch-full border-0 shadow-sm rounded-4 overflow-hidden ">
                        <div class="card-header  border-bottom-0 py-4">
                            <h5 class="card-title mb-0 fw-bold"><i class="feather-clock text-info me-2"></i>Recent Settlements</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush border-top">
                                @forelse($settlements as $s)
                                    <div class="list-group-item p-3 border-bottom border-light hover-bg-light transition-all">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="badge {{ $s->type == 'Doctor' ? 'bg-soft-primary text-primary' : ($s->type == 'Agent' ? 'bg-soft-info text-info' : 'bg-soft-success text-success') }} rounded-pill fs-9 px-2 fw-bold text-uppercase">{{ $s->type }}</span>
                                            <span class="text-muted fs-10 fw-bold text-uppercase">{{ $s->payment_date->format('d M, Y') }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <div class="fw-bolder text-dark fs-13">{{ $s->user->name ?? 'Deleted User' }}</div>
                                            @php
                                                $st = $s->status ?? 'Approved';
                                                $stColor = $st === 'Approved' ? 'success' : ($st === 'Pending' ? 'warning' : 'danger');
                                            @endphp
                                            <span class="badge bg-soft-{{ $stColor }} text-{{ $stColor }} rounded-pill fs-9 px-2 fw-bold">{{ $st }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-end">
                                            <div class="fs-12">
                                                <div class="text-success fw-bold">₹{{ number_format($s->amount, 2) }}</div>
                                                <div class="fs-10 text-muted mt-1">{{ $s->payment_mode }}</div>
                                            </div>
                                            <div class="d-flex gap-1">
                                                @if(($s->status ?? 'Approved') === 'Pending')
                                                    @can('edit settlements')
                                                        <button wire:click="approveSettlement({{ $s->id }})" class="btn btn-sm btn-success py-1 px-2 fs-9 fw-bold"><i class="feather-check"></i></button>
                                                        <button wire:click="rejectSettlement({{ $s->id }})" class="btn btn-sm btn-danger py-1 px-2 fs-9 fw-bold"><i class="feather-x"></i></button>
                                                    @endcan
                                                @endif
                                                <button class="btn btn-sm btn-light border-0 rounded px-2 py-1 fs-10"><i class="feather-printer"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="p-5 text-center text-muted"><small>No records</small></div>
                                @endforelse
                            </div>
                        </div>
                        @if($settlements->hasPages())
                            <div class="card-footer  p-3 border-top border-light d-flex justify-content-end pagination-xs"> 
                                {{ $settlements->links('livewire::bootstrap') }} 
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        @elseif($viewMode === 'process')
            {{-- Settlement Processing View --}}
            <div class="card stretch stretch-full border-0 shadow-sm rounded-4  animated slideInUp-quick">
                <div class="card-header  border-bottom py-4 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <button wire:click="$set('viewMode', 'list')" class="btn btn-sm btn-light rounded-circle shadow-sm" style="width:36px; height:36px; padding:0;"><i class="feather-arrow-left"></i></button>
                        <div>
                            <h5 class="card-title mb-0 fw-bold">{{ $partnerType === 'Collection Center' ? 'Record Collection' : 'Process Settlement' }}</h5>
                            <p class="text-muted small mb-0">
                                {{ $selectedPartner->name ?? 'Unknown' }} 
                                @if($partnerType === 'Collection Center')
                                    • {{ $selectedPartner->center_code ?? 'No Code' }}
                                @else
                                    • {{ $selectedPartner->phone ?? 'No Phone' }}
                                @endif
                            </p>
                        </div>
                    </div>
                    <button wire:click="$set('viewMode', 'insights')" class="btn btn-outline-info btn-sm rounded-pill px-3 shadow-sm"><i class="feather-pie-chart me-2"></i>Full Portfolio</button>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-lg-7">
                            <h6 class="fw-bold mb-3 text-dark">{{ $partnerType === 'Collection Center' ? 'Unpaid B2B Bills' : 'Pending Commissions' }}</h6>
                            <div class="table-responsive border rounded-4 overflow-hidden shadow-sm">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light fs-10 text-uppercase text-muted">
                                        <tr>
                                            <th class="ps-4" style="width: 40px;">Sel</th>
                                            <th>Invoice Info</th>
                                            <th class="text-end pe-4">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody class="fs-13">
                                        @forelse($pendingInvoices as $inv)
                                            <tr wire:click="toggleInvoice({{ $inv->id }})" class="{{ in_array($inv->id, $selectedInvoices) ? 'bg-soft-primary' : '' }} pointer transition-all">
                                                <td class="ps-4">
                                                    <div class="form-check custom-check">
                                                        <input class="form-check-input" type="checkbox" wire:model.live="selectedInvoices" value="{{ $inv->id }}" @click.stop>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="fw-bold text-dark">{{ $inv->invoice_number }}</div>
                                                    <div class="text-muted fs-11">{{ $inv->patient->name ?? 'Patient N/A' }} • {{ $inv->invoice_date->format('d M, Y') }}</div>
                                                </td>
                                                <td class="text-end pe-4 fw-bolder text-primary">₹{{ number_format($partnerType == 'Doctor' ? $inv->doctor_commission_amount : ($partnerType == 'Agent' ? $inv->agent_commission_amount : ($partnerType == 'Collection Center' ? $inv->total_b2b_amount : $inv->total_amount)), 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="3" class="text-center py-5 text-muted">No pending commissions found.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-lg-5">
                            <div class="bg-light p-4 rounded-4 border border-light h-100 shadow-inner">
                                <div class="p-4  rounded-4 shadow-sm text-center mb-4 border border-primary border-opacity-10">
                                    <p class="text-muted fs-11 text-uppercase fw-bold ls-1 mb-1">{{ $partnerType === 'Collection Center' ? 'Receivable Amount' : 'Payable Amount' }}</p>
                                    <h2 class="fw-bolder text-primary mb-0">₹{{ number_format($amount_to_pay, 2) }}</h2>
                                </div>
                                <div class="row g-3">
                                    <div class="col-12"><label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">Date</label><input type="date" class="form-control rounded-3 border-0 shadow-sm py-2 fs-13" wire:model="payment_date"></div>
                                    <div class="col-12">
                                        <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">Method</label>
                                        <select class="form-select rounded-3 border-0 shadow-sm py-2 fs-13" wire:model="payment_mode">
                                            @foreach($paymentModes as $mode) <option value="{{ $mode->name }}">{{ $mode->name }}</option> @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12"><label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">UTR / Ref No.</label><input type="text" class="form-control rounded-3 border-0 shadow-sm py-2 fs-13" wire:model="reference_no" placeholder="Transaction ID"></div>
                                    <div class="col-12"><label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">Notes</label><textarea class="form-control rounded-3 border-0 shadow-sm fs-13" wire:model="notes" rows="2" placeholder="Add any details..."></textarea></div>
                                </div>
                                <button wire:click="processSettlement" class="btn {{ $partnerType === 'Collection Center' ? 'btn-success' : 'btn-primary' }} w-100 py-3 rounded-4 fw-bolder mt-4 shadow" {{ count($selectedInvoices) == 0 ? 'disabled' : '' }}>
                                    {{ $partnerType === 'Collection Center' ? 'RECORD PAYMENT RECEIVED' : 'SUBMIT SETTLEMENT' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @elseif($viewMode === 'insights')
            {{-- Partner Insights Portfolio View --}}
            <div class="card stretch stretch-full border-0 shadow-sm rounded-4  animated fadeIn">
                <div class="card-header  border-bottom py-4 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <button wire:click="$set('viewMode', 'list')" class="btn btn-sm btn-light rounded-circle shadow-sm" style="width:36px; height:36px; padding:0;"><i class="feather-arrow-left"></i></button>
                        <div>
                            <h5 class="card-title mb-0 fw-bold">{{ $selectedPartner->name ?? 'Partner' }} Portfolio</h5>
                            <p class="text-muted small mb-0">{{ $partnerType }} Insights • {{ $selectedPartner->phone ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="d-flex gap-2 align-items-center">
                        <div class="input-group input-group-sm">
                            <input type="date" wire:model.live="startDate" class="form-control border-0 bg-light rounded-start shadow-sm">
                            <span class="input-group-text bg-light border-0">To</span>
                            <input type="date" wire:model.live="endDate" class="form-control border-0 bg-light rounded-end shadow-sm">
                        </div>
                        <button wire:click="selectPartner({{ $selectedPartnerId }}, 'process')" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
                            <i class="feather-dollar-sign me-2"></i>New Settlement
                        </button>
                    </div>
                </div>
                <div class="card-body p-4 bg-soft-light">
                    {{-- Mini Stats Layer --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <div class="stat-card-inner p-4 rounded-4  border-start border-primary border-4">
                                <p class="text-muted fs-10 fw-bold text-uppercase mb-1">Patients Referred</p>
                                <h3 class="fw-bolder text-dark mb-0">{{ $partnerStats['total_bills'] ?? 0 }}</h3>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card-inner p-4 rounded-4  border-start border-success border-4">
                                <p class="text-muted fs-10 fw-bold text-uppercase mb-1">Total Revenue</p>
                                <h3 class="fw-bolder text-dark mb-0">₹{{ number_format($partnerStats['total_revenue'] ?? 0, 2) }}</h3>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card-inner p-4 rounded-4  border-start border-info border-4">
                                <p class="text-muted fs-10 fw-bold text-uppercase mb-1">Earned Commission</p>
                                <h3 class="fw-bolder text-dark mb-0">₹{{ number_format($partnerStats['total_commission'] ?? 0, 2) }}</h3>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card-inner p-4 rounded-4  border-start border-warning border-4">
                                <p class="text-muted fs-10 fw-bold text-uppercase mb-1">Avg Ticket Size</p>
                                <h3 class="fw-bolder text-dark mb-0">₹{{ number_format($partnerStats['avg_bill'] ?? 0, 2) }}</h3>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-lg-8">
                            <h6 class="fw-bold mb-3 d-flex align-items-center"><i class="feather-activity text-primary me-2"></i>Referral Performance History</h6>
                            <div class="table-responsive  rounded-4 shadow-sm border">
                                <table class="table table-hover align-middle mb-0 fs-13">
                                    <thead class="bg-light fs-11 text-muted text-uppercase">
                                        <tr>
                                            <th class="ps-4">Patient</th>
                                            <th>Bill Date</th>
                                            <th class="text-end">Bill Amount</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($partnerHistory as $bill)
                                            <tr>
                                                <td class="ps-4">
                                                    <div class="fw-bold">{{ $bill->patient->name ?? 'N/A' }}</div>
                                                    <small class="text-muted">{{ $bill->invoice_number }}</small>
                                                </td>
                                                <td>{{ $bill->invoice_date->format('d M, Y') }}</td>
                                                <td class="text-end fw-bold">₹{{ number_format($bill->total_amount, 2) }}</td>
                                                <td class="text-center">
                                                    <span class="badge {{ $bill->payment_status === 'Paid' ? 'bg-soft-success text-success' : 'bg-soft-warning text-warning' }} rounded-pill px-3 py-1 fs-10">
                                                        {{ $bill->payment_status }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="4" class="text-center py-5">No referral history for this period.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <h6 class="fw-bold mb-3 d-flex align-items-center"><i class="feather-check-square text-success me-2"></i>Recent Settlements</h6>
                            <div class="list-group list-group-flush rounded-4  shadow-sm border overflow-hidden">
                                @forelse($partnerStats['settlement_history'] as $sh)
                                    <div class="list-group-item p-3 hover-bg-light border-bottom">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="fw-bold text-dark fs-13">₹{{ number_format($sh->amount, 2) }}</span>
                                            <small class="text-muted fs-10 fw-bold text-uppercase">{{ $sh->payment_date->format('d M, Y') }}</small>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted fs-11">{{ $sh->payment_mode }}</small>
                                            <span class="badge bg-soft-success text-success fs-9 rounded-pill px-2">Completed</span>
                                        </div>
                                    </div>
                                @empty
                                    <div class="p-5 text-center text-muted small">No past settlements found.</div>
                                @endforelse
                                @if(count($partnerStats['settlement_history']) > 0)
                                    <div class="p-3 text-center bg-light border-top mt-auto">
                                        <small class="text-primary fw-bold cursor-pointer">VIEW ALL HISTORY</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <style>
    .search-group .form-control:focus { box-shadow: none; }
    .bg-soft-primary { background-color: rgba(59, 113, 202, 0.08) !important; }
    .bg-soft-success { background-color: rgba(25, 135, 84, 0.08) !important; }
    .bg-soft-warning { background-color: rgba(224, 167, 0, 0.08) !important; }
    .bg-soft-info { background-color: rgba(23, 162, 184, 0.08) !important; }
    .bg-soft-danger { background-color: rgba(220, 53, 69, 0.08) !important; }
    .bg-soft-secondary { background-color: rgba(108, 117, 125, 0.08) !important; }
    
    .bg-soft-light { background-color: #f8fafc !important; }
    html.app-skin-dark .bg-soft-light { background-color: rgba(255, 255, 255, 0.02) !important; }

    .stat-card-inner { background-color: #fff; transition: all 0.3s; border: 1px solid #f1f5f9; box-shadow: 0 4px 12px rgba(0,0,0,0.02) !important; }
    html.app-skin-dark .stat-card-inner { background-color: #1a1a2e !important; border: 1px solid rgba(255,255,255,0.05) !important; }
    
    .hover-bg-light:hover { background-color: #f1f5f9 !important; }
    html.app-skin-dark .hover-bg-light:hover { background-color: rgba(255, 255, 255, 0.05) !important; }

    .transition-all { transition: all 0.2s ease-in-out; }
    .custom-check .form-check-input:checked { background-color: var(--bs-primary); border-color: var(--bs-primary); }
    .pointer { cursor: pointer; }
    .shadow-inner { box-shadow: inset 0 2px 4px 0 rgba(0,0,0,0.02); }
    html.app-skin-dark .shadow-inner { box-shadow: none; }
    .ls-1 { letter-spacing: 0.5px; }
    .pagination { margin-bottom: 0; gap: 2px; }
    .page-item .page-link { border-radius: 6px !important; margin: 0 2px; border: 1px solid #e2e8f0; color: #64748b; font-size: 13px; font-weight: 600; padding: 0.4rem 0.8rem; }
    .page-item.active .page-link { background-color: var(--bs-primary); border-color: var(--bs-primary); color: white; }
    .animated { animation-duration: 0.4s; }
    @keyframes pulse-one { 0% { transform: scale(1); } 50% { transform: scale(1.02); } 100% { transform: scale(1); } }
    .pulse-once { animation: pulse-one 1s ease-out; }
    </style>
</div>
