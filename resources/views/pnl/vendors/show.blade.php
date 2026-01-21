@extends('pnl.layouts.app')

@section('pnl_content')
    <div class="container-fluid" style="max-width: 1000px;">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">
                    <i class="fas fa-user"></i> {{ $vendor->display_name }}
                    <span class="badge bg-info small">{{ ucfirst($vendor->type) }}</span>
                    <span class="badge bg-{{ $vendor->is_active ? 'success' : 'secondary' }} small">
                        {{ $vendor->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </h4>
                @if($vendor->business_name && $vendor->full_name !== $vendor->business_name)
                    <p class="text-muted small mb-0">{{ $vendor->full_name }}</p>
                @endif
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('pnl.vendors.edit', $vendor) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('pnl.vendors.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card bg-success text-white h-100 border-0 shadow-sm">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-0">£{{ number_format($summary['total_paid'], 0) }}</h5>
                                <small>Total Paid</small>
                            </div>
                            <i class="fas fa-check-circle fa-lg opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card bg-warning text-dark h-100 border-0 shadow-sm">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-0">£{{ number_format($summary['total_pending'], 0) }}</h5>
                                <small>Pending</small>
                            </div>
                            <i class="fas fa-clock fa-lg opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card bg-info text-white h-100 border-0 shadow-sm">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-0">£{{ number_format($summary['total_expenses'], 0) }}</h5>
                                <small>Total Expenses</small>
                            </div>
                            <i class="fas fa-pound-sign fa-lg opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card bg-primary text-white h-100 border-0 shadow-sm">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-0">{{ $summary['events_count'] }}</h5>
                                <small>Events</small>
                            </div>
                            <i class="fas fa-calendar fa-lg opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <!-- Contact Info -->
                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-header bg-primary text-white py-2">
                        <h6 class="card-title mb-0"><i class="fas fa-address-card me-1"></i> Contact Information</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless mb-0 small">
                            <tr>
                                <td class="text-muted" style="width: 120px;"><i class="fas fa-envelope me-1"></i> Email</td>
                                <td>@if($vendor->email)<a href="mailto:{{ $vendor->email }}">{{ $vendor->email }}</a>@else - @endif</td>
                            </tr>
                            <tr>
                                <td class="text-muted"><i class="fas fa-phone me-1"></i> Phone</td>
                                <td>@if($vendor->phone)<a href="tel:{{ $vendor->phone }}">{{ $vendor->phone }}</a>@else - @endif</td>
                            </tr>
                            @if($vendor->alternate_phone)
                            <tr>
                                <td class="text-muted"><i class="fas fa-phone-alt me-1"></i> Alt Phone</td>
                                <td>{{ $vendor->alternate_phone }}</td>
                            </tr>
                            @endif
                            @if($vendor->business_address)
                            <tr>
                                <td class="text-muted"><i class="fas fa-building me-1"></i> Business</td>
                                <td>{{ $vendor->business_address }}</td>
                            </tr>
                            @endif
                            @if($vendor->specialization)
                            <tr>
                                <td class="text-muted"><i class="fas fa-star me-1"></i> Service</td>
                                <td>{{ $vendor->specialization }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <!-- Additional Info -->
                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-header bg-success text-white py-2">
                        <h6 class="card-title mb-0"><i class="fas fa-info-circle me-1"></i> Additional Info</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless mb-0 small">
                            <tr>
                                <td class="text-muted" style="width: 130px;">Payment Cycle</td>
                                <td>{{ $vendor->preferred_payment_cycle ? ucfirst(str_replace('_', ' ', $vendor->preferred_payment_cycle)) : '-' }}</td>
                            </tr>
                            @if($vendor->gst_number)
                            <tr>
                                <td class="text-muted">VAT Number</td>
                                <td>{{ $vendor->gst_number }}</td>
                            </tr>
                            @endif
                            @if($vendor->pan_number)
                            <tr>
                                <td class="text-muted">UTR</td>
                                <td>{{ $vendor->pan_number }}</td>
                            </tr>
                            @endif
                            @if($vendor->tax_vat_reference)
                            <tr>
                                <td class="text-muted">Company No</td>
                                <td>{{ $vendor->tax_vat_reference }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bank Details (Collapsible) -->
        @if($vendor->bank_name || $vendor->bank_account_number)
        <div class="card mb-3 border-0 shadow-sm">
            <div class="card-header bg-secondary text-white py-2" data-bs-toggle="collapse" data-bs-target="#bankDetails" style="cursor: pointer;">
                <h6 class="card-title mb-0">
                    <i class="fas fa-university me-1"></i> Bank Details
                    <i class="fas fa-chevron-down float-end"></i>
                </h6>
            </div>
            <div class="collapse" id="bankDetails">
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0 small">
                        @if($vendor->bank_name)
                        <tr><td class="text-muted" style="width: 130px;">Bank</td><td>{{ $vendor->bank_name }}</td></tr>
                        @endif
                        @if($vendor->bank_account_name)
                        <tr><td class="text-muted">Account Name</td><td>{{ $vendor->bank_account_name }}</td></tr>
                        @endif
                        @if($vendor->bank_account_number)
                        <tr><td class="text-muted">Account No</td><td>{{ $vendor->bank_account_number }}</td></tr>
                        @endif
                        @if($vendor->bank_ifsc_code)
                        <tr><td class="text-muted">Sort Code</td><td>{{ $vendor->bank_ifsc_code }}</td></tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Notes -->
        @if($vendor->notes)
        <div class="card mb-3 border-0 shadow-sm">
            <div class="card-header bg-light py-2">
                <h6 class="card-title mb-0"><i class="fas fa-sticky-note me-1"></i> Notes</h6>
            </div>
            <div class="card-body small">
                {{ $vendor->notes }}
            </div>
        </div>
        @endif

        <!-- Payment History -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light py-2">
                <h6 class="card-title mb-0"><i class="fas fa-history me-1"></i> Payment History</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th>Event</th>
                                <th>Expense</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Invoice</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $payment)
                                <tr>
                                    <td>{{ $payment->expense?->event?->name ?? 'N/A' }}</td>
                                    <td>
                                        @if($payment->expense)
                                            <a href="{{ route('pnl.expenses.show', $payment->expense) }}" class="text-dark">
                                                {{ $payment->expense->title }}
                                            </a>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>£{{ number_format($payment->amount, 0) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $payment->status === 'paid' ? 'success' : ($payment->status === 'scheduled' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($payment->status === 'paid' && $payment->actual_paid_date)
                                            <small class="text-success"><i class="fas fa-check-circle me-1"></i>{{ $payment->actual_paid_date->format('d M Y') }}</small>
                                        @elseif($payment->scheduled_date)
                                            <small class="text-muted"><i class="fas fa-clock me-1"></i>{{ $payment->scheduled_date->format('d M Y') }}</small>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $payment->expense?->invoice_number ?? '-' }}</small>
                                    </td>
                                    <td>
                                        @if($payment->expense)
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('pnl.expenses.pdf', $payment->expense) }}" class="btn btn-outline-danger" title="Download PDF Invoice">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>
                                                @if($vendor->email)
                                                    <button type="button" class="btn btn-outline-primary" onclick="emailInvoice('{{ $payment->expense->id }}')" title="Email Invoice to {{ $vendor->email }}">
                                                        <i class="fas fa-envelope"></i>
                                                    </button>
                                                @endif
                                                <a href="{{ route('pnl.expenses.show', $payment->expense) }}" class="btn btn-outline-secondary" title="View Expense Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">No payment history</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Email Invoice Modal -->
    <div class="modal fade" id="emailModal" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header border-0 bg-primary text-white">
                    <h6 class="modal-title"><i class="fas fa-envelope me-1"></i> Email Invoice</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Send invoice to:</p>
                    <p class="fw-bold">{{ $vendor->email ?? 'No email on file' }}</p>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="emailForm" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-paper-plane me-1"></i> Send</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('customjs')
    <script>
        function emailInvoice(expenseId) {
            document.getElementById('emailForm').action = '/pnl/expenses/' + expenseId + '/email';
            var modal = new bootstrap.Modal(document.getElementById('emailModal'));
            modal.show();
        }
    </script>
@endsection
