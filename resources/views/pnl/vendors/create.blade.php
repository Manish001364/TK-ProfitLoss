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
                            <div class="input-group">
                                <select name="phone_country_code" id="phone_country_code" class="form-select phone-country-select" style="max-width: 140px;">
                                    <option value="+44" data-example="7911 123456" {{ old('phone_country_code', '+44') == '+44' ? 'selected' : '' }}>🇬🇧 +44</option>
                                    <option value="+1" data-example="(201) 555-0123" {{ old('phone_country_code') == '+1' ? 'selected' : '' }}>🇺🇸 +1</option>
                                    <option value="+91" data-example="98765 43210" {{ old('phone_country_code') == '+91' ? 'selected' : '' }}>🇮🇳 +91</option>
                                    <option value="+49" data-example="1512 3456789" {{ old('phone_country_code') == '+49' ? 'selected' : '' }}>🇩🇪 +49</option>
                                    <option value="+33" data-example="6 12 34 56 78" {{ old('phone_country_code') == '+33' ? 'selected' : '' }}>🇫🇷 +33</option>
                                    <option value="+34" data-example="612 34 56 78" {{ old('phone_country_code') == '+34' ? 'selected' : '' }}>🇪🇸 +34</option>
                                    <option value="+39" data-example="312 345 6789" {{ old('phone_country_code') == '+39' ? 'selected' : '' }}>🇮🇹 +39</option>
                                    <option value="+31" data-example="6 12345678" {{ old('phone_country_code') == '+31' ? 'selected' : '' }}>🇳🇱 +31</option>
                                    <option value="+353" data-example="85 123 4567" {{ old('phone_country_code') == '+353' ? 'selected' : '' }}>🇮🇪 +353</option>
                                    <option value="+61" data-example="412 345 678" {{ old('phone_country_code') == '+61' ? 'selected' : '' }}>🇦🇺 +61</option>
                                    <option value="+971" data-example="50 123 4567" {{ old('phone_country_code') == '+971' ? 'selected' : '' }}>🇦🇪 +971</option>
                                    <option value="+65" data-example="8123 4567" {{ old('phone_country_code') == '+65' ? 'selected' : '' }}>🇸🇬 +65</option>
                                    <option value="+81" data-example="90 1234 5678" {{ old('phone_country_code') == '+81' ? 'selected' : '' }}>🇯🇵 +81</option>
                                    <option value="+86" data-example="131 2345 6789" {{ old('phone_country_code') == '+86' ? 'selected' : '' }}>🇨🇳 +86</option>
                                    <option value="+55" data-example="11 91234 5678" {{ old('phone_country_code') == '+55' ? 'selected' : '' }}>🇧🇷 +55</option>
                                    <option value="+27" data-example="71 123 4567" {{ old('phone_country_code') == '+27' ? 'selected' : '' }}>🇿🇦 +27</option>
                                    <option value="+234" data-example="803 123 4567" {{ old('phone_country_code') == '+234' ? 'selected' : '' }}>🇳🇬 +234</option>
                                    <option value="+254" data-example="712 345678" {{ old('phone_country_code') == '+254' ? 'selected' : '' }}>🇰🇪 +254</option>
                                    <option value="+92" data-example="300 1234567" {{ old('phone_country_code') == '+92' ? 'selected' : '' }}>🇵🇰 +92</option>
                                    <option value="+880" data-example="1712 345678" {{ old('phone_country_code') == '+880' ? 'selected' : '' }}>🇧🇩 +880</option>
                                </select>
                                <input type="tel" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" required 
                                       value="{{ old('phone') }}" placeholder="7911 123456">
                            </div>
                            @error('phone')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            <small class="text-muted d-block mt-1" id="phone-example">Example: 7911 123456</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Secondary Phone</label>
                            <div class="input-group">
                                <select name="alternate_phone_country_code" id="alternate_phone_country_code" class="form-select phone-country-select" style="max-width: 140px;">
                                    <option value="+44" data-example="7911 123456" {{ old('alternate_phone_country_code', '+44') == '+44' ? 'selected' : '' }}>🇬🇧 +44</option>
                                    <option value="+1" data-example="(201) 555-0123" {{ old('alternate_phone_country_code') == '+1' ? 'selected' : '' }}>🇺🇸 +1</option>
                                    <option value="+91" data-example="98765 43210" {{ old('alternate_phone_country_code') == '+91' ? 'selected' : '' }}>🇮🇳 +91</option>
                                    <option value="+49" data-example="1512 3456789" {{ old('alternate_phone_country_code') == '+49' ? 'selected' : '' }}>🇩🇪 +49</option>
                                    <option value="+33" data-example="6 12 34 56 78" {{ old('alternate_phone_country_code') == '+33' ? 'selected' : '' }}>🇫🇷 +33</option>
                                    <option value="+34" data-example="612 34 56 78" {{ old('alternate_phone_country_code') == '+34' ? 'selected' : '' }}>🇪🇸 +34</option>
                                    <option value="+39" data-example="312 345 6789" {{ old('alternate_phone_country_code') == '+39' ? 'selected' : '' }}>🇮🇹 +39</option>
                                    <option value="+31" data-example="6 12345678" {{ old('alternate_phone_country_code') == '+31' ? 'selected' : '' }}>🇳🇱 +31</option>
                                    <option value="+353" data-example="85 123 4567" {{ old('alternate_phone_country_code') == '+353' ? 'selected' : '' }}>🇮🇪 +353</option>
                                    <option value="+61" data-example="412 345 678" {{ old('alternate_phone_country_code') == '+61' ? 'selected' : '' }}>🇦🇺 +61</option>
                                    <option value="+971" data-example="50 123 4567" {{ old('alternate_phone_country_code') == '+971' ? 'selected' : '' }}>🇦🇪 +971</option>
                                    <option value="+65" data-example="8123 4567" {{ old('alternate_phone_country_code') == '+65' ? 'selected' : '' }}>🇸🇬 +65</option>
                                </select>
                                <input type="tel" name="alternate_phone" id="alternate_phone" class="form-control" 
                                       value="{{ old('alternate_phone') }}" placeholder="Optional">
                            </div>
                            <small class="text-muted d-block mt-1" id="alt-phone-example">Example: 7911 123456</small>
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
                            <div class="input-group">
                                <select name="emergency_contact_phone_country_code" class="form-select phone-country-select" style="max-width: 110px;">
                                    <option value="+44" {{ old('emergency_contact_phone_country_code', '+44') == '+44' ? 'selected' : '' }}>🇬🇧 +44</option>
                                    <option value="+1" {{ old('emergency_contact_phone_country_code') == '+1' ? 'selected' : '' }}>🇺🇸 +1</option>
                                    <option value="+91" {{ old('emergency_contact_phone_country_code') == '+91' ? 'selected' : '' }}>🇮🇳 +91</option>
                                    <option value="+49" {{ old('emergency_contact_phone_country_code') == '+49' ? 'selected' : '' }}>🇩🇪 +49</option>
                                    <option value="+33" {{ old('emergency_contact_phone_country_code') == '+33' ? 'selected' : '' }}>🇫🇷 +33</option>
                                    <option value="+353" {{ old('emergency_contact_phone_country_code') == '+353' ? 'selected' : '' }}>🇮🇪 +353</option>
                                    <option value="+61" {{ old('emergency_contact_phone_country_code') == '+61' ? 'selected' : '' }}>🇦🇺 +61</option>
                                    <option value="+971" {{ old('emergency_contact_phone_country_code') == '+971' ? 'selected' : '' }}>🇦🇪 +971</option>
                                </select>
                                <input type="tel" name="emergency_contact_phone" class="form-control" value="{{ old('emergency_contact_phone') }}" placeholder="Phone number">
                            </div>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Phone country code change - update placeholder/example
            document.querySelectorAll('.phone-country-select').forEach(function(select) {
                select.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const example = selectedOption.getAttribute('data-example') || '';
                    const phoneInput = this.closest('.input-group').querySelector('input[type="tel"]');
                    if (phoneInput && example) {
                        phoneInput.placeholder = example;
                    }
                    // Update example text below
                    const exampleEl = this.closest('.col-md-6, .col-md-4').querySelector('[id$="-example"]');
                    if (exampleEl && example) {
                        exampleEl.textContent = 'Example: ' + example;
                    }
                });
            });
            
            // Home address toggle
            const homeAddressToggle = document.querySelector('#showHomeAddress');
            if (homeAddressToggle) {
                homeAddressToggle.addEventListener('change', function() {
                    const sections = document.querySelectorAll('.home-address-section');
                    sections.forEach(function(section) {
                        section.style.display = homeAddressToggle.checked ? 'block' : 'none';
                    });
                });
            }
            
            // Dynamic postcode placeholder based on country
            const businessCountry = document.querySelector('#business_country');
            const homeCountry = document.querySelector('#home_country');
            const postcodeFormats = {
                'United Kingdom': 'SW1A 1AA',
                'United States': '10001',
                'India': '400001',
                'Germany': '10115',
                'France': '75001',
                'Spain': '28001',
                'Italy': '00100',
                'Netherlands': '1012 AB',
                'Ireland': 'D01 F5P2',
                'Australia': '2000',
                'UAE': '',
                'Singapore': '018956'
            };
            
            function updatePostcodePlaceholder(countrySelect, postcodeInput) {
                if (!countrySelect || !postcodeInput) return;
                const country = countrySelect.value;
                const format = postcodeFormats[country] || '';
                postcodeInput.placeholder = format ? 'e.g., ' + format : 'Postcode / ZIP';
            }
            
            if (businessCountry) {
                businessCountry.addEventListener('change', function() {
                    updatePostcodePlaceholder(this, document.querySelector('input[name="business_postcode"]'));
                });
            }
            if (homeCountry) {
                homeCountry.addEventListener('change', function() {
                    updatePostcodePlaceholder(this, document.querySelector('input[name="home_postcode"]'));
                });
            }
        });
    </script>
@endsection
