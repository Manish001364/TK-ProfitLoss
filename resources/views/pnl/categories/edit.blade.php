@extends('pnl.layouts.app')

@section('pnl_content')
    <div class="container-fluid" style="max-width: 900px;">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">Edit Category</h4>
                <p class="text-muted small mb-0">Update expense category details</p>
            </div>
            <a href="{{ route('pnl.categories.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
        </div>

        <form action="{{ route('pnl.categories.update', $category) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-danger text-white border-0 py-3">
                    <h6 class="mb-0"><i class="fas fa-tag me-2"></i>Category Details</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small">Category Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $category->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Type</label>
                            <select name="type" class="form-select">
                                <option value="fixed" {{ old('type', $category->type) === 'fixed' ? 'selected' : '' }}>Fixed</option>
                                <option value="variable" {{ old('type', $category->type) === 'variable' ? 'selected' : '' }}>Variable</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Color</label>
                            <input type="color" name="color" class="form-control form-control-color w-100" 
                                   value="{{ old('color', $category->color) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Icon</label>
                            <input type="text" name="icon" class="form-control" 
                                   value="{{ old('icon', $category->icon) }}" placeholder="fas fa-tag">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Sort Order</label>
                            <input type="number" name="sort_order" class="form-control" 
                                   value="{{ old('sort_order', $category->sort_order) }}" min="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Default Budget Limit (£)</label>
                            <input type="number" step="0.01" name="default_budget_limit" class="form-control" 
                                   value="{{ old('default_budget_limit', $category->default_budget_limit) }}" min="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Status</label>
                            <div class="form-check form-switch mt-2">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" 
                                       {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label small">Description</label>
                            <textarea name="description" class="form-control" rows="2">{{ old('description', $category->description) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-danger"><i class="fas fa-save me-1"></i> Update Category</button>
                <a href="{{ route('pnl.categories.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
