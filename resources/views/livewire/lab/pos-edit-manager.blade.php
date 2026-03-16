<div>
    {{-- ======================== PAGE HEADER ======================== --}}
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10"><i class="feather-edit-3 me-2"></i>Edit Invoice — {{ $invoice->invoice_number ?? '' }}</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('lab.invoices') }}" wire:navigate>Invoices</a></li>
                <li class="breadcrumb-item">Edit</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <a href="{{ route('lab.invoices') }}" wire:navigate class="btn btn-sm btn-outline-dark"><i class="feather-arrow-left me-1"></i>Back to Invoices</a>
        </div>
    </div>

    {{-- ======================== MAIN CONTENT ======================== --}}
    <div class="main-content">

        {{-- Flash Messages --}}
        @if (session('message'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-3" role="alert">
                <i class="feather-check-circle fs-16"></i>
                <div class="fw-semibold">{{ session('message') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-3" role="alert">
                <i class="feather-alert-triangle fs-16"></i>
                <div class="fw-semibold">{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row g-3">
            {{-- ============== LEFT COLUMN ============== --}}
            <div class="col-xl-8">

                {{-- ══════ ROW 1: PATIENT (read-only) · DOCTOR · AGENT ══════ --}}
                <div class="row g-3 mb-3">
                    {{-- Patient (Read-Only in edit mode) --}}
                    <div class="col-lg-4">
                        <div class="card stretch stretch-full h-100">
                            <div class="card-header py-2">
                                <h6 class="card-title fs-12 mb-0"><i class="feather-user text-primary me-1"></i>Patient <span class="badge bg-soft-info text-info fs-10 ms-1">Locked</span></h6>
                            </div>
                            <div class="card-body py-2">
                                @if ($selectedPatient)
                                    <div class="d-flex align-items-start gap-2">
                                        <div class="avatar-text avatar-md bg-soft-primary rounded-circle flex-shrink-0">
                                            <span class="fw-bold text-primary">{{ strtoupper(substr($selectedPatient['name'], 0, 2)) }}</span>
                                        </div>
                                        <div class="flex-grow-1 min-width-0">
                                            <div class="fw-bold text-dark fs-13">{{ $selectedPatient['name'] }}</div>
                                            <div class="fs-11 text-muted"><i class="feather-phone fs-10 me-1"></i>{{ $selectedPatient['phone'] }}</div>
                                            @if($patientProfileData)
                                                <div class="d-flex flex-wrap gap-1 mt-1">
                                                    <span class="badge bg-soft-primary text-primary fs-10">{{ $patientProfileData['patient_id_string'] ?? '—' }}</span>
                                                    <span class="badge bg-soft-info text-info fs-10">{{ $patientProfileData['age'] ?? '' }} {{ $patientProfileData['age_type'] ?? 'Yrs' }}</span>
                                                    <span class="badge bg-soft-{{ ($patientProfileData['gender'] ?? '') == 'Male' ? 'primary' : (($patientProfileData['gender'] ?? '') == 'Female' ? 'danger' : 'warning') }} fs-10">{{ $patientProfileData['gender'] ?? '—' }}</span>
                                                </div>
                                            @endif
                                            @if($active_membership)
                                                <div class="mt-1">
                                                    <span class="badge bg-success fs-10"><i class="feather-award me-1 fs-10"></i>{{ $active_membership['name'] ?? '' }} · {{ number_format($active_membership['discount_percentage'] ?? 0, 0) }}% OFF</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Doctor --}}
                    <div class="col-lg-4">
                        <div class="card stretch stretch-full h-100">
                            <div class="card-header py-2">
                                <h6 class="card-title fs-12 mb-0"><i class="feather-activity text-success me-1"></i>Referring Doctor</h6>
                                @if($selectedDoctor)
                                    <button wire:click="clearDoctor" class="btn btn-sm text-danger p-0 ms-auto" title="Remove"><i class="feather-x-circle fs-14"></i></button>
                                @endif
                            </div>
                            <div class="card-body py-2">
                                @if ($selectedDoctor)
                                    <div class="d-flex align-items-start gap-2">
                                        <div class="avatar-text avatar-md bg-soft-success rounded-circle flex-shrink-0">
                                            <i class="feather-user-check text-success fs-16"></i>
                                        </div>
                                        <div class="flex-grow-1 min-width-0">
                                            <div class="fw-bold text-dark fs-13">{{ $selectedDoctor['name'] }}</div>
                                            <div class="fs-11 text-muted"><i class="feather-phone fs-10 me-1"></i>{{ $selectedDoctor['phone'] ?? '' }}</div>
                                            @if($doctorProfileData)
                                                <div class="d-flex flex-wrap gap-1 mt-1">
                                                    @if(!empty($doctorProfileData['specialization']))
                                                        <span class="badge bg-soft-success text-success fs-10">{{ $doctorProfileData['specialization'] }}</span>
                                                    @endif
                                                    <span class="badge bg-soft-warning text-warning fs-10">{{ number_format($doctorProfileData['commission_percentage'] ?? 0, 1) }}% Commission</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <div class="position-relative">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-gray-100"><i class="feather-search text-muted fs-12"></i></span>
                                            <input type="text" class="form-control" wire:model.live.debounce.300ms="doctorSearch" placeholder="Doctor Name / Phone">
                                        </div>
                                        @if (strlen($doctorSearch) >= 2)
                                            @if (!empty($doctors) && count($doctors) > 0)
                                                <div class="list-group position-absolute w-100 shadow-lg z-3 rounded-3 border" style="top:100%;left:0;">
                                                    @foreach ($doctors as $doc)
                                                        <button wire:click="selectDoctor({{ $doc->id }})" class="list-group-item list-group-item-action py-2 px-3">
                                                            <div class="fw-bold fs-12">{{ $doc->name }}</div>
                                                            <div class="text-muted fs-10">{{ $doc->doctorProfile->specialization ?? '' }} · {{ number_format($doc->doctorProfile->commission_percentage ?? 0, 1) }}%</div>
                                                        </button>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="position-absolute w-100 shadow-lg z-3 rounded-3 border bg-white p-3 text-center" style="top:100%;left:0;">
                                                    <div class="fw-bold text-muted fs-11"><i class="feather-user-x me-1"></i>No doctor found</div>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Agent --}}
                    <div class="col-lg-4">
                        <div class="card stretch stretch-full h-100">
                            <div class="card-header py-2">
                                <h6 class="card-title fs-12 mb-0"><i class="feather-briefcase text-warning me-1"></i>Agent / Franchise</h6>
                                @if($selectedAgent)
                                    <button wire:click="clearAgent" class="btn btn-sm text-danger p-0 ms-auto" title="Remove"><i class="feather-x-circle fs-14"></i></button>
                                @endif
                            </div>
                            <div class="card-body py-2">
                                @if ($selectedAgent)
                                    <div class="d-flex align-items-start gap-2">
                                        <div class="avatar-text avatar-md bg-soft-warning rounded-circle flex-shrink-0">
                                            <i class="feather-briefcase text-warning fs-16"></i>
                                        </div>
                                        <div class="flex-grow-1 min-width-0">
                                            <div class="fw-bold text-dark fs-13">{{ $selectedAgent['name'] }}</div>
                                            <div class="fs-11 text-muted"><i class="feather-phone fs-10 me-1"></i>{{ $selectedAgent['phone'] ?? '' }}</div>
                                            @if($agentProfileData)
                                                <div class="d-flex flex-wrap gap-1 mt-1">
                                                    @if(!empty($agentProfileData['agency_name']))
                                                        <span class="badge bg-soft-warning text-warning fs-10"><i class="feather-home fs-10 me-1"></i>{{ $agentProfileData['agency_name'] }}</span>
                                                    @endif
                                                    <span class="badge bg-soft-info text-info fs-10">{{ number_format($agentProfileData['commission_percentage'] ?? 0, 1) }}% Commission</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <div class="position-relative">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-gray-100"><i class="feather-search text-muted fs-12"></i></span>
                                            <input type="text" class="form-control" wire:model.live.debounce.300ms="agentSearch" placeholder="Agent Name / Phone">
                                        </div>
                                        @if (strlen($agentSearch) >= 2)
                                            @if (!empty($agents) && count($agents) > 0)
                                                <div class="list-group position-absolute w-100 shadow-lg z-3 rounded-3 border" style="top:100%;left:0;">
                                                    @foreach ($agents as $agt)
                                                        <button wire:click="selectAgent({{ $agt->id }})" class="list-group-item list-group-item-action py-2 px-3">
                                                            <div class="fw-bold fs-12">{{ $agt->name }}</div>
                                                            <div class="text-muted fs-10">{{ $agt->agentProfile->agency_name ?? '' }} · {{ number_format($agt->agentProfile->commission_percentage ?? 0, 1) }}%</div>
                                                        </button>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="position-absolute w-100 shadow-lg z-3 rounded-3 border bg-white p-3 text-center" style="top:100%;left:0;">
                                                    <div class="fw-bold text-muted fs-11"><i class="feather-user-x me-1"></i>No agent found</div>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ══════ ROW 2: LOGISTICS + STATUS ══════ --}}
                <div class="card mb-3">
                    <div class="card-header py-2">
                        <h6 class="card-title fs-12 mb-0"><i class="feather-map-pin text-info me-1"></i>Collection & Logistics</h6>
                    </div>
                    <div class="card-body py-2">
                        <div class="row g-2">
                            <div class="col-md-2 col-6">
                                <label class="form-label fw-bold fs-10 text-muted text-uppercase mb-1">Collection Center</label>
                                <select class="form-select form-select-sm" wire:model="collection_center_id">
                                    <option value="">— Select —</option>
                                    @foreach ($centers as $center)
                                        <option value="{{ $center->id }}">{{ $center->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 col-6">
                                <label class="form-label fw-bold fs-10 text-muted text-uppercase mb-1">Lab Branch</label>
                                <select class="form-select form-select-sm" wire:model="branch_id">
                                    <option value="">— Select —</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 col-6">
                                <label class="form-label fw-bold fs-10 text-muted text-uppercase mb-1">Collected At</label>
                                <select class="form-select form-select-sm" wire:model="collection_type">
                                    <option value="Center">🏥 Center</option>
                                    <option value="Home Collection">🏠 Home</option>
                                    <option value="Hospital">🏨 Hospital</option>
                                </select>
                            </div>
                            <div class="col-md-2 col-6">
                                <label class="form-label fw-bold fs-10 text-muted text-uppercase mb-1">Report Date</label>
                                <input type="date" class="form-control form-control-sm" wire:model="expected_report_date">
                            </div>
                            <div class="col-md-2 col-6">
                                <label class="form-label fw-bold fs-10 text-muted text-uppercase mb-1">Report Time</label>
                                <input type="time" class="form-control form-control-sm" wire:model="expected_report_time">
                            </div>
                            <div class="col-md-2 col-6">
                                <label class="form-label fw-bold fs-10 text-muted text-uppercase mb-1">Status</label>
                                <select class="form-select form-select-sm fw-bold" wire:model="invoiceStatus">
                                    <option value="Pending">🟡 Pending</option>
                                    <option value="Processing">🔵 Processing</option>
                                    <option value="Completed">🟢 Completed</option>
                                    <option value="Delivered">✅ Delivered</option>
                                    <option value="Cancelled">🔴 Cancelled</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ══════ ROW 3: TEST SEARCH + CART ══════ --}}
                <div class="card">
                    <div class="card-header py-2">
                        <h5 class="card-title fs-13 mb-0"><i class="feather-shopping-cart me-2 text-primary"></i>Lab Tests / Packages</h5>
                        <div class="card-header-action">
                            <span class="badge bg-primary rounded-pill fs-11">{{ count($cart) }} items · ₹{{ number_format($subtotal, 2) }}</span>
                        </div>
                    </div>
                    <div class="card-body pb-0 pt-2">
                        <div class="position-relative mb-2">
                            <div class="input-group">
                                <span class="input-group-text border" style="background:rgba(59,113,202,0.08);"><i class="feather-search text-primary"></i></span>
                                <input type="text" class="form-control border fw-semibold" style="background:rgba(59,113,202,0.08);" wire:model.live.debounce.300ms="testSearch" placeholder="Search Test Name, Profile, or Code...">
                            </div>
                            @if (!empty($tests) && count($tests) > 0)
                                <div class="list-group position-absolute w-100 shadow-lg mt-1 z-3 border rounded-3" style="max-height:280px;overflow-y:auto;">
                                    @foreach ($tests as $test)
                                        <button wire:click="addTestToCart({{ $test->id }})" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2 px-3">
                                            <div>
                                                <span class="fw-bold text-dark fs-13">{{ $test->name }}</span>
                                                @if ($test->is_package)
                                                    <span class="badge bg-primary ms-1 rounded-pill fs-10">PKG</span>
                                                @endif
                                                <div class="fs-11 text-muted">{{ $test->test_code ?? '' }} · {{ $test->department ?? 'General' }}</div>
                                            </div>
                                            <span class="fw-bold text-success fs-14">₹{{ number_format($test->mrp, 0) }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Cart Table --}}
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="ps-3 text-uppercase fs-10 text-muted fw-bold" style="width:40px;">#</th>
                                    <th class="text-uppercase fs-10 text-muted fw-bold">Test / Package</th>
                                    <th class="text-uppercase fs-10 text-muted fw-bold text-center" style="width:90px;">Type</th>
                                    <th class="text-uppercase fs-10 text-muted fw-bold text-end" style="width:100px;">MRP (₹)</th>
                                    <th class="text-uppercase fs-10 text-muted fw-bold text-center pe-3" style="width:50px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cart as $index => $item)
                                    <tr wire:click="toggleCartItemDetail({{ $index }})" style="cursor:pointer;" class="{{ in_array($index, $expandedCartItems) ? 'table-active' : '' }}">
                                        <td class="ps-3 fw-bold text-muted fs-12">{{ $index + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-1">
                                                <span class="fw-bold text-dark fs-13">{{ $item['name'] }}</span>
                                                @if(!empty($item['linked_tests']) || !empty($item['parameters']))
                                                    <i class="feather-chevron-{{ in_array($index, $expandedCartItems) ? 'up' : 'down' }} text-muted fs-11"></i>
                                                @endif
                                            </div>
                                            <div class="fs-10 text-muted">{{ $item['test_code'] ?? '' }}{{ $item['department'] ? ' · '.$item['department'] : '' }}{{ $item['sample_type'] ? ' · '.$item['sample_type'] : '' }}</div>
                                        </td>
                                        <td class="text-center">
                                            @if ($item['is_package'])
                                                <span class="badge bg-soft-primary text-primary rounded-pill fs-10">
                                                    <i class="feather-layers fs-10 me-1"></i>Package
                                                    @if(!empty($item['linked_tests'])) ({{ count($item['linked_tests']) }}) @endif
                                                </span>
                                            @else
                                                <span class="badge bg-soft-success text-success rounded-pill fs-10">Test</span>
                                            @endif
                                        </td>
                                        <td class="text-end fw-bold text-dark fs-14">₹{{ number_format($item['mrp'], 0) }}</td>
                                        <td class="text-center pe-3">
                                            <button wire:click.stop="removeFromCart({{ $index }})" class="btn btn-sm p-0 text-danger" title="Remove"><i class="feather-trash-2 fs-14"></i></button>
                                        </td>
                                    </tr>

                                    {{-- Expanded Details --}}
                                    @if(in_array($index, $expandedCartItems))
                                        @if($item['is_package'] && !empty($item['linked_tests']))
                                            <tr>
                                                <td colspan="5" class="bg-gray-50 ps-4 py-2 border-0">
                                                    <div class="fw-bold text-primary fs-11 mb-2"><i class="feather-layers me-1"></i>Included Tests ({{ count($item['linked_tests']) }}):</div>
                                                    <div class="row g-2">
                                                        @foreach($item['linked_tests'] as $lt)
                                                            <div class="col-md-6 col-lg-4">
                                                                <div class="d-flex align-items-start gap-2 p-2 bg-white rounded border">
                                                                    <div class="avatar-text avatar-sm bg-soft-primary flex-shrink-0"><i class="feather-activity text-primary fs-12"></i></div>
                                                                    <div class="flex-grow-1">
                                                                        <div class="fw-bold fs-11 text-dark">{{ $lt['name'] }}</div>
                                                                        <div class="fs-10 text-muted">{{ $lt['test_code'] ?? '' }} · ₹{{ number_format($lt['mrp'], 0) }}</div>
                                                                        @if(!empty($lt['parameters']))
                                                                            <div class="mt-1 d-flex flex-wrap gap-1">
                                                                                @foreach($lt['parameters'] as $param)
                                                                                    <span class="badge bg-gray-200 text-dark fs-9">{{ is_array($param) ? ($param['param'] ?? $param['name'] ?? '—') : $param }}{{ is_array($param) && !empty($param['unit']) ? ' ('.$param['unit'].')' : '' }}</span>
                                                                                @endforeach
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif

                                        @if(!$item['is_package'] && !empty($item['parameters']))
                                            <tr>
                                                <td colspan="5" class="bg-gray-50 ps-4 py-2 border-0">
                                                    <div class="fw-bold text-muted fs-11 mb-1"><i class="feather-list me-1"></i>Parameters ({{ count($item['parameters']) }}):</div>
                                                    <div class="d-flex flex-wrap gap-1">
                                                        @foreach($item['parameters'] as $param)
                                                            <span class="badge bg-white border text-dark fs-10 px-2 py-1"><i class="feather-check-circle text-success me-1 fs-9"></i>{{ is_array($param) ? ($param['param'] ?? $param['name'] ?? '—') : $param }}{{ is_array($param) && !empty($param['unit']) ? ' ('.$param['unit'].')' : '' }}</span>
                                                        @endforeach
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-3">
                                            <i class="feather-shopping-cart fs-4 text-muted d-block mb-1"></i>
                                            <p class="text-muted fw-medium mb-0 fs-12">Cart is empty — search above</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if (count($cart) > 0)
                                <tfoot>
                                    <tr style="background:rgba(59,113,202,0.08);">
                                        <td class="ps-3 fw-bold text-primary fs-13" colspan="3">Subtotal ({{ count($cart) }} items)</td>
                                        <td class="text-end fw-bold text-primary fs-16">₹{{ number_format($subtotal, 0) }}</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>

            {{-- ============== RIGHT COLUMN — Invoice Summary ============== --}}
            <div class="col-xl-4">
                <div class="card stretch stretch-full sticky-top" style="top:80px;">
                    <div class="card-header py-3" style="background:linear-gradient(135deg,#1a1a2e,#16213e);">
                        <h5 class="card-title text-white fs-13 mb-0"><i class="feather-edit-3 me-2"></i>Edit Invoice Summary</h5>
                        <span class="badge bg-warning text-dark ms-auto fs-10">{{ $invoice->invoice_number ?? '' }}</span>
                    </div>
                    <div class="card-body py-3">

                        {{-- Subtotal --}}
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                            <span class="text-muted fw-semibold fs-12">Cart Subtotal</span>
                            <span class="fw-bold text-dark fs-16">₹{{ number_format($subtotal, 0) }}</span>
                        </div>

                        {{-- Membership --}}
                        @if ($active_membership)
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 rounded-3 border" style="background:rgba(59,113,202,0.08);border-color:rgba(59,113,202,0.2)!important;">
                                <div>
                                    <span class="fw-bold text-primary fs-11"><i class="feather-award me-1 fs-10"></i>{{ $active_membership['name'] ?? '' }}</span>
                                    <span class="d-block fs-10 text-muted">{{ number_format($active_membership['discount_percentage'] ?? 0, 0) }}% auto-applied</span>
                                </div>
                                <span class="fw-bold fs-13" style="color:#198754;">- ₹{{ number_format($membership_discount_amt, 0) }}</span>
                            </div>
                        @endif

                        {{-- Voucher --}}
                        <div class="mb-2">
                            @if ($applied_voucher)
                                <div class="d-flex justify-content-between align-items-center p-2 rounded-3 border" style="background:rgba(25,135,84,0.08);border-color:rgba(25,135,84,0.25)!important;">
                                    <span class="fw-bold fs-11" style="color:#198754;"><i class="feather-tag me-1 fs-10"></i>{{ $applied_voucher->code }}</span>
                                    <div class="d-flex align-items-center gap-1">
                                        <span class="fw-bold fs-12" style="color:#198754;">- ₹{{ number_format($voucher_discount_amt, 0) }}</span>
                                        <button wire:click="removeVoucher" class="btn btn-sm text-danger p-0"><i class="feather-x-circle fs-14"></i></button>
                                    </div>
                                </div>
                            @else
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control text-uppercase fw-bold fs-11" wire:model="voucher_code" placeholder="VOUCHER CODE">
                                    <button wire:click="applyVoucher" class="btn btn-dark fw-bold px-3 fs-11">APPLY</button>
                                </div>
                                @error('voucher_code')
                                    <span class="text-danger fs-10 fw-semibold mt-1 d-block">{{ $message }}</span>
                                @enderror
                            @endif
                        </div>

                        {{-- Manual Discount --}}
                        <div class="p-2 rounded-3 bg-gray-100 border mb-2">
                            <label class="form-label fs-10 text-muted fw-bold text-uppercase mb-1">Manual Discount</label>
                            <div class="input-group input-group-sm">
                                <select class="form-select fw-bold" wire:model.live="manual_discount_type" style="max-width:90px;">
                                    <option value="flat">₹ Flat</option>
                                    <option value="percent">%</option>
                                </select>
                                <input type="number" class="form-control text-end fw-bold" wire:model.live.debounce.500ms="manual_discount_input" placeholder="0">
                            </div>
                            @if ($manual_discount_amt > 0)
                                <div class="text-end text-success fs-10 fw-bold mt-1">- ₹{{ number_format($manual_discount_amt, 0) }}</div>
                            @endif
                        </div>

                        {{-- Total Discount --}}
                        @if ($total_discount > 0)
                            <div class="d-flex justify-content-between align-items-center mb-1 fs-11">
                                <span class="text-muted">Total Savings</span>
                                <span class="fw-bold text-success">- ₹{{ number_format($total_discount, 0) }}</span>
                            </div>
                        @endif

                        <hr class="my-2">

                        {{-- NET PAYABLE --}}
                        <div class="d-flex justify-content-between align-items-center mb-3 p-3 rounded-3 border" style="background:rgba(59,113,202,0.08);border-color:rgba(59,113,202,0.25)!important;">
                            <span class="fs-13 fw-bold text-primary text-uppercase">NET PAYABLE</span>
                            <span class="fs-2 fw-bold text-primary">₹{{ number_format($net_payable, 0) }}</span>
                        </div>

                        {{-- Payment --}}
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                            <h6 class="fw-bold text-dark fs-11 text-uppercase mb-0"><i class="feather-credit-card me-1"></i>Payment</h6>
                        </div>

                        @foreach ($payments as $index => $payment)
                            <div class="d-flex gap-2 mb-2 align-items-center">
                                <select class="form-select form-select-sm fw-medium" wire:model.live="payments.{{ $index }}.mode_id" style="width:40%;">
                                    <option value="">Mode</option>
                                    @foreach ($paymentModes as $mode)
                                        <option value="{{ $mode->id }}">{{ $mode->name }}</option>
                                    @endforeach
                                </select>
                                <input type="number" class="form-control form-control-sm fw-bold text-end" wire:model.live.debounce.500ms="payments.{{ $index }}.amount" placeholder="₹ Amount">
                                @if (count($payments) > 1)
                                    <button wire:click="removePaymentRow({{ $index }})" class="btn btn-sm text-danger p-0"><i class="feather-trash-2 fs-14"></i></button>
                                @endif
                            </div>
                        @endforeach
                        <button wire:click="addPaymentRow" class="btn btn-sm btn-outline-primary w-100 mt-1 fs-10 fw-bold"><i class="feather-plus me-1"></i>Split Payment</button>

                        {{-- Due --}}
                        <div class="d-flex justify-content-between align-items-center mt-3 p-2 rounded-3 border" style="background:{{ $due_amount > 0 ? 'rgba(220,53,69,0.08)' : 'rgba(25,135,84,0.08)' }};border-color:{{ $due_amount > 0 ? 'rgba(220,53,69,0.25)' : 'rgba(25,135,84,0.25)' }}!important;">
                            <span class="fw-bold fs-12" style="color:{{ $due_amount > 0 ? '#dc3545' : '#198754' }};">{{ $due_amount > 0 ? 'Balance Due' : 'Fully Paid' }}</span>
                            <span class="fw-bold fs-2" style="color:{{ $due_amount > 0 ? '#dc3545' : '#198754' }};">₹{{ number_format($due_amount, 0) }}</span>
                        </div>

                        {{-- Update Button --}}
                        <button wire:click="updateBill" class="btn btn-warning w-100 py-3 fw-bold mt-3 fs-13" style="color:#000;">
                            <span wire:loading.remove wire:target="updateBill"><i class="feather-save me-1"></i>UPDATE INVOICE</span>
                            <span wire:loading wire:target="updateBill"><span class="spinner-border spinner-border-sm me-1"></span>UPDATING...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
