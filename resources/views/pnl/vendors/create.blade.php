@extends('pnl.layouts.app')

@section('pnl_content')
    <div class="container-fluid" style="max-width: 900px;">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">Add Vendor / Artist</h4>
                <p class="text-muted small mb-0">Add a new vendor, artist, DJ, caterer, or service provider</p>
            </div>
            <a href="{{ route('pnl.vendors.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Duplicate Warning:</strong> {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('pnl.vendors.store') }}" method="POST" id="vendorForm">
            @csrf
            
            <!-- Basic Information - Required -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0"><i class="fas fa-user me-2 text-danger"></i>Basic Information</h6>
                    <small class="text-muted">Only vendor/artist name is required. Other fields are optional.</small>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small">Vendor/Artist Name <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" class="form-control @error('full_name') is-invalid @enderror" 
                                   value="{{ old('full_name') }}" required placeholder="e.g., DJ Alpha, ABC Catering">
                            @error('full_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Business/Company Name</label>
                            <input type="text" name="business_name" class="form-control @error('business_name') is-invalid @enderror" 
                                   value="{{ old('business_name') }}" placeholder="Legal business name (if different)">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Service Type <span class="text-danger">*</span></label>
                            <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                <option value="">Select Service Type</option>
                                @foreach($vendorTypes as $key => $label)
                                    <option value="{{ $key }}" {{ old('type') === $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="text-muted">What service do they provide?</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Service Area / Specialization</label>
                            <input type="text" name="specialization" class="form-control" 
                                   value="{{ old('specialization') }}" placeholder="e.g., Bollywood DJ, Continental Food, Security">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Details -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0"><i class="fas fa-address-book me-2 text-primary"></i>Contact Details</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small">Email Address</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email') }}" placeholder="vendor@example.com">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <!-- Empty for layout -->
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Primary Phone <span class="text-danger">*</span></label>
                            <input type="tel" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" required 
                                   value="{{ old('phone') }}">
                            <input type="hidden" name="phone_country_code" id="phone_country_code" value="{{ old('phone_country_code', '+44') }}">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="text-muted phone-valid-msg d-none text-success"><i class="fas fa-check"></i> Valid number</small>
                            <small class="text-muted phone-invalid-msg d-none text-danger"><i class="fas fa-times"></i> Invalid number format</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Secondary Phone</label>
                            <input type="tel" name="alternate_phone" id="alternate_phone" class="form-control" 
                                   value="{{ old('alternate_phone') }}">
                            <input type="hidden" name="alternate_phone_country_code" id="alternate_phone_country_code" value="{{ old('alternate_phone_country_code', '+44') }}">
                            <small class="text-muted alt-phone-valid-msg d-none text-success"><i class="fas fa-check"></i> Valid number</small>
                            <small class="text-muted alt-phone-invalid-msg d-none text-danger"><i class="fas fa-times"></i> Invalid number format</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Address Details -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0"><i class="fas fa-map-marker-alt me-2 text-success"></i>Address Details</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small">Business Address</label>
                            <textarea name="business_address" class="form-control" rows="2" placeholder="Street address, city">{{ old('business_address') }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Country</label>
                            <select name="business_country" id="business_country" class="form-select">
                                @foreach($countries as $code => $name)
                                    <option value="{{ $name }}" {{ old('business_country', 'United Kingdom') === $name ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Postcode / ZIP</label>
                            <input type="text" name="business_postcode" id="business_postcode" class="form-control" 
                                   value="{{ old('business_postcode') }}" placeholder="e.g., SW1A 1AA">
                            <small class="text-muted postcode-hint">UK format: SW1A 1AA</small>
                        </div>
                        
                        <div class="col-12 mt-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="showHomeAddress" {{ old('home_address') ? 'checked' : '' }}>
                                <label class="form-check-label small" for="showHomeAddress">Add different home address</label>
                            </div>
                        </div>
                        
                        <div class="col-12 home-address-section" style="{{ old('home_address') ? '' : 'display: none;' }}">
                            <hr class="my-3">
                            <label class="form-label small">Home Address</label>
                            <textarea name="home_address" class="form-control" rows="2" placeholder="Residential address">{{ old('home_address') }}</textarea>
                        </div>
                        <div class="col-md-6 home-address-section" style="{{ old('home_address') ? '' : 'display: none;' }}">
                            <label class="form-label small">Home Country</label>
                            <select name="home_country" class="form-select">
                                <option value="">Same as business</option>
                                @foreach($countries as $code => $name)
                                    <option value="{{ $name }}" {{ old('home_country') === $name ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 home-address-section" style="{{ old('home_address') ? '' : 'display: none;' }}">
                            <label class="form-label small">Home Postcode</label>
                            <input type="text" name="home_postcode" class="form-control" value="{{ old('home_postcode') }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Emergency Contact -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0"><i class="fas fa-phone-alt me-2 text-warning"></i>Emergency Contact</h6>
                    <small class="text-muted">Manager, agent, or emergency point of contact</small>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small">Contact Name</label>
                            <input type="text" name="emergency_contact_name" class="form-control" value="{{ old('emergency_contact_name') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Contact Phone</label>
                            <input type="tel" name="emergency_contact_phone" id="emergency_phone" class="form-control" value="{{ old('emergency_contact_phone') }}">
                            <input type="hidden" name="emergency_contact_phone_country_code" id="emergency_phone_country_code" value="{{ old('emergency_contact_phone_country_code', '+44') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Relation/Role</label>
                            <input type="text" name="emergency_contact_relation" class="form-control" value="{{ old('emergency_contact_relation') }}" placeholder="e.g., Manager, Agent, Spouse">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bank & Payment Details (Collapsible) -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3" data-bs-toggle="collapse" data-bs-target="#bankDetails" style="cursor: pointer;">
                    <h6 class="mb-0">
                        <i class="fas fa-university me-2 text-info"></i>Bank & Payment Details
                        <i class="fas fa-chevron-down float-end text-muted"></i>
                    </h6>
                    <small class="text-muted">Click to expand (for payment reference only)</small>
                </div>
                <div class="collapse" id="bankDetails">
                    <div class="card-body border-top">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small">Bank Name</label>
                                <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">Branch</label>
                                <input type="text" name="bank_branch" class="form-control" value="{{ old('bank_branch') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">Account Holder Name</label>
                                <input type="text" name="bank_account_name" class="form-control" value="{{ old('bank_account_name') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">Account Number</label>
                                <input type="text" name="bank_account_number" class="form-control" value="{{ old('bank_account_number') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">Sort Code / IFSC</label>
                                <input type="text" name="bank_ifsc_code" class="form-control" value="{{ old('bank_ifsc_code') }}" placeholder="e.g., 20-30-40">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">Preferred Payment Method</label>
                                <select name="preferred_payment_cycle" class="form-select">
                                    <option value="">Select...</option>
                                    <option value="bank_transfer" {{ old('preferred_payment_cycle') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer (BACS)</option>
                                    <option value="per-event" {{ old('preferred_payment_cycle') === 'per-event' ? 'selected' : '' }}>Per Event</option>
                                    <option value="advance" {{ old('preferred_payment_cycle') === 'advance' ? 'selected' : '' }}>Advance Payment</option>
                                    <option value="weekly" {{ old('preferred_payment_cycle') === 'weekly' ? 'selected' : '' }}>Weekly</option>
                                    <option value="monthly" {{ old('preferred_payment_cycle') === 'monthly' ? 'selected' : '' }}>Monthly</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tax Information (Collapsible) -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3" data-bs-toggle="collapse" data-bs-target="#taxDetails" style="cursor: pointer;">
                    <h6 class="mb-0">
                        <i class="fas fa-file-invoice me-2 text-secondary"></i>Tax Information
                        <i class="fas fa-chevron-down float-end text-muted"></i>
                    </h6>
                    <small class="text-muted">Click to expand</small>
                </div>
                <div class="collapse" id="taxDetails">
                    <div class="card-body border-top">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small">UTR / Tax Reference</label>
                                <input type="text" name="pan_number" class="form-control" value="{{ old('pan_number') }}" placeholder="Unique Taxpayer Reference">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">VAT Number</label>
                                <input type="text" name="gst_number" class="form-control" value="{{ old('gst_number') }}" placeholder="GB123456789">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">Company Number</label>
                                <input type="text" name="tax_vat_reference" class="form-control" value="{{ old('tax_vat_reference') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes & Description -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0"><i class="fas fa-sticky-note me-2 text-success"></i>Additional Notes</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small">Description / Notes</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Special terms, requirements, rider details, equipment needs, etc.">{{ old('notes') }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label small" for="is_active">Active (available for booking)</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-save me-1"></i> Save Vendor/Artist
                </button>
                <a href="{{ route('pnl.vendors.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection

@section('customjs')
    <!-- intl-tel-input CSS & JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.5.3/build/css/intlTelInput.css">
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.5.3/build/js/intlTelInput.min.js"></script>
    
    <style>
        /* Fix intl-tel-input width and styling */
        .iti { width: 100%; }
        .iti__flag { background-image: url("https://cdn.jsdelivr.net/npm/intl-tel-input@18.5.3/build/img/flags.png"); }
        @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
            .iti__flag { background-image: url("https://cdn.jsdelivr.net/npm/intl-tel-input@18.5.3/build/img/flags@2x.png"); }
        }
        /* Ensure dropdown appears properly */
        .iti__country-list {
            z-index: 9999;
            max-height: 250px;
            background: #fff;
            border: 1px solid #dee2e6;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .iti--separate-dial-code .iti__selected-flag {
            background-color: #f8f9fa;
            border-right: 1px solid #dee2e6;
        }
        .iti--separate-dial-code input {
            padding-left: 100px !important;
        }
        .phone-example { font-size: 11px; }
    </style>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Phone format examples per country
            const phoneExamples = {
                'gb': '7911 123456',
                'us': '(201) 555-0123',
                'in': '98765 43210',
                'de': '1512 3456789',
                'fr': '6 12 34 56 78',
                'es': '612 34 56 78',
                'it': '312 345 6789',
                'nl': '6 12345678',
                'ie': '85 123 4567',
                'au': '412 345 678',
                'ca': '(506) 234-5678',
                'ae': '50 123 4567',
                'sg': '8123 4567'
            };

            // Initialize intl-tel-input for all phone fields
            const phoneInputs = [
                { input: document.querySelector('#phone'), hidden: document.querySelector('#phone_country_code'), validMsg: '.phone-valid-msg', invalidMsg: '.phone-invalid-msg', exampleId: 'phone-example' },
                { input: document.querySelector('#alternate_phone'), hidden: document.querySelector('#alternate_phone_country_code'), validMsg: '.alt-phone-valid-msg', invalidMsg: '.alt-phone-invalid-msg', exampleId: 'alt-phone-example' },
                { input: document.querySelector('#emergency_phone'), hidden: document.querySelector('#emergency_phone_country_code'), validMsg: null, invalidMsg: null, exampleId: null }
            ];
            
            const itiInstances = [];
            
            phoneInputs.forEach(function(config) {
                if (!config.input) return;
                
                const iti = intlTelInput(config.input, {
                    initialCountry: "gb",
                    preferredCountries: ["gb", "us", "in", "de", "fr", "es", "it", "nl", "ie", "au", "ca", "ae", "sg"],
                    separateDialCode: true,
                    utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.5.3/build/js/utils.js",
                    nationalMode: true,
                    autoPlaceholder: "aggressive",
                    formatOnDisplay: true
                });
                
                itiInstances.push({ iti: iti, config: config });
                
                // Update hidden field and show example when country changes
                config.input.addEventListener('countrychange', function() {
                    const countryData = iti.getSelectedCountryData();
                    config.hidden.value = '+' + countryData.dialCode;
                    
                    // Update example text
                    if (config.exampleId) {
                        const exampleEl = document.getElementById(config.exampleId);
                        if (exampleEl) {
                            const example = phoneExamples[countryData.iso2] || '';
                            exampleEl.textContent = example ? `Example: +${countryData.dialCode} ${example}` : '';
                        }
                    }
                });
                
                // Validate on blur with specific error message
                config.input.addEventListener('blur', function() {
                    const countryData = iti.getSelectedCountryData();
                    config.hidden.value = '+' + countryData.dialCode;
                    
                    if (config.validMsg && config.invalidMsg) {
                        const validMsg = document.querySelector(config.validMsg);
                        const invalidMsg = document.querySelector(config.invalidMsg);
                        
                        if (config.input.value.trim()) {
                            if (iti.isValidNumber()) {
                                validMsg.classList.remove('d-none');
                                invalidMsg.classList.add('d-none');
                                config.input.classList.remove('is-invalid');
                                config.input.classList.add('is-valid');
                            } else {
                                validMsg.classList.add('d-none');
                                invalidMsg.classList.remove('d-none');
                                // Show specific error with example
                                const example = phoneExamples[countryData.iso2] || '';
                                const countryName = countryData.name || 'selected country';
                                invalidMsg.innerHTML = `<i class="fas fa-times"></i> Invalid format for ${countryName}. ${example ? 'Example: +' + countryData.dialCode + ' ' + example : ''}`;
                                config.input.classList.add('is-invalid');
                                config.input.classList.remove('is-valid');
                            }
                        } else {
                            validMsg.classList.add('d-none');
                            invalidMsg.classList.add('d-none');
                            config.input.classList.remove('is-invalid', 'is-valid');
                        }
                    }
                });
                
                // Set initial value and trigger initial example
                const countryData = iti.getSelectedCountryData();
                config.hidden.value = '+' + countryData.dialCode;
                if (config.exampleId) {
                    const exampleEl = document.getElementById(config.exampleId);
                    if (exampleEl) {
                        const example = phoneExamples[countryData.iso2] || '';
                        exampleEl.textContent = example ? `Example: +${countryData.dialCode} ${example}` : '';
                    }
                }
            });
            
            // Form submit - get national number only
            document.querySelector('#vendorForm').addEventListener('submit', function() {
                itiInstances.forEach(function(item) {
                    if (item.config.input.value) {
                        // Store just the national number (without country code)
                        item.config.input.value = item.iti.getNumber(intlTelInputUtils.numberFormat.NATIONAL).replace(/\s/g, '');
                    }
                });
            });
            
            // Home address toggle
            document.querySelector('#showHomeAddress').addEventListener('change', function() {
                const sections = document.querySelectorAll('.home-address-section');
                sections.forEach(function(section) {
                    section.style.display = this.checked ? '' : 'none';
                }.bind(this));
            });
            
            // Postcode patterns and placeholders per country
            const postcodeConfig = {
                'United Kingdom': { placeholder: 'SW1A 1AA', hint: 'UK format: SW1A 1AA' },
                'United States': { placeholder: '12345', hint: 'US format: 12345 or 12345-6789' },
                'Canada': { placeholder: 'A1A 1A1', hint: 'Canadian format: A1A 1A1' },
                'India': { placeholder: '110001', hint: 'Indian format: 110001 (6 digits)' },
                'Germany': { placeholder: '10115', hint: 'German format: 10115 (5 digits)' },
                'France': { placeholder: '75001', hint: 'French format: 75001 (5 digits)' },
                'Australia': { placeholder: '2000', hint: 'Australian format: 2000 (4 digits)' },
                'Ireland': { placeholder: 'D02 Y006', hint: 'Irish Eircode: D02 Y006' },
                'Netherlands': { placeholder: '1012 AB', hint: 'Dutch format: 1012 AB' },
                'Spain': { placeholder: '28001', hint: 'Spanish format: 28001 (5 digits)' },
                'Italy': { placeholder: '00100', hint: 'Italian format: 00100 (5 digits)' },
                'United Arab Emirates': { placeholder: '', hint: 'UAE: No postcode required' },
                'Singapore': { placeholder: '018956', hint: 'Singapore format: 018956 (6 digits)' },
                'default': { placeholder: '', hint: 'Enter postcode/ZIP' }
            };
            
            document.querySelector('#business_country').addEventListener('change', function() {
                const country = this.value;
                const postcodeInput = document.querySelector('#business_postcode');
                const hint = document.querySelector('.postcode-hint');
                
                const config = postcodeConfig[country] || postcodeConfig['default'];
                postcodeInput.placeholder = config.placeholder;
                hint.textContent = config.hint;
            });
        });
    </script>
@endsection
