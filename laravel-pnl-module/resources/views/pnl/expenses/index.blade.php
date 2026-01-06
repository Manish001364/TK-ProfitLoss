@extends('adminlte::page')

@section('title', 'Expenses')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-receipt"></i> Expenses</h1>
        <a href="{{ route('pnl.expenses.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Expense
        </a>
    </div>
@stop

@section('content')
    <!-- Filters -->
    <div class="card card-outline card-primary mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('pnl.expenses.index') }}" class="row align-items-end">
                <div class="col-md-3">
                    <div class="form-group mb-0">
                        <label>Event</label>
                        <select name="event_id" class="form-control select2">
                            <option value="">All Events</option>
                            @foreach($events as $event)
                                <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                                    {{ $event->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-0">
                        <label>Category</label>
                        <select name="category_id" class="form-control">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-0">
                        <label>Payment Status</label>
                        <select name="payment_status" class="form-control">
                            <option value="">All</option>
                            <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-0">
                        <label>Search</label>
                        <input type="text" name="search" class="form-control" placeholder="Title..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filter</button>
                    <a href="{{ route('pnl.expenses.index') }}" class="btn btn-secondary"><i class="fas fa-times"></i></a>
                </div>
            </form>
        </div>
    </div>

    <!-- Expenses Table -->
    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>Title</th>
                        <th>Event</th>
                        <th>Category</th>
                        <th>Vendor</th>
                        <th>Date</th>
                        <th class="text-right">Amount</th>
                        <th>Payment</th>
                        <th width="100">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $expense)
                        <tr>
                            <td><strong><a href="{{ route('pnl.expenses.show', $expense) }}">{{ $expense->title }}</a></strong></td>
                            <td><a href="{{ route('pnl.events.show', $expense->event) }}">{{ $expense->event->name }}</a></td>
                            <td>
                                <span style="color: {{ $expense->category->color }}">
                                    <i class="{{ $expense->category->icon ?? 'fas fa-tag' }}"></i>
                                    {{ $expense->category->name }}
                                </span>
                            </td>
                            <td>{{ $expense->vendor?->display_name ?? '-' }}</td>
                            <td>{{ $expense->expense_date->format('d M Y') }}</td>
                            <td class="text-right">
                                <strong>₹{{ number_format($expense->total_amount, 0) }}</strong>
                                @if($expense->tax_amount > 0)
                                    <br><small class="text-muted">+ ₹{{ number_format($expense->tax_amount, 0) }} tax</small>
                                @endif
                            </td>
                            <td>
                                @if($expense->payment)
                                    <span class="badge badge-{{ $expense->payment->status_color }}">
                                        {{ ucfirst($expense->payment->status) }}
                                    </span>
                                    @if($expense->payment->is_overdue)
                                        <span class="badge badge-danger">Overdue</span>
                                    @endif
                                @else
                                    <span class="badge badge-secondary">No Payment</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('pnl.expenses.edit', $expense) }}" class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                            onclick="confirmDelete('{{ route('pnl.expenses.destroy', $expense) }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="fas fa-receipt fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">No expenses found</h5>
                                <a href="{{ route('pnl.expenses.create') }}" class="btn btn-primary mt-2">
                                    <i class="fas fa-plus"></i> Add Expense
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($expenses->hasPages())
            <div class="card-footer">{{ $expenses->links() }}</div>
        @endif
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white">Confirm Delete</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">Are you sure you want to delete this expense?</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <form id="deleteForm" method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap4-theme@1.0.0/dist/select2-bootstrap4.min.css">
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $('.select2').select2({ theme: 'bootstrap4' });
        function confirmDelete(url) {
            document.getElementById('deleteForm').action = url;
            $('#deleteModal').modal('show');
        }
    </script>
@stop
