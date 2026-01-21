@extends('pnl.layouts.app')

@section('pnl_content')
    <div class="container-fluid" style="max-width: 900px;">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Create Expense Category</h4>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-danger text-white border-0 py-3">
                        <h6 class="mb-0"><i class="fas fa-tag me-2"></i>Category Details</h6>
                    </div>
                    <form action="{{ route('pnl.categories.store') }}" method="POST">
                        @csrf
                        <div class="card-body">
                            <div class="form-group mb-3">
                                <label class="form-label small">Category Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name') }}" required placeholder="e.g., Artist Fee, Venue, Marketing">
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label small">Type <span class="text-danger">*</span></label>
                                <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                    <option value="fixed" {{ old('type') === 'fixed' ? 'selected' : '' }}>Fixed (consistent cost)</option>
                                    <option value="variable" {{ old('type', 'variable') === 'variable' ? 'selected' : '' }}>Variable (varies by event)</option>
                                </select>
                                @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <small class="text-muted">Fixed: Artist fees, Venue | Variable: Marketing, Catering</small>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label small">Description</label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="2" placeholder="Optional description">{{ old('description') }}</textarea>
                                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label small">Default Budget Limit (£)</label>
                                <div class="input-group">
                                    <span class="input-group-text">£</span>
                                    <input type="number" step="0.01" name="default_budget_limit" class="form-control @error('default_budget_limit') is-invalid @enderror" 
                                           value="{{ old('default_budget_limit') }}" min="0" placeholder="0.00">
                                </div>
                                @error('default_budget_limit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <small class="text-muted">Optional. Used for budget warnings when creating expenses.</small>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label small">Color <span class="text-danger">*</span></label>
                                        <input type="color" name="color" class="form-control form-control-color @error('color') is-invalid @enderror" 
                                               value="{{ old('color', '#6366f1') }}" style="height: 45px; width: 100%;">
                                        @error('color')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label small">Icon (FontAwesome)</label>
                                        <input type="text" name="icon" class="form-control @error('icon') is-invalid @enderror" 
                                               value="{{ old('icon', 'fas fa-tag') }}" placeholder="fas fa-music">
                                        @error('icon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-0 pt-0">
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-save me-1"></i> Create Category
                            </button>
                            <a href="{{ route('pnl.configuration.index') }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-info text-white border-0 py-3">
                        <h6 class="mb-0"><i class="fas fa-icons me-2"></i>Common Icons</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <p class="mb-2"><i class="fas fa-music me-2"></i><code>fas fa-music</code></p>
                                <p class="mb-2"><i class="fas fa-building me-2"></i><code>fas fa-building</code></p>
                                <p class="mb-2"><i class="fas fa-bullhorn me-2"></i><code>fas fa-bullhorn</code></p>
                                <p class="mb-2"><i class="fas fa-users me-2"></i><code>fas fa-users</code></p>
                            </div>
                            <div class="col-6">
                                <p class="mb-2"><i class="fas fa-cogs me-2"></i><code>fas fa-cogs</code></p>
                                <p class="mb-2"><i class="fas fa-utensils me-2"></i><code>fas fa-utensils</code></p>
                                <p class="mb-2"><i class="fas fa-shield-alt me-2"></i><code>fas fa-shield-alt</code></p>
                                <p class="mb-2"><i class="fas fa-truck me-2"></i><code>fas fa-truck</code></p>
                            </div>
                        </div>
                        <hr>
                        <small class="text-muted">Find more icons at <a href="https://fontawesome.com/icons" target="_blank">fontawesome.com/icons</a></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
