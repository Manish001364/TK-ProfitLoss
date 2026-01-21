@extends('pnl.layouts.app')

@section('pnl_content')
    <div class="container-fluid" style="max-width: 900px;">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">Edit Revenue Entry</h4>
                <p class="text-muted small mb-0">Update ticket sales for {{ $revenue->event->name }}</p>
            </div>
            <a href="{{ route('pnl.revenues.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('pnl.revenues.update', $revenue) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-8">
                    <!-- Revenue Details -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-0 py-3">
                            <h6 class="mb-0"><i class="fas fa-ticket-alt me-2 text-danger"></i>Revenue Details</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small">Event <span class="text-danger">*</span></label>
                                    <select name="event_id" class="form-select @error('event_id') is-invalid @enderror" required>
                                        <option value="">Select Event</option>
                                        @foreach($events as $event)
                                            <option value="{{ $event->id }}" {{ old('event_id', $revenue->event_id) == $event->id ? 'selected' : '' }}>
                                                {{ $event->name }} ({{ $event->event_date->format('d M Y') }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('event_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small">Ticket Type <span class="text-danger">*</span></label>
                                    <select name="ticket_type" class="form-select" required>
                                        @foreach($ticketTypes as $key => $label)
                                            <option value="{{ $key }}" {{ old('ticket_type', $revenue->ticket_type) === $key ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small">Custom Ticket Name</label>
                                    <input type="text" name="ticket_name" class="form-control" value="{{ old('ticket_name', $revenue->ticket_name) }}" placeholder="e.g., Gold Pass, Platinum VIP">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">Ticket Price (£) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">£</span>
                                        <input type="number" step="0.01" name="ticket_price" id="ticket_price" class="form-control" value="{{ old('ticket_price', $revenue->ticket_price) }}" required min="0">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">Tickets Available <span class="text-danger">*</span></label>
                                    <input type="number" name="tickets_available" id="tickets_available" class="form-control" value="{{ old('tickets_available', $revenue->tickets_available) }}" required min="0">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">
                                        Tickets Sold <span class="text-danger">*</span>
                                        <span class="badge bg-success ms-1">Editable</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="number" name="tickets_sold" id="tickets_sold" class="form-control" value="{{ old('tickets_sold', $revenue->tickets_sold) }}" required min="0">
                                        <button type="button" class="btn btn-outline-success" onclick="addTickets()" title="Add more tickets sold">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                    <small class="text-muted">Current: {{ $revenue->tickets_sold }}/{{ $revenue->tickets_available }}</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Add Tickets -->
                    <div class="card border-0 shadow-sm mb-4 bg-success-subtle">
                        <div class="card-header bg-transparent border-0 py-3">
                            <h6 class="mb-0 text-success"><i class="fas fa-plus-circle me-2"></i>Quick Add Tickets Sold</h6>
                        </div>
                        <div class="card-body pt-0">
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="button" class="btn btn-outline-success btn-sm" onclick="incrementTickets(1)">+1</button>
                                <button type="button" class="btn btn-outline-success btn-sm" onclick="incrementTickets(5)">+5</button>
                                <button type="button" class="btn btn-outline-success btn-sm" onclick="incrementTickets(10)">+10</button>
                                <button type="button" class="btn btn-outline-success btn-sm" onclick="incrementTickets(25)">+25</button>
                                <button type="button" class="btn btn-outline-success btn-sm" onclick="incrementTickets(50)">+50</button>
                                <button type="button" class="btn btn-outline-success btn-sm" onclick="incrementTickets(100)">+100</button>
                                <div class="input-group input-group-sm" style="width: 150px;">
                                    <input type="number" id="custom_add" class="form-control" placeholder="Custom" min="1">
                                    <button type="button" class="btn btn-success" onclick="addCustomTickets()">Add</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Deductions -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-0 py-3">
                            <h6 class="mb-0"><i class="fas fa-minus-circle me-2 text-danger"></i>Deductions</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label small">Platform Fees (£)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">£</span>
                                        <input type="number" step="0.01" name="platform_fees" id="platform_fees" class="form-control" value="{{ old('platform_fees', $revenue->platform_fees) }}" min="0">
                                    </div>
                                    <small class="text-muted">TicketKart commission</small>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">Gateway Fees (£)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">£</span>
                                        <input type="number" step="0.01" name="payment_gateway_fees" id="gateway_fees" class="form-control" value="{{ old('payment_gateway_fees', $revenue->payment_gateway_fees) }}" min="0">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">VAT/Taxes (£)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">£</span>
                                        <input type="number" step="0.01" name="taxes" id="taxes" class="form-control" value="{{ old('taxes', $revenue->taxes) }}" min="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Refunds -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-0 py-3">
                            <h6 class="mb-0"><i class="fas fa-undo me-2 text-warning"></i>Refunds</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small">Tickets Refunded</label>
                                    <input type="number" name="tickets_refunded" id="tickets_refunded" class="form-control" value="{{ old('tickets_refunded', $revenue->tickets_refunded) }}" min="0">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small">Refund Amount (£)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">£</span>
                                        <input type="number" step="0.01" name="refund_amount" id="refund_amount" class="form-control" value="{{ old('refund_amount', $revenue->refund_amount) }}" min="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label small">Notes</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Additional notes...">{{ old('notes', $revenue->notes) }}</textarea>
                    </div>
                </div>

                <!-- Summary Sidebar -->
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                        <div class="card-header bg-success text-white py-3">
                            <h6 class="mb-0"><i class="fas fa-calculator me-1"></i> Revenue Summary</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td class="small">Gross Revenue:</td>
                                    <td class="text-end" id="gross_display">£0</td>
                                </tr>
                                <tr class="text-danger">
                                    <td class="small">- Platform Fees:</td>
                                    <td class="text-end" id="platform_display">£0</td>
                                </tr>
                                <tr class="text-danger">
                                    <td class="small">- Gateway Fees:</td>
                                    <td class="text-end" id="gateway_display">£0</td>
                                </tr>
                                <tr class="text-danger">
                                    <td class="small">- VAT/Taxes:</td>
                                    <td class="text-end" id="taxes_display">£0</td>
                                </tr>
                                <tr class="border-top">
                                    <td class="small"><strong>Net Revenue:</strong></td>
                                    <td class="text-end text-success" id="net_display"><strong>£0</strong></td>
                                </tr>
                                <tr class="text-warning">
                                    <td class="small">- Refunds:</td>
                                    <td class="text-end" id="refund_display">£0</td>
                                </tr>
                                <tr class="table-success">
                                    <td class="small"><strong>Final Revenue:</strong></td>
                                    <td class="text-end" id="final_display"><strong>£0</strong></td>
                                </tr>
                            </table>
                            <hr>
                            <div class="small text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Sell Rate: <span id="sell_rate">0%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-danger"><i class="fas fa-save me-1"></i> Update Revenue Entry</button>
                <a href="{{ route('pnl.revenues.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection

@section('customjs')
    <script>
        $(document).ready(function() {
            function formatCurrency(value) {
                return '£' + parseFloat(value || 0).toLocaleString('en-GB', {minimumFractionDigits: 0, maximumFractionDigits: 0});
            }

            function updateSummary() {
                const price = parseFloat($('#ticket_price').val()) || 0;
                const sold = parseInt($('#tickets_sold').val()) || 0;
                const available = parseInt($('#tickets_available').val()) || 0;
                const platformFees = parseFloat($('#platform_fees').val()) || 0;
                const gatewayFees = parseFloat($('#gateway_fees').val()) || 0;
                const taxes = parseFloat($('#taxes').val()) || 0;
                const refundAmount = parseFloat($('#refund_amount').val()) || 0;

                const gross = price * sold;
                const net = gross - platformFees - gatewayFees - taxes;
                const final = net - refundAmount;
                const sellRate = available > 0 ? (sold / available * 100).toFixed(1) : 0;

                $('#gross_display').text(formatCurrency(gross));
                $('#platform_display').text(formatCurrency(platformFees));
                $('#gateway_display').text(formatCurrency(gatewayFees));
                $('#taxes_display').text(formatCurrency(taxes));
                $('#net_display').html('<strong>' + formatCurrency(net) + '</strong>');
                $('#refund_display').text(formatCurrency(refundAmount));
                $('#final_display').html('<strong>' + formatCurrency(final) + '</strong>');
                $('#sell_rate').text(sellRate + '%');
            }

            $('#ticket_price, #tickets_sold, #tickets_available, #platform_fees, #gateway_fees, #taxes, #refund_amount').on('input', updateSummary);
            updateSummary();
        });

        function incrementTickets(amount) {
            const input = document.getElementById('tickets_sold');
            const available = parseInt(document.getElementById('tickets_available').value) || 0;
            const current = parseInt(input.value) || 0;
            const newValue = current + amount;
            
            if (newValue > available) {
                if (confirm('This exceeds available tickets (' + available + '). Continue anyway?')) {
                    input.value = newValue;
                }
            } else {
                input.value = newValue;
            }
            $(input).trigger('input');
        }

        function addTickets() {
            const amount = prompt('Enter number of tickets to add:', '1');
            if (amount && !isNaN(amount)) {
                incrementTickets(parseInt(amount));
            }
        }

        function addCustomTickets() {
            const customInput = document.getElementById('custom_add');
            const amount = parseInt(customInput.value) || 0;
            if (amount > 0) {
                incrementTickets(amount);
                customInput.value = '';
            }
        }
    </script>
@endsection
