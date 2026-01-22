@extends('pnl.layouts.app')

@section('pnl_content')
    <div class="container-fluid" style="max-width: 1100px;">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Upcoming Payments</h4>
            <a href="{{ route('pnl.payments.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back to All Payments
            </a>
        </div>

        <!-- Summary Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #dc3545 !important;">
                    <div class="card-body py-3">
                        <p class="text-muted small mb-1">Next 7 Days</p>
                        <h4 class="mb-0 text-danger">£{{ number_format($summary['next_7_days'], 0) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #ffc107 !important;">
                    <div class="card-body py-3">
                        <p class="text-muted small mb-1">Next 14 Days</p>
                        <h4 class="mb-0 text-warning">£{{ number_format($summary['next_14_days'], 0) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #17a2b8 !important;">
                    <div class="card-body py-3">
                        <p class="text-muted small mb-1">Next 30 Days</p>
                        <h4 class="mb-0 text-info">£{{ number_format($summary['next_30_days'], 0) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        @foreach(['next_7_days' => ['Next 7 Days', 'danger'], 'next_14_days' => ['8-14 Days', 'warning'], 'next_30_days' => ['15-30 Days', 'info']] as $key => $config)
            @if($payments->has($key) && $payments->get($key)->count() > 0)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-{{ $config[1] }}-subtle border-0 py-3 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 text-{{ $config[1] }}"><i class="fas fa-calendar me-1"></i> {{ $config[0] }}</h6>
                        <span class="badge bg-{{ $config[1] }}">£{{ number_format($payments->get($key)->sum('amount'), 0) }}</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="border-0">Event / Expense</th>
                                        <th class="border-0">Vendor</th>
                                        <th class="border-0 text-end">Amount</th>
                                        <th class="border-0">Due Date</th>
                                        <th class="border-0" width="120">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payments->get($key) as $payment)
                                        <tr>
                                            <td class="border-0">
                                                <strong>{{ $payment->expense?->event?->name ?? 'Unknown Event' }}</strong><br>
                                                <small class="text-muted">{{ $payment->expense?->title ?? 'No expense linked' }}</small>
                                            </td>
                                            <td class="border-0 small">{{ $payment->vendor?->display_name ?? 'Not assigned' }}</td>
                                            <td class="border-0 text-end"><strong>£{{ number_format($payment->amount, 0) }}</strong></td>
                                            <td class="border-0 small">
                                                {{ $payment->scheduled_date ? $payment->scheduled_date->format('d M Y') : 'Not set' }}
                                                @if($payment->days_until_due !== null)
                                                    <br><small class="text-{{ $payment->days_until_due <= 3 ? 'danger' : 'muted' }}">{{ $payment->days_until_due }} days</small>
                                                @endif
                                            </td>
                                            <td class="border-0">
                                                <button type="button" class="btn btn-sm btn-success" 
                                                        onclick="openPaymentModal('{{ $payment->id }}', '{{ $payment->expense?->title ?? 'Payment' }}', '{{ number_format($payment->amount, 2) }}', '{{ $payment->vendor?->email ?? '' }}', '{{ $payment->vendor?->display_name ?? 'Vendor' }}')"
                                                        title="Mark Paid">
                                                    <i class="fas fa-check"></i> Pay
                                                </button>
                                                <a href="{{ route('pnl.payments.edit', $payment) }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-edit"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach

        @if($payments->flatten()->count() === 0)
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="fas fa-calendar-check fa-3x text-success mb-3"></i>
                    <h5 class="text-muted">No upcoming payments in the next 30 days!</h5>
                </div>
            </div>
        @endif
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
