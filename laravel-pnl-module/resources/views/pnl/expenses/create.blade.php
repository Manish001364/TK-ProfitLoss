@extends('adminlte::page')

@section('title', 'Add Expense')

@section('content_header')
    <h1><i class="fas fa-plus-circle"></i> Add Expense</h1>
@stop

@section('content')
    <form action="{{ route('pnl.expenses.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-8">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Expense Details</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Event <span class="text-danger">*</span></label>
                                    <select name="event_id" class="form-control select2 @error('event_id') is-invalid @enderror" required>
                                        <option value="">Select Event</option>
                                        @foreach($events as $event)
                                            <option value="{{ $event->id }}" {{ old('event_id', $selectedEventId) == $event->id ? 'selected' : '' }}>
                                                {{ $event->name }} ({{ $event->event_date->format('d M Y') }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('event_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Category <span class="text-danger">*</span></label>
                                    <select name="category_id" class="form-control @error('category_id') is-invalid @enderror" required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }} ({{ ucfirst($category->type) }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                                   value="{{ old('title') }}" required placeholder="e.g., Artist Fee - DJ XYZ">
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="2">{{ old('description') }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Vendor/Artist</label>
                                    <select name="vendor_id" class="form-control select2 @error('vendor_id') is-invalid @enderror">
                                        <option value="">Select Vendor (Optional)</option>
                                        @foreach($vendors as $vendor)
                                            <option value="{{ $vendor->id }}" {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                                {{ $vendor->display_name }} ({{ ucfirst($vendor->type) }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('vendor_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Invoice Number</label>
                                    <input type="text" name="invoice_number" class="form-control @error('invoice_number') is-invalid @enderror" 
                                           value="{{ old('invoice_number') }}">
                                    @error('invoice_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Amount (₹) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" name="amount" class="form-control @error('amount') is-invalid @enderror" 
                                           value="{{ old('amount', 0) }}" required min="0" id="amount">
                                    @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Tax Amount (₹)</label>
                                    <input type="number" step="0.01" name="tax_amount" class="form-control @error('tax_amount') is-invalid @enderror" 
                                           value="{{ old('tax_amount', 0) }}" min="0" id="tax_amount">
                                    @error('tax_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Total Amount</label>
                                    <input type="text" class="form-control" id="total_display" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Expense Date <span class="text-danger">*</span></label>
                            <input type="date" name="expense_date" class="form-control @error('expense_date') is-invalid @enderror" 
                                   value="{{ old('expense_date', date('Y-m-d')) }}" required>
                            @error('expense_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Payment Settings -->
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-credit-card"></i> Payment Settings</h3>
                    </div>
                    <div class="card-body">
                        <input type="hidden" name="create_payment" value="1">
                        
                        <div class="form-group">
                            <label>Payment Status</label>
                            <select name="payment_status" class="form-control" id="payment_status">
                                <option value="pending" {{ old('payment_status', 'pending') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="scheduled" {{ old('payment_status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                <option value="paid" {{ old('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
                            </select>
                        </div>

                        <div class="form-group" id="scheduled_date_group">
                            <label>Scheduled Payment Date</label>
                            <input type="date" name="scheduled_date" class="form-control" value="{{ old('scheduled_date') }}">
                        </div>

                        <div class="form-group">
                            <label>Payment Method</label>
                            <select name="payment_method" class="form-control">
                                <option value="">Not Specified</option>
                                <option value="bank_transfer" {{ old('payment_method') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="cash" {{ old('payment_method') === 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="cheque" {{ old('payment_method') === 'cheque' ? 'selected' : '' }}>Cheque</option>
                                <option value="upi" {{ old('payment_method') === 'upi' ? 'selected' : '' }}>UPI</option>
                                <option value="other" {{ old('payment_method') === 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>

                        <hr>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="reminder_enabled" name="reminder_enabled" value="1" {{ old('reminder_enabled', true) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="reminder_enabled">Enable Payment Reminders</label>
                            </div>
                        </div>

                        <div class="form-group" id="reminder_days_group">
                            <label>Remind Before (Days)</label>
                            <input type="number" name="reminder_days_before" class="form-control" value="{{ old('reminder_days_before', 3) }}" min="1" max="30">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-footer">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save"></i> Create Expense
                </button>
                <a href="{{ route('pnl.expenses.index') }}" class="btn btn-secondary btn-lg">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </div>
    </form>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap4-theme@1.0.0/dist/select2-bootstrap4.min.css">
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({ theme: 'bootstrap4' });

            // Calculate total
            function updateTotal() {
                const amount = parseFloat($('#amount').val()) || 0;
                const tax = parseFloat($('#tax_amount').val()) || 0;
                $('#total_display').val('₹' + (amount + tax).toLocaleString('en-IN', {minimumFractionDigits: 2}));
            }

            $('#amount, #tax_amount').on('input', updateTotal);
            updateTotal();

            // Toggle scheduled date
            $('#payment_status').on('change', function() {
                if ($(this).val() === 'scheduled') {
                    $('#scheduled_date_group').show();
                } else {
                    $('#scheduled_date_group').hide();
                }
            }).trigger('change');

            // Toggle reminder settings
            $('#reminder_enabled').on('change', function() {
                $('#reminder_days_group').toggle(this.checked);
            }).trigger('change');
        });
    </script>
@stop
