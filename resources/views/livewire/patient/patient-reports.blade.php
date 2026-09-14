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
            margin-bottom: 1.25rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .report-card:hover {
            transform: translateX(6px);
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
                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
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

                            <div class="d-flex align-items-center gap-3 gap-md-5 flex-wrap">
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
                                    @php
                                        $restrictUnpaid = \App\Models\Configuration::getFor('restrict_unpaid_reports', '0', $report->invoice->company_id) === '1';
                                        $isPaid = strtolower($report->invoice->payment_status) === 'paid';
                                        $isRestricted = $restrictUnpaid && !$isPaid;
                                    @endphp

                                    @if(strtolower($report->status) === 'approved')
                                        @if($isRestricted)
                                            <button class="btn btn-light fw-900 fs-11 px-4 py-2 rounded-pill shadow-sm border text-danger" disabled title="Clear pending dues to view report">
                                                <i class="feather-lock me-2"></i>PAYMENT PENDING
                                            </button>
                                        @else
                                            <a href="{{ route('portal.report.download', $report->invoice_id) }}" target="_blank" 
                                               class="btn btn-primary fw-900 fs-11 px-4 py-2 rounded-pill shadow-sm border-0">
                                                <i class="feather-download me-2"></i>GET REPORT
                                            </a>
                                        @endif
                                    @else
                                        <button class="btn btn-light fw-900 fs-11 px-4 py-2 rounded-pill border text-muted" disabled>
                                            <i class="feather-lock me-2"></i>LOCKED
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Home Collection Ribbon if booking has Home Collection --}}
                        @if($report->invoice?->homeCollection)
                            @php
                                $hCol = $report->invoice->homeCollection;
                                $hPhlebo = $hCol->phlebotomist;
                            @endphp
                            <div class="p-3 rounded-3 bg-light border border-light d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="badge bg-soft-info text-info rounded-pill px-3 py-1 fs-11 fw-bold">
                                        <i class="feather-truck me-1"></i>Home Sample Collection
                                    </span>
                                    <span class="badge bg-soft-{{ $hCol->status_color }} text-{{ $hCol->status_color }} rounded-pill px-2 py-1 fs-11 fw-bold">
                                        <i class="{{ $hCol->status_badge_icon }} me-1"></i>{{ $hCol->status }}
                                    </span>
                                    @if($hPhlebo)
                                        <span class="text-muted fs-12 fw-medium">
                                            Phlebo: <strong class="text-dark">{{ $hPhlebo->name }}</strong>
                                            @if($hPhlebo->phone)
                                                <a href="tel:{{ $hPhlebo->phone }}" class="text-primary ms-1 fw-bold">
                                                    <i class="feather-phone"></i> {{ $hPhlebo->phone }}
                                                </a>
                                            @endif
                                        </span>
                                    @else
                                        <span class="text-muted fs-12">
                                            <i class="feather-clock me-1 text-warning"></i>Phlebotomist Assignment Pending
                                        </span>
                                    @endif
                                    <span class="text-muted fs-12">
                                        • <i class="feather-calendar me-1"></i>{{ $hCol->scheduled_slot_label }}
                                    </span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <a href="{{ $hCol->google_maps_link }}" target="_blank" class="btn btn-xs btn-outline-secondary rounded-pill px-3 py-1 fs-11 fw-semibold">
                                        <i class="feather-map-pin me-1"></i>Location
                                    </a>
                                    <button wire:click="viewTracking({{ $hCol->id }})" class="btn btn-xs btn-outline-primary rounded-pill px-3 py-1 fs-11 fw-bold">
                                        <i class="feather-compass me-1"></i>Track Timeline
                                    </button>
                                </div>
                            </div>
                        @endif
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
