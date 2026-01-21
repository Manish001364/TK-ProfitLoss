@extends('layouts.organiser_layout')

@section('content')
    <div class="container py-4" style="max-width: 1000px;">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">Revenue Details</h4>
                <p class="text-muted small mb-0">{{ $revenue->event->name ?? 'N/A' }} - {{ $revenue->display_name }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('pnl.revenues.edit', $revenue) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
                <a href="{{ route('pnl.revenues.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
            </div>
        </div>

        <div class="row g-3">
            <!-- Summary Cards -->
            <div class="col-md-3">
                <div class="card border-0 shadow-sm bg-primary text-white">
                    <div class="card-body py-3">
                        <h5 class="mb-0">£{{ number_format($revenue->gross_revenue, 0) }}</h5>
                        <small>Gross Revenue</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm bg-success text-white">
                    <div class="card-body py-3">
                        <h5 class="mb-0">£{{ number_format($revenue->net_revenue_after_refunds, 0) }}</h5>
                        <small>Net Revenue</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm bg-info text-white">
                    <div class="card-body py-3">
                        <h5 class="mb-0">{{ $revenue->tickets_sold }}/{{ $revenue->tickets_available }}</h5>
                        <small>Tickets Sold</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm bg-secondary text-white">
                    <div class="card-body py-3">
                        <h5 class="mb-0">{{ number_format($revenue->sell_through_rate, 1) }}%</h5>
                        <small>Sell Rate</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Details -->
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0"><i class="fas fa-ticket-alt me-2 text-danger"></i>Ticket Details</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="text-muted">Event:</td>
                                <td><strong>{{ $revenue->event->name ?? 'N/A' }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Ticket Type:</td>
                                <td><span class="badge bg-info">{{ ucfirst($revenue->ticket_type) }}</span></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Ticket Name:</td>
                                <td>{{ $revenue->ticket_name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Price per Ticket:</td>
                                <td>£{{ number_format($revenue->ticket_price, 2) }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="text-muted">Tickets Available:</td>
                                <td>{{ $revenue->tickets_available }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tickets Sold:</td>
                                <td class="text-success fw-bold">{{ $revenue->tickets_sold }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tickets Refunded:</td>
                                <td class="text-danger">{{ $revenue->tickets_refunded }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Remaining:</td>
                                <td>{{ $revenue->tickets_available - $revenue->tickets_sold }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Breakdown -->
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0"><i class="fas fa-calculator me-2 text-success"></i>Financial Breakdown</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <td>Gross Revenue ({{ $revenue->tickets_sold }} × £{{ number_format($revenue->ticket_price, 2) }})</td>
                        <td class="text-end fw-bold">£{{ number_format($revenue->gross_revenue, 2) }}</td>
                    </tr>
                    <tr class="text-danger">
                        <td>- Platform Fees</td>
                        <td class="text-end">£{{ number_format($revenue->platform_fees, 2) }}</td>
                    </tr>
                    <tr class="text-danger">
                        <td>- Payment Gateway Fees</td>
                        <td class="text-end">£{{ number_format($revenue->payment_gateway_fees, 2) }}</td>
                    </tr>
                    <tr class="text-danger">
                        <td>- VAT/Taxes</td>
                        <td class="text-end">£{{ number_format($revenue->taxes, 2) }}</td>
                    </tr>
                    <tr class="table-light">
                        <td><strong>Net Revenue</strong></td>
                        <td class="text-end fw-bold">£{{ number_format($revenue->net_revenue, 2) }}</td>
                    </tr>
                    <tr class="text-warning">
                        <td>- Refunds</td>
                        <td class="text-end">£{{ number_format($revenue->refund_amount, 2) }}</td>
                    </tr>
                    <tr class="table-success">
                        <td><strong>Final Revenue</strong></td>
                        <td class="text-end fw-bold text-success">£{{ number_format($revenue->net_revenue_after_refunds, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        @if($revenue->notes)
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Notes</h6>
            </div>
            <div class="card-body">
                {{ $revenue->notes }}
            </div>
        </div>
        @endif
    </div>
@endsection
