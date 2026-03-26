<div>
    <div class="page-header mb-4">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10 fw-bold text-dark">Partner Analytics</h5>
                <p class="text-muted small mb-0 font-medium">Performance overview for {{ $role }}</p>
            </div>
            <ul class="breadcrumb d-none d-md-flex ms-3">
                <li class="breadcrumb-item"><a href="#" class="text-muted small">Home</a></li>
                <li class="breadcrumb-item text-primary fw-medium small">Dashboard</li>
            </ul>
        </div>
        <div class="page-header-right d-flex align-items-center gap-3">
            <div class="d-flex align-items-center gap-2 bg-white rounded-3 p-1 px-2 border shadow-sm">
                <i class="feather-calendar text-primary fs-12"></i>
                <input type="date" wire:model.live="startDate" class="form-control form-control-sm border-0 shadow-none fs-12 fw-bold text-dark bg-transparent" style="width: 120px;">
                <span class="text-muted small">to</span>
                <input type="date" wire:model.live="endDate" class="form-control form-control-sm border-0 shadow-none fs-12 fw-bold text-dark bg-transparent" style="width: 120px;">
            </div>
            <div class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill fs-11 fw-bold border border-primary border-opacity-10 d-flex align-items-center gap-1 shadow-sm">
                <i class="feather-user"></i> {{ $role }}
            </div>
        </div>
    </div>

    <div class="main-content">
        <div class="row g-4">
            {{-- Stats Cards --}}
            @if($role === 'Collection Center')
                <div class="col-xxl-3 col-md-6">
                    <div class="card stretch stretch-full border-0 shadow-sm rounded-4 bg-white">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center gap-4">
                                <div class="avatar-text avatar-lg bg-soft-primary text-primary rounded-4 shadow-sm">
                                    <i class="feather-box fs-4"></i>
                                </div>
                                <div>
                                    <div class="fs-4 fw-bold text-dark mb-0">{{ $stats['samples_collected'] }}</div>
                                    <h3 class="fs-13 fw-semibold text-muted text-uppercase ls-1 mb-0">Collected Today</h3>
                                </div>
                            </div>
                            <div class="mt-4 pt-2 border-top border-light d-flex justify-content-between align-items-center">
                                <span class="fs-11 text-muted">Pending: <strong>{{ $stats['pending_collection'] }}</strong></span>
                                <span class="badge bg-soft-primary text-primary rounded-pill px-2 fw-bold">Live</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-md-6">
                    <div class="card stretch stretch-full border-0 shadow-sm rounded-4 bg-white">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center gap-4">
                                <div class="avatar-text avatar-lg bg-soft-success text-success rounded-4 shadow-sm">
                                    <i class="feather-file-check fs-4"></i>
                                </div>
                                <div>
                                    <div class="fs-4 fw-bold text-dark mb-0">{{ $stats['reports_ready'] }}</div>
                                    <h3 class="fs-13 fw-semibold text-muted text-uppercase ls-1 mb-0">Reports Ready</h3>
                                </div>
                            </div>
                            <div class="mt-4 pt-2 border-top border-light">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fs-11 text-muted">Completion Rate</span>
                                    <span class="fs-11 fw-bold text-success">{{ $stats['total_invoices'] > 0 ? round($stats['reports_ready'] / $stats['total_invoices'] * 100) : 0 }}%</span>
                                </div>
                                <div class="progress" style="height: 4px;">
                                    <div class="progress-bar bg-success" style="width: {{ $stats['total_invoices'] > 0 ? ($stats['reports_ready'] / $stats['total_invoices'] * 100) : 0 }}%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-md-6">
                    <div class="card stretch stretch-full border-0 shadow-sm rounded-4 bg-white">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center gap-4">
                                <div class="avatar-text avatar-lg bg-soft-info text-info rounded-4 shadow-sm">
                                    <i class="feather-trending-up fs-4"></i>
                                </div>
                                <div>
                                    <div class="fs-4 fw-bold text-dark mb-0">₹{{ number_format($stats['total_billing'], 2) }}</div>
                                    <h3 class="fs-13 fw-semibold text-muted text-uppercase ls-1 mb-0">Total Revenue</h3>
                                </div>
                            </div>
                            <div class="mt-4 pt-2 border-top border-light d-flex justify-content-between align-items-center">
                                <span class="fs-11 text-muted">Total Billing Done</span>
                                <span class="badge bg-soft-info text-info rounded-pill px-2 fw-bold">₹{{ number_format($stats['this_month_profit'] + ($stats['lab_dues'] ?? 0), 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-md-6">
                    <div class="card stretch stretch-full border-0 shadow-sm rounded-4 bg-white">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center gap-4">
                                <div class="avatar-text avatar-lg bg-soft-success text-success rounded-4 shadow-sm">
                                    <i class="feather-dollar-sign fs-4"></i>
                                </div>
                                <div>
                                    <div class="fs-4 fw-bold text-dark mb-0">₹{{ number_format($stats['total_profit'], 2) }}</div>
                                    <h3 class="fs-13 fw-semibold text-muted text-uppercase ls-1 mb-0">My Profit</h3>
                                </div>
                            </div>
                            <div class="mt-4 pt-2 border-top border-light d-flex justify-content-between align-items-center">
                                <span class="fs-11 text-muted">Take home earnings</span>
                                <span class="badge bg-soft-success text-success rounded-pill px-2 fw-bold">₹{{ number_format($stats['this_month_profit'], 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-md-6">
                    <div class="card stretch stretch-full border-0 shadow-sm rounded-4 bg-white">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center gap-4">
                                <div class="avatar-text avatar-lg bg-soft-danger text-danger rounded-4 shadow-sm">
                                    <i class="feather-alert-circle fs-4"></i>
                                </div>
                                <div>
                                    <div class="fs-4 fw-bold text-dark mb-0">₹{{ number_format($stats['lab_dues'], 2) }}</div>
                                    <h3 class="fs-13 fw-semibold text-muted text-uppercase ls-1 mb-0">Total Lab Dues</h3>
                                </div>
                            </div>
                            <div class="mt-4 pt-2 border-top border-light d-flex justify-content-between align-items-center">
                                <span class="fs-11 text-muted">Amount to pay lab</span>
                                <span class="badge bg-soft-danger text-danger rounded-pill px-2 fw-bold">Dues</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-md-6">
                    <div class="card stretch stretch-full border-0 shadow-sm rounded-4 bg-white border border-warning border-opacity-25">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center gap-4">
                                <div class="avatar-text avatar-lg bg-soft-warning text-warning rounded-4 shadow-sm">
                                    <i class="feather-clock fs-4"></i>
                                </div>
                                <div>
                                    <div class="fs-4 fw-bold text-dark mb-0">₹{{ number_format($stats['pending_balance'], 2) }}</div>
                                    <h3 class="fs-13 fw-semibold text-muted text-uppercase ls-1 mb-0">Unsettled Balance</h3>
                                </div>
                            </div>
                            <div class="mt-4 pt-2 border-top border-light d-flex justify-content-between align-items-center">
                                <span class="fs-11 text-muted">Pending Settlement</span>
                                <a href="{{ route('partner.settlements') }}" wire:navigate class="btn btn-xs btn-warning rounded-pill px-2 py-0 fs-9 fw-bold mt-1">Pay Now</a>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                {{-- Original Stats for Doctor/Agent --}}
                <div class="col-xxl-3 col-md-6">
                    <div class="card stretch stretch-full border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center gap-4">
                                <div class="avatar-text avatar-lg bg-soft-primary text-primary rounded-4 shadow-sm">
                                    <i class="feather-trending-up fs-4"></i>
                                </div>
                                <div>
                                    <div class="fs-4 fw-bold text-dark mb-0">₹{{ number_format($stats['total_earnings'], 2) }}</div>
                                    <h3 class="fs-13 fw-semibold text-muted text-uppercase ls-1 mb-0">Lifetime Earnings</h3>
                                </div>
                            </div>
                            <div class="mt-4 pt-2 border-top border-light d-flex justify-content-between align-items-center">
                                <span class="fs-11 text-muted">Range Earnings</span>
                                <span class="badge bg-soft-primary text-primary rounded-pill px-2 fw-bold">₹{{ number_format($stats['this_month_earnings'], 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-md-6">
                    <div class="card stretch stretch-full border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center gap-4">
                                <div class="avatar-text avatar-lg bg-soft-success text-success rounded-4 shadow-sm">
                                    <i class="feather-check-circle fs-4"></i>
                                </div>
                                <div>
                                    <div class="fs-4 fw-bold text-dark mb-0">₹{{ number_format($stats['settled_amount'], 2) }}</div>
                                    <h3 class="fs-13 fw-semibold text-muted text-uppercase ls-1 mb-0">Received Payouts</h3>
                                </div>
                            </div>
                            <div class="mt-4 pt-2 border-top border-light">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fs-11 text-muted">Settlement Ratio</span>
                                    <span class="fs-11 fw-bold text-success">{{ $stats['total_earnings'] > 0 ? round($stats['settled_amount'] / $stats['total_earnings'] * 100) : 0 }}%</span>
                                </div>
                                <div class="progress" style="height: 4px;">
                                    <div class="progress-bar bg-success" style="width: {{ $stats['total_earnings'] > 0 ? ($stats['settled_amount'] / $stats['total_earnings'] * 100) : 0 }}%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-md-6">
                    <div class="card stretch stretch-full border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center gap-4">
                                <div class="avatar-text avatar-lg bg-soft-warning text-warning rounded-4 shadow-sm">
                                    <i class="feather-clock fs-4"></i>
                                </div>
                                <div>
                                    <div class="fs-4 fw-bold text-dark mb-0">₹{{ number_format($stats['pending_balance'], 2) }}</div>
                                    <h3 class="fs-13 fw-semibold text-muted text-uppercase ls-1 mb-0">Unsettled Balance</h3>
                                </div>
                            </div>
                            <div class="mt-4 pt-2 border-top border-light d-flex justify-content-between align-items-center">
                                <span class="fs-11 text-muted">Pending Payout</span>
                                <i class="feather-info fs-12 text-warning" title="Amount awaiting settlement"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-md-6">
                    <div class="card stretch stretch-full border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center gap-4">
                                <div class="avatar-text avatar-lg bg-soft-info text-info rounded-4 shadow-sm">
                                    <i class="feather-users fs-4"></i>
                                </div>
                                <div>
                                    <div class="fs-4 fw-bold text-dark mb-0">{{ number_format($stats['total_invoices']) }}</div>
                                    <h3 class="fs-13 fw-semibold text-muted text-uppercase ls-1 mb-0">Total Referrals</h3>
                                </div>
                            </div>
                            <div class="mt-4 pt-2 border-top border-light d-flex justify-content-between align-items-center">
                                <span class="fs-11 text-muted">In Filter Range</span>
                                <span class="badge bg-soft-info text-info rounded-pill px-2 fw-bold">Active</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Analytics Chart --}}
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-white border-bottom-0 py-4 px-4 d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1 fw-bold text-dark">Earnings Overview</h5>
                            <p class="text-muted small mb-0">Daily earnings distribution for the selected period</p>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light border rounded-pill px-3 fs-11 fw-bold" type="button">
                                <i class="feather-download me-1"></i> Export Data
                            </button>
                        </div>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div id="earningsChart" style="height: 350px;" 
                            wire:ignore
                            x-data="{
                                chart: null,
                                labels: @js($chartData['labels']),
                                data: @js($chartData['data']),
                                init() {
                                    const options = {
                                        series: [{
                                            name: 'Earnings',
                                            data: this.data
                                        }],
                                        chart: {
                                            type: 'area',
                                            height: 350,
                                            toolbar: { show: false },
                                            zoom: { enabled: false },
                                            fontFamily: 'inherit'
                                        },
                                        dataLabels: { enabled: false },
                                        stroke: { curve: 'smooth', width: 3, colors: ['#3b71ca'] },
                                        fill: {
                                            type: 'gradient',
                                            gradient: {
                                                shadeIntensity: 1,
                                                opacityFrom: 0.45,
                                                opacityTo: 0.05,
                                                stops: [20, 100]
                                            }
                                        },
                                        colors: ['#3b71ca'],
                                        xaxis: {
                                            categories: this.labels,
                                            axisBorder: { show: false },
                                            axisTicks: { show: false },
                                            labels: { style: { colors: '#94a3b8', fontSize: '11px' } }
                                        },
                                        yaxis: {
                                            labels: { 
                                                style: { colors: '#94a3b8', fontSize: '11px' },
                                                formatter: (val) => '₹' + val.toLocaleString()
                                            }
                                        },
                                        grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
                                        tooltip: {
                                            theme: 'light',
                                            x: { show: false },
                                            y: { formatter: (val) => '₹' + val.toLocaleString() }
                                        }
                                    };
                                    this.chart = new ApexCharts(document.querySelector('#earningsChart'), options);
                                    this.chart.render();

                                    Livewire.on('chartDataUpdated', (chartData) => {
                                        this.chart.updateSeries([{ data: chartData.data }]);
                                        this.chart.updateOptions({ xaxis: { categories: chartData.labels } });
                                    });
                                }
                            }">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Lists Section --}}
            <div class="col-lg-8">
                {{-- Recent Invoices --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center px-4">
                        <h6 class="mb-0 fw-bold text-dark"><i class="feather-file-text me-2 text-primary"></i>Recent Invoices</h6>
                        <a href="{{ route('partner.invoices') }}" wire:navigate class="btn btn-sm btn-soft-primary px-3 rounded-pill fw-bold fs-11">View All</a>
                    </div>
                    <div class="table-responsive" style="min-height: 200px;">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-soft-light-gray fs-11 fw-bold text-uppercase text-muted border-bottom">
                                <tr class="fs-11 text-uppercase text-muted fw-bold">
                                    <th class="ps-4 py-3 border-0">Invoice #</th>
                                    <th class="py-3 border-0">Patient</th>
                                    <th class="py-3 border-0">Date</th>
                                    <th class="py-3 text-end pe-4 border-0">Earnings (₹)</th>
                                </tr>
                            </thead>
                            <tbody class="fs-13">
                                @forelse($recentInvoices as $inv)
                                    <tr class="border-bottom-light">
                                        <td class="ps-4">
                                            <div class="fw-bold text-dark fs-14">{{ $inv->invoice_number }}</div>
                                            <span class="badge {{ $inv->payment_status == 'Paid' ? 'bg-soft-success text-success' : 'bg-soft-warning text-warning' }} fs-10 rounded-pill px-2">
                                                {{ $inv->payment_status }}
                                            </span>
                                        </td>
                                        <td class="text-dark fw-medium">{{ $inv->patient->name ?? 'N/A' }}</td>
                                        <td class="text-muted font-medium">{{ $inv->invoice_date->format('d M, Y') }}</td>
                                        <td class="text-end pe-4 fw-bold text-primary">
                                            @php
                                                $amt = 0;
                                                if($role === 'Doctor') $amt = $inv->doctor_commission_amount;
                                                elseif($role === 'Agent') $amt = $inv->agent_commission_amount;
                                                elseif($role === 'Collection Center') $amt = $inv->cc_profit_amount;
                                                else $amt = $inv->total_amount;
                                            @endphp
                                            ₹{{ number_format($amt, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5">
                                            <div class="text-muted mb-3"><i class="feather-file-minus" style="font-size: 3rem; opacity: 0.15;"></i></div>
                                            <p class="text-muted mb-0 fs-13">No recent activity recorded.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                {{-- Recent Settlements --}}
                <div class="card stretch stretch-full border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center px-4">
                        <h5 class="card-title mb-0 fw-bold text-dark"><i class="feather-credit-card me-2 text-success"></i>Settlements</h5>
                        <a href="{{ route('partner.settlements') }}" wire:navigate class="fs-11 fw-bold text-primary text-decoration-none">History <i class="feather-chevron-right fs-10"></i></a>
                    </div>
                    <div class="list-group list-group-flush">
                        @forelse($recentSettlements as $s)
                            <div class="list-group-item p-4 border-0 border-bottom border-light">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-muted fs-11 fw-bold">{{ $s->payment_date->format('d M, Y') }}</span>
                                    <span class="fw-bold text-success fs-15">₹{{ number_format($s->amount, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <div class="fs-12 text-dark fw-medium">
                                        <span class="badge bg-soft-success text-success rounded-circle p-1 me-1"><i class="feather-check fs-10"></i></span>Settled
                                    </div>
                                    <div class="fs-11 text-muted fw-bold text-uppercase ls-1">{{ $s->payment_mode }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="p-5 text-center">
                                <i class="feather-info text-muted fs-3 mb-2"></i>
                                <p class="text-muted small mb-0">No payouts received yet.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Contact Support --}}
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white border border shadow-sm">
                    <div class="card-body p-4 text-center">
                        <div class="avatar-text avatar-lg bg-soft-primary text-primary rounded-circle mx-auto mb-3 shadow-sm">
                            <i class="feather-help-circle fs-3 font-bold"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-2">Technical Support</h6>
                        <p class="text-muted small mb-4 font-medium px-2">Need help with reports or billing? Our support team is available 24/7.</p>
                        <a href="https://wa.me/917307000216" target="_blank" class="btn btn-primary w-100 rounded-pill fw-bold fs-12 py-2 shadow-sm d-flex align-items-center justify-content-center gap-2">
                            <i class="feather-message-circle fs-6"></i> Chat with Us
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .font-medium { font-weight: 500; }
        .border-bottom-light { border-bottom: 1px solid #f8fafc; }
        .bg-soft-light-gray { background-color: #f8f9fa !important; }
        .bg-soft-primary { background-color: rgba(59, 113, 202, 0.08) !important; }
        .bg-soft-success { background-color: rgba(16, 185, 129, 0.08) !important; }
        .bg-soft-warning { background-color: rgba(245, 158, 11, 0.08) !important; }
        .bg-soft-info { background-color: rgba(6, 182, 212, 0.08) !important; }
        .text-primary { color: #3b71ca !important; }
        .text-success { color: #10b981 !important; }
        .text-warning { color: #f59e0b !important; }
        .text-info { color: #06b6d4 !important; }
        
        .avatar-text.avatar-lg { width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
        .stretch { height: calc(100% - 30px); }
        .stretch-full { height: 100%; }
        
        input[type="date"]::-webkit-calendar-picker-indicator {
            cursor: pointer;
            filter: invert(0.4) sepia(0) saturate(0) hue-rotate(0deg);
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</div>
