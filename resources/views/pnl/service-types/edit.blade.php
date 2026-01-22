@extends('pnl.layouts.app')

@section('pnl_content')
    <div class="container-fluid" style="max-width: 700px;">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Service Type</h4>
                <small class="text-muted">Modify custom vendor category</small>
            </div>
            <a href="{{ route('pnl.service-types.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-warning text-dark border-0 py-3">
                <h6 class="mb-0"><i class="fas fa-user-tag me-2"></i>Edit: {{ $serviceType->name }}</h6>
            </div>
            <form action="{{ route('pnl.service-types.update', $serviceType->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label small">Type Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $serviceType->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="text-muted">Slug: {{ $serviceType->slug }} (cannot be changed)</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Color <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="color" name="color" class="form-control form-control-color" 
                                       value="{{ old('color', $serviceType->color ?? '#6366f1') }}" style="width: 50px;">
                                <input type="text" class="form-control" id="colorHex" 
                                       value="{{ old('color', $serviceType->color ?? '#6366f1') }}" readonly>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label small">Description</label>
                            <textarea name="description" class="form-control" rows="2">{{ old('description', $serviceType->description) }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Icon (Font Awesome class)</label>
                            <div class="input-group">
                                <span class="input-group-text" id="iconPreview">
                                    <i class="{{ old('icon', $serviceType->icon ?? 'fas fa-tag') }}"></i>
                                </span>
                                <input type="text" name="icon" id="iconInput" class="form-control" 
                                       value="{{ old('icon', $serviceType->icon ?? 'fas fa-tag') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Status</label>
                            <div class="form-check form-switch mt-2">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" 
                                       {{ old('is_active', $serviceType->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save me-1"></i> Update Service Type
                        </button>
                        <a href="{{ route('pnl.configuration.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('customjs')
    <script>
        // Color picker sync
        document.querySelector('[name="color"]').addEventListener('input', function() {
            document.getElementById('colorHex').value = this.value;
        });
        
        // Icon preview
        document.getElementById('iconInput').addEventListener('input', function() {
            const iconClass = this.value || 'fas fa-tag';
            document.getElementById('iconPreview').innerHTML = '<i class="' + iconClass + '"></i>';
        });
    </script>
@endsection
