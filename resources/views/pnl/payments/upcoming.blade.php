@extends('layouts.organiser_layout')

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0"><i class="fas fa-calendar-alt"></i> Upcoming Payments</h1>
            <a href="{{ route('pnl.payments.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to All Payments
            </a>
        </div>

        <!-- Summary Cards -->
        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="card bg-danger text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-0">₹{{ number_format($summary['next_7_days'], 0) }}</h4>
                                <small>Next 7 Days</small>
                            </div>
                            <i class="fas fa-exclamation-triangle fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card bg-warning text-dark h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-0">₹{{ number_format($summary['next_14_days'], 0) }}</h4>
                                <small>Next 14 Days</small>
                            </div>
                            <i class="fas fa-clock fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card bg-info text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-0">₹{{ number_format($summary['next_30_days'], 0) }}</h4>
                                <small>Next 30 Days</small>
                            </div>
                            <i class="fas fa-calendar fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @foreach(['next_7_days' => ['Next 7 Days', 'danger'], 'next_14_days' => ['8-14 Days', 'warning'], 'next_30_days' => ['15-30 Days', 'info']] as $key => $config)
            @if($payments->has($key) && $payments->get($key)->count() > 0)
                <div class="card mb-4">
                    <div class="card-header bg-{{ $config[1] }} {{ $config[1] === 'warning' ? 'text-dark' : 'text-white' }} d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><i class="fas fa-calendar"></i> {{ $config[0] }}</h5>
                        <span class="badge bg-light text-dark">₹{{ number_format($payments->get($key)->sum('amount'), 0) }}</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Event / Expense</th>
                                        <th>Vendor</th>
                                        <th class="text-end">Amount</th>
                                        <th>Due Date</th>
                                        <th width="150">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payments->get($key) as $payment)
                                        <tr>
                                            <td>
                                                <strong>{{ $payment->expense->event->name }}</strong><br>
                                                <small>{{ $payment->expense->title }}</small>
                                            </td>
                                            <td>{{ $payment->vendor?->display_name ?? '-' }}</td>
                                            <td class="text-end"><strong>₹{{ number_format($payment->amount, 0) }}</strong></td>
                                            <td>
                                                {{ $payment->scheduled_date->format('d M Y') }}
                                                <br><small class="text-{{ $payment->days_until_due <= 3 ? 'danger' : 'muted' }}">{{ $payment->days_until_due }} days</small>
                                            </td>
                                            <td>
                                                <form action="{{ route('pnl.payments.mark-paid', $payment) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success" title="Mark Paid">
                                                        <i class="fas fa-check"></i> Pay
                                                    </button>
                                                </form>
                                                <a href="{{ route('pnl.payments.edit', $payment) }}" class="btn btn-sm btn-outline-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach

        @if($payments->flatten()->count() === 0)
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-calendar-check fa-4x text-success mb-3"></i>
                    <h4 class="text-muted">No upcoming payments in the next 30 days!</h4>
                </div>
            </div>
        @endif
    </div>
@endsection
