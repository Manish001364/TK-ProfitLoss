@extends('pnl.layouts.app')

@section('pnl_content')
    <div class="container-fluid" style="max-width: 1000px;">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">Payment Details</h4>
                <p class="text-muted small mb-0">{{ $payment->expense->title ?? 'N/A' }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('pnl.payments.edit', $payment) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
                <a href="{{ route('pnl.payments.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
            </div>
        </div>

        <div class="row g-3">
            <!-- Status Card -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center py-4">
                        @if($payment->status === 'paid')
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h5 class="text-success">PAID</h5>
                        @elseif($payment->status === 'scheduled')
                            <i class="fas fa-clock fa-3x text-warning mb-3"></i>
                            <h5 class="text-warning">SCHEDULED</h5>
                        @else
                            <i class="fas fa-hourglass-half fa-3x text-secondary mb-3"></i>
                            <h5 class="text-secondary">PENDING</h5>
                        @endif
                        <h3 class="mb-0">£{{ number_format($payment->amount, 2) }}</h3>
                    </div>
                </div>
            </div>

            <!-- Payment Info -->
            <div class="col-md-8">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 py-3">
                        <h6 class="mb-0"><i class="fas fa-info-circle me-2 text-primary"></i>Payment Information</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td class="text-muted" width="150">Expense:</td>
                                <td><strong>{{ $payment->expense->title ?? 'N/A' }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Event:</td>
                                <td>{{ $payment->expense->event->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Vendor:</td>
                                <td>{{ $payment->vendor->display_name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Payment Method:</td>
                                <td>{{ $payment->payment_method ? ucfirst(str_replace('_', ' ', $payment->payment_method)) : '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Scheduled Date:</td>
                                <td>{{ $payment->scheduled_date?->format('d M Y') ?? '-' }}</td>
                            </tr>
                            @if($payment->actual_paid_date)
                            <tr>
                                <td class="text-muted">Paid Date:</td>
                                <td class="text-success">{{ $payment->actual_paid_date->format('d M Y') }}</td>
                            </tr>
                            @endif
                            @if($payment->transaction_reference)
                            <tr>
                                <td class="text-muted">Reference:</td>
                                <td><code>{{ $payment->transaction_reference }}</code></td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>

        @if($payment->internal_notes)
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Internal Notes</h6>
            </div>
            <div class="card-body">
                {{ $payment->internal_notes }}
            </div>
        </div>
        @endif

        <!-- Quick Actions -->
        @if($payment->status !== 'paid')
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0"><i class="fas fa-bolt me-2 text-warning"></i>Quick Actions</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('pnl.payments.markPaid', $payment) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-success" onclick="return confirm('Mark this payment as paid?')">
                        <i class="fas fa-check"></i> Mark as Paid
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>
@endsection
