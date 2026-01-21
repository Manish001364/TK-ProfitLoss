@extends('layouts.organiser_layout')

@section('content')
    <div class="container py-4" style="max-width: 1200px;">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Payment Tracking</h4>
            <div class="d-flex gap-2">
                <a href="{{ route('pnl.payments.upcoming') }}" class="btn btn-info btn-sm">
                    <i class="fas fa-calendar"></i> Upcoming
                </a>
                <a href="{{ route('pnl.payments.overdue') }}" class="btn btn-danger btn-sm">
                    <i class="fas fa-exclamation-triangle"></i> Overdue
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
                <form method="GET" action="{{ route('pnl.payments.index') }}" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small text-muted mb-1">Status</label>
                        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">All Status</option>
                            @foreach($statuses as $key => $label)
                                <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="overdue" name="overdue" value="1" {{ request('overdue') ? 'checked' : '' }} onchange="this.form.submit()">
                            <label class="form-check-label small" for="overdue">Show Only Overdue</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('pnl.payments.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-times"></i> Clear</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Payments Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">Event / Expense</th>
                                <th class="border-0">Vendor</th>
                                <th class="border-0 text-end">Amount</th>
                                <th class="border-0">Status</th>
                                <th class="border-0">Scheduled</th>
                                <th class="border-0">Paid Date</th>
                                <th class="border-0" width="150">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $payment)
                                <tr class="{{ $payment->is_overdue ? 'table-danger' : '' }}">
                                    <td class="border-0">
                                        <strong><a href="{{ route('pnl.events.show', $payment->expense->event) }}">{{ $payment->expense->event->name }}</a></strong>
                                        <br><small class="text-muted"><a href="{{ route('pnl.expenses.show', $payment->expense) }}">{{ $payment->expense->title }}</a></small>
                                    </td>
                                    <td class="border-0 small">
                                        @if($payment->vendor)
                                            <a href="{{ route('pnl.vendors.show', $payment->vendor) }}">{{ $payment->vendor->display_name }}</a>
                                        @else
                                            <span class="text-muted">Not assigned</span>
                                        @endif
                                    </td>
                                    <td class="border-0 text-end"><strong>£{{ number_format($payment->amount, 0) }}</strong></td>
                                    <td class="border-0">
                                        <span class="badge bg-{{ $payment->status_color }}-subtle text-{{ $payment->status_color }}">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                        @if($payment->is_overdue)
                                            <span class="badge bg-danger">Overdue</span>
                                        @endif
                                    </td>
                                    <td class="border-0 small">
                                        @if($payment->scheduled_date)
                                            {{ $payment->scheduled_date->format('d M Y') }}
                                            @if($payment->days_until_due !== null && $payment->status !== 'paid')
                                                <br><small class="{{ $payment->days_until_due < 0 ? 'text-danger' : ($payment->days_until_due <= 7 ? 'text-warning' : 'text-muted') }}">
                                                    {{ $payment->days_until_due < 0 ? abs($payment->days_until_due) . ' days overdue' : $payment->days_until_due . ' days left' }}
                                                </small>
                                            @endif
                                        @else
                                            <span class="text-muted">Not set</span>
                                        @endif
                                    </td>
                                    <td class="border-0 small">{{ $payment->actual_paid_date?->format('d M Y') ?? '-' }}</td>
                                    <td class="border-0">
                                        <div class="btn-group btn-group-sm">
                                            @if($payment->status !== 'paid')
                                                <form action="{{ route('pnl.payments.mark-paid', $payment) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success" title="Mark Paid"><i class="fas fa-check"></i></button>
                                                </form>
                                            @endif
                                            <a href="{{ route('pnl.payments.edit', $payment) }}" class="btn btn-outline-secondary" title="Edit"><i class="fas fa-edit"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 border-0">
                                        <i class="fas fa-credit-card fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">No payments found</h5>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($payments->hasPages())
                <div class="card-footer bg-white border-0">{{ $payments->links() }}</div>
            @endif
        </div>
    </div>
@endsection
