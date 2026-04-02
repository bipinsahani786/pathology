<div class="nxl-h-item dropdown">
    @if(auth()->user()->hasRole('lab_admin') || auth()->user()->hasRole('super_admin'))
        <a href="javascript:void(0);" 
           class="nxl-head-link me-3 border rounded-pill px-3 py-2 d-flex align-items-center gap-2 bg-white shadow-sm" 
           data-bs-toggle="dropdown" 
           role="button" 
           data-bs-auto-close="outside">
            <div class="bg-soft-primary text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                <i class="feather-git-merge fs-12"></i>
            </div>
            <span class="fs-12 fw-bold text-dark text-truncate d-none d-md-inline-block" style="max-width: 150px;">
                @if($activeBranchId === 'all' || !$activeBranchId)
                    All Branches
                @else
                    {{ collect($branches)->firstWhere('id', $activeBranchId)['name'] ?? \App\Models\Branch::find($activeBranchId)?->name ?? 'Select Branch' }}
                @endif
            </span>
            <i class="feather-chevron-down fs-10 text-muted ms-1"></i>
        </a>
        
        <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown shadow-lg border-0 rounded-3 p-0" style="min-width: 280px; margin-top: 10px !important;">
            <div class="dropdown-header p-3 border-bottom bg-light">
                <div class="d-flex align-items-center justify-content-between">
                    <span class="fw-bold text-muted text-uppercase fs-10 ls-1">Select Branch Context</span>
                    <i class="feather-layers text-primary fs-12"></i>
                </div>
            </div>
            
            <div class="p-2 max-h-300 overflow-y-auto">
                <a href="javascript:void(0);" 
                   wire:click="switchBranch('all')" 
                   class="dropdown-item rounded-2 py-2 px-3 d-flex align-items-center gap-3 transition-all mb-1 {{ ($activeBranchId === 'all' || !$activeBranchId) ? 'active bg-soft-primary text-primary' : '' }}">
                    <div class="avatar-text avatar-xs bg-soft-secondary text-secondary rounded">
                        <i class="feather-globe fs-14"></i>
                    </div>
                    <span class="fw-medium">Standard View (All Branches)</span>
                </a>
                
                <div class="dropdown-divider mx-2 my-2"></div>

                @forelse($branches as $branch)
                    <a href="javascript:void(0);" 
                       wire:click="switchBranch({{ $branch->id }})" 
                       class="dropdown-item rounded-2 py-2 px-3 d-flex align-items-center gap-3 transition-all mb-1 {{ $activeBranchId == $branch->id ? 'active bg-soft-primary text-primary font-bold' : '' }}">
                        <div class="avatar-text avatar-xs {{ $branch->type === 'main_lab' ? 'bg-soft-danger text-danger' : 'bg-soft-info text-info' }} rounded">
                            <i class="feather-briefcase fs-14"></i>
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="fw-semibold text-truncate fs-13">{{ $branch->name }}</div>
                            <div class="fs-10 opacity-75 text-truncate">{{ $branch->type === 'main_lab' ? 'Main Lab' : 'Processing Center' }}</div>
                        </div>
                        @if($activeBranchId == $branch->id)
                            <i class="feather-check-circle fs-12 text-primary"></i>
                        @endif
                    </a>
                @empty
                    <div class="p-4 text-center">
                        <i class="feather-alert-circle text-muted fs-4 d-block mb-2"></i>
                        <span class="text-muted fs-12">No active branches found</span>
                    </div>
                @endforelse
            </div>
            
            <div class="p-2 border-top bg-light text-center">
                <a href="{{ route('lab.branches') }}" wire:navigate class="fs-11 fw-bold text-primary text-decoration-none hover-underline">
                    Manage Branches <i class="feather-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    @endif
    
    <style>
        .max-h-300 { max-height: 300px; }
        .overflow-y-auto { overflow-y: auto; }
        .transition-all { transition: all 0.2s ease; }
        .dropdown-item:hover:not(.active) { background-color: rgba(0,0,0,0.03); transform: translateX(3px); }
        .dropdown-item.active { border-left: 3px solid var(--bs-primary); }
    </style>
</div>
