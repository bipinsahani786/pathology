<div>
    <div class="row g-4">
        {{-- Sidebar for Sub-Tabs --}}
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="list-group list-group-flush border-0">
                    <button wire:click="$set('activeSubTab', 'staff')" class="list-group-item list-group-item-action py-3 border-0 d-flex align-items-center gap-3 {{ $activeSubTab === 'staff' ? 'active bg-soft-primary text-primary' : '' }}">
                        <i class="feather-users fs-5"></i>
                        <div class="flex-grow-1">
                            <div class="fw-bold fs-12">Staff Members</div>
                            <div class="fs-10 opacity-75">Manage login accounts</div>
                        </div>
                    </button>
                    <button wire:click="$set('activeSubTab', 'roles')" class="list-group-item list-group-item-action py-3 border-0 d-flex align-items-center gap-3 {{ $activeSubTab === 'roles' ? 'active bg-soft-primary text-primary' : '' }}">
                        <i class="feather-shield fs-5"></i>
                        <div class="flex-grow-1">
                            <div class="fw-bold fs-12">Roles & Permissions</div>
                            <div class="fs-10 opacity-75">Define access levels</div>
                        </div>
                    </button>
                </div>
            </div>
        </div>

        {{-- Main Content Area --}}
        <div class="col-lg-9">
            @if(session()->has('message'))
                <div class="alert alert-success border-0 shadow-sm py-2 fs-12 mb-4 d-flex align-items-center gap-2">
                    <i class="feather-check-circle"></i> {{ session('message') }}
                </div>
            @endif

            @if($activeSubTab === 'staff')
                {{-- Staff List Card --}}
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                        <h6 class="card-title mb-0 fw-bold text-dark"><i class="feather-users text-primary me-2"></i>Staff Directory</h6>
                        <button wire:click="createStaff" class="btn btn-primary btn-sm px-3 rounded-pill">
                            <i class="feather-plus me-1"></i>Add Staff
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr class="fs-10 text-muted fw-bold text-uppercase">
                                        <th class="ps-4">Name & Contact</th>
                                        <th>Role</th>
                                        <th>Since</th>
                                        <th class="text-end pe-4">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($staff as $member)
                                        <tr>
                                            <td class="ps-4 py-3">
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="avatar-text avatar-md rounded-circle bg-soft-primary text-primary fw-bold">
                                                        {{ $member->initials() }}
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-dark fs-13">{{ $member->name }}</div>
                                                        <div class="fs-11 text-muted">{{ $member->email }} • {{ $member->phone }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-soft-info text-info rounded-pill px-3 py-1 fs-10 fw-bold">
                                                    {{ str_replace(['lab_'.auth()->user()->company_id.'_', 'lab_admin'], ['', 'System Admin'], $member->roles->first()?->name ?? 'No Role') }}
                                                </span>
                                            </td>
                                            <td class="fs-11 text-muted">
                                                {{ $member->created_at->format('M d, Y') }}
                                            </td>
                                            <td class="text-end pe-4">
                                                <div class="btn-group">
                                                    <button wire:click="editStaff({{ $member->id }})" class="btn btn-icon btn-soft-primary btn-sm border-0">
                                                        <i class="feather-edit"></i>
                                                    </button>
                                                    <button onclick="confirm('Are you sure?') || event.stopImmediatePropagation()" wire:click="deleteStaff({{ $member->id }})" class="btn btn-icon btn-soft-danger btn-sm border-0">
                                                        <i class="feather-trash-2"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5">
                                                <i class="feather-users text-muted opacity-25 mb-3 d-block" style="font-size: 3rem;"></i>
                                                <h6 class="text-muted fw-normal">No staff members found.</h6>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                {{-- Roles List Card --}}
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                        <h6 class="card-title mb-0 fw-bold text-dark"><i class="feather-shield text-primary me-2"></i>Access Roles</h6>
                        <button wire:click="createRole" class="btn btn-soft-primary btn-sm px-3 rounded-pill">
                            <i class="feather-plus me-1"></i>New Role
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr class="fs-10 text-muted fw-bold text-uppercase">
                                        <th class="ps-4">Role Name</th>
                                        <th>Permissions Count</th>
                                        <th class="text-end pe-4">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($roles as $role)
                                        <tr>
                                            <td class="ps-4 py-3">
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="feather-shield text-{{ str_contains($role->name, 'admin') ? 'danger' : 'primary' }}"></i>
                                                    <span class="fw-bold text-dark fs-13">
                                                        {{ ucfirst(str_replace(['lab_'.auth()->user()->company_id.'_', 'lab_admin'], ['', 'Lab Administrator'], $role->name)) }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="fs-12 fw-bold text-muted">{{ $role->permissions->count() }} permissions</span>
                                            </td>
                                            <td class="text-end pe-4">
                                                @if(!str_contains($role->name, 'admin'))
                                                    <button wire:click="editRole({{ $role->id }})" class="btn btn-sm btn-soft-primary px-3 rounded-pill border-0">
                                                        <i class="feather-edit me-1"></i>Edit Permissions
                                                    </button>
                                                @else
                                                    <span class="fs-10 text-muted italic">System Role</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- MODAL: STAFF CREATE/EDIT --}}
    @if($isStaffModalOpen)
    <div class="modal show d-block" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-xl rounded-4">
                <div class="modal-header border-0 pb-0">
                    <h5 class="fw-bold text-dark">{{ $staff_id ? 'Edit Staff Member' : 'Add New Staff' }}</h5>
                    <button type="button" class="btn-close" wire:click="$set('isStaffModalOpen', false)"></button>
                </div>
                <div class="modal-body p-4">
                    <form wire:submit.prevent="saveStaff">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">Full Name</label>
                                <input type="text" class="form-control" wire:model="name" placeholder="E.g. John Doe">
                                @error('name') <span class="text-danger fs-10">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-6">
                                <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">Email Address</label>
                                <input type="email" class="form-control" wire:model="email" placeholder="john@example.com">
                                @error('email') <span class="text-danger fs-10">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-6">
                                <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">Phone Number</label>
                                <input type="text" class="form-control" wire:model="phone" placeholder="9876543210">
                                @error('phone') <span class="text-danger fs-10">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-6">
                                <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">Role</label>
                                <select class="form-select" wire:model="role_id">
                                    <option value="">Select Role</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}">
                                            {{ ucfirst(str_replace(['lab_'.auth()->user()->company_id.'_', 'lab_admin'], ['', 'System Admin'], $role->name)) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role_id') <span class="text-danger fs-10">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-6">
                                <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">{{ $staff_id ? 'Change Password' : 'Password' }}</label>
                                <input type="password" class="form-control" wire:model="password" placeholder="••••••••">
                                @error('password') <span class="text-danger fs-10">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="mt-4 text-end">
                            <button type="button" class="btn btn-light me-2" wire:click="$set('isStaffModalOpen', false)">Cancel</button>
                            <button type="submit" class="btn btn-primary px-4 fw-bold">Save Staff Account</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL: ROLE CREATE/EDIT --}}
    @if($isRoleModalOpen)
    <div class="modal show d-block" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-xl rounded-4">
                <div class="modal-header border-0 pb-0">
                    <h5 class="fw-bold text-dark">{{ $role_id_to_edit ? 'Edit Role Permissions' : 'Create New Role' }}</h5>
                    <button type="button" class="btn-close" wire:click="$set('isRoleModalOpen', false)"></button>
                </div>
                <div class="modal-body p-4">
                    <form wire:submit.prevent="saveRole">
                        <div class="mb-4">
                            <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">Role Name</label>
                            <input type="text" class="form-control fw-bold" wire:model="role_name" placeholder="E.g. Senior Technician" {{ $role_id_to_edit ? '' : '' }}>
                            @error('role_name') <span class="text-danger fs-10">{{ $message }}</span> @enderror
                        </div>

                        <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-3 d-block border-bottom pb-2">Assign Permissions</label>
                        <div class="row g-3">
                            @foreach($permissions->groupBy(fn($p) => explode(' ', $p->name)[1] ?? 'general') as $group => $perms)
                                <div class="col-md-6 mb-3">
                                    <h6 class="fs-10 fw-bold text-primary text-uppercase mb-2">{{ ucfirst(str_replace('_', ' ', $group)) }}</h6>
                                    <div class="p-3 bg-light rounded-3 border">
                                        @foreach($perms as $p)
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" value="{{ $p->name }}" wire:model="selectedPermissions" id="p-{{ $p->id }}">
                                                <label class="form-check-label fs-12 text-dark c-pointer" for="p-{{ $p->id }}">
                                                    {{ ucfirst(str_replace(['manage ', '_'], ['', ' '], $p->name)) }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4 text-end">
                            <button type="button" class="btn btn-light me-2" wire:click="$set('isRoleModalOpen', false)">Cancel</button>
                            <button type="submit" class="btn btn-primary px-4 fw-bold">Update Role Permissions</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
