@extends('adminlte::page')

@section('title', 'Create Category')

@section('content_header')
    <h1><i class="fas fa-plus-circle"></i> Create Expense Category</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Category Details</h3>
                </div>
                <form action="{{ route('pnl.categories.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label>Category Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" required placeholder="e.g., Artist Fee, Venue, Marketing">
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group">
                            <label>Type <span class="text-danger">*</span></label>
                            <select name="type" class="form-control @error('type') is-invalid @enderror" required>
                                <option value="fixed" {{ old('type') === 'fixed' ? 'selected' : '' }}>Fixed (consistent cost)</option>
                                <option value="variable" {{ old('type', 'variable') === 'variable' ? 'selected' : '' }}>Variable (varies by event)</option>
                            </select>
                            @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="text-muted">Fixed: Artist fees, Venue | Variable: Marketing, Catering</small>
                        </div>

                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="2">{{ old('description') }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group">
                            <label>Default Budget Limit (₹)</label>
                            <input type="number" step="0.01" name="default_budget_limit" class="form-control @error('default_budget_limit') is-invalid @enderror" 
                                   value="{{ old('default_budget_limit') }}" min="0">
                            @error('default_budget_limit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="text-muted">Optional. Used for budget alerts.</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Color <span class="text-danger">*</span></label>
                                    <input type="color" name="color" class="form-control @error('color') is-invalid @enderror" 
                                           value="{{ old('color', '#6366f1') }}" style="height: 45px;">
                                    @error('color')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Icon (FontAwesome)</label>
                                    <input type="text" name="icon" class="form-control @error('icon') is-invalid @enderror" 
                                           value="{{ old('icon', 'fas fa-tag') }}" placeholder="fas fa-music">
                                    @error('icon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <small class="text-muted">e.g., fas fa-music, fas fa-building</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create Category
                        </button>
                        <a href="{{ route('pnl.categories.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-info-circle"></i> Common Icons</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <p><i class="fas fa-music"></i> <code>fas fa-music</code> (Artist)</p>
                            <p><i class="fas fa-building"></i> <code>fas fa-building</code> (Venue)</p>
                            <p><i class="fas fa-bullhorn"></i> <code>fas fa-bullhorn</code> (Marketing)</p>
                            <p><i class="fas fa-users"></i> <code>fas fa-users</code> (Staff)</p>
                        </div>
                        <div class="col-6">
                            <p><i class="fas fa-cogs"></i> <code>fas fa-cogs</code> (Equipment)</p>
                            <p><i class="fas fa-utensils"></i> <code>fas fa-utensils</code> (Catering)</p>
                            <p><i class="fas fa-shield-alt"></i> <code>fas fa-shield-alt</code> (Security)</p>
                            <p><i class="fas fa-ellipsis-h"></i> <code>fas fa-ellipsis-h</code> (Misc)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
