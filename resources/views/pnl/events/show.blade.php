@extends('adminlte::page')

@section('title', $event->name . ' - Event Details')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1>
                <i class="fas fa-calendar-alt"></i> {{ $event->name }}
                @php
                    $statusColors = [
                        'draft' => 'secondary',
                        'planning' => 'info',
                        'active' => 'primary',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                    ];
                @endphp
                <span class="badge badge-{{ $statusColors[$event->status] ?? 'secondary' }}">
                    {{ ucfirst($event->status) }}
                </span>
            </h1>
            <p class="text-muted mb-0">
                <i class="fas fa-calendar"></i> {{ $event->event_date->format('d M Y') }}
                @if($event->venue)
                    | <i class="fas fa-map-marker-alt"></i> {{ $event->venue }}
                @endif
            </p>
        </div>
        <div>
            <a href="{{ route('pnl.events.edit', $event) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('pnl.export.event', ['event' => $event, 'format' => 'xlsx']) }}" class="btn btn-success">
                <i class="fas fa-download"></i> Export
            </a>
            <a href="{{ route('pnl.events.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
@stop

@section('content')
    <!-- P&L Summary Cards -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>₹{{ number_format($summary['total_revenue'], 0) }}</h3>
                    <p>Net Revenue</p>
                </div>
                <div class="icon"><i class="fas fa-rupee-sign"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>₹{{ number_format($summary['total_expenses'], 0) }}</h3>
                    <p>Total Expenses</p>
                </div>
                <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box {{ $summary['profit_status'] === 'profit' ? 'bg-primary' : ($summary['profit_status'] === 'loss' ? 'bg-warning' : 'bg-secondary') }}">
                <div class="inner">
                    <h3>{{ $summary['net_profit'] >= 0 ? '' : '-' }}₹{{ number_format(abs($summary['net_profit']), 0) }}</h3>
                    <p>{{ ucfirst($summary['profit_status']) }}</p>
                </div>
                <div class="icon">
                    <i class="fas {{ $summary['profit_status'] === 'profit' ? 'fa-arrow-up' : ($summary['profit_status'] === 'loss' ? 'fa-arrow-down' : 'fa-equals') }}"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ number_format($summary['tickets_sold']) }}</h3>
                    <p>Tickets Sold</p>
                </div>
                <div class="icon"><i class="fas fa-ticket-alt"></i></div>
            </div>
        </div>
    </div>

    <!-- Budget Progress -->
    @if($event->budget > 0)
        <div class="card card-outline card-primary mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <span>Budget Utilization</span>
                    <span>
                        <strong>₹{{ number_format($summary['total_expenses'], 0) }}</strong> / ₹{{ number_format($event->budget, 0) }}
                        ({{ number_format($summary['budget_utilization'], 1) }}%)
                    </span>
                </div>
                <div class="progress mt-2" style="height: 20px;">
                    @php
                        $utilization = min($summary['budget_utilization'], 100);
                        $progressClass = $utilization > 100 ? 'bg-danger' : ($utilization > 80 ? 'bg-warning' : 'bg-success');
                    @endphp
                    <div class="progress-bar {{ $progressClass }}" role="progressbar" 
                         style="width: {{ min($utilization, 100) }}%"></div>
                </div>
                @if($summary['budget_utilization'] > 100)
                    <div class="text-danger mt-2">
                        <i class="fas fa-exclamation-triangle"></i> 
                        Over budget by ₹{{ number_format($summary['total_expenses'] - $event->budget, 0) }}
                    </div>
                @endif
            </div>
        </div>
    @endif

    <div class="row">
        <!-- Expenses Section -->
        <div class="col-lg-7">
            <div class="card card-outline card-danger">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-receipt"></i> Expenses</h3>
                    <div class="card-tools">
                        <a href="{{ route('pnl.expenses.create', ['event_id' => $event->id]) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus"></i> Add Expense
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Vendor</th>
                                <th class="text-right">Amount</th>
                                <th>Payment</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($event->expenses as $expense)
                                <tr>
                                    <td>
                                        <a href="{{ route('pnl.expenses.show', $expense) }}">{{ $expense->title }}</a>
                                    </td>
                                    <td>
                                        <span style="color: {{ $expense->category->color }}">
                                            <i class="{{ $expense->category->icon ?? 'fas fa-tag' }}"></i>
                                            {{ $expense->category->name }}
                                        </span>
                                    </td>
                                    <td>{{ $expense->vendor?->display_name ?? '-' }}</td>
                                    <td class="text-right">₹{{ number_format($expense->total_amount, 0) }}</td>
                                    <td>
                                        @if($expense->payment)
                                            <span class="badge badge-{{ $expense->payment->status_color }}">
                                                {{ ucfirst($expense->payment->status) }}
                                            </span>
                                        @else
                                            <span class="badge badge-secondary">No Payment</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-3">
                                        <p class="text-muted mb-0">No expenses recorded yet.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($event->expenses->count() > 0)
                            <tfoot class="bg-light">
                                <tr>
                                    <th colspan="3">Total Expenses</th>
                                    <th class="text-right text-danger">₹{{ number_format($summary['total_expenses'], 0) }}</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>

            <!-- Revenue Section -->
            <div class="card card-outline card-success">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-ticket-alt"></i> Revenue (Ticket Sales)</h3>
                    <div class="card-tools">
                        <a href="{{ route('pnl.revenues.create', ['event_id' => $event->id]) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus"></i> Add Revenue
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Ticket Type</th>
                                <th class="text-right">Price</th>
                                <th class="text-right">Sold</th>
                                <th class="text-right">Gross</th>
                                <th class="text-right">Net</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($event->revenues as $revenue)
                                <tr>
                                    <td>
                                        <a href="{{ route('pnl.revenues.show', $revenue) }}">{{ $revenue->display_name }}</a>
                                    </td>
                                    <td class="text-right">₹{{ number_format($revenue->ticket_price, 0) }}</td>
                                    <td class="text-right">{{ $revenue->tickets_sold }} / {{ $revenue->tickets_available }}</td>
                                    <td class="text-right">₹{{ number_format($revenue->gross_revenue, 0) }}</td>
                                    <td class="text-right text-success">₹{{ number_format($revenue->net_revenue_after_refunds, 0) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-3">
                                        <p class="text-muted mb-0">No revenue recorded yet.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($event->revenues->count() > 0)
                            <tfoot class="bg-light">
                                <tr>
                                    <th colspan="2">Total</th>
                                    <th class="text-right">{{ $summary['tickets_sold'] }}</th>
                                    <th class="text-right">₹{{ number_format($summary['gross_revenue'], 0) }}</th>
                                    <th class="text-right text-success">₹{{ number_format($summary['total_revenue'], 0) }}</th>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-5">
            <!-- Expense Breakdown Chart -->
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-pie"></i> Expense Breakdown</h3>
                </div>
                <div class="card-body">
                    @if($expenseByCategory->count() > 0)
                        <canvas id="expenseChart" height="200"></canvas>
                        <hr>
                        <ul class="list-unstyled">
                            @foreach($expenseByCategory as $cat)
                                <li class="mb-2">
                                    <span class="badge" style="background-color: {{ $cat['color'] }}">&nbsp;</span>
                                    {{ $cat['name'] }}: <strong>₹{{ number_format($cat['total'], 0) }}</strong>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted text-center">No expenses to display</p>
                    @endif
                </div>
            </div>

            <!-- Event Details -->
            <div class="card card-outline card-secondary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-info-circle"></i> Event Details</h3>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Date</dt>
                        <dd class="col-sm-8">{{ $event->event_date->format('d M Y') }}</dd>
                        
                        @if($event->event_time)
                            <dt class="col-sm-4">Time</dt>
                            <dd class="col-sm-8">{{ $event->event_time->format('h:i A') }}</dd>
                        @endif
                        
                        @if($event->venue)
                            <dt class="col-sm-4">Venue</dt>
                            <dd class="col-sm-8">{{ $event->venue }}</dd>
                        @endif
                        
                        @if($event->location)
                            <dt class="col-sm-4">Location</dt>
                            <dd class="col-sm-8">{{ $event->location }}</dd>
                        @endif
                        
                        <dt class="col-sm-4">Budget</dt>
                        <dd class="col-sm-8">₹{{ number_format($event->budget, 0) }}</dd>
                        
                        @if($event->description)
                            <dt class="col-sm-4">Description</dt>
                            <dd class="col-sm-8">{{ $event->description }}</dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
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
                    }]
                },
                options: {
                    responsive: true,
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
        @endif
    </script>
@stop
