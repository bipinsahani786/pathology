<div>
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Inventory Analytics</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">Inventory</li>
                    <li class="breadcrumb-item">Dashboard</li>
                </ul>
            </div>
            <div class="page-header-right ms-auto">
                <button onclick="window.print()" class="btn btn-light-primary shadow-none border">
                    <i class="feather-printer me-2"></i>Print Report
                </button>
            </div>
        </div>

        <div class="main-content">
            <!-- Stats Cards -->
            <div class="row">
                <div class="col-xxl-3 col-md-6">
                    <div class="card border-0 shadow-sm stretch stretch-full">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between">
                                <div>
                                    <p class="fs-11 fw-bold text-uppercase text-muted mb-2">Total Items Master</p>
                                    <h2 class="mb-0 fw-black">{{ $stats['total_items'] }}</h2>
                                </div>
                                <div class="avatar-text avatar-md bg-soft-primary text-primary rounded">
                                    <i class="feather-package"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-3 col-md-6">
                    <div class="card border-0 shadow-sm stretch stretch-full">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between">
                                <div>
                                    <p class="fs-11 fw-bold text-uppercase text-muted mb-2">Low Stock Alerts</p>
                                    <h2 class="mb-0 fw-black text-danger">{{ $stats['low_stock_count'] }}</h2>
                                </div>
                                <div class="avatar-text avatar-md bg-soft-danger text-danger rounded">
                                    <i class="feather-alert-triangle"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-3 col-md-6">
                    <div class="card border-0 shadow-sm stretch stretch-full">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between">
                                <div>
                                    <p class="fs-11 fw-bold text-uppercase text-muted mb-2">Near Expiry (30d)</p>
                                    <h2 class="mb-0 fw-black text-warning">{{ $stats['near_expiry_count'] }}</h2>
                                </div>
                                <div class="avatar-text avatar-md bg-soft-warning text-warning rounded">
                                    <i class="feather-clock"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-3 col-md-6">
                    <div class="card border-0 shadow-sm stretch stretch-full">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between">
                                <div>
                                    <p class="fs-11 fw-bold text-uppercase text-muted mb-2">Expired Items</p>
                                    <h2 class="mb-0 fw-black text-dark">{{ $stats['expired_count'] }}</h2>
                                </div>
                                <div class="avatar-text avatar-md bg-dark text-white rounded">
                                    <i class="feather-x-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Low Stock Items Table -->
                <div class="col-xxl-8 col-lg-7">
                    <div class="card border-0 shadow-sm stretch stretch-full">
                        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                            <h6 class="mb-0 fw-bold"><i class="feather-trending-down text-danger me-2"></i>Critical Low Stock</h6>
                            <a href="{{ route('lab.inventory.stock') }}" class="btn btn-xs btn-light">View All</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light fs-10 text-uppercase text-muted">
                                        <tr>
                                            <th class="ps-4">Item</th>
                                            <th>Category</th>
                                            <th>Available</th>
                                            <th>Min Level</th>
                                            <th class="text-end pe-4">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($lowStockItems as $stock)
                                        <tr>
                                            <td class="ps-4">
                                                <div class="fw-bold text-dark">{{ $stock->item->name }}</div>
                                            </td>
                                            <td><span class="badge bg-soft-secondary text-secondary">{{ $stock->item->category->name }}</span></td>
                                            <td><span class="text-danger fw-bold">{{ $stock->quantity }} {{ $stock->item->unit }}</span></td>
                                            <td>{{ $stock->item->min_stock_level }}</td>
                                            <td class="text-end pe-4">
                                                <span class="badge bg-soft-danger text-danger">Reorder Now</span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">No low stock items. All good!</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Most Used Items -->
                <div class="col-xxl-4 col-lg-5">
                    <div class="card border-0 shadow-sm stretch stretch-full">
                        <div class="card-header bg-white py-3">
                            <h6 class="mb-0 fw-bold"><i class="feather-pie-chart text-primary me-2"></i>Most Used (Last 30 Days)</h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                @forelse($mostUsedItems as $usage)
                                <li class="list-group-item px-0 border-0 mb-3">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-text avatar-sm bg-soft-primary text-primary me-3">
                                                {{ substr($usage->item->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-bold">{{ $usage->item->name }}</h6>
                                                <small class="text-muted">{{ $usage->item->unit }}</small>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <div class="fw-bold text-dark">{{ $usage->total_used }}</div>
                                            <small class="text-muted">Total Issued</small>
                                        </div>
                                    </div>
                                    <div class="progress mt-2" style="height: 4px;">
                                        <div class="progress-bar bg-primary" style="width: 100%"></div>
                                    </div>
                                </li>
                                @empty
                                <li class="text-center py-4 text-muted">No consumption data available.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Expiry Alerts -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm stretch stretch-full">
                        <div class="card-header bg-white py-3">
                            <h6 class="mb-0 fw-bold"><i class="feather-calendar text-warning me-2"></i>Expiry Alerts (Next 60 Days)</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light fs-10 text-uppercase text-muted">
                                        <tr>
                                            <th class="ps-4">Item / Batch</th>
                                            <th>Expiry Date</th>
                                            <th>Days Left</th>
                                            <th class="text-end pe-4">Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($nearExpiryBatches as $batch)
                                        <tr>
                                            <td class="ps-4">
                                                <div class="fw-bold text-dark">{{ $batch->stock->item->name }}</div>
                                                <small class="text-muted">Batch: {{ $batch->batch_number }}</small>
                                            </td>
                                            <td>{{ date('d M, Y', strtotime($batch->expiry_date)) }}</td>
                                            <td>
                                                @php $days = now()->diffInDays($batch->expiry_date, false); @endphp
                                                <span class="badge {{ $days <= 0 ? 'bg-dark' : ($days <= 30 ? 'bg-danger' : 'bg-warning') }} text-white">
                                                    {{ $days <= 0 ? 'Expired' : $days . ' Days' }}
                                                </span>
                                            </td>
                                            <td class="text-end pe-4 fw-bold">{{ $batch->quantity }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">No near expiry batches.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm stretch stretch-full">
                        <div class="card-header bg-white py-3">
                            <h6 class="mb-0 fw-bold"><i class="feather-list text-info me-2"></i>Recent Activity</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light fs-10 text-uppercase text-muted">
                                        <tr>
                                            <th class="ps-4">Action</th>
                                            <th>Item</th>
                                            <th>Qty</th>
                                            <th class="text-end pe-4">Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentActivity as $act)
                                        <tr>
                                            <td class="ps-4">
                                                <span class="badge bg-soft-{{ $act->type == 'in' ? 'success' : 'danger' }} text-{{ $act->type == 'in' ? 'success' : 'danger' }} text-uppercase">
                                                    {{ $act->source }} {{ $act->type }}
                                                </span>
                                            </td>
                                            <td>{{ $act->item->name }}</td>
                                            <td class="fw-bold">{{ $act->type == 'in' ? '+' : '-' }}{{ $act->quantity }}</td>
                                            <td class="text-end pe-4 small text-muted">{{ $act->created_at->diffForHumans() }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">No recent activity.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
