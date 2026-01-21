@extends('layouts.organiser_layout')

@section('content')
    <div class="container py-4" style="max-width: 900px;">
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

        <form action="{{ route('pnl.vendors.store') }}" method="POST">
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
                            <label class="form-label small">Service Type</label>
                            <select name="type" class="form-select @error('type') is-invalid @enderror">
                                @foreach($vendorTypes as $key => $label)
                                    <option value="{{ $key }}" {{ old('type', 'artist') === $key ? 'selected' : '' }}>{{ $label }}</option>
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
                        <div class="col-md-3">
                            <label class="form-label small">Primary Phone</label>
                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                                   value="{{ old('phone') }}" placeholder="+44 7xxx xxx xxx">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Secondary Phone</label>
                            <input type="text" name="alternate_phone" class="form-control" 
                                   value="{{ old('alternate_phone') }}" placeholder="Alternate number">
                        </div>
                        <div class="col-12">
                            <label class="form-label small">Business Address</label>
                            <textarea name="business_address" class="form-control" rows="2" placeholder="Full business address">{{ old('business_address') }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label small">Home Address (if different)</label>
                            <textarea name="home_address" class="form-control" rows="2" placeholder="Residential address">{{ old('home_address') }}</textarea>
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
                            <input type="text" name="emergency_contact_phone" class="form-control" value="{{ old('emergency_contact_phone') }}">
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
