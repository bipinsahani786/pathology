<div>
    {{-- ======================== PAGE HEADER ======================== --}}
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="text-dark fw-bold">Settings</h5>
            </div>
            <ul class="breadcrumb d-none d-md-flex ms-3">
                <li class="breadcrumb-item"><a href="{{ route('lab.dashboard') }}" wire:navigate class="text-muted">Home</a></li>
                <li class="breadcrumb-item text-primary fw-medium">Settings</li>
            </ul>
        </div>
    </div>

    {{-- ======================== MAIN CONTENT ======================== --}}
    <div class="main-content">

        {{-- Tab Navigation --}}
        <ul class="nav nav-tabs mb-4" role="tablist">
            @role('lab_admin|super_admin')
            <li class="nav-item">
                <button wire:click="$set('activeTab', 'general')" class="nav-link {{ $activeTab === 'general' ? 'active' : '' }}">
                    <i class="feather-settings me-1"></i> General
                </button>
            </li>
            <li class="nav-item">
                <button wire:click="$set('activeTab', 'profile')" class="nav-link {{ $activeTab === 'profile' ? 'active' : '' }}">
                    <i class="feather-home me-1"></i> Lab Profile
                </button>
            </li>
            <li class="nav-item">
                <button wire:click="$set('activeTab', 'invoice')" class="nav-link {{ $activeTab === 'invoice' ? 'active' : '' }}">
                    <i class="feather-file-text me-1"></i> Invoice Settings
                </button>
            </li>
            <li class="nav-item">
                <button wire:click="$set('activeTab', 'template')" class="nav-link {{ $activeTab === 'template' ? 'active' : '' }}">
                    <i class="feather-layout me-1"></i> Bill Templates
                </button>
            </li>
            <li class="nav-item">
                <button wire:click="$set('activeTab', 'pdf')" class="nav-link {{ $activeTab === 'pdf' ? 'active' : '' }}">
                    <i class="feather-printer me-1"></i> PDF Header / Footer
                </button>
            </li>
            <li class="nav-item">
                <button wire:click="$set('activeTab', 'signatures')" class="nav-link {{ $activeTab === 'signatures' ? 'active' : '' }}">
                    <i class="feather-edit-3 me-1"></i> Signatures
                </button>
            </li>
            <li class="nav-item">
                <button wire:click="$set('activeTab', 'barcode')" class="nav-link {{ $activeTab === 'barcode' ? 'active' : '' }}">
                    <i class="feather-maximize me-1"></i> Barcode Settings
                </button>
            </li>
            @endrole

            @can('view staff_roles')
            <li class="nav-item">
                <button wire:click="$set('activeTab', 'staff')" class="nav-link {{ $activeTab === 'staff' ? 'active' : '' }}">
                    <i class="feather-users me-1"></i> Staff & Roles
                </button>
            </li>
            @endcan

            @role('lab_admin|super_admin')
            @if(\App\Models\Configuration::getFor('restrict_branch_access', '1') === '1')
            <li class="nav-item">
                <button wire:click="$set('activeTab', 'branch')" class="nav-link {{ $activeTab === 'branch' ? 'active' : '' }}">
                    <i class="feather-git-merge me-1"></i> Branch Controls
                </button>
            </li>
            @endif
            @endrole
        </ul>

        {{-- ═══════════════════════════════════════════════════════ --}}
        {{-- TAB 0: GENERAL SETTINGS --}}
        {{-- ═══════════════════════════════════════════════════════ --}}
        @if($activeTab === 'general')
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0 fs-13"><i class="feather-user text-primary me-2"></i>Patient ID Configuration</h6>
                        </div>
                        <div class="card-body">
                            @if($patientSettingsSaved)
                                <div class="alert alert-success py-2 fs-12 mb-3 d-flex align-items-center gap-2">
                                    <i class="feather-check-circle"></i> Patient settings saved!
                                </div>
                            @endif

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-11">Patient ID Prefix <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" wire:model.live.debounce.500ms="patient_id_prefix" placeholder="e.g. PAT, REG, PT">
                                    <div class="fs-10 text-muted mt-1">Text at the start of Patient ID. Default: PAT</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-11">Counter Digits</label>
                                    <select class="form-select" wire:model.live="patient_id_digits">
                                        <option value="4">4 digits (0001)</option>
                                        <option value="5">5 digits (00001)</option>
                                        <option value="6">6 digits (000001)</option>
                                        <option value="8">8 digits (00000001)</option>
                                    </select>
                                    <div class="fs-10 text-muted mt-1">Number of zero-padded digits in the ID.</div>
                                </div>
                            </div>

                            @can('edit settings')
                            <hr class="my-3">
                            <div class="text-end">
                                <button wire:click="savePatientSettings" class="btn btn-primary fw-bold px-4">
                                    <span wire:loading.remove wire:target="savePatientSettings"><i class="feather-save me-1"></i>Save Patient Settings</span>
                                    <span wire:loading wire:target="savePatientSettings"><span class="spinner-border spinner-border-sm me-1"></span>Saving...</span>
                                </button>
                            </div>
                            @endcan
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card border-primary h-100">
                        <div class="card-header" style="background:rgba(59,113,202,0.08);">
                            <h6 class="card-title mb-0 fs-13 text-primary"><i class="feather-eye me-2"></i>Patient ID Preview</h6>
                        </div>
                        <div class="card-body text-center py-5 d-flex flex-column justify-content-center">
                            <div class="fs-10 fw-bold text-muted text-uppercase mb-3">Sample Patient ID:</div>
                            <div class="fs-3 fw-bold text-dark py-3 px-3 rounded-3 border bg-light font-monospace" style="letter-spacing:1px;">
                                {{ $this->patientIdPreview }}
                            </div>
                            <div class="mt-4">
                                <div class="fs-10 text-muted px-3 text-start">
                                    <p><i class="feather-info me-1"></i> This pattern will be used for all patient records. Existing IDs will also reflect this change.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @if($activeTab === 'profile')
            <div class="row g-4">
                {{-- Logo & Branding --}}
                <div class="col-lg-4">
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="card-title mb-0 fs-13"><i class="feather-image text-primary me-2"></i>Lab Logo</h6>
                        </div>
                        <div class="card-body text-center">
                            <div class="mb-3">
                                @if($lab_logo)
                                    <img src="{{ asset('storage/' . $lab_logo) }}" alt="Lab Logo" class="rounded border" style="max-height:80px;max-width:140px;object-fit:contain;">
                                @else
                                    <div class="avatar-text avatar-xl mx-auto rounded" style="background:rgba(59,113,202,0.1);">
                                        <i class="feather-image text-primary" style="font-size:32px;"></i>
                                    </div>
                                @endif
                            </div>
                            @if($new_logo)
                                <div class="mb-2">
                                    <img src="{{ $new_logo->temporaryUrl() }}" alt="Preview" class="rounded border" style="max-height:80px;">
                                </div>
                            @endif
                            <input type="file" wire:model="new_logo" accept="image/*" class="form-control form-control-sm">
                            <div class="fs-10 text-muted mt-1">Main sidebar logo (Max 2MB)</div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0 fs-13"><i class="feather-compass text-primary me-2"></i>Browser Favicon</h6>
                        </div>
                        <div class="card-body text-center">
                            <div class="mb-3">
                                @if($lab_favicon)
                                    <img src="{{ asset('storage/' . $lab_favicon) }}" alt="Favicon" class="rounded border p-1" style="height:48px;width:48px;object-fit:contain;">
                                @else
                                    <div class="avatar-text avatar-md mx-auto rounded" style="background:rgba(20,184,166,0.1);">
                                        <i class="feather-compass text-teal" style="font-size:24px;"></i>
                                    </div>
                                @endif
                            </div>
                            @if($new_favicon)
                                <div class="mb-2">
                                    <img src="{{ $new_favicon->temporaryUrl() }}" alt="Preview" class="rounded border p-1" style="height:32px;width:32px;">
                                </div>
                            @endif
                            <input type="file" wire:model="new_favicon" accept="image/*" class="form-control form-control-sm">
                            <div class="fs-10 text-muted mt-1">Browser tab icon (Square, Max 1MB)</div>
                        </div>
                    </div>
                </div>

                {{-- Contact Details --}}
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0 fs-13"><i class="feather-info text-primary me-2"></i>Lab Information</h6>
                        </div>
                        <div class="card-body">
                            @if($profileSaved)
                                <div class="alert alert-success py-2 fs-12 mb-3 d-flex align-items-center gap-2">
                                    <i class="feather-check-circle"></i> Profile saved successfully!
                                </div>
                            @endif

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-11">Lab / Pathology Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" wire:model="lab_name" placeholder="e.g. Sahani Pathology Lab">
                                    @error('lab_name') <span class="text-danger fs-10">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-11">Tagline / Subtitle</label>
                                    <input type="text" class="form-control" wire:model="lab_tagline" placeholder="e.g. Trusted Diagnostics Since 2010">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-11"><i class="feather-mail me-1 text-muted"></i>Email</label>
                                    <input type="email" class="form-control" wire:model="lab_email" placeholder="lab@example.com">
                                    @error('lab_email') <span class="text-danger fs-10">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-11"><i class="feather-phone me-1 text-muted"></i>Phone</label>
                                    <input type="text" class="form-control" wire:model="lab_phone" placeholder="+91 9876543210">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-11"><i class="feather-globe me-1 text-muted"></i>Website</label>
                                    <input type="url" class="form-control" wire:model="lab_website" placeholder="https://www.example.com">
                                    @error('lab_website') <span class="text-danger fs-10">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-11"><i class="feather-hash me-1 text-muted"></i>GST / Tax Number</label>
                                    <input type="text" class="form-control" wire:model="lab_gst_number" placeholder="e.g. 22AAAAA0000A1Z5">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold fs-11"><i class="feather-map-pin me-1 text-muted"></i>Full Address</label>
                                    <textarea class="form-control" wire:model="lab_address" rows="3" placeholder="Street, City, State, PIN"></textarea>
                                </div>
                            </div>

                            @can('edit settings')
                            <hr class="my-3">
                            <div class="text-end">
                                <button wire:click="saveProfile" class="btn btn-primary fw-bold px-4">
                                    <span wire:loading.remove wire:target="saveProfile"><i class="feather-save me-1"></i>Save Profile</span>
                                    <span wire:loading wire:target="saveProfile"><span class="spinner-border spinner-border-sm me-1"></span>Saving...</span>
                                </button>
                            </div>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- ═══════════════════════════════════════════════════════ --}}
        {{-- TAB 2: INVOICE SETTINGS --}}
        {{-- ═══════════════════════════════════════════════════════ --}}
        @if($activeTab === 'invoice')
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0 fs-13"><i class="feather-hash text-primary me-2"></i>Invoice Number Pattern</h6>
                        </div>
                        <div class="card-body">
                            @if($invoiceSaved)
                                <div class="alert alert-success py-2 fs-12 mb-3 d-flex align-items-center gap-2">
                                    <i class="feather-check-circle"></i> Invoice settings saved!
                                </div>
                            @endif

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-11">Invoice Prefix <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" wire:model.live.debounce.500ms="invoice_prefix" placeholder="e.g. INV, BILL, LAB">
                                    <div class="fs-10 text-muted mt-1">Text before the number. Example: INV, BILL, LAB</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-11">Separator</label>
                                    <select class="form-select" wire:model.live="invoice_separator">
                                        <option value="-">Dash ( - )</option>
                                        <option value="/">Slash ( / )</option>
                                        <option value="">None</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold fs-11">Date Format</label>
                                    <select class="form-select" wire:model.live="invoice_date_format">
                                        <option value="ym">YYMM ({{ date('ym') }})</option>
                                        <option value="ymd">YYMMDD ({{ date('ymd') }})</option>
                                        <option value="Ymd">YYYYMMDD ({{ date('Ymd') }})</option>
                                        <option value="Y">YYYY ({{ date('Y') }})</option>
                                        <option value="none">No Date</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold fs-11">Counter Digits</label>
                                    <select class="form-select" wire:model.live="invoice_counter_digits">
                                        <option value="2">2 digits (01)</option>
                                        <option value="3">3 digits (001)</option>
                                        <option value="4">4 digits (0001)</option>
                                        <option value="5">5 digits (00001)</option>
                                        <option value="6">6 digits (000001)</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold fs-11">Counter Reset</label>
                                    <select class="form-select" wire:model.live="invoice_counter_reset">
                                        <option value="daily">Daily (resets every day)</option>
                                        <option value="monthly">Monthly (resets each month)</option>
                                        <option value="yearly">Yearly (resets each year)</option>
                                        <option value="never">Never (continuous)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="p-3 rounded-3 mt-4" style="background:rgba(59,113,202,0.05);">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="fw-bold fs-12 text-dark"><i class="feather-shield text-primary me-2"></i>Restrict Billing below B2B Price</div>
                                        <div class="fs-11 text-muted mt-1">If enabled, Collection Centers cannot generate bills if the patient amount is less than the B2B cost.</div>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" wire:model.live="restrict_billing_below_b2b" style="width:3em;height:1.5em;">
                                    </div>
                                </div>
                            </div>

                            @can('edit settings')
                            <hr class="my-3">
                            <div class="text-end">
                                <button wire:click="saveInvoiceSettings" class="btn btn-primary fw-bold px-4">
                                    <span wire:loading.remove wire:target="saveInvoiceSettings"><i class="feather-save me-1"></i>Save Invoice Settings</span>
                                    <span wire:loading wire:target="saveInvoiceSettings"><span class="spinner-border spinner-border-sm me-1"></span>Saving...</span>
                                </button>
                            </div>
                            @endcan
                        </div>
                    </div>
                </div>

                {{-- Live Preview --}}
                <div class="col-lg-4">
                    <div class="card border-primary">
                        <div class="card-header" style="background:rgba(59,113,202,0.08);">
                            <h6 class="card-title mb-0 fs-13 text-primary"><i class="feather-eye me-2"></i>Live Preview</h6>
                        </div>
                        <div class="card-body text-center py-4">
                            <div class="fs-10 fw-bold text-muted text-uppercase mb-2">Your invoice numbers will look like:</div>
                            <div class="fs-2 fw-bold text-primary py-3 px-3 rounded-3 border" style="background:rgba(59,113,202,0.05);letter-spacing:2px;">
                                {{ $this->invoicePreview }}
                            </div>
                            <div class="fs-10 text-muted mt-3">
                                <div class="d-flex justify-content-between border-bottom py-1">
                                    <span>Prefix</span><span class="fw-bold">{{ $invoice_prefix }}</span>
                                </div>
                                <div class="d-flex justify-content-between border-bottom py-1">
                                    <span>Separator</span><span class="fw-bold">{{ $invoice_separator ?: 'None' }}</span>
                                </div>
                                <div class="d-flex justify-content-between border-bottom py-1">
                                    <span>Date Format</span><span class="fw-bold">{{ $invoice_date_format }}</span>
                                </div>
                                <div class="d-flex justify-content-between border-bottom py-1">
                                    <span>Counter Digits</span><span class="fw-bold">{{ $invoice_counter_digits }}</span>
                                </div>
                                <div class="d-flex justify-content-between py-1">
                                    <span>Resets</span><span class="fw-bold text-capitalize">{{ $invoice_counter_reset }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- ═══════════════════════════════════════════════════════ --}}
        {{-- TAB 3: BILL TEMPLATES --}}
        {{-- ═══════════════════════════════════════════════════════ --}}
        @if($activeTab === 'template')
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0 fs-13"><i class="feather-layout text-primary me-2"></i>Select Bill Print Template</h6>
                </div>
                <div class="card-body">
                    @if($templateSaved)
                        <div class="alert alert-success py-2 fs-12 mb-3 d-flex align-items-center gap-2">
                            <i class="feather-check-circle"></i> Template preference saved!
                        </div>
                    @endif

                    <div class="row g-4">
                        @php
                            $templates = [
                                'classic' => ['name' => 'Classic', 'icon' => 'feather-file-text', 'color' => '#3b71ca', 'desc' => 'Traditional layout with header, table, and footer. Best for formal medical reports.'],
                                'modern' => ['name' => 'Modern', 'icon' => 'feather-layout', 'color' => '#14b8a6', 'desc' => 'Clean contemporary design with gradient banner and colored info cards.'],
                                'pro' => ['name' => 'Professional', 'icon' => 'feather-shield', 'color' => '#1e293b', 'desc' => 'High-end Black & White design with clean borders, QR & Barcodes.'],
                                'compact' => ['name' => 'Compact', 'icon' => 'feather-minimize-2', 'color' => '#f59e0b', 'desc' => 'Space-efficient layout for quick printing. Fits more data per page.'],
                                'thermal' => ['name' => 'Thermal', 'icon' => 'feather-printer', 'color' => '#6366f1', 'desc' => 'Optimized for 80mm thermal printers. Narrow receipt format.'],
                            ];
                        @endphp

                        @foreach($templates as $key => $tpl)
                            <div class="col-md-3 col-sm-6">
                                <div wire:click="$set('bill_template', '{{ $key }}')"
                                     class="card h-100 border-2 {{ $bill_template === $key ? 'shadow-lg' : '' }}"
                                     style="cursor:pointer;transition:all .2s;border-color:{{ $bill_template === $key ? $tpl['color'] : '#e5e7eb' }}!important;">
                                    <div class="card-body text-center p-4">
                                        <div class="avatar-text avatar-xl mx-auto mb-3 rounded-3" style="background:{{ $tpl['color'] }}15;">
                                            <i class="{{ $tpl['icon'] }}" style="font-size:32px;color:{{ $tpl['color'] }};"></i>
                                        </div>
                                        <h6 class="fw-bold mb-1 fs-14">{{ $tpl['name'] }}</h6>
                                        <p class="fs-11 text-muted mb-2">{{ $tpl['desc'] }}</p>
                                        <div class="d-flex justify-content-center align-items-center gap-2">
                                            @if($bill_template === $key)
                                                <span class="badge rounded-pill fw-bold fs-11 px-3 py-2" style="background:{{ $tpl['color'] }};color:#fff;">
                                                    <i class="feather-check me-1"></i>Active
                                                </span>
                                            @else
                                                <span class="badge bg-light text-muted rounded-pill fs-11 px-3 py-1">Select</span>
                                            @endif
                                        </div>
                                        <div class="mt-3">
                                            <a href="{{ route('lab.settings.invoice.preview', $key) }}" target="_blank" onclick="event.stopPropagation()" class="btn btn-sm btn-light border w-100 fs-11 fw-bold">
                                                <i class="feather-eye me-1"></i> Preview Format
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @can('edit settings')
                    <hr class="my-3">
                    <div class="text-end">
                        <button wire:click="saveTemplate" class="btn btn-primary fw-bold px-4">
                            <span wire:loading.remove wire:target="saveTemplate"><i class="feather-save me-1"></i>Save Template</span>
                            <span wire:loading wire:target="saveTemplate"><span class="spinner-border spinner-border-sm me-1"></span>Saving...</span>
                        </button>
                    </div>
                    @endcan
                </div>
            </div>
        @endif

        {{-- ═══════════════════════════════════════════════════════ --}}
        {{-- TAB 4: PDF HEADER / FOOTER --}}
        {{-- ═══════════════════════════════════════════════════════ --}}
        @if($activeTab === 'pdf')
            <div class="row g-4">
                {{-- Toggle Switches --}}
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0 fs-13"><i class="feather-toggle-left text-primary me-2"></i>Header & Footer Visibility</h6>
                        </div>
                        <div class="card-body">
                            @if($pdfSaved)
                                <div class="alert alert-success py-2 fs-12 mb-3 d-flex align-items-center gap-2">
                                    <i class="feather-check-circle"></i> PDF settings saved!
                                </div>
                            @endif

                            <div class="p-3 rounded-3 mb-3" style="background:rgba(59,113,202,0.05);">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div>
                                        <strong class="fs-12">Show Header in PDF</strong>
                                        <div class="fs-10 text-muted">Lab name, logo, contact details appear at top</div>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" wire:model.live="pdf_show_header" style="width:3em;height:1.5em;">
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <strong class="fs-12">Show Footer in PDF</strong>
                                        <div class="fs-10 text-muted">Thank you message, website, disclaimer at bottom</div>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" wire:model.live="pdf_show_footer" style="width:3em;height:1.5em;">
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-info py-2 fs-11 mb-0">
                                <i class="feather-info me-1"></i>
                                <strong>Tip:</strong> Labs using their own letterpad can turn OFF both header & footer. Use the "Without Header" PDF option when printing.
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Custom Header/Footer Images --}}
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0 fs-13"><i class="feather-image text-primary me-2"></i>Custom Header / Footer Images</h6>
                        </div>
                        <div class="card-body">
                            <div class="fs-11 text-muted mb-3">
                                Upload custom header/footer images (screenshot of your letterpad). These will replace the default lab info section in the PDF.
                            </div>

                            {{-- Header Image --}}
                            <div class="mb-3 pb-3 border-bottom">
                                <label class="form-label fw-bold fs-11">📄 Custom Header Image</label>
                                @if($pdf_header_image)
                                    <div class="mb-2 p-2 border rounded text-center" style="background:#f8fafc;">
                                        <img src="{{ asset('storage/' . $pdf_header_image) }}" alt="Header" style="max-height:60px;max-width:100%;object-fit:contain;">
                                        <div class="mt-1">
                                            <button wire:click="removeHeaderImage" class="btn btn-sm btn-outline-danger"><i class="feather-trash-2 me-1"></i>Remove</button>
                                        </div>
                                    </div>
                                @endif
                                @if($new_header_image)
                                    <div class="mb-2 text-center">
                                        <img src="{{ $new_header_image->temporaryUrl() }}" alt="Preview" class="rounded border" style="max-height:60px;">
                                        <div class="fs-10 text-success mt-1"><i class="feather-check-circle me-1"></i>New header selected</div>
                                    </div>
                                @endif
                                <input type="file" wire:model="new_header_image" accept="image/*" class="form-control form-control-sm">
                                @error('new_header_image') <span class="text-danger fs-10">{{ $message }}</span> @enderror
                            </div>

                            {{-- Footer Image --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold fs-11">📋 Custom Footer Image</label>
                                @if($pdf_footer_image)
                                    <div class="mb-2 p-2 border rounded text-center" style="background:#f8fafc;">
                                        <img src="{{ asset('storage/' . $pdf_footer_image) }}" alt="Footer" style="max-height:50px;max-width:100%;object-fit:contain;">
                                        <div class="mt-1">
                                            <button wire:click="removeFooterImage" class="btn btn-sm btn-outline-danger"><i class="feather-trash-2 me-1"></i>Remove</button>
                                        </div>
                                    </div>
                                @endif
                                @if($new_footer_image)
                                    <div class="mb-2 text-center">
                                        <img src="{{ $new_footer_image->temporaryUrl() }}" alt="Preview" class="rounded border" style="max-height:50px;">
                                        <div class="fs-10 text-success mt-1"><i class="feather-check-circle me-1"></i>New footer selected</div>
                                    </div>
                                @endif
                                <input type="file" wire:model="new_footer_image" accept="image/*" class="form-control form-control-sm">
                                @error('new_footer_image') <span class="text-danger fs-10">{{ $message }}</span> @enderror
                                <div class="fs-10 text-muted mt-1">Max 3MB · JPG, PNG</div>
                            </div>

                    </div>

                    {{-- NEW: Layout & Typography Settings --}}
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="card-title mb-0 fs-13"><i class="feather-type text-primary me-2"></i>Layout & Typography</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold fs-11">Font Family</label>
                                    <select class="form-select form-select-sm" wire:model="pdf_font_family">
                                        @foreach($this->fontFamilies as $val => $label)
                                            <option value="{{ $val }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold fs-11">Font Size (px)</label>
                                    <input type="number" class="form-control form-control-sm" wire:model="pdf_font_size" min="8" max="14">
                                </div>
                            </div>
                            
                            <hr class="my-3">
                            <div class="fw-bold fs-11 mb-2 text-muted text-uppercase">Page Spacing (pixels)</div>
                            
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-11">Top Margin</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" class="form-control" wire:model="pdf_margin_top">
                                        <span class="input-group-text">px</span>
                                    </div>
                                    <div class="fs-10 text-muted mt-1">Space for Letterhead Header</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-11">Bottom Margin</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" class="form-control" wire:model="pdf_margin_bottom">
                                        <span class="input-group-text">px</span>
                                    </div>
                                    <div class="fs-10 text-muted mt-1">Space for Letterhead Footer</div>
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-11">Header Height</label>
                                    <input type="number" class="form-control form-control-sm" wire:model="pdf_header_height">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-11">Footer Height</label>
                                    <input type="number" class="form-control form-control-sm" wire:model="pdf_footer_height">
                                </div>
                            </div>

                            @can('edit settings')
                            <div class="text-end border-top pt-3">
                                <button wire:click="savePdfSettings" class="btn btn-primary fw-bold px-4">
                                    <span wire:loading.remove wire:target="savePdfSettings"><i class="feather-save me-1"></i>Save All PDF Settings</span>
                                    <span wire:loading wire:target="savePdfSettings"><span class="spinner-border spinner-border-sm me-1"></span>Saving...</span>
                                </button>
                            </div>
                            @endcan
                        </div>
                    </div>
                </div>

                {{-- PDF Options Explainer --}}
                <div class="col-12">
                    <div class="card border-0" style="background:linear-gradient(135deg,rgba(59,113,202,0.05),rgba(124,58,237,0.05));">
                        <div class="card-body">
                            <h6 class="fw-bold fs-13 mb-3"><i class="feather-book-open me-2 text-primary"></i>How PDF Options Work</h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="p-3 bg-white rounded-3 h-100 border">
                                        <div class="fw-bold fs-12 mb-1 text-primary"><i class="feather-file-text me-1"></i>PDF with Header</div>
                                        <div class="fs-11 text-muted">Contains lab name, logo, contact info at top & thank-you at bottom. Use for patients who need a complete invoice.</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-3 bg-white rounded-3 h-100 border">
                                        <div class="fw-bold fs-12 mb-1" style="color:#f59e0b;"><i class="feather-minimize me-1"></i>PDF Without Header</div>
                                        <div class="fs-11 text-muted">Only invoice content — no lab header/footer. Perfect for printing on your own <strong>pre-printed letterpad</strong>.</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-3 bg-white rounded-3 h-100 border">
                                        <div class="fw-bold fs-12 mb-1" style="color:#7c3aed;"><i class="feather-image me-1"></i>Custom Image Header</div>
                                        <div class="fs-11 text-muted">Upload a <strong>screenshot of your letterpad header</strong> and it will be placed at the top of the PDF instead of default info.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- ═══════════════════════════════════════════════════════ --}}
        {{-- TAB: SIGNATURES --}}
        {{-- ═══════════════════════════════════════════════════════ --}}
        @if($activeTab === 'signatures')
            <div class="row g-4">
                {{-- Global Settings & Mode --}}
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-title mb-0 fw-bold text-dark"><i class="feather-edit-3 text-primary me-2"></i>Signature Display Strategy</h6>
                                <p class="fs-12 text-muted mb-0">Choose how signatures should appear on your medical reports.</p>
                            </div>
                            @if($signaturesSaved)
                                <div class="badge bg-soft-success text-success border border-success px-3 py-2 animate__animated animate__fadeIn">
                                    <i class="feather-check-circle me-1"></i>Settings Saved
                                </div>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="row g-4 align-items-center">
                                <div class="col-md-6">
                                    <div class="d-flex gap-3">
                                        <div wire:click="$set('report_signature_mode', 'global_bottom')" 
                                             class="flex-fill p-3 rounded-4 border-2 cursor-pointer transition-all {{ $report_signature_mode === 'global_bottom' ? 'border-primary bg-soft-primary' : 'border-light bg-light opacity-75' }}">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="avatar-text avatar-md rounded-circle {{ $report_signature_mode === 'global_bottom' ? 'bg-primary text-white' : 'bg-white text-muted' }}">
                                                    <i class="feather-align-center"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold fs-13 {{ $report_signature_mode === 'global_bottom' ? 'text-primary' : 'text-dark' }}">Global Bottom</div>
                                                    <div class="fs-10 text-muted">Fixed signatures at end of report</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div wire:click="$set('report_signature_mode', 'per_department')" 
                                             class="flex-fill p-3 rounded-4 border-2 cursor-pointer transition-all {{ $report_signature_mode === 'per_department' ? 'border-primary bg-soft-primary' : 'border-light bg-light opacity-75' }}">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="avatar-text avatar-md rounded-circle {{ $report_signature_mode === 'per_department' ? 'bg-primary text-white' : 'bg-white text-muted' }}">
                                                    <i class="feather-layers"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold fs-13 {{ $report_signature_mode === 'per_department' ? 'text-primary' : 'text-dark' }}">Per Department</div>
                                                    <div class="fs-10 text-muted">Signatures after each dept section</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 border-start ps-4">
                                    <div class="alert alert-soft-info border-0 shadow-none mb-0 fs-11 py-2">
                                        <i class="feather-info me-2"></i>
                                        <strong>Option B active:</strong> In "Global Bottom" mode, the fixed signatures set below will be used. In "Per Department" mode, each department's unique signatures will be used.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Global Signatures (For Bottom Mode) --}}
                <div class="col-12">
                    <h6 class="fw-bold text-dark mt-2 mb-3"><i class="feather-globe text-primary me-2"></i>Global Signatures (Fixed Bottom)</h6>
                    <div class="row g-3">
                        {{-- Slot 1 --}}
                        <div class="col-md-4">
                            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                                <div class="card-body p-4 text-center">
                                    <div class="fs-10 fw-bold text-muted text-uppercase mb-3">Signatory Slot 1</div>
                                    <div class="position-relative mb-4 mx-auto" style="width: 140px; height: 80px; border: 2px dashed #e5e7eb; border-radius: 12px; background: #f9fafb; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                        @if($new_signature_image)
                                            <img src="{{ $new_signature_image->temporaryUrl() }}" class="w-100 h-100 object-fit-contain">
                                        @elseif($signature_image)
                                            <img src="{{ asset('storage/' . $signature_image) }}" class="w-100 h-100 object-fit-contain">
                                        @else
                                            <i class="feather-upload-cloud fs-4 text-muted"></i>
                                        @endif
                                        <input type="file" wire:model="new_signature_image" class="position-absolute opacity-0 w-100 h-100 cursor-pointer">
                                    </div>
                                    <div class="mb-3">
                                        <input type="text" class="form-control form-control-sm border-0 border-bottom rounded-0 px-0 fw-bold text-center fs-13" wire:model="authorized_signatory_name" placeholder="Name">
                                    </div>
                                    <div>
                                        <input type="text" class="form-control form-control-sm border-0 border-bottom rounded-0 px-0 text-center fs-11 text-muted" wire:model="authorized_signatory_designation" placeholder="Designation">
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- Slot 2 --}}
                        <div class="col-md-4">
                            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                                <div class="card-body p-4 text-center">
                                    <div class="fs-10 fw-bold text-muted text-uppercase mb-3">Signatory Slot 2</div>
                                    <div class="position-relative mb-4 mx-auto" style="width: 140px; height: 80px; border: 2px dashed #e5e7eb; border-radius: 12px; background: #f9fafb; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                        @if($new_global_sig_2)
                                            <img src="{{ $new_global_sig_2->temporaryUrl() }}" class="w-100 h-100 object-fit-contain">
                                        @elseif($global_sig_2_path)
                                            <img src="{{ asset('storage/' . $global_sig_2_path) }}" class="w-100 h-100 object-fit-contain">
                                        @else
                                            <i class="feather-upload-cloud fs-4 text-muted"></i>
                                        @endif
                                        <input type="file" wire:model="new_global_sig_2" class="position-absolute opacity-0 w-100 h-100 cursor-pointer">
                                    </div>
                                    <div class="mb-3">
                                        <input type="text" class="form-control form-control-sm border-0 border-bottom rounded-0 px-0 fw-bold text-center fs-13" wire:model="global_sig_2_name" placeholder="Name">
                                    </div>
                                    <div>
                                        <input type="text" class="form-control form-control-sm border-0 border-bottom rounded-0 px-0 text-center fs-11 text-muted" wire:model="global_sig_2_desig" placeholder="Designation">
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- Slot 3 --}}
                        <div class="col-md-4">
                            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                                <div class="card-body p-4 text-center">
                                    <div class="fs-10 fw-bold text-muted text-uppercase mb-3">Signatory Slot 3</div>
                                    <div class="position-relative mb-4 mx-auto" style="width: 140px; height: 80px; border: 2px dashed #e5e7eb; border-radius: 12px; background: #f9fafb; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                        @if($new_global_sig_3)
                                            <img src="{{ $new_global_sig_3->temporaryUrl() }}" class="w-100 h-100 object-fit-contain">
                                        @elseif($global_sig_3_path)
                                            <img src="{{ asset('storage/' . $global_sig_3_path) }}" class="w-100 h-100 object-fit-contain">
                                        @else
                                            <i class="feather-upload-cloud fs-4 text-muted"></i>
                                        @endif
                                        <input type="file" wire:model="new_global_sig_3" class="position-absolute opacity-0 w-100 h-100 cursor-pointer">
                                    </div>
                                    <div class="mb-3">
                                        <input type="text" class="form-control form-control-sm border-0 border-bottom rounded-0 px-0 fw-bold text-center fs-13" wire:model="global_sig_3_name" placeholder="Name">
                                    </div>
                                    <div>
                                        <input type="text" class="form-control form-control-sm border-0 border-bottom rounded-0 px-0 text-center fs-11 text-muted" wire:model="global_sig_3_desig" placeholder="Designation">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Department Signatures --}}
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <h6 class="card-title mb-0 fw-bold text-dark"><i class="feather-layers text-primary me-2"></i>Department-Specific Signatures</h6>
                                <div style="width: 250px;">
                                    <select class="form-select border-0 bg-light fw-bold fs-12 text-primary" wire:model.live="selected_dept_id">
                                        <option value="">Choose Department...</option>
                                        @foreach(\App\Models\Department::forCompany(auth()->user()->company_id)->get() as $dept)
                                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            @if(!$selected_dept_id)
                                <div class="text-center py-5">
                                    <div class="avatar-text avatar-xl bg-soft-primary text-primary mx-auto mb-3">
                                        <i class="feather-arrow-up"></i>
                                    </div>
                                    <h6 class="fw-bold">Select a Department</h6>
                                    <p class="fs-12 text-muted">Choose a department above to manage its specific signatures for "Per Department" mode.</p>
                                </div>
                            @else
                                <div class="row g-4">
                                    {{-- Dept Slot 1 --}}
                                    <div class="col-md-4">
                                        <div class="p-4 border rounded-4 text-center bg-white shadow-sm">
                                            <div class="fs-10 fw-bold text-muted text-uppercase mb-3">Dept Signatory 1</div>
                                            <div class="position-relative mb-4 mx-auto" style="width: 130px; height: 70px; border: 2px dashed #e5e7eb; border-radius: 12px; background: #f9fafb; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                                @if($new_dept_sig_1)
                                                    <img src="{{ $new_dept_sig_1->temporaryUrl() }}" class="w-100 h-100 object-fit-contain">
                                                @elseif($dept_sig_1_path)
                                                    <img src="{{ asset('storage/' . $dept_sig_1_path) }}" class="w-100 h-100 object-fit-contain">
                                                @else
                                                    <i class="feather-plus text-muted"></i>
                                                @endif
                                                <input type="file" wire:model="new_dept_sig_1" class="position-absolute opacity-0 w-100 h-100 cursor-pointer">
                                            </div>
                                            <input type="text" class="form-control form-control-sm border-0 border-bottom rounded-0 px-0 fw-bold text-center fs-12 mb-2" wire:model="dept_sig_1_name" placeholder="Name">
                                            <input type="text" class="form-control form-control-sm border-0 border-bottom rounded-0 px-0 text-center fs-10 text-muted" wire:model="dept_sig_1_desig" placeholder="Designation">
                                        </div>
                                    </div>
                                    {{-- Dept Slot 2 --}}
                                    <div class="col-md-4">
                                        <div class="p-4 border rounded-4 text-center bg-white shadow-sm">
                                            <div class="fs-10 fw-bold text-muted text-uppercase mb-3">Dept Signatory 2</div>
                                            <div class="position-relative mb-4 mx-auto" style="width: 130px; height: 70px; border: 2px dashed #e5e7eb; border-radius: 12px; background: #f9fafb; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                                @if($new_dept_sig_2)
                                                    <img src="{{ $new_dept_sig_2->temporaryUrl() }}" class="w-100 h-100 object-fit-contain">
                                                @elseif($dept_sig_2_path)
                                                    <img src="{{ asset('storage/' . $dept_sig_2_path) }}" class="w-100 h-100 object-fit-contain">
                                                @else
                                                    <i class="feather-plus text-muted"></i>
                                                @endif
                                                <input type="file" wire:model="new_dept_sig_2" class="position-absolute opacity-0 w-100 h-100 cursor-pointer">
                                            </div>
                                            <input type="text" class="form-control form-control-sm border-0 border-bottom rounded-0 px-0 fw-bold text-center fs-12 mb-2" wire:model="dept_sig_2_name" placeholder="Name">
                                            <input type="text" class="form-control form-control-sm border-0 border-bottom rounded-0 px-0 text-center fs-10 text-muted" wire:model="dept_sig_2_desig" placeholder="Designation">
                                        </div>
                                    </div>
                                    {{-- Dept Slot 3 --}}
                                    <div class="col-md-4">
                                        <div class="p-4 border rounded-4 text-center bg-white shadow-sm">
                                            <div class="fs-10 fw-bold text-muted text-uppercase mb-3">Dept Signatory 3</div>
                                            <div class="position-relative mb-4 mx-auto" style="width: 130px; height: 70px; border: 2px dashed #e5e7eb; border-radius: 12px; background: #f9fafb; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                                @if($new_dept_sig_3)
                                                    <img src="{{ $new_dept_sig_3->temporaryUrl() }}" class="w-100 h-100 object-fit-contain">
                                                @elseif($dept_sig_3_path)
                                                    <img src="{{ asset('storage/' . $dept_sig_3_path) }}" class="w-100 h-100 object-fit-contain">
                                                @else
                                                    <i class="feather-plus text-muted"></i>
                                                @endif
                                                <input type="file" wire:model="new_dept_sig_3" class="position-absolute opacity-0 w-100 h-100 cursor-pointer">
                                            </div>
                                            <input type="text" class="form-control form-control-sm border-0 border-bottom rounded-0 px-0 fw-bold text-center fs-12 mb-2" wire:model="dept_sig_3_name" placeholder="Name">
                                            <input type="text" class="form-control form-control-sm border-0 border-bottom rounded-0 px-0 text-center fs-10 text-muted" wire:model="dept_sig_3_desig" placeholder="Designation">
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Action Bar --}}
                <div class="col-12 mt-2">
                    <div class="card bg-dark border-0 shadow-lg rounded-4 overflow-hidden">
                        <div class="card-body p-4 d-flex align-items-center justify-content-between">
                            <div class="text-white">
                                <h6 class="fw-bold mb-1"><i class="feather-shield me-2 text-primary"></i>Finalize Signatures</h6>
                                <p class="fs-11 text-white-50 mb-0">Changes will be reflected in all new and re-printed reports instantly.</p>
                            </div>
                            <button wire:click="saveSignatures" class="btn btn-primary btn-lg shadow-sm px-5 fw-bold transition-all hover-lift">
                                <span wire:loading.remove wire:target="saveSignatures"><i class="feather-save me-2"></i>Publish All Changes</span>
                                <span wire:loading wire:target="saveSignatures"><span class="spinner-border spinner-border-sm me-2"></span>Publishing...</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <style>
                .bg-soft-primary { background-color: rgba(59, 113, 202, 0.08) !important; }
                .cursor-pointer { cursor: pointer; }
                .transition-all { transition: all 0.2s ease-in-out; }
                .hover-lift:hover { transform: translateY(-3px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; }
            </style>
        @endif

        {{-- ═══════════════════════════════════════════════════════ --}}
        {{-- TAB 5: BARCODE SETTINGS --}}
        {{-- ═══════════════════════════════════════════════════════ --}}
        @if($activeTab === 'barcode')
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0 fs-13"><i class="feather-maximize text-primary me-2"></i>Barcode Label Pattern</h6>
                        </div>
                        <div class="card-body">
                            @if($barcodeSaved)
                                <div class="alert alert-success py-2 fs-12 mb-3 d-flex align-items-center gap-2">
                                    <i class="feather-check-circle"></i> Barcode settings saved!
                                </div>
                            @endif

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-11">Barcode Prefix <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" wire:model.live.debounce.500ms="barcode_prefix" placeholder="e.g. BC, LAB, UID">
                                    <div class="fs-10 text-muted mt-1">Text at the start of barcode. Default: LAB</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-11">Date Format</label>
                                    <select class="form-select" wire:model.live="barcode_date_format">
                                        <option value="ym">YYMM ({{ date('ym') }})</option>
                                        <option value="ymd">YYMMDD ({{ date('ymd') }})</option>
                                        <option value="Ymd">YYYYMMDD ({{ date('Ymd') }})</option>
                                        <option value="Y">YYYY ({{ date('Y') }})</option>
                                        <option value="none">No Date</option>
                                    </select>
                                    <div class="fs-10 text-muted mt-1">Included after prefix</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-11">Counter Digits</label>
                                    <select class="form-select" wire:model.live="barcode_counter_digits">
                                        <option value="4">4 digits (0001)</option>
                                        <option value="5">5 digits (00001)</option>
                                        <option value="6">6 digits (000001)</option>
                                        <option value="8">8 digits (00000001)</option>
                                        <option value="10">10 digits (0000000001)</option>
                                    </select>
                                    <div class="fs-10 text-muted mt-1">Length of the unique serial number</div>
                                </div>
                            </div>

                            @can('edit settings')
                            <hr class="my-3">
                            <div class="text-end">
                                <button wire:click="saveBarcodeSettings" class="btn btn-primary fw-bold px-4">
                                    <span wire:loading.remove wire:target="saveBarcodeSettings"><i class="feather-save me-1"></i>Save Barcode Settings</span>
                                    <span wire:loading wire:target="saveBarcodeSettings"><span class="spinner-border spinner-border-sm me-1"></span>Saving...</span>
                                </button>
                            </div>
                            @endcan
                        </div>
                    </div>
                </div>

                {{-- Live Preview --}}
                <div class="col-lg-4">
                    <div class="card border-primary h-100">
                        <div class="card-header" style="background:rgba(59,113,202,0.08);">
                            <h6 class="card-title mb-0 fs-13 text-primary"><i class="feather-eye me-2"></i>Barcode Preview</h6>
                        </div>
                        <div class="card-body text-center py-5 d-flex flex-column justify-content-center">
                            <div class="fs-10 fw-bold text-muted text-uppercase mb-3">Sample Barcode ID:</div>
                            <div class="fs-3 fw-bold text-dark py-3 px-3 rounded-3 border bg-light font-monospace" style="letter-spacing:1px;">
                                {{ $this->barcodePreview }}
                            </div>
                            <div class="mt-4">
                                <div class="fs-10 text-muted px-3 text-start">
                                    <p><i class="feather-info me-1"></i> This ID is unique for every invoice and is printed on all sample stickers associated with the bill.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- ═══════════════════════════════════════════════════════ --}}
        {{-- TAB 6: STAFF & ROLES --}}
        {{-- ═══════════════════════════════════════════════════════ --}}
        @if($activeTab === 'staff')
            @livewire('lab.staff-role-manager')
        @endif

        {{-- ═══════════════════════════════════════════════════════ --}}
        {{-- TAB 7: BRANCH CONTROLS --}}
        {{-- ═══════════════════════════════════════════════════════ --}}
        @if($activeTab === 'branch')
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white py-3">
                            <h6 class="card-title mb-0 fs-14 fw-bold text-dark"><i class="feather-git-merge text-primary me-2"></i>Global Data Sharing Policies</h6>
                            <p class="fs-12 text-muted mb-0 mt-1">Configure what global company data your sub-branches are allowed to interact with.</p>
                        </div>
                        <div class="card-body">
                            @if($branchControlsSaved)
                                <div class="alert alert-success py-2 fs-12 mb-4 d-flex align-items-center gap-2">
                                    <i class="feather-check-circle"></i> Branch sharing policy saved successfully!
                                </div>
                            @endif

                            <div class="list-group list-group-flush border-0">
                                {{-- Share Patients --}}
                                <div class="list-group-item px-0 py-3 border-light d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold text-dark fs-13"><i class="feather-users me-2 text-primary"></i>Share Patient Registry</div>
                                        <div class="fs-11 text-muted mt-1" style="max-width: 80%;">If ON, sub-branches can search and bill any patient registered in the main database. If OFF, each branch can only see patients they registered themselves.</div>
                                    </div>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" wire:model.defer="branch_share_patients" style="width: 2.5em; height: 1.3em;">
                                    </div>
                                </div>

                                {{-- Share Doctors --}}
                                <div class="list-group-item px-0 py-3 border-light d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold text-dark fs-13"><i class="feather-user-plus me-2 text-info"></i>Share Referral Doctors</div>
                                        <div class="fs-11 text-muted mt-1" style="max-width: 80%;">If ON, sub-branches can select referral doctors from the global company list. If OFF, branches must add their own local doctors.</div>
                                    </div>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" wire:model.defer="branch_share_doctors" style="width: 2.5em; height: 1.3em;">
                                    </div>
                                </div>

                                {{-- Share Agents --}}
                                <div class="list-group-item px-0 py-3 border-light d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold text-dark fs-13"><i class="feather-briefcase me-2 text-warning"></i>Share Referral Agents</div>
                                        <div class="fs-11 text-muted mt-1" style="max-width: 80%;">If ON, sub-branches can use global agents. If OFF, agents are strictly siloed to the branch that created them.</div>
                                    </div>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" wire:model.defer="branch_share_agents" style="width: 2.5em; height: 1.3em;">
                                    </div>
                                </div>

                                {{-- Share Lab Tests --}}
                                <div class="list-group-item px-0 py-3 border-light d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold text-dark fs-13"><i class="feather-activity me-2 text-danger"></i>Share Lab Test Catalog</div>
                                        <div class="fs-11 text-muted mt-1" style="max-width: 80%;">If ON, sub-branches use the company's master lab test templates and pricing. Recommended to keep ON for consistency.</div>
                                    </div>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" wire:model.defer="branch_share_tests" style="width: 2.5em; height: 1.3em;">
                                    </div>
                                </div>

                                {{-- Restrict Branch Admins --}}
                                <div class="list-group-item px-0 py-3 border-light d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold text-dark fs-13"><i class="feather-lock me-2 text-dark"></i>Lock Branch Admins to Assigned Branch</div>
                                        <div class="fs-11 text-muted mt-1" style="max-width: 80%;">If ON, branch-level admins can ONLY create and edit bills for their assigned branch. If OFF, they can switch branches freely.</div>
                                    </div>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" wire:model.defer="restrict_branch_access" style="width: 2.5em; height: 1.3em;">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-light border-top py-3 text-end">
                            <button wire:click="saveBranchControls" class="btn btn-primary px-4 fw-bold shadow-sm">
                                <span wire:loading.remove wire:target="saveBranchControls"><i class="feather-save me-2"></i>Save Access Policies</span>
                                <span wire:loading wire:target="saveBranchControls"><span class="spinner-border spinner-border-sm me-2"></span>Saving Policies...</span>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card border-0 bg-primary-subtle shadow-sm rounded-4 h-100">
                        <div class="card-body p-4 text-center d-flex flex-column justify-content-center align-items-center">
                            <div class="bg-white p-3 rounded-circle shadow-sm mb-3">
                                <i class="feather-shield text-primary" style="font-size: 2rem;"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-2">Data Siloing</h5>
                            <p class="fs-12 text-muted mb-4">
                                Turning off sharing toggles will strictly isolate the sub-branch records. <br><br>
                                E.g. If Patient Sharing is OFF, a patient created by Branch A will be completely invisible to Branch B when searching during billing.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
