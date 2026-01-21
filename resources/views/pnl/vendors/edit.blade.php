@extends('pnl.layouts.app')

@section('pnl_content')
    <div class="container-fluid" style="max-width: 900px;">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">Edit Vendor/Artist</h4>
                <p class="text-muted small mb-0">{{ $vendor->display_name }}</p>
            </div>
            <a href="{{ route('pnl.vendors.show', $vendor) }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <form action="{{ route('pnl.vendors.update', $vendor) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Basic Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-danger text-white border-0 py-3">
                    <h6 class="mb-0"><i class="fas fa-user me-2"></i>Basic Information</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" class="form-control @error('full_name') is-invalid @enderror" 
                                   value="{{ old('full_name', $vendor->full_name) }}" required placeholder="John Smith">
                            @error('full_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Business/Stage Name</label>
                            <input type="text" name="business_name" class="form-control" 
                                   value="{{ old('business_name', $vendor->business_name) }}" placeholder="DJ Nova">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Type <span class="text-danger">*</span></label>
                            <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                @foreach($vendorTypes as $key => $label)
                                    <option value="{{ $key }}" {{ old('type', $vendor->type) === $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Service/Specialization</label>
                            <input type="text" name="specialization" class="form-control" 
                                   value="{{ old('specialization', $vendor->specialization) }}" placeholder="House music, Corporate events">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email', $vendor->email) }}" required placeholder="vendor@email.com">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Phone</label>
                            <input type="text" name="phone" class="form-control" 
                                   value="{{ old('phone', $vendor->phone) }}" placeholder="+44 7xxx">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Alt Phone</label>
                            <input type="text" name="alternate_phone" class="form-control" 
                                   value="{{ old('alternate_phone', $vendor->alternate_phone) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label small">Business Address</label>
                            <textarea name="business_address" class="form-control" rows="2" placeholder="Street, City, Postcode">{{ old('business_address', $vendor->business_address) }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label small">Home Address</label>
                            <textarea name="home_address" class="form-control" rows="2">{{ old('home_address', $vendor->home_address) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Emergency Contact -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-info text-white border-0 py-3">
                    <h6 class="mb-0"><i class="fas fa-phone-alt me-2"></i>Emergency Contact</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small">Contact Name</label>
                            <input type="text" name="emergency_contact_name" class="form-control" 
                                   value="{{ old('emergency_contact_name', $vendor->emergency_contact_name) }}" placeholder="Jane Smith">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Contact Phone</label>
                            <input type="text" name="emergency_contact_phone" class="form-control" 
                                   value="{{ old('emergency_contact_phone', $vendor->emergency_contact_phone) }}" placeholder="+44 7xxx">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Relation</label>
                            <input type="text" name="emergency_contact_relation" class="form-control" 
                                   value="{{ old('emergency_contact_relation', $vendor->emergency_contact_relation) }}" placeholder="Manager, Spouse">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bank Details -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-secondary text-white border-0 py-3">
                    <h6 class="mb-0"><i class="fas fa-university me-2"></i>Bank Details</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small">Bank Name</label>
                            <input type="text" name="bank_name" class="form-control" 
                                   value="{{ old('bank_name', $vendor->bank_name) }}" placeholder="Barclays, HSBC">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Account Holder Name</label>
                            <input type="text" name="bank_account_name" class="form-control" 
                                   value="{{ old('bank_account_name', $vendor->bank_account_name) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Account Number</label>
                            <input type="text" name="bank_account_number" class="form-control" 
                                   value="{{ old('bank_account_number', $vendor->bank_account_number) }}" placeholder="12345678">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Sort Code</label>
                            <input type="text" name="bank_ifsc_code" class="form-control" 
                                   value="{{ old('bank_ifsc_code', $vendor->bank_ifsc_code) }}" placeholder="12-34-56">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Branch</label>
                            <input type="text" name="bank_branch" class="form-control" 
                                   value="{{ old('bank_branch', $vendor->bank_branch) }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tax Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-dark text-white border-0 py-3">
                    <h6 class="mb-0"><i class="fas fa-file-invoice me-2"></i>Tax Information</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small">UTR Number</label>
                            <input type="text" name="pan_number" class="form-control" 
                                   value="{{ old('pan_number', $vendor->pan_number) }}" placeholder="10-digit UTR">
                            <small class="text-muted">Unique Taxpayer Reference</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">VAT Number</label>
                            <input type="text" name="gst_number" class="form-control" 
                                   value="{{ old('gst_number', $vendor->gst_number) }}" placeholder="GB123456789">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Company Number</label>
                            <input type="text" name="tax_vat_reference" class="form-control" 
                                   value="{{ old('tax_vat_reference', $vendor->tax_vat_reference) }}" placeholder="Companies House No.">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-success text-white border-0 py-3">
                    <h6 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Additional Information</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small">Preferred Payment Cycle</label>
                            <select name="preferred_payment_cycle" class="form-select">
                                <option value="">Not Specified</option>
                                <option value="per-event" {{ old('preferred_payment_cycle', $vendor->preferred_payment_cycle) === 'per-event' ? 'selected' : '' }}>Per Event</option>
                                <option value="weekly" {{ old('preferred_payment_cycle', $vendor->preferred_payment_cycle) === 'weekly' ? 'selected' : '' }}>Weekly</option>
                                <option value="monthly" {{ old('preferred_payment_cycle', $vendor->preferred_payment_cycle) === 'monthly' ? 'selected' : '' }}>Monthly</option>
                                <option value="advance" {{ old('preferred_payment_cycle', $vendor->preferred_payment_cycle) === 'advance' ? 'selected' : '' }}>Advance Payment</option>
                            </select>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" role="switch" id="is_active" name="is_active" value="1" 
                                       {{ old('is_active', $vendor->is_active) ? 'checked' : '' }} style="width: 2.5em; height: 1.25em;">
                                <label class="form-check-label ms-2" for="is_active">
                                    <strong>Active Vendor</strong>
                                    <br><small class="text-muted">Inactive vendors won't appear in dropdowns</small>
                                </label>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label small">Notes</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Any additional notes about this vendor...">{{ old('notes', $vendor->notes) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-save me-1"></i> Update Vendor
                </button>
                <a href="{{ route('pnl.vendors.show', $vendor) }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
