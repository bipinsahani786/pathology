<div>
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="text-dark fw-bold">{{ $package_id ? 'Edit Package Configuration' : 'Create New Test Package' }}</h5>
            </div>
            <ul class="breadcrumb d-none d-md-flex ms-3">
                <li class="breadcrumb-item"><a href="{{ route('lab.dashboard') }}" wire:navigate class="text-muted">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('lab.packages') }}" wire:navigate class="text-muted">Packages</a></li>
                <li class="breadcrumb-item text-primary fw-medium">{{ $package_id ? 'Edit' : 'Create' }}</li>
            </ul>
        </div>
        <div class="page-header-right d-flex gap-2">
            <a href="{{ route('lab.packages') }}" wire:navigate class="btn btn-light px-4">
                <i class="feather-arrow-left me-2"></i>Back to List
            </a>
            <button wire:click="save" class="btn btn-primary px-5">
                <i class="feather-save me-2"></i>{{ $package_id ? 'Update Package' : 'Save Package' }}
            </button>
        </div>
    </div>

    <div class="main-content mt-4">
        <form wire:submit.prevent="save">
            <div class="row g-4">
                <!-- Build Profile Card (Left) -->
                <div class="col-xl-7">
                    <div class="card stretch stretch-full border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header py-3">
                            <h6 class="card-title mb-0 fw-bold text-dark small-caps"><i class="feather-layers text-primary me-2"></i>Step 1: Build Your Profile</h6>
                        </div>
                        <div class="card-body p-4">
                            <p class="fs-12 text-muted mb-4">Search and aggregate single tests into this comprehensive package/profile.</p>
                            
                            @error('selectedTests') 
                                <div class="alert alert-danger py-2 fs-11 mb-3 border-0 shadow-sm"><i class="feather-alert-octagon me-1"></i>{{ $message }}</div> 
                            @enderror

                            <!-- Search Input -->
                            <div class="position-relative mb-4">
                                <div class="input-group border border-primary rounded-pill bg-white shadow-sm px-3 py-1 overflow-hidden" style="border-width: 2px !important;">
                                    <span class="input-group-text bg-transparent border-0 pe-2 text-primary">
                                        <div wire:loading.remove wire:target="testSearchTerm"><i class="feather-search fs-5"></i></div>
                                        <div wire:loading wire:target="testSearchTerm"><span class="spinner-border spinner-border-sm" role="status"></span></div>
                                    </span>
                                    <input type="text" wire:model.live.debounce.300ms="testSearchTerm" class="form-control border-0 bg-transparent shadow-none fw-medium text-dark fs-14" placeholder="Type test name (e.g. CBC, Liver)...">
                                </div>

                                @if(strlen($testSearchTerm) > 1)
                                    <div class="position-absolute w-100 mt-2 bg-white border border-light rounded-4 shadow-xl overflow-hidden z-3" style="max-height: 300px; overflow-y: auto;">
                                        <div class="list-group list-group-flush">
                                            @forelse($searchResultTests as $srt)
                                                @if(!isset($selectedTests[$srt->id]))
                                                    <button type="button" wire:click="addTestToPackage({{ $srt->id }}, '{{ addslashes($srt->name) }}', '{{ addslashes($srt->department) }}', {{ $srt->mrp ?? 0 }})" 
                                                        class="list-group-item list-group-item-action py-3 px-4 border-bottom d-flex justify-content-between align-items-center hover-bg-light transition-all">
                                                        <div class="text-start">
                                                            <span class="fw-bold text-dark d-block fs-13">{{ $srt->name }}</span>
                                                            <span class="fs-10 text-muted text-uppercase fw-bold opacity-75">{{ $srt->department }}</span>
                                                        </div>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <span class="badge bg-soft-primary text-primary fs-11 rounded-pill px-2">₹{{ number_format($srt->mrp, 0) }}</span>
                                                            <button type="button" class="btn btn-icon btn-soft-primary btn-xs rounded-circle">
                                                                <i class="feather-plus"></i>
                                                            </button>
                                                        </div>
                                                    </button>
                                                @endif
                                            @empty
                                                <div class="p-4 text-center text-muted fs-12 bg-soft-light">
                                                    <i class="feather-info fs-5 d-block mb-1 text-warning"></i>
                                                    No matches found in catalog.
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Selected Tests List -->
                            <div class="selected-tests-container" style="min-height: 200px;">
                                <h6 class="fs-11 fw-bold text-muted text-uppercase mb-3">Included Tests ({{ count($selectedTests) }})</h6>
                                @if(count($selectedTests) > 0)
                                    <div class="list-group border border-light rounded-3 overflow-hidden shadow-xs">
                                        @foreach($selectedTests as $testId => $sTest)
                                            <div wire:key="sel-test-{{ $testId }}" class="list-group-item d-flex justify-content-between align-items-center py-3 px-4 border-bottom border-light bg-white hover-bg-light transition-all">
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="bg-soft-success text-success rounded-circle d-flex align-items-center justify-content-center shadow-xs" style="width: 28px; height: 28px;">
                                                        <i class="feather-check fs-12 fw-bold"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-dark fs-14">{{ $sTest['name'] }}</div>
                                                        <div class="fs-10 text-muted text-uppercase fw-bold opacity-75">{{ $sTest['department'] }}</div>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center gap-4">
                                                    <span class="fw-bold text-dark fs-14">₹{{ number_format($sTest['mrp'], 2) }}</span>
                                                    <button type="button" wire:click="removeTestFromPackage({{ $testId }})" class="btn btn-icon btn-soft-danger btn-sm border-0 rounded-circle" title="Remove">
                                                        <i class="feather-x"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="mt-4 p-3 bg-light rounded-3 d-flex justify-content-between align-items-center border">
                                        <span class="text-muted fw-bold small-caps fs-12">Total Catalog Value:</span>
                                        <span class="fs-18 fw-bold text-primary">₹{{ number_format(array_sum(array_column($selectedTests, 'mrp')), 2) }}</span>
                                    </div>
                                @else
                                    <div class="p-5 text-center border border-dashed rounded-4 bg-light">
                                        <i class="feather-package text-muted opacity-25 mb-3 d-block" style="font-size: 3rem;"></i>
                                        <h6 class="fw-bold text-dark opacity-75 mb-1">Profile is currently empty</h6>
                                        <p class="text-muted fs-12 mb-0">Search above to add tests to this panel.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Metadata & Pricing (Right) -->
                <div class="col-xl-5">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header py-3">
                            <h6 class="card-title mb-0 fw-bold text-dark small-caps"><i class="feather-info text-primary me-2"></i>Step 2: Profile Details</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">Package / Profile Name *</label>
                                    <input type="text" class="form-control fw-bold fs-15 border-2 shadow-xs" wire:model="name" placeholder="E.g. Comprehensive Lipid Panel">
                                    @error('name') <span class="text-danger fs-11 mt-1 d-block">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">Internal Code</label>
                                    <input type="text" class="form-control" wire:model="test_code" placeholder="PR-001">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">Classification</label>
                                    <input type="text" class="form-control bg-light text-muted fw-medium" wire:model="department" readonly>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fs-11 fw-bold text-primary text-uppercase mb-1 border-bottom border-primary d-inline-block">Selling Price (MRP) *</label>
                                    <div class="input-group mt-1">
                                        <span class="input-group-text bg-soft-primary text-primary border-primary border-2 border-end-0 fw-bold">₹</span>
                                        <input type="number" step="0.01" class="form-control border-primary border-2 fw-bold text-primary fs-18 shadow-none" wire:model="mrp" placeholder="0.00">
                                    </div>
                                    @error('mrp') <span class="text-danger fs-11 mt-1 d-block">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">Franchise (B2B) Rate</label>
                                    <div class="input-group mt-1">
                                        <span class="input-group-text bg-light text-muted fw-bold">₹</span>
                                        <input type="number" step="0.01" class="form-control shadow-none" wire:model="b2b_price" placeholder="0.00">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">TAT (Hours)</label>
                                    <input type="number" class="form-control" wire:model="tat_hours" placeholder="e.g. 24">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">Sample Type</label>
                                    <input type="text" class="form-control" wire:model="sample_type" placeholder="Serum/Citrate etc.">
                                </div>

                                <div class="col-12 mt-4" wire:ignore>
                                    <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-2">Clinical Instructions / Preparation</label>
                                    <textarea class="form-control rich-editor" id="package-description-editor" 
                                        x-data x-init="
                                            ClassicEditor
                                                .create($el, {
                                                    toolbar: ['bold', 'italic', 'link', 'bulletedList', 'numberedList', 'insertTable', 'undo', 'redo']
                                                })
                                                .then(editor => {
                                                    editor.model.document.on('change:data', () => {
                                                        @this.set('description', editor.getData());
                                                    });
                                                    editor.setData(@js($description));
                                                })
                                        "></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Finalize Card -->
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4 text-center">
                            <div class="form-check form-switch d-inline-block mb-4">
                                <input class="form-check-input shadow-none" type="checkbox" wire:model="is_active" id="pkg-active" style="width: 45px; height: 22px;">
                                <label class="form-check-label fw-bold text-dark ms-2 mt-1" for="pkg-active">Active in Catalog</label>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-3 mb-2">
                                <i class="feather-check-circle me-2"></i>Complete & Save Profile
                            </button>
                            <a href="{{ route('lab.packages') }}" wire:navigate class="btn btn-light w-100 py-3 fw-bold rounded-3">
                                Cancel & Discard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <style>
        .fs-10 { font-size: 10px; }
        .fs-11 { font-size: 11px; }
        .fs-12 { font-size: 12px; }
        .fs-15 { font-size: 15px; }
        .fs-18 { font-size: 18px; }
        .small-caps { font-variant: small-caps; letter-spacing: 0.5px; }
        .border-dashed { border-style: dashed !important; border-width: 2px !important; }
        .hover-bg-light:hover { background-color: #f8fafc !important; }
        .transition-all { transition: all 0.2s ease-in-out; }
    </style>
</div>
