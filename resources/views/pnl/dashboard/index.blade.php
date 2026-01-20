@extends('layouts.organiser_layout')

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0"><i class="fas fa-chart-line"></i> P&L Dashboard</h1>
            <div>
                <a href="{{ route('pnl.events.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> New Event
                </a>
                <a href="{{ route('pnl.export.pnl-summary', request()->query()) }}" class="btn btn-success">
                    <i class="fas fa-download"></i> Export
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="fas fa-filter"></i> Filters</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('pnl.dashboard') }}" class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Event</label>
                            <select name="event_id" class="form-control" id="event-filter-dashboard">
                                <option value="">All Events</option>
                                @foreach($events as $event)
                                    <option value="{{ $event->id }}" {{ $eventId == $event->id ? 'selected' : '' }}>
                                        {{ $event->name }} ({{ $event->event_date->format('d M Y') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>From Date</label>
                            <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>To Date</label>
                            <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <a href="{{ route('pnl.dashboard') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row">
            <div class="col-lg-3 col-6 mb-3">
                <div class="card bg-info text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0">₹{{ number_format($totalRevenue, 0) }}</h3>
                                <p class="mb-0">Net Revenue</p>
                            </div>
                            <i class="fas fa-rupee-sign fa-2x opacity-50"></i>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <a href="{{ route('pnl.revenues.index') }}" class="text-white">
                            View Details <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6 mb-3">
                <div class="card bg-danger text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0">₹{{ number_format($totalExpenses, 0) }}</h3>
                                <p class="mb-0">Total Expenses</p>
                            </div>
                            <i class="fas fa-money-bill-wave fa-2x opacity-50"></i>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <a href="{{ route('pnl.expenses.index') }}" class="text-white">
                            View Details <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6 mb-3">
                <div class="card {{ $profitStatus === 'profit' ? 'bg-success' : ($profitStatus === 'loss' ? 'bg-warning' : 'bg-secondary') }} text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0">{{ $netProfit >= 0 ? '' : '-' }}₹{{ number_format(abs($netProfit), 0) }}</h3>
                                <p class="mb-0">Net {{ ucfirst($profitStatus) }}</p>
                            </div>
                            <i class="fas {{ $profitStatus === 'profit' ? 'fa-arrow-up' : ($profitStatus === 'loss' ? 'fa-arrow-down' : 'fa-equals') }} fa-2x opacity-50"></i>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <span class="text-white">
                            {{ $profitStatus === 'profit' ? 'Profitable!' : ($profitStatus === 'loss' ? 'Needs Attention' : 'Break Even') }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6 mb-3">
                <div class="card bg-primary text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0">{{ number_format($totalTicketsSold) }}</h3>
                                <p class="mb-0">Tickets Sold</p>
                            </div>
                            <i class="fas fa-ticket-alt fa-2x opacity-50"></i>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <a href="{{ route('pnl.revenues.index') }}" class="text-white">
                            View Sales <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Revenue vs Expenses Chart -->
            <div class="col-lg-8 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-chart-bar"></i> Revenue vs Expenses Trend</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="trendChart" height="300"></canvas>
                    </div>
                </div>
            </div>

            <!-- Expense Breakdown -->
            <div class="col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-chart-pie"></i> Expense Breakdown</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="expenseChart" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Payment Summary -->
            <div class="col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-credit-card"></i> Payment Status</h5>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-check-circle text-success"></i> Paid</span>
                                <span class="badge bg-success rounded-pill">₹{{ number_format($paymentSummary['paid'], 0) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-clock text-warning"></i> Scheduled</span>
                                <span class="badge bg-warning rounded-pill">₹{{ number_format($paymentSummary['scheduled'], 0) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-hourglass-half text-info"></i> Pending</span>
                                <span class="badge bg-info rounded-pill">₹{{ number_format($paymentSummary['pending'], 0) }}</span>
                            </li>
                        </ul>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('pnl.payments.index') }}" class="btn btn-sm btn-outline-primary">
                            View All Payments
                        </a>
                    </div>
                </div>
            </div>

            <!-- Overdue Payments Alert -->
            <div class="col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="card-title mb-0"><i class="fas fa-exclamation-triangle"></i> Overdue Payments</h5>
                    </div>
                    <div class="card-body p-0" style="max-height: 250px; overflow-y: auto;">
                        @forelse($overduePayments as $payment)
                            <div class="p-2 border-bottom">
                                <strong>{{ $payment->vendor?->display_name ?? 'N/A' }}</strong>
                                <br>
                                <small class="text-muted">{{ $payment->expense->title }}</small>
                                <span class="float-end badge bg-danger">₹{{ number_format($payment->amount, 0) }}</span>
                                <br>
                                <small class="text-danger">Due: {{ $payment->scheduled_date->format('d M Y') }}</small>
                            </div>
                        @empty
                            <div class="p-3 text-center text-muted">
                                <i class="fas fa-check-circle fa-2x mb-2"></i>
                                <p>No overdue payments!</p>
                            </div>
                        @endforelse
                    </div>
                    @if($overduePayments->count() > 0)
                        <div class="card-footer">
                            <a href="{{ route('pnl.payments.overdue') }}" class="btn btn-sm btn-warning">
                                View All Overdue
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Upcoming Payments -->
            <div class="col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-calendar-alt"></i> Upcoming Payments (30 days)</h5>
                    </div>
                    <div class="card-body p-0" style="max-height: 250px; overflow-y: auto;">
                        @forelse($upcomingPayments as $payment)
                            <div class="p-2 border-bottom">
                                <strong>{{ $payment->vendor?->display_name ?? 'N/A' }}</strong>
                                <br>
                                <small class="text-muted">{{ $payment->expense->title }}</small>
                                <span class="float-end badge bg-info">₹{{ number_format($payment->amount, 0) }}</span>
                                <br>
                                <small>Due: {{ $payment->scheduled_date->format('d M Y') }} 
                                    <span class="badge {{ $payment->days_until_due <= 7 ? 'bg-warning' : 'bg-secondary' }}">
                                        {{ $payment->days_until_due }} days
                                    </span>
                                </small>
                            </div>
                        @empty
                            <div class="p-3 text-center text-muted">
                                <i class="fas fa-calendar-check fa-2x mb-2"></i>
                                <p>No upcoming payments!</p>
                            </div>
                        @endforelse
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('pnl.payments.upcoming') }}" class="btn btn-sm btn-outline-info">
                            View All Upcoming
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Events -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="fas fa-calendar-alt"></i> Recent Events Performance</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Event</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th class="text-end">Revenue</th>
                                <th class="text-end">Expenses</th>
                                <th class="text-end">Profit/Loss</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentEvents as $event)
                                <tr>
                                    <td><strong>{{ $event['name'] }}</strong></td>
                                    <td>{{ $event['date']->format('d M Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $event['status'] === 'completed' ? 'success' : ($event['status'] === 'active' ? 'primary' : 'secondary') }}">
                                            {{ ucfirst($event['status']) }}
                                        </span>
                                    </td>
                                    <td class="text-end text-success">₹{{ number_format($event['revenue'], 0) }}</td>
                                    <td class="text-end text-danger">₹{{ number_format($event['expenses'], 0) }}</td>
                                    <td class="text-end">
                                        <span class="{{ $event['profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ $event['profit'] >= 0 ? '' : '-' }}₹{{ number_format(abs($event['profit']), 0) }}
                                        </span>
                                        <i class="fas fa-{{ $event['profit_status'] === 'profit' ? 'arrow-up text-success' : ($event['profit_status'] === 'loss' ? 'arrow-down text-danger' : 'equals text-secondary') }}"></i>
                                    </td>
                                    <td>
                                        <a href="{{ route('pnl.events.show', $event['id']) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="fas fa-calendar-plus fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No events yet. <a href="{{ route('pnl.events.create') }}">Create your first event!</a></p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('pnl.events.index') }}" class="btn btn-outline-primary">
                    View All Events
                </a>
            </div>
        </div>
    </div>
@endsection

@section('customjs')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2 if available
            if ($.fn.select2) {
                $('#event-filter-dashboard').select2({
                    placeholder: "Filter by Event"
                });
            }

            // Trend Chart
            const trendCtx = document.getElementById('trendChart').getContext('2d');
            new Chart(trendCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($trendData['labels']) !!},
                    datasets: [
                        {
                            label: 'Revenue',
                            data: {!! json_encode($trendData['revenues']) !!},
                            backgroundColor: 'rgba(40, 167, 69, 0.7)',
                            borderColor: 'rgba(40, 167, 69, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Expenses',
                            data: {!! json_encode($trendData['expenses']) !!},
                            backgroundColor: 'rgba(220, 53, 69, 0.7)',
                            borderColor: 'rgba(220, 53, 69, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '₹' + value.toLocaleString();
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ₹' + context.parsed.y.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });

            // Expense Pie Chart
            const expenseCtx = document.getElementById('expenseChart').getContext('2d');
            const expenseData = {!! json_encode($expenseByCategory) !!};
            new Chart(expenseCtx, {
                type: 'doughnut',
                data: {
                    labels: expenseData.map(item => item.name),
                    datasets: [{
                        data: expenseData.map(item => item.total),
                        backgroundColor: expenseData.map(item => item.color),
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': ₹' + context.parsed.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
