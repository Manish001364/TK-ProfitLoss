@extends('pnl.layouts.app')

@section('pnl_content')
    <div class="container-fluid" style="max-width: 900px;">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-0"><i class="fas fa-cogs me-2"></i>Categories & Services</h4>
                <small class="text-muted">Manage your custom expense categories and vendor service types</small>
            </div>
        </div>

        <!-- Info Alert -->
        <div class="alert alert-info border-0 mb-4">
            <div class="d-flex">
                <i class="fas fa-lightbulb text-warning me-3 fa-lg mt-1"></i>
                <div>
                    <strong>How it works:</strong><br>
                    <small>
                        • <strong>System Defaults</strong> are pre-defined and always available when creating expenses or vendors.<br>
                        • <strong>Your Custom</strong> entries are shown here for you to manage (edit/delete).<br>
                        • When creating expenses or vendors, you'll see both system defaults AND your custom entries in the dropdown.
                    </small>
                </div>
            </div>
        </div>

        <!-- Your Custom Expense Categories -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center py-3">
                <h6 class="mb-0"><i class="fas fa-tags me-2"></i>Your Custom Expense Categories</h6>
                <a href="{{ route('pnl.categories.create') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-plus"></i> Add New
                </a>
            </div>
            <div class="card-body p-0">
                @if($userCategories->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Category Name</th>
                                    <th>Type</th>
                                    <th>Used In</th>
                                    <th width="120">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($userCategories as $category)
                                    <tr>
                                        <td>
                                            <span style="color: {{ $category->color ?? '#6c757d' }}">
                                                <i class="{{ $category->icon ?? 'fas fa-tag' }}"></i>
                                            </span>
                                            <strong class="ms-2">{{ $category->name }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $category->type === 'fixed' ? 'primary' : 'info' }}">
                                                {{ ucfirst($category->type ?? 'variable') }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ $category->expenses_count ?? 0 }} expenses</span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('pnl.categories.edit', $category) }}" class="btn btn-outline-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if(($category->expenses_count ?? 0) == 0)
                                                    <button type="button" class="btn btn-outline-danger" 
                                                            onclick="confirmDelete('category', '{{ route('pnl.categories.destroy', $category) }}')" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-outline-secondary" disabled title="Cannot delete - in use">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">No custom categories yet</h6>
                        <p class="text-muted small mb-3">You can use system defaults, or create your own custom categories.</p>
                        <a href="{{ route('pnl.categories.create') }}" class="btn btn-danger btn-sm">
                            <i class="fas fa-plus me-1"></i> Create Custom Category
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Your Custom Vendor Service Types -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-3">
                <h6 class="mb-0"><i class="fas fa-user-tag me-2"></i>Your Custom Vendor Service Types</h6>
                <a href="{{ route('pnl.service-types.create') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-plus"></i> Add New
                </a>
            </div>
            <div class="card-body p-0">
                @if($userServiceTypes->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Service Type</th>
                                    <th>Description</th>
                                    <th width="120">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($userServiceTypes as $type)
                                    <tr>
                                        <td>
                                            <span style="color: {{ $type->color ?? '#6366f1' }}">
                                                <i class="{{ $type->icon ?? 'fas fa-user' }}"></i>
                                            </span>
                                            <strong class="ms-2">{{ $type->name }}</strong>
                                        </td>
                                        <td>
                                            <span class="text-muted small">{{ Str::limit($type->description ?? '-', 50) }}</span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('pnl.service-types.edit', $type->id) }}" class="btn btn-outline-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-outline-danger" 
                                                        onclick="confirmDelete('service type', '{{ route('pnl.service-types.destroy', $type->id) }}')" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-user-tag fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">No custom service types yet</h6>
                        <p class="text-muted small mb-3">System defaults (Artist, DJ, Venue, etc.) are always available.</p>
                        <a href="{{ route('pnl.service-types.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i> Create Custom Service Type
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- System Defaults Reference (Collapsible) -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light py-3">
                <a class="text-decoration-none text-dark d-flex justify-content-between align-items-center" 
                   data-bs-toggle="collapse" href="#systemDefaultsCollapse" role="button">
                    <h6 class="mb-0"><i class="fas fa-lock me-2 text-secondary"></i>System Defaults (Read-Only Reference)</h6>
                    <i class="fas fa-chevron-down"></i>
                </a>
            </div>
            <div class="collapse" id="systemDefaultsCollapse">
                <div class="card-body">
                    <div class="row">
                        <!-- System Expense Categories -->
                        <div class="col-md-6 mb-3">
                            <h6 class="text-danger mb-2"><i class="fas fa-tags me-1"></i> Expense Categories</h6>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($systemCategories as $cat)
                                    <span class="badge bg-light text-dark border" style="border-color: {{ $cat->color ?? '#ccc' }} !important;">
                                        <i class="{{ $cat->icon ?? 'fas fa-tag' }} me-1" style="color: {{ $cat->color ?? '#666' }}"></i>
                                        {{ $cat->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        <!-- System Service Types -->
                        <div class="col-md-6 mb-3">
                            <h6 class="text-primary mb-2"><i class="fas fa-user-tag me-1"></i> Vendor Service Types</h6>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($systemServiceTypes as $type)
                                    <span class="badge bg-light text-dark border">
                                        <i class="{{ $type->icon ?? 'fas fa-user' }} me-1" style="color: {{ $type->color ?? '#666' }}"></i>
                                        {{ $type->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <p class="text-muted small mb-0 mt-2">
                        <i class="fas fa-info-circle me-1"></i>
                        These system defaults cannot be edited. They are automatically available in all dropdowns.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white py-2">
                    <h6 class="modal-title">Confirm Delete</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Are you sure you want to delete this <span id="deleteItemType">item</span>?</p>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteForm" method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('customjs')
    <script>
        function confirmDelete(itemType, url) {
            document.getElementById('deleteItemType').textContent = itemType;
            document.getElementById('deleteForm').action = url;
            var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        }
    </script>
@endsection
