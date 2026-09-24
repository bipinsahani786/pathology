<div>
    {{-- Page Header --}}
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="text-dark fw-bold mb-1">Online Web Bookings</h5>
                <p class="fs-12 text-muted mb-0">Manage incoming appointments, home collections, and test bookings from your external website.</p>
            </div>
            <ul class="breadcrumb d-none d-md-flex ms-3">
                <li class="breadcrumb-item"><a href="{{ route('lab.dashboard') }}" wire:navigate class="text-muted">Home</a></li>
                <li class="breadcrumb-item text-primary fw-medium">Web Bookings</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto d-flex align-items-center gap-2">
            <a href="{{ route('lab.settings') }}?tab=website_api" wire:navigate class="btn btn-outline-primary btn-sm">
                <i class="feather-key me-1"></i> API Credentials & Setup
            </a>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="main-content">
        {{-- Flash Messages --}}
        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="feather-check-circle fs-5 me-2"></i>
                    <div>{{ session('message') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="feather-alert-triangle fs-5 me-2"></i>
                    <div>{{ session('error') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Stats Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-md-3 col-6">
                <div class="card mb-0 shadow-sm border-0 rounded-3">
                    <div class="card-body py-3 d-flex align-items-center gap-3">
                        <div class="avatar-text avatar-lg rounded-3 bg-soft-primary text-primary">
                            <i class="feather-globe fs-20"></i>
                        </div>
                        <div>
                            <div class="fs-11 fw-bold text-muted text-uppercase">Total Bookings</div>
                            <div class="fs-4 fw-bold text-dark">{{ number_format($stats['total']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card mb-0 shadow-sm border-0 rounded-3">
                    <div class="card-body py-3 d-flex align-items-center gap-3">
                        <div class="avatar-text avatar-lg rounded-3 bg-soft-warning text-warning">
                            <i class="feather-clock fs-20"></i>
                        </div>
                        <div>
                            <div class="fs-11 fw-bold text-muted text-uppercase">Pending Review</div>
                            <div class="fs-4 fw-bold text-warning">{{ number_format($stats['pending']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card mb-0 shadow-sm border-0 rounded-3">
                    <div class="card-body py-3 d-flex align-items-center gap-3">
                        <div class="avatar-text avatar-lg rounded-3 bg-soft-info text-info">
                            <i class="feather-check-circle fs-20"></i>
                        </div>
                        <div>
                            <div class="fs-11 fw-bold text-muted text-uppercase">Confirmed</div>
                            <div class="fs-4 fw-bold text-info">{{ number_format($stats['confirmed']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card mb-0 shadow-sm border-0 rounded-3">
                    <div class="card-body py-3 d-flex align-items-center gap-3">
                        <div class="avatar-text avatar-lg rounded-3 bg-soft-success text-success">
                            <i class="feather-file-text fs-20"></i>
                        </div>
                        <div>
                            <div class="fs-11 fw-bold text-muted text-uppercase">Converted To Invoice</div>
                            <div class="fs-4 fw-bold text-success">{{ number_format($stats['converted']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters & Search Card --}}
        <div class="card mb-4 border-0 shadow-sm rounded-3">
            <div class="card-body py-3">
                <div class="row g-2 align-items-center">
                    <div class="col-md-4">
                        <div class="input-group shadow-sm rounded-3">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="feather-search text-primary"></i></span>
                            <input type="text" class="form-control border-start-0 ps-0 fs-12" wire:model.live.debounce.300ms="search" placeholder="Search by patient name, phone, or reference...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select fs-12 shadow-sm rounded-3" wire:model.live="statusFilter">
                            <option value="all">All Statuses</option>
                            <option value="pending">⏳ Pending Review</option>
                            <option value="confirmed">👍 Confirmed</option>
                            <option value="converted_to_invoice">🧾 Converted to Bill</option>
                            <option value="cancelled">❌ Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select fs-12 shadow-sm rounded-3" wire:model.live="collectionTypeFilter">
                            <option value="all">All Visit Types</option>
                            <option value="lab_visit">🏥 Lab Visit</option>
                            <option value="home_collection">🏠 Home Collection</option>
                        </select>
                    </div>
                    @if($branches->count() > 1)
                    <div class="col-md-2">
                        <select class="form-select fs-12 shadow-sm rounded-3" wire:model.live="branchFilter">
                            <option value="all">All Branches</option>
                            @foreach($branches as $b)
                                <option value="{{ $b->id }}">{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="col-md-2 ms-auto text-end">
                        <button wire:click="clearFilters" class="btn btn-outline-secondary btn-sm w-100 py-2" title="Reset Filters">
                            <i class="feather-rotate-ccw me-1"></i> Reset Filters
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bookings Table Card --}}
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr class="fs-11 fw-bold text-uppercase text-muted">
                                <th class="ps-3 py-3" style="min-width: 140px;">Booking Ref</th>
                                <th style="min-width: 180px;">Patient Details</th>
                                <th style="min-width: 150px;">Visit Type</th>
                                <th style="min-width: 220px;">Selected Tests</th>
                                <th style="min-width: 110px;">Amount</th>
                                <th class="text-center" style="min-width: 110px;">Status</th>
                                <th class="pe-3 text-end" style="min-width: 170px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bookings as $booking)
                                <tr>
                                    <td class="ps-3">
                                        <div class="fw-bold text-dark font-monospace fs-12">{{ $booking->booking_reference }}</div>
                                        <div class="text-muted fs-11">
                                            <i class="feather-calendar me-1"></i>{{ $booking->created_at->format('d M Y, h:i A') }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark fs-13">{{ $booking->patient_name }}</div>
                                        <div class="text-muted fs-11">
                                            <a href="tel:{{ $booking->patient_phone }}" class="text-muted text-decoration-none">
                                                <i class="feather-phone me-1"></i>{{ $booking->patient_phone }}
                                            </a>
                                            @if($booking->patient_gender || $booking->patient_age)
                                                <span class="badge bg-light text-muted border ms-1">
                                                    {{ ucfirst($booking->patient_gender ?? '') }} {{ $booking->patient_age ? $booking->patient_age . ' ' . ($booking->patient_age_unit ?? 'yr') : '' }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($booking->collection_type === 'home_collection')
                                            <span class="badge bg-soft-primary text-primary px-2 py-1 fs-11 fw-semibold">
                                                <i class="feather-home me-1"></i> Home Collection
                                            </span>
                                            @if($booking->preferred_date)
                                                <div class="text-dark fs-11 mt-1">
                                                    <i class="feather-clock text-muted me-1"></i>{{ $booking->preferred_date->format('d M Y') }}
                                                    @if($booking->preferred_time_slot)
                                                        <span class="text-muted">({{ $booking->preferred_time_slot }})</span>
                                                    @endif
                                                </div>
                                            @endif
                                        @else
                                            <span class="badge bg-soft-secondary text-secondary px-2 py-1 fs-11 fw-semibold">
                                                <i class="feather-map-pin me-1"></i> Lab Visit
                                            </span>
                                            @if($booking->preferred_date)
                                                <div class="text-dark fs-11 mt-1">
                                                    <i class="feather-calendar text-muted me-1"></i>{{ $booking->preferred_date->format('d M Y') }}
                                                </div>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $items = is_array($booking->items) ? $booking->items : json_decode($booking->items, true) ?? [];
                                        @endphp
                                        <div class="d-flex align-items-center gap-1 mb-1">
                                            <span class="badge bg-soft-dark text-dark border px-2 py-1 fs-10 fw-bold">{{ count($items) }} Test{{ count($items) > 1 ? 's' : '' }}</span>
                                        </div>
                                        <div class="text-truncate text-muted fs-11" style="max-width: 250px;" title="{{ collect($items)->pluck('name')->implode(', ') }}">
                                            {{ collect($items)->pluck('name')->implode(', ') }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark fs-13">₹{{ number_format($booking->total_amount, 2) }}</div>
                                    </td>
                                    <td class="text-center">
                                        @if($booking->status === 'pending')
                                            <span class="badge bg-soft-warning text-warning px-2 py-1 rounded-pill fs-11"><i class="feather-clock me-1"></i>Pending</span>
                                        @elseif($booking->status === 'confirmed')
                                            <span class="badge bg-soft-info text-info px-2 py-1 rounded-pill fs-11"><i class="feather-check-circle me-1"></i>Confirmed</span>
                                        @elseif($booking->status === 'converted_to_invoice')
                                            <span class="badge bg-soft-success text-success px-2 py-1 rounded-pill fs-11"><i class="feather-file-text me-1"></i>Invoiced</span>
                                        @elseif($booking->status === 'cancelled')
                                            <span class="badge bg-soft-danger text-danger px-2 py-1 rounded-pill fs-11"><i class="feather-x-circle me-1"></i>Cancelled</span>
                                        @endif
                                    </td>
                                    <td class="pe-3 text-end">
                                        <div class="d-flex align-items-center justify-content-end gap-1">
                                            <button wire:click="viewBooking({{ $booking->id }})" class="btn btn-sm btn-light border" title="View Booking Details">
                                                <i class="feather-eye text-muted"></i>
                                            </button>
                                            
                                            @if($booking->status === 'pending')
                                                <button wire:click="updateStatus({{ $booking->id }}, 'confirmed')" class="btn btn-sm btn-outline-info" title="Mark as Confirmed">
                                                    <i class="feather-check"></i>
                                                </button>
                                            @endif

                                            @if($booking->status !== 'converted_to_invoice' && $booking->status !== 'cancelled')
                                                <button wire:click="convertToInvoice({{ $booking->id }})" wire:confirm="Convert this booking into a bill now?" class="btn btn-sm btn-primary fw-semibold px-2" title="Accept and Create Invoice">
                                                    <i class="feather-file-plus me-1"></i> Generate Bill
                                                </button>
                                            @elseif($booking->invoice_id)
                                                <a href="{{ route('lab.pos.summary', $booking->invoice_id) }}" wire:navigate class="btn btn-sm btn-outline-success fw-semibold px-2" title="View Generated Bill">
                                                    <i class="feather-arrow-up-right me-1"></i> View Bill
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="avatar-text avatar-xl bg-soft-primary text-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
                                            <i class="feather-globe fs-28"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1">No Web Bookings Found</h5>
                                        <p class="text-muted fs-12 mb-3" style="max-width: 420px; margin: 0 auto;">
                                            When patients book test appointments on your external website or mobile application, they will instantly appear here for staff verification.
                                        </p>
                                        <a href="{{ route('lab.settings') }}?tab=website_api" wire:navigate class="btn btn-outline-primary btn-sm px-3">
                                            <i class="feather-code me-1"></i> Check Website API Credentials
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($bookings->hasPages())
                    <div class="p-3 border-top d-flex justify-content-end">
                        {{ $bookings->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Detail Modal --}}
    @if($isDetailModalOpen && $selectedBooking)
        <div class="modal-backdrop fade show" style="z-index: 1040;"></div>
        <div class="modal fade show d-block" tabindex="-1" role="dialog" style="z-index: 1050;">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="modal-header bg-light border-bottom p-3 px-4">
                        <div>
                            <h5 class="modal-title fw-bold text-dark">
                                <i class="feather-calendar text-primary me-2"></i>Booking #{{ $selectedBooking['booking_reference'] }}
                            </h5>
                            <span class="fs-11 text-muted">Received on {{ \Carbon\Carbon::parse($selectedBooking['created_at'])->format('d M Y, h:i A') }}</span>
                        </div>
                        <button type="button" wire:click="closeDetailModal" class="btn-close shadow-none"></button>
                    </div>

                    <div class="modal-body p-4 bg-white">
                        <div class="row g-3 mb-4">
                            {{-- Patient Profile --}}
                            <div class="col-md-6">
                                <div class="p-3 border rounded-3 bg-light h-100">
                                    <h6 class="fw-bold text-dark fs-12 mb-3 text-uppercase border-bottom pb-2">
                                        <i class="feather-user text-primary me-2"></i>Patient Profile
                                    </h6>
                                    <div class="mb-2">
                                        <span class="text-muted fs-11 d-block">Full Name</span>
                                        <span class="fw-bold text-dark fs-13">{{ $selectedBooking['patient_name'] }}</span>
                                    </div>
                                    <div class="mb-2">
                                        <span class="text-muted fs-11 d-block">Phone Number</span>
                                        <a href="tel:{{ $selectedBooking['patient_phone'] }}" class="fw-bold text-primary fs-12 text-decoration-none">
                                            <i class="feather-phone me-1"></i>{{ $selectedBooking['patient_phone'] }}
                                        </a>
                                    </div>
                                    @if(!empty($selectedBooking['patient_email']))
                                    <div class="mb-2">
                                        <span class="text-muted fs-11 d-block">Email Address</span>
                                        <span class="fs-12 text-dark">{{ $selectedBooking['patient_email'] }}</span>
                                    </div>
                                    @endif
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <span class="text-muted fs-11 d-block">Gender</span>
                                            <span class="fw-semibold text-dark fs-12">{{ ucfirst($selectedBooking['patient_gender'] ?? 'N/A') }}</span>
                                        </div>
                                        <div class="col-6">
                                            <span class="text-muted fs-11 d-block">Age</span>
                                            <span class="fw-semibold text-dark fs-12">{{ $selectedBooking['patient_age'] ? $selectedBooking['patient_age'] . ' ' . ($selectedBooking['patient_age_unit'] ?? 'years') : 'N/A' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Appointment Logistics --}}
                            <div class="col-md-6">
                                <div class="p-3 border rounded-3 bg-light h-100">
                                    <h6 class="fw-bold text-dark fs-12 mb-3 text-uppercase border-bottom pb-2">
                                        <i class="feather-map-pin text-primary me-2"></i>Visit Logistics
                                    </h6>
                                    <div class="mb-2">
                                        <span class="text-muted fs-11 d-block">Collection Type</span>
                                        @if($selectedBooking['collection_type'] === 'home_collection')
                                            <span class="badge bg-primary px-2 py-1 fs-11"><i class="feather-home me-1"></i> Home Collection</span>
                                        @else
                                            <span class="badge bg-secondary px-2 py-1 fs-11"><i class="feather-map-pin me-1"></i> Lab Visit</span>
                                        @endif
                                    </div>
                                    <div class="row g-2 mb-2">
                                        <div class="col-6">
                                            <span class="text-muted fs-11 d-block">Preferred Date</span>
                                            <span class="fw-bold text-dark fs-12">{{ $selectedBooking['preferred_date'] ? \Carbon\Carbon::parse($selectedBooking['preferred_date'])->format('d M Y') : 'Flexible' }}</span>
                                        </div>
                                        <div class="col-6">
                                            <span class="text-muted fs-11 d-block">Preferred Time Slot</span>
                                            <span class="fw-bold text-dark fs-12">{{ $selectedBooking['preferred_time_slot'] ?: 'Flexible' }}</span>
                                        </div>
                                    </div>
                                    @if(!empty($selectedBooking['collection_address']))
                                    <div class="mb-2">
                                        <span class="text-muted fs-11 d-block">Collection Address</span>
                                        <span class="fs-12 text-dark">{{ $selectedBooking['collection_address'] }}</span>
                                    </div>
                                    @endif
                                    @if(!empty($selectedBooking['notes']))
                                    <div>
                                        <span class="text-muted fs-11 d-block">Patient Remarks</span>
                                        <span class="fs-12 text-dark fst-italic">"{{ $selectedBooking['notes'] }}"</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Tests Ordered Table --}}
                        <div class="border rounded-3 overflow-hidden mb-2">
                            <div class="bg-light p-2 px-3 border-bottom d-flex align-items-center justify-content-between">
                                <h6 class="fw-bold text-dark fs-12 mb-0 text-uppercase"><i class="feather-list text-primary me-2"></i>Selected Tests & Packages</h6>
                                <span class="badge bg-white text-dark border fs-10">Total Items: {{ count(is_array($selectedBooking['items']) ? $selectedBooking['items'] : json_decode($selectedBooking['items'], true) ?? []) }}</span>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover align-middle fs-12 mb-0">
                                    <thead class="table-light text-muted fs-11">
                                        <tr>
                                            <th class="ps-3">Item Name</th>
                                            <th>Type</th>
                                            <th>Sample Type</th>
                                            <th class="pe-3 text-end">Price</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $modalItems = is_array($selectedBooking['items']) ? $selectedBooking['items'] : json_decode($selectedBooking['items'], true) ?? [];
                                        @endphp
                                        @foreach($modalItems as $item)
                                            <tr>
                                                <td class="ps-3 fw-bold text-dark">{{ $item['name'] ?? '' }}</td>
                                                <td><span class="badge bg-light text-dark border">{{ ucfirst($item['type'] ?? 'test') }}</span></td>
                                                <td class="text-muted">{{ $item['sample_type'] ?? 'Blood' }}</td>
                                                <td class="pe-3 text-end fw-bold text-dark">₹{{ number_format((float) ($item['price'] ?? 0), 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-light">
                                            <td colspan="3" class="ps-3 text-end fw-bold">Total Estimated Amount:</td>
                                            <td class="pe-3 text-end fw-bold text-primary fs-14">₹{{ number_format((float) $selectedBooking['total_amount'], 2) }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light border-top p-3 px-4 d-flex justify-content-between">
                        <div>
                            @if($selectedBooking['status'] === 'pending')
                                <button type="button" wire:click="updateStatus({{ $selectedBooking['id'] }}, 'cancelled')" wire:confirm="Are you sure you want to cancel this booking?" class="btn btn-outline-danger btn-sm">
                                    <i class="feather-x me-1"></i> Cancel Booking
                                </button>
                                <button type="button" wire:click="updateStatus({{ $selectedBooking['id'] }}, 'confirmed')" class="btn btn-outline-info btn-sm">
                                    <i class="feather-check me-1"></i> Mark Confirmed
                                </button>
                            @endif
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" wire:click="closeDetailModal" class="btn btn-light btn-sm px-3">Close</button>
                            @if($selectedBooking['status'] !== 'converted_to_invoice' && $selectedBooking['status'] !== 'cancelled')
                                <button type="button" wire:click="convertToInvoice({{ $selectedBooking['id'] }})" class="btn btn-primary btn-sm px-4 fw-bold">
                                    <i class="feather-file-text me-1"></i> Accept & Generate Bill
                                </button>
                            @elseif(!empty($selectedBooking['invoice_id']))
                                <a href="{{ route('lab.pos.summary', $selectedBooking['invoice_id']) }}" wire:navigate class="btn btn-success btn-sm px-4 fw-bold">
                                    <i class="feather-arrow-up-right me-1"></i> View Generated Bill
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
