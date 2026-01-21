@extends('layouts.organiser_layout')

@section('content')
    <div class="container py-4" style="max-width: 1200px;">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">P&L Dashboard</h4>
            <div class="d-flex gap-2">
                <a href="{{ route('pnl.settings.index') }}" class="btn btn-outline-secondary btn-sm" title="Settings">
                    <i class="fas fa-cog"></i>
                </a>
                <a href="{{ route('pnl.events.create') }}" class="btn btn-danger btn-sm">
                    <i class="fas fa-plus"></i> New Event
                </a>
                <a href="{{ route('pnl.export.pnl-summary', request()->query()) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-download"></i> Export
                </a>
            </div>
        </div>

        <!-- Filters Card -->
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-body py-3">
                <form method="GET" action="{{ route('pnl.dashboard') }}" id="filterForm">
                    <div class="row align-items-end g-3">
                        <div class="col-md-4">
                            <label class="form-label small text-muted mb-1">Event</label>
                            <select name="event_id" class="form-select form-select-sm" id="event-filter" onchange="document.getElementById('filterForm').submit()">
                                <option value="">All Events</option>
                                @foreach($events as $event)
                                    <option value="{{ $event->id }}" {{ $eventId == $event->id ? 'selected' : '' }}>
                                        {{ $event->name }} ({{ $event->event_date->format('d M Y') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-muted mb-1">Date Range</label>
                            <input type="text" name="date_range" id="dateRangePicker" class="form-control form-control-sm" 
                                   placeholder="Select date range" value="{{ $dateFrom && $dateTo ? $dateFrom . ' - ' . $dateTo : '' }}">
                            <input type="hidden" name="date_from" id="date_from" value="{{ $dateFrom }}">
                            <input type="hidden" name="date_to" id="date_to" value="{{ $dateTo }}">
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('pnl.dashboard') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-times"></i> Clear Filters
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row g-3 mb-4">
            <div class="col-lg-3 col-6">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #28a745 !important;">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted small mb-1">Net Revenue</p>
                                <h4 class="mb-0 text-success">£{{ number_format($totalRevenue, 0) }}</h4>
                            </div>
                            <div class="bg-success bg-opacity-10 rounded p-2">
                                <i class="fas fa-pound-sign text-success"></i>
                            </div>
                        </div>
                        <a href="{{ route('pnl.revenues.index') }}" class="small text-muted">View Details →</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #dc3545 !important;">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted small mb-1">Total Expenses</p>
                                <h4 class="mb-0 text-danger">£{{ number_format($totalExpenses, 0) }}</h4>
                            </div>
                            <div class="bg-danger bg-opacity-10 rounded p-2">
                                <i class="fas fa-receipt text-danger"></i>
                            </div>
                        </div>
                        <a href="{{ route('pnl.expenses.index') }}" class="small text-muted">View Details →</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid {{ $profitStatus === 'profit' ? '#28a745' : ($profitStatus === 'loss' ? '#ffc107' : '#6c757d') }} !important;">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted small mb-1">Net {{ ucfirst($profitStatus) }}</p>
                                <h4 class="mb-0 {{ $profitStatus === 'profit' ? 'text-success' : ($profitStatus === 'loss' ? 'text-warning' : 'text-secondary') }}">
                                    {{ $netProfit >= 0 ? '' : '-' }}£{{ number_format(abs($netProfit), 0) }}
                                </h4>
                            </div>
                            <div class="bg-{{ $profitStatus === 'profit' ? 'success' : ($profitStatus === 'loss' ? 'warning' : 'secondary') }} bg-opacity-10 rounded p-2">
                                <i class="fas fa-{{ $profitStatus === 'profit' ? 'arrow-up' : ($profitStatus === 'loss' ? 'arrow-down' : 'equals') }} text-{{ $profitStatus === 'profit' ? 'success' : ($profitStatus === 'loss' ? 'warning' : 'secondary') }}"></i>
                            </div>
                        </div>
                        <span class="small text-muted">{{ $profitStatus === 'profit' ? 'Profitable!' : ($profitStatus === 'loss' ? 'Needs Attention' : 'Break Even') }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #17a2b8 !important;">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted small mb-1">Tickets Sold</p>
                                <h4 class="mb-0 text-info">{{ number_format($totalTicketsSold) }}</h4>
                            </div>
                            <div class="bg-info bg-opacity-10 rounded p-2">
                                <i class="fas fa-ticket-alt text-info"></i>
                            </div>
                        </div>
                        <a href="{{ route('pnl.revenues.index') }}" class="small text-muted">View Sales →</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <!-- Revenue vs Expenses Chart -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 py-3">
                        <h6 class="mb-0">Revenue vs Expenses Trend</h6>
                    </div>
                    <div class="card-body pt-0">
                        <canvas id="trendChart" height="220"></canvas>
                    </div>
                </div>
            </div>

            <!-- Expense Breakdown by Category - Smaller -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 py-3">
                        <h6 class="mb-0">Expense by Category</h6>
                    </div>
                    <div class="card-body pt-0">
                        <div style="max-width: 180px; margin: 0 auto;">
                            <canvas id="expenseChart" height="180"></canvas>
                        </div>
                        <div class="mt-3">
                            @foreach($expenseByCategory as $cat)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="small">
                                        <span class="d-inline-block rounded-circle me-1" style="width: 10px; height: 10px; background-color: {{ $cat->color }};"></span>
                                        {{ $cat->name }}
                                    </span>
                                    <span class="small fw-bold">£{{ number_format($cat->total, 0) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Expense by Vendor Type -->
        <div class="row g-3 mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h6 class="mb-0"><i class="fas fa-chart-pie me-2 text-primary"></i>Expense by Vendor Type</h6>
                        <small class="text-muted">Where is your money going? Artist, DJ, Venue, Equipment, etc.</small>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-5">
                                <canvas id="vendorTypeChart" height="200"></canvas>
                            </div>
                            <div class="col-md-7">
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Vendor Type</th>
                                                <th class="text-center">Expenses</th>
                                                <th class="text-end">Amount</th>
                                                <th class="text-end">% of Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $totalVendorExpense = $expenseByVendorType->sum('total'); @endphp
                                            @forelse($expenseByVendorType as $vt)
                                                <tr>
                                                    <td>
                                                        <span class="d-inline-block rounded-circle me-2" style="width: 12px; height: 12px; background-color: {{ $vt->color }};"></span>
                                                        <strong>{{ $vt->label }}</strong>
                                                    </td>
                                                    <td class="text-center">{{ $vt->count }}</td>
                                                    <td class="text-end fw-bold">£{{ number_format($vt->total, 0) }}</td>
                                                    <td class="text-end text-muted">
                                                        {{ $totalVendorExpense > 0 ? number_format(($vt->total / $totalVendorExpense) * 100, 1) : 0 }}%
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted py-3">No vendor expenses yet</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                        @if($totalVendorExpense > 0)
                                        <tfoot class="table-light">
                                            <tr>
                                                <td><strong>Total</strong></td>
                                                <td class="text-center"><strong>{{ $expenseByVendorType->sum('count') }}</strong></td>
                                                <td class="text-end"><strong>£{{ number_format($totalVendorExpense, 0) }}</strong></td>
                                                <td class="text-end"><strong>100%</strong></td>
                                            </tr>
                                        </tfoot>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <!-- Payment Summary -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 py-3">
                        <h6 class="mb-0">Payment Status</h6>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <span class="small"><i class="fas fa-check-circle text-success me-2"></i>Paid</span>
                            <span class="badge bg-success-subtle text-success">£{{ number_format($paymentSummary['paid'], 0) }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <span class="small"><i class="fas fa-clock text-warning me-2"></i>Scheduled</span>
                            <span class="badge bg-warning-subtle text-warning">£{{ number_format($paymentSummary['scheduled'], 0) }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-2">
                            <span class="small"><i class="fas fa-hourglass-half text-info me-2"></i>Pending</span>
                            <span class="badge bg-info-subtle text-info">£{{ number_format($paymentSummary['pending'], 0) }}</span>
                        </div>
                        <a href="{{ route('pnl.payments.index') }}" class="btn btn-sm btn-outline-secondary w-100 mt-3">
                            View All Payments
                        </a>
                    </div>
                </div>
            </div>

            <!-- Overdue Payments -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-warning-subtle border-0 py-3">
                        <h6 class="mb-0 text-warning"><i class="fas fa-exclamation-triangle me-1"></i> Overdue Payments</h6>
                    </div>
                    <div class="card-body pt-0" style="max-height: 200px; overflow-y: auto;">
                        @forelse($overduePayments as $payment)
                            <div class="py-2 border-bottom">
                                <div class="d-flex justify-content-between">
                                    <strong class="small">{{ $payment->vendor?->display_name ?? 'Unknown Vendor' }}</strong>
                                    <span class="badge bg-danger">£{{ number_format($payment->amount, 0) }}</span>
                                </div>
                                <small class="text-muted">{{ $payment->expense->title ?? 'N/A' }}</small>
                                <br><small class="text-danger">Due: {{ $payment->scheduled_date ? $payment->scheduled_date->format('d M Y') : 'Not set' }}</small>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                <p class="small text-muted mb-0">No overdue payments!</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Upcoming Payments -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 py-3">
                        <h6 class="mb-0">Upcoming Payments (30 days)</h6>
                    </div>
                    <div class="card-body pt-0" style="max-height: 200px; overflow-y: auto;">
                        @forelse($upcomingPayments as $payment)
                            <div class="py-2 border-bottom">
                                <div class="d-flex justify-content-between">
                                    <strong class="small">{{ $payment->vendor?->display_name ?? 'Unknown Vendor' }}</strong>
                                    <span class="badge bg-info">£{{ number_format($payment->amount, 0) }}</span>
                                </div>
                                <small class="text-muted">{{ $payment->expense->title ?? 'Expense' }}</small>
                                <br>
                                <small>Due: {{ $payment->scheduled_date ? $payment->scheduled_date->format('d M Y') : 'Not set' }}
                                    @if($payment->days_until_due !== null)
                                        <span class="badge {{ $payment->days_until_due <= 7 ? 'bg-warning' : 'bg-secondary' }}">
                                            {{ $payment->days_until_due }} days
                                        </span>
                                    @endif
                                </small>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <i class="fas fa-calendar-check fa-2x text-muted mb-2"></i>
                                <p class="small text-muted mb-0">No upcoming payments in next 30 days</p>
                            </div>
                        @endforelse
                    </div>
                    <div class="card-footer bg-white border-0 pt-0">
                        <a href="{{ route('pnl.payments.upcoming') }}" class="btn btn-sm btn-outline-info w-100">
                            View All Upcoming
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Events -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Recent Events Performance</h6>
                <a href="{{ route('pnl.events.index') }}" class="btn btn-sm btn-outline-secondary">View All Events</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">Event</th>
                                <th class="border-0">Date</th>
                                <th class="border-0">Status</th>
                                <th class="border-0 text-end">Revenue</th>
                                <th class="border-0 text-end">Expenses</th>
                                <th class="border-0 text-end">Profit/Loss</th>
                                <th class="border-0"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentEvents as $event)
                                <tr>
                                    <td class="border-0"><strong>{{ $event['name'] }}</strong></td>
                                    <td class="border-0">{{ $event['date']->format('d M Y') }}</td>
                                    <td class="border-0">
                                        <span class="badge bg-{{ $event['status'] === 'completed' ? 'success' : ($event['status'] === 'active' ? 'primary' : 'secondary') }}-subtle text-{{ $event['status'] === 'completed' ? 'success' : ($event['status'] === 'active' ? 'primary' : 'secondary') }}">
                                            {{ ucfirst($event['status']) }}
                                        </span>
                                    </td>
                                    <td class="border-0 text-end text-success">£{{ number_format($event['revenue'], 0) }}</td>
                                    <td class="border-0 text-end text-danger">£{{ number_format($event['expenses'], 0) }}</td>
                                    <td class="border-0 text-end">
                                        <span class="{{ $event['profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ $event['profit'] >= 0 ? '' : '-' }}£{{ number_format(abs($event['profit']), 0) }}
                                        </span>
                                        <i class="fas fa-{{ $event['profit_status'] === 'profit' ? 'arrow-up text-success' : ($event['profit_status'] === 'loss' ? 'arrow-down text-danger' : 'equals text-secondary') }} ms-1"></i>
                                    </td>
                                    <td class="border-0">
                                        <a href="{{ route('pnl.events.show', $event['id']) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 border-0">
                                        <i class="fas fa-calendar-plus fa-3x text-muted mb-3"></i>
                                        <p class="text-muted mb-2">No events yet</p>
                                        <a href="{{ route('pnl.events.create') }}" class="btn btn-danger btn-sm">Create your first event</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Vendor Summary -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-users me-2"></i>Vendors & Artists</h6>
                <div class="d-flex gap-2">
                    <div class="input-group input-group-sm" style="width: 200px;">
                        <input type="text" class="form-control" id="vendorSearch" placeholder="Search vendors...">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                    <a href="{{ route('pnl.vendors.create') }}" class="btn btn-sm btn-success"><i class="fas fa-plus"></i> Add</a>
                    <a href="{{ route('pnl.vendors.index') }}" class="btn btn-sm btn-outline-secondary">View All</a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="vendorTable">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">Vendor/Artist</th>
                                <th class="border-0">Type</th>
                                <th class="border-0">Email</th>
                                <th class="border-0 text-end">Total Paid</th>
                                <th class="border-0 text-end">Pending</th>
                                <th class="border-0 text-center">Payments</th>
                                <th class="border-0"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vendorSummary as $vendor)
                                <tr class="vendor-row">
                                    <td class="border-0">
                                        <strong>{{ $vendor['name'] }}</strong>
                                    </td>
                                    <td class="border-0">
                                        <span class="badge bg-info-subtle text-info">{{ ucfirst($vendor['type']) }}</span>
                                    </td>
                                    <td class="border-0 small text-muted">{{ $vendor['email'] ?? '-' }}</td>
                                    <td class="border-0 text-end text-success fw-bold">£{{ number_format($vendor['total_paid'], 0) }}</td>
                                    <td class="border-0 text-end text-warning">£{{ number_format($vendor['total_pending'], 0) }}</td>
                                    <td class="border-0 text-center">
                                        <span class="badge bg-secondary">{{ $vendor['payments_count'] }}</span>
                                    </td>
                                    <td class="border-0">
                                        <a href="{{ route('pnl.vendors.show', $vendor['id']) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 border-0">
                                        <i class="fas fa-user-plus fa-3x text-muted mb-3"></i>
                                        <p class="text-muted mb-2">No vendors yet</p>
                                        <a href="{{ route('pnl.vendors.create') }}" class="btn btn-success btn-sm">Add your first vendor</a>
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

@section('customjs')
    <!-- Date Range Picker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        $(document).ready(function() {
            // Date Range Picker
            $('#dateRangePicker').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear',
                    format: 'YYYY-MM-DD'
                },
                opens: 'left'
            });

            $('#dateRangePicker').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
                $('#date_from').val(picker.startDate.format('YYYY-MM-DD'));
                $('#date_to').val(picker.endDate.format('YYYY-MM-DD'));
                document.getElementById('filterForm').submit();
            });

            $('#dateRangePicker').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                $('#date_from').val('');
                $('#date_to').val('');
                document.getElementById('filterForm').submit();
            });

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
                            backgroundColor: 'rgba(40, 167, 69, 0.8)',
                            borderRadius: 4
                        },
                        {
                            label: 'Expenses',
                            data: {!! json_encode($trendData['expenses']) !!},
                            backgroundColor: 'rgba(220, 53, 69, 0.8)',
                            borderRadius: 4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: { usePointStyle: true, pointStyle: 'circle' }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': £' + context.parsed.y.toLocaleString();
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f0f0f0' },
                            ticks: {
                                callback: function(value) {
                                    return '£' + value.toLocaleString();
                                }
                            }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });

            // Expense Pie Chart - Smaller
            const expenseCtx = document.getElementById('expenseChart').getContext('2d');
            const expenseData = {!! json_encode($expenseByCategory) !!};
            new Chart(expenseCtx, {
                type: 'doughnut',
                data: {
                    labels: expenseData.map(item => item.name),
                    datasets: [{
                        data: expenseData.map(item => item.total),
                        backgroundColor: expenseData.map(item => item.color),
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    cutout: '60%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': £' + context.parsed.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });

            // Vendor Search
            $('#vendorSearch').on('keyup', function() {
                const searchText = $(this).val().toLowerCase();
                $('#vendorTable tbody tr.vendor-row').each(function() {
                    const rowText = $(this).text().toLowerCase();
                    $(this).toggle(rowText.indexOf(searchText) > -1);
                });
            });
        });
    </script>
@endsection
