@extends('pnl.layouts.app')

@section('pnl_content')
    <div class="container-fluid" style="max-width: 1100px;">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1 text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Overdue Payments</h4>
                <p class="text-muted small mb-0">Payments past their scheduled date</p>
            </div>
            <a href="{{ route('pnl.payments.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left"></i> All Payments</a>
        </div>

        <!-- Summary -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-danger text-white">
                    <div class="card-body py-3">
                        <h4 class="mb-0">£{{ number_format($payments->sum('amount'), 0) }}</h4>
                        <small>Total Overdue</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-warning text-dark">
                    <div class="card-body py-3">
                        <h4 class="mb-0">{{ $payments->count() }}</h4>
                        <small>Overdue Payments</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-secondary text-white">
                    <div class="card-body py-3">
                        <h4 class="mb-0">{{ $payments->pluck('vendor_id')->unique()->count() }}</h4>
                        <small>Vendors Affected</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Overdue Payments Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-danger">
                            <tr>
                                <th class="border-0">Expense</th>
                                <th class="border-0">Vendor</th>
                                <th class="border-0">Event</th>
                                <th class="border-0 text-end">Amount</th>
                                <th class="border-0">Due Date</th>
                                <th class="border-0">Days Overdue</th>
                                <th class="border-0">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $payment)
                                <tr>
                                    <td class="border-0">
                                        <strong>{{ $payment->expense->title ?? 'N/A' }}</strong>
                                    </td>
                                    <td class="border-0">
                                        @if($payment->vendor)
                                            <a href="{{ route('pnl.vendors.show', $payment->vendor) }}">{{ $payment->vendor->display_name }}</a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="border-0">{{ $payment->expense->event->name ?? 'N/A' }}</td>
                                    <td class="border-0 text-end fw-bold text-danger">£{{ number_format($payment->amount, 0) }}</td>
                                    <td class="border-0">{{ $payment->scheduled_date?->format('d M Y') ?? '-' }}</td>
                                    <td class="border-0">
                                        <span class="badge bg-danger">
                                            {{ $payment->scheduled_date ? $payment->scheduled_date->diffInDays(now()) : 0 }} days
                                        </span>
                                    </td>
                                    <td class="border-0">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('pnl.payments.edit', $payment) }}" class="btn btn-outline-secondary"><i class="fas fa-edit"></i></a>
                                            <form action="{{ route('pnl.payments.markPaid', $payment) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-success" onclick="return confirm('Mark as paid?')">
                                                    <i class="fas fa-check"></i> Pay
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 border-0">
                                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                        <p class="text-muted mb-0">No overdue payments! 🎉</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
