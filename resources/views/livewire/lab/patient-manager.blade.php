<div>
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="text-dark fw-bold">Patient Management</h5>
                <p class="fs-13 text-muted mb-0">Register and manage patients.</p>
            </div>
            <ul class="breadcrumb d-none d-md-flex ms-3">
                <li class="breadcrumb-item"><a href="{{ route('lab.dashboard') }}" wire:navigate class="text-muted">Home</a></li>
                <li class="breadcrumb-item text-primary fw-medium">Patients</li>
            </ul>
        </div>
        <div class="page-header-right">
            <button wire:click="create" class="btn btn-primary px-4">
                <i class="feather-user-plus me-2"></i> Add New Patient
            </button>
        </div>
    </div>

    <div class="main-content">
        
        @if (session()->has('message'))
            <div class="alert alert-success border-0 shadow-sm rounded-3 d-flex align-items-center py-3 alert-dismissible fade show">
                <i class="feather-check-circle fs-4 me-2"></i>
                <strong>{{ session('message') }}</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-danger border-0 shadow-sm rounded-3 d-flex align-items-center py-3 alert-dismissible fade show">
                <i class="feather-alert-triangle fs-4 me-2"></i>
                <strong>{{ session('error') }}</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card stretch stretch-full border-0 shadow-sm rounded-4 overflow-hidden">
            
            <div class="card-header bg-white py-3 border-bottom-0">
                <div class="row g-3">
                    <div class="col-md-8">
                        <div class="input-group search-group shadow-sm">
                            <span class="input-group-text">
                                <i class="feather-search text-primary"></i>
                            </span>
                            <input type="text" wire:model.live.debounce.300ms="searchTerm" 
                                class="form-control" 
                                placeholder="Search by name, phone, or PAT-ID...">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive border-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light fs-11 fw-bold text-uppercase text-muted">
                            <tr>
                                <th class="ps-4 py-3">Patient Details</th>
                                <th class="py-3">Demographics</th>
                                <th class="py-3">Address / Contact</th>
                                <th class="text-center pe-4 py-3" style="width: 120px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($patients as $patient)
                                <tr wire:key="patient-{{ $patient->id }}" class="border-bottom border-light">
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="bg-soft-primary text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold fs-5 shadow-sm" style="width: 45px; height: 45px;">
                                                {{ strtoupper(substr($patient->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark fs-14">{{ $patient->name }}</div>
                                                <div class="fs-12 mt-1">
                                                    <span class="badge bg-light text-primary border border-primary border-opacity-25">{{ $patient->patientProfile->patient_id_string ?? 'N/A' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fs-13 text-dark fw-medium">
                                            {{ $patient->patientProfile->age ?? 'N/A' }} {{ $patient->patientProfile->age_type ?? 'Yrs' }}, 
                                            {{ $patient->patientProfile->gender ?? 'N/A' }}
                                        </div>
                                        @if(!empty($patient->patientProfile->blood_group))
                                            <div class="fs-12 text-danger mt-1 fw-bold"><i class="feather-droplet me-1"></i>Blood: {{ $patient->patientProfile->blood_group }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fs-13 text-dark fw-medium mb-1"><i class="feather-phone me-1 text-muted"></i>{{ $patient->phone }}</div>
                                        <div class="fs-12 text-muted text-truncate" style="max-width: 200px;">
                                            <i class="feather-map-pin me-1"></i>{{ $patient->patientProfile->address ?? 'No address provided' }}
                                        </div>
                                    </td>
                                    <td class="text-center pe-4">
                                        <div class="d-flex justify-content-center gap-2">
                                            <button wire:click="edit({{ $patient->id }})" class="btn btn-sm btn-light border text-primary shadow-sm rounded align-center-btn transition-all hover-primary" title="Edit Profile">
                                                <i class="feather-edit-2 fs-14"></i>
                                            </button>
                                            <button wire:click="delete({{ $patient->id }})" wire:confirm="Warning: This will delete the patient and all associated data. Continue?" class="btn btn-sm btn-light border text-danger shadow-sm rounded align-center-btn transition-all hover-danger" title="Delete Patient">
                                                <i class="feather-trash-2 fs-14"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div class="text-muted mb-3"><i class="feather-users" style="font-size: 3.5rem; opacity: 0.5;"></i></div>
                                        <h6 class="fw-bold text-dark">No Patients Found</h6>
                                        <p class="text-muted fs-13">Start registering patients to build your database.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white border-top border-light py-3">
                {{ $patients->links() }}
            </div>
        </div>
    </div>

    @if ($isModalOpen)
        <div class="modal-backdrop fade show" style="z-index: 1040;"></div>
        <div class="modal fade show d-block" tabindex="-1" style="z-index: 1050;">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg rounded-4">

                    <div class="modal-header bg-light border-bottom p-4">
                        <h5 class="modal-title fw-bold text-dark d-flex align-items-center">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                                <i class="feather-user-plus fs-5"></i>
                            </div>
                            {{ $user_id ? 'Update Patient Details' : 'Register New Patient' }}
                        </h5>
                        <button type="button" wire:click="closeModal" class="btn-close shadow-none"></button>
                    </div>

                    <form wire:submit.prevent="store">
                        <div class="modal-body p-4 bg-white">
                        <div class="row g-4">
                            <div class="col-12"><h6 class="fw-bold text-primary mb-0 border-bottom pb-2">Basic Information</h6></div>
                            
                            <div class="col-md-6">
                                <label class="form-label fs-12 fw-bold text-muted text-uppercase">Full Name *</label>
                                <input type="text" class="form-control fw-medium text-dark" wire:model="name" placeholder="e.g. Rahul Kumar">
                                @error('name') <span class="text-danger fs-11 fw-bold">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fs-12 fw-bold text-muted text-uppercase">Mobile Number *</label>
                                <input type="number" class="form-control" wire:model="phone" placeholder="10-digit mobile number">
                                @error('phone') <span class="text-danger fs-11 fw-bold">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-12 mt-4"><h6 class="fw-bold text-primary mb-0 border-bottom pb-2">Demographics & Medical</h6></div>

                            <div class="col-md-4">
                                <label class="form-label fs-12 fw-bold text-muted text-uppercase">Age *</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" wire:model="age" placeholder="Age">
                                    <select class="form-select bg-light" wire:model="age_type" style="max-width: 90px;">
                                        <option value="Years">Yrs</option>
                                        <option value="Months">Mos</option>
                                        <option value="Days">Dys</option>
                                    </select>
                                </div>
                                @error('age') <span class="text-danger fs-11 fw-bold d-block">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-12 fw-bold text-muted text-uppercase">Gender *</label>
                                <select class="form-select" wire:model="gender">
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                                @error('gender') <span class="text-danger fs-11 fw-bold">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-12 fw-bold text-muted text-uppercase">Blood Group</label>
                                <select class="form-select" wire:model="blood_group">
                                    <option value="">Select...</option>
                                    <option value="A+">A+</option>
                                    <option value="A-">A-</option>
                                    <option value="B+">B+</option>
                                    <option value="B-">B-</option>
                                    <option value="O+">O+</option>
                                    <option value="O-">O-</option>
                                    <option value="AB+">AB+</option>
                                    <option value="AB-">AB-</option>
                                </select>
                                @error('blood_group') <span class="text-danger fs-11 fw-bold">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fs-12 fw-bold text-muted text-uppercase">Full Address</label>
                                <textarea class="form-control" wire:model="address" rows="2" placeholder="Enter complete patient address..."></textarea>
                                @error('address') <span class="text-danger fs-11 fw-bold">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light border-top p-3 d-flex justify-content-end gap-2">
                        <button type="button" wire:click="closeModal" class="btn btn-light border px-4 fw-medium shadow-sm">Cancel</button>
                        <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm d-flex align-items-center">
                            <div wire:loading.remove wire:target="store"><i class="feather-save me-2"></i> Save Patient</div>
                            <div wire:loading wire:target="store"><span class="spinner-border spinner-border-sm me-2" role="status"></span> Saving...</div>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <style>
        .bg-soft-primary { background-color: rgba(59, 113, 202, 0.08) !important; }
        .text-primary { color: #3b71ca !important; }
        
        .transition-all { transition: all 0.2s ease-in-out; }
        .hover-lift:hover { transform: translateY(-1px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; }
        
        input.form-control:focus, select.form-select:focus, textarea.form-control:focus {
            background-color: #ffffff !important;
            border-color: #3b71ca !important;
            box-shadow: 0 4px 15px rgba(59, 113, 202, 0.08), 0 0 0 0.25rem rgba(59, 113, 202, 0.15) !important;
        }

        .hover-primary:hover { background-color: #3b71ca !important; color: #fff !important; border-color: #3b71ca !important; }
        .hover-danger:hover { background-color: #dc3545 !important; color: #fff !important; border-color: #dc3545 !important; }

        .align-center-btn {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 32px; height: 32px; padding: 0 !important;
        }
    </style>
</div>