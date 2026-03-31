<div>
    <style>
        :root {
            /* Light Theme Palette - Professional & Refined */
            --db-primary: #4361ee;
            --db-success: #22c55e;
            --db-warning: #f59e0b;
            --db-danger: #ef4444;
            --db-info: #3b82f6;
            --db-bg: #f8fafc;
            --db-card-bg: #ffffff;
            --db-text-main: #1e293b;
            --db-text-muted: #64748b;
            --db-border: #e2e8f0;
            --db-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            --db-glass-bg: rgba(255, 255, 255, 0.7);
            --db-glass-border: rgba(255, 255, 255, 0.4);
            --db-grad-primary: linear-gradient(135deg, #4361ee 0%, #7209b7 100%);
        }

        /* Dark Theme Overrides - Premium Night Mode */
        html.app-skin-dark {
            --db-bg: #0d0d1a;
            --db-card-bg: #1a1a2e;
            --db-text-main: #e2e8f0;
            --db-text-muted: #94a3b8;
            --db-border: rgba(255, 255, 255, 0.08);
            --db-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.4);
            --db-glass-bg: rgba(26, 26, 46, 0.8);
            --db-glass-border: rgba(255, 255, 255, 0.05);
        }

        body {
            background-color: var(--db-bg) !important;
            color: var(--db-text-main);
            transition: all 0.4s ease;
        }

        .db-container {
            padding: 2rem 1.5rem;
            max-width: 1600px;
            margin: 0 auto;
        }

        /* Stat Cards */
        .db-card {
            background: var(--db-card-bg);
            border: 1px solid var(--db-border);
            border-radius: 1.5rem;
            padding: 1.75rem;
            box-shadow: var(--db-shadow);
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
        }

        .db-card:hover {
            transform: translateY(-8px);
            border-color: var(--db-primary);
            box-shadow: 0 20px 40px -10px rgba(67, 97, 238, 0.15);
        }

        .db-card::after {
            content: "";
            position: absolute;
            top: 0; right: 0;
            width: 100px; height: 100px;
            background: radial-gradient(circle, var(--db-primary) 0%, transparent 70%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .db-card:hover::after {
            opacity: 0.05;
        }

        .db-card-link {
            text-decoration: none;
            color: inherit;
        }

        .icon-box {
            width: 54px;
            height: 54px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.25rem;
            font-size: 1.4rem;
            transition: all 0.4s ease;
        }

        .db-card:hover .icon-box {
            transform: scale(1.1) rotate(10deg);
            filter: brightness(1.1);
        }

        .val-display {
            font-size: 2.2rem;
            font-weight: 800;
            letter-spacing: -1px;
            margin-bottom: 0.25rem;
            line-height: 1;
        }

        .val-label {
            color: var(--db-text-muted);
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Chart Sections */
        .chart-card {
            background: var(--db-card-bg);
            border: 1px solid var(--db-border);
            border-radius: 1.75rem;
            padding: 2rem;
            box-shadow: var(--db-shadow);
            height: 100%;
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .chart-title {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--db-text-main);
            margin: 0;
            letter-spacing: -0.5px;
        }

        /* Ranking Lists */
        .rank-list {
            margin-top: 1.5rem;
        }

        .rank-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.1rem 0.75rem;
            border-radius: 12px;
            transition: all 0.3s;
            margin-bottom: 0.5rem;
            background: transparent;
        }

        .rank-row:hover {
            background: rgba(var(--bs-primary-rgb), 0.05);
            transform: scale(1.02);
        }

        .rank-name {
            font-weight: 750;
            font-size: 0.95rem;
            color: var(--db-text-main);
            margin-bottom: 2px;
            display: block;
        }

        .rank-sub {
            font-size: 0.75rem;
            color: var(--db-text-muted);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .rank-val {
            font-weight: 850;
            color: var(--db-primary);
            font-size: 1rem;
            letter-spacing: -0.2px;
            white-space: nowrap;
        }

        .rank-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .rank-row:hover .rank-icon {
            transform: scale(1.1);
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }

        /* Filter Presets */
        .preset-btn {
            border: 1px solid var(--db-border);
            background: var(--db-card-bg);
            color: var(--db-text-muted);
            padding: 0.5rem 1.25rem;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.9rem;
            transition: all 0.3s;
        }

        .preset-btn:hover, .preset-btn.active {
            background: var(--db-primary);
            color: white;
            border-color: var(--db-primary);
            box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
            transform: translateY(-1px);
        }

        .see-all {
            font-size: 0.75rem;
            font-weight: 800;
            color: var(--db-primary) !important;
            text-decoration: none;
            padding: 5px 12px;
            border-radius: 8px;
            background: rgba(var(--bs-primary-rgb), 0.08);
            border: 1px solid rgba(var(--bs-primary-rgb), 0.05);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .see-all:hover {
            background: var(--db-primary);
            color: white !important;
            box-shadow: 0 5px 12px rgba(var(--bs-primary-rgb), 0.25);
            transform: translateY(-2px);
        }

        /* Glass Summary Bar - Enhanced */
        .glass-bar {
            background: var(--db-glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--db-glass-border);
            border-radius: 2rem;
            padding: 2rem 2.5rem;
            margin-bottom: 3rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 2rem;
            box-shadow: var(--db-shadow);
        }

        .glass-item {
            display: flex;
            align-items: center;
            gap: 1.25rem;
        }

        .glass-val {
            font-size: 1.6rem;
            font-weight: 850;
            letter-spacing: -0.5px;
            color: var(--db-text-main);
        }

        .glass-lbl {
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            color: var(--db-text-muted);
            letter-spacing: 1px;
        }

        /* Badges */
        .badge-premium {
            padding: 0.5rem 1rem;
            border-radius: 100px;
            font-weight: 750;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Personnel Cards */
        .personnel-card {
            padding: 1.25rem;
            border-radius: 1.25rem;
            transition: all 0.3s;
            border: 1px solid transparent;
        }

        .personnel-card:hover {
            background-color: var(--db-card-bg) !important;
            border-color: var(--db-border);
            box-shadow: var(--db-shadow);
            transform: translateX(5px);
        }

        /* Responsive Fixes */
        @media (max-width: 992px) {
            .glass-bar { justify-content: center; text-align: center; }
        }
    </style>

    <div class="db-container">
        
        {{-- Header & Filters --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-4">
            <div>
                <h2 class="fw-black mb-1" style="letter-spacing: -1.5px; font-size: 2.25rem;">Executive Insights</h2>
                <div class="d-flex align-items-center gap-3">
                    <span class="badge-premium bg-soft-primary text-primary shadow-none border-0">
                        <span class="pulse-dot me-2"></span>Live Analytics
                    </span>
                    <span class="text-muted small fw-bold">
                        <i class="feather-calendar me-1"></i>
                        {{ Carbon\Carbon::parse($fromDate)->format('M d') }} - {{ Carbon\Carbon::parse($toDate)->format('M d, Y') }}
                    </span>
                </div>
            </div>

            <div class="d-flex flex-column flex-sm-row gap-3">
                <div class="d-flex gap-2">
                    <button wire:click="setPreset('today')" class="preset-btn {{ $fromDate == date('Y-m-d') ? 'active' : '' }}">Today</button>
                    <button wire:click="setPreset('this_week')" class="preset-btn">Week</button>
                    <button wire:click="setPreset('this_month')" class="preset-btn">Month</button>
                </div>
                <div class="input-group">
                    <input type="date" wire:model="fromDate" class="form-control border-0 shadow-sm" style="border-radius: 12px 0 0 12px;">
                    <input type="date" wire:model="toDate" class="form-control border-0 shadow-sm" style="border-radius: 0;">
                    <button wire:click="updateFilter" class="btn btn-primary px-3 shadow-sm" style="border-radius: 0 12px 12px 0;">
                        <i class="feather-refresh-cw"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- Top Summary Bar --}}
        <div class="glass-bar shadow-sm">
            <div class="glass-item">
                <div class="icon-box m-0 bg-soft-primary text-primary"><i class="feather-trending-up"></i></div>
                <div>
                    <div class="glass-val">₹{{ number_format($financials->revenue ?? 0, 0) }}</div>
                    <div class="glass-lbl">Revenue</div>
                </div>
            </div>
            <div class="vr d-none d-lg-block"></div>
            <div class="glass-item">
                <div class="icon-box m-0 bg-soft-success text-success"><i class="feather-dollar-sign"></i></div>
                <div>
                    <div class="glass-val">₹{{ number_format($financials->profit ?? 0, 0) }}</div>
                    <div class="glass-lbl">Est. Profit</div>
                </div>
            </div>
            <div class="vr d-none d-lg-block"></div>
            <div class="glass-item">
                <div class="icon-box m-0 bg-soft-info text-info"><i class="feather-credit-card"></i></div>
                <div>
                    <div class="glass-val">₹{{ number_format($financials->collections ?? 0, 0) }}</div>
                    <div class="glass-lbl">Collections</div>
                </div>
            </div>
            <div class="vr d-none d-lg-block"></div>
            <div class="glass-item">
                <div class="icon-box m-0 bg-soft-danger text-danger"><i class="feather-alert-octagon"></i></div>
                <div>
                    <div class="glass-val">₹{{ number_format($financials->dues ?? 0, 0) }}</div>
                    <div class="glass-lbl">Outstandings</div>
                </div>
            </div>
        </div>

        {{-- Main Stats Grid --}}
        <div class="row g-4 mb-5">
            <div class="col-xl-3 col-md-6">
                <a href="{{ route('lab.patients') }}" wire:navigate class="db-card-link">
                    <div class="db-card">
                        <div class="icon-box bg-soft-primary text-primary"><i class="feather-users"></i></div>
                        <div>
                            <div class="val-display">{{ number_format($stats['total_patients']) }}</div>
                            <div class="val-label">Registered Patients</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xl-3 col-md-6">
                <a href="{{ route('lab.invoices') }}?status=Pending" wire:navigate class="db-card-link">
                    <div class="db-card">
                        <div class="icon-box bg-soft-warning text-warning"><i class="feather-clock"></i></div>
                        <div>
                            <div class="val-display">{{ number_format($ops['pending_tests']) }}</div>
                            <div class="val-label">Pending Reports</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xl-3 col-md-6">
                <a href="{{ route('lab.invoices') }}?status=Completed" wire:navigate class="db-card-link">
                    <div class="db-card">
                        <div class="icon-box bg-soft-success text-success"><i class="feather-check-circle"></i></div>
                        <div>
                            <div class="val-display">{{ number_format($ops['completed_tests']) }}</div>
                            <div class="val-label">Completed Tests</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xl-3 col-md-6">
                <a href="{{ route('lab.invoices') }}?collection_type=Home" wire:navigate class="db-card-link">
                    <div class="db-card">
                        <div class="icon-box bg-soft-danger text-danger"><i class="feather-map-pin"></i></div>
                        <div>
                            <div class="val-display">{{ number_format($ops['home_visits']) }}</div>
                            <div class="val-label">Home Collections</div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        {{-- Charts Grid 1 --}}
        <div class="row g-4 mb-5">
            <div class="col-lg-8">
                <div class="chart-card">
                    <div class="chart-header">
                        <h5 class="chart-title">Revenue & Profit Analytics</h5>
                        <div class="d-flex gap-3">
                            <span class="small fw-bold text-muted"><i class="feather-circle text-primary me-1"></i> Revenue</span>
                            <span class="small fw-bold text-muted"><i class="feather-circle text-success me-1"></i> Profit</span>
                        </div>
                    </div>
                    <div style="height: 380px;" wire:ignore wire:key="main-finance-container">
                        <canvas id="mainFinanceChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="chart-card">
                    <div class="chart-header">
                        <h5 class="chart-title">Department Share</h5>
                    </div>
                    <div style="height: 300px;" wire:ignore wire:key="dept-dist-container">
                        <canvas id="deptDistributionChart"></canvas>
                    </div>
                    <div class="mt-4">
                         @foreach(array_slice($deptLabels, 0, 3) as $index => $label)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small fw-bold text-muted">{{ $label }}</span>
                                <span class="badge bg-soft-primary text-primary px-2 rounded-pill">{{ $deptCounts[$index] }}</span>
                            </div>
                         @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Charts Grid 2 & Rankings --}}
        <div class="row g-4 mb-5">
            <div class="col-xl-4 col-md-6">
                <div class="chart-card">
                    <div class="chart-header">
                        <h5 class="chart-title">Payment Methods</h5>
                    </div>
                    <div style="height: 250px;" wire:ignore wire:key="payment-mode-container">
                        <canvas id="paymentModeChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-6">
                <div class="chart-card">
                    <div class="chart-header">
                        <h5 class="chart-title">Collection Channels</h5>
                    </div>
                    <div style="height: 250px;" wire:ignore wire:key="channel-container">
                        <canvas id="channelChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-12">
                <div class="chart-card">
                    <div class="chart-header">
                        <h5 class="chart-title">Lab Personnel</h5>
                        <i class="feather-shield text-muted opacity-50"></i>
                    </div>
                    <div class="d-flex flex-column gap-3 mt-2">
                        <div class="personnel-card bg-soft-primary d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-3">
                                <div class="icon-box m-0 bg-white shadow-sm text-primary" style="width: 40px; height: 40px;">
                                    <i class="feather-shield fs-5"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">Lab Administrators</div>
                                    <div class="small text-muted">Full Access Control</div>
                                </div>
                            </div>
                            <span class="badge bg-primary fs-6 px-3 rounded-pill">{{ $staffActivity['active_admins'] }}</span>
                        </div>
                        <div class="personnel-card bg-soft-success d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-3">
                                <div class="icon-box m-0 bg-white shadow-sm text-success" style="width: 40px; height: 40px;">
                                    <i class="feather-user fs-5"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">Staff Members</div>
                                    <div class="small text-muted">Operational Duties</div>
                                </div>
                            </div>
                            <span class="badge bg-success fs-6 px-3 rounded-pill">{{ $staffActivity['active_staff'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Top Performing Entity Grid --}}
        <div class="row g-4">
            {{-- Packages --}}
            <div class="col-xl-3 col-md-6">
                <div class="chart-card">
                    <div class="chart-header">
                        <h5 class="chart-title">Top Packages</h5>
                        <a href="{{ route('lab.packages') }}" wire:navigate class="see-all">See All</a>
                    </div>
                    <div class="rank-list">
                        @forelse($topPackages as $pkg)
                            <div class="rank-row">
                                <div class="d-flex align-items-center gap-3 overflow-hidden">
                                    <div class="rank-icon bg-soft-primary text-primary">
                                        <i class="feather-package"></i>
                                    </div>
                                    <div class="overflow-hidden">
                                        <div class="rank-name text-truncate" title="{{ $pkg->test_name }}">{{ $pkg->test_name }}</div>
                                        <div class="d-flex align-items-center gap-2 mt-1">
                                            <div class="fw-bold" style="font-size: 0.85rem; color: var(--db-primary);">₹{{ number_format($pkg->total_income, 0) }}</div>
                                            <span class="text-muted" style="font-size: 0.7rem;">•</span>
                                            <div class="rank-sub" style="margin-bottom: 0;">{{ $pkg->total_sold }} Sales</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <div class="icon-box mx-auto bg-light text-muted opacity-50" style="width: 50px; height: 50px;">
                                    <i class="feather-box"></i>
                                </div>
                                <p class="text-muted small fw-bold mt-2">No package analytics</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Doctors --}}
            <div class="col-xl-3 col-md-6">
                <div class="chart-card">
                    <div class="chart-header">
                        <h5 class="chart-title">Top Doctors</h5>
                        <a href="{{ route('lab.doctors') }}" wire:navigate class="see-all">See All</a>
                    </div>
                    <div class="rank-list">
                        @forelse($topDoctors as $doc)
                            <div class="rank-row">
                                <div class="d-flex align-items-center gap-3 overflow-hidden">
                                    <div class="rank-icon bg-soft-success text-success">
                                        <i class="feather-user-plus"></i>
                                    </div>
                                    <div class="overflow-hidden">
                                        <div class="rank-name text-truncate" title="{{ $doc->doctor->name ?? 'Doctor' }}">{{ $doc->doctor->name ?? 'Doctor' }}</div>
                                        <div class="d-flex align-items-center gap-2 mt-1">
                                            <div class="fw-bold text-success" style="font-size: 0.85rem;">₹{{ number_format($doc->total_income, 0) }}</div>
                                            <span class="text-muted" style="font-size: 0.7rem;">•</span>
                                            <div class="rank-sub" style="margin-bottom: 0;">Performance</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <div class="icon-box mx-auto bg-light text-muted opacity-50" style="width: 50px; height: 50px;">
                                    <i class="feather-user-x"></i>
                                </div>
                                <p class="text-muted small fw-bold mt-2">No referral data</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Collection Centers --}}
            <div class="col-xl-3 col-md-6">
                <div class="chart-card">
                    <div class="chart-header">
                        <h5 class="chart-title">Best Centers</h5>
                        <a href="{{ route('lab.collection.centers') }}" wire:navigate class="see-all">See All</a>
                    </div>
                    <div class="rank-list">
                        @forelse($topCCs as $cc)
                            <div class="rank-row">
                                <div class="d-flex align-items-center gap-3 overflow-hidden">
                                    <div class="rank-icon bg-soft-info text-info">
                                        <i class="feather-map-pin"></i>
                                    </div>
                                    <div class="overflow-hidden">
                                        <div class="rank-name text-truncate" title="{{ $cc->collectionCenter->name ?? 'In-House' }}">{{ $cc->collectionCenter->name ?? 'In-House' }}</div>
                                        <div class="d-flex align-items-center gap-2 mt-1">
                                            <div class="fw-bold text-info" style="font-size: 0.85rem;">₹{{ number_format($cc->total_income, 0) }}</div>
                                            <span class="text-muted" style="font-size: 0.7rem;">•</span>
                                            <div class="rank-sub" style="margin-bottom: 0;">{{ $cc->total_bills }} Invoices</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
@empty
                            <div class="text-center py-5">
                                <div class="icon-box mx-auto bg-light text-muted opacity-50" style="width: 50px; height: 50px;">
                                    <i class="feather-map"></i>
                                </div>
                                <p class="text-muted small fw-bold mt-2">No center data</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Lab Tests --}}
            <div class="col-xl-3 col-md-6">
                <div class="chart-card">
                    <div class="chart-header">
                        <h5 class="chart-title">High Yield Tests</h5>
                        <a href="{{ route('lab.tests') }}" wire:navigate class="see-all">See All</a>
                    </div>
                    <div class="rank-list">
                        @forelse($topTests as $test)
                            <div class="rank-row">
                                <div class="d-flex align-items-center gap-3 overflow-hidden">
                                    <div class="rank-icon bg-soft-danger text-danger">
                                        <i class="feather-activity"></i>
                                    </div>
                                    <div class="overflow-hidden">
                                        <div class="rank-name text-truncate" title="{{ $test->test_name }}">{{ $test->test_name }}</div>
                                        <div class="d-flex align-items-center gap-2 mt-1">
                                            <div class="fw-bold text-danger" style="font-size: 0.85rem;">₹{{ number_format($test->total_income, 0) }}</div>
                                            <span class="text-muted" style="font-size: 0.7rem;">•</span>
                                            <div class="rank-sub" style="margin-bottom: 0;">{{ $test->total_sold }} Sold</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <div class="icon-box mx-auto bg-light text-muted opacity-50" style="width: 50px; height: 50px;">
                                    <i class="feather-activity"></i>
                                </div>
                                <p class="text-muted small fw-bold mt-2">No test analytics</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Analysis Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        (function() {
            // Self-invoking robust initializer
            window.initAllCharts = function() {
                if (typeof Chart === 'undefined') {
                    console.log('Chart.js not loaded, retrying...');
                    setTimeout(window.initAllCharts, 200);
                    return;
                }

                const root = document.documentElement;
                const style = getComputedStyle(root);
                
                // Safe color lookup with fallbacks
                const primaryColor = style.getPropertyValue('--db-primary').trim() || '#4361ee';
                const successColor = style.getPropertyValue('--db-success').trim() || '#22c55e';
                const textColor = style.getPropertyValue('--db-text-main').trim() || '#1e293b';
                const mutedColor = style.getPropertyValue('--db-text-muted').trim() || '#64748b';
                const isDark = root.classList.contains('app-skin-dark');
                const borderColor = isDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.05)';

                // Helper to safe-destroy existing charts
                const destroyIfExists = (id) => {
                    const chart = Chart.getChart(id);
                    if (chart) chart.destroy();
                };

                // Global Defaults
                Chart.defaults.color = mutedColor;
                Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
                Chart.defaults.font.weight = '600';

                // 1. Line Chart: Revenue vs Profit
                const mainCtx = document.getElementById('mainFinanceChart');
                if (mainCtx) {
                    destroyIfExists('mainFinanceChart');
                    new Chart(mainCtx, {
                        type: 'line',
                        data: {
                            labels: @json($chartLabels),
                            datasets: [
                                {
                                    label: 'Revenue',
                                    data: @json($revenueValues),
                                    borderColor: primaryColor,
                                    backgroundColor: isDark ? 'rgba(67, 97, 238, 0.1)' : 'rgba(67, 97, 238, 0.05)',
                                    fill: true, tension: 0.4, borderWidth: 4, pointRadius: 4, pointHoverRadius: 6, pointBackgroundColor: '#fff'
                                },
                                {
                                    label: 'Profit',
                                    data: @json($profitValues),
                                    borderColor: successColor,
                                    backgroundColor: 'transparent',
                                    fill: false, tension: 0.4, borderWidth: 3, borderDash: [5, 5], pointRadius: 0
                                }
                            ]
                        },
                        options: {
                            responsive: true, maintainAspectRatio: false,
                            interaction: { intersect: false, mode: 'index' },
                            plugins: { 
                                legend: { display: false }, 
                                tooltip: { 
                                    padding: 12, cornerRadius: 12, 
                                    backgroundColor: isDark ? '#1a1a2e' : '#fff', 
                                    titleColor: isDark ? '#fff' : '#1e293b', 
                                    bodyColor: mutedColor, 
                                    borderColor: borderColor, borderWidth: 1,
                                    usePointStyle: true
                                } 
                            },
                            scales: {
                                y: { 
                                    grid: { color: borderColor, drawBorder: false },
                                    ticks: { callback: (v) => '₹' + v.toLocaleString(), padding: 10 } 
                                },
                                x: { grid: { display: false }, ticks: { padding: 10 } }
                            }
                        }
                    });
                }

                // 2. Donut: Dept Distribution
                const deptCtx = document.getElementById('deptDistributionChart');
                if (deptCtx) {
                    destroyIfExists('deptDistributionChart');
                    new Chart(deptCtx, {
                        type: 'doughnut',
                        data: {
                            labels: @json($deptLabels),
                            datasets: [{
                                data: @json($deptCounts),
                                backgroundColor: ['#4361ee', '#7209b7', '#22c55e', '#f59e0b', '#ef4444', '#3b82f6'],
                                borderWidth: isDark ? 4 : 2,
                                borderColor: isDark ? '#1a1a2e' : '#fff',
                                hoverOffset: 15
                            }]
                        },
                        options: {
                            responsive: true, maintainAspectRatio: false,
                            cutout: '75%',
                            plugins: { 
                                legend: { 
                                    position: 'bottom', 
                                    labels: { boxWidth: 8, usePointStyle: true, padding: 20, font: { size: 11 } } 
                                } 
                            }
                        }
                    });
                }

                // 3. Donut: Payment Modes
                const payCtx = document.getElementById('paymentModeChart');
                if (payCtx) {
                    destroyIfExists('paymentModeChart');
                    new Chart(payCtx, {
                        type: 'doughnut',
                        data: {
                            labels: @json($payLabels),
                            datasets: [{
                                data: @json($payValues),
                                backgroundColor: ['#4361ee', '#3b82f6', '#8b5cf6', '#ec4899', '#f97316'],
                                borderWidth: isDark ? 4 : 2,
                                borderColor: isDark ? '#1a1a2e' : '#fff',
                            }]
                        },
                        options: {
                            responsive: true, maintainAspectRatio: false,
                            cutout: '70%',
                            plugins: { 
                                legend: { 
                                    position: 'right', 
                                    labels: { boxWidth: 8, usePointStyle: true, font: { size: 11 } } 
                                } 
                            }
                        }
                    });
                }

                // 4. Bar: Channel Comparison
                const channelCtx = document.getElementById('channelChart');
                if (channelCtx) {
                    destroyIfExists('channelChart');
                    new Chart(channelCtx, {
                        type: 'bar',
                        data: {
                            labels: @json($channelLabels),
                            datasets: [{
                                label: 'Invoices',
                                data: @json($channelValues),
                                backgroundColor: primaryColor,
                                borderRadius: 12, barThickness: 20
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true, maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: {
                                x: { grid: { color: borderColor }, ticks: { stepSize: 1 } },
                                y: { grid: { display: false } }
                            }
                        }
                    });
                }
            };

            // Event Listeners for Livewire and direct load
            document.addEventListener('livewire:navigated', () => { setTimeout(window.initAllCharts, 100); });
            window.addEventListener('refreshCharts', () => { setTimeout(window.initAllCharts, 100); });
            
            if (document.readyState === 'complete') { 
                window.initAllCharts(); 
            } else { 
                window.addEventListener('load', window.initAllCharts); 
            }
        })();
    </script>
</div>