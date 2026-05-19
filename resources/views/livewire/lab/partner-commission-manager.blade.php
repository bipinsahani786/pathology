<div>
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Test-based Commissions</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('lab.dashboard') }}" wire:navigate>Home</a></li>
                @if($partner_role === 'doctor')
                    <li class="breadcrumb-item"><a href="{{ route('lab.doctors') }}" wire:navigate>Doctors</a></li>
                @else
                    <li class="breadcrumb-item"><a href="{{ route('lab.agents') }}" wire:navigate>Agents</a></li>
                @endif
                <li class="breadcrumb-item">Commissions</li>
            </ul>
        </div>
    </div>

    <div class="main-content">
        <div class="row">
            <div class="col-xl-12">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">{{ $partner_name }} ({{ ucfirst($partner_role) }})</h5>
                        <p class="text-muted">Global Default Commission: <strong>{{ $global_commission }}%</strong></p>
                        <p class="text-muted small">Any specific override you set below will replace the global commission for that specific test/package.</p>
                        
                        <div class="mb-4">
                            <input type="text" class="form-control" placeholder="Search tests or packages..." wire:model.live.debounce.300ms="searchTerm">
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Test/Package Name</th>
                                        <th>Price (MRP)</th>
                                        <th>B2B Price</th>
                                        <th>Type</th>
                                        <th>Commission Type</th>
                                        <th>Commission Value</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tests as $test)
                                        <tr wire:key="test-{{ $test->id }}">
                                            <td>
                                                <strong>{{ $test->name }}</strong><br>
                                                <small class="text-muted">{{ $test->test_code }}</small>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-dark">₹{{ number_format($test->mrp, 2) }}</span>
                                            </td>
                                            <td>
                                                <span class="text-muted">₹{{ number_format($test->b2b_price, 2) }}</span>
                                            </td>
                                            <td>
                                                @if($test->is_package)
                                                    <span class="badge bg-primary">Package</span>
                                                @else
                                                    <span class="badge bg-secondary">Test</span>
                                                @endif
                                            </td>
                                            <td>
                                                <select class="form-control form-control-sm" wire:model="commissions.{{ $test->id }}.type">
                                                    <option value="percentage">Percentage (%)</option>
                                                    <option value="fixed">Fixed Amount (₹)</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" class="form-control form-control-sm" 
                                                       wire:model="commissions.{{ $test->id }}.value" 
                                                       placeholder="Global: {{ $global_commission }}%">
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-success" wire:click="saveCommission({{ $test->id }})">Save</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-3">
                            {{ $tests->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
