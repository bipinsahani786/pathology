<div>
    <div class="main-content">
        <div class="container-fluid">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h2 class="fs-20 fw-bolder mb-0">Landing Content</h2>
                    <p class="text-muted mb-0 fs-12">Manage features, testimonials, and FAQs</p>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show"><i class="feather-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif

            <!-- Tabs -->
            <ul class="nav nav-tabs mb-4">
                <li class="nav-item"><a class="nav-link {{ $activeTab === 'features' ? 'active' : '' }}" href="#" wire:click.prevent="$set('activeTab', 'features')">Features ({{ $features->count() }})</a></li>
                <li class="nav-item"><a class="nav-link {{ $activeTab === 'testimonials' ? 'active' : '' }}" href="#" wire:click.prevent="$set('activeTab', 'testimonials')">Testimonials ({{ $testimonials->count() }})</a></li>
                <li class="nav-item"><a class="nav-link {{ $activeTab === 'faqs' ? 'active' : '' }}" href="#" wire:click.prevent="$set('activeTab', 'faqs')">FAQs ({{ $faqs->count() }})</a></li>
            </ul>

            <!-- FEATURES TAB -->
            @if($activeTab === 'features')
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Landing Features</h5>
                        <button wire:click="createFeature" class="btn btn-sm btn-primary"><i class="feather-plus me-1"></i> Add Feature</button>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0">
                            <thead><tr><th>#</th><th>Icon</th><th>Title</th><th>Color</th><th>Status</th><th>Actions</th></tr></thead>
                            <tbody>
                                @foreach($features as $feature)
                                    <tr>
                                        <td>{{ $feature->sort_order }}</td>
                                        <td><i class="{{ $feature->icon }}"></i></td>
                                        <td><strong>{{ $feature->title }}</strong><br><small class="text-muted">{{ Str::limit($feature->description, 60) }}</small></td>
                                        <td><span class="badge bg-{{ $feature->color === 'primary' ? 'primary' : ($feature->color === 'success' ? 'success' : 'secondary') }}">{{ $feature->color }}</span></td>
                                        <td><button wire:click="toggleFeature({{ $feature->id }})" class="btn btn-sm {{ $feature->is_active ? 'btn-success' : 'btn-secondary' }}">{{ $feature->is_active ? 'Active' : 'Hidden' }}</button></td>
                                        <td>
                                            <button wire:click="editFeature({{ $feature->id }})" class="btn btn-sm btn-outline-primary"><i class="feather-edit-2"></i></button>
                                            <button wire:click="deleteFeature({{ $feature->id }})" wire:confirm="Delete this feature?" class="btn btn-sm btn-outline-danger"><i class="feather-trash-2"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Feature Modal -->
                @if($showFeatureModal)
                    <div class="modal show d-block" style="background: rgba(0,0,0,0.5);">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header"><h5 class="modal-title">{{ $editingFeatureId ? 'Edit' : 'Add' }} Feature</h5><button type="button" class="btn-close" wire:click="$set('showFeatureModal', false)"></button></div>
                                <div class="modal-body">
                                    <div class="mb-3"><label class="form-label fw-bold">Title</label><input type="text" wire:model="featureTitle" class="form-control">@error('featureTitle')<small class="text-danger">{{ $message }}</small>@enderror</div>
                                    <div class="mb-3"><label class="form-label fw-bold">Description</label><textarea wire:model="featureDescription" class="form-control" rows="3"></textarea>@error('featureDescription')<small class="text-danger">{{ $message }}</small>@enderror</div>
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6"><label class="form-label fw-bold">Icon (feather class)</label><input type="text" wire:model="featureIcon" class="form-control" placeholder="feather-zap"></div>
                                        <div class="col-md-6"><label class="form-label fw-bold">Color</label><select wire:model="featureColor" class="form-select">
                                            @foreach(['primary','success','info','warning','danger','purple','teal','dark'] as $c)<option value="{{ $c }}">{{ ucfirst($c) }}</option>@endforeach
                                        </select></div>
                                    </div>
                                </div>
                                <div class="modal-footer"><button wire:click="saveFeature" class="btn btn-primary"><i class="feather-save me-1"></i> Save</button></div>
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            <!-- TESTIMONIALS TAB -->
            @if($activeTab === 'testimonials')
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Testimonials</h5>
                        <button wire:click="createTestimonial" class="btn btn-sm btn-primary"><i class="feather-plus me-1"></i> Add Testimonial</button>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0">
                            <thead><tr><th>Author</th><th>Quote</th><th>Rating</th><th>Actions</th></tr></thead>
                            <tbody>
                                @foreach($testimonials as $t)
                                    <tr>
                                        <td><strong>{{ $t->author_name }}</strong><br><small class="text-muted">{{ $t->author_role }}, {{ $t->author_company }}</small></td>
                                        <td><small>{{ Str::limit($t->quote, 80) }}</small></td>
                                        <td>{{ $t->rating }}/5</td>
                                        <td>
                                            <button wire:click="editTestimonial({{ $t->id }})" class="btn btn-sm btn-outline-primary"><i class="feather-edit-2"></i></button>
                                            <button wire:click="deleteTestimonial({{ $t->id }})" wire:confirm="Delete?" class="btn btn-sm btn-outline-danger"><i class="feather-trash-2"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($showTestimonialModal)
                    <div class="modal show d-block" style="background: rgba(0,0,0,0.5);">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header"><h5 class="modal-title">{{ $editingTestimonialId ? 'Edit' : 'Add' }} Testimonial</h5><button type="button" class="btn-close" wire:click="$set('showTestimonialModal', false)"></button></div>
                                <div class="modal-body">
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-4"><label class="form-label fw-bold">Name</label><input type="text" wire:model="testimonialName" class="form-control"></div>
                                        <div class="col-md-4"><label class="form-label fw-bold">Role</label><input type="text" wire:model="testimonialRole" class="form-control"></div>
                                        <div class="col-md-4"><label class="form-label fw-bold">Company</label><input type="text" wire:model="testimonialCompany" class="form-control"></div>
                                    </div>
                                    <div class="mb-3"><label class="form-label fw-bold">Quote</label><textarea wire:model="testimonialQuote" class="form-control" rows="3"></textarea></div>
                                    <div class="mb-3"><label class="form-label fw-bold">Rating (1-5)</label><input type="number" wire:model="testimonialRating" class="form-control" min="1" max="5"></div>
                                </div>
                                <div class="modal-footer"><button wire:click="saveTestimonial" class="btn btn-primary"><i class="feather-save me-1"></i> Save</button></div>
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            <!-- FAQS TAB -->
            @if($activeTab === 'faqs')
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">FAQs</h5>
                        <button wire:click="createFaq" class="btn btn-sm btn-primary"><i class="feather-plus me-1"></i> Add FAQ</button>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0">
                            <thead><tr><th>#</th><th>Question</th><th>Category</th><th>Status</th><th>Actions</th></tr></thead>
                            <tbody>
                                @foreach($faqs as $faq)
                                    <tr>
                                        <td>{{ $faq->sort_order }}</td>
                                        <td><strong>{{ $faq->question }}</strong><br><small class="text-muted">{{ Str::limit($faq->answer, 60) }}</small></td>
                                        <td><span class="badge bg-soft-primary text-primary">{{ $faq->category ?? 'general' }}</span></td>
                                        <td><span class="badge {{ $faq->is_active ? 'bg-soft-success text-success' : 'bg-soft-secondary text-secondary' }}">{{ $faq->is_active ? 'Active' : 'Hidden' }}</span></td>
                                        <td>
                                            <button wire:click="editFaq({{ $faq->id }})" class="btn btn-sm btn-outline-primary"><i class="feather-edit-2"></i></button>
                                            <button wire:click="deleteFaq({{ $faq->id }})" wire:confirm="Delete?" class="btn btn-sm btn-outline-danger"><i class="feather-trash-2"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($showFaqModal)
                    <div class="modal show d-block" style="background: rgba(0,0,0,0.5);">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header"><h5 class="modal-title">{{ $editingFaqId ? 'Edit' : 'Add' }} FAQ</h5><button type="button" class="btn-close" wire:click="$set('showFaqModal', false)"></button></div>
                                <div class="modal-body">
                                    <div class="mb-3"><label class="form-label fw-bold">Question</label><input type="text" wire:model="faqQuestion" class="form-control">@error('faqQuestion')<small class="text-danger">{{ $message }}</small>@enderror</div>
                                    <div class="mb-3"><label class="form-label fw-bold">Answer</label><textarea wire:model="faqAnswer" class="form-control" rows="4"></textarea>@error('faqAnswer')<small class="text-danger">{{ $message }}</small>@enderror</div>
                                    <div class="mb-3"><label class="form-label fw-bold">Category</label><select wire:model="faqCategory" class="form-select"><option value="general">General</option><option value="pricing">Pricing</option><option value="technical">Technical</option></select></div>
                                </div>
                                <div class="modal-footer"><button wire:click="saveFaq" class="btn btn-primary"><i class="feather-save me-1"></i> Save</button></div>
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
