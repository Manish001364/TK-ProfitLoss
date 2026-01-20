@extends('layouts.organiser_layout')

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="mb-4">
            <h1 class="h3 mb-0"><i class="fas fa-plus-circle"></i> Add Revenue Entry</h1>
        </div>

        <form action="{{ route('pnl.revenues.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">Revenue Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Event <span class="text-danger">*</span></label>
                                        <select name="event_id" class="form-control @error('event_id') is-invalid @enderror" required>
                                            <option value="">Select Event</option>
                                            @foreach($events as $event)
                                                <option value="{{ $event->id }}" {{ old('event_id', $selectedEventId) == $event->id ? 'selected' : '' }}>
                                                    {{ $event->name }} ({{ $event->event_date->format('d M Y') }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('event_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Ticket Type <span class="text-danger">*</span></label>
                                        <select name="ticket_type" class="form-control @error('ticket_type') is-invalid @enderror" required>
                                            @foreach($ticketTypes as $key => $label)
                                                <option value="{{ $key }}" {{ old('ticket_type') === $key ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('ticket_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label>Custom Ticket Name</label>
                                <input type="text" name="ticket_name" class="form-control @error('ticket_name') is-invalid @enderror" 
                                       value="{{ old('ticket_name') }}" placeholder="e.g., Gold Pass, Platinum VIP">
                                @error('ticket_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label>Ticket Price (₹) <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" name="ticket_price" id="ticket_price" 
                                               class="form-control @error('ticket_price') is-invalid @enderror" 
                                               value="{{ old('ticket_price', 0) }}" required min="0">
                                        @error('ticket_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label>Tickets Available <span class="text-danger">*</span></label>
                                        <input type="number" name="tickets_available" id="tickets_available" 
                                               class="form-control @error('tickets_available') is-invalid @enderror" 
                                               value="{{ old('tickets_available', 0) }}" required min="0">
                                        @error('tickets_available')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label>Tickets Sold <span class="text-danger">*</span></label>
                                        <input type="number" name="tickets_sold" id="tickets_sold" 
                                               class="form-control @error('tickets_sold') is-invalid @enderror" 
                                               value="{{ old('tickets_sold', 0) }}" required min="0">
                                        @error('tickets_sold')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <h5>Deductions</h5>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label>Platform Fees (₹)</label>
                                        <input type="number" step="0.01" name="platform_fees" id="platform_fees" 
                                               class="form-control @error('platform_fees') is-invalid @enderror" 
                                               value="{{ old('platform_fees', 0) }}" min="0">
                                        @error('platform_fees')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        <small class="text-muted">TicketKart commission</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label>Payment Gateway Fees (₹)</label>
                                        <input type="number" step="0.01" name="payment_gateway_fees" id="gateway_fees" 
                                               class="form-control @error('payment_gateway_fees') is-invalid @enderror" 
                                               value="{{ old('payment_gateway_fees', 0) }}" min="0">
                                        @error('payment_gateway_fees')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label>Taxes (₹)</label>
                                        <input type="number" step="0.01" name="taxes" id="taxes" 
                                               class="form-control @error('taxes') is-invalid @enderror" 
                                               value="{{ old('taxes', 0) }}" min="0">
                                        @error('taxes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <h5>Refunds</h5>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Tickets Refunded</label>
                                        <input type="number" name="tickets_refunded" id="tickets_refunded" 
                                               class="form-control @error('tickets_refunded') is-invalid @enderror" 
                                               value="{{ old('tickets_refunded', 0) }}" min="0">
                                        @error('tickets_refunded')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Refund Amount (₹)</label>
                                        <input type="number" step="0.01" name="refund_amount" id="refund_amount" 
                                               class="form-control @error('refund_amount') is-invalid @enderror" 
                                               value="{{ old('refund_amount', 0) }}" min="0">
                                        @error('refund_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label>Notes</label>
                                <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="2">{{ old('notes') }}</textarea>
                                @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-header bg-success text-white">
                            <h5 class="card-title mb-0"><i class="fas fa-calculator"></i> Revenue Summary</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm mb-0">
                                <tr>
                                    <td>Gross Revenue:</td>
                                    <td class="text-end" id="gross_display">₹0</td>
                                </tr>
                                <tr class="text-danger">
                                    <td>- Platform Fees:</td>
                                    <td class="text-end" id="platform_display">₹0</td>
                                </tr>
                                <tr class="text-danger">
                                    <td>- Gateway Fees:</td>
                                    <td class="text-end" id="gateway_display">₹0</td>
                                </tr>
                                <tr class="text-danger">
                                    <td>- Taxes:</td>
                                    <td class="text-end" id="taxes_display">₹0</td>
                                </tr>
                                <tr>
                                    <td><strong>Net Revenue:</strong></td>
                                    <td class="text-end text-success" id="net_display"><strong>₹0</strong></td>
                                </tr>
                                <tr class="text-warning">
                                    <td>- Refunds:</td>
                                    <td class="text-end" id="refund_display">₹0</td>
                                </tr>
                                <tr class="table-success">
                                    <td><strong>Final Revenue:</strong></td>
                                    <td class="text-end" id="final_display"><strong>₹0</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i> Create Revenue Entry
                    </button>
                    <a href="{{ route('pnl.revenues.index') }}" class="btn btn-secondary btn-lg">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('customjs')
    <script>
        $(document).ready(function() {
            function formatCurrency(value) {
                return '₹' + parseFloat(value || 0).toLocaleString('en-IN', {minimumFractionDigits: 0, maximumFractionDigits: 0});
            }

            function updateSummary() {
                const price = parseFloat($('#ticket_price').val()) || 0;
                const sold = parseInt($('#tickets_sold').val()) || 0;
                const platformFees = parseFloat($('#platform_fees').val()) || 0;
                const gatewayFees = parseFloat($('#gateway_fees').val()) || 0;
                const taxes = parseFloat($('#taxes').val()) || 0;
                const refundAmount = parseFloat($('#refund_amount').val()) || 0;

                const gross = price * sold;
                const net = gross - platformFees - gatewayFees - taxes;
                const final = net - refundAmount;

                $('#gross_display').text(formatCurrency(gross));
                $('#platform_display').text(formatCurrency(platformFees));
                $('#gateway_display').text(formatCurrency(gatewayFees));
                $('#taxes_display').text(formatCurrency(taxes));
                $('#net_display').html('<strong>' + formatCurrency(net) + '</strong>');
                $('#refund_display').text(formatCurrency(refundAmount));
                $('#final_display').html('<strong>' + formatCurrency(final) + '</strong>');
            }

            $('#ticket_price, #tickets_sold, #platform_fees, #gateway_fees, #taxes, #refund_amount').on('input', updateSummary);
            updateSummary();
        });
    </script>
@endsection
