<div>
    <style>
        :root {
            --db-primary: #4f46e5;
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

        .report-card {
            background: var(--db-card-bg);
            border: 1px solid var(--db-border);
            border-radius: 1.25rem;
            padding: 1.5rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .report-card:hover {
            transform: translateX(10px);
            border-color: var(--db-primary);
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.08);
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 100px;
            font-size: 0.7rem;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
    </style>

    <div class="db-container">
        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-3">
            <div>
                <h2 class="fw-black mb-1" style="letter-spacing: -1.5px; font-size: 2.25rem;">My Diagnostic History</h2>
                <div class="d-flex align-items-center gap-3">
                    <span class="text-muted small fw-bold text-uppercase tracking-widest">
                        <i class="feather-file-text me-1"></i> {{ $reports->count() }} Total Records Found
                    </span>
                </div>
            </div>
            <button class="btn btn-outline-primary fw-900 px-4 rounded-pill shadow-sm" onclick="window.location.reload();">
                <i class="feather-refresh-cw me-2"></i>REFRESH LIST
            </button>
        </div>

        <div class="row">
            <div class="col-12">
                @forelse($reports as $report)
                    <div class="report-card animate__animated animate__fadeInUp" style="animation-delay: {{ $loop->index * 0.1 }}s">
                        <div class="d-flex align-items-center gap-4">
                            <div class="avatar-text avatar-lg bg-soft-primary text-primary rounded-4 fw-black">
                                <i class="feather-file-text"></i>
                            </div>
                            <div>
                                <div class="fs-12 fw-black text-muted text-uppercase tracking-widest mb-1">Invoice #{{ $report->invoice->invoice_number }}</div>
                                <h5 class="fw-900 text-dark mb-1">Diagnostic Report</h5>
                                <div class="d-flex align-items-center gap-3 fs-13 text-muted fw-medium">
                                    <span><i class="feather-calendar me-1"></i> {{ \Carbon\Carbon::parse($report->created_at)->format('d M, Y') }}</span>
                                    <span><i class="feather-clock me-1"></i> {{ \Carbon\Carbon::parse($report->created_at)->format('h:i A') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-5">
                            <div class="text-center">
                                @if(strtolower($report->status) === 'approved')
                                    <span class="status-badge bg-soft-success text-success border border-success border-opacity-10">
                                        <i class="feather-check-circle me-1"></i> Ready for Download
                                    </span>
                                @elseif(strtolower($report->status) === 'pending' || strtolower($report->status) === 'draft')
                                    <span class="status-badge bg-soft-warning text-warning border border-warning border-opacity-10">
                                        <i class="feather-clock me-1"></i> Processing...
                                    </span>
                                @else
                                    <span class="status-badge bg-soft-primary text-primary border border-primary border-opacity-10">
                                        {{ strtoupper($report->status) }}
                                    </span>
                                @endif
                            </div>

                            <div class="d-flex gap-2">
                                <a href="{{ route('portal.invoice.download', $report->invoice->id) }}" target="_blank" 
                                   class="btn btn-soft-primary fw-900 fs-11 px-4 py-2 rounded-pill">
                                    <i class="feather-printer me-2"></i>PRINT BILL
                                </a>
                                
                                @if(strtolower($report->status) === 'approved')
                                    <a href="{{ route('portal.report.download', $report->invoice_id) }}" target="_blank" 
                                       class="btn btn-primary fw-900 fs-11 px-4 py-2 rounded-pill shadow-sm border-0">
                                        <i class="feather-download me-2"></i>GET REPORT
                                    </a>
                                @else
                                    <button class="btn btn-light fw-900 fs-11 px-4 py-2 rounded-pill border text-muted" disabled>
                                        <i class="feather-lock me-2"></i>LOCKED
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="card border-0 shadow-sm rounded-4 py-5 text-center">
                        <div class="bg-light d-inline-flex p-4 rounded-circle mb-4 border border-dashed">
                            <i class="feather-inbox fs-1 text-muted opacity-50"></i>
                        </div>
                        <h4 class="fw-900 text-dark mb-2">No records found</h4>
                        <p class="text-muted fs-14 max-w-sm mx-auto fw-medium">Your medical history will appear here once your samples are processed and approved by our medical experts.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="mt-5 p-4 rounded-4 bg-soft-info border-0 shadow-sm d-flex align-items-start gap-3">
            <div class="avatar-text avatar-sm bg-info text-white rounded-circle flex-shrink-0">
                <i class="feather-help-circle"></i>
            </div>
            <div>
                <h6 class="fw-900 text-dark mb-1">Facing issues downloading your reports?</h6>
                <p class="fs-13 text-muted mb-0 fw-medium">Our helpdesk is standing by to assist you. Please contact us at <b>{{ $patient->company->phone ?? '' }}</b> or visit the lab directly.</p>
            </div>
        </div>
    </div>
</div>
