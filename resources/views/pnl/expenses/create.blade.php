@extends('layouts.organiser_layout')

@section('content')
    <div class="container py-4" style="max-width: 900px;">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">Add Expense</h4>
                <p class="text-muted small mb-0">Record a new expense for an event</p>
            </div>
            <a href="{{ route('pnl.expenses.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
        </div>

        <form action="{{ route('pnl.expenses.store') }}" method="POST">
            @csrf
            
            <!-- Expense Details -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0"><i class="fas fa-receipt me-2 text-danger"></i>Expense Details</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small">Event <span class="text-danger">*</span></label>
                            <select name="event_id" class="form-select @error('event_id') is-invalid @enderror" required>
                                <option value="">Select Event</option>
                                @foreach($events as $event)
                                    <option value="{{ $event->id }}" {{ old('event_id', $selectedEventId) == $event->id ? 'selected' : '' }}>
                                        {{ $event->name }} ({{ $event->event_date->format('d M Y') }})
                                    </option>
                                @endforeach
                            </select>
                            @error('event_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Category <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }} ({{ ucfirst($category->type) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label small">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                                   value="{{ old('title') }}" required placeholder="e.g., Artist Fee - DJ XYZ">
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label small">Description</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="Additional details...">{{ old('description') }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Vendor/Artist</label>
                            <select name="vendor_id" class="form-select">
                                <option value="">Select or add later</option>
                                @foreach($vendors as $vendor)
                                    <option value="{{ $vendor->id }}" {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                        {{ $vendor->display_name }} ({{ ucfirst($vendor->type) }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">
                                <a href="{{ route('pnl.vendors.create') }}" target="_blank">+ Add new vendor</a>
                            </small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Invoice Number</label>
                            <input type="text" name="invoice_number" class="form-control" value="{{ old('invoice_number') }}" placeholder="INV-001">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Amount -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0"><i class="fas fa-pound-sign me-2 text-success"></i>Amount</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small">Amount (£) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">£</span>
                                <input type="number" step="0.01" name="amount" id="amount" class="form-control @error('amount') is-invalid @enderror" 
                                       value="{{ old('amount', 0) }}" required min="0">
                            </div>
                            @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Tax/VAT (£)</label>
                            <div class="input-group">
                                <span class="input-group-text">£</span>
                                <input type="number" step="0.01" name="tax_amount" id="tax_amount" class="form-control" 
                                       value="{{ old('tax_amount', 0) }}" min="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Total Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">£</span>
                                <input type="text" class="form-control bg-light" id="total_display" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Expense Date <span class="text-danger">*</span></label>
                            <input type="date" name="expense_date" class="form-control" value="{{ old('expense_date', date('Y-m-d')) }}" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Settings -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0"><i class="fas fa-credit-card me-2 text-info"></i>Payment Settings</h6>
                </div>
                <div class="card-body">
                    <input type="hidden" name="create_payment" value="1">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small">Payment Status</label>
                            <select name="payment_status" class="form-select" id="payment_status">
                                <option value="pending" {{ old('payment_status', 'pending') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="scheduled" {{ old('payment_status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                <option value="paid" {{ old('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
                            </select>
                        </div>
                        <div class="col-md-4" id="scheduled_date_group">
                            <label class="form-label small">Scheduled Date</label>
                            <input type="date" name="scheduled_date" class="form-control" value="{{ old('scheduled_date') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Payment Method</label>
                            <select name="payment_method" class="form-select">
                                <option value="">Not Specified</option>
                                <option value="bank_transfer" {{ old('payment_method') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer (BACS)</option>
                                <option value="cash" {{ old('payment_method') === 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="cheque" {{ old('payment_method') === 'cheque' ? 'selected' : '' }}>Cheque</option>
                                <option value="card" {{ old('payment_method') === 'card' ? 'selected' : '' }}>Card</option>
                                <option value="other" {{ old('payment_method') === 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="reminder_enabled" name="reminder_enabled" value="1" {{ old('reminder_enabled', true) ? 'checked' : '' }}>
                                <label class="form-check-label small" for="reminder_enabled">Enable Payment Reminders</label>
                            </div>
                        </div>
                        <div class="col-md-6" id="reminder_days_group">
                            <label class="form-label small">Remind Before (Days)</label>
                            <input type="number" name="reminder_days_before" class="form-control form-control-sm" value="{{ old('reminder_days_before', 3) }}" min="1" max="30" style="width: 80px;">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-danger"><i class="fas fa-save me-1"></i> Create Expense</button>
                <a href="{{ route('pnl.expenses.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection

@section('customjs')
    <script>
        $(document).ready(function() {
            function updateTotal() {
                const amount = parseFloat($('#amount').val()) || 0;
                const tax = parseFloat($('#tax_amount').val()) || 0;
                $('#total_display').val((amount + tax).toFixed(2));
            }
            $('#amount, #tax_amount').on('input', updateTotal);
            updateTotal();

            $('#payment_status').on('change', function() {
                $('#scheduled_date_group').toggle($(this).val() === 'scheduled');
            }).trigger('change');

            $('#reminder_enabled').on('change', function() {
                $('#reminder_days_group').toggle(this.checked);
            }).trigger('change');
        });
    </script>
@endsection
