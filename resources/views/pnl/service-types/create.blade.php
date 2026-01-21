@extends('pnl.layouts.app')

@section('pnl_content')
    <div class="container-fluid" style="max-width: 700px;">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-0"><i class="fas fa-plus me-2"></i>Add Custom Service Type</h4>
                <small class="text-muted">Create a new vendor/artist category</small>
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
            <div class="card-header bg-danger text-white border-0 py-3">
                <h6 class="mb-0"><i class="fas fa-user-tag me-2"></i>Service Type Details</h6>
            </div>
            <form action="{{ route('pnl.service-types.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label small">Type Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" required placeholder="e.g., Pyrotechnics, Sound Engineer">
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="text-muted">A unique name for this service type</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Color <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="color" name="color" class="form-control form-control-color" 
                                       value="{{ old('color', '#6366f1') }}" style="width: 50px;">
                                <input type="text" class="form-control" id="colorHex" value="{{ old('color', '#6366f1') }}" readonly>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label small">Description</label>
                            <textarea name="description" class="form-control" rows="2" 
                                      placeholder="Brief description of this service type">{{ old('description') }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Icon (Font Awesome class)</label>
                            <div class="input-group">
                                <span class="input-group-text" id="iconPreview"><i class="fas fa-tag"></i></span>
                                <input type="text" name="icon" id="iconInput" class="form-control" 
                                       value="{{ old('icon', 'fas fa-tag') }}" placeholder="fas fa-tag">
                            </div>
                            <small class="text-muted">
                                Browse icons at <a href="https://fontawesome.com/icons" target="_blank">fontawesome.com</a>
                            </small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Preview</label>
                            <div class="form-control bg-light d-flex align-items-center gap-2" id="typePreview">
                                <span id="previewIcon" style="color: #6366f1; font-size: 1.5rem;">
                                    <i class="fas fa-tag"></i>
                                </span>
                                <span id="previewName">New Service Type</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-save me-1"></i> Create Service Type
                        </button>
                        <a href="{{ route('pnl.service-types.index') }}" class="btn btn-outline-secondary">Cancel</a>
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
            document.getElementById('previewIcon').style.color = this.value;
        });
        
        // Icon preview
        document.getElementById('iconInput').addEventListener('input', function() {
            const iconClass = this.value || 'fas fa-tag';
            document.getElementById('iconPreview').innerHTML = '<i class="' + iconClass + '"></i>';
            document.getElementById('previewIcon').innerHTML = '<i class="' + iconClass + '"></i>';
        });
        
        // Name preview
        document.querySelector('[name="name"]').addEventListener('input', function() {
            document.getElementById('previewName').textContent = this.value || 'New Service Type';
        });
    </script>
@endsection
