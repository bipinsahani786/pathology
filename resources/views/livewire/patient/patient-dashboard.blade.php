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

        {{-- ======================================================== --}}
        {{-- HOME COLLECTION & PHLEBOTOMIST TRACKING SECTION          --}}
        {{-- ======================================================== --}}
        @if($activeHomeVisits && $activeHomeVisits->isNotEmpty())
        <div class="mb-5 animate__animated animate__fadeIn">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="icon-box-sm bg-soft-primary text-primary rounded-circle">
                        <i class="feather-truck fs-5"></i>
                    </div>
                    <div>
                        <h4 class="fw-black mb-0 text-dark" style="letter-spacing: -0.5px;">Home Sample Collection Tracking</h4>
                        <div class="text-muted fs-12 fw-medium">Live phlebotomist updates, scheduled timings & doorstep collection details</div>
                    </div>
                </div>
                <span class="badge bg-soft-primary text-primary px-3 py-1 rounded-pill fw-bold fs-11">
                    {{ $activeHomeVisits->count() }} {{ \Illuminate\Support\Str::plural('Booking', $activeHomeVisits->count()) }}
                </span>
            </div>

            <div class="d-flex flex-column gap-3">
                @foreach($activeHomeVisits as $visit)
                @php
                    $phlebo = $visit->phlebotomist;
                    $profile = $phlebo?->phlebotomistProfile;
                    $phoneDigits = $phlebo?->phone ? preg_replace('/[^0-9]/', '', $phlebo->phone) : null;
                    if ($phoneDigits && strlen($phoneDigits) === 10) {
                        $phoneDigits = '91' . $phoneDigits;
                    }

                    // Progress stages
                    $steps = [
                        'Pending'    => ['label' => 'Order Booked', 'icon' => 'feather-clipboard', 'time' => $visit->created_at],
                        'Assigned'   => ['label' => 'Phlebo Assigned', 'icon' => 'feather-user-check', 'time' => $visit->assigned_at],
                        'En Route'   => ['label' => 'On The Way', 'icon' => 'feather-navigation', 'time' => $visit->en_route_at],
                        'Arrived'    => ['label' => 'At Doorstep', 'icon' => 'feather-map-pin', 'time' => $visit->arrived_at],
                        'Collected'  => ['label' => 'Sample Collected', 'icon' => 'feather-check-circle', 'time' => $visit->collected_at],
                        'Received'   => ['label' => 'Testing in Lab', 'icon' => 'feather-activity', 'time' => $visit->received_at ?? $visit->dispatched_at],
                    ];

                    $statusOrder = [
                        'Pending'    => 1,
                        'Assigned'   => 2,
                        'En Route'   => 3,
                        'Arrived'    => 4,
                        'Collected'  => 5,
                        'Dispatched' => 6,
                        'Received'   => 6,
                    ];
                    $currentRank = $statusOrder[$visit->status] ?? 1;
                    $isCancelled = $visit->status === 'Cancelled';
                @endphp

                <div class="card border rounded-4 p-4 shadow-sm bg-white position-relative overflow-hidden" 
                     style="border-color: #e2e8f0 !important; border-left: 5px solid {{ $visit->status === 'Collected' || $visit->status === 'Received' ? '#10b981' : ($visit->status === 'En Route' ? '#0ea5e9' : ($visit->status === 'Cancelled' ? '#ef4444' : '#6366f1')) }} !important;">
                    
                    {{-- Status Banner Top Row --}}
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 pb-3 border-bottom border-light">
                        <div>
                            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                <span class="badge bg-soft-{{ $visit->status_color }} text-{{ $visit->status_color }} rounded-pill px-3 py-1 fs-12 fw-bold text-uppercase">
                                    <i class="{{ $visit->status_badge_icon }} me-1"></i>{{ $visit->status }}
                                </span>
                                @if($visit->status === 'En Route')
                                <span class="badge bg-danger text-white rounded-pill px-2 py-0 fs-10 fw-bold animate__animated animate__flash animate__infinite">
                                    ● LIVE EN ROUTE
                                </span>
                                @endif
                                <span class="text-muted fs-12 fw-semibold">
                                    Invoice #{{ $visit->invoice?->invoice_number ?? $visit->invoice_id }}
                                </span>
                            </div>
                            <h5 class="fw-black text-dark mb-0 fs-16">{{ $visit->patient_status_headline }}</h5>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <button wire:click="viewTracking({{ $visit->id }})" class="btn btn-outline-primary fw-bold rounded-pill px-4 py-2 fs-12 d-inline-flex align-items-center gap-1 shadow-sm">
                                <i class="feather-compass"></i>
                                <span>Track Timeline & GPS</span>
                            </button>
                        </div>
                    </div>

                    {{-- Main Info Grid: Phlebotomist, Timing, Location --}}
                    <div class="row g-3 my-2 align-items-stretch">
                        
                        {{-- 1. Phlebotomist Contact Card --}}
                        <div class="col-lg-4 col-md-6">
                            <div class="p-3 rounded-3 bg-light h-100 border border-light d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="fs-10 fw-black text-uppercase text-muted tracking-wider">
                                            <i class="feather-user me-1 text-primary"></i>Phlebotomist Contact
                                        </span>
                                        @if($phlebo)
                                        <span class="badge bg-soft-success text-success rounded-pill px-2 py-0 fs-9 fw-bold">Verified Lab Staff</span>
                                        @endif
                                    </div>

                                    @if($phlebo)
                                    <div class="d-flex align-items-center gap-3 mb-2">
                                        <div class="avatar-text avatar-md bg-primary text-white rounded-circle fw-black fs-14 flex-shrink-0 shadow-sm">
                                            {{ strtoupper(substr($phlebo->name, 0, 1)) }}
                                        </div>
                                        <div class="overflow-hidden">
                                            <div class="fw-bold text-dark fs-14 text-truncate">{{ $phlebo->name }}</div>
                                            @if($profile && ($profile->vehicle_type || $profile->vehicle_number))
                                            <div class="text-muted fs-11">
                                                <i class="feather-truck me-1"></i>{{ $profile->vehicle_type ?? 'Vehicle' }}
                                                @if($profile->vehicle_number) • <span class="font-monospace fw-semibold">{{ $profile->vehicle_number }}</span>@endif
                                            </div>
                                            @else
                                            <div class="text-muted fs-11"><i class="feather-shield text-success me-1"></i>Medical Sample Collector</div>
                                            @endif
                                        </div>
                                    </div>
                                    @else
                                    <div class="p-2 rounded bg-white border border-dashed text-center my-2">
                                        <div class="text-muted fs-12 fw-medium">
                                            <i class="feather-clock text-warning me-1"></i>Lab is assigning a phlebotomist shortly
                                        </div>
                                    </div>
                                    @endif
                                </div>

                                <div class="d-flex gap-2 mt-2">
                                    @if($phlebo && $phlebo->phone)
                                    <a href="tel:{{ $phlebo->phone }}" class="btn btn-primary btn-sm rounded-pill fw-bold flex-grow-1 d-inline-flex align-items-center justify-content-center gap-1 shadow-sm">
                                        <i class="feather-phone fs-12"></i>
                                        <span>Call Phlebo</span>
                                    </a>
                                    @if($phoneDigits)
                                    <a href="https://wa.me/{{ $phoneDigits }}?text=Hello%20{{ urlencode($phlebo->name) }}%2C%20I%20am%20tracking%20my%20home%20sample%20collection%20visit%20for%20Invoice%20%23{{ $visit->invoice?->invoice_number }}" 
                                       target="_blank" 
                                       class="btn btn-success btn-sm rounded-pill fw-bold text-white px-3 d-inline-flex align-items-center justify-content-center gap-1 shadow-sm"
                                       title="Chat on WhatsApp">
                                        <i class="feather-message-circle fs-12"></i>
                                        <span>WhatsApp</span>
                                    </a>
                                    @endif
                                    @else
                                    <a href="tel:{{ $lab->phone ?? '' }}" class="btn btn-outline-primary btn-sm rounded-pill fw-bold w-100 d-inline-flex align-items-center justify-content-center gap-1">
                                        <i class="feather-phone-call fs-12"></i>
                                        <span>Call Lab Support</span>
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- 2. Collection Timing Information --}}
                        <div class="col-lg-4 col-md-6">
                            <div class="p-3 rounded-3 bg-light h-100 border border-light d-flex flex-column justify-content-between">
                                <div>
                                    <span class="fs-10 fw-black text-uppercase text-muted tracking-wider d-block mb-2">
                                        <i class="feather-clock me-1 text-primary"></i>Collection Timing & Slot
                                    </span>

                                    <div class="p-2 rounded bg-white border mb-2">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <span class="text-muted fs-11 fw-semibold">Scheduled Date:</span>
                                            <span class="fw-bold text-dark fs-12">
                                                <i class="feather-calendar text-primary me-1"></i>{{ $visit->scheduled_date?->format('d M Y') }}
                                            </span>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between mt-1">
                                            <span class="text-muted fs-11 fw-semibold">Time Slot Window:</span>
                                            <span class="badge bg-soft-primary text-primary fw-bold fs-11">
                                                @if($visit->scheduled_slot_start)
                                                    {{ \Carbon\Carbon::parse($visit->scheduled_slot_start)->format('h:i A') }}
                                                    @if($visit->scheduled_slot_end) - {{ \Carbon\Carbon::parse($visit->scheduled_slot_end)->format('h:i A') }} @endif
                                                @else
                                                    Anytime Today
                                                @endif
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Status Milestone Timestamps --}}
                                    <div class="d-flex flex-column gap-1">
                                        @if($visit->en_route_at)
                                        <div class="fs-11 text-muted d-flex justify-content-between">
                                            <span><i class="feather-navigation text-info me-1"></i>Phlebo En Route:</span>
                                            <strong class="text-dark">{{ $visit->en_route_at->format('h:i A') }}</strong>
                                        </div>
                                        @endif
                                        @if($visit->arrived_at)
                                        <div class="fs-11 text-muted d-flex justify-content-between">
                                            <span><i class="feather-map-pin text-warning me-1"></i>Phlebo Arrived:</span>
                                            <strong class="text-dark">{{ $visit->arrived_at->format('h:i A') }}</strong>
                                        </div>
                                        @endif
                                        @if($visit->collected_at)
                                        <div class="fs-11 text-muted d-flex justify-content-between">
                                            <span><i class="feather-check-circle text-success me-1"></i>Sample Collected:</span>
                                            <strong class="text-dark">{{ $visit->collected_at->format('h:i A') }}</strong>
                                        </div>
                                        @endif
                                        @if(!$visit->en_route_at && !$visit->arrived_at && !$visit->collected_at)
                                        <div class="text-muted fs-11 text-center py-1">
                                            <i class="feather-info me-1"></i>Sample collection is queued for the scheduled time
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- 3. Location Information --}}
                        <div class="col-lg-4 col-md-12">
                            <div class="p-3 rounded-3 bg-light h-100 border border-light d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="fs-10 fw-black text-uppercase text-muted tracking-wider">
                                            <i class="feather-map-pin me-1 text-primary"></i>Collection Location
                                        </span>
                                        @if($visit->collection_lat && $visit->collection_lng)
                                        <span class="badge bg-soft-success text-success rounded-pill px-2 py-0 fs-9 fw-semibold">
                                            <i class="feather-check-circle me-1"></i>GPS Pinned
                                        </span>
                                        @endif
                                    </div>

                                    <div class="p-2 rounded bg-white border mb-2">
                                        <div class="fw-semibold text-dark fs-12 text-break mb-1">
                                            {{ $visit->collection_address }}
                                        </div>
                                        @if($visit->collection_landmark)
                                        <div class="text-muted fs-11">
                                            <i class="feather-compass me-1 text-primary"></i>Landmark: <strong>{{ $visit->collection_landmark }}</strong>
                                        </div>
                                        @endif
                                    </div>

                                    @php
                                        $latestLogWithGps = $visit->statusLogs->whereNotNull('latitude')->whereNotNull('longitude')->first();
                                    @endphp
                                    @if($latestLogWithGps)
                                    <div class="text-success fs-11 fw-semibold mb-2">
                                        <i class="feather-radio me-1"></i>Phlebo GPS at {{ $latestLogWithGps->to_status }}: 
                                        <a href="https://maps.google.com/?q={{ $latestLogWithGps->latitude }},{{ $latestLogWithGps->longitude }}" target="_blank" class="text-primary text-decoration-underline ms-1">
                                            View Spot Map
                                        </a>
                                    </div>
                                    @endif
                                </div>

                                <a href="{{ $visit->google_maps_link }}" target="_blank" class="btn btn-outline-secondary btn-sm rounded-pill fw-bold w-100 d-inline-flex align-items-center justify-content-center gap-1 shadow-sm mt-2">
                                    <i class="feather-external-link fs-12"></i>
                                    <span>Open Address in Google Maps</span>
                                </a>
                            </div>
                        </div>

                    </div>

                    {{-- Horizontal Visual Step Tracker --}}
                    @if(!$isCancelled)
                    <div class="mt-3 pt-3 border-top border-light">
                        <div class="row g-2 align-items-center text-center">
                            @foreach($steps as $stKey => $stData)
                            @php
                                $stRank = $statusOrder[$stKey] ?? 1;
                                $isCompleted = $currentRank >= $stRank;
                                $isCurrent = $visit->status === $stKey;
                            @endphp
                            <div class="col">
                                <div class="d-flex flex-column align-items-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                                         style="width: 34px; height: 34px; font-size: 13px;
                                                {{ $isCurrent ? 'background: #0ea5e9; color: white; transform: scale(1.15); box-shadow: 0 0 10px rgba(14,165,233,0.5) !important;' : ($isCompleted ? 'background: #10b981; color: white;' : 'background: #f1f5f9; color: #94a3b8;') }}">
                                        @if($isCompleted && !$isCurrent)
                                            <i class="feather-check"></i>
                                        @else
                                            <i class="{{ $stData['icon'] }}"></i>
                                        @endif
                                    </div>
                                    <div class="fs-11 mt-1 {{ $isCurrent ? 'fw-bold text-primary' : ($isCompleted ? 'fw-semibold text-dark' : 'text-muted') }}">
                                        {{ $stData['label'] }}
                                    </div>
                                    @if($stData['time'])
                                    <div class="fs-10 text-muted">{{ \Carbon\Carbon::parse($stData['time'])->format('h:i A') }}</div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @else
                    <div class="alert alert-danger rounded-3 py-2 px-3 mt-3 mb-0 fs-12 d-flex align-items-center gap-2">
                        <i class="feather-alert-circle fs-5"></i>
                        <div>This home collection visit was cancelled. @if($visit->cancellation_reason) Reason: <strong>{{ $visit->cancellation_reason }}</strong> @endif</div>
                    </div>
                    @endif

                </div>
                @endforeach
            </div>
        </div>
        @endif

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

        {{-- ======================================================== --}}
        {{-- MODAL: DETAILED HOME VISIT TRACKING & FIELD GPS TIMELINE --}}
        {{-- ======================================================== --}}
        @if($showTrackingModal && $selectedVisit)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 1060;">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="modal-header border-0 bg-primary text-white p-4">
                        <div>
                            <div class="fs-11 fw-black text-uppercase text-white-50 tracking-wider mb-1">
                                Live Visit Tracking & GPS Audit
                            </div>
                            <h5 class="modal-title fw-bold text-white mb-0 fs-18">
                                Invoice #{{ $selectedVisit->invoice?->invoice_number ?? $selectedVisit->invoice_id }}
                                <span class="badge bg-white text-primary rounded-pill px-3 py-1 fs-12 ms-2 fw-bold">
                                    {{ $selectedVisit->status }}
                                </span>
                            </h5>
                        </div>
                        <button type="button" wire:click="closeTrackingModal" class="btn-close btn-close-white" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-4 bg-white">
                        {{-- Phlebotomist & Timing Overview --}}
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="p-3 rounded-3 bg-light border border-light h-100">
                                    <span class="fs-11 fw-bold text-uppercase text-muted d-block mb-1">Assigned Phlebotomist</span>
                                    @if($selectedVisit->phlebotomist)
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-text avatar-md bg-primary text-white rounded-circle fw-bold">
                                            {{ strtoupper(substr($selectedVisit->phlebotomist->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark fs-14">{{ $selectedVisit->phlebotomist->name }}</div>
                                            <div class="text-muted fs-12"><i class="feather-phone me-1"></i>{{ $selectedVisit->phlebotomist->phone ?? 'No phone' }}</div>
                                            @if($selectedVisit->phlebotomist->phlebotomistProfile?->vehicle_number)
                                            <div class="text-muted fs-11">
                                                <i class="feather-truck me-1"></i>{{ $selectedVisit->phlebotomist->phlebotomistProfile->vehicle_type ?? 'Vehicle' }}:
                                                {{ $selectedVisit->phlebotomist->phlebotomistProfile->vehicle_number }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    @if($selectedVisit->phlebotomist->phone)
                                    <div class="mt-2 pt-2 border-top border-light d-flex gap-2">
                                        <a href="tel:{{ $selectedVisit->phlebotomist->phone }}" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold flex-grow-1">
                                            <i class="feather-phone me-1"></i>Call
                                        </a>
                                        @php
                                            $pDigits = preg_replace('/[^0-9]/', '', $selectedVisit->phlebotomist->phone);
                                            if (strlen($pDigits) === 10) $pDigits = '91' . $pDigits;
                                        @endphp
                                        @if($pDigits)
                                        <a href="https://wa.me/{{ $pDigits }}" target="_blank" class="btn btn-sm btn-success rounded-pill px-3 fw-bold text-white">
                                            <i class="feather-message-circle me-1"></i>WhatsApp
                                        </a>
                                        @endif
                                    </div>
                                    @endif
                                    @else
                                    <div class="text-muted fs-12 mt-1">Phlebotomist will be assigned shortly by the laboratory team.</div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="p-3 rounded-3 bg-light border border-light h-100">
                                    <span class="fs-11 fw-bold text-uppercase text-muted d-block mb-1">Schedule & Address</span>
                                    <div class="fw-bold text-dark fs-13 mb-1">
                                        <i class="feather-calendar text-primary me-1"></i>{{ $selectedVisit->scheduled_date?->format('d M Y') }}
                                        @if($selectedVisit->scheduled_slot_start)
                                        • {{ \Carbon\Carbon::parse($selectedVisit->scheduled_slot_start)->format('h:i A') }}
                                        @if($selectedVisit->scheduled_slot_end) - {{ \Carbon\Carbon::parse($selectedVisit->scheduled_slot_end)->format('h:i A') }} @endif
                                        @endif
                                    </div>
                                    <div class="text-muted fs-12 mb-2">
                                        <i class="feather-map-pin me-1"></i>{{ $selectedVisit->collection_address }}
                                        @if($selectedVisit->collection_landmark) <br><small>Landmark: {{ $selectedVisit->collection_landmark }}</small> @endif
                                    </div>
                                    <a href="{{ $selectedVisit->google_maps_link }}" target="_blank" class="btn btn-xs btn-outline-primary rounded-pill px-3 py-1 fs-11 fw-bold">
                                        <i class="feather-external-link me-1"></i>Open in Google Maps
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- Status Transition Logs & GPS Verification Table --}}
                        <div class="mb-2 d-flex justify-content-between align-items-center">
                            <span class="fs-12 fw-bold text-uppercase text-dark">
                                <i class="feather-clock text-primary me-1"></i>Lifecycle Milestones & GPS Audit
                            </span>
                            <span class="badge bg-light text-muted border rounded-pill fs-11">
                                {{ count($selectedVisit->statusLogs) }} Event Logs
                            </span>
                        </div>

                        <div class="table-responsive border rounded-3" style="max-height: 260px; overflow-y: auto;">
                            <table class="table table-hover align-middle mb-0 fs-12">
                                <thead class="bg-light text-muted sticky-top">
                                    <tr>
                                        <th class="ps-3 py-2">Timestamp</th>
                                        <th class="py-2">Milestone / Status</th>
                                        <th class="pe-3 py-2">Location & GPS Link</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($selectedVisit->statusLogs as $log)
                                    <tr>
                                        <td class="ps-3 py-2 text-nowrap">
                                            <div class="fw-semibold text-dark">{{ $log->created_at?->format('d M, h:i A') }}</div>
                                            <div class="text-muted fs-10">{{ $log->created_at?->diffForHumans() }}</div>
                                        </td>
                                        <td class="py-2">
                                            <span class="badge bg-soft-primary text-primary px-2 py-1 fs-11 fw-bold">{{ $log->to_status }}</span>
                                            @if($log->notes)
                                            <div class="text-muted fs-11 mt-1">{{ $log->notes }}</div>
                                            @endif
                                        </td>
                                        <td class="pe-3 py-2">
                                            @if($log->latitude && $log->longitude)
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge bg-soft-success text-success rounded-pill px-2 py-0 fs-10">
                                                    <i class="feather-check-circle me-1"></i>GPS Captured
                                                </span>
                                                <a href="https://maps.google.com/?q={{ $log->latitude }},{{ $log->longitude }}" target="_blank" class="btn btn-xs btn-outline-success rounded-pill px-2 py-0 fs-10 fw-bold">
                                                    <i class="feather-map-pin me-1"></i>View Location
                                                </a>
                                            </div>
                                            @else
                                            <span class="text-muted fs-11">Lab / System Log</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-3 text-muted">No timeline status logs recorded yet.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="modal-footer border-0 bg-light p-3">
                        <button type="button" wire:click="closeTrackingModal" class="btn btn-secondary rounded-pill px-4 fw-bold fs-12">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show" style="z-index: 1050;"></div>
        @endif
    </div>
</div>