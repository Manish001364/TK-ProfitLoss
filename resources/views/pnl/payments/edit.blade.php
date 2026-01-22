@extends('pnl.layouts.app')

@section('pnl_content')
    <div class="container-fluid" style="max-width: 900px;">
        <!-- Page Header -->
        <div class="mb-4">
            <h4 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Payment</h4>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-danger text-white border-0 py-3">
                        <h6 class="mb-0"><i class="fas fa-credit-card me-2"></i>Payment Details</h6>
                    </div>
                    <form action="{{ route('pnl.payments.update', $payment) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="alert alert-light border">
                                <strong>Expense:</strong> {{ $payment->expense->title ?? 'Unknown' }}<br>
                                <strong>Amount:</strong> £{{ number_format($payment->amount, 2) }}
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Status <span class="text-danger">*</span></label>
                                        <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                                            @foreach($statuses as $key => $label)
                                                <option value="{{ $key }}" {{ old('status', $payment->status) === $key ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Payment Method</label>
                                        <select name="payment_method" class="form-control @error('payment_method') is-invalid @enderror">
                                            <option value="">Not Specified</option>
                                            @foreach($paymentMethods as $key => $label)
                                                <option value="{{ $key }}" {{ old('payment_method', $payment->payment_method) === $key ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('payment_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Scheduled Date</label>
                                        <input type="date" name="scheduled_date" class="form-control @error('scheduled_date') is-invalid @enderror" 
                                               value="{{ old('scheduled_date', $payment->scheduled_date?->format('Y-m-d')) }}">
                                        @error('scheduled_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Actual Paid Date</label>
                                        <input type="date" name="actual_paid_date" class="form-control @error('actual_paid_date') is-invalid @enderror" 
                                               value="{{ old('actual_paid_date', $payment->actual_paid_date?->format('Y-m-d')) }}">
                                        @error('actual_paid_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label>Transaction Reference</label>
                                <input type="text" name="transaction_reference" class="form-control @error('transaction_reference') is-invalid @enderror" 
                                       value="{{ old('transaction_reference', $payment->transaction_reference) }}">
                                @error('transaction_reference')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="form-group mb-3">
                                <label>Internal Notes</label>
                                <textarea name="internal_notes" class="form-control @error('internal_notes') is-invalid @enderror" rows="3">{{ old('internal_notes', $payment->internal_notes) }}</textarea>
                                @error('internal_notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <hr>
                            <h5>Reminder Settings</h5>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input" id="reminder_enabled" name="reminder_enabled" value="1" 
                                                   {{ old('reminder_enabled', $payment->reminder_enabled) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="reminder_enabled">Enable Reminders</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label>Remind Before (Days)</label>
                                        <input type="number" name="reminder_days_before" class="form-control" 
                                               value="{{ old('reminder_days_before', $payment->reminder_days_before) }}" min="1" max="30">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <div class="form-check form-switch mt-4">
                                            <input type="checkbox" class="form-check-input" id="reminder_on_due_date" name="reminder_on_due_date" value="1" 
                                                   {{ old('reminder_on_due_date', $payment->reminder_on_due_date) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="reminder_on_due_date">Remind on Due Date</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Email Notification Settings (shown when status changes to Paid) -->
                            <div id="emailNotificationSection" class="mt-3" style="display: none;">
                                <hr>
                                <h5><i class="fas fa-envelope text-success me-2"></i>Payment Confirmation Emails</h5>
                                <p class="text-muted small">When marking as paid, send confirmation emails to:</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="send_vendor_email" name="send_vendor_email" value="1" checked>
                                            <label class="form-check-label" for="send_vendor_email">
                                                <i class="fas fa-user-tie text-primary me-1"></i> Vendor
                                                @if($payment->vendor && $payment->vendor->email)
                                                    <br><small class="text-muted">{{ $payment->vendor->email }}</small>
                                                @else
                                                    <br><small class="text-danger">No email on file</small>
                                                @endif
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="send_organiser_email" name="send_organiser_email" value="1" checked>
                                            <label class="form-check-label" for="send_organiser_email">
                                                <i class="fas fa-user text-success me-1"></i> Yourself (Organiser)
                                                <br><small class="text-muted">{{ auth()->user()->email ?? 'Your email' }}</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Update Payment
                            </button>
                            <a href="{{ route('pnl.payments.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">Vendor Details</h5>
                    </div>
                    <div class="card-body">
                        @if($payment->vendor)
                            <p><strong>{{ $payment->vendor->display_name }}</strong></p>
                            <p><i class="fas fa-envelope"></i> {{ $payment->vendor->email }}</p>
                            @if($payment->vendor->phone)
                                <p><i class="fas fa-phone"></i> {{ $payment->vendor->phone }}</p>
                            @endif
                        @else
                            <p class="text-muted">No vendor assigned</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('customjs')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const statusSelect = document.querySelector('select[name="status"]');
        const emailSection = document.getElementById('emailNotificationSection');
        
        function toggleEmailSection() {
            if (statusSelect.value === 'paid') {
                emailSection.style.display = 'block';
            } else {
                emailSection.style.display = 'none';
            }
        }
        
        // Initial check
        toggleEmailSection();
        
        // On change
        statusSelect.addEventListener('change', toggleEmailSection);
    });
</script>
@endsection
