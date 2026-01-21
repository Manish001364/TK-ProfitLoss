@extends('layouts.organiser_layout')

@section('content')
    <div class="container py-4" style="max-width: 900px;">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">Add Revenue Entry</h4>
                <p class="text-muted small mb-0">Record ticket sales revenue</p>
            </div>
            <a href="{{ route('pnl.revenues.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
        </div>

        <form action="{{ route('pnl.revenues.store') }}" method="POST">
            @csrf
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
                                            <option value="{{ $event->id }}" {{ old('event_id', $selectedEventId) == $event->id ? 'selected' : '' }}>
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
                                            <option value="{{ $key }}" {{ old('ticket_type') === $key ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small">Custom Ticket Name</label>
                                    <input type="text" name="ticket_name" class="form-control" value="{{ old('ticket_name') }}" placeholder="e.g., Gold Pass, Platinum VIP">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">Ticket Price (£) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">£</span>
                                        <input type="number" step="0.01" name="ticket_price" id="ticket_price" class="form-control" value="{{ old('ticket_price', 0) }}" required min="0">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">Tickets Available <span class="text-danger">*</span></label>
                                    <input type="number" name="tickets_available" id="tickets_available" class="form-control" value="{{ old('tickets_available', 0) }}" required min="0">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">Tickets Sold <span class="text-danger">*</span></label>
                                    <input type="number" name="tickets_sold" id="tickets_sold" class="form-control" value="{{ old('tickets_sold', 0) }}" required min="0">
                                    <small class="text-muted">You can update this later</small>
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
                                        <input type="number" step="0.01" name="platform_fees" id="platform_fees" class="form-control" value="{{ old('platform_fees', 0) }}" min="0">
                                    </div>
                                    <small class="text-muted">TicketKart commission</small>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">Gateway Fees (£)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">£</span>
                                        <input type="number" step="0.01" name="payment_gateway_fees" id="gateway_fees" class="form-control" value="{{ old('payment_gateway_fees', 0) }}" min="0">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">VAT/Taxes (£)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">£</span>
                                        <input type="number" step="0.01" name="taxes" id="taxes" class="form-control" value="{{ old('taxes', 0) }}" min="0">
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
                                    <input type="number" name="tickets_refunded" id="tickets_refunded" class="form-control" value="{{ old('tickets_refunded', 0) }}" min="0">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small">Refund Amount (£)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">£</span>
                                        <input type="number" step="0.01" name="refund_amount" id="refund_amount" class="form-control" value="{{ old('refund_amount', 0) }}" min="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label small">Notes</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Additional notes...">{{ old('notes') }}</textarea>
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
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-danger"><i class="fas fa-save me-1"></i> Create Revenue Entry</button>
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
