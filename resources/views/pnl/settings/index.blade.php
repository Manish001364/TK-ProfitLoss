@extends('pnl.layouts.app')

@section('pnl_content')
    <div class="container-fluid" style="max-width: 900px;">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-0" style="color: #dc3545;">P&L Settings</h4>
                <small class="text-muted">Configure your P&L module preferences</small>
            </div>
            <a href="{{ route('pnl.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('pnl.settings.update') }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Currency Settings -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-success text-white border-0 py-3">
                    <h6 class="mb-0"><i class="fas fa-coins me-2"></i>Currency Settings</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small">Default Currency <span class="text-danger">*</span></label>
                            <select name="default_currency" class="form-select @error('default_currency') is-invalid @enderror" id="defaultCurrency">
                                @foreach($currencies as $code => $info)
                                    <option value="{{ $code }}" {{ old('default_currency', $settings->default_currency ?? 'GBP') === $code ? 'selected' : '' }}>
                                        {{ $info['symbol'] }} - {{ $info['name'] }} ({{ $code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('default_currency')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="text-muted">This currency will be used for new events</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Default VAT/Tax Rate (%)</label>
                            <div class="input-group">
                                <input type="number" name="default_tax_rate" class="form-control @error('default_tax_rate') is-invalid @enderror" 
                                       value="{{ old('default_tax_rate', $settings->default_tax_rate ?? 20) }}" 
                                       min="0" max="100" step="0.01">
                                <span class="input-group-text">%</span>
                            </div>
                            @error('default_tax_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="text-muted">UK standard VAT is 20%</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Exchange Rates -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-info text-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="fas fa-exchange-alt me-2"></i>Exchange Rates</h6>
                        <button type="button" class="btn btn-light btn-sm" onclick="addExchangeRate()">
                            <i class="fas fa-plus me-1"></i> Add Rate
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-3">
                        Define exchange rates for currency conversion. These rates are used when you have events in multiple currencies.
                    </p>
                    
                    <div id="exchangeRatesContainer">
                        @forelse($currencyRates as $index => $rate)
                        <div class="row g-2 mb-2 exchange-rate-row">
                            <div class="col-md-3">
                                <select name="rates[{{ $index }}][from_currency]" class="form-select form-select-sm">
                                    @foreach($currencies as $code => $info)
                                        <option value="{{ $code }}" {{ $rate->from_currency === $code ? 'selected' : '' }}>{{ $code }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-auto d-flex align-items-center">
                                <i class="fas fa-arrow-right text-muted"></i>
                            </div>
                            <div class="col-md-3">
                                <select name="rates[{{ $index }}][to_currency]" class="form-select form-select-sm">
                                    @foreach($currencies as $code => $info)
                                        <option value="{{ $code }}" {{ $rate->to_currency === $code ? 'selected' : '' }}>{{ $code }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="number" name="rates[{{ $index }}][rate]" class="form-control form-control-sm" 
                                       value="{{ $rate->rate }}" step="0.000001" placeholder="Rate">
                            </div>
                            <div class="col-auto">
                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeExchangeRate(this)">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        @empty
                        <div class="text-center text-muted py-3" id="noRatesMsg">
                            <i class="fas fa-info-circle me-1"></i> No exchange rates defined. Click "Add Rate" to add one.
                        </div>
                        @endforelse
                    </div>
                    
                    <div class="alert alert-light border mt-3 mb-0">
                        <small>
                            <strong>Example:</strong> If 1 GBP = 1.27 USD, enter: GBP → USD = 1.27<br>
                            The reverse rate (USD → GBP) will be calculated automatically.
                        </small>
                    </div>
                </div>
            </div>

            <!-- Invoice Settings -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white border-0 py-3">
                    <h6 class="mb-0"><i class="fas fa-file-invoice me-2"></i>Invoice Settings</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small">Invoice Prefix</label>
                            <input type="text" name="invoice_prefix" class="form-control @error('invoice_prefix') is-invalid @enderror" 
                                   value="{{ old('invoice_prefix', $settings->invoice_prefix ?? 'INV') }}" maxlength="10">
                            @error('invoice_prefix')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="text-muted">e.g., INV, TKT, PNL</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Next Invoice Number</label>
                            <input type="number" name="invoice_next_number" class="form-control @error('invoice_next_number') is-invalid @enderror" 
                                   value="{{ old('invoice_next_number', $settings->invoice_next_number ?? 1) }}" min="1">
                            @error('invoice_next_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="text-muted">Format: PREFIX-YYYYMM-XXX</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Preview</label>
                            <div class="form-control bg-light" id="invoicePreview">
                                {{ ($settings->invoice_prefix ?? 'INV') }}-{{ date('Ym') }}-{{ str_pad($settings->invoice_next_number ?? 1, 3, '0', STR_PAD_LEFT) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Company Details for Invoices -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-secondary text-white border-0 py-3">
                    <h6 class="mb-0"><i class="fas fa-building me-2"></i>Company Details (for Invoices)</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small">Company Name</label>
                            <input type="text" name="company_name" class="form-control @error('company_name') is-invalid @enderror" 
                                   value="{{ old('company_name', $settings->company_name) }}">
                            @error('company_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">VAT Registration Number</label>
                            <input type="text" name="company_vat_number" class="form-control @error('company_vat_number') is-invalid @enderror" 
                                   value="{{ old('company_vat_number', $settings->company_vat_number) }}" placeholder="GB123456789">
                            @error('company_vat_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label small">Company Address</label>
                            <textarea name="company_address" class="form-control @error('company_address') is-invalid @enderror" rows="2">{{ old('company_address', $settings->company_address) }}</textarea>
                            @error('company_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Email Notification Settings -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-warning text-dark border-0 py-3">
                    <h6 class="mb-0"><i class="fas fa-bell me-2"></i>Email Notifications</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" role="switch" id="email_on_created" 
                                       name="send_email_on_payment_created" value="1" 
                                       {{ old('send_email_on_payment_created', $settings->send_email_on_payment_created ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="email_on_created">
                                    On Invoice Created
                                </label>
                            </div>
                            <small class="text-muted">Send email when a new expense invoice is created</small>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" role="switch" id="email_on_scheduled" 
                                       name="send_email_on_payment_scheduled" value="1"
                                       {{ old('send_email_on_payment_scheduled', $settings->send_email_on_payment_scheduled ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="email_on_scheduled">
                                    On Payment Scheduled
                                </label>
                            </div>
                            <small class="text-muted">Send email when a payment is scheduled</small>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" role="switch" id="email_on_paid" 
                                       name="send_email_on_payment_paid" value="1"
                                       {{ old('send_email_on_payment_paid', $settings->send_email_on_payment_paid ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="email_on_paid">
                                    On Payment Completed
                                </label>
                            </div>
                            <small class="text-muted">Send email when a payment is marked as paid</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-save me-1"></i> Save Settings
                </button>
                <a href="{{ route('pnl.dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection

@section('customjs')
    <script>
        let rateIndex = {{ count($currencyRates) }};
        
        function addExchangeRate() {
            const container = document.getElementById('exchangeRatesContainer');
            const noRatesMsg = document.getElementById('noRatesMsg');
            if (noRatesMsg) noRatesMsg.remove();
            
            const currencies = @json(array_keys($currencies));
            
            const row = document.createElement('div');
            row.className = 'row g-2 mb-2 exchange-rate-row';
            row.innerHTML = `
                <div class="col-md-3">
                    <select name="rates[${rateIndex}][from_currency]" class="form-select form-select-sm">
                        ${currencies.map(c => `<option value="${c}" ${c === 'GBP' ? 'selected' : ''}>${c}</option>`).join('')}
                    </select>
                </div>
                <div class="col-auto d-flex align-items-center">
                    <i class="fas fa-arrow-right text-muted"></i>
                </div>
                <div class="col-md-3">
                    <select name="rates[${rateIndex}][to_currency]" class="form-select form-select-sm">
                        ${currencies.map(c => `<option value="${c}" ${c === 'USD' ? 'selected' : ''}>${c}</option>`).join('')}
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="number" name="rates[${rateIndex}][rate]" class="form-control form-control-sm" 
                           step="0.000001" placeholder="e.g., 1.27" required>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeExchangeRate(this)">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            container.appendChild(row);
            rateIndex++;
        }
        
        function removeExchangeRate(btn) {
            btn.closest('.exchange-rate-row').remove();
            
            // Show message if no rates left
            const container = document.getElementById('exchangeRatesContainer');
            if (container.querySelectorAll('.exchange-rate-row').length === 0) {
                container.innerHTML = `
                    <div class="text-center text-muted py-3" id="noRatesMsg">
                        <i class="fas fa-info-circle me-1"></i> No exchange rates defined. Click "Add Rate" to add one.
                    </div>
                `;
            }
        }
        
        // Update invoice preview
        document.querySelector('[name="invoice_prefix"]').addEventListener('input', updateInvoicePreview);
        document.querySelector('[name="invoice_next_number"]').addEventListener('input', updateInvoicePreview);
        
        function updateInvoicePreview() {
            const prefix = document.querySelector('[name="invoice_prefix"]').value || 'INV';
            const number = document.querySelector('[name="invoice_next_number"]').value || 1;
            const yearMonth = '{{ date("Ym") }}';
            const padded = String(number).padStart(3, '0');
            document.getElementById('invoicePreview').textContent = `${prefix}-${yearMonth}-${padded}`;
        }
    </script>
@endsection
