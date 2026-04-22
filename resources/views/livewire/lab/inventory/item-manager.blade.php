<div>
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Inventory Items Master</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">Inventory</li>
                    <li class="breadcrumb-item">Items Master</li>
                </ul>
            </div>
            <div class="page-header-right ms-auto">
                <div class="btn-group">
                    <button wire:click="createCategory" class="btn btn-outline-primary px-4 py-2 shadow-sm rounded-pill me-2">
                        <i class="feather-folder me-2"></i>New Category
                    </button>
                    <button wire:click="create" class="btn btn-primary px-4 py-2 shadow-sm rounded-pill">
                        <i class="feather-plus me-2"></i>New Item
                    </button>
                </div>
            </div>
        </div>

        <div class="main-content">
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm overflow-hidden">
                        <div class="card-header bg-white py-3 border-bottom-0">
                            <div class="row g-3 align-items-center">
                                <div class="col-md-8">
                                    <div class="input-group search-group shadow-sm">
                                        <span class="input-group-text bg-white">
                                            <i class="feather-search text-primary"></i>
                                        </span>
                                        <input type="text" wire:model.live.debounce.300ms="searchTerm" 
                                            class="form-control" 
                                            placeholder="Search items...">
                                    </div>
                                </div>
                                <div class="col-md-8 text-md-end">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" wire:click="$set('activeTab', 'items')" class="btn {{ $activeTab == 'items' ? 'btn-primary' : 'btn-light' }}">Items</button>
                                        <button type="button" wire:click="$set('activeTab', 'categories')" class="btn {{ $activeTab == 'categories' ? 'btn-primary' : 'btn-light' }}">Categories</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            @if($activeTab == 'items')
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light text-muted">
                                        <tr>
                                            <th class="ps-4">Item Name</th>
                                            <th>Category</th>
                                            <th>Unit</th>
                                            <th>Min Stock</th>
                                            <th>Status</th>
                                            <th class="text-end pe-4">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($items as $item)
                                        <tr wire:key="item-{{ $item->id }}">
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-text avatar-sm bg-soft-info text-info me-3">
                                                        {{ substr($item->name, 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0 fw-bold">{{ $item->name }}</h6>
                                                        <small class="text-muted">{{ $item->barcode }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="badge bg-soft-secondary text-secondary rounded-pill px-3">{{ $item->category->name }}</span></td>
                                            <td>{{ $item->unit }}</td>
                                            <td><span class="text-danger fw-bold">{{ $item->min_stock_level }}</span></td>
                                            <td>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" {{ $item->is_active ? 'checked' : '' }} disabled>
                                                </div>
                                            </td>
                                            <td class="text-end pe-4">
                                                <button wire:click="edit({{ $item->id }})" class="btn btn-sm btn-light-primary me-2 shadow-none border">
                                                    <i class="feather-edit-3"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5 text-muted">No items found.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer bg-white border-top-0 p-4">
                                {{ $items->links() }}
                            </div>
                            @else
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light text-muted">
                                        <tr>
                                            <th class="ps-4">Category Name</th>
                                            <th>Items Count</th>
                                            <th class="text-end pe-4">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($categories as $cat)
                                        <tr wire:key="cat-{{ $cat->id }}">
                                            <td class="ps-4 fw-bold">{{ $cat->name }}</td>
                                            <td>{{ $cat->items_count ?? $cat->items()->count() }}</td>
                                            <td class="text-end pe-4">
                                                <button wire:click="editCategory({{ $cat->id }})" class="btn btn-sm btn-light-primary me-2 shadow-none border">
                                                    <i class="feather-edit-3"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-5 text-muted">No categories found.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Item Modal --}}
    @if($isModalOpen)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-white border-bottom p-4">
                    <h5 class="modal-title fw-bold">{{ $item_id ? 'Edit Item' : 'New Inventory Item' }}</h5>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label text-muted small fw-bold text-uppercase">Item Name</label>
                            <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror" placeholder="e.g. CBC Reagent Pack">
                            @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small fw-bold text-uppercase">Category</label>
                            <select wire:model="category_id" class="form-select @error('category_id') is-invalid @enderror">
                                <option value="">Select Category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small fw-bold text-uppercase">Unit</label>
                            <select wire:model="unit" class="form-select">
                                <option value="pcs">Pcs</option>
                                <option value="ml">ML</option>
                                <option value="pkt">Packet</option>
                                <option value="box">Box</option>
                                <option value="vial">Vial</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small fw-bold text-uppercase">Min Stock Level</label>
                            <input type="number" wire:model="min_stock_level" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small fw-bold text-uppercase">Barcode</label>
                            <input type="text" wire:model="barcode" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted small fw-bold text-uppercase">Description</label>
                            <textarea wire:model="description" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 p-4">
                    <button wire:click="closeModal" class="btn btn-secondary px-4 fw-bold">Cancel</button>
                    <button wire:click="store" class="btn btn-primary px-4 fw-bold shadow-sm">
                        {{ $item_id ? 'Update Item' : 'Create Item' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Category Modal --}}
    @if($isCategoryModalOpen)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered shadow-lg">
            <div class="modal-content border-0">
                <div class="modal-header bg-white border-bottom p-4">
                    <h5 class="modal-title fw-bold">{{ $edit_category_id ? 'Edit Category' : 'New Category' }}</h5>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="form-group mb-0">
                        <label class="form-label text-muted small fw-bold text-uppercase">Category Name</label>
                        <input type="text" wire:model="category_name" class="form-control @error('category_name') is-invalid @enderror" placeholder="e.g. Reagents">
                        @error('category_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 p-4">
                    <button wire:click="closeModal" class="btn btn-secondary px-4 fw-bold">Cancel</button>
                    <button wire:click="storeCategory" class="btn btn-primary px-4 fw-bold shadow-sm">
                        {{ $edit_category_id ? 'Update Category' : 'Create Category' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
