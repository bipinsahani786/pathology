<div>
    <!-- Page Header -->
    <div class="page-header d-flex flex-wrap align-items-center justify-content-between gap-2 gap-md-3">
        <div class="page-header-left d-flex align-items-center flex-wrap">
            <div class="page-header-title">
                <h5 class="m-b-10 text-dark fw-bold">Super Admin Dashboard</h5>
            </div>
            <ul class="breadcrumb d-none d-md-flex mb-0 ms-md-3">
                <li class="breadcrumb-item"><a href="#" class="text-muted">Home</a></li>
                <li class="breadcrumb-item text-primary fw-medium">Dashboard</li>
            </ul>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.labs') }}" wire:navigate class="btn btn-primary px-4 shadow-sm">
                <i class="feather-plus me-2"></i>Register New Lab
            </a>
        </div>
    </div>

    <div class="main-content mt-4">
        <!-- Stats Row -->
        <div class="row g-4 mb-4">
            <!-- Stats Card: Total Labs -->
            <div class="col-xxl-3 col-md-6">
                <div class="card stretch stretch-full border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="d-flex gap-4 align-items-center">
                                <div class="avatar-text avatar-lg bg-soft-primary text-primary rounded-circle shadow-sm fw-bold text-center" style="width: 54px; height: 54px; line-height: 54px; font-size: 24px;">
                                    <i class="feather-briefcase"></i>
                                </div>
                                <div>
                                    <div class="fs-4 fw-bold text-dark mb-0">{{ $totalLabs }}</div>
                                    <h6 class="fs-13 fw-semibold text-muted mb-0">Total Live Labs</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Card: MRR -->
            <div class="col-xxl-3 col-md-6">
                <div class="card stretch stretch-full border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="d-flex gap-4 align-items-center">
                                <div class="avatar-text avatar-lg bg-soft-success text-success rounded-circle shadow-sm fw-bold text-center" style="width: 54px; height: 54px; line-height: 54px; font-size: 24px;">
                                    <i class="feather-dollar-sign"></i>
                                </div>
                                <div>
                                    <div class="fs-4 fw-bold text-dark mb-0">₹{{ number_format($mrr, 2) }}</div>
                                    <h6 class="fs-13 fw-semibold text-muted mb-0">Estimated MRR</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Card: Global Tests -->
            <div class="col-xxl-3 col-md-6">
                <div class="card stretch stretch-full border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="d-flex gap-4 align-items-center">
                                <div class="avatar-text avatar-lg bg-soft-warning text-warning rounded-circle shadow-sm fw-bold text-center" style="width: 54px; height: 54px; line-height: 54px; font-size: 24px;">
                                    <i class="feather-activity"></i>
                                </div>
                                <div>
                                    <div class="fs-4 fw-bold text-dark mb-0">{{ $totalGlobalTests }}</div>
                                    <h6 class="fs-13 fw-semibold text-muted mb-0">Master Test Library</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Card: Active Plans -->
            <div class="col-xxl-3 col-md-6">
                <div class="card stretch stretch-full border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="d-flex gap-4 align-items-center">
                                <div class="avatar-text avatar-lg bg-soft-info text-info rounded-circle shadow-sm fw-bold text-center" style="width: 54px; height: 54px; line-height: 54px; font-size: 24px;">
                                    <i class="feather-credit-card"></i>
                                </div>
                                <div>
                                    <div class="fs-4 fw-bold text-dark mb-0">{{ $activePlans }} / {{ $totalPlans }}</div>
                                    <h6 class="fs-13 fw-semibold text-muted mb-0">Active SaaS Plans</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Split Content Row -->
        <div class="row g-4">
            <!-- Recent Registrations -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white py-3 border-bottom-0 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 fw-bold text-dark">Recent Lab Registrations</h6>
                        <a href="{{ route('admin.labs') }}" wire:navigate class="btn btn-sm btn-soft-primary">View All</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle mb-0">
                                <thead class="bg-light fs-11 text-uppercase text-muted fw-bold">
                                    <tr>
                                        <th class="ps-4">Laboratory</th>
                                        <th>Current Plan</th>
                                        <th>Contact</th>
                                        <th class="text-end pe-4">Registered On</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentLabs as $lab)
                                        <tr class="border-bottom border-light">
                                            <td class="ps-4">
                                                <div class="fw-bold text-dark fs-13">{{ $lab->name }}</div>
                                            </td>
                                            <td>
                                                <span class="badge bg-soft-primary text-primary px-2 py-1 fs-11 rounded-pill">
                                                    {{ $lab->plan->name ?? 'Free Tier' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="fs-12 text-muted">{{ $lab->email }}</div>
                                            </td>
                                            <td class="text-end pe-4">
                                                <div class="fs-12 text-muted">{{ $lab->created_at->format('d M, Y') }}</div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center py-4 text-muted">No labs registered yet.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Panel -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4 text-center d-flex flex-column justify-content-center">
                        <div class="avatar-text avatar-xl bg-soft-primary text-primary rounded-circle shadow-sm mx-auto mb-3" style="width: 70px; height: 70px; font-size: 30px;">
                            <i class="feather-settings"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">Master Settings</h5>
                        <p class="text-muted fs-13 mx-auto mb-4" style="max-width: 250px;">
                            Manage global lab tests, pricing limits, and departments for all standard tenants.
                        </p>
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.global-tests') }}" wire:navigate class="btn btn-outline-primary shadow-sm"><i class="feather-activity me-2"></i>Global Tests Library</a>
                            <a href="{{ route('admin.plans') }}" wire:navigate class="btn btn-outline-dark shadow-sm"><i class="feather-credit-card me-2"></i>Manage SaaS Plans</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <style>
        .bg-soft-primary { background-color: rgba(59, 113, 202, 0.1); }
        .bg-soft-success { background-color: rgba(25, 135, 84, 0.1); }
        .bg-soft-warning { background-color: rgba(234, 184, 0, 0.1); }
        .bg-soft-info { background-color: rgba(63, 194, 255, 0.1); }
    </style>
</div>