@extends('layouts.organiser_layout')

@section('content')
    <div class="container py-4" style="max-width: 900px;">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">Edit Expense</h4>
                <p class="text-muted small mb-0">Update expense details</p>
            </div>
            <a href="{{ route('pnl.expenses.show', $expense) }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
        </div>

        <form action="{{ route('pnl.expenses.update', $expense) }}" method="POST">
            @csrf
            @method('PUT')
            
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
                                    <option value="{{ $event->id }}" {{ old('event_id', $expense->event_id) == $event->id ? 'selected' : '' }}>
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
                                    <option value="{{ $category->id }}" {{ old('category_id', $expense->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }} ({{ ucfirst($category->type) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label small">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                                   value="{{ old('title', $expense->title) }}" required placeholder="e.g., Artist Fee - DJ XYZ">
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label small">Description</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="Additional details...">{{ old('description', $expense->description) }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Vendor/Artist</label>
                            <select name="vendor_id" class="form-select">
                                <option value="">No vendor selected</option>
                                @foreach($vendors as $vendor)
                                    <option value="{{ $vendor->id }}" {{ old('vendor_id', $expense->vendor_id) == $vendor->id ? 'selected' : '' }}>
                                        {{ $vendor->display_name }} ({{ ucfirst($vendor->type) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Invoice Number</label>
                            <input type="text" name="invoice_number" class="form-control" 
                                   value="{{ old('invoice_number', $expense->invoice_number) }}" placeholder="INV-202501-001">
                            <small class="text-muted">You can edit the invoice number</small>
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
                        <div class="col-md-6">
                            <label class="form-label small">Net Amount (£) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">£</span>
                                <input type="number" step="0.01" name="amount" id="amount" class="form-control @error('amount') is-invalid @enderror" 
                                       value="{{ old('amount', $expense->amount) }}" required min="0">
                            </div>
                            @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Expense Date <span class="text-danger">*</span></label>
                            <input type="date" name="expense_date" class="form-control" value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}" required>
                        </div>
                        
                        <!-- Tax Toggle - iPhone Style -->
                        <div class="col-12">
                            <div class="d-flex align-items-center justify-content-between p-3 rounded" id="tax_toggle_container" style="background: {{ old('is_taxable', $expense->is_taxable) ? '#d1e7dd' : '#f8f9fa' }}; border: 1px solid {{ old('is_taxable', $expense->is_taxable) ? '#badbcc' : '#dee2e6' }};">
                                <div>
                                    <span class="fw-semibold">Apply VAT/Tax</span>
                                    <br><small class="text-muted" id="tax_status_text">{{ old('is_taxable', $expense->is_taxable) ? 'Tax will be added at ' . ($expense->tax_rate ?? $defaultTaxRate ?? 20) . '%' : 'No tax will be applied' }}</small>
                                </div>
                                <div class="form-check form-switch form-switch-lg mb-0">
                                    <input type="checkbox" class="form-check-input" role="switch" id="is_taxable" name="is_taxable" value="1" 
                                           {{ old('is_taxable', $expense->is_taxable) ? 'checked' : '' }} style="width: 3em; height: 1.5em; cursor: pointer;">
                                </div>
                            </div>
                        </div>

                        <!-- Tax Details (shown when toggle is ON) -->
                        <div class="col-12" id="tax_details_section" style="{{ old('is_taxable', $expense->is_taxable) ? '' : 'display: none;' }}">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label small">Tax Rate (%)</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" name="tax_rate" id="tax_rate" class="form-control" 
                                               value="{{ old('tax_rate', $expense->tax_rate ?? $defaultTaxRate ?? 20) }}" min="0" max="100">
                                        <span class="input-group-text">%</span>
                                    </div>
                                    <small class="text-muted">Default: {{ $defaultTaxRate ?? 20 }}% VAT</small>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">Tax Amount (£)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">£</span>
                                        <input type="number" step="0.01" name="tax_amount" id="tax_amount" class="form-control bg-light" 
                                               value="{{ old('tax_amount', $expense->tax_amount) }}" min="0" readonly>
                                    </div>
                                    <small class="text-muted">Auto-calculated</small>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">Total (Gross)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">£</span>
                                        <input type="text" class="form-control bg-success-subtle fw-bold text-success" id="total_display" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Non-Taxable Summary (shown when toggle is OFF) -->
                        <div class="col-12" id="non_taxable_section" style="{{ old('is_taxable', $expense->is_taxable) ? 'display: none;' : '' }}">
                            <div class="alert alert-info mb-0 d-flex align-items-center">
                                <i class="fas fa-info-circle me-2"></i>
                                <div>
                                    <strong>Non-Taxable Expense</strong> - No VAT/Tax will be applied. 
                                    Total: <strong id="non_taxable_total">£{{ number_format($expense->amount, 2) }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-danger"><i class="fas fa-save me-1"></i> Update Expense</button>
                <a href="{{ route('pnl.expenses.show', $expense) }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection

@section('customjs')
    <script>
        $(document).ready(function() {
            // Tax toggle and calculation
            function updateTaxDisplay() {
                const isTaxable = $('#is_taxable').is(':checked');
                const amount = parseFloat($('#amount').val()) || 0;
                const taxRate = parseFloat($('#tax_rate').val()) || 0;
                
                if (isTaxable) {
                    // Show tax section, hide non-taxable message
                    $('#tax_details_section').show();
                    $('#non_taxable_section').hide();
                    $('#tax_toggle_container').css('background', '#d1e7dd').css('border-color', '#badbcc');
                    $('#tax_status_text').text('Tax will be added at ' + taxRate + '%');
                    
                    // Calculate tax
                    const taxAmount = (amount * taxRate / 100);
                    $('#tax_amount').val(taxAmount.toFixed(2));
                    $('#total_display').val((amount + taxAmount).toFixed(2));
                } else {
                    // Hide tax section, show non-taxable message
                    $('#tax_details_section').hide();
                    $('#non_taxable_section').show();
                    $('#tax_toggle_container').css('background', '#f8f9fa').css('border-color', '#dee2e6');
                    $('#tax_status_text').text('No tax will be applied');
                    
                    // No tax
                    $('#tax_amount').val('0.00');
                    $('#non_taxable_total').text('£' + amount.toFixed(2));
                }
            }

            $('#amount, #tax_rate').on('input', updateTaxDisplay);
            $('#is_taxable').on('change', updateTaxDisplay);
            
            // Initial state
            updateTaxDisplay();
        });
    </script>
@endsection
