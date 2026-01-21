@extends('layouts.organiser_layout')

@section('content')
    <div class="container py-4" style="max-width: 900px;">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">{{ $expense->title }}</h4>
                <p class="text-muted small mb-0">
                    <span class="badge" style="background-color: {{ $expense->category->color }}20; color: {{ $expense->category->color }}">
                        {{ $expense->category->name }}
                    </span>
                    <span class="ms-2">{{ $expense->expense_date->format('d M Y') }}</span>
                </p>
            </div>
            <div class="d-flex gap-2">
                <!-- PDF Download -->
                <a href="{{ route('pnl.expenses.pdf', $expense) }}" class="btn btn-outline-danger btn-sm" title="Download PDF">
                    <i class="fas fa-file-pdf"></i> PDF
                </a>
                <!-- Email to Vendor -->
                @if($expense->vendor && $expense->vendor->email)
                    <form action="{{ route('pnl.expenses.email', $expense) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-outline-primary btn-sm" title="Email Invoice to Vendor" 
                                onclick="return confirm('Send invoice to {{ $expense->vendor->email }}?')">
                            <i class="fas fa-envelope"></i> Email
                        </button>
                    </form>
                @endif
                <a href="{{ route('pnl.expenses.edit', $expense) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('pnl.events.show', $expense->event) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row g-4">
            <div class="col-md-8">
                <!-- Expense Details -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h6 class="mb-0"><i class="fas fa-receipt me-2"></i>Expense Details</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless mb-0">
                            <tr>
                                <td class="text-muted" width="35%">Invoice Number</td>
                                <td><strong>{{ $expense->invoice_number ?? 'N/A' }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Event</td>
                                <td><a href="{{ route('pnl.events.show', $expense->event) }}">{{ $expense->event->name }}</a></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Category</td>
                                <td>
                                    <span class="badge" style="background-color: {{ $expense->category->color }}20; color: {{ $expense->category->color }}">
                                        <i class="{{ $expense->category->icon ?? 'fas fa-tag' }} me-1"></i>
                                        {{ $expense->category->name }}
                                    </span>
                                    <span class="badge bg-secondary-subtle text-secondary ms-1">{{ ucfirst($expense->category->type) }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Vendor/Artist</td>
                                <td>
                                    @if($expense->vendor)
                                        <a href="{{ route('pnl.vendors.show', $expense->vendor) }}">{{ $expense->vendor->display_name }}</a>
                                        <span class="badge bg-info-subtle text-info ms-1">{{ ucfirst($expense->vendor->type) }}</span>
                                    @else
                                        <span class="text-muted">Not assigned</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Date</td>
                                <td>{{ $expense->expense_date->format('d M Y') }}</td>
                            </tr>
                            @if($expense->description)
                            <tr>
                                <td class="text-muted">Description</td>
                                <td>{{ $expense->description }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>

                <!-- Payment Status -->
                @if($expense->payment)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="fas fa-credit-card me-2"></i>Payment Status</h6>
                        <span class="badge bg-{{ $expense->payment->status_color }}-subtle text-{{ $expense->payment->status_color }} py-2 px-3">
                            {{ ucfirst($expense->payment->status) }}
                        </span>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless mb-0">
                            <tr>
                                <td class="text-muted" width="35%">Amount</td>
                                <td><strong class="h5 mb-0">£{{ number_format($expense->payment->amount, 2) }}</strong></td>
                            </tr>
                            @if($expense->payment->payment_method)
                            <tr>
                                <td class="text-muted">Payment Method</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $expense->payment->payment_method)) }}</td>
                            </tr>
                            @endif
                            @if($expense->payment->scheduled_date)
                            <tr>
                                <td class="text-muted">Scheduled Date</td>
                                <td>
                                    {{ $expense->payment->scheduled_date->format('d M Y') }}
                                    @if($expense->payment->is_overdue)
                                        <span class="badge bg-danger ms-1">Overdue</span>
                                    @elseif($expense->payment->days_until_due !== null && $expense->payment->status !== 'paid')
                                        <span class="badge bg-{{ $expense->payment->days_until_due <= 7 ? 'warning' : 'secondary' }} ms-1">
                                            {{ $expense->payment->days_until_due }} days
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @endif
                            @if($expense->payment->actual_paid_date)
                            <tr>
                                <td class="text-muted">Paid Date</td>
                                <td>{{ $expense->payment->actual_paid_date->format('d M Y') }}</td>
                            </tr>
                            @endif
                            @if($expense->payment->transaction_reference)
                            <tr>
                                <td class="text-muted">Reference</td>
                                <td>{{ $expense->payment->transaction_reference }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td class="text-muted">Email Notifications</td>
                                <td>
                                    @if($expense->payment->send_email_to_vendor)
                                        <span class="badge bg-success-subtle text-success"><i class="fas fa-check me-1"></i>Enabled</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary"><i class="fas fa-times me-1"></i>Disabled</span>
                                    @endif
                                </td>
                            </tr>
                        </table>

                        @if($expense->payment->status !== 'paid')
                        <div class="mt-3 pt-3 border-top">
                            <form action="{{ route('pnl.payments.mark-paid', $expense->payment) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="fas fa-check me-1"></i> Mark as Paid
                                </button>
                            </form>
                            <a href="{{ route('pnl.payments.edit', $expense->payment) }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-edit me-1"></i> Edit Payment
                            </a>
                            @if($expense->vendor && $expense->vendor->email && $expense->payment->send_email_to_vendor)
                                <form action="{{ route('pnl.expenses.email', $expense) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-info btn-sm" title="Send invoice/reminder to vendor">
                                        <i class="fas fa-envelope me-1"></i> Send Reminder
                                    </button>
                                </form>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            <div class="col-md-4">
                <!-- Amount Summary -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h6 class="mb-0"><i class="fas fa-pound-sign me-2"></i>Amount</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td>Net Amount</td>
                                <td class="text-end">£{{ number_format($expense->amount, 2) }}</td>
                            </tr>
                            @if($expense->tax_amount > 0)
                            <tr>
                                <td>VAT ({{ $expense->tax_rate ?? 20 }}%)</td>
                                <td class="text-end">£{{ number_format($expense->tax_amount, 2) }}</td>
                            </tr>
                            @else
                            <tr>
                                <td>VAT</td>
                                <td class="text-end text-muted">Non-taxable</td>
                            </tr>
                            @endif
                            <tr class="border-top">
                                <td><strong>Total</strong></td>
                                <td class="text-end"><strong class="text-danger">£{{ number_format($expense->total_amount, 2) }}</strong></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Vendor Contact -->
                @if($expense->vendor)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h6 class="mb-0"><i class="fas fa-user me-2"></i>Vendor Contact</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-2"><strong>{{ $expense->vendor->display_name }}</strong></p>
                        @if($expense->vendor->email)
                            <p class="small mb-1"><i class="fas fa-envelope text-muted me-2"></i>
                                <a href="mailto:{{ $expense->vendor->email }}">{{ $expense->vendor->email }}</a>
                            </p>
                        @endif
                        @if($expense->vendor->phone)
                            <p class="small mb-1"><i class="fas fa-phone text-muted me-2"></i>
                                <a href="tel:{{ $expense->vendor->phone }}">{{ $expense->vendor->phone }}</a>
                            </p>
                        @endif
                        <a href="{{ route('pnl.vendors.show', $expense->vendor) }}" class="btn btn-sm btn-outline-secondary mt-2">
                            View Full Profile
                        </a>
                    </div>
                </div>
                @endif

                <!-- Quick Actions -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h6 class="mb-0"><i class="fas fa-bolt me-2"></i>Actions</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('pnl.expenses.pdf', $expense) }}" class="btn btn-outline-danger btn-sm">
                                <i class="fas fa-file-pdf me-1"></i> Download PDF Invoice
                            </a>
                            @if($expense->vendor && $expense->vendor->email)
                            <form action="{{ route('pnl.expenses.email', $expense) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline-primary btn-sm w-100" 
                                        onclick="return confirm('Send invoice to {{ $expense->vendor->email }}?')">
                                    <i class="fas fa-envelope me-1"></i> Email to Vendor
                                </button>
                            </form>
                            @endif
                            <a href="{{ route('pnl.expenses.edit', $expense) }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-edit me-1"></i> Edit Expense
                            </a>
                            <button type="button" class="btn btn-outline-danger btn-sm" 
                                    onclick="confirmDelete('{{ route('pnl.expenses.destroy', $expense) }}')">
                                <i class="fas fa-trash me-1"></i> Delete Expense
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <h6 class="modal-title">Confirm Delete</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">Delete this expense? This cannot be undone.</div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <form id="deleteForm" method="POST" style="display:inline;">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('customjs')
    <script>
        function confirmDelete(url) {
            document.getElementById('deleteForm').action = url;
            var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        }
    </script>
@endsection
