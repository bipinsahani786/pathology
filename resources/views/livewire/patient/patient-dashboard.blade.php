<div>
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Patient Portal</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('portal.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">My Dashboard</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <div class="d-flex align-items-center gap-3">
                <div class="fw-bold fs-14">
                    <span class="text-muted fw-normal me-1">Medical ID:</span> {{ $patient->formatted_id }}
                </div>
                <div class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill fw-bold">
                    {{ $reports->count() }} Tests Completed
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="row">
            <!-- Left Side: Laboratory / Membership details -->
            <div class="col-lg-4 col-md-12">
                <!-- Lab Details Card -->
                <div class="card stretch stretch-full">
                    <div class="card-header bg-soft-primary">
                        <h5 class="card-title text-primary"><i class="feather-home me-2"></i> Your Laboratory</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4 pb-4 border-bottom border-dashed border-gray-200">
                            <h4 class="fw-bolder mb-1">{{ $lab->name ?? 'Primary Laboratory' }}</h4>
                            <p class="text-muted fs-12 mb-0">{{ $lab->tagline ?? 'Quality & Precision diagnostics' }}</p>
                        </div>
                        
                        <div class="mt-4">
                            <label class="text-muted fs-10 text-uppercase fw-bold tracking-widest mb-1 d-block">Registered Branch</label>
                            <div class="d-flex align-items-start gap-2">
                                <i class="feather-map-pin text-primary mt-1"></i>
                                <div>
                                    <div class="fs-13 fw-semibold text-dark">{{ $branch->name ?? 'Main Branch' }}</div>
                                    @if($branch && $branch->address)
                                        <p class="fs-12 text-muted mt-1 mb-0">{{ $branch->address }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if($collectionCenter)
                            <div class="mt-4 pt-4 border-top border-dashed border-gray-200">
                                <label class="text-muted fs-10 text-uppercase fw-bold tracking-widest mb-1 d-block">Collection Center</label>
                                <div class="d-flex align-items-start gap-2">
                                    <i class="feather-box text-success mt-1"></i>
                                    <div>
                                        <div class="fs-13 fw-semibold text-dark">{{ $collectionCenter->name }}</div>
                                        @if($collectionCenter->address)
                                            <p class="fs-12 text-muted mt-1 mb-0">{{ $collectionCenter->address }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="mt-4 pt-4 border-top border-dashed border-gray-200">
                            <label class="text-muted fs-10 text-uppercase fw-bold tracking-widest mb-1 d-block">Technical Queries</label>
                            <div class="p-3 bg-soft-info rounded-3 mt-2">
                                <p class="fs-12 text-dark mb-2">If you have any questions regarding your reports, please call our 24/7 helpline:</p>
                                <a href="tel:{{ $lab->phone ?? '' }}" class="fw-bold text-info text-decoration-none">
                                    <i class="feather-phone-call me-1"></i> +{{ $lab->phone ?? 'Contact Lab' }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Membership Card Info -->
                @if($activeMembership)
                    <div class="card stretch stretch-full bg-dark text-white overflow-hidden" style="background: linear-gradient(135deg, #1e293b, #0f172a);">
                        <div class="card-body position-relative">
                            <i class="feather-shield position-absolute text-white" style="font-size: 100px; opacity: 0.05; right: -10px; bottom: -20px;"></i>
                            <p class="fs-10 fw-bold text-white text-uppercase tracking-widest mb-1 opacity-75">Active Membership</p>
                            <h4 class="fw-bolder mb-4 text-white">{{ $activeMembership->membership->name ?? 'VIP Member' }}</h4>
                            
                            <div class="bg-white bg-opacity-10 p-3 rounded-3 mb-3 border border-white border-opacity-10">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fs-11 fw-semibold opacity-75">Valid Until:</span>
                                    <span class="fs-12 fw-bold text-success">{{ \Carbon\Carbon::parse($activeMembership->valid_until)->format('d M, Y') }}</span>
                                </div>
                                <div class="progress bg-white bg-opacity-20 mb-3" style="height: 4px;">
                                    <div class="progress-bar bg-success" style="width: 100%"></div>
                                </div>
                                @if($totalSavings > 0)
                                <div class="border-top border-white border-opacity-10 pt-3 mt-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-text avatar-sm bg-soft-success text-success rounded-circle">
                                                <i class="feather-trending-down"></i>
                                            </div>
                                            <span class="fs-11 fw-bold text-white opacity-75">Total Savings</span>
                                        </div>
                                        <h5 class="mb-0 fw-bolder text-success">₹{{ number_format($totalSavings, 2) }}</h5>
                                    </div>
                                    <p class="fs-10 text-white opacity-50 mt-2 mb-0">Total money saved on tests compared to standard rates.</p>
                                </div>
                                @endif
                            </div>
                            <p class="fs-11 opacity-75 mb-0">Enjoy priority processing and exclusive discounts on all your diagnostic tests.</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Side: Test Reports -->
            <div class="col-lg-8 col-md-12">
                <div class="card stretch stretch-full">
                    <div class="card-header">
                        <h5 class="card-title">My Test Reports</h5>
                        <div class="card-header-action">
                            <a href="javascript:void(0);" class="btn btn-sm btn-light border" onclick="window.location.reload();">
                                <i class="feather-refresh-cw me-2"></i>Refresh
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if($reports->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table table-hover mb-0 align-middle">
                                    <thead class="bg-light fs-11 fw-bold text-uppercase text-muted tracking-wide">
                                        <tr>
                                            <th class="ps-4">Report Details</th>
                                            <th>Date & Time</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-end pe-4">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($reports as $report)
                                            <tr>
                                                <td class="ps-4">
                                                    <div class="fw-bold text-dark fs-14">Invoice #{{ $report->invoice->invoice_number }}</div>
                                                    <div class="text-muted fs-12 mt-1"><i class="feather-layers me-1"></i>Multiple Tests</div>
                                                </td>
                                                <td>
                                                    <div class="fw-bolder text-dark fs-13">{{ \Carbon\Carbon::parse($report->created_at)->format('d M, Y') }}</div>
                                                    <div class="text-muted fs-11 mt-1">{{ \Carbon\Carbon::parse($report->created_at)->format('h:i A') }}</div>
                                                </td>
                                                <td class="text-center">
                                                    @if($report->status === 'approved')
                                                        <span class="badge bg-soft-success text-success px-3 py-1 rounded fw-bold fs-11 border border-success border-opacity-10">
                                                            <i class="feather-check-circle me-1"></i> APPROVED
                                                        </span>
                                                    @elseif($report->status === 'pending')
                                                        <span class="badge bg-soft-warning text-warning px-3 py-1 rounded fw-bold fs-11 border border-warning border-opacity-10">
                                                            <i class="feather-clock me-1"></i> PENDING
                                                        </span>
                                                    @else
                                                        <span class="badge bg-soft-primary text-primary px-3 py-1 rounded fw-bold fs-11 border border-primary border-opacity-10">
                                                            <i class="feather-activity me-1"></i> {{ strtoupper($report->status) }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="text-end pe-4">
                                                    <div class="d-flex justify-content-end gap-2">
                                                        <a href="{{ route('portal.invoice.download', $report->invoice->id) }}" target="_blank" 
                                                           class="btn btn-soft-secondary btn-sm px-3 fw-bold shadow-sm" title="Download Invoice">
                                                            <i class="feather-file-text me-1"></i> Bill
                                                        </a>
                                                        
                                                        @if($report->status === 'approved')
                                                            <a href="{{ route('portal.report.download', $report->id) }}" target="_blank" 
                                                               class="btn btn-primary btn-sm px-3 fw-bold shadow-sm">
                                                                <i class="feather-download me-1"></i> Report
                                                            </a>
                                                        @else
                                                            <button class="btn btn-light btn-sm px-3 fw-bold border" disabled title="Report Processing">
                                                                <i class="feather-lock me-1"></i> Report
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="py-5 text-center px-4">
                                <div class="bg-light d-inline-flex p-4 rounded-circle mb-4 border">
                                    <i class="feather-inbox fs-1 text-muted"></i>
                                </div>
                                <h5 class="fw-bolder text-dark mb-2">No test reports found</h5>
                                <p class="text-muted fs-13 max-w-sm mx-auto">Once your tests are completed and approved by the doctor, your reports will be securely available here for download.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Footer Security Notice -->
                <div class="alert alert-info border-0 shadow-sm rounded-4 d-flex align-items-start p-4 mb-4">
                    <i class="feather-info fs-3 text-info me-3 mt-1"></i>
                    <div>
                        <h6 class="fw-bold text-info mb-1">Important Privacy Note</h6>
                        <p class="fs-13 text-dark opacity-75 mb-0">Your medical records are highly confidential. Please do not share your Medical ID with unauthorized personnel. Always ensure you click 'Logout' after reviewing your reports on a shared device.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
