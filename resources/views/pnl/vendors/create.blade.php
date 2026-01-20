@extends('layouts.organiser_layout')

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="mb-4">
            <h1 class="h3 mb-0"><i class="fas fa-user-plus"></i> Add New Vendor/Artist</h1>
        </div>

        <form action="{{ route('pnl.vendors.store') }}" method="POST">
            @csrf
            <div class="row">
                <!-- Basic Info -->
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0"><i class="fas fa-user"></i> Basic Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Full Name <span class="text-danger">*</span></label>
                                        <input type="text" name="full_name" class="form-control @error('full_name') is-invalid @enderror" 
                                               value="{{ old('full_name') }}" required>
                                        @error('full_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Business Name</label>
                                        <input type="text" name="business_name" class="form-control @error('business_name') is-invalid @enderror" 
                                               value="{{ old('business_name') }}">
                                        @error('business_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label>Type <span class="text-danger">*</span></label>
                                <select name="type" class="form-control @error('type') is-invalid @enderror" required>
                                    @foreach($vendorTypes as $key => $label)
                                        <option value="{{ $key }}" {{ old('type') === $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="form-group mb-3">
                                <label>Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                       value="{{ old('email') }}" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Phone</label>
                                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                                               value="{{ old('phone') }}">
                                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Alternate Phone</label>
                                        <input type="text" name="alternate_phone" class="form-control @error('alternate_phone') is-invalid @enderror" 
                                               value="{{ old('alternate_phone') }}">
                                        @error('alternate_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label>Business Address</label>
                                <textarea name="business_address" class="form-control @error('business_address') is-invalid @enderror" rows="2">{{ old('business_address') }}</textarea>
                                @error('business_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="form-group mb-3">
                                <label>Home Address</label>
                                <textarea name="home_address" class="form-control @error('home_address') is-invalid @enderror" rows="2">{{ old('home_address') }}</textarea>
                                @error('home_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <!-- Emergency Contact -->
                    <div class="card mb-4">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="card-title mb-0"><i class="fas fa-phone-alt"></i> Emergency Contact</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Contact Name</label>
                                        <input type="text" name="emergency_contact_name" class="form-control" value="{{ old('emergency_contact_name') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Contact Phone</label>
                                        <input type="text" name="emergency_contact_phone" class="form-control" value="{{ old('emergency_contact_phone') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-0">
                                <label>Relation</label>
                                <input type="text" name="emergency_contact_relation" class="form-control" value="{{ old('emergency_contact_relation') }}" placeholder="e.g., Manager, Spouse">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bank & Tax Info -->
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="card-title mb-0"><i class="fas fa-university"></i> Bank Details (For Reference)</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Bank Name</label>
                                        <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Branch</label>
                                        <input type="text" name="bank_branch" class="form-control" value="{{ old('bank_branch') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label>Account Holder Name</label>
                                <input type="text" name="bank_account_name" class="form-control" value="{{ old('bank_account_name') }}">
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Account Number</label>
                                        <input type="text" name="bank_account_number" class="form-control" value="{{ old('bank_account_number') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>IFSC Code</label>
                                        <input type="text" name="bank_ifsc_code" class="form-control" value="{{ old('bank_ifsc_code') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="card-title mb-0"><i class="fas fa-file-invoice"></i> Tax Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>PAN Number</label>
                                        <input type="text" name="pan_number" class="form-control" value="{{ old('pan_number') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>GST Number</label>
                                        <input type="text" name="gst_number" class="form-control" value="{{ old('gst_number') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-0">
                                <label>Tax/VAT Reference</label>
                                <input type="text" name="tax_vat_reference" class="form-control" value="{{ old('tax_vat_reference') }}">
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header bg-success text-white">
                            <h5 class="card-title mb-0"><i class="fas fa-sticky-note"></i> Additional Info</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group mb-3">
                                <label>Preferred Payment Cycle</label>
                                <select name="preferred_payment_cycle" class="form-control">
                                    <option value="">Not Specified</option>
                                    <option value="per-event" {{ old('preferred_payment_cycle') === 'per-event' ? 'selected' : '' }}>Per Event</option>
                                    <option value="weekly" {{ old('preferred_payment_cycle') === 'weekly' ? 'selected' : '' }}>Weekly</option>
                                    <option value="monthly" {{ old('preferred_payment_cycle') === 'monthly' ? 'selected' : '' }}>Monthly</option>
                                    <option value="advance" {{ old('preferred_payment_cycle') === 'advance' ? 'selected' : '' }}>Advance</option>
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label>Notes</label>
                                <textarea name="notes" class="form-control" rows="3" placeholder="Special terms, requirements, etc.">{{ old('notes') }}</textarea>
                            </div>
                            <div class="form-group mb-0">
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Active</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> Create Vendor/Artist
                            </button>
                            <a href="{{ route('pnl.vendors.index') }}" class="btn btn-secondary btn-lg">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
