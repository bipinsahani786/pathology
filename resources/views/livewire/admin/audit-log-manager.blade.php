<div>
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Global Audit Logs</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">System</li>
                    <li class="breadcrumb-item">Audit Logs</li>
                </ul>
            </div>
        </div>

        <div class="main-content">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-4">
                            <div class="input-group search-group shadow-sm">
                                <span class="input-group-text bg-white border-0">
                                    <i class="feather-search text-primary"></i>
                                </span>
                                <input type="text" wire:model.live.debounce.300ms="searchTerm" 
                                    class="form-control border-0 shadow-none" 
                                    placeholder="Search user or module...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select wire:model.live="companyFilter" class="form-select shadow-sm border-0 bg-light">
                                <option value="">All Companies / Labs</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select wire:model.live="eventFilter" class="form-select shadow-sm border-0 bg-light">
                                <option value="">All Events</option>
                                <option value="created">Created</option>
                                <option value="updated">Updated</option>
                                <option value="deleted">Deleted</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Lab / Company</th>
                                    <th>User</th>
                                    <th>Event</th>
                                    <th>Module</th>
                                    <th>Date & Time</th>
                                    <th class="pe-4 text-end">IP Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-bold text-primary">{{ $log->company->name ?? 'System' }}</div>
                                            <small class="text-muted">ID: {{ $log->company_id ?? 'N/A' }}</small>
                                        </td>
                                        <td>
                                            <div class="fw-medium text-dark">{{ $log->user->name ?? 'System' }}</div>
                                            <small class="text-muted">{{ $log->user?->roles?->first()?->name ?? 'System Action' }}</small>
                                        </td>
                                        <td>
                                            @php
                                                $badgeClass = match($log->event) {
                                                    'created' => 'bg-soft-success text-success',
                                                    'updated' => 'bg-soft-warning text-warning',
                                                    'deleted' => 'bg-soft-danger text-danger',
                                                    default => 'bg-soft-secondary text-secondary',
                                                };
                                            @endphp
                                            <span class="badge {{ $badgeClass }} px-2 py-1 rounded-pill text-uppercase" style="font-size: 10px;">{{ $log->event }}</span>
                                        </td>
                                        <td>
                                            <div class="text-dark fw-medium">{{ class_basename($log->auditable_type) }}</div>
                                            <small class="text-muted">ID: {{ $log->auditable_id }}</small>
                                        </td>
                                        <td>
                                            <div class="text-dark">{{ $log->created_at->format('d M, Y') }}</div>
                                            <small class="text-muted">{{ $log->created_at->format('h:i A') }}</small>
                                        </td>
                                        <td class="pe-4 text-end">
                                            <div class="text-muted" style="font-size: 11px;">{{ $log->ip_address }}</div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <p class="text-muted">No audit logs found.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-4 border-top">
                        {{ $logs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
