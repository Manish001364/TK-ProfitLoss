@extends('adminlte::page')

@section('title', $vendor->display_name . ' - Vendor Details')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1>
                <i class="fas fa-user"></i> {{ $vendor->display_name }}
                <span class="badge badge-info">{{ ucfirst($vendor->type) }}</span>
                <span class="badge badge-{{ $vendor->is_active ? 'success' : 'secondary' }}">
                    {{ $vendor->is_active ? 'Active' : 'Inactive' }}
                </span>
            </h1>
            @if($vendor->business_name && $vendor->full_name !== $vendor->business_name)
                <p class="text-muted mb-0">{{ $vendor->full_name }}</p>
            @endif
        </div>
        <div>
            <a href="{{ route('pnl.vendors.edit', $vendor) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('pnl.vendors.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
@stop

@section('content')
    <!-- Summary Cards -->
    <div class="row">
        <div class="col-md-3">
            <div class="info-box bg-success">
                <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Paid</span>
                    <span class="info-box-number">₹{{ number_format($summary['total_paid'], 0) }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box bg-warning">
                <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Pending</span>
                    <span class="info-box-number">₹{{ number_format($summary['total_pending'], 0) }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box bg-info">
                <span class="info-box-icon"><i class="fas fa-rupee-sign"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Expenses</span>
                    <span class="info-box-number">₹{{ number_format($summary['total_expenses'], 0) }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box bg-primary">
                <span class="info-box-icon"><i class="fas fa-calendar"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Events</span>
                    <span class="info-box-number">{{ $summary['events_count'] }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <!-- Contact Info -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-address-card"></i> Contact Information</h3>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4"><i class="fas fa-envelope"></i> Email</dt>
                        <dd class="col-sm-8"><a href="mailto:{{ $vendor->email }}">{{ $vendor->email }}</a></dd>
                        
                        @if($vendor->phone)
                            <dt class="col-sm-4"><i class="fas fa-phone"></i> Phone</dt>
                            <dd class="col-sm-8"><a href="tel:{{ $vendor->phone }}">{{ $vendor->phone }}</a></dd>
                        @endif
                        
                        @if($vendor->alternate_phone)
                            <dt class="col-sm-4"><i class="fas fa-phone-alt"></i> Alt Phone</dt>
                            <dd class="col-sm-8">{{ $vendor->alternate_phone }}</dd>
                        @endif
                        
                        @if($vendor->business_address)
                            <dt class="col-sm-4"><i class="fas fa-building"></i> Business Address</dt>
                            <dd class="col-sm-8">{{ $vendor->business_address }}</dd>
                        @endif
                        
                        @if($vendor->home_address)
                            <dt class="col-sm-4"><i class="fas fa-home"></i> Home Address</dt>
                            <dd class="col-sm-8">{{ $vendor->home_address }}</dd>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Emergency Contact -->
            @if($vendor->emergency_contact_name || $vendor->emergency_contact_phone)
                <div class="card card-warning">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-exclamation-triangle"></i> Emergency Contact</h3>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            @if($vendor->emergency_contact_name)
                                <dt class="col-sm-4">Name</dt>
                                <dd class="col-sm-8">{{ $vendor->emergency_contact_name }}</dd>
                            @endif
                            @if($vendor->emergency_contact_phone)
                                <dt class="col-sm-4">Phone</dt>
                                <dd class="col-sm-8">{{ $vendor->emergency_contact_phone }}</dd>
                            @endif
                            @if($vendor->emergency_contact_relation)
                                <dt class="col-sm-4">Relation</dt>
                                <dd class="col-sm-8">{{ $vendor->emergency_contact_relation }}</dd>
                            @endif
                        </dl>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-md-6">
            <!-- Bank Details -->
            @if($vendor->bank_name || $vendor->bank_account_number)
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-university"></i> Bank Details</h3>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            @if($vendor->bank_name)
                                <dt class="col-sm-4">Bank</dt>
                                <dd class="col-sm-8">{{ $vendor->bank_name }}</dd>
                            @endif
                            @if($vendor->bank_branch)
                                <dt class="col-sm-4">Branch</dt>
                                <dd class="col-sm-8">{{ $vendor->bank_branch }}</dd>
                            @endif
                            @if($vendor->bank_account_name)
                                <dt class="col-sm-4">Account Name</dt>
                                <dd class="col-sm-8">{{ $vendor->bank_account_name }}</dd>
                            @endif
                            @if($vendor->bank_account_number)
                                <dt class="col-sm-4">Account No.</dt>
                                <dd class="col-sm-8">{{ $vendor->masked_bank_account }}</dd>
                            @endif
                            @if($vendor->bank_ifsc_code)
                                <dt class="col-sm-4">IFSC</dt>
                                <dd class="col-sm-8">{{ $vendor->bank_ifsc_code }}</dd>
                            @endif
                        </dl>
                    </div>
                </div>
            @endif

            <!-- Tax Info -->
            @if($vendor->pan_number || $vendor->gst_number || $vendor->tax_vat_reference)
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-file-invoice"></i> Tax Information</h3>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            @if($vendor->pan_number)
                                <dt class="col-sm-4">PAN</dt>
                                <dd class="col-sm-8">{{ $vendor->pan_number }}</dd>
                            @endif
                            @if($vendor->gst_number)
                                <dt class="col-sm-4">GST</dt>
                                <dd class="col-sm-8">{{ $vendor->gst_number }}</dd>
                            @endif
                            @if($vendor->tax_vat_reference)
                                <dt class="col-sm-4">Tax/VAT Ref</dt>
                                <dd class="col-sm-8">{{ $vendor->tax_vat_reference }}</dd>
                            @endif
                        </dl>
                    </div>
                </div>
            @endif

            <!-- Notes -->
            @if($vendor->notes || $vendor->preferred_payment_cycle)
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-sticky-note"></i> Additional Info</h3>
                    </div>
                    <div class="card-body">
                        @if($vendor->preferred_payment_cycle)
                            <p><strong>Payment Cycle:</strong> {{ ucfirst($vendor->preferred_payment_cycle) }}</p>
                        @endif
                        @if($vendor->notes)
                            <p class="mb-0"><strong>Notes:</strong><br>{{ $vendor->notes }}</p>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Payment History -->
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-history"></i> Payment History</h3>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Event</th>
                        <th>Expense</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Scheduled</th>
                        <th>Paid Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vendor->payments as $payment)
                        <tr>
                            <td>{{ $payment->expense->event->name ?? 'N/A' }}</td>
                            <td><a href="{{ route('pnl.expenses.show', $payment->expense) }}">{{ $payment->expense->title }}</a></td>
                            <td>₹{{ number_format($payment->amount, 0) }}</td>
                            <td><span class="badge badge-{{ $payment->status_color }}">{{ ucfirst($payment->status) }}</span></td>
                            <td>{{ $payment->scheduled_date?->format('d M Y') ?? '-' }}</td>
                            <td>{{ $payment->actual_paid_date?->format('d M Y') ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No payment history</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop
