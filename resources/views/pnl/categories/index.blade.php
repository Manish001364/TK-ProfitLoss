@extends('pnl.layouts.app')

@section('pnl_content')
    <div class="container-fluid" style="max-width: 1100px;">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-0"><i class="fas fa-tags me-2"></i>Expense Categories</h4>
                <small class="text-muted">System defaults + your custom categories</small>
            </div>
            <a href="{{ route('pnl.categories.create') }}" class="btn btn-danger btn-sm">
                <i class="fas fa-plus"></i> Add Custom Category
            </a>
        </div>

        <!-- Info Alert -->
        <div class="alert alert-light border mb-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-info-circle text-info me-2 fa-lg"></i>
                <div>
                    <strong>System Default Categories</strong> are available for all users and cannot be edited. 
                    Create your own <strong>Custom Categories</strong> for specific needs.
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="categories-table">
                        <thead class="table-light">
                            <tr>
                                <th width="50"></th>
                                <th>Category</th>
                                <th>Type</th>
                                <th>Budget Limit</th>
                                <th>Expenses</th>
                                <th>Source</th>
                                <th width="150">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $category)
                                @php
                                    $isSystem = isset($category->is_system) ? $category->is_system : ($category->user_id === null);
                                @endphp
                                <tr data-id="{{ $category->id }}" class="{{ $isSystem ? 'table-light' : '' }}">
                                    <td class="text-center">
                                        @if(!$isSystem)
                                            <i class="fas fa-grip-vertical text-muted drag-handle" style="cursor: grab;"></i>
                                        @else
                                            <i class="fas fa-lock text-muted small"></i>
                                        @endif
                                    </td>
                                    <td>
                                        <span style="color: {{ $category->color }}">
                                            <i class="{{ $category->icon ?? 'fas fa-tag' }} fa-lg"></i>
                                        </span>
                                        <strong class="ms-2">{{ $category->name }}</strong>
                                        @if($category->description)
                                            <br><small class="text-muted">{{ $category->description }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $category->type === 'fixed' ? 'primary' : 'info' }}">
                                            {{ ucfirst($category->type) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($category->default_budget_limit)
                                            £{{ number_format($category->default_budget_limit, 0) }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $category->expenses_count ?? 0 }}</td>
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
                                                <a href="{{ route('pnl.categories.edit', $category) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if(($category->expenses_count ?? 0) == 0)
                                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                                            onclick="confirmDelete('{{ route('pnl.categories.destroy', $category) }}')" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted small">Protected</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="fas fa-tags fa-4x text-muted mb-3"></i>
                                        <h5 class="text-muted">No categories found</h5>
                                        <p class="text-muted">System default categories will appear after running the migration.</p>
                                        <a href="{{ route('pnl.categories.create') }}" class="btn btn-primary mt-2">
                                            <i class="fas fa-plus"></i> Create Custom Category
                                        </a>
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
                    <div class="modal-body">Are you sure you want to delete this category?</div>
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
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        function confirmDelete(url) {
            document.getElementById('deleteForm').action = url;
            var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }

        // Drag and drop reordering (only for custom categories)
        new Sortable(document.querySelector('#categories-table tbody'), {
            handle: '.drag-handle',
            animation: 150,
            filter: '.table-light', // Don't allow dragging system categories
            onEnd: function() {
                const order = [];
                document.querySelectorAll('#categories-table tbody tr:not(.table-light)').forEach(row => {
                    if (row.dataset.id) order.push(row.dataset.id);
                });
                
                if (order.length > 0) {
                    fetch('{{ route('pnl.categories.reorder') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ categories: order })
                    });
                }
            }
        });
    </script>
@endsection
