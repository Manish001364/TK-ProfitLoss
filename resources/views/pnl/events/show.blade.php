@extends('pnl.layouts.app')

@section('pnl_content')
    <div class="container-fluid" style="max-width: 1100px;">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">{{ $event->name }}</h4>
                <p class="text-muted small mb-0">
                    <i class="fas fa-calendar me-1"></i>{{ $event->event_date->format('d M Y') }}
                    @if($event->venue) | <i class="fas fa-map-marker-alt me-1"></i>{{ $event->venue }} @endif
                </p>
            </div>
            <div class="d-flex gap-2">
                <span class="badge bg-{{ $event->status === 'completed' ? 'success' : ($event->status === 'active' ? 'primary' : 'secondary') }}-subtle text-{{ $event->status === 'completed' ? 'success' : ($event->status === 'active' ? 'primary' : 'secondary') }} py-2 px-3">
                    {{ ucfirst($event->status) }}
                </span>
                <a href="{{ route('pnl.events.edit', $event) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('pnl.export.event', ['event' => $event, 'format' => 'xlsx']) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-download"></i> Export
                </a>
                <a href="{{ route('pnl.events.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <!-- P&L Summary Cards -->
        <div class="row g-3 mb-4">
            <div class="col-lg-3 col-6">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #28a745 !important;">
                    <div class="card-body py-3">
                        <p class="text-muted small mb-1">Net Revenue</p>
                        <h4 class="mb-0 text-success">£{{ number_format($summary['total_revenue'], 0) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #dc3545 !important;">
                    <div class="card-body py-3">
                        <p class="text-muted small mb-1">Total Expenses</p>
                        <h4 class="mb-0 text-danger">£{{ number_format($summary['total_expenses'], 0) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid {{ $summary['profit_status'] === 'profit' ? '#28a745' : ($summary['profit_status'] === 'loss' ? '#ffc107' : '#6c757d') }} !important;">
                    <div class="card-body py-3">
                        <p class="text-muted small mb-1">{{ ucfirst($summary['profit_status']) }}</p>
                        <h4 class="mb-0 {{ $summary['profit_status'] === 'profit' ? 'text-success' : ($summary['profit_status'] === 'loss' ? 'text-warning' : 'text-secondary') }}">
                            {{ $summary['net_profit'] >= 0 ? '' : '-' }}£{{ number_format(abs($summary['net_profit']), 0) }}
                        </h4>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #17a2b8 !important;">
                    <div class="card-body py-3">
                        <p class="text-muted small mb-1">Tickets Sold</p>
                        <h4 class="mb-0 text-info">{{ number_format($summary['tickets_sold']) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Budget Progress -->
        @if($event->budget > 0)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small">Budget Utilisation</span>
                        <span class="small">
                            <strong>£{{ number_format($summary['total_expenses'], 0) }}</strong> / £{{ number_format($event->budget, 0) }}
                            ({{ number_format($summary['budget_utilization'], 1) }}%)
                        </span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        @php
                            $utilization = min($summary['budget_utilization'], 100);
                            $progressClass = $utilization > 100 ? 'bg-danger' : ($utilization > 80 ? 'bg-warning' : 'bg-success');
                        @endphp
                        <div class="progress-bar {{ $progressClass }}" style="width: {{ min($utilization, 100) }}%"></div>
                    </div>
                    @if($summary['budget_utilization'] > 100)
                        <small class="text-danger"><i class="fas fa-exclamation-triangle"></i> Over budget by £{{ number_format($summary['total_expenses'] - $event->budget, 0) }}</small>
                    @endif
                </div>
            </div>
        @endif

        <!-- Event-Specific Tips & Insights -->
        @php
            $totalBudget = $event->budget ?? 0;
            $totalExpenses = $summary['total_expenses'] ?? 0;
            $totalRevenue = $summary['total_revenue'] ?? 0;
            $netProfit = $summary['net_profit'] ?? 0;
        @endphp
        @include('pnl.partials.tips')

        <div class="row g-3 mb-4">
            <!-- Expenses Section -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Expenses</h6>
                        <a href="{{ route('pnl.expenses.create', ['event_id' => $event->id]) }}" class="btn btn-sm btn-danger">
                            <i class="fas fa-plus"></i> Add
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-sm mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="border-0">Title</th>
                                        <th class="border-0">Category</th>
                                        <th class="border-0">Vendor</th>
                                        <th class="border-0 text-end">Amount</th>
                                        <th class="border-0">Payment</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($event->expenses as $expense)
                                        <tr>
                                            <td class="border-0"><a href="{{ route('pnl.expenses.show', $expense) }}">{{ $expense->title }}</a></td>
                                            <td class="border-0">
                                                @if($expense->category)
                                                    <span class="badge" style="background-color: {{ $expense->category->color ?? '#6c757d' }}20; color: {{ $expense->category->color ?? '#6c757d' }}">
                                                        {{ $expense->category->name ?? 'Uncategorized' }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary-subtle text-secondary">Uncategorized</span>
                                                @endif
                                            </td>
                                            <td class="border-0 small">{{ $expense->vendor?->display_name ?? '-' }}</td>
                                            <td class="border-0 text-end">£{{ number_format($expense->total_amount, 0) }}</td>
                                            <td class="border-0">
                                                @if($expense->payment)
                                                    <span class="badge bg-{{ $expense->payment->status_color }}-subtle text-{{ $expense->payment->status_color }}">
                                                        {{ ucfirst($expense->payment->status) }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary-subtle text-secondary">No Payment</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4 border-0 text-muted">No expenses yet</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                @if($event->expenses->count() > 0)
                                    <tfoot class="table-light">
                                        <tr>
                                            <th colspan="3" class="border-0">Total</th>
                                            <th class="border-0 text-end text-danger">£{{ number_format($summary['total_expenses'], 0) }}</th>
                                            <th class="border-0"></th>
                                        </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar - Expense Chart + Event Details -->
            <div class="col-lg-5">
                <!-- Expense Breakdown - SMALLER -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-0 py-3">
                        <h6 class="mb-0">Expense Breakdown</h6>
                    </div>
                    <div class="card-body pt-0">
                        @if($expenseByCategory->count() > 0)
                            <div class="row align-items-center">
                                <div class="col-5">
                                    <div style="max-width: 120px;">
                                        <canvas id="expenseChart" height="120"></canvas>
                                    </div>
                                </div>
                                <div class="col-7">
                                    @foreach($expenseByCategory as $cat)
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="small">
                                                <span class="d-inline-block rounded-circle me-1" style="width: 8px; height: 8px; background-color: {{ $cat['color'] }};"></span>
                                                {{ $cat['name'] }}
                                            </span>
                                            <span class="small fw-bold">£{{ number_format($cat['total'], 0) }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <p class="text-muted text-center small mb-0">No expenses to display</p>
                        @endif
                    </div>
                </div>

                <!-- Event Details -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h6 class="mb-0">Event Details</h6>
                    </div>
                    <div class="card-body pt-0">
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td class="text-muted small" width="40%">Date</td>
                                <td class="small">{{ $event->event_date->format('d M Y') }}</td>
                            </tr>
                            @if($event->event_time)
                                <tr>
                                    <td class="text-muted small">Time</td>
                                    <td class="small">{{ $event->event_time->format('h:i A') }}</td>
                                </tr>
                            @endif
                            @if($event->venue)
                                <tr>
                                    <td class="text-muted small">Venue</td>
                                    <td class="small">{{ $event->venue }}</td>
                                </tr>
                            @endif
                            @if($event->location)
                                <tr>
                                    <td class="text-muted small">Location</td>
                                    <td class="small">{{ $event->location }}</td>
                                </tr>
                            @endif
                            <tr>
                                <td class="text-muted small">Budget</td>
                                <td class="small">£{{ number_format($event->budget, 0) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Section -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Revenue (Ticket Sales)</h6>
                <a href="{{ route('pnl.revenues.create', ['event_id' => $event->id]) }}" class="btn btn-sm btn-danger">
                    <i class="fas fa-plus"></i> Add Revenue
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">Ticket Type</th>
                                <th class="border-0 text-end">Price</th>
                                <th class="border-0 text-end">Sold / Available</th>
                                <th class="border-0 text-end">Gross</th>
                                <th class="border-0 text-end">Net</th>
                                <th class="border-0"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($event->revenues as $revenue)
                                <tr>
                                    <td class="border-0"><a href="{{ route('pnl.revenues.show', $revenue) }}">{{ $revenue->display_name }}</a></td>
                                    <td class="border-0 text-end">£{{ number_format($revenue->ticket_price, 0) }}</td>
                                    <td class="border-0 text-end">
                                        {{ $revenue->tickets_sold }} / {{ $revenue->tickets_available }}
                                        <a href="{{ route('pnl.revenues.edit', $revenue) }}" class="btn btn-sm btn-link p-0 ms-1" title="Update sold count">
                                            <i class="fas fa-plus-circle text-success"></i>
                                        </a>
                                    </td>
                                    <td class="border-0 text-end">£{{ number_format($revenue->gross_revenue, 0) }}</td>
                                    <td class="border-0 text-end text-success">£{{ number_format($revenue->net_revenue_after_refunds, 0) }}</td>
                                    <td class="border-0">
                                        <a href="{{ route('pnl.revenues.edit', $revenue) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 border-0 text-muted">No revenue recorded yet</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($event->revenues->count() > 0)
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="2" class="border-0">Total</th>
                                    <th class="border-0 text-end">{{ $summary['tickets_sold'] }}</th>
                                    <th class="border-0 text-end">£{{ number_format($summary['gross_revenue'], 0) }}</th>
                                    <th class="border-0 text-end text-success">£{{ number_format($summary['total_revenue'], 0) }}</th>
                                    <th class="border-0"></th>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('customjs')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        @if($expenseByCategory->count() > 0)
            const expenseData = {!! json_encode($expenseByCategory) !!};
            const ctx = document.getElementById('expenseChart').getContext('2d');
            new Chart(ctx, {
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
                    cutout: '55%',
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
        @endif
    </script>
@endsection
