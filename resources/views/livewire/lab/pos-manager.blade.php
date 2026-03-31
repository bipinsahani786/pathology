<div>
    <style>
        .search-dropdown {
            max-height: 250px; /* Shows approx 5 items before scrolling */
            overflow-y: auto;
            z-index: 1060;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            border: 1px solid var(--bs-border-color, #e0e0e0);
            background: var(--bs-card-bg, #fff);
            width: 100%;
        }
        .search-dropdown .list-group-item {
            border-left: 0;
            border-right: 0;
            padding: 10px 15px;
            cursor: pointer;
            transition: all 0.2s;
            background: transparent;
            color: inherit;
        }
        .search-dropdown .list-group-item:hover {
            background-color: rgba(var(--bs-primary-rgb), 0.08);
            color: var(--bs-primary);
        }
        .search-dropdown::-webkit-scrollbar {
            width: 6px;
        }
        .search-dropdown::-webkit-scrollbar-thumb {
            background-color: #ddd;
            border-radius: 10px;
        }
        html.app-skin-dark .search-dropdown::-webkit-scrollbar-thumb {
            background-color: #323248;
        }
        .pos-unit-price-input {
            background-color: rgba(var(--bs-primary-rgb), 0.05);
        }
        .pos-membership-box, .pos-voucher-box {
            background: rgba(var(--bs-primary-rgb), 0.08);
        }
    </style>
    {{-- ======================== PAGE HEADER ======================== --}}
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Point of Sale — Billing</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                <li class="breadcrumb-item">Lab</li>
                <li class="breadcrumb-item">POS</li>
            </ul>
        </div>
    </div>

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
            {{-- ============== LEFT COLUMN (Fullwidth search + cart) ============== --}}
            <div class="col-xl-8">

                {{-- ══════ ROW 1: PATIENT · DOCTOR · AGENT — wide cards ══════ --}}
                <div class="row g-3 mb-3">
                    {{-- Patient --}}
                    <div class="col-lg-4">
                        <div class="card stretch stretch-full h-100">
                            <div class="card-header py-2">
                                <h6 class="card-title fs-12 mb-0"><i class="feather-user text-primary me-1"></i>Patient <span class="text-danger">*</span></h6>
                                @if($selectedPatient)
                                    <button wire:click="clearPatient" class="btn btn-sm text-danger p-0 ms-auto" title="Remove"><i class="feather-x-circle fs-14"></i></button>
                                @endif
                            </div>
                            <div class="card-body py-2">
                                @if ($selectedPatient)
                                    <div class="d-flex align-items-start gap-2">
                                        <div class="avatar-text avatar-md bg-soft-primary rounded-circle flex-shrink-0">
                                            <span class="fw-bold text-primary">{{ strtoupper(substr($selectedPatient['name'], 0, 2)) }}</span>
                                        </div>
                                        <div class="flex-grow-1 min-width-0">
                                            <h6 class="mb-0 fw-bold">{{ $selectedPatient['name'] ?? '' }} <span class="badge bg-soft-info text-info ms-1">{{ $selectedPatient['formatted_id'] ?? '' }}</span></h6>
                                            <div class="fs-11 text-muted"><i class="feather-phone me-1 fs-10"></i>{{ $selectedPatient['phone'] ?? '' }}</div>
                                            @if($patientProfileData)
                                                <div class="d-flex flex-wrap gap-1 mt-1">
                                                    <span class="badge bg-soft-info text-info fs-10">{{ $patientProfileData['age'] ?? '' }} {{ $patientProfileData['age_type'] ?? 'Yrs' }}</span>
                                                    <span class="badge bg-soft-{{ ($patientProfileData['gender'] ?? '') == 'Male' ? 'primary' : (($patientProfileData['gender'] ?? '') == 'Female' ? 'danger' : 'warning') }} fs-10">{{ $patientProfileData['gender'] ?? '—' }}</span>
                                                    @if(!empty($patientProfileData['blood_group']))
                                                        <span class="badge bg-soft-danger text-danger fs-10">{{ $patientProfileData['blood_group'] }}</span>
                                                    @endif
                                                </div>
                                                @if(!empty($patientProfileData['address']))
                                                    <div class="fs-10 text-muted mt-1"><i class="feather-map-pin fs-10 me-1"></i>{{ Str::limit($patientProfileData['address'], 40) }}</div>
                                                @endif
                                            @endif
                                            @if($active_membership)
                                                <div class="mt-1">
                                                    <span class="badge bg-success fs-10"><i class="feather-award me-1 fs-10"></i>{{ $active_membership['name'] ?? '' }} · {{ number_format($active_membership['discount_percentage'] ?? 0, 0) }}% OFF</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <div class="position-relative" x-data="{ open: false }" wire:key="patient-search-box" @click.away="open = false" @focusin="open = true; $wire.set('activeSearchField', 'patient')">
                                        <div class="d-flex gap-2 mb-1">
                                            <div class="input-group input-group-sm flex-grow-1">
                                                <span class="input-group-text bg-light"><i class="feather-search text-muted fs-12"></i></span>
                                                <input type="text" class="form-control" wire:model.live.debounce.300ms="patientSearch" 
                                                    @focus="open = true"
                                                    onclick="this.select()"
                                                    placeholder="Phone / Name">
                                            </div>
                                            @can('create patients')
                                                <button wire:click="$set('isPatientModalOpen', true)" @click="open = false" class="btn btn-sm btn-primary px-2" title="New Patient"><i class="feather-user-plus fs-12"></i></button>
                                            @endcan
                                        </div>
                                        <div x-show="open" x-transition.opacity.duration.150ms style="display:none;">
                                            @if (!empty($patients) && count($patients) > 0)
                                                <div class="list-group position-absolute shadow-lg z-3 rounded-3 search-dropdown" style="top:100%;left:0;">
                                                    @foreach ($patients as $pt)
                                                        <button wire:click="selectPatient({{ $pt->id }})" @click="open = false" class="list-group-item list-group-item-action py-2 px-3">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                        <div class="fw-bold fs-12">{{ $pt->name }} <span class="badge bg-soft-info text-info ms-1">{{ $pt->formatted_id }}</span></div>
                                                                        <div class="text-muted fs-10">{{ $pt->phone }}</div>
                                                                </div>
                                                                <span class="badge bg-soft-info text-info fs-10">{{ $pt->patientProfile->age ?? '' }}{{ $pt->patientProfile->age_type == 'Years' ? 'Y' : ($pt->patientProfile->age_type == 'Months' ? 'M' : 'D') }}/{{ substr($pt->patientProfile->gender ?? '', 0, 1) }}</span>
                                                            </div>
                                                        </button>
                                                    @endforeach
                                                </div>
                                            @elseif(!empty($patientSearch))
                                                <div class="position-absolute shadow-lg z-3 rounded-3 search-dropdown p-3 text-center" style="top:100%;left:0;">
                                                    <i class="feather-user-x text-muted fs-3 d-block mb-1"></i>
                                                    <div class="fw-bold text-muted fs-11">No patient found for "{{ $patientSearch }}"</div>
                                                    @can('create patients')
                                                        <button wire:click="$set('isPatientModalOpen', true)" @click="open = false" class="btn btn-sm btn-primary mt-2 fw-bold fs-10"><i class="feather-user-plus me-1"></i>Register New</button>
                                                    @endcan
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
                                                    @if(!empty($doctorProfileData['clinic_name']))
                                                        <span class="badge bg-soft-info text-info fs-10"><i class="feather-home fs-10 me-1"></i>{{ $doctorProfileData['clinic_name'] }}</span>
                                                    @endif
                                                    <span class="badge bg-soft-warning text-warning fs-10">{{ number_format($doctorProfileData['commission_percentage'] ?? 0, 1) }}% Commission</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <div class="position-relative" x-data="{ open: false }" wire:key="doctor-search-box" @click.away="open = false" @focusin="open = true; $wire.set('activeSearchField', 'doctor')">
                                        <div class="d-flex gap-2 mb-1">
                                            <div class="input-group input-group-sm flex-grow-1">
                                                <span class="input-group-text bg-light"><i class="feather-search text-muted fs-12"></i></span>
                                                <input type="text" class="form-control" wire:model.live.debounce.300ms="doctorSearch" 
                                                    @focus="open = true"
                                                    onclick="this.select()"
                                                    placeholder="Doctor Name / Phone">
                                            </div>
                                            @can('create doctors')
                                                <button wire:click="$set('isDoctorModalOpen', true)" @click="open = false" class="btn btn-sm btn-success px-2" title="New Doctor"><i class="feather-plus fs-12"></i></button>
                                            @endcan
                                        </div>
                                        <div x-show="open" x-transition.opacity.duration.150ms style="display:none;">
                                            @if (!empty($doctors) && count($doctors) > 0)
                                                <div class="list-group position-absolute shadow-lg z-3 rounded-3 search-dropdown" style="top:100%;left:0;">
                                                    @foreach ($doctors as $doc)
                                                        <button wire:click="selectDoctor({{ $doc->id }})" @click="open = false" class="list-group-item list-group-item-action py-2 px-3">
                                                            <div class="fw-bold fs-12">{{ $doc->name }}</div>
                                                            <div class="text-muted fs-10">{{ $doc->doctorProfile->specialization ?? '' }} · {{ number_format($doc->doctorProfile->commission_percentage ?? 0, 1) }}%</div>
                                                        </button>
                                                    @endforeach
                                                </div>
                                            @elseif(!empty($doctorSearch))
                                                <div class="position-absolute shadow-lg z-3 rounded-3 search-dropdown p-3 text-center" style="top:100%;left:0;">
                                                    <div class="fw-bold text-muted fs-11"><i class="feather-user-x me-1"></i>No doctor found for "{{ $doctorSearch }}"</div>
                                                    @can('create doctors')
                                                        <button wire:click="$set('isDoctorModalOpen', true)" @click="open = false" class="btn btn-sm btn-success mt-1 fw-bold fs-10"><i class="feather-plus me-1"></i>Add New</button>
                                                    @endcan
                                                </div>
                                            @endif
                                        </div>
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
                                    <div class="position-relative" x-data="{ open: false }" wire:key="agent-search-box" @click.away="open = false" @focusin="open = true; $wire.set('activeSearchField', 'agent')">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light"><i class="feather-search text-muted fs-12"></i></span>
                                            <input type="text" class="form-control" wire:model.live.debounce.300ms="agentSearch" 
                                                @focus="open = true"
                                                onclick="this.select()"
                                                placeholder="Agent Name / Phone">
                                            @can('create agents')
                                                <button wire:click="$set('isAgentModalOpen', true)" @click="open = false" class="btn btn-sm btn-warning px-2" title="New Agent"><i class="feather-plus fs-12"></i></button>
                                            @endcan
                                        </div>
                                        <div x-show="open" x-transition.opacity.duration.150ms style="display:none;">
                                            @if (!empty($agents) && count($agents) > 0)
                                                <div class="list-group position-absolute shadow-lg z-3 rounded-3 search-dropdown" style="top:100%;left:0;">
                                                    @foreach ($agents as $agt)
                                                        <button wire:click="selectAgent({{ $agt->id }})" @click="open = false" class="list-group-item list-group-item-action py-2 px-3">
                                                            <div class="fw-bold fs-12">{{ $agt->name }}</div>
                                                            <div class="text-muted fs-10">{{ $agt->agentProfile->agency_name ?? '' }} · {{ number_format($agt->agentProfile->commission_percentage ?? 0, 1) }}%</div>
                                                        </button>
                                                    @endforeach
                                                </div>
                                            @elseif(!empty($agentSearch))
                                                <div class="position-absolute shadow-lg z-3 rounded-3 search-dropdown p-3 text-center" style="top:100%;left:0;">
                                                    <div class="fw-bold text-muted fs-11"><i class="feather-user-x me-1"></i>No agent found for "{{ $agentSearch }}"</div>
                                                    @can('create agents')
                                                        <button wire:click="$set('isAgentModalOpen', true)" @click="open = false" class="btn btn-sm btn-warning mt-1 fw-bold fs-10"><i class="feather-plus me-1"></i>Add New</button>
                                                    @endcan
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ══════ ROW 2: LOGISTICS — Collection Center · Branch · Collection At · Report ══════ --}}
                <div class="card mb-3">
                    <div class="card-header py-2">
                        <h6 class="card-title fs-12 mb-0"><i class="feather-map-pin text-info me-1"></i>Collection & Logistics</h6>
                    </div>
                    <div class="card-body py-2">
                        <div class="row g-2 mb-2">
                            <div class="col-md-4 col-6">
                                <label class="form-label fw-bold fs-10 text-muted text-uppercase mb-1">Collection Center <span class="text-danger">*</span></label>
                                <select class="form-select form-select-sm @error('collection_center_id') is-invalid @enderror" wire:model.live="collection_center_id" {{ auth()->user()->hasRole('collection_center') ? 'disabled' : '' }}>
                                    <option value="">— Select —</option>
                                    @foreach ($centers as $center)
                                        <option value="{{ $center->id }}">
                                            {{ $center->name }} {{ $center->is_main_lab ? '⭐' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('collection_center_id')
                                    <div class="invalid-feedback fs-10 fw-bold">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 col-6">
                                <label class="form-label fw-bold fs-10 text-muted text-uppercase mb-1">Lab Branch</label>
                                @php
                                    $restrictAccess = \App\Models\Configuration::getFor('restrict_branch_access', '1') === '1';
                                    $canSwitch = auth()->user()->hasRole('lab_admin') || auth()->user()->hasRole('super_admin') || !$restrictAccess;
                                @endphp
                                <select class="form-select form-select-sm @error('branch_id') is-invalid @enderror" 
                                    wire:model.live="branch_id"
                                    @if(!$canSwitch) disabled @endif>
                                    @if($canSwitch) <option value="">— Select Branch —</option> @endif
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                                @error('branch_id')
                                    <div class="invalid-feedback fs-10 fw-bold">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 col-6">
                                <label class="form-label fw-bold fs-10 text-muted text-uppercase mb-1">Collected At</label>
                                <select class="form-select form-select-sm" wire:model="collection_type">
                                    <option value="Center">🏥 Center</option>
                                    <option value="Home Collection">🏠 Home Collection</option>
                                    <option value="Hospital">🏨 Hospital</option>
                                </select>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-4 col-6">
                                <label class="form-label fw-bold fs-10 text-muted text-uppercase mb-1">Sample Received At</label>
                                <input type="datetime-local" class="form-control form-control-sm" wire:model="sample_received_at">
                            </div>
                            <div class="col-md-4 col-6">
                                <label class="form-label fw-bold fs-10 text-muted text-uppercase mb-1">Report Date</label>
                                <input type="date" class="form-control form-control-sm" wire:model="expected_report_date">
                            </div>
                            <div class="col-md-4 col-6">
                                <label class="form-label fw-bold fs-10 text-muted text-uppercase mb-1">Report Time</label>
                                <input type="time" class="form-control form-control-sm" wire:model="expected_report_time">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ══════ ROW 3: TEST SEARCH + CART ══════ --}}
                <div class="card">
                    <div class="card-header py-2">
                        <h5 class="card-title fs-13 mb-0"><i class="feather-shopping-cart me-2 text-primary"></i>Lab Tests / Packages</h5>
                        <div class="card-header-action">
                            <span class="badge bg-primary rounded-pill fs-11">
                                <span wire:loading.remove wire:target="removeFromCart, addTestToCart, calculateTotals, cart">
                                    {{ count($cart) }} items · ₹{{ number_format($subtotal, 2) }}
                                </span>
                                <span wire:loading wire:target="removeFromCart, addTestToCart, calculateTotals, cart">
                                    <span class="spinner-border spinner-border-sm me-1" role="status"></span> Updating...
                                </span>
                            </span>
                        </div>
                    </div>
                    <div class="card-body pb-0 pt-2">
                        <div class="position-relative mb-2" x-data="{ open: false }" wire:key="test-search-box" @click.away="open = false" @focusin="open = true; $wire.set('activeSearchField', 'test')">
                            <div class="input-group search-group shadow-sm">
                                <span class="input-group-text"><i class="feather-search text-primary"></i></span>
                                <input type="text" class="form-control fw-semibold" wire:model.live.debounce.300ms="testSearch" 
                                    @focus="open = true"
                                    onclick="this.select()"
                                    placeholder="Search Test Name, Profile, or Code...">
                            </div>
                            <div x-show="open" x-transition.opacity.duration.150ms style="display:none;">
                                @if(!empty($tests) && count($tests) > 0)
                                    <div class="list-group position-absolute shadow-lg mt-1 z-3 rounded-3 search-dropdown" style="top:100%;left:0;">
                                        @foreach ($tests as $test)
                                            <button wire:click="addTestToCart({{ $test->id }})" @click="open = false" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2 px-3">
                                                <div>
                                                    <span class="fw-bold text-dark fs-13">{{ $test->name }}</span>
                                                    @if ($test->is_package)
                                                        <span class="badge bg-primary ms-1 rounded-pill fs-10">PKG</span>
                                                    @endif
                                                    <div class="fs-11 text-muted">{{ $test->test_code ?? '' }} · {{ $test->department ?? 'General' }} · {{ $test->sample_type ?? 'Blood' }}</div>
                                                </div>
                                                <span class="fw-bold text-success fs-14">₹{{ number_format($test->mrp, 0) }}</span>
                                            </button>
                                        @endforeach
                                    </div>
                                @elseif(!empty($testSearch))
                                    <div class="position-absolute shadow-lg z-3 rounded-3 search-dropdown p-3 text-center" style="top:100%;left:0;">
                                        <div class="fw-bold text-muted fs-11"><i class="feather-search me-1"></i>No test found for "{{ $testSearch }}"</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Cart Table --}}
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="bg-light">
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
                                                    @if(!empty($item['linked_tests']))
                                                        ({{ count($item['linked_tests']) }})
                                                    @endif
                                                </span>
                                            @else
                                                <span class="badge bg-soft-success text-success rounded-pill fs-10">Test</span>
                                            @endif
                                        </td>
                                        <td class="text-end" style="width: 140px;">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text bg-light border-end-0 fs-11" style="padding: 0 8px;">₹</span>
                                                <input type="number" class="form-control form-control-sm fw-bold border-start-0 text-end pos-unit-price-input" 
                                                    wire:model.live.debounce.300ms="cart.{{ $index }}.price" 
                                                    wire:change="calculateTotals"
                                                    onclick="event.stopPropagation(); this.select()">
                                            </div>
                                            <div class="fs-10 text-muted mt-1">MRP: <del>₹{{ number_format($item['mrp'], 0) }}</del></div>
                                        </td>
                                        <td class="text-center pe-3">
                                            <button wire:click.stop="removeFromCart({{ $index }})" class="btn btn-sm p-0 text-danger" title="Remove"><i class="feather-trash-2 fs-14"></i></button>
                                        </td>
                                    </tr>

                                    {{-- Optimized Expanded Details --}}
                                    @if(in_array($index, $expandedCartItems))
                                        <tr>
                                            <td colspan="5" class="bg-gray-50 border-0 pt-0 pb-3 ps-5 pe-3">
                                                <div class="card border-dashed bg-white shadow-none mb-0 overflow-hidden">
                                                    <div class="card-body p-2">
                                                        @if($item['is_package'] && !empty($item['linked_tests']))
                                                            <div class="fw-bold text-muted fs-11 mb-2 text-uppercase"><i class="feather-layers me-1"></i>Included Tests ({{ count($item['linked_tests']) }}):</div>
                                                            <div class="row g-2">
                                                                @foreach($item['linked_tests'] as $lt)
                                                                    <div class="col-md-6 col-lg-4">
                                                                        <div class="d-flex align-items-start gap-2 p-2 border rounded bg-light bg-opacity-50">
                                                                            <div class="avatar-text avatar-xs bg-soft-primary rounded-circle mt-1"><i class="feather-check text-primary fs-10"></i></div>
                                                                            <div class="flex-grow-1 min-width-0">
                                                                                <div class="fw-bold fs-11 text-dark text-truncate">{{ $lt['name'] }}</div>
                                                                                <div class="fs-10 text-muted">{{ $lt['test_code'] ?? 'N/A' }} · ₹{{ number_format($lt['mrp'], 0) }}</div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                        
                                                        @if(!empty($item['parameters']))
                                                            <div class="@if($item['is_package']) mt-3 @endif">
                                                                <div class="fw-bold text-muted fs-11 mb-2 text-uppercase"><i class="feather-activity me-1"></i>Parameters ({{ count($item['parameters']) }}):</div>
                                                                <div class="d-flex flex-wrap gap-1">
                                                                    @foreach($item['parameters'] as $param)
                                                                        <span class="badge bg-soft-secondary text-secondary fs-10 border rounded-pill px-2 py-1">
                                                                            {{ is_array($param) ? ($param['param'] ?? $param['name'] ?? '—') : $param }}
                                                                            @if(is_array($param) && !empty($param['unit'])) <small class="ms-1">({{ $param['unit'] }})</small> @endif
                                                                        </span>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
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
                                    <tr class="pos-cart-footer" style="background:rgba(13,110,253,0.05); border-top: 2px solid rgba(13,110,253,0.1);">
                                        <td class="ps-4 py-3 fw-bold text-primary fs-14" colspan="3">
                                            Subtotal ({{ count($cart) }} items)
                                            <span wire:loading wire:target="cart" class="spinner-border spinner-border-sm text-primary ms-2" role="status"></span>
                                        </td>
                                        <td class="text-end py-3 fw-bold text-primary fs-18">₹{{ number_format($subtotal, 2) }}</td>
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
                    <div class="card-header bg-dark py-3">
                        <h5 class="card-title text-white fs-13 mb-0"><i class="feather-file-text me-2"></i>Invoice Summary</h5>
                    </div>
                    <div class="card-body py-3">

                        {{-- Subtotal --}}
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                            <span class="text-muted fw-semibold fs-12">Cart Subtotal</span>
                            <span class="fw-bold text-dark fs-16">₹{{ number_format($subtotal, 0) }}</span>
                        </div>

                        {{-- Membership --}}
                        @if ($active_membership)
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 rounded-3 border pos-membership-box" style="border-color:rgba(59,113,202,0.2)!important;">
                                <div>
                                    <span class="fw-bold text-primary fs-11"><i class="feather-award me-1 fs-10"></i>{{ $active_membership['name'] ?? '' }}</span>
                                    <span class="d-block fs-10 text-muted">{{ number_format($active_membership['discount_percentage'] ?? 0, 0) }}% {{ $membership_fee > 0 ? 'applied (new purchase)' : 'auto-applied' }}</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fw-bold fs-13" style="color:#198754;">- ₹{{ number_format($membership_discount_amt, 0) }}</span>
                                    <button wire:click="removeMembership" class="btn btn-sm text-danger p-0" title="Remove Membership"><i class="feather-x-circle fs-14"></i></button>
                                </div>
                            </div>
                        @elseif($selectedPatient)
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 rounded-3 border" style="background:rgba(255,193,7,0.1);border-color:rgba(255,193,7,0.3)!important;">
                                <span class="fw-bold fs-11" style="color:#8a6d00;"><i class="feather-award me-1 fs-10"></i>No Membership</span>
                                @can('create marketing')
                                    <button wire:click="$set('isMembershipModalOpen', true)" class="btn btn-sm btn-warning fw-bold fs-10 px-2 py-1" style="color:#000;"><i class="feather-plus fs-10 me-1"></i>Buy</button>
                                @endcan
                                @cannot('create marketing')
                                    <span class="badge bg-soft-secondary text-secondary fs-10 px-2 py-1">Contact Admin</span>
                                @endcannot
                            </div>
                        @endif

                        {{-- Membership Fee (when bought via POS) --}}
                        @if ($membership_fee > 0)
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 rounded-3 border" style="background:rgba(124,58,237,0.08);border-color:rgba(124,58,237,0.2)!important;">
                                <div>
                                    <span class="fw-bold fs-11" style="color:#7c3aed;"><i class="feather-credit-card me-1 fs-10"></i>Membership Fee</span>
                                    <span class="d-block fs-10 text-muted">{{ $active_membership['name'] ?? '' }} plan purchased</span>
                                </div>
                                <span class="fw-bold fs-13" style="color:#7c3aed;">+ ₹{{ number_format($membership_fee, 0) }}</span>
                            </div>
                        @endif

                        {{-- Voucher --}}
                        <div class="mb-2">
                            @if ($applied_voucher)
                                <div class="d-flex justify-content-between align-items-center p-2 rounded-3 border pos-voucher-box" style="border-color:rgba(25,135,84,0.25)!important;">
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
                            @can('edit settings')
                                <button wire:click="$set('isPaymentModeModalOpen', true)" class="btn btn-sm btn-outline-dark fs-10 py-0 px-2"><i class="feather-plus fs-10 me-1"></i>Mode</button>
                            @endcan
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

                        {{-- Overpayment Error --}}
                        @if ($overpaymentError)
                            <div class="alert alert-danger py-2 mt-2 mb-0 d-flex align-items-center gap-2">
                                <i class="feather-alert-octagon fs-14"></i>
                                <span class="fs-11 fw-bold">Payment exceeds Net Payable!</span>
                            </div>
                        @endif

                        {{-- Due --}}
                        <div class="d-flex justify-content-between align-items-center mt-3 p-2 rounded-3 border" style="background:{{ $due_amount > 0 ? 'rgba(220,53,69,0.08)' : 'rgba(25,135,84,0.08)' }};border-color:{{ $due_amount > 0 ? 'rgba(220,53,69,0.25)' : 'rgba(25,135,84,0.25)' }}!important;">
                            <span class="fw-bold fs-12" style="color:{{ $due_amount > 0 ? '#dc3545' : '#198754' }};">{{ $due_amount > 0 ? 'Balance Due' : 'Fully Paid' }}</span>
                            <span class="fw-bold fs-2" style="color:{{ $due_amount > 0 ? '#dc3545' : '#198754' }};">₹{{ number_format($due_amount, 0) }}</span>
                        </div>

                        {{-- Generate --}}
                        <button wire:click="generateBill" class="btn btn-primary w-100 py-3 fw-bold mt-3 fs-13" 
                            @if($overpaymentError) disabled @endif>
                            <span wire:loading.remove wire:target="generateBill"><i class="feather-check-circle me-1"></i>GENERATE INVOICE</span>
                            <span wire:loading wire:target="generateBill"><span class="spinner-border spinner-border-sm me-1"></span>PROCESSING...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ======================== MODALS ======================== --}}

    {{-- Quick Add Patient --}}
    @if ($isPatientModalOpen)
        <div class="modal-backdrop fade show" style="z-index:1050;"></div>
        <div class="modal fade show d-block" tabindex="-1" style="z-index:1055;">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header"><h5 class="modal-title fs-14"><i class="feather-user-plus text-primary me-2"></i>Quick Register Patient</h5><button wire:click="$set('isPatientModalOpen', false)" class="btn-close"></button></div>
                    <div class="modal-body">
                        @if($modalError)<div class="alert alert-danger py-2 fs-12 mb-3"><i class="feather-alert-circle me-1"></i>{{ $modalError }}</div>@endif
                        @if($errors->any())<div class="alert alert-danger py-2 fs-12 mb-3">@foreach($errors->all() as $err)<div><i class="feather-x-circle me-1"></i>{{ $err }}</div>@endforeach</div>@endif
                        <div class="row g-3">
                            <div class="col-12"><label class="form-label fw-semibold fs-11">Name <span class="text-danger">*</span></label><input type="text" class="form-control" wire:model="new_name" placeholder="Full Name"></div>
                            <div class="col-12"><label class="form-label fw-semibold fs-11">Mobile</label><input type="text" class="form-control" wire:model="new_phone" placeholder="10 Digit" maxlength="10"></div>
                            <div class="col-6"><label class="form-label fw-semibold fs-11">Age <span class="text-danger">*</span></label><input type="number" class="form-control" wire:model="new_age" placeholder="Years"></div>
                            <div class="col-6"><label class="form-label fw-semibold fs-11">Gender</label><select class="form-select" wire:model="new_gender"><option value="Male">Male</option><option value="Female">Female</option><option value="Other">Other</option></select></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button wire:click="$set('isPatientModalOpen', false)" class="btn btn-light">Cancel</button>
                        <button wire:click="quickAddPatient" class="btn btn-primary fw-bold"><span wire:loading.remove wire:target="quickAddPatient"><i class="feather-save me-1"></i>Save & Select</span><span wire:loading wire:target="quickAddPatient"><span class="spinner-border spinner-border-sm me-1"></span>Saving...</span></button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Quick Add Agent --}}
    @if ($isAgentModalOpen)
        <div class="modal-backdrop fade show" style="z-index:1050;"></div>
        <div class="modal fade show d-block" tabindex="-1" style="z-index:1055;">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header">
                        <h5 class="modal-title fs-14"><i class="feather-briefcase text-warning me-2"></i>Quick Add Agent</h5>
                        <button wire:click="$set('isAgentModalOpen', false)" class="btn-close"></button>
                    </div>
                    <div class="modal-body">
                        @if($modalError)<div class="alert alert-danger py-2 fs-12 mb-3"><i class="feather-alert-circle me-1"></i>{{ $modalError }}</div>@endif
                        @if($errors->any())<div class="alert alert-danger py-2 fs-12 mb-3">@foreach($errors->all() as $err)<div><i class="feather-x-circle me-1"></i>{{ $err }}</div>@endforeach</div>@endif
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold fs-11">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" wire:model="new_agent_name" placeholder="Full Name">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold fs-11">Mobile (Optional)</label>
                                <input type="text" class="form-control" wire:model="new_agent_phone" placeholder="10 Digit (optional)" maxlength="10">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold fs-11">Agency Name</label>
                                <input type="text" class="form-control" wire:model="new_agent_agency" placeholder="Agency/Shop Name">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold fs-11">Commission (%)</label>
                                <input type="number" class="form-control" wire:model="new_agent_commission" placeholder="e.g. 10">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button wire:click="$set('isAgentModalOpen', false)" class="btn btn-light">Cancel</button>
                        <button wire:click="quickAddAgent" class="btn btn-warning fw-bold text-dark"><i class="feather-save me-1"></i>Save & Select</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Quick Add Doctor --}}
    @if ($isDoctorModalOpen)
        <div class="modal-backdrop fade show" style="z-index:1050;"></div>
        <div class="modal fade show d-block" tabindex="-1" style="z-index:1055;">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header"><h5 class="modal-title fs-14"><i class="feather-activity text-success me-2"></i>Quick Add Doctor</h5><button wire:click="$set('isDoctorModalOpen', false)" class="btn-close"></button></div>
                    <div class="modal-body">
                        @if($modalError)<div class="alert alert-danger py-2 fs-12 mb-3"><i class="feather-alert-circle me-1"></i>{{ $modalError }}</div>@endif
                        @if($errors->any())<div class="alert alert-danger py-2 fs-12 mb-3">@foreach($errors->all() as $err)<div><i class="feather-x-circle me-1"></i>{{ $err }}</div>@endforeach</div>@endif
                        <div class="row g-3">
                            <div class="col-12"><label class="form-label fw-semibold fs-11">Name <span class="text-danger">*</span></label><div class="input-group"><span class="input-group-text fw-bold">Dr.</span><input type="text" class="form-control" wire:model="new_doc_name" placeholder="Full Name"></div></div>
                            <div class="col-12"><label class="form-label fw-semibold fs-11">Mobile (Optional)</label><input type="text" class="form-control" wire:model="new_doc_phone" placeholder="10 Digit (optional)" maxlength="10"></div>
                            <div class="col-12"><label class="form-label fw-semibold fs-11">Commission (%)</label><input type="number" class="form-control" wire:model="new_doc_commission" placeholder="e.g. 20"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button wire:click="$set('isDoctorModalOpen', false)" class="btn btn-light">Cancel</button>
                        <button wire:click="quickAddDoctor" class="btn btn-success fw-bold"><i class="feather-save me-1"></i>Save & Select</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Membership Purchase --}}
    @if ($isMembershipModalOpen)
        <div class="modal-backdrop fade show" style="z-index:1050;"></div>
        <div class="modal fade show d-block" tabindex="-1" style="z-index:1055;">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header"><h5 class="modal-title fs-14"><i class="feather-award text-warning me-2"></i>Buy Membership for {{ $selectedPatient['name'] ?? 'Patient' }}</h5><button wire:click="$set('isMembershipModalOpen', false)" class="btn-close"></button></div>
                    <div class="modal-body">
                        @if($modalError)<div class="alert alert-danger py-2 fs-12 mb-3"><i class="feather-alert-circle me-1"></i>{{ $modalError }}</div>@endif
                        <div class="row g-3">
                            @forelse ($memberships as $mem)
                                <div class="col-md-4 col-sm-6">
                                    <div wire:click="$set('selectedMembershipId', {{ $mem->id }})" class="card h-100 border-2 {{ $selectedMembershipId == $mem->id ? 'border-primary shadow' : 'border-gray-200' }}" style="cursor:pointer;transition:all .2s;">
                                        <div class="card-body text-center p-3">
                                            <div class="avatar-text avatar-lg mx-auto mb-2" style="background-color:{{ $mem->color_code ?? '#3b71ca' }}20;"><i class="feather-award fs-3" style="color:{{ $mem->color_code ?? '#3b71ca' }};"></i></div>
                                            <h6 class="fw-bold mb-1 fs-13" style="color:#1a1a2e;">{{ $mem->name }}</h6>
                                            <div class="fs-2 fw-bold mb-1" style="color:#1a1a2e;">₹{{ number_format($mem->price, 0) }}</div>
                                            <div class="badge fs-11 mb-1 px-2 py-1" style="background:#198754;color:#fff;">{{ number_format($mem->discount_percentage, 0) }}% OFF on all tests</div>
                                            <div class="fs-11" style="color:#555;">Valid {{ $mem->validity_days }} days</div>
                                            @if($mem->description)
                                                <div class="fs-10 mt-1" style="color:#777;">{{ $mem->description }}</div>
                                            @endif
                                            @if($selectedMembershipId == $mem->id)<div class="mt-1"><i class="feather-check-circle text-primary fs-4"></i></div>@endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-center py-3"><i class="feather-info fs-1 d-block mb-2" style="color:#ccc;"></i><p style="color:#888;">No plans configured.</p></div>
                            @endforelse

                            {{-- Membership Price Note --}}
                            @if($selectedMembershipId)
                                @php $selectedMem = $memberships->firstWhere('id', $selectedMembershipId); @endphp
                                @if($selectedMem)
                                    <div class="col-12">
                                        <div class="alert mb-0 py-2 d-flex align-items-center gap-2" style="background:rgba(59,113,202,0.08);border:1px solid rgba(59,113,202,0.2);color:#1a1a2e;">
                                            <i class="feather-info text-primary"></i>
                                            <span class="fs-11"><strong>₹{{ number_format($selectedMem->price, 0) }}</strong> membership fee will be added to this bill. Patient will get <strong>{{ number_format($selectedMem->discount_percentage, 0) }}% discount</strong> on all tests.</span>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button wire:click="$set('isMembershipModalOpen', false)" class="btn btn-light">Cancel</button>
                        <button wire:click="purchaseMembership" class="btn btn-primary fw-bold" {{ !$selectedMembershipId ? 'disabled' : '' }}><i class="feather-check me-1"></i>Activate & Apply</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Quick Add Payment Mode --}}
    @if ($isPaymentModeModalOpen)
        <div class="modal-backdrop fade show" style="z-index:1050;"></div>
        <div class="modal fade show d-block" tabindex="-1" style="z-index:1055;">
            <div class="modal-dialog modal-dialog-centered modal-sm modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header"><h5 class="modal-title fs-14"><i class="feather-credit-card text-primary me-2"></i>Add Payment Mode</h5><button wire:click="$set('isPaymentModeModalOpen', false)" class="btn-close"></button></div>
                    <div class="modal-body">
                        @if($modalError)<div class="alert alert-danger py-2 fs-12 mb-3"><i class="feather-alert-circle me-1"></i>{{ $modalError }}</div>@endif
                        <label class="form-label fw-semibold fs-11">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" wire:model="new_payment_mode_name" placeholder="e.g. Cash, UPI, Card">
                    </div>
                    <div class="modal-footer">
                        <button wire:click="$set('isPaymentModeModalOpen', false)" class="btn btn-light btn-sm">Cancel</button>
                        <button wire:click="quickAddPaymentMode" class="btn btn-primary btn-sm fw-bold"><i class="feather-save me-1"></i>Save</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
