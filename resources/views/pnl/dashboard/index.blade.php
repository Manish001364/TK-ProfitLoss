@extends('pnl.layouts.app')

@section('pnl_content')
    {{-- Include Walkthrough Modal for First-Time Users --}}
    @include('pnl.partials.walkthrough')

    <div class="container-fluid" style="max-width: 1200px;">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-0" style="color: #dc3545;">P&L Dashboard</h4>
                <small class="text-muted">Track your event profitability</small>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-info btn-sm" onclick="showWalkthroughAgain()" title="Show Guide">
                    <i class="fas fa-question-circle"></i>
                </button>
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

        {{-- Include Smart Tips & Insights --}}
        @include('pnl.partials.tips')

        <!-- Summary Cards - Collapsible Section -->
        <div class="collapsible-section mb-4" data-section="summary-cards">
            <div class="section-header d-flex justify-content-between align-items-center mb-3 cursor-pointer" onclick="toggleSection('summary-cards')">
                <h6 class="mb-0"><i class="fas fa-chart-bar me-2 text-danger"></i>Summary Overview</h6>
                <i class="fas fa-chevron-down section-toggle" id="toggle-summary-cards"></i>
            </div>
            <div class="section-content" id="content-summary-cards">
                <div class="row g-3">
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
            </div>
        </div>

        <!-- Charts Section - Collapsible -->
        <div class="collapsible-section mb-4" data-section="charts">
            <div class="section-header d-flex justify-content-between align-items-center mb-3 cursor-pointer" onclick="toggleSection('charts')">
                <h6 class="mb-0"><i class="fas fa-chart-line me-2 text-primary"></i>Revenue vs Expenses Trend</h6>
                <i class="fas fa-chevron-down section-toggle" id="toggle-charts"></i>
            </div>
            <div class="section-content" id="content-charts">
                <div class="row g-3">
                    <!-- Revenue vs Expenses Chart -->
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                                <span class="small text-muted">Monthly Trend</span>
                                <div class="d-flex gap-2 align-items-center">
                                    <span class="small text-muted">Period:</span>
                                    <select class="form-select form-select-sm" style="width: 130px;" id="chartPeriodSelect" onchange="updateChartPeriod()">
                                        <option value="3" {{ $chartPeriod == '3' ? 'selected' : '' }}>Last 3 months</option>
                                        <option value="6" {{ $chartPeriod == '6' ? 'selected' : '' }}>Last 6 months</option>
                                        <option value="12" {{ $chartPeriod == '12' ? 'selected' : '' }}>Last 12 months</option>
                                        <option value="ytd" {{ $chartPeriod == 'ytd' ? 'selected' : '' }}>Year to Date</option>
                                    </select>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <canvas id="trendChart" height="220"></canvas>
                            </div>
                        </div>
                    </div>
                    <!-- Expense Breakdown by Category -->
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-0 py-3">
                                <h6 class="mb-0" style="font-size: 0.9rem;">Expense by Category</h6>
                            </div>
                            <div class="card-body pt-0">
                                <div style="max-width: 160px; margin: 0 auto;">
                                    <canvas id="expenseChart" height="160"></canvas>
                                </div>
                                <div class="mt-2">
                                    @foreach($expenseByCategory->take(5) as $cat)
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="small" style="font-size: 0.75rem;">
                                                <span class="d-inline-block rounded-circle me-1" style="width: 8px; height: 8px; background-color: {{ $cat->color }};"></span>
                                                {{ Str::limit($cat->name, 15) }}
                                            </span>
                                            <span class="small fw-bold" style="font-size: 0.75rem;">£{{ number_format($cat->total, 0) }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Status Section - Collapsible -->
        <div class="collapsible-section mb-4" data-section="payments">
            <div class="section-header d-flex justify-content-between align-items-center mb-3 cursor-pointer" onclick="toggleSection('payments')">
                <h6 class="mb-0"><i class="fas fa-credit-card me-2 text-info"></i>Payment Overview</h6>
                <i class="fas fa-chevron-down section-toggle" id="toggle-payments"></i>
            </div>
            <div class="section-content" id="content-payments">
                <div class="row g-3">
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
                            <div class="card-header bg-danger text-white border-0 py-3">
                                <h6 class="mb-0"><i class="fas fa-exclamation-triangle me-1"></i> Overdue Payments</h6>
                            </div>
                            <div class="card-body pt-0" style="max-height: 200px; overflow-y: auto;">
                                @forelse($overduePayments->take(5) as $payment)
                                    <div class="py-2 border-bottom">
                                        <div class="d-flex justify-content-between">
                                            <strong class="small">{{ $payment->vendor?->display_name ?? 'Unknown Vendor' }}</strong>
                                            <span class="badge bg-danger">£{{ number_format($payment->amount, 0) }}</span>
                                        </div>
                                        <small class="text-muted">{{ $payment->expense?->title ?? 'No expense linked' }}</small>
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
                                @forelse($upcomingPayments->take(5) as $payment)
                                    <div class="py-2 border-bottom">
                                        <div class="d-flex justify-content-between">
                                            <strong class="small">{{ $payment->vendor?->display_name ?? 'Unknown Vendor' }}</strong>
                                            <span class="badge bg-info">£{{ number_format($payment->amount, 0) }}</span>
                                        </div>
                                        <small class="text-muted">{{ $payment->expense?->title ?? 'No expense linked' }}</small>
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
            </div>
        </div>

        <!-- Recent Events - Collapsible with Pagination -->
        <div class="collapsible-section mb-4" data-section="events">
            <div class="section-header d-flex justify-content-between align-items-center mb-3 cursor-pointer" onclick="toggleSection('events')">
                <h6 class="mb-0"><i class="fas fa-calendar-alt me-2 text-success"></i>Recent Events Performance</h6>
                <i class="fas fa-chevron-down section-toggle" id="toggle-events"></i>
            </div>
            <div class="section-content" id="content-events">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <span class="small text-muted">Show:</span>
                            <select class="form-select form-select-sm" style="width: 70px;" id="eventsPerPage" onchange="updateEventsPerPage()">
                                <option value="5" selected>5</option>
                                <option value="10">10</option>
                                <option value="25">25</option>
                            </select>
                        </div>
                        <a href="{{ route('pnl.events.index') }}" class="btn btn-sm btn-outline-secondary">View All Events</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="eventsTable">
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
                                        <tr class="event-row">
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
            </div>
        </div>

        <!-- Vendor Summary - Collapsible with Pagination -->
        <div class="collapsible-section mb-4" data-section="vendors">
            <div class="section-header d-flex justify-content-between align-items-center mb-3 cursor-pointer" onclick="toggleSection('vendors')">
                <h6 class="mb-0"><i class="fas fa-users me-2 text-primary"></i>Vendors & Artists</h6>
                <i class="fas fa-chevron-down section-toggle" id="toggle-vendors"></i>
            </div>
            <div class="section-content" id="content-vendors">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                        <div class="d-flex gap-2 align-items-center">
                            <div class="input-group input-group-sm" style="width: 200px;">
                                <input type="text" class="form-control" id="vendorSearch" placeholder="Search vendors...">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                            </div>
                            <span class="small text-muted">Show:</span>
                            <select class="form-select form-select-sm" style="width: 70px;" id="vendorsPerPage" onchange="updateVendorsPerPage()">
                                <option value="5" selected>5</option>
                                <option value="10">10</option>
                                <option value="25">25</option>
                            </select>
                        </div>
                        <div class="d-flex gap-2">
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
        </div>

        <!-- Expense by Vendor Type - Bottom Section - Collapsible -->
        <div class="collapsible-section mb-4" data-section="vendor-expense">
            <div class="section-header d-flex justify-content-between align-items-center mb-3 cursor-pointer" onclick="toggleSection('vendor-expense')">
                <h6 class="mb-0"><i class="fas fa-chart-pie me-2 text-warning"></i>Expense by Vendor Type</h6>
                <i class="fas fa-chevron-down section-toggle" id="toggle-vendor-expense"></i>
            </div>
            <div class="section-content" id="content-vendor-expense">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-4 col-lg-3">
                                <div style="max-width: 200px; margin: 0 auto;">
                                    <canvas id="vendorTypeChart" height="200"></canvas>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-9">
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
    </div>
@endsection

@section('customjs')
    <!-- Date Range Picker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        .cursor-pointer { cursor: pointer; }
        .section-header:hover { background: #f8f9fa; border-radius: 5px; padding: 8px; margin: -8px; }
        .section-toggle { transition: transform 0.3s ease; }
        .section-toggle.collapsed { transform: rotate(-90deg); }
        .section-content { transition: all 0.3s ease; }
        .section-content.collapsed { display: none; }
    </style>
    
    <script>
        // Collapsible sections with localStorage persistence
        function toggleSection(sectionId) {
            const content = document.getElementById('content-' + sectionId);
            const toggle = document.getElementById('toggle-' + sectionId);
            const isCollapsed = content.classList.contains('collapsed');
            
            if (isCollapsed) {
                content.classList.remove('collapsed');
                toggle.classList.remove('collapsed');
                localStorage.setItem('pnl_section_' + sectionId, 'open');
            } else {
                content.classList.add('collapsed');
                toggle.classList.add('collapsed');
                localStorage.setItem('pnl_section_' + sectionId, 'collapsed');
            }
        }

        // Restore section states from localStorage
        function restoreSectionStates() {
            const sections = ['summary-cards', 'charts', 'payments', 'events', 'vendors', 'vendor-expense'];
            sections.forEach(function(sectionId) {
                const state = localStorage.getItem('pnl_section_' + sectionId);
                if (state === 'collapsed') {
                    const content = document.getElementById('content-' + sectionId);
                    const toggle = document.getElementById('toggle-' + sectionId);
                    if (content && toggle) {
                        content.classList.add('collapsed');
                        toggle.classList.add('collapsed');
                    }
                }
            });
        }

        $(document).ready(function() {
            // Restore section states
            restoreSectionStates();

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

            // Vendor Type Expense Chart
            const vendorTypeCtx = document.getElementById('vendorTypeChart').getContext('2d');
            const vendorTypeData = {!! json_encode($expenseByVendorType) !!};
            new Chart(vendorTypeCtx, {
                type: 'doughnut',
                data: {
                    labels: vendorTypeData.map(item => item.label),
                    datasets: [{
                        data: vendorTypeData.map(item => item.total),
                        backgroundColor: vendorTypeData.map(item => item.color),
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    cutout: '50%',
                    plugins: {
                        legend: { 
                            display: true,
                            position: 'bottom',
                            labels: { usePointStyle: true, pointStyle: 'circle', font: { size: 10 } }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((context.parsed / total) * 100).toFixed(1);
                                    return context.label + ': £' + context.parsed.toLocaleString() + ' (' + percentage + '%)';
                                }
                            }
                        }
                    }
                }
            });

            // Live Search - Vendor Table
            $('#vendorSearch').on('keyup', function() {
                const searchText = $(this).val().toLowerCase();
                $('#vendorTable tbody tr.vendor-row').each(function() {
                    const rowText = $(this).text().toLowerCase();
                    $(this).toggle(rowText.indexOf(searchText) > -1);
                });
            });
        });

        // Pagination helpers
        function updateEventsPerPage() {
            const perPage = document.getElementById('eventsPerPage').value;
            localStorage.setItem('pnl_events_per_page', perPage);
            const rows = document.querySelectorAll('#eventsTable tbody tr.event-row');
            rows.forEach((row, index) => {
                row.style.display = index < perPage ? '' : 'none';
            });
        }

        function updateVendorsPerPage() {
            const perPage = document.getElementById('vendorsPerPage').value;
            localStorage.setItem('pnl_vendors_per_page', perPage);
            const rows = document.querySelectorAll('#vendorTable tbody tr.vendor-row');
            rows.forEach((row, index) => {
                row.style.display = index < perPage ? '' : 'none';
            });
        }

        // Restore pagination settings
        document.addEventListener('DOMContentLoaded', function() {
            const eventsPerPage = localStorage.getItem('pnl_events_per_page') || '5';
            const vendorsPerPage = localStorage.getItem('pnl_vendors_per_page') || '5';
            document.getElementById('eventsPerPage').value = eventsPerPage;
            document.getElementById('vendorsPerPage').value = vendorsPerPage;
            updateEventsPerPage();
            updateVendorsPerPage();
        });

        // Chart period filter
        function updateChartPeriod() {
            const period = document.getElementById('chartPeriodSelect').value;
            const url = new URL(window.location.href);
            url.searchParams.set('chart_period', period);
            window.location.href = url.toString();
        }

        // Show walkthrough again
        function showWalkthroughAgain() {
            // Remove dismissed flag from localStorage
            localStorage.removeItem('pnl_walkthrough_dismissed');
            // Reload page with walkthrough parameter
            const url = new URL(window.location.href);
            url.searchParams.set('show_walkthrough', '1');
            window.location.href = url.toString();
        }
    </script>
@endsection
