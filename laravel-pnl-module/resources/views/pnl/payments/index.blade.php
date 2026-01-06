@extends('adminlte::page')

@section('title', 'Payments')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-credit-card"></i> Payment Tracking</h1>
        <div>
            <a href="{{ route('pnl.payments.upcoming') }}" class="btn btn-info">
                <i class="fas fa-calendar"></i> Upcoming
            </a>
            <a href="{{ route('pnl.payments.overdue') }}" class="btn btn-danger">
                <i class="fas fa-exclamation-triangle"></i> Overdue
            </a>
        </div>
    </div>
@stop

@section('content')
    <!-- Filters -->
    <div class="card card-outline card-primary mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('pnl.payments.index') }}" class="row align-items-end">
                <div class="col-md-3">
                    <div class="form-group mb-0">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="">All Status</option>
                            @foreach($statuses as $key => $label)
                                <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group mb-0">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="overdue" name="overdue" value="1" {{ request('overdue') ? 'checked' : '' }}>
                            <label class="custom-control-label" for="overdue">Show Only Overdue</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filter</button>
                    <a href="{{ route('pnl.payments.index') }}" class="btn btn-secondary"><i class="fas fa-times"></i></a>
                </div>
            </form>
        </div>
    </div>

    <!-- Payments Table -->
    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>Event / Expense</th>
                        <th>Vendor</th>
                        <th class="text-right">Amount</th>
                        <th>Status</th>
                        <th>Scheduled</th>
                        <th>Paid Date</th>
                        <th width="180">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr class="{{ $payment->is_overdue ? 'table-danger' : '' }}">
                            <td>
                                <strong><a href="{{ route('pnl.events.show', $payment->expense->event) }}">{{ $payment->expense->event->name }}</a></strong>
                                <br>
                                <small><a href="{{ route('pnl.expenses.show', $payment->expense) }}">{{ $payment->expense->title }}</a></small>
                            </td>
                            <td>
                                @if($payment->vendor)
                                    <a href="{{ route('pnl.vendors.show', $payment->vendor) }}">{{ $payment->vendor->display_name }}</a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-right"><strong>₹{{ number_format($payment->amount, 0) }}</strong></td>
                            <td>
                                <span class="badge badge-{{ $payment->status_color }}">{{ ucfirst($payment->status) }}</span>
                                @if($payment->is_overdue)
                                    <span class="badge badge-danger">Overdue</span>
                                @endif
                            </td>
                            <td>
                                @if($payment->scheduled_date)
                                    {{ $payment->scheduled_date->format('d M Y') }}
                                    @if($payment->days_until_due !== null && $payment->status !== 'paid')
                                        <br>
                                        <small class="{{ $payment->days_until_due < 0 ? 'text-danger' : ($payment->days_until_due <= 7 ? 'text-warning' : 'text-muted') }}">
                                            {{ $payment->days_until_due < 0 ? abs($payment->days_until_due) . ' days overdue' : $payment->days_until_due . ' days left' }}
                                        </small>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $payment->actual_paid_date?->format('d M Y') ?? '-' }}</td>
                            <td>
                                <div class="btn-group">
                                    @if($payment->status !== 'paid')
                                        <form action="{{ route('pnl.payments.mark-paid', $payment) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" title="Mark as Paid">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        @if($payment->vendor && $payment->vendor->email)
                                            <form action="{{ route('pnl.payments.send-reminder', $payment) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-info" title="Send Reminder">
                                                    <i class="fas fa-envelope"></i>
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                    <a href="{{ route('pnl.payments.edit', $payment) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('pnl.payments.show', $payment) }}" class="btn btn-sm btn-outline-primary" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-credit-card fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">No payments found</h5>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($payments->hasPages())
            <div class="card-footer">{{ $payments->links() }}</div>
        @endif
    </div>
@stop
