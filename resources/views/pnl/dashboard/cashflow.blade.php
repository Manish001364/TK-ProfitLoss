@extends('pnl.layouts.app')

@section('pnl_content')
    <div class="container-fluid" style="max-width: 1200px;">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-0" style="color: #dc3545;">Cash Flow Projections</h4>
                <small class="text-muted">Forecast your upcoming payments and expected revenue</small>
            </div>
            <div class="d-flex gap-2">
                <select class="form-select form-select-sm" style="width: auto;" onchange="changePeriod(this.value)">
                    <option value="30" {{ $period == '30' ? 'selected' : '' }}>Next 30 Days</option>
                    <option value="60" {{ $period == '60' ? 'selected' : '' }}>Next 60 Days</option>
                    <option value="90" {{ $period == '90' ? 'selected' : '' }}>Next 90 Days</option>
                </select>
                <a href="{{ route('pnl.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row g-3 mb-4">
            <!-- Overdue -->
            @if($overdue > 0)
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #dc3545 !important;">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted small mb-1">Overdue</p>
                                <h4 class="mb-0 text-danger">{{ $currencySymbol }}{{ number_format($overdue, 0) }}</h4>
                            </div>
                            <div class="text-danger">
                                <i class="fas fa-exclamation-triangle fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Next 7 Days -->
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #ffc107 !important;">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted small mb-1">Due in 7 Days</p>
                                <h4 class="mb-0 text-warning">{{ $currencySymbol }}{{ number_format($upcoming7, 0) }}</h4>
                            </div>
                            <div class="text-warning">
                                <i class="fas fa-clock fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Next 14 Days -->
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #0dcaf0 !important;">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted small mb-1">Due in 14 Days</p>
                                <h4 class="mb-0 text-info">{{ $currencySymbol }}{{ number_format($upcoming14, 0) }}</h4>
                            </div>
                            <div class="text-info">
                                <i class="fas fa-calendar-week fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Next 30 Days -->
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #198754 !important;">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted small mb-1">Due in 30 Days</p>
                                <h4 class="mb-0 text-success">{{ $currencySymbol }}{{ number_format($upcoming30, 0) }}</h4>
                            </div>
                            <div class="text-success">
                                <i class="fas fa-calendar-alt fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cash Flow Chart -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0"><i class="fas fa-chart-area me-2 text-primary"></i>Cash Flow Projection ({{ $period }} Days)</h6>
            </div>
            <div class="card-body">
                <canvas id="cashFlowChart" height="150"></canvas>
            </div>
        </div>

        <div class="row g-4">
            <!-- Upcoming Payments -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-danger text-white border-0 py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0"><i class="fas fa-arrow-circle-up me-2"></i>Upcoming Outflows (Payments)</h6>
                            <span class="badge bg-white text-danger">{{ $currencySymbol }}{{ number_format($totalProjectedOutflow, 0) }}</span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if($upcomingPaymentsList->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="small">Vendor</th>
                                        <th class="small">Event</th>
                                        <th class="small text-end">Amount</th>
                                        <th class="small text-center">Due</th>
                                        <th class="small text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($upcomingPaymentsList as $payment)
                                    <tr>
                                        <td class="small">{{ Str::limit($payment['vendor_name'], 20) }}</td>
                                        <td class="small text-muted">{{ Str::limit($payment['event_name'], 20) }}</td>
                                        <td class="small text-end fw-bold">{{ $currencySymbol }}{{ number_format($payment['amount'], 0) }}</td>
                                        <td class="small text-center">
                                            @if($payment['scheduled_date'])
                                                <span class="@if($payment['urgency'] == 'high') text-danger @elseif($payment['urgency'] == 'medium') text-warning @else text-success @endif">
                                                    {{ \Carbon\Carbon::parse($payment['scheduled_date'])->format('d M') }}
                                                </span>
                                                <br><small class="text-muted">{{ $payment['days_until'] }} days</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="small text-center">
                                            @if($payment['urgency'] == 'high')
                                                <span class="badge bg-danger">Urgent</span>
                                            @elseif($payment['urgency'] == 'medium')
                                                <span class="badge bg-warning text-dark">Soon</span>
                                            @else
                                                <span class="badge bg-secondary">Scheduled</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-check-circle fa-2x mb-2 text-success"></i>
                            <p class="mb-0">No upcoming payments scheduled</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Upcoming Events (Revenue) -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-success text-white border-0 py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0"><i class="fas fa-arrow-circle-down me-2"></i>Expected Inflows (Events)</h6>
                            <span class="badge bg-white text-success">{{ $currencySymbol }}{{ number_format($totalProjectedInflow, 0) }}</span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if($upcomingEvents->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="small">Event</th>
                                        <th class="small text-end">Expected</th>
                                        <th class="small text-center">Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($upcomingEvents as $event)
                                    <tr>
                                        <td class="small">
                                            <a href="{{ route('pnl.events.show', $event['id']) }}" class="text-decoration-none">
                                                {{ Str::limit($event['name'], 25) }}
                                            </a>
                                        </td>
                                        <td class="small text-end">
                                            <span class="fw-bold text-success">{{ $currencySymbol }}{{ number_format($event['expected_revenue'], 0) }}</span>
                                            @if($event['projected_profit'] < 0)
                                                <br><small class="text-danger">Loss: {{ $currencySymbol }}{{ number_format(abs($event['projected_profit']), 0) }}</small>
                                            @else
                                                <br><small class="text-success">Profit: {{ $currencySymbol }}{{ number_format($event['projected_profit'], 0) }}</small>
                                            @endif
                                        </td>
                                        <td class="small text-center">
                                            {{ \Carbon\Carbon::parse($event['date'])->format('d M') }}
                                            <br><small class="text-muted">{{ $event['days_until'] }} days</small>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-calendar-plus fa-2x mb-2"></i>
                            <p class="mb-0">No upcoming events</p>
                            <a href="{{ route('pnl.events.create') }}" class="btn btn-sm btn-outline-success mt-2">
                                <i class="fas fa-plus me-1"></i> Add Event
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Net Position Summary -->
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4">
                        <p class="text-muted small mb-1">Total Expected Inflows</p>
                        <h4 class="text-success mb-0">{{ $currencySymbol }}{{ number_format($totalProjectedInflow, 0) }}</h4>
                    </div>
                    <div class="col-md-4">
                        <p class="text-muted small mb-1">Total Projected Outflows</p>
                        <h4 class="text-danger mb-0">{{ $currencySymbol }}{{ number_format($totalProjectedOutflow, 0) }}</h4>
                    </div>
                    <div class="col-md-4">
                        <p class="text-muted small mb-1">Net Position</p>
                        @php $netPosition = $totalProjectedInflow - $totalProjectedOutflow; @endphp
                        <h4 class="mb-0 {{ $netPosition >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $netPosition >= 0 ? '+' : '' }}{{ $currencySymbol }}{{ number_format($netPosition, 0) }}
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('customjs')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function changePeriod(period) {
            window.location.href = '{{ route("pnl.dashboard.cashflow") }}?period=' + period;
        }

        // Cash Flow Chart
        const ctx = document.getElementById('cashFlowChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($projectionData['labels']),
                datasets: [
                    {
                        label: 'Expected Inflows',
                        data: @json($projectionData['inflows']),
                        backgroundColor: 'rgba(25, 135, 84, 0.7)',
                        borderColor: 'rgba(25, 135, 84, 1)',
                        borderWidth: 1,
                        stack: 'stack1',
                    },
                    {
                        label: 'Scheduled Outflows',
                        data: @json($projectionData['outflows']).map(v => -v),
                        backgroundColor: 'rgba(220, 53, 69, 0.7)',
                        borderColor: 'rgba(220, 53, 69, 1)',
                        borderWidth: 1,
                        stack: 'stack1',
                    },
                    {
                        label: 'Cumulative Position',
                        data: @json($projectionData['cumulative']),
                        type: 'line',
                        borderColor: 'rgba(13, 110, 253, 1)',
                        backgroundColor: 'rgba(13, 110, 253, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3,
                        yAxisID: 'y1',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                scales: {
                    y: {
                        stacked: true,
                        ticks: {
                            callback: function(value) {
                                return '{{ $currencySymbol }}' + Math.abs(value).toLocaleString();
                            }
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        }
                    },
                    y1: {
                        position: 'right',
                        ticks: {
                            callback: function(value) {
                                return '{{ $currencySymbol }}' + value.toLocaleString();
                            }
                        },
                        grid: {
                            drawOnChartArea: false
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                let value = Math.abs(context.raw);
                                return label + ': {{ $currencySymbol }}' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    </script>
@endsection
