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
                            <div class="input-group">
                                <select name="vendor_id" class="form-select" id="vendor_select">
                                    <option value="">Select vendor or add new</option>
                                    @foreach($vendors as $vendor)
                                        <option value="{{ $vendor->id }}" {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                            {{ $vendor->display_name }} ({{ ucfirst($vendor->type) }})
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#addVendorModal" title="Add New Vendor">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Invoice Number</label>
                            <div class="input-group">
                                <input type="text" name="invoice_number" id="invoice_number" class="form-control" 
                                       value="{{ old('invoice_number', $nextInvoiceNumber) }}" placeholder="INV-00001">
                                <button type="button" class="btn btn-outline-secondary" onclick="generateInvoiceNumber()" title="Generate New">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </div>
                            <small class="text-muted">Auto-generated. You can edit if needed.</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Amount & Tax -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0"><i class="fas fa-pound-sign me-2 text-success"></i>Amount & Tax</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small">Net Amount (£) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">£</span>
                                <input type="number" step="0.01" name="amount" id="amount" class="form-control @error('amount') is-invalid @enderror" 
                                       value="{{ old('amount', 0) }}" required min="0">
                            </div>
                            @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small d-flex justify-content-between align-items-center">
                                <span>VAT/Tax</span>
                                <div class="form-check form-switch mb-0">
                                    <input type="checkbox" class="form-check-input" id="is_taxable" name="is_taxable" value="1" 
                                           {{ old('is_taxable', true) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="is_taxable">Taxable</label>
                                </div>
                            </label>
                            <div class="input-group" id="tax_input_group">
                                <input type="number" step="0.01" name="tax_rate" id="tax_rate" class="form-control" 
                                       value="{{ old('tax_rate', $defaultTaxRate ?? 20) }}" min="0" max="100" style="max-width: 80px;">
                                <span class="input-group-text">%</span>
                                <span class="input-group-text">=</span>
                                <span class="input-group-text">£</span>
                                <input type="number" step="0.01" name="tax_amount" id="tax_amount" class="form-control" 
                                       value="{{ old('tax_amount', 0) }}" min="0" readonly>
                            </div>
                            <small class="text-muted">Default: {{ $defaultTaxRate ?? 20 }}% VAT</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Total Amount (Gross)</label>
                            <div class="input-group">
                                <span class="input-group-text">£</span>
                                <input type="text" class="form-control bg-light fw-bold" id="total_display" readonly>
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
                    </div>
                </div>
            </div>

            <!-- Notification Settings -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0"><i class="fas fa-bell me-2 text-warning"></i>Notifications</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="send_email_to_vendor" name="send_email_to_vendor" value="1" 
                                       {{ old('send_email_to_vendor', true) ? 'checked' : '' }}>
                                <label class="form-check-label small" for="send_email_to_vendor">
                                    <i class="fas fa-envelope text-muted me-1"></i>Send email notifications to vendor
                                </label>
                            </div>
                            <small class="text-muted ms-4">Vendor will be notified when payment status changes</small>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="reminder_enabled" name="reminder_enabled" value="1" 
                                       {{ old('reminder_enabled', true) ? 'checked' : '' }}>
                                <label class="form-check-label small" for="reminder_enabled">Enable payment reminders</label>
                            </div>
                            <div class="mt-2" id="reminder_days_group">
                                <label class="form-label small">Remind before (days)</label>
                                <input type="number" name="reminder_days_before" class="form-control form-control-sm" 
                                       value="{{ old('reminder_days_before', 3) }}" min="1" max="30" style="width: 80px;">
                            </div>
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

    <!-- Add Vendor Modal -->
    <div class="modal fade" id="addVendorModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Quick Add Vendor/Artist</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="quickVendorForm">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small">Name <span class="text-danger">*</span></label>
                                <input type="text" name="vendor_name" id="vendor_name" class="form-control" required placeholder="Vendor/Artist name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">Type</label>
                                <select name="vendor_type" id="vendor_type" class="form-select">
                                    <option value="artist">Artist</option>
                                    <option value="dj">DJ</option>
                                    <option value="vendor">Vendor</option>
                                    <option value="caterer">Caterer</option>
                                    <option value="security">Security</option>
                                    <option value="equipment">Equipment</option>
                                    <option value="venue">Venue</option>
                                    <option value="marketing">Marketing</option>
                                    <option value="staff">Staff</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">Email</label>
                                <input type="email" name="vendor_email" id="vendor_email" class="form-control" placeholder="email@example.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">Phone</label>
                                <input type="text" name="vendor_phone" id="vendor_phone" class="form-control" placeholder="+44 7xxx xxx xxx">
                            </div>
                            <div class="col-12">
                                <label class="form-label small">Service Area / Specialization</label>
                                <input type="text" name="vendor_specialization" id="vendor_specialization" class="form-control" placeholder="e.g., Bollywood DJ, Continental Food">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success btn-sm" onclick="saveQuickVendor()">
                        <i class="fas fa-save me-1"></i> Save & Select
                    </button>
                    <a href="{{ route('pnl.vendors.create') }}" target="_blank" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-external-link-alt me-1"></i> Full Form
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('customjs')
    <script>
        $(document).ready(function() {
            // Tax calculation
            function updateTax() {
                const isTaxable = $('#is_taxable').is(':checked');
                const amount = parseFloat($('#amount').val()) || 0;
                const taxRate = parseFloat($('#tax_rate').val()) || 0;
                
                if (isTaxable) {
                    const taxAmount = (amount * taxRate / 100);
                    $('#tax_amount').val(taxAmount.toFixed(2));
                    $('#total_display').val((amount + taxAmount).toFixed(2));
                    $('#tax_input_group').removeClass('opacity-50');
                    $('#tax_rate, #tax_amount').prop('disabled', false);
                } else {
                    $('#tax_amount').val('0.00');
                    $('#total_display').val(amount.toFixed(2));
                    $('#tax_input_group').addClass('opacity-50');
                    $('#tax_rate, #tax_amount').prop('disabled', true);
                }
            }

            $('#amount, #tax_rate').on('input', updateTax);
            $('#is_taxable').on('change', updateTax);
            updateTax();

            // Payment status toggle
            $('#payment_status').on('change', function() {
                $('#scheduled_date_group').toggle($(this).val() === 'scheduled');
            }).trigger('change');

            // Reminder toggle
            $('#reminder_enabled').on('change', function() {
                $('#reminder_days_group').toggle(this.checked);
            }).trigger('change');
        });

        // Generate invoice number
        function generateInvoiceNumber() {
            const timestamp = Date.now().toString().slice(-6);
            const random = Math.floor(Math.random() * 100).toString().padStart(2, '0');
            $('#invoice_number').val('INV-' + timestamp + random);
        }

        // Quick add vendor
        function saveQuickVendor() {
            const name = $('#vendor_name').val();
            if (!name) {
                alert('Please enter vendor name');
                return;
            }

            $.ajax({
                url: '{{ route("pnl.vendors.store") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    full_name: name,
                    type: $('#vendor_type').val(),
                    email: $('#vendor_email').val(),
                    phone: $('#vendor_phone').val(),
                    specialization: $('#vendor_specialization').val(),
                    is_active: 1,
                    _quick_add: 1
                },
                success: function(response) {
                    if (response.success && response.vendor) {
                        // Add to dropdown and select
                        const option = new Option(
                            response.vendor.display_name + ' (' + response.vendor.type.charAt(0).toUpperCase() + response.vendor.type.slice(1) + ')',
                            response.vendor.id,
                            true,
                            true
                        );
                        $('#vendor_select').append(option).trigger('change');
                        
                        // Close modal and reset form
                        $('#addVendorModal').modal('hide');
                        $('#quickVendorForm')[0].reset();
                        
                        // Show success message
                        alert('Vendor "' + response.vendor.display_name + '" added successfully!');
                    } else {
                        alert('Error: ' + (response.message || 'Could not save vendor'));
                    }
                },
                error: function(xhr) {
                    alert('Error saving vendor. Please try again.');
                }
            });
        }
    </script>
@endsection
