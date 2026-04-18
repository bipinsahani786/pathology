<div>
    <style>
        :root {
            --db-primary: #4f46e5;
            --db-success: #10b981;
            --db-warning: #f59e0b;
            --db-danger: #ef4444;
            --db-info: #3b82f6;
            --db-bg: #f8fafc;
            --db-card-bg: #ffffff;
            --db-text-main: #1e293b;
            --db-text-muted: #64748b;
            --db-border: #e2e8f0;
            --db-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            --db-glass-bg: rgba(255, 255, 255, 0.7);
            --db-glass-border: rgba(255, 255, 255, 0.4);
        }

        html.app-skin-dark {
            --db-bg: #0d0d1a;
            --db-card-bg: #1a1a2e;
            --db-text-main: #e2e8f0;
            --db-text-muted: #94a3b8;
            --db-border: rgba(255, 255, 255, 0.08);
            --db-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.4);
        }

        .db-container {
            padding: 1.5rem;
            max-width: 1600px;
            margin: 0 auto;
        }

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
            box-shadow: 0 20px 40px -10px rgba(79, 70, 229, 0.15);
        }

        .glass-bar {
            background: var(--db-glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--db-glass-border);
            border-radius: 2rem;
            padding: 1.5rem 2rem;
            margin-bottom: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1.5rem;
            box-shadow: var(--db-shadow);
        }

        .glass-item {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .glass-val {
            font-size: 1.5rem;
            font-weight: 850;
            letter-spacing: -0.5px;
            color: var(--db-text-main);
            line-height: 1;
        }

        .glass-lbl {
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            color: var(--db-text-muted);
            letter-spacing: 1px;
            margin-top: 4px;
        }

        .icon-box-sm {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .wellness-banner {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            border-radius: 2rem;
            padding: 3rem;
            position: relative;
            overflow: hidden;
            margin-bottom: 2.5rem;
            color: white;
            box-shadow: 0 20px 40px -10px rgba(79, 70, 229, 0.3);
        }

        .wellness-banner::after {
            content: "\e991"; /* feather-heart icon */
            font-family: 'feather' !important;
            position: absolute;
            top: -40px;
            right: -20px;
            font-size: 240px;
            opacity: 0.1;
            transform: rotate(-15deg);
        }

        .pulse-dot {
            width: 8px;
            height: 8px;
            background: #10b981;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 0 rgba(16, 185, 129, 0.4);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }
    </style>

    <div class="db-container">
        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-3">
            <div>
                <h2 class="fw-black mb-1" style="letter-spacing: -1.5px; font-size: 2.25rem;">Patient Overview</h2>
                <div class="d-flex align-items-center gap-3">
                    <span class="badge bg-soft-primary text-primary px-3 py-1 rounded-pill fw-bold fs-10">
                        <span class="pulse-dot me-2"></span>LIVE HEALTH UPDATES
                    </span>
                    <span class="text-muted small fw-bold">
                        <i class="feather-calendar me-1"></i> {{ date('l, d M Y') }}
                    </span>
                </div>
            </div>
            <div class="d-flex gap-2">
                <div class="px-4 py-2 bg-white border rounded-pill shadow-sm d-flex align-items-center gap-2">
                    <span class="text-muted fs-11 fw-bold text-uppercase">Medical ID:</span>
                    <span class="fw-black text-primary">{{ $patient->formatted_id }}</span>
                </div>
            </div>
        </div>

        <!-- Wellness Banner -->
        <div class="wellness-banner animate__animated animate__fadeIn">
            <div class="position-relative" style="z-index: 2;">
                <h1 class="fw-900 text-white mb-2 display-5">Hello, {{ $patient->name }}!</h1>
                <p class="fs-18 opacity-90 mb-4 fw-medium max-w-md">{{ $greeting }}</p>
                <div class="d-flex gap-3 mt-4">
                    <a href="{{ route('portal.reports') }}" class="btn btn-white text-primary fw-900 px-5 py-3 rounded-pill shadow-lg border-0 bg-white">
                        <i class="feather-file-text me-2"></i>ACCESS MY REPORTS
                    </a>
                </div>
            </div>
        </div>

        <!-- Executive Summary Bar -->
        <div class="glass-bar">
            <div class="glass-item">
                <div class="icon-box-sm bg-soft-primary text-primary"><i class="feather-clipboard"></i></div>
                <div>
                    <div class="glass-val">{{ $reportsCount }}</div>
                    <div class="glass-lbl">Total Reports</div>
                </div>
            </div>
            <div class="vr d-none d-lg-block opacity-10"></div>
            <div class="glass-item">
                <div class="icon-box-sm bg-soft-warning text-warning"><i class="feather-clock"></i></div>
                <div>
                    <div class="glass-val">{{ $pendingReportsCount }}</div>
                    <div class="glass-lbl">Processing</div>
                </div>
            </div>
            <div class="vr d-none d-lg-block opacity-10"></div>
            <div class="glass-item">
                <div class="icon-box-sm bg-soft-success text-success"><i class="feather-trending-down"></i></div>
                <div>
                    <div class="glass-val">₹{{ number_format($totalSavings, 0) }}</div>
                    <div class="glass-lbl">Your Savings</div>
                </div>
            </div>
            <div class="vr d-none d-lg-block opacity-10"></div>
            <div class="glass-item">
                <div class="icon-box-sm bg-soft-info text-info"><i class="feather-award"></i></div>
                <div>
                    <div class="glass-val text-truncate" style="max-width: 150px;">{{ $activeMembership->membership->name ?? 'Free Plan' }}</div>
                    <div class="glass-lbl">Member Status</div>
                </div>
            </div>
        </div>

        <!-- Details Grid -->
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="db-card p-0 overflow-hidden">
                    <div class="p-4 border-bottom bg-light bg-opacity-50">
                        <h5 class="fw-bold mb-0 text-dark">Laboratory Information</h5>
                    </div>
                    <div class="p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="p-4 rounded-4 border border-dashed bg-white h-100">
                                    <h6 class="fw-black text-muted fs-11 text-uppercase mb-3 tracking-widest">Main Lab & Branch</h6>
                                    <h5 class="fw-900 text-dark mb-1">{{ $lab->name ?? 'Primary Diagnostics' }}</h5>
                                    <p class="text-muted fs-12 mb-3 fw-medium">{{ $branch->name ?? 'Main Testing Facility' }}</p>
                                    <div class="d-flex align-items-center gap-2 fs-13 text-muted fw-medium">
                                        <i class="feather-map-pin text-primary"></i>
                                        <span>{{ $branch->address ?? 'Address not available' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-4 rounded-4 bg-soft-info border border-info border-opacity-10 h-100">
                                    <h6 class="fw-black text-info fs-11 text-uppercase mb-3 tracking-widest">Support Helpline</h6>
                                    <p class="fs-13 text-dark mb-4 fw-medium">Need help with your reports? Our technical team is available 24/7.</p>
                                    <a href="tel:{{ $lab->phone ?? '' }}" class="btn btn-info w-100 fw-900 text-white rounded-pill py-3 shadow-sm border-0">
                                        <i class="feather-phone-call me-2"></i>CALL HELPLINE
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="db-card p-0 overflow-hidden">
                    <div class="p-4 border-bottom bg-light bg-opacity-50">
                        <h5 class="fw-bold mb-0 text-dark">Quick Navigation</h5>
                    </div>
                    <div class="p-0">
                        <div class="list-group list-group-flush">
                            <a href="{{ route('portal.reports') }}" class="list-group-item list-group-item-action p-4 border-0">
                                <div class="d-flex align-items-center gap-4">
                                    <div class="avatar-text avatar-md bg-soft-primary text-primary rounded-4">
                                        <i class="feather-file-text fs-4"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-900 text-dark fs-15">Download Reports</div>
                                        <div class="fs-12 text-muted fw-medium">Approved lab results</div>
                                    </div>
                                    <i class="feather-chevron-right text-muted opacity-40"></i>
                                </div>
                            </a>
                            <a href="{{ route('portal.membership') }}" class="list-group-item list-group-item-action p-4 border-0 border-top">
                                <div class="d-flex align-items-center gap-4">
                                    <div class="avatar-text avatar-md bg-soft-success text-success rounded-4">
                                        <i class="feather-award fs-4"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-900 text-dark fs-15">VIP Perks</div>
                                        <div class="fs-12 text-muted fw-medium">Plan benefits & savings</div>
                                    </div>
                                    <i class="feather-chevron-right text-muted opacity-40"></i>
                                </div>
                            </a>
                            <a href="{{ route('portal.profile') }}" class="list-group-item list-group-item-action p-4 border-0 border-top">
                                <div class="d-flex align-items-center gap-4">
                                    <div class="avatar-text avatar-md bg-soft-danger text-danger rounded-4">
                                        <i class="feather-settings fs-4"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-900 text-dark fs-15">Settings</div>
                                        <div class="fs-12 text-muted fw-medium">Account & Security</div>
                                    </div>
                                    <i class="feather-chevron-right text-muted opacity-40"></i>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>