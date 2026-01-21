@extends('pnl.layouts.app')

@section('pnl_content')
    <div class="container-fluid" style="max-width: 1200px;">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-0"><i class="fas fa-cogs me-2"></i>Categories & Services</h4>
                <small class="text-muted">Manage expense categories and vendor service types</small>
            </div>
        </div>

        <!-- Info Alert -->
        <div class="alert alert-light border mb-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-info-circle text-info me-2 fa-lg"></i>
                <div>
                    <strong>System Defaults</strong> are available for all users and cannot be edited. 
                    Create your own <strong>Custom</strong> entries for specific needs.
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Expense Categories Section -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center py-3">
                        <h6 class="mb-0"><i class="fas fa-tags me-2"></i>Expense Categories</h6>
                        <a href="{{ route('pnl.categories.create') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-plus"></i> Add
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                            <table class="table table-hover mb-0 small">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th>Category</th>
                                        <th>Type</th>
                                        <th>Source</th>
                                        <th width="80">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($expenseCategories as $category)
                                        @php
                                            $isSystem = isset($category->is_system) ? $category->is_system : ($category->user_id === null);
                                        @endphp
                                        <tr class="{{ $isSystem ? 'table-light' : '' }}">
                                            <td>
                                                <span style="color: {{ $category->color }}">
                                                    <i class="{{ $category->icon ?? 'fas fa-tag' }}"></i>
                                                </span>
                                                <strong class="ms-1">{{ $category->name }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $category->type === 'fixed' ? 'primary' : 'info' }} small">
                                                    {{ ucfirst($category->type) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($isSystem)
                                                    <span class="badge bg-dark small"><i class="fas fa-lock me-1"></i>System</span>
                                                @else
                                                    <span class="badge bg-success small"><i class="fas fa-user me-1"></i>Custom</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if(!$isSystem)
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="{{ route('pnl.categories.edit', $category) }}" class="btn btn-outline-warning btn-sm" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        @if(($category->expenses_count ?? 0) == 0)
                                                            <button type="button" class="btn btn-outline-danger btn-sm" 
                                                                    onclick="confirmDeleteCategory('{{ route('pnl.categories.destroy', $category) }}')" title="Delete">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-muted small">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">
                                                <i class="fas fa-tags fa-2x mb-2 d-block"></i>
                                                No categories found
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-light text-muted small">
                        {{ $expenseCategories->count() }} categories total
                    </div>
                </div>
            </div>

            <!-- Service Types Section -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-3">
                        <h6 class="mb-0"><i class="fas fa-user-tag me-2"></i>Vendor Service Types</h6>
                        <a href="{{ route('pnl.service-types.create') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-plus"></i> Add
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                            <table class="table table-hover mb-0 small">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th>Service Type</th>
                                        <th>Source</th>
                                        <th width="80">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($serviceTypes as $type)
                                        @php
                                            $isSystem = isset($type->is_system) ? $type->is_system : false;
                                        @endphp
                                        <tr class="{{ $isSystem ? 'table-light' : '' }}">
                                            <td>
                                                <span style="color: {{ $type->color ?? '#6366f1' }}">
                                                    <i class="{{ $type->icon ?? 'fas fa-user' }}"></i>
                                                </span>
                                                <strong class="ms-1">{{ $type->name }}</strong>
                                                @if($type->description)
                                                    <br><small class="text-muted">{{ Str::limit($type->description, 40) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($isSystem)
                                                    <span class="badge bg-dark small"><i class="fas fa-lock me-1"></i>System</span>
                                                @else
                                                    <span class="badge bg-success small"><i class="fas fa-user me-1"></i>Custom</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if(!$isSystem)
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="{{ route('pnl.service-types.edit', $type->id) }}" class="btn btn-outline-warning btn-sm" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-outline-danger btn-sm" 
                                                                onclick="confirmDeleteServiceType('{{ route('pnl.service-types.destroy', $type->id) }}')" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                @else
                                                    <span class="text-muted small">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-4 text-muted">
                                                <i class="fas fa-user-tag fa-2x mb-2 d-block"></i>
                                                No service types found
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-light text-muted small">
                        {{ $serviceTypes->count() }} service types total
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Category Modal -->
    <div class="modal fade" id="deleteCategoryModal" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white py-2">
                    <h6 class="modal-title">Delete Category</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">Are you sure you want to delete this expense category?</div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteCategoryForm" method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Service Type Modal -->
    <div class="modal fade" id="deleteServiceTypeModal" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white py-2">
                    <h6 class="modal-title">Delete Service Type</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">Are you sure you want to delete this service type?</div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteServiceTypeForm" method="POST" style="display:inline;">
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
        function confirmDeleteCategory(url) {
            document.getElementById('deleteCategoryForm').action = url;
            var modal = new bootstrap.Modal(document.getElementById('deleteCategoryModal'));
            modal.show();
        }

        function confirmDeleteServiceType(url) {
            document.getElementById('deleteServiceTypeForm').action = url;
            var modal = new bootstrap.Modal(document.getElementById('deleteServiceTypeModal'));
            modal.show();
        }
    </script>
@endsection
