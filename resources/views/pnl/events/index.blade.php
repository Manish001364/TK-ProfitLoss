@extends('layouts.organiser_layout')

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0"><i class="fas fa-calendar-alt"></i> Events</h1>
            <a href="{{ route('pnl.events.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create Event
            </a>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('pnl.events.index') }}" class="row align-items-end">
                    <div class="col-md-3">
                        <div class="form-group mb-2 mb-md-0">
                            <label>Search</label>
                            <input type="text" name="search" class="form-control" placeholder="Event name..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-2 mb-md-0">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="">All Status</option>
                                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="planning" {{ request('status') === 'planning' ? 'selected' : '' }}>Planning</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-2 mb-md-0">
                            <label>From Date</label>
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-2 mb-md-0">
                            <label>To Date</label>
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Filter
                        </button>
                        <a href="{{ route('pnl.events.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Events Table -->
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Event Name</th>
                                <th>Date</th>
                                <th>Venue</th>
                                <th>Status</th>
                                <th class="text-end">Budget</th>
                                <th class="text-end">Revenue</th>
                                <th class="text-end">Expenses</th>
                                <th class="text-end">P&L</th>
                                <th width="150">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($events as $event)
                                <tr>
                                    <td>
                                        <strong><a href="{{ route('pnl.events.show', $event) }}">{{ $event->name }}</a></strong>
                                    </td>
                                    <td>{{ $event->event_date->format('d M Y') }}</td>
                                    <td>{{ $event->venue ?? '-' }}</td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'draft' => 'secondary',
                                                'planning' => 'info',
                                                'active' => 'primary',
                                                'completed' => 'success',
                                                'cancelled' => 'danger',
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$event->status] ?? 'secondary' }}">
                                            {{ ucfirst($event->status) }}
                                        </span>
                                    </td>
                                    <td class="text-end">₹{{ number_format($event->budget, 0) }}</td>
                                    <td class="text-end text-success">₹{{ number_format($event->total_revenue, 0) }}</td>
                                    <td class="text-end text-danger">₹{{ number_format($event->total_expenses, 0) }}</td>
                                    <td class="text-end">
                                        @php $profit = $event->net_profit; @endphp
                                        <span class="{{ $profit >= 0 ? 'text-success' : 'text-danger' }} fw-bold">
                                            {{ $profit >= 0 ? '' : '-' }}₹{{ number_format(abs($profit), 0) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('pnl.events.show', $event) }}" class="btn btn-sm btn-outline-primary" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('pnl.events.edit', $event) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('pnl.events.duplicate', $event) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-info" title="Duplicate">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    onclick="confirmDelete('{{ route('pnl.events.destroy', $event) }}')" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <i class="fas fa-calendar-plus fa-4x text-muted mb-3"></i>
                                        <h5 class="text-muted">No events found</h5>
                                        <a href="{{ route('pnl.events.create') }}" class="btn btn-primary mt-2">
                                            <i class="fas fa-plus"></i> Create Your First Event
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($events->hasPages())
                <div class="card-footer">
                    {{ $events->links() }}
                </div>
            @endif
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title"><i class="fas fa-exclamation-triangle"></i> Confirm Delete</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this event? This action cannot be undone.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <form id="deleteForm" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
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
