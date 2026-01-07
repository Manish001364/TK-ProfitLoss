@extends('adminlte::page')

@section('title', 'Revenues')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-ticket-alt"></i> Revenue (Ticket Sales)</h1>
        <a href="{{ route('pnl.revenues.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Revenue
        </a>
    </div>
@stop

@section('content')
    <!-- Filters -->
    <div class="card card-outline card-primary mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('pnl.revenues.index') }}" class="row align-items-end">
                <div class="col-md-4">
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
                <div class="col-md-3">
                    <div class="form-group mb-0">
                        <label>Ticket Type</label>
                        <select name="ticket_type" class="form-control">
                            <option value="">All Types</option>
                            @foreach($ticketTypes as $key => $label)
                                <option value="{{ $key }}" {{ request('ticket_type') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filter</button>
                    <a href="{{ route('pnl.revenues.index') }}" class="btn btn-secondary"><i class="fas fa-times"></i></a>
                </div>
            </form>
        </div>
    </div>

    <!-- Revenues Table -->
    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>Event</th>
                        <th>Ticket Type</th>
                        <th class="text-right">Price</th>
                        <th class="text-right">Sold / Available</th>
                        <th class="text-right">Gross Revenue</th>
                        <th class="text-right">Fees & Taxes</th>
                        <th class="text-right">Net Revenue</th>
                        <th width="100">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($revenues as $revenue)
                        <tr>
                            <td><a href="{{ route('pnl.events.show', $revenue->event) }}">{{ $revenue->event->name }}</a></td>
                            <td>
                                <span class="badge badge-primary">{{ $revenue->display_name }}</span>
                            </td>
                            <td class="text-right">₹{{ number_format($revenue->ticket_price, 0) }}</td>
                            <td class="text-right">
                                <strong>{{ number_format($revenue->tickets_sold) }}</strong> / {{ number_format($revenue->tickets_available) }}
                                <br>
                                <small class="text-muted">{{ number_format($revenue->sell_through_rate, 1) }}% sold</small>
                            </td>
                            <td class="text-right">₹{{ number_format($revenue->gross_revenue, 0) }}</td>
                            <td class="text-right text-danger">
                                ₹{{ number_format($revenue->platform_fees + $revenue->payment_gateway_fees + $revenue->taxes, 0) }}
                            </td>
                            <td class="text-right text-success">
                                <strong>₹{{ number_format($revenue->net_revenue_after_refunds, 0) }}</strong>
                                @if($revenue->refund_amount > 0)
                                    <br><small class="text-warning">-₹{{ number_format($revenue->refund_amount, 0) }} refunds</small>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('pnl.revenues.edit', $revenue) }}" class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                            onclick="confirmDelete('{{ route('pnl.revenues.destroy', $revenue) }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="fas fa-ticket-alt fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">No revenue entries found</h5>
                                <a href="{{ route('pnl.revenues.create') }}" class="btn btn-primary mt-2">
                                    <i class="fas fa-plus"></i> Add Revenue Entry
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($revenues->hasPages())
            <div class="card-footer">{{ $revenues->links() }}</div>
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
                <div class="modal-body">Are you sure you want to delete this revenue entry?</div>
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
