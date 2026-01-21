@extends('layouts.organiser_layout')

@section('content')
    <div class="container py-4" style="max-width: 1000px;">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">Cash Flow Projections</h4>
                <p class="text-muted small mb-0">Upcoming payment obligations</p>
            </div>
            <a href="{{ route('pnl.dashboard') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left"></i> Dashboard</a>
        </div>

        <!-- Summary Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-danger text-white">
                    <div class="card-body py-3">
                        <h4 class="mb-0">£{{ number_format($overdue, 0) }}</h4>
                        <small>Overdue</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-warning text-dark">
                    <div class="card-body py-3">
                        <h4 class="mb-0">£{{ number_format($upcoming7, 0) }}</h4>
                        <small>Next 7 Days</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-info text-white">
                    <div class="card-body py-3">
                        <h4 class="mb-0">£{{ number_format($upcoming30, 0) }}</h4>
                        <small>Next 30 Days</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Timeline -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0"><i class="fas fa-chart-line me-2 text-primary"></i>Payment Timeline</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <div class="border rounded p-3 mb-3">
                            <h5 class="text-danger mb-1">£{{ number_format($overdue, 0) }}</h5>
                            <small class="text-muted">Overdue</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3 mb-3">
                            <h5 class="text-warning mb-1">£{{ number_format($upcoming7, 0) }}</h5>
                            <small class="text-muted">7 Days</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3 mb-3">
                            <h5 class="text-info mb-1">£{{ number_format($upcoming14, 0) }}</h5>
                            <small class="text-muted">14 Days</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3 mb-3">
                            <h5 class="text-secondary mb-1">£{{ number_format($upcoming30, 0) }}</h5>
                            <small class="text-muted">30 Days</small>
                        </div>
                    </div>
                </div>

                <div class="progress mt-3" style="height: 30px;">
                    @php
                        $total = $overdue + $upcoming30;
                        $overduePercent = $total > 0 ? ($overdue / $total) * 100 : 0;
                        $upcoming7Percent = $total > 0 ? ($upcoming7 / $total) * 100 : 0;
                        $remaining = 100 - $overduePercent - $upcoming7Percent;
                    @endphp
                    <div class="progress-bar bg-danger" style="width: {{ $overduePercent }}%">Overdue</div>
                    <div class="progress-bar bg-warning" style="width: {{ $upcoming7Percent }}%">7 Days</div>
                    <div class="progress-bar bg-info" style="width: {{ $remaining }}%">Later</div>
                </div>

                <div class="mt-4">
                    <h6>Total Outstanding: <span class="text-danger">£{{ number_format($outstanding, 0) }}</span></h6>
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="row g-3 mt-3">
            <div class="col-md-4">
                <a href="{{ route('pnl.payments.overdue') }}" class="btn btn-outline-danger w-100">
                    <i class="fas fa-exclamation-circle me-1"></i> View Overdue
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('pnl.payments.upcoming') }}" class="btn btn-outline-warning w-100">
                    <i class="fas fa-clock me-1"></i> View Upcoming
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('pnl.payments.index') }}" class="btn btn-outline-primary w-100">
                    <i class="fas fa-list me-1"></i> All Payments
                </a>
            </div>
        </div>
    </div>
@endsection
