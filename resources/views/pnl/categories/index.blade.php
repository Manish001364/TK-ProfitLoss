@extends('pnl.layouts.app')

@section('pnl_content')
    <div class="container-fluid" style="max-width: 1100px;">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0"><i class="fas fa-tags me-2"></i>Expense Categories</h4>
            <a href="{{ route('pnl.categories.create') }}" class="btn btn-danger btn-sm">
                <i class="fas fa-plus"></i> Add Category
            </a>
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
                                <th>Expenses Count</th>
                                <th>Status</th>
                                <th width="150">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $category)
                                <tr data-id="{{ $category->id }}">
                                    <td class="text-center">
                                        <i class="fas fa-grip-vertical text-muted drag-handle" style="cursor: grab;"></i>
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
                                    <td>{{ $category->expenses_count }}</td>
                                    <td>
                                        @if($category->user_id === null)
                                            <span class="badge bg-dark">System Default</span>
                                        @else
                                            <span class="badge bg-{{ $category->is_active ? 'success' : 'secondary' }}">
                                                {{ $category->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($category->user_id === auth()->id() || $category->user_id === null)
                                            <div class="btn-group">
                                                @if($category->user_id !== null)
                                                    <a href="{{ route('pnl.categories.edit', $category) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    @if($category->expenses_count == 0)
                                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                                onclick="confirmDelete('{{ route('pnl.categories.destroy', $category) }}')" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @endif
                                                @else
                                                    <span class="text-muted small"><i class="fas fa-lock me-1"></i>Protected</span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="fas fa-tags fa-4x text-muted mb-3"></i>
                                        <h5 class="text-muted">No categories found</h5>
                                        <a href="{{ route('pnl.categories.create') }}" class="btn btn-primary mt-2">
                                            <i class="fas fa-plus"></i> Create Category
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

        // Drag and drop reordering
        new Sortable(document.querySelector('#categories-table tbody'), {
            handle: '.drag-handle',
            animation: 150,
            onEnd: function() {
                const order = [];
                document.querySelectorAll('#categories-table tbody tr').forEach(row => {
                    order.push(row.dataset.id);
                });
                
                fetch('{{ route('pnl.categories.reorder') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ categories: order })
                });
            }
        });
    </script>
@endsection
