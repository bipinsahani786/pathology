<div>
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Audit Logs</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">Settings</li>
                    <li class="breadcrumb-item">Audit Logs</li>
                </ul>
            </div>
        </div>

        <div class="main-content">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-5">
                            <div class="input-group search-group shadow-sm">
                                <span class="input-group-text bg-white border-0">
                                    <i class="feather-search text-primary"></i>
                                </span>
                                <input type="text" wire:model.live.debounce.300ms="searchTerm" 
                                    class="form-control border-0 shadow-none" 
                                    placeholder="Search user, module or event...">
                            </div>
                        </div>
                        <div class="col-md-4">
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
                            <thead class="bg-light text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">
                                <tr>
                                    <th class="ps-4">User</th>
                                    <th>Event</th>
                                    <th>Module</th>
                                    <th>Changes</th>
                                    <th>Date & Time</th>
                                    <th class="pe-4 text-end">IP Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-2 bg-soft-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                    <span class="text-primary fw-bold" style="font-size: 12px;">{{ substr($log->user->name ?? 'S', 0, 1) }}</span>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark" style="font-size: 13px;">{{ $log->user->name ?? 'System' }}</div>
                                                    <small class="text-muted" style="font-size: 11px;">{{ $log->user->roles->first()->name ?? 'No Role' }}</small>
                                                </div>
                                            </div>
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
                                            <div class="text-dark fw-medium" style="font-size: 13px;">{{ class_basename($log->auditable_type) }}</div>
                                            <small class="text-muted" style="font-size: 11px;">ID: {{ $log->auditable_id }}</small>
                                        </td>
                                        <td>
                                            @if($log->event === 'updated')
                                                <div class="text-muted" style="font-size: 11px; max-width: 300px;">
                                                    @foreach($log->new_values ?? [] as $key => $val)
                                                        @if(!is_array($val))
                                                            <div class="mb-1 text-truncate">
                                                                <strong>{{ str_replace('_', ' ', $key) }}:</strong> 
                                                                <span class="text-danger text-decoration-line-through">{{ is_string($log->old_values[$key] ?? '') ? Str::limit($log->old_values[$key], 20) : 'N/A' }}</span> 
                                                                <i class="feather-arrow-right mx-1"></i> 
                                                                <span class="text-success">{{ is_string($val) ? Str::limit($val, 20) : 'N/A' }}</span>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @elseif($log->event === 'created')
                                                <span class="text-muted" style="font-size: 11px;">Record created with initial data.</span>
                                            @elseif($log->event === 'deleted')
                                                <span class="text-muted" style="font-size: 11px;">Full record removed.</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="text-dark" style="font-size: 13px;">{{ $log->created_at->format('d M, Y') }}</div>
                                            <small class="text-muted" style="font-size: 11px;">{{ $log->created_at->format('h:i A') }}</small>
                                        </td>
                                        <td class="pe-4 text-end">
                                            <div class="text-muted" style="font-size: 11px;">{{ $log->ip_address }}</div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <div class="mb-3">
                                                <i class="feather-search text-muted" style="font-size: 50px;"></i>
                                            </div>
                                            <h6 class="text-muted">No audit logs found</h6>
                                            <p class="text-muted small">Try adjusting your filters or search term.</p>
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
