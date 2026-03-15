<div>
    {{-- ======================== PAGE HEADER ======================== --}}
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Settings</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                <li class="breadcrumb-item">Lab</li>
                <li class="breadcrumb-item">Settings</li>
            </ul>
        </div>
    </div>

    {{-- ======================== MAIN CONTENT ======================== --}}
    <div class="main-content">

        {{-- Tab Navigation --}}
        <ul class="nav nav-tabs mb-4" role="tablist">
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
        </ul>

        {{-- ═══════════════════════════════════════════════════════ --}}
        {{-- TAB 1: LAB PROFILE --}}
        {{-- ═══════════════════════════════════════════════════════ --}}
        @if($activeTab === 'profile')
            <div class="row g-4">
                {{-- Logo & Branding --}}
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0 fs-13"><i class="feather-image text-primary me-2"></i>Lab Logo & Branding</h6>
                        </div>
                        <div class="card-body text-center">
                            <div class="mb-3">
                                @if($lab_logo)
                                    <img src="{{ asset('storage/' . $lab_logo) }}" alt="Lab Logo" class="rounded border" style="max-height:120px;max-width:200px;object-fit:contain;">
                                @else
                                    <div class="avatar-text avatar-xxl mx-auto rounded" style="background:rgba(59,113,202,0.1);">
                                        <i class="feather-image text-primary" style="font-size:48px;"></i>
                                    </div>
                                @endif
                            </div>

                            @if($new_logo)
                                <div class="mb-2">
                                    <img src="{{ $new_logo->temporaryUrl() }}" alt="Preview" class="rounded border" style="max-height:100px;">
                                    <div class="fs-10 text-success mt-1"><i class="feather-check-circle me-1"></i>New logo selected</div>
                                </div>
                            @endif

                            <input type="file" wire:model="new_logo" accept="image/*" class="form-control form-control-sm">
                            @error('new_logo') <span class="text-danger fs-10">{{ $message }}</span> @enderror
                            <div class="fs-10 text-muted mt-2">Max 2MB · JPG, PNG, SVG</div>
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

                            <hr class="my-3">
                            <div class="text-end">
                                <button wire:click="saveProfile" class="btn btn-primary fw-bold px-4">
                                    <span wire:loading.remove wire:target="saveProfile"><i class="feather-save me-1"></i>Save Profile</span>
                                    <span wire:loading wire:target="saveProfile"><span class="spinner-border spinner-border-sm me-1"></span>Saving...</span>
                                </button>
                            </div>
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

                            <hr class="my-3">
                            <div class="text-end">
                                <button wire:click="saveInvoiceSettings" class="btn btn-primary fw-bold px-4">
                                    <span wire:loading.remove wire:target="saveInvoiceSettings"><i class="feather-save me-1"></i>Save Invoice Settings</span>
                                    <span wire:loading wire:target="saveInvoiceSettings"><span class="spinner-border spinner-border-sm me-1"></span>Saving...</span>
                                </button>
                            </div>
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
                                'modern' => ['name' => 'Modern', 'icon' => 'feather-layout', 'color' => '#14b8a6', 'desc' => 'Clean, contemporary design with gradient accents and rounded elements.'],
                                'compact' => ['name' => 'Compact', 'icon' => 'feather-minimize-2', 'color' => '#f59e0b', 'desc' => 'Space-efficient layout for quick printing. Fits more data per page.'],
                                'thermal' => ['name' => 'Thermal', 'icon' => 'feather-printer', 'color' => '#6366f1', 'desc' => 'Optimized for 80mm thermal printers. Narrow format, monospaced text.'],
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
                                        @if($bill_template === $key)
                                            <span class="badge rounded-pill fw-bold fs-11 px-3 py-2" style="background:{{ $tpl['color'] }};color:#fff;">
                                                <i class="feather-check me-1"></i>Active
                                            </span>
                                        @else
                                            <span class="badge bg-light text-muted rounded-pill fs-11 px-3 py-1">Select</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <hr class="my-3">
                    <div class="text-end">
                        <button wire:click="saveTemplate" class="btn btn-primary fw-bold px-4">
                            <span wire:loading.remove wire:target="saveTemplate"><i class="feather-save me-1"></i>Save Template</span>
                            <span wire:loading wire:target="saveTemplate"><span class="spinner-border spinner-border-sm me-1"></span>Saving...</span>
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
