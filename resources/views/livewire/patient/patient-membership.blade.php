<div>
    <style>
        :root {
            --db-primary: #4f46e5;
            --db-success: #10b981;
            --db-warning: #f59e0b;
            --db-bg: #f8fafc;
            --db-card-bg: #ffffff;
            --db-text-main: #1e293b;
            --db-text-muted: #64748b;
            --db-border: #e2e8f0;
            --db-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        }

        html.app-skin-dark {
            --db-bg: #0d0d1a;
            --db-card-bg: #1a1a2e;
            --db-border: rgba(255, 255, 255, 0.08);
        }

        .db-container {
            padding: 1.5rem;
            max-width: 1600px;
            margin: 0 auto;
        }

        .vip-card {
            background: linear-gradient(135deg, {{ $activeMembership->membership->color_code ?? '#4f46e5' }} 0%, #1e1b4b 100%);
            border-radius: 2.5rem;
            min-height: 520px;
            padding: 4rem;
            position: relative;
            overflow: hidden;
            color: white;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            transition: all 0.5s ease;
        }

        .vip-card:hover {
            transform: translateY(-10px) rotate(1deg);
        }

        .glass-box {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 1.5rem;
            padding: 1.5rem;
        }

        .stat-badge {
            background: rgba(var(--bs-success-rgb), 0.1);
            color: #10b981;
            padding: 0.5rem 1rem;
            border-radius: 100px;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
        }
    </style>

    <div class="db-container">
        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-3">
            <div>
                <h2 class="fw-black mb-1" style="letter-spacing: -1.5px; font-size: 2.25rem;">Membership Privileges</h2>
                <div class="d-flex align-items-center gap-3">
                    <span class="badge bg-soft-success text-success px-3 py-1 rounded-pill fw-bold fs-10">
                        <i class="feather-shield me-1"></i> VERIFIED MEMBER
                    </span>
                </div>
            </div>
        </div>

        <div class="row g-5">
            <div class="col-xl-7">
                @if($activeMembership)
                    <div class="vip-card animate__animated animate__fadeInLeft">
                        <!-- Shield Decoration -->
                        <div class="position-absolute" style="top: -80px; right: -80px; opacity: 0.1;">
                            <i class="feather-shield text-white" style="font-size: 380px;"></i>
                        </div>

                        <div class="position-relative" style="z-index: 2;">
                            <div class="d-flex justify-content-between align-items-start mb-5 pb-3">
                                <div>
                                    <div class="fs-12 fw-black text-white opacity-40 text-uppercase tracking-widest mb-2">Exclusive Membership Card</div>
                                    <h1 class="display-3 fw-900 text-white mb-0 tracking-tighter">{{ strtoupper($activeMembership->membership->name) }}</h1>
                                    <div class="fw-bold fs-15 text-white opacity-70 mt-1">{{ $patient->company->name ?? 'Diagnostic Center' }}</div>
                                </div>
                                <div class="avatar-text avatar-xl bg-white bg-opacity-10 text-white rounded-circle border border-white border-opacity-20 shadow-lg">
                                    <i class="feather-award fs-1"></i>
                                </div>
                            </div>

                            <div class="row g-4 mb-5">
                                <div class="col-sm-6">
                                    <div class="glass-box">
                                        <div class="fs-11 text-white opacity-50 text-uppercase tracking-widest mb-2 fw-bold">Live Status</div>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="pulse-dot"></div>
                                            <span class="fs-16 fw-900 tracking-wide text-white">ACTIVE MEMBER</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="glass-box">
                                        <div class="fs-11 text-white opacity-50 text-uppercase tracking-widest mb-2 fw-bold">Privilege Level</div>
                                        <span class="fs-16 fw-900 tracking-wide text-white">{{ number_format($activeMembership->membership->discount_percentage, 0) }}% FLAT DISCOUNT</span>
                                    </div>
                                </div>
                            </div>

                            <div class="glass-box p-4" style="background: rgba(0, 0, 0, 0.3);">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="feather-calendar text-white opacity-50"></i>
                                        <span class="fs-12 fw-black tracking-widest opacity-60">MEMBERSHIP LIFESPAN</span>
                                    </div>
                                    <span class="badge bg-success bg-opacity-75 fs-10 fw-black px-3 py-1">UNTIL {{ \Carbon\Carbon::parse($activeMembership->valid_until)->format('d M, Y') }}</span>
                                </div>
                                @php
                                    $from = \Carbon\Carbon::parse($activeMembership->valid_from);
                                    $until = \Carbon\Carbon::parse($activeMembership->valid_until);
                                    $now = now();
                                    
                                    $totalDays = max(1, $from->diffInDays($until));
                                    $daysLeft = max(0, $now->diffInDays($until, false));
                                    $daysPassed = $totalDays - $daysLeft;
                                    
                                    $percent = min(100, max(0, ($daysPassed / $totalDays) * 100));
                                @endphp
                                <div class="progress overflow-visible bg-white bg-opacity-10" style="height: 12px; border-radius: 6px;">
                                    <div class="progress-bar bg-success rounded-pill shadow-lg" style="width: {{ 100 - $percent }}%; height: 12px;"></div>
                                </div>
                                <div class="d-flex justify-content-between mt-3 px-1">
                                    <span class="fs-11 text-white opacity-50 fw-bold">Issued on {{ $from->format('M d, Y') }}</span>
                                    <span class="fs-11 text-white fw-black tracking-widest">{{ round($daysLeft) }} DAYS REMAINING</span>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Section - Replaced Absolute with Flex-End for Stability -->
                        <div class="mt-auto pt-5 d-flex justify-content-between align-items-end">
                            <div>
                                <div class="fw-black text-white fs-18 tracking-wide">{{ strtoupper($patient->name) }}</div>
                                <div class="fs-10 text-white opacity-30 mt-1 fw-bold text-uppercase tracking-widest">DIGITALLY SIGNED & ENCRYPTED IDENTITY</div>
                            </div>
                            <div class="text-end">
                                <img src="{{ asset('assets/images/icon.webp') }}" height="55px" style="filter: brightness(0) invert(1); opacity: 0.4;">
                                <div class="fs-9 text-white opacity-20 mt-1 fw-bold">SECURE PORTAL V2.0</div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="card border-0 shadow-sm rounded-4 py-5 text-center">
                        <i class="feather-award fs-1 text-muted opacity-20 mb-4"></i>
                        <h4 class="fw-900 text-dark">No Active Membership</h4>
                        <p class="text-muted fs-14 max-w-sm mx-auto fw-medium">Upgrade to a premium plan to unlock global lab discounts and priority services.</p>
                    </div>
                @endif
            </div>

            <div class="col-xl-5">
                <!-- Savings Card -->
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-5">
                    <div class="card-body p-5">
                        <h4 class="fw-900 text-dark mb-4 tracking-tighter">Your Savings Journey</h4>
                        <div class="row g-4 mb-5">
                            <div class="col-6">
                                <div class="p-4 rounded-4 bg-soft-success border border-success border-opacity-10 text-center h-100 d-flex flex-column justify-content-center">
                                    <h1 class="fw-900 text-success mb-1 display-5">₹{{ number_format($totalSavings, 0) }}</h1>
                                    <div class="fs-11 fw-black text-success opacity-70 tracking-widest">MONEY SAVED</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-4 rounded-4 bg-soft-primary border border-primary border-opacity-10 text-center h-100 d-flex flex-column justify-content-center">
                                    <h1 class="fw-900 text-primary mb-1 display-5">{{ $testsBenefitedCount }}</h1>
                                    <div class="fs-11 fw-black text-primary opacity-70 tracking-widest">TESTS COVERED</div>
                                </div>
                            </div>
                        </div>

                        <h6 class="fs-11 fw-black text-muted text-uppercase tracking-widest mb-4">Plan Roadmap & Privileges</h6>
                        <div class="d-flex flex-column gap-4">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-text avatar-xs bg-success text-white rounded-circle shadow-sm"><i class="feather-check"></i></div>
                                <div>
                                    <div class="fw-bold text-dark fs-14">Guaranteed {{ number_format($activeMembership->membership->discount_percentage ?? 0, 0) }}% Less on All Bills</div>
                                    <div class="text-muted fs-11 fw-medium mt-1">Applied automatically on every diagnostic session</div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-text avatar-xs bg-success text-white rounded-circle shadow-sm"><i class="feather-check"></i></div>
                                <div>
                                    <div class="fw-bold text-dark fs-14">Priority Result Processing</div>
                                    <div class="text-muted fs-11 fw-medium mt-1">Jump to the front of the lab technical queue</div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-text avatar-xs bg-success text-white rounded-circle shadow-sm"><i class="feather-check"></i></div>
                                <div>
                                    <div class="fw-bold text-dark fs-14">Complementary Phlebotomy</div>
                                    <div class="text-muted fs-11 fw-medium mt-1">Free home sample collection visits monthly</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- History -->
                @if($membershipHistory->isNotEmpty())
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                        <div class="card-header bg-light bg-opacity-50 p-4 border-0">
                            <h6 class="fw-900 text-dark mb-0 tracking-tighter">Previous Plans</h6>
                        </div>
                        <div class="p-0">
                            <div class="list-group list-group-flush">
                                @foreach($membershipHistory as $hist)
                                    <div class="list-group-item p-4 border-0 border-top">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="avatar-text avatar-sm bg-light text-muted rounded-3 fw-bold"><i class="feather-shield"></i></div>
                                                <div>
                                                    <div class="fw-bold text-dark fs-14">{{ $hist->membership->name }}</div>
                                                    <div class="fs-11 text-muted fw-medium">{{ \Carbon\Carbon::parse($hist->valid_from)->format('M Y') }} — {{ \Carbon\Carbon::parse($hist->valid_until)->format('M Y') }}</div>
                                                </div>
                                            </div>
                                            <span class="badge bg-soft-secondary text-secondary fs-10 fw-black px-3 py-1">ARCHIVED</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>