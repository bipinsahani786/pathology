<div class="main-content" style="padding: 1.75rem 2rem; max-width: 1850px; margin: 0 auto;">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="text-dark fw-bold mb-0">System Announcements</h5>
            </div>
            <ul class="breadcrumb d-none d-md-flex ms-md-3 mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" wire:navigate class="text-muted">Home</a></li>
                <li class="breadcrumb-item text-muted">Communication</li>
                <li class="breadcrumb-item text-primary fw-medium">Announcements</li>
            </ul>
        </div>
        <div class="page-header-right">
            <button wire:click="create" class="btn btn-primary px-4 shadow-sm rounded-3">
                <i class="feather-plus-circle me-2"></i>Post New Announcement
            </button>
        </div>
    </div>

    <!-- Feedback Message -->
    @if (session()->has('message'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 d-flex align-items-center py-3 mb-4 animate__animated animate__fadeIn">
            <div class="avatar-text avatar-sm bg-soft-success text-success me-3 rounded-circle d-flex align-items-center justify-content-center">
                <i class="feather-check-circle fs-5"></i>
            </div>
            <div class="fw-bold flex-grow-1">{{ session('message') }}</div>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Metrics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white">
                <div class="d-flex align-items-center">
                    <div class="avatar-text avatar-lg bg-soft-primary text-primary rounded-4 me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="feather-bell fs-4"></i>
                    </div>
                    <div>
                        <span class="text-muted fs-11 text-uppercase fw-bold">Total Announcements</span>
                        <h4 class="fw-bolder text-dark mb-0 mt-1">{{ $stats['total'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white">
                <div class="d-flex align-items-center">
                    <div class="avatar-text avatar-lg bg-soft-success text-success rounded-4 me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="feather-radio fs-4"></i>
                    </div>
                    <div>
                        <span class="text-muted fs-11 text-uppercase fw-bold">Live Broadcasting</span>
                        <h4 class="fw-bolder text-success mb-0 mt-1">{{ $stats['active'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white">
                <div class="d-flex align-items-center">
                    <div class="avatar-text avatar-lg bg-soft-info text-info rounded-4 me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="feather-eye-off fs-4"></i>
                    </div>
                    <div>
                        <span class="text-muted fs-11 text-uppercase fw-bold">Lab Dismissals</span>
                        <h4 class="fw-bolder text-info mb-0 mt-1">{{ $stats['dismissals'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white">
                <div class="d-flex align-items-center">
                    <div class="avatar-text avatar-lg bg-soft-warning text-warning rounded-4 me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="feather-clock fs-4"></i>
                    </div>
                    <div>
                        <span class="text-muted fs-11 text-uppercase fw-bold">Expired</span>
                        <h4 class="fw-bolder text-warning mb-0 mt-1">{{ $stats['expired'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filter Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-body p-3">
            <div class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-0 ps-3"><i class="feather-search text-muted"></i></span>
                        <input type="text" class="form-control bg-light border-0 py-2 fs-12" placeholder="Search announcement title or content..." wire:model.live.debounce.300ms="search">
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <select class="form-select form-select-sm bg-light border-0 py-2 fs-12" wire:model.live="filterType">
                        <option value="">All Types (Info, Warning, etc.)</option>
                        <option value="info">Info (Blue)</option>
                        <option value="warning">Warning (Yellow/Gold)</option>
                        <option value="success">Success (Green)</option>
                        <option value="danger">Danger (Red Alert)</option>
                    </select>
                </div>
                <div class="col-md-3 col-6">
                    <select class="form-select form-select-sm bg-light border-0 py-2 fs-12" wire:model.live="filterStatus">
                        <option value="">All Statuses</option>
                        <option value="active">Active Broadcasting Only</option>
                        <option value="inactive">Inactive Only</option>
                        <option value="expired">Expired Only</option>
                    </select>
                </div>
                <div class="col-md-1 text-end">
                    @if($search || $filterType || $filterStatus)
                        <button class="btn btn-sm btn-light border text-muted w-100" wire:click="$set('search', ''); $set('filterType', ''); $set('filterStatus', '');" title="Reset Filters">
                            <i class="feather-refresh-cw"></i>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Announcements Table Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <h6 class="card-title mb-0 fw-bold text-dark">
                <i class="feather-list me-2 text-primary"></i>Announcement Broadcasts
            </h6>
            <span class="badge bg-soft-primary text-primary px-3 py-1 rounded-pill fs-11">
                {{ $announcements->total() }} Total
            </span>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light fs-11 text-uppercase text-muted fw-bold">
                        <tr>
                            <th class="ps-4" style="width: 100px;">Type</th>
                            <th>Announcement Details</th>
                            <th class="text-center" style="width: 140px;">Active Period</th>
                            <th class="text-center" style="width: 110px;">Dismissible</th>
                            <th class="text-center" style="width: 100px;">Dismissals</th>
                            <th class="text-center" style="width: 110px;">Status</th>
                            <th class="text-end pe-4" style="width: 120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($announcements as $item)
                            @php
                                $badgeClass = match($item->type) {
                                    'warning' => 'bg-soft-warning text-warning border-warning border-opacity-25',
                                    'danger'  => 'bg-soft-danger text-danger border-danger border-opacity-25',
                                    'success' => 'bg-soft-success text-success border-success border-opacity-25',
                                    default   => 'bg-soft-primary text-primary border-primary border-opacity-25',
                                };
                                $isLive = $item->is_active && (! $item->starts_at || $item->starts_at->isPast()) && (! $item->expires_at || $item->expires_at->isFuture());
                            @endphp
                            <tr class="border-bottom border-light">
                                <td class="ps-4">
                                    <span class="badge {{ $badgeClass }} border px-2 py-1 rounded-pill text-uppercase fs-10 fw-bold d-inline-flex align-items-center">
                                        <i class="{{ $item->resolved_icon }} me-1"></i> {{ ucfirst($item->type) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <div class="fw-bold text-dark fs-13 mb-1">
                                            {{ $item->title }}
                                            @if($item->priority > 0)
                                                <span class="badge bg-soft-danger text-danger rounded-pill fs-10 ms-1">Priority: +{{ $item->priority }}</span>
                                            @endif
                                        </div>
                                        <div class="text-muted fs-12 text-truncate" style="max-width: 480px;">
                                            {{ Str::limit($item->message, 120) }}
                                        </div>
                                        @if($item->action_url)
                                            <div class="mt-1">
                                                <a href="{{ $item->action_url }}" target="_blank" class="fs-11 text-primary fw-semibold text-decoration-none">
                                                    <i class="feather-external-link me-1"></i>{{ $item->action_label ?: 'Action Link' }}
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center fs-11">
                                    @if($item->starts_at || $item->expires_at)
                                        <div class="text-dark fw-medium">
                                            {{ $item->starts_at ? $item->starts_at->format('d M Y') : 'Immediate' }}
                                        </div>
                                        <div class="text-muted fs-10">
                                            to {{ $item->expires_at ? $item->expires_at->format('d M Y') : 'No Expiry' }}
                                        </div>
                                    @else
                                        <span class="text-muted">Always Active</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($item->is_dismissible)
                                        <span class="badge bg-soft-success text-success fs-10 rounded-pill"><i class="feather-check me-1"></i>Yes</span>
                                    @else
                                        <span class="badge bg-soft-secondary text-secondary fs-10 rounded-pill"><i class="feather-lock me-1"></i>Mandatory</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border fs-11 rounded-pill px-2">
                                        <i class="feather-users me-1 text-muted"></i>{{ $item->dismissals->count() }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="form-check form-switch d-inline-block m-0">
                                        <input class="form-check-input" type="checkbox" role="switch" 
                                               wire:click="toggleStatus({{ $item->id }})" 
                                               {{ $item->is_active ? 'checked' : '' }}>
                                    </div>
                                    <div class="fs-10 mt-1">
                                        @if(!$item->is_active)
                                            <span class="text-muted">● Disabled</span>
                                        @elseif($item->starts_at && $item->starts_at->isFuture())
                                            <span class="text-warning fw-bold">● Scheduled ({{ $item->starts_at->format('d M') }})</span>
                                        @elseif($item->expires_at && $item->expires_at->isPast())
                                            <span class="text-danger fw-bold">● Expired</span>
                                        @else
                                            <span class="text-success fw-bold">● Live Now</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-inline-flex gap-1">
                                        <button class="btn btn-sm btn-icon btn-light text-primary rounded-circle" wire:click="edit({{ $item->id }})" title="Edit Announcement">
                                            <i class="feather-edit-2 fs-12"></i>
                                        </button>
                                        <button class="btn btn-sm btn-icon btn-light text-danger rounded-circle" wire:click="confirmDelete({{ $item->id }})" title="Delete Announcement">
                                            <i class="feather-trash-2 fs-12"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="avatar-text avatar-xl bg-soft-primary text-primary mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                        <i class="feather-bell-off fs-3"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">No Announcements Found</h6>
                                    <p class="text-muted fs-12 mb-3">Broadcast system notifications, feature launches, or maintenance notices to all lab dashboards.</p>
                                    <button wire:click="create" class="btn btn-sm btn-primary px-3 rounded-3">
                                        <i class="feather-plus me-1"></i>Create First Announcement
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($announcements->hasPages())
                <div class="p-3 border-top d-flex justify-content-end">
                    {{ $announcements->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Create / Edit Announcement Modal -->
    @if ($isModalOpen)
        <div class="modal-backdrop fade show" style="z-index: 1040;"></div>
        <div class="modal fade show d-block" tabindex="-1" role="dialog" style="z-index: 1050;">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="modal-header bg-light border-bottom p-3 px-4">
                        <h5 class="modal-title fw-bold text-dark">
                            <i class="feather-{{ $announcement_id ? 'edit' : 'plus-circle' }} text-primary me-2"></i>
                            {{ $announcement_id ? 'Edit Announcement' : 'New Lab Broadcast Announcement' }}
                        </h5>
                        <button type="button" wire:click="closeModal" class="btn-close shadow-none" aria-label="Close"></button>
                    </div>

                    <form wire:submit.prevent="save" class="d-flex flex-column mb-0 overflow-hidden">
                        <div class="modal-body p-4 bg-white">
                            <!-- Type & Priority Row -->
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">Type / Theme Color <span class="text-danger">*</span></label>
                                    <select class="form-select @error('type') is-invalid @enderror" wire:model.live="type">
                                        <option value="info">Info (Soft Blue / General Notice)</option>
                                        <option value="warning">Warning (Amber Gold / Important Alert)</option>
                                        <option value="success">Success (Emerald Green / New Feature)</option>
                                        <option value="danger">Danger (Crimson / Urgent Downtime Alert)</option>
                                    </select>
                                    @error('type') <span class="invalid-feedback fs-11">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">Feather Icon</label>
                                    <input type="text" class="form-control @error('icon') is-invalid @enderror" wire:model.live="icon" placeholder="feather-bell">
                                    <div class="fs-10 text-muted mt-1">Leave empty for auto-match</div>
                                    @error('icon') <span class="invalid-feedback fs-11">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">Display Priority</label>
                                    <input type="number" class="form-control @error('priority') is-invalid @enderror" wire:model="priority" placeholder="0">
                                    <div class="fs-10 text-muted mt-1">Higher = top of dashboard</div>
                                    @error('priority') <span class="invalid-feedback fs-11">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Title -->
                            <div class="mb-3">
                                <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">Announcement Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control fw-bold @error('title') is-invalid @enderror" wire:model.live="title" placeholder="e.g. Scheduled System Maintenance Notice">
                                @error('title') <span class="invalid-feedback fs-11">{{ $message }}</span> @enderror
                            </div>

                            <!-- Message Content -->
                            <div class="mb-3">
                                <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">Announcement Message <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('message') is-invalid @enderror" rows="3" wire:model.live="message" placeholder="Provide full details of the announcement that all laboratories should read..."></textarea>
                                @error('message') <span class="invalid-feedback fs-11">{{ $message }}</span> @enderror
                            </div>

                            <!-- Optional Action Button -->
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">Action Button Link (Optional)</label>
                                    <input type="url" class="form-control @error('action_url') is-invalid @enderror" wire:model.live="action_url" placeholder="https://example.com/update-details">
                                    @error('action_url') <span class="invalid-feedback fs-11">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">Action Button Label (Optional)</label>
                                    <input type="text" class="form-control @error('action_label') is-invalid @enderror" wire:model.live="action_label" placeholder="e.g. Learn More / Upgrade Now">
                                    @error('action_label') <span class="invalid-feedback fs-11">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Schedule (Starts / Expires) -->
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-0">Starts At (Optional)</label>
                                        @if($starts_at)
                                            <button type="button" wire:click="$set('starts_at', null)" class="btn btn-link p-0 text-danger fs-10 text-decoration-none">Clear (Make Immediate)</button>
                                        @endif
                                    </div>
                                    <input type="datetime-local" class="form-control @error('starts_at') is-invalid @enderror" wire:model="starts_at">
                                    <div class="fs-10 text-muted mt-1">Leave empty to broadcast <strong>Immediately</strong>.</div>
                                    @error('starts_at') <span class="invalid-feedback fs-11">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-0">Expires At (Optional)</label>
                                        @if($expires_at)
                                            <button type="button" wire:click="$set('expires_at', null)" class="btn btn-link p-0 text-danger fs-10 text-decoration-none">Clear (No Expiry)</button>
                                        @endif
                                    </div>
                                    <input type="datetime-local" class="form-control @error('expires_at') is-invalid @enderror" wire:model="expires_at">
                                    <div class="fs-10 text-muted mt-1">Leave empty to keep active indefinitely.</div>
                                    @error('expires_at') <span class="invalid-feedback fs-11">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Settings Switches -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <div class="p-3 bg-light rounded-3 d-flex align-items-center justify-content-between">
                                        <div>
                                            <div class="fw-bold fs-12 text-dark">Allow Lab to Dismiss / Hide</div>
                                            <div class="text-muted fs-11">Lab users can close this announcement card.</div>
                                        </div>
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input" type="checkbox" wire:model.live="is_dismissible">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 bg-light rounded-3 d-flex align-items-center justify-content-between">
                                        <div>
                                            <div class="fw-bold fs-12 text-dark">Broadcast Active</div>
                                            <div class="text-muted fs-11">Set ON to show on lab dashboards.</div>
                                        </div>
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input" type="checkbox" wire:model="is_active">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- LIVE PREVIEW BOX -->
                            <div class="border rounded-4 p-3 bg-light">
                                <div class="fs-11 fw-bold text-muted text-uppercase mb-2 d-flex align-items-center">
                                    <i class="feather-eye me-1 text-primary"></i>Live Preview (How it will appear on Lab Dashboard)
                                </div>

                                @php
                                    $previewStyles = match($type) {
                                        'warning' => [
                                            'bg' => 'linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%)',
                                            'border' => '#f59e0b',
                                            'iconBg' => 'bg-warning text-white',
                                            'btn' => 'btn-warning text-white',
                                            'icon' => $icon ?: 'feather-alert-triangle',
                                        ],
                                        'danger' => [
                                            'bg' => 'linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%)',
                                            'border' => '#ef4444',
                                            'iconBg' => 'bg-danger text-white',
                                            'btn' => 'btn-danger text-white',
                                            'icon' => $icon ?: 'feather-alert-octagon',
                                        ],
                                        'success' => [
                                            'bg' => 'linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%)',
                                            'border' => '#10b981',
                                            'iconBg' => 'bg-success text-white',
                                            'btn' => 'btn-success text-white',
                                            'icon' => $icon ?: 'feather-check-circle',
                                        ],
                                        default => [
                                            'bg' => 'linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%)',
                                            'border' => '#3b82f6',
                                            'iconBg' => 'bg-primary text-white',
                                            'btn' => 'btn-primary text-white',
                                            'icon' => $icon ?: 'feather-bell',
                                        ],
                                    };
                                @endphp

                                <div class="alert border-0 shadow-sm rounded-4 p-3 p-md-4 mb-0 d-flex flex-column flex-md-row align-items-start align-items-md-center position-relative" 
                                     style="background: {{ $previewStyles['bg'] }}; border-left: 5px solid {{ $previewStyles['border'] }} !important;">
                                    <div class="icon-box {{ $previewStyles['iconBg'] }} me-3 mb-2 mb-md-0 d-flex align-items-center justify-content-center" 
                                         style="width: 46px; height: 46px; border-radius: 12px; flex-shrink: 0;">
                                        <i class="{{ $previewStyles['icon'] }} fs-4"></i>
                                    </div>
                                    <div class="flex-grow-1 pe-md-3">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <span class="badge bg-white text-dark border px-2 py-1 rounded-pill fs-10 fw-bold text-uppercase shadow-sm">
                                                System Announcement
                                            </span>
                                            <h6 class="fw-bolder text-dark mb-0 fs-14">{{ $title ?: 'Announcement Title Preview' }}</h6>
                                        </div>
                                        <p class="text-muted fs-12 mb-0" style="white-space: pre-line;">
                                            {{ $message ?: 'Your announcement message will be displayed cleanly here for all lab staff to see.' }}
                                        </p>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 mt-3 mt-md-0 ms-md-auto flex-shrink-0">
                                        @if($action_url || $action_label)
                                            <a href="javascript:void(0)" class="btn btn-sm {{ $previewStyles['btn'] }} fw-bold px-3 py-2 rounded-3 shadow-sm">
                                                {{ $action_label ?: 'View Details' }} <i class="feather-arrow-right ms-1"></i>
                                            </a>
                                        @endif
                                        @if($is_dismissible)
                                            <button type="button" class="btn btn-sm btn-light border bg-white rounded-circle shadow-none p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Hide announcement">
                                                <i class="feather-x fs-12 text-muted"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer bg-light border-top p-3 px-4">
                            <button type="button" wire:click="closeModal" class="btn btn-light border px-4">Cancel</button>
                            <button type="submit" class="btn btn-primary px-4 fw-bold">
                                <i class="feather-check-circle me-1"></i> {{ $announcement_id ? 'Update Announcement' : 'Broadcast Announcement' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if ($deleteConfirmId)
        <div class="modal-backdrop fade show" style="z-index: 1060;"></div>
        <div class="modal fade show d-block" tabindex="-1" role="dialog" style="z-index: 1070;">
            <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden text-center p-4">
                    <div class="avatar-text avatar-xl bg-soft-danger text-danger mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="feather-trash-2 fs-3"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">Delete Announcement?</h5>
                    <p class="text-muted fs-12 mb-4">This will permanently remove the announcement and all lab dismissal records.</p>
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" wire:click="cancelDelete" class="btn btn-light border px-3 rounded-3">Cancel</button>
                        <button type="button" wire:click="delete" class="btn btn-danger px-4 rounded-3 fw-bold">Yes, Delete</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
