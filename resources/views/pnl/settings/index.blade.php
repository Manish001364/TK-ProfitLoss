@extends('pnl.layouts.app')

@section('pnl_content')
    <div class="container-fluid" style="max-width: 900px;">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">P&L Settings</h4>
                <p class="text-muted small mb-0">Configure your default VAT, invoice settings, and notifications</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('pnl.settings.update') }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Tax Settings -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0"><i class="fas fa-percent me-2 text-primary"></i>Tax / VAT Settings</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small">Default Tax Rate (%) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" step="0.01" name="default_tax_rate" class="form-control @error('default_tax_rate') is-invalid @enderror" 
                                       value="{{ old('default_tax_rate', $settings->default_tax_rate) }}" min="0" max="100" required>
                                <span class="input-group-text">%</span>
                            </div>
                            <small class="text-muted">This rate will be pre-filled when creating new expenses. UK standard VAT is 20%.</small>
                            @error('default_tax_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Company VAT Number</label>
                            <input type="text" name="company_vat_number" class="form-control" 
                                   value="{{ old('company_vat_number', $settings->company_vat_number) }}" placeholder="GB 123 4567 89">
                            <small class="text-muted">Appears on invoices</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoice Settings -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0"><i class="fas fa-file-invoice me-2 text-success"></i>Invoice Settings</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small">Invoice Prefix <span class="text-danger">*</span></label>
                            <input type="text" name="invoice_prefix" class="form-control @error('invoice_prefix') is-invalid @enderror" 
                                   value="{{ old('invoice_prefix', $settings->invoice_prefix) }}" maxlength="10" required placeholder="INV">
                            <small class="text-muted">Letters/numbers only (e.g., INV, TK, BILL)</small>
                            @error('invoice_prefix')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Next Invoice Number</label>
                            <input type="text" class="form-control bg-light" value="{{ $settings->invoice_next_number }}" disabled>
                            <small class="text-muted">Auto-incremented</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Invoice Format Preview</label>
                            <input type="text" class="form-control bg-light fw-bold" 
                                   value="{{ $settings->invoice_prefix }}-{{ now()->format('Ym') }}-{{ str_pad($settings->invoice_next_number, 3, '0', STR_PAD_LEFT) }}" disabled>
                            <small class="text-muted">Example: INV-202501-001</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Company Details for Invoices -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0"><i class="fas fa-building me-2 text-info"></i>Company Details (for Invoices)</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small">Company Name</label>
                            <input type="text" name="company_name" class="form-control" 
                                   value="{{ old('company_name', $settings->company_name) }}" placeholder="Your Company Ltd">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Company Address</label>
                            <textarea name="company_address" class="form-control" rows="2" placeholder="123 Business Street, London, UK">{{ old('company_address', $settings->company_address) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Email Notification Settings -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0"><i class="fas fa-envelope me-2 text-warning"></i>Email Notifications to Vendors</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">When should we automatically email vendors about their invoices?</p>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="send_email_on_payment_created" name="send_email_on_payment_created" value="1" 
                                       {{ old('send_email_on_payment_created', $settings->send_email_on_payment_created) ? 'checked' : '' }}>
                                <label class="form-check-label small" for="send_email_on_payment_created">
                                    <strong>On Invoice Created</strong><br>
                                    <small class="text-muted">Send when new expense is added</small>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="send_email_on_payment_scheduled" name="send_email_on_payment_scheduled" value="1" 
                                       {{ old('send_email_on_payment_scheduled', $settings->send_email_on_payment_scheduled) ? 'checked' : '' }}>
                                <label class="form-check-label small" for="send_email_on_payment_scheduled">
                                    <strong>On Payment Scheduled</strong><br>
                                    <small class="text-muted">Notify when payment date is set</small>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="send_email_on_payment_paid" name="send_email_on_payment_paid" value="1" 
                                       {{ old('send_email_on_payment_paid', $settings->send_email_on_payment_paid) ? 'checked' : '' }}>
                                <label class="form-check-label small" for="send_email_on_payment_paid">
                                    <strong>On Payment Completed</strong><br>
                                    <small class="text-muted">Confirm when payment is marked paid</small>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-info mt-3 mb-0 small">
                        <i class="fas fa-info-circle me-1"></i>
                        These settings apply globally. You can also disable emails per-expense when creating invoices.
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-danger"><i class="fas fa-save me-1"></i> Save Settings</button>
                <a href="{{ route('pnl.dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>

        <!-- Reset Invoice Sequence (separate form) -->
        <div class="card border-0 shadow-sm mt-4 border-warning" style="border-left: 4px solid #ffc107 !important;">
            <div class="card-header bg-warning-subtle border-0 py-3">
                <h6 class="mb-0 text-warning"><i class="fas fa-exclamation-triangle me-2"></i>Reset Invoice Sequence</h6>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-3">Use this to reset the invoice number sequence. Only do this at the start of a new financial year or if needed.</p>
                <form action="{{ route('pnl.settings.reset-invoice') }}" method="POST" class="d-flex gap-2 align-items-end">
                    @csrf
                    <div>
                        <label class="form-label small">Start From</label>
                        <input type="number" name="start_number" class="form-control form-control-sm" value="1" min="1" style="width: 100px;">
                    </div>
                    <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm('Are you sure you want to reset the invoice sequence?')">
                        <i class="fas fa-sync-alt me-1"></i> Reset
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
