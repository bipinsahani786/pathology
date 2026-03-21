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
    </div>

    <div class="main-content mt-4">
        <div class="row g-4">
            <!-- Stats Card: Total Labs -->
            <div class="col-xxl-3 col-md-6">
                <div class="card stretch stretch-full border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="d-flex gap-4 align-items-center">
                                <div class="avatar-text avatar-lg bg-soft-primary text-primary rounded-circle shadow-sm fw-bold text-center" style="width: 54px; height: 54px; line-height: 54px; font-size: 24px;">
                                    <i class="feather-box"></i>
                                </div>
                                <div>
                                    <div class="fs-4 fw-bold text-dark mb-0">{{ $totalLabs }}</div>
                                    <h6 class="fs-13 fw-semibold text-muted mb-0">Total Registered Labs</h6>
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
                                <div class="avatar-text avatar-lg bg-soft-success text-success rounded-circle shadow-sm fw-bold text-center" style="width: 54px; height: 54px; line-height: 54px; font-size: 24px;">
                                    <i class="feather-activity"></i>
                                </div>
                                <div>
                                    <div class="fs-4 fw-bold text-dark mb-0">{{ $totalGlobalTests }}</div>
                                    <h6 class="fs-13 fw-semibold text-muted mb-0">Tests in Master Library</h6>
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
                                <div class="avatar-text avatar-lg bg-soft-warning text-warning rounded-circle shadow-sm fw-bold text-center" style="width: 54px; height: 54px; line-height: 54px; font-size: 24px;">
                                    <i class="feather-credit-card"></i>
                                </div>
                                <div>
                                    <div class="fs-4 fw-bold text-dark mb-0">{{ $totalPlans }}</div>
                                    <h6 class="fs-13 fw-semibold text-muted mb-0">Subscription Plans</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Card: System Health -->
            <div class="col-xxl-3 col-md-6">
                <div class="card stretch stretch-full border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="d-flex gap-4 align-items-center">
                                <div class="avatar-text avatar-lg bg-soft-info text-info rounded-circle shadow-sm fw-bold text-center" style="width: 54px; height: 54px; line-height: 54px; font-size: 24px;">
                                    <i class="feather-shield"></i>
                                </div>
                                <div>
                                    <div class="fs-4 fw-bold text-dark mb-0">{{ $activePlans }}</div>
                                    <h6 class="fs-13 fw-semibold text-muted mb-0">Live Subscription Plans</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-5 text-center">
                        <h4 class="fw-bold text-dark mb-3">Welcome to Super Admin Control Panel</h4>
                        <p class="text-muted fs-14 mx-auto" style="max-width: 600px;">
                            Manage the entire pathology network from here. You can add new global tests, create mandatory system departments, and define pricing plans that labs can subscribe to.
                        </p>
                        <div class="hstack gap-3 justify-content-center mt-4">
                            <a href="{{ route('admin.global-tests') }}" wire:navigate class="btn btn-primary px-4 shadow-sm">Manage Master Library</a>
                            <a href="{{ route('admin.departments') }}" wire:navigate class="btn btn-outline-primary px-4 bg-white shadow-sm">Configure Departments</a>
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