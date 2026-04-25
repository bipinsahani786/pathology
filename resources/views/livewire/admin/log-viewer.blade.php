<div>
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Production System Logs</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item">Log Monitor</li>
                </ul>
            </div>
            <div class="page-header-right ms-auto">
                <div class="page-header-right-items">
                    <div class="d-flex align-items-center gap-2">
                        <button wire:click="loadLogs" class="btn btn-primary d-flex align-items-center">
                            <i class="feather-refresh-cw me-2"></i> Refresh
                        </button>
                        <button wire:confirm="Are you sure you want to clear all production logs?" wire:click="clearLog" class="btn btn-outline-danger d-flex align-items-center">
                            <i class="feather-trash-2 me-2"></i> Clear Logs
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="main-content">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-0 py-3">
                    <div class="row align-items-center g-3">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="feather-search"></i></span>
                                <input type="text" wire:model.live.debounce.500ms="search" class="form-control bg-light border-0" placeholder="Search in logs (e.g. Error, Exception)...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select wire:model.live="lines" class="form-select bg-light border-0">
                                <option value="50">Last 50 Lines</option>
                                <option value="100">Last 100 Lines</option>
                                <option value="500">Last 500 Lines</option>
                                <option value="1000">Last 1000 Lines</option>
                            </select>
                        </div>
                        <div class="col-md-5 text-md-end">
                            <span class="badge bg-soft-info text-info p-2 px-3 rounded-pill">
                                <i class="feather-file-text me-1"></i> Path: storage/logs/laravel.log
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="bg-dark p-4 font-monospace overflow-auto" style="height: 70vh; color: #a9b7c6; font-size: 13px; line-height: 1.6;">
                        @if(empty($logs))
                            <div class="text-center py-5 opacity-50">
                                <i class="feather-slash mb-3 d-block" style="font-size: 40px;"></i>
                                No logs found matching your criteria.
                            </div>
                        @else
                            <pre class="m-0" style="white-space: pre-wrap;">{!! 
                                collect(explode("\n", $logs))->map(function($line) {
                                    if (str_contains(strtolower($line), 'error') || str_contains(strtolower($line), 'exception') || str_contains(strtolower($line), 'critical')) {
                                        return '<span style="color: #ff6b6b; background: rgba(255, 107, 107, 0.1); display: block; padding: 2px 5px; border-radius: 4px;">' . e($line) . '</span>';
                                    }
                                    if (str_contains(strtolower($line), 'warning')) {
                                        return '<span style="color: #fcc419;">' . e($line) . '</span>';
                                    }
                                    if (str_contains(strtolower($line), 'info')) {
                                        return '<span style="color: #4dabf7;">' . e($line) . '</span>';
                                    }
                                    return e($line);
                                })->implode("\n") 
                            !!}</pre>
                        @endif
                    </div>
                </div>
            </div>

            <div class="mt-4 p-4 bg-white rounded-4 shadow-sm border-0">
                <h6 class="fw-bold mb-3 d-flex align-items-center text-primary">
                    <i class="feather-info me-2"></i> How to use this monitor
                </h6>
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="d-flex gap-3">
                            <div class="bg-soft-primary text-primary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="feather-search"></i>
                            </div>
                            <div>
                                <div class="fw-bold mb-1">Live Filtering</div>
                                <p class="text-muted small mb-0">Type any error code or keyword to filter logs in real-time.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex gap-3">
                            <div class="bg-soft-success text-success rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="feather-refresh-cw"></i>
                            </div>
                            <div>
                                <div class="fw-bold mb-1">Tail Monitoring</div>
                                <p class="text-muted small mb-0">Select the number of lines to fetch. Larger numbers may load slower.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex gap-3">
                            <div class="bg-soft-danger text-danger rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="feather-zap"></i>
                            </div>
                            <div>
                                <div class="fw-bold mb-1">Smart Highlighting</div>
                                <p class="text-muted small mb-0">Errors, warnings, and stack traces are automatically highlighted for quick scanning.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
