@extends('pnl.layouts.app')

@section('pnl_content')
    <div class="container-fluid" style="max-width: 1100px;">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Payment Tracking</h4>
            <div class="d-flex gap-2">
                <a href="{{ route('pnl.payments.upcoming') }}" class="btn btn-info btn-sm">
                    <i class="fas fa-calendar"></i> Upcoming
                </a>
                <a href="{{ route('pnl.payments.overdue') }}" class="btn btn-danger btn-sm">
                    <i class="fas fa-exclamation-triangle"></i> Overdue
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
                <form method="GET" action="{{ route('pnl.payments.index') }}" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small text-muted mb-1">Status</label>
                        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">All Status</option>
                            @foreach($statuses as $key => $label)
                                <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="overdue" name="overdue" value="1" {{ request('overdue') ? 'checked' : '' }} onchange="this.form.submit()">
                            <label class="form-check-label small" for="overdue">Show Only Overdue</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('pnl.payments.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-times"></i> Clear</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Payments Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">Event / Expense</th>
                                <th class="border-0">Vendor</th>
                                <th class="border-0 text-end">Amount</th>
                                <th class="border-0">Status</th>
                                <th class="border-0">Scheduled</th>
                                <th class="border-0">Paid Date</th>
                                <th class="border-0" width="150">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $payment)
                                <tr class="{{ $payment->is_overdue ? 'table-danger' : '' }}">
                                    <td class="border-0">
                                        <strong><a href="{{ route('pnl.events.show', $payment->expense->event) }}">{{ $payment->expense->event->name }}</a></strong>
                                        <br><small class="text-muted"><a href="{{ route('pnl.expenses.show', $payment->expense) }}">{{ $payment->expense->title }}</a></small>
                                    </td>
                                    <td class="border-0 small">
                                        @if($payment->vendor)
                                            <a href="{{ route('pnl.vendors.show', $payment->vendor) }}">{{ $payment->vendor->display_name }}</a>
                                        @else
                                            <span class="text-muted">Not assigned</span>
                                        @endif
                                    </td>
                                    <td class="border-0 text-end"><strong>£{{ number_format($payment->amount, 0) }}</strong></td>
                                    <td class="border-0">
                                        <span class="badge bg-{{ $payment->status_color }}-subtle text-{{ $payment->status_color }}">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                        @if($payment->is_overdue)
                                            <span class="badge bg-danger">Overdue</span>
                                        @endif
                                    </td>
                                    <td class="border-0 small">
                                        @if($payment->scheduled_date)
                                            {{ $payment->scheduled_date->format('d M Y') }}
                                            @if($payment->days_until_due !== null && $payment->status !== 'paid')
                                                <br><small class="{{ $payment->days_until_due < 0 ? 'text-danger' : ($payment->days_until_due <= 7 ? 'text-warning' : 'text-muted') }}">
                                                    {{ $payment->days_until_due < 0 ? abs($payment->days_until_due) . ' days overdue' : $payment->days_until_due . ' days left' }}
                                                </small>
                                            @endif
                                        @else
                                            <span class="text-muted">Not set</span>
                                        @endif
                                    </td>
                                    <td class="border-0 small">{{ $payment->actual_paid_date?->format('d M Y') ?? '-' }}</td>
                                    <td class="border-0">
                                        <div class="btn-group btn-group-sm">
                                            @if($payment->status !== 'paid')
                                                <button type="button" class="btn btn-success" 
                                                        onclick="openPaymentModal('{{ $payment->id }}', '{{ $payment->expense?->title ?? 'Payment' }}', '{{ number_format($payment->amount, 2) }}', '{{ $payment->vendor?->email ?? '' }}', '{{ $payment->vendor?->display_name ?? 'Vendor' }}')"
                                                        title="Mark Paid">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            @endif
                                            <a href="{{ route('pnl.payments.edit', $payment) }}" class="btn btn-outline-secondary" title="Edit"><i class="fas fa-edit"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 border-0">
                                        <i class="fas fa-credit-card fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">No payments found</h5>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($payments->hasPages())
                <div class="card-footer bg-white border-0">{{ $payments->links() }}</div>
            @endif
        </div>
    </div>

    <!-- Mark as Paid Modal -->
    <div class="modal fade" id="markPaidModal" tabindex="-1" aria-labelledby="markPaidModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="markPaidModalLabel"><i class="fas fa-check-circle me-2"></i>Mark Payment as Paid</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="markPaidForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-light border mb-3">
                            <strong id="modalExpenseTitle">Payment</strong><br>
                            <span class="text-success fw-bold" id="modalAmount">£0.00</span>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small">Payment Date</label>
                                <input type="date" name="actual_paid_date" class="form-control" value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">Payment Method</label>
                                <select name="payment_method" class="form-control">
                                    <option value="">Not Specified</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="cash">Cash</option>
                                    <option value="cheque">Cheque</option>
                                    <option value="card">Card</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small">Transaction Reference (optional)</label>
                            <input type="text" name="transaction_reference" class="form-control" placeholder="e.g. REF-12345">
                        </div>

                        <hr>
                        <h6><i class="fas fa-envelope text-primary me-2"></i>Send Confirmation Emails</h6>
                        
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="modal_send_vendor_email" name="send_vendor_email" value="1" checked>
                                    <label class="form-check-label" for="modal_send_vendor_email">
                                        <i class="fas fa-user-tie text-primary me-1"></i> Vendor
                                        <br><small class="text-muted" id="modalVendorEmail">No email</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="modal_send_organiser_email" name="send_organiser_email" value="1" checked>
                                    <label class="form-check-label" for="modal_send_organiser_email">
                                        <i class="fas fa-user text-success me-1"></i> Yourself
                                        <br><small class="text-muted">{{ auth()->user()->email ?? 'Your email' }}</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success"><i class="fas fa-check me-1"></i> Mark as Paid & Send Emails</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('customjs')
<script>
    function openPaymentModal(paymentId, expenseTitle, amount, vendorEmail, vendorName) {
        document.getElementById('markPaidForm').action = '/pnl/payments/' + paymentId + '/mark-paid';
        document.getElementById('modalExpenseTitle').textContent = expenseTitle;
        document.getElementById('modalAmount').textContent = '£' + amount;
        
        const vendorEmailEl = document.getElementById('modalVendorEmail');
        const vendorCheckbox = document.getElementById('modal_send_vendor_email');
        
        if (vendorEmail) {
            vendorEmailEl.textContent = vendorEmail;
            vendorCheckbox.disabled = false;
            vendorCheckbox.checked = true;
        } else {
            vendorEmailEl.textContent = 'No email on file';
            vendorCheckbox.disabled = true;
            vendorCheckbox.checked = false;
        }
        
        var modal = new bootstrap.Modal(document.getElementById('markPaidModal'));
        modal.show();
    }
</script>
@endsection
