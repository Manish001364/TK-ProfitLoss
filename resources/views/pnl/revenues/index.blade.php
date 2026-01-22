@extends('pnl.layouts.app')

@section('pnl_content')
    <div class="container-fluid" style="max-width: 1100px;">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Revenue (Ticket Sales)</h4>
            <a href="{{ route('pnl.revenues.create') }}" class="btn btn-danger btn-sm">
                <i class="fas fa-plus"></i> Add Revenue
            </a>
        </div>

        <!-- Filters -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
                <form method="GET" action="{{ route('pnl.revenues.index') }}" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small text-muted mb-1">Event</label>
                        <select name="event_id" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">All Events</option>
                            @foreach($events as $event)
                                <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>{{ $event->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted mb-1">Ticket Type</label>
                        <select name="ticket_type" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">All Types</option>
                            @foreach($ticketTypes as $key => $label)
                                <option value="{{ $key }}" {{ request('ticket_type') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('pnl.revenues.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-times"></i> Clear</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Revenues Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">Event</th>
                                <th class="border-0">Ticket Type</th>
                                <th class="border-0 text-end">Price</th>
                                <th class="border-0 text-end">Sold / Available</th>
                                <th class="border-0 text-end">Gross Revenue</th>
                                <th class="border-0 text-end">Fees & Taxes</th>
                                <th class="border-0 text-end">Net Revenue</th>
                                <th class="border-0" width="100">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($revenues as $revenue)
                                <tr>
                                    <td class="border-0"><a href="{{ route('pnl.events.show', $revenue->event) }}">{{ $revenue->event->name }}</a></td>
                                    <td class="border-0"><span class="badge bg-primary-subtle text-primary">{{ $revenue->display_name }}</span></td>
                                    <td class="border-0 text-end">£{{ number_format($revenue->ticket_price, 0) }}</td>
                                    <td class="border-0 text-end">
                                        <strong>{{ number_format($revenue->tickets_sold) }}</strong> / {{ number_format($revenue->tickets_available) }}
                                        <a href="{{ route('pnl.revenues.edit', $revenue) }}" class="btn btn-sm btn-link p-0 ms-1" title="Update tickets sold">
                                            <i class="fas fa-plus-circle text-success"></i>
                                        </a>
                                        <br><small class="text-muted">{{ number_format($revenue->sell_through_rate, 1) }}% sold</small>
                                    </td>
                                    <td class="border-0 text-end">£{{ number_format($revenue->gross_revenue, 0) }}</td>
                                    <td class="border-0 text-end text-danger small">
                                        £{{ number_format($revenue->platform_fees + $revenue->payment_gateway_fees + $revenue->taxes, 0) }}
                                    </td>
                                    <td class="border-0 text-end text-success">
                                        <strong>£{{ number_format($revenue->net_revenue_after_refunds, 0) }}</strong>
                                        @if($revenue->refund_amount > 0)
                                            <br><small class="text-warning">-£{{ number_format($revenue->refund_amount, 0) }} refunds</small>
                                        @endif
                                    </td>
                                    <td class="border-0">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('pnl.revenues.edit', $revenue) }}" class="btn btn-outline-secondary" title="Edit"><i class="fas fa-edit"></i></a>
                                            <button type="button" class="btn btn-outline-danger" onclick="confirmDelete('{{ route('pnl.revenues.destroy', $revenue) }}')" title="Delete"><i class="fas fa-trash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 border-0">
                                        <i class="fas fa-ticket-alt fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">No revenue entries found</h5>
                                        <a href="{{ route('pnl.revenues.create') }}" class="btn btn-danger btn-sm mt-2"><i class="fas fa-plus"></i> Add Revenue</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($revenues->hasPages())
                <div class="card-footer bg-white border-0">{{ $revenues->links() }}</div>
            @endif
        </div>

        <!-- Delete Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header border-0"><h6 class="modal-title">Confirm Delete</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">Delete this revenue entry?</div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <form id="deleteForm" method="POST" style="display:inline;">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-danger">Delete</button></form>
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
            var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        }
    </script>
@endsection
