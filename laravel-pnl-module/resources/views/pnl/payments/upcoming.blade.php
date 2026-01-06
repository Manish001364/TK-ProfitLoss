@extends('adminlte::page')

@section('title', 'Upcoming Payments')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-calendar-alt"></i> Upcoming Payments</h1>
        <a href="{{ route('pnl.payments.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to All Payments
        </a>
    </div>
@stop

@section('content')
    <!-- Summary Cards -->
    <div class="row">
        <div class="col-md-4">
            <div class="info-box bg-danger">
                <span class="info-box-icon"><i class="fas fa-exclamation-triangle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Next 7 Days</span>
                    <span class="info-box-number">₹{{ number_format($summary['next_7_days'], 0) }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box bg-warning">
                <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Next 14 Days</span>
                    <span class="info-box-number">₹{{ number_format($summary['next_14_days'], 0) }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box bg-info">
                <span class="info-box-icon"><i class="fas fa-calendar"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Next 30 Days</span>
                    <span class="info-box-number">₹{{ number_format($summary['next_30_days'], 0) }}</span>
                </div>
            </div>
        </div>
    </div>

    @foreach(['next_7_days' => ['Next 7 Days', 'danger'], 'next_14_days' => ['8-14 Days', 'warning'], 'next_30_days' => ['15-30 Days', 'info']] as $key => $config)
        @if($payments->has($key) && $payments->get($key)->count() > 0)
            <div class="card card-outline card-{{ $config[1] }}">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-calendar"></i> {{ $config[0] }}</h3>
                    <div class="card-tools">
                        <span class="badge badge-{{ $config[1] }}">₹{{ number_format($payments->get($key)->sum('amount'), 0) }}</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Event / Expense</th>
                                <th>Vendor</th>
                                <th class="text-right">Amount</th>
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
                                    <td class="text-right"><strong>₹{{ number_format($payment->amount, 0) }}</strong></td>
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
@stop
