<div>
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">System Maintenance</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                <li class="breadcrumb-item">Maintenance</li>
            </ul>
        </div>
    </div>

    <div class="main-content">
        @if (session('message'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-3 mb-4" role="alert">
                <i class="feather-check-circle fs-20"></i>
                <div class="fw-bold">{{ session('message') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-3 mb-4" role="alert">
                <i class="feather-alert-octagon fs-20"></i>
                <div class="fw-bold">{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row g-4">
            {{-- Big Action Card --}}
            <div class="col-12">
                <div class="card border-0 shadow-sm overflow-hidden" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
                    <div class="card-body p-5">
                        <div class="row align-items-center">
                            <div class="col-lg-8">
                                <h3 class="text-white fw-bold mb-2">Full System Optimization</h3>
                                <p class="text-slate-400 mb-4 fs-16">Clear all caches, recompile views, and reset configurations in one click. Use this to resolve unexpected UI bugs or deployment sync issues.</p>
                                <button wire:click="clearAll" wire:loading.attr="disabled" class="btn btn-warning btn-lg px-5 py-3 fw-bold shadow">
                                    <span wire:loading.remove wire:target="clearAll"><i class="feather-zap me-2"></i>RUN SYSTEM PURGE</span>
                                    <span wire:loading wire:target="clearAll"><span class="spinner-border spinner-border-sm me-2"></span>PURGING SYSTEM...</span>
                                </button>
                            </div>
                            <div class="col-lg-4 d-none d-lg-block text-center">
                                <i class="feather-settings text-warning opacity-25" style="font-size: 120px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Individual Cards --}}
            <div class="col-md-6 col-xl-3">
                <div class="card h-100 border-0 shadow-sm card-hover">
                    <div class="card-body text-center p-4">
                        <div class="avatar-lg bg-soft-primary text-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
                            <i class="feather-database fs-30"></i>
                        </div>
                        <h5 class="fw-bold">Application Cache</h5>
                        <p class="text-muted fs-12 mb-4">Clear stored data from models, queries, and custom cache keys.</p>
                        <button wire:click="runCommand('cache')" wire:loading.attr="disabled" class="btn btn-outline-primary w-100 fw-bold">
                            <span wire:loading.remove wire:target="runCommand('cache')">Clear Cache</span>
                            <span wire:loading wire:target="runCommand('cache')"><span class="spinner-border spinner-border-sm me-2"></span></span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="card h-100 border-0 shadow-sm card-hover">
                    <div class="card-body text-center p-4">
                        <div class="avatar-lg bg-soft-info text-info rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
                            <i class="feather-layout fs-30"></i>
                        </div>
                        <h5 class="fw-bold">Blade Views</h5>
                        <p class="text-muted fs-12 mb-4">Force re-compilation of all Blade templates to reflect recent HTML changes.</p>
                        <button wire:click="runCommand('view')" wire:loading.attr="disabled" class="btn btn-outline-info w-100 fw-bold text-info">
                            <span wire:loading.remove wire:target="runCommand('view')">Clear Views</span>
                            <span wire:loading wire:target="runCommand('view')"><span class="spinner-border spinner-border-sm me-2"></span></span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="card h-100 border-0 shadow-sm card-hover">
                    <div class="card-body text-center p-4">
                        <div class="avatar-lg bg-soft-success text-success rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
                            <i class="feather-settings fs-30"></i>
                        </div>
                        <h5 class="fw-bold">Configuration</h5>
                        <p class="text-muted fs-12 mb-4">Reload all environment variables and config files from the .env source.</p>
                        <button wire:click="runCommand('config')" wire:loading.attr="disabled" class="btn btn-outline-success w-100 fw-bold">
                            <span wire:loading.remove wire:target="runCommand('config')">Clear Config</span>
                            <span wire:loading wire:target="runCommand('config')"><span class="spinner-border spinner-border-sm me-2"></span></span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="card h-100 border-0 shadow-sm card-hover">
                    <div class="card-body text-center p-4">
                        <div class="avatar-lg bg-soft-warning text-warning rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
                            <i class="feather-map fs-30"></i>
                        </div>
                        <h5 class="fw-bold">Route Cache</h5>
                        <p class="text-muted fs-12 mb-4">Clear the route registration cache to recognize new URLs or changes.</p>
                        <button wire:click="runCommand('route')" wire:loading.attr="disabled" class="btn btn-outline-warning w-100 fw-bold text-dark">
                            <span wire:loading.remove wire:target="runCommand('route')">Clear Routes</span>
                            <span wire:loading wire:target="runCommand('route')"><span class="spinner-border spinner-border-sm me-2"></span></span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Advanced Tools --}}
            <div class="col-12 mt-2">
                <h6 class="fw-bold text-muted text-uppercase fs-11 mb-3"><i class="feather-terminal me-2"></i>Advanced Utilities</h6>
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <div class="list-group-item d-flex align-items-center justify-content-between p-4">
                                <div>
                                    <h6 class="fw-bold mb-1">Optimize Application</h6>
                                    <p class="text-muted fs-12 mb-0">Caches config and routes for production performance.</p>
                                </div>
                                <button wire:click="runCommand('optimize')" class="btn btn-sm btn-dark px-3 fw-bold">RUN OPTIMIZE</button>
                            </div>
                            <div class="list-group-item d-flex align-items-center justify-content-between p-4">
                                <div>
                                    <h6 class="fw-bold mb-1">Symbolic Link</h6>
                                    <p class="text-muted fs-12 mb-0">Ensure public/storage is linked to storage/app/public.</p>
                                </div>
                                <button wire:click="runCommand('storage_link')" class="btn btn-sm btn-dark px-3 fw-bold">CREATE LINK</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .card-hover { transition: transform 0.2s; }
        .card-hover:hover { transform: translateY(-5px); }
        .bg-soft-primary { background: rgba(59, 113, 202, 0.1); }
        .bg-soft-info { background: rgba(57, 175, 209, 0.1); }
        .bg-soft-success { background: rgba(25, 135, 84, 0.1); }
        .bg-soft-warning { background: rgba(255, 193, 7, 0.1); }
        .text-slate-400 { color: #94a3b8; }
        .avatar-lg { width: 64px; height: 64px; }
        .fs-30 { font-size: 30px; }
        .fs-20 { font-size: 20px; }
    </style>
</div>
