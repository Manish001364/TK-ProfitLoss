@extends('pnl.layouts.app')

@section('pnl_content')
    <div class="container-fluid" style="max-width: 1100px;">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Events</h4>
            <a href="{{ route('pnl.events.create') }}" class="btn btn-danger btn-sm">
                <i class="fas fa-plus"></i> Create Event
            </a>
        </div>

        <!-- Filters -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
                <form method="GET" action="{{ route('pnl.events.index') }}" id="filterForm" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small text-muted mb-1">Search</label>
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Event name..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted mb-1">Status</label>
                        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">All Status</option>
                            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="planning" {{ request('status') === 'planning' ? 'selected' : '' }}>Planning</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small text-muted mb-1">Date Range</label>
                        <input type="text" name="date_range" id="dateRangePicker" class="form-control form-control-sm" placeholder="Select date range">
                        <input type="hidden" name="date_from" id="date_from" value="{{ request('date_from') }}">
                        <input type="hidden" name="date_to" id="date_to" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i> Filter</button>
                        <a href="{{ route('pnl.events.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-times"></i></a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Events Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">Event Name</th>
                                <th class="border-0">Date</th>
                                <th class="border-0">Venue</th>
                                <th class="border-0">Status</th>
                                <th class="border-0 text-end">Budget</th>
                                <th class="border-0 text-end">Revenue</th>
                                <th class="border-0 text-end">Expenses</th>
                                <th class="border-0 text-end">P&L</th>
                                <th class="border-0" width="120">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($events as $event)
                                <tr>
                                    <td class="border-0">
                                        <strong><a href="{{ route('pnl.events.show', $event) }}" class="text-dark">{{ $event->name }}</a></strong>
                                    </td>
                                    <td class="border-0">{{ $event->event_date->format('d M Y') }}</td>
                                    <td class="border-0 small">{{ $event->venue ?? '-' }}</td>
                                    <td class="border-0">
                                        @php
                                            $statusColors = [
                                                'draft' => 'secondary',
                                                'planning' => 'info',
                                                'active' => 'primary',
                                                'completed' => 'success',
                                                'cancelled' => 'danger',
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$event->status] ?? 'secondary' }}-subtle text-{{ $statusColors[$event->status] ?? 'secondary' }}">
                                            {{ ucfirst($event->status) }}
                                        </span>
                                    </td>
                                    <td class="border-0 text-end">£{{ number_format($event->budget, 0) }}</td>
                                    <td class="border-0 text-end text-success">£{{ number_format($event->total_revenue, 0) }}</td>
                                    <td class="border-0 text-end text-danger">£{{ number_format($event->total_expenses, 0) }}</td>
                                    <td class="border-0 text-end">
                                        @php $profit = $event->net_profit; @endphp
                                        <span class="{{ $profit >= 0 ? 'text-success' : 'text-danger' }} fw-bold">
                                            {{ $profit >= 0 ? '' : '-' }}£{{ number_format(abs($profit), 0) }}
                                        </span>
                                    </td>
                                    <td class="border-0">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('pnl.events.show', $event) }}" class="btn btn-outline-secondary" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('pnl.events.edit', $event) }}" class="btn btn-outline-secondary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-danger" onclick="confirmDelete('{{ route('pnl.events.destroy', $event) }}')" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5 border-0">
                                        <i class="fas fa-calendar-plus fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">No events found</h5>
                                        <a href="{{ route('pnl.events.create') }}" class="btn btn-danger btn-sm mt-2">
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
                <div class="card-footer bg-white border-0">{{ $events->links() }}</div>
            @endif
        </div>

        <!-- Delete Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <h6 class="modal-title">Confirm Delete</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">Are you sure you want to delete this event?</div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <form id="deleteForm" method="POST" style="display:inline;">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('customjs')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#dateRangePicker').daterangepicker({
                autoUpdateInput: false,
                locale: { cancelLabel: 'Clear', format: 'YYYY-MM-DD' },
                opens: 'left'
            });

            $('#dateRangePicker').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('DD MMM YYYY') + ' - ' + picker.endDate.format('DD MMM YYYY'));
                $('#date_from').val(picker.startDate.format('YYYY-MM-DD'));
                $('#date_to').val(picker.endDate.format('YYYY-MM-DD'));
            });

            $('#dateRangePicker').on('cancel.daterangepicker', function() {
                $(this).val('');
                $('#date_from').val('');
                $('#date_to').val('');
            });
        });

        function confirmDelete(url) {
            document.getElementById('deleteForm').action = url;
            var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        }
    </script>
@endsection
