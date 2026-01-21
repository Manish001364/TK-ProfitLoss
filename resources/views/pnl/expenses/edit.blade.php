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
                            <small class="text-muted">You can edit the auto-generated number</small>
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
                                       value="{{ old('amount', $expense->amount) }}" required min="0">
                            </div>
                            @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small d-flex justify-content-between align-items-center">
                                <span>VAT/Tax</span>
                                <div class="form-check form-switch mb-0">
                                    <input type="checkbox" class="form-check-input" id="is_taxable" name="is_taxable" value="1" 
                                           {{ old('is_taxable', $expense->is_taxable) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="is_taxable">Taxable</label>
                                </div>
                            </label>
                            <div class="input-group" id="tax_input_group">
                                <input type="number" step="0.01" name="tax_rate" id="tax_rate" class="form-control" 
                                       value="{{ old('tax_rate', $expense->tax_rate ?? $defaultTaxRate ?? 20) }}" min="0" max="100" style="max-width: 80px;">
                                <span class="input-group-text">%</span>
                                <span class="input-group-text">=</span>
                                <span class="input-group-text">£</span>
                                <input type="number" step="0.01" name="tax_amount" id="tax_amount" class="form-control" 
                                       value="{{ old('tax_amount', $expense->tax_amount) }}" min="0" readonly>
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
                            <input type="date" name="expense_date" class="form-control" value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}" required>
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
        });
    </script>
@endsection
