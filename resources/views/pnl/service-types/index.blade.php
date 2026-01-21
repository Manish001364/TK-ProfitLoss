@extends('pnl.layouts.app')

@section('pnl_content')
    <div class="container-fluid" style="max-width: 1100px;">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-0"><i class="fas fa-user-tag me-2"></i>Service Types</h4>
                <small class="text-muted">Manage vendor/artist service categories</small>
            </div>
            <a href="{{ route('pnl.service-types.create') }}" class="btn btn-danger btn-sm">
                <i class="fas fa-plus"></i> Add Custom Type
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Info Alert -->
        <div class="alert alert-light border mb-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-info-circle text-info me-2 fa-lg"></i>
                <div>
                    <strong>System Default Types</strong> are available for all users and cannot be edited. 
                    Create your own <strong>Custom Types</strong> for specific vendor categories unique to your events.
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="60">Icon</th>
                                <th>Service Type</th>
                                <th>Description</th>
                                <th>Vendors</th>
                                <th>Source</th>
                                <th width="120">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($serviceTypes as $type)
                                @php
                                    $isSystem = isset($type->is_system) ? $type->is_system : true;
                                @endphp
                                <tr class="{{ $isSystem ? 'table-light' : '' }}">
                                    <td class="text-center">
                                        <span style="color: {{ $type->color ?? '#6366f1' }}; font-size: 1.25rem;">
                                            <i class="{{ $type->icon ?? 'fas fa-tag' }}"></i>
                                        </span>
                                    </td>
                                    <td>
                                        <strong>{{ $type->name }}</strong>
                                        <br><small class="text-muted">Slug: {{ $type->slug }}</small>
                                    </td>
                                    <td class="small text-muted">{{ $type->description ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $type->vendors_count ?? 0 }}</span>
                                    </td>
                                    <td>
                                        @if($isSystem)
                                            <span class="badge bg-dark"><i class="fas fa-shield-alt me-1"></i>System Default</span>
                                        @else
                                            <span class="badge bg-success"><i class="fas fa-user me-1"></i>Custom</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!$isSystem)
                                            <div class="btn-group">
                                                <a href="{{ route('pnl.service-types.edit', $type->id) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if(($type->vendors_count ?? 0) == 0)
                                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                                            onclick="confirmDelete('{{ route('pnl.service-types.destroy', $type->id) }}')" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted small"><i class="fas fa-lock"></i> Protected</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i class="fas fa-user-tag fa-4x text-muted mb-3"></i>
                                        <h5 class="text-muted">No service types found</h5>
                                        <p class="text-muted">System default types will appear after running the migration.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Delete Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Confirm Delete</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">Are you sure you want to delete this service type?</div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <form id="deleteForm" method="POST" style="display:inline;">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('customjs')
    <script>
        function confirmDelete(url) {
            document.getElementById('deleteForm').action = url;
            var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }
    </script>
@endsection
