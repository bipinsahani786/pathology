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
        <div class="page-header-right d-flex gap-2">
            @if(auth()->user()->can('create patients') || auth()->user()->collection_center_id)
                <button wire:click="openImportModal" class="btn btn-outline-success px-3 shadow-sm d-flex align-items-center transition-all hover-lift">
                    <i class="feather-upload me-2 text-success"></i> Import from Excel
                </button>
                <button wire:click="create" class="btn btn-primary px-4 shadow-sm d-flex align-items-center transition-all hover-lift">
                    <i class="feather-user-plus me-2"></i> Add New Patient
                </button>
            @endif
        </div>
    </div>

    <div class="main-content">
        
        @if (session()->has('message') && !$isModalOpen && !$isImportModalOpen)
            <div class="alert alert-success border-0 shadow-sm rounded-3 d-flex align-items-center py-3 alert-dismissible fade show">
                <i class="feather-check-circle fs-4 me-2"></i>
                <strong>{{ session('message') }}</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session()->has('error') && !$isModalOpen && !$isImportModalOpen)
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
                                                    <span class="badge bg-soft-info text-info border border-info border-opacity-25">{{ $patient->formatted_id }}</span>
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
                                            @if(auth()->user()->can('edit patients') || auth()->user()->collection_center_id)
                                                <button wire:click="edit({{ $patient->id }})" 
                                                        class="btn btn-sm btn-light border p-2 text-primary hover-primary shadow-sm align-center-btn" 
                                                        title="Edit Patient">
                                                    <i class="feather-edit-2"></i>
                                                </button>
                                            @endif
                                            
                                            @can('delete patients')
                                                <button wire:click="delete({{ $patient->id }})" 
                                                        wire:confirm="Are you sure you want to delete this patient? All associated profile records will be removed."
                                                        class="btn btn-sm btn-light border p-2 text-danger hover-danger shadow-sm align-center-btn" 
                                                        title="Delete Patient">
                                                    <i class="feather-trash-2"></i>
                                                </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center">
                                            <div class="bg-soft-primary p-4 rounded-circle mb-3">
                                                <i class="feather-users fs-1 text-primary"></i>
                                            </div>
                                            <h6 class="fw-bold text-dark mb-1">No Patients Found</h6>
                                            <p class="text-muted fs-13 mb-3">There are no registered patients matching your search criteria.</p>
                                            @if(auth()->user()->can('create patients') || auth()->user()->collection_center_id)
                                                <button wire:click="create" class="btn btn-primary btn-sm px-3 shadow-sm">
                                                    <i class="feather-plus me-1"></i> Add First Patient
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($patients->hasPages())
                <div class="card-footer bg-white border-top-0 py-3">
                    {{ $patients->links() }}
                </div>
            @endif

        </div>
    </div>

    {{-- CREATE / EDIT PATIENT MODAL --}}
    @if($isModalOpen)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px);">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <form wire:submit.prevent="store" class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="modal-header bg-white border-bottom p-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-soft-primary text-primary p-3 rounded-circle shadow-sm">
                                <i class="feather-user-plus fs-4"></i>
                            </div>
                            <div>
                                <h5 class="modal-title fw-bold text-dark mb-0">{{ $user_id ? 'Edit Patient Details' : 'Register New Patient' }}</h5>
                                <p class="text-muted fs-12 mb-0">Fill in the required profile and demographic details below.</p>
                            </div>
                        </div>
                        <button type="button" wire:click="closeModal" class="btn-close shadow-none"></button>
                    </div>

                    <div class="modal-body p-4 bg-white">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fs-12 fw-bold text-muted text-uppercase">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" wire:model="name" placeholder="e.g. Ramesh Kumar">
                                @error('name') <span class="text-danger fs-11 fw-bold">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fs-12 fw-bold text-muted text-uppercase">Phone Number</label>
                                <input type="text" class="form-control" wire:model="phone" placeholder="10-digit mobile number">
                                @error('phone') <span class="text-danger fs-11 fw-bold">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fs-12 fw-bold text-muted text-uppercase">Email Address</label>
                                <input type="email" class="form-control" wire:model="email" placeholder="patient@example.com">
                                @error('email') <span class="text-danger fs-11 fw-bold">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-12 fw-bold text-muted text-uppercase">Age <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" wire:model="age" placeholder="Age" min="1" max="150">
                                @error('age') <span class="text-danger fs-11 fw-bold">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-12 fw-bold text-muted text-uppercase">Age Unit <span class="text-danger">*</span></label>
                                <select class="form-select" wire:model="age_type">
                                    <option value="Years">Years</option>
                                    <option value="Months">Months</option>
                                    <option value="Days">Days</option>
                                </select>
                                @error('age_type') <span class="text-danger fs-11 fw-bold">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-12 fw-bold text-muted text-uppercase">Gender <span class="text-danger">*</span></label>
                                <select class="form-select" wire:model="gender">
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                                @error('gender') <span class="text-danger fs-11 fw-bold">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fs-12 fw-bold text-muted text-uppercase">Full Address</label>
                                <textarea class="form-control" wire:model="address" rows="2" placeholder="Enter complete address..."></textarea>
                                @error('address') <span class="text-danger fs-11 fw-bold">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light border-top p-3 d-flex justify-content-end gap-2">
                        <button type="button" wire:click="closeModal" class="btn btn-light border px-4 fw-medium shadow-sm">Cancel</button>
                        <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm d-flex align-items-center">
                            <div wire:loading.remove wire:target="store"><i class="feather-save me-2"></i> Save Changes</div>
                            <div wire:loading wire:target="store"><span class="spinner-border spinner-border-sm me-2" role="status"></span> Saving...</div>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- BULK EXCEL / CSV IMPORT MODAL --}}
    @if($isImportModalOpen)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px);">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="modal-header bg-white border-bottom p-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-soft-success text-success p-3 rounded-circle shadow-sm">
                                <i class="feather-file-text fs-4"></i>
                            </div>
                            <div>
                                <h5 class="modal-title fw-bold text-dark mb-0">Bulk Patient Import from Excel</h5>
                                <p class="text-muted fs-12 mb-0">Upload an Excel (.xlsx, .xls) or CSV (.csv) file to import multiple patients at once.</p>
                            </div>
                        </div>
                        <button type="button" wire:click="closeImportModal" class="btn-close shadow-none"></button>
                    </div>

                    <div class="modal-body p-4 bg-white">
                        
                        {{-- Import Results Summary (Show at TOP) --}}
                        @if($importSummary)
                            <div class="mb-4">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="p-3 bg-soft-success border border-success border-opacity-25 rounded-3 text-center flex-grow-1">
                                        <div class="fs-4 fw-bold text-success">{{ $importSummary['success'] }}</div>
                                        <div class="fs-12 text-muted fw-medium">Successfully Imported</div>
                                    </div>
                                    <div class="p-3 {{ $importSummary['skipped'] > 0 ? 'bg-soft-danger border border-danger border-opacity-25' : 'bg-soft-warning border border-warning border-opacity-25' }} rounded-3 text-center flex-grow-1">
                                        <div class="fs-4 fw-bold {{ $importSummary['skipped'] > 0 ? 'text-danger' : 'text-warning' }}">{{ $importSummary['skipped'] }}</div>
                                        <div class="fs-12 text-muted fw-medium">Skipped / Failed</div>
                                    </div>
                                    <div class="p-3 bg-soft-primary border border-primary border-opacity-25 rounded-3 text-center flex-grow-1">
                                        <div class="fs-4 fw-bold text-primary">{{ $importSummary['total'] }}</div>
                                        <div class="fs-12 text-muted fw-medium">Total Rows Read</div>
                                    </div>
                                </div>

                                @if(!empty($importSummary['errors']))
                                    <div class="card border border-danger border-opacity-25 bg-soft-danger rounded-3 mb-3">
                                        <div class="card-header bg-transparent border-0 pb-0">
                                            <h6 class="fs-13 fw-bold text-danger mb-0">
                                                <i class="feather-alert-triangle me-1"></i> Import Issues & Skipped Records:
                                            </h6>
                                        </div>
                                        <div class="card-body p-3">
                                            <ul class="mb-0 fs-12 text-danger ps-3" style="max-height: 180px; overflow-y: auto;">
                                                @foreach($importSummary['errors'] as $err)
                                                    <li class="mb-1 fw-medium">{{ $err }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- Step 1: Download Template --}}
                        <div class="p-3 mb-4 rounded-3 border border-primary border-opacity-25 bg-soft-primary d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
                            <div>
                                <div class="fw-bold text-primary fs-14 mb-1">
                                    <i class="feather-download me-1"></i> Need a starting file?
                                </div>
                                <div class="text-muted fs-12">
                                    Download our pre-formatted Excel template with sample patient data and column headers.
                                </div>
                            </div>
                            <button type="button" wire:click="downloadSampleTemplate" class="btn btn-primary btn-sm px-3 shadow-sm text-nowrap d-flex align-items-center">
                                <i class="feather-download me-2"></i> Download Sample Excel
                            </button>
                        </div>

                        {{-- Step 2: Upload File --}}
                        <div class="mb-4">
                            <label class="form-label fs-12 fw-bold text-muted text-uppercase mb-2">
                                Select Excel / CSV File <span class="text-danger">*</span>
                            </label>
                            <div class="border border-2 border-dashed rounded-4 p-4 text-center bg-light position-relative">
                                <input type="file" wire:model="importFile" accept=".xlsx,.xls,.csv" class="position-absolute top-0 start-0 w-100 h-100 opacity-0 cursor-pointer" id="excelPatientFile">
                                
                                {{-- Loading State inside dropzone --}}
                                <div wire:loading wire:target="importFile" class="py-2">
                                    <div class="spinner-border text-primary mb-2" role="status" style="width: 2.5rem; height: 2.5rem;"></div>
                                    <div class="fw-bold text-dark fs-14">Uploading & Processing Spreadsheet...</div>
                                    <div class="text-muted fs-12">Please wait while your file is being uploaded</div>
                                </div>

                                {{-- Idle / Selected State --}}
                                <div wire:loading.remove wire:target="importFile" class="d-flex flex-column align-items-center">
                                    @if($importFile)
                                        <div class="bg-white p-3 rounded-circle shadow-sm mb-2 text-success">
                                            <i class="feather-check-circle fs-2"></i>
                                        </div>
                                        <span class="badge bg-success fs-13 px-3 py-2 mb-1 shadow-sm">
                                            <i class="feather-file-text me-1"></i> {{ $importFile->getClientOriginalName() }}
                                        </span>
                                        <span class="text-muted fs-11 mt-1">Click or drag another file to replace</span>
                                    @else
                                        <div class="bg-white p-3 rounded-circle shadow-sm mb-2 text-primary">
                                            <i class="feather-upload-cloud fs-2"></i>
                                        </div>
                                        <div class="fw-bold text-dark fs-14 mb-1">Drag & drop your Excel or CSV file here</div>
                                        <div class="text-muted fs-12 mb-3">Supported formats: .xlsx, .xls, .csv (Max 10MB)</div>
                                        <label for="excelPatientFile" class="btn btn-outline-primary btn-sm px-4 shadow-sm cursor-pointer">
                                            <i class="feather-file-plus me-1"></i> Browse File
                                        </label>
                                    @endif
                                </div>
                            </div>
                            @error('importFile') <div class="text-danger fs-11 fw-bold mt-2">{{ $message }}</div> @enderror
                        </div>

                        {{-- Column Reference Accordion/Guide --}}
                        <div class="card border border-light bg-light rounded-3 mb-3">
                            <div class="card-body p-3">
                                <div class="fw-bold text-dark fs-12 text-uppercase mb-2">
                                    <i class="feather-info text-info me-1"></i> Expected Column Headers:
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered bg-white fs-11 mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Column</th>
                                                <th>Required?</th>
                                                <th>Accepted Values / Format</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="fw-bold">Patient Name</td>
                                                <td><span class="badge bg-danger">Required</span></td>
                                                <td>e.g. Ramesh Kumar</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">Age</td>
                                                <td><span class="badge bg-danger">Required</span></td>
                                                <td>Numeric (e.g. 32, 5, 45)</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">Age Type</td>
                                                <td><span class="badge bg-secondary">Optional</span></td>
                                                <td>Years / Months / Days (Default: Years)</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">Gender</td>
                                                <td><span class="badge bg-secondary">Optional</span></td>
                                                <td>Male / Female / Other (Default: Male)</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">Phone Number</td>
                                                <td><span class="badge bg-secondary">Optional</span></td>
                                                <td>10-digit mobile number</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">Blood Group</td>
                                                <td><span class="badge bg-secondary">Optional</span></td>
                                                <td>A+, A-, B+, B-, O+, O-, AB+, AB-</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">Email</td>
                                                <td><span class="badge bg-secondary">Optional</span></td>
                                                <td>Valid email address</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">Address</td>
                                                <td><span class="badge bg-secondary">Optional</span></td>
                                                <td>Patient complete residence address</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer bg-light border-top p-3 d-flex justify-content-end gap-2">
                        <button type="button" wire:click="closeImportModal" class="btn btn-light border px-4 fw-medium shadow-sm">Close</button>
                        <button type="button" wire:click="processBulkImport" wire:loading.attr="disabled" class="btn btn-success px-5 fw-bold shadow-sm d-flex align-items-center">
                            <div wire:loading.remove wire:target="processBulkImport"><i class="feather-check-circle me-2"></i> Start Import</div>
                            <div wire:loading wire:target="processBulkImport"><span class="spinner-border spinner-border-sm me-2" role="status"></span> Importing Patients...</div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <style>
        .bg-soft-primary { background-color: rgba(59, 113, 202, 0.08) !important; }
        .bg-soft-success { background-color: rgba(25, 135, 84, 0.08) !important; }
        .bg-soft-warning { background-color: rgba(255, 193, 7, 0.12) !important; }
        .bg-soft-danger { background-color: rgba(220, 53, 69, 0.08) !important; }
        .text-primary { color: #3b71ca !important; }
        
        .transition-all { transition: all 0.2s ease-in-out; }
        .hover-lift:hover { transform: translateY(-1px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; }
        .cursor-pointer { cursor: pointer !important; }
        
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