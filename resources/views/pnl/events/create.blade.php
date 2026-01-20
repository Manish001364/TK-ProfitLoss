@extends('layouts.organiser_layout')

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="mb-4">
            <h1 class="h3 mb-0"><i class="fas fa-calendar-plus"></i> Create New Event</h1>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">Event Details</h5>
                    </div>
                    <form action="{{ route('pnl.events.store') }}" method="POST">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group mb-3">
                                        <label for="name">Event Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                               id="name" name="name" value="{{ old('name') }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="status">Status <span class="text-danger">*</span></label>
                                        <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                            <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                                            <option value="planning" {{ old('status', 'planning') === 'planning' ? 'selected' : '' }}>Planning</option>
                                            <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="description">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="3">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="venue">Venue</label>
                                        <input type="text" class="form-control @error('venue') is-invalid @enderror" 
                                               id="venue" name="venue" value="{{ old('venue') }}">
                                        @error('venue')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="location">Location</label>
                                        <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                               id="location" name="location" value="{{ old('location') }}" placeholder="City, State">
                                        @error('location')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="event_date">Event Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('event_date') is-invalid @enderror" 
                                               id="event_date" name="event_date" value="{{ old('event_date') }}" required>
                                        @error('event_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="event_time">Event Time</label>
                                        <input type="time" class="form-control @error('event_time') is-invalid @enderror" 
                                               id="event_time" name="event_time" value="{{ old('event_time') }}">
                                        @error('event_time')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="budget">Budget (₹)</label>
                                        <input type="number" step="0.01" class="form-control @error('budget') is-invalid @enderror" 
                                               id="budget" name="budget" value="{{ old('budget', 0) }}" min="0">
                                        @error('budget')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Event
                            </button>
                            <a href="{{ route('pnl.events.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0"><i class="fas fa-info-circle"></i> Tips</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2"><i class="fas fa-check text-success"></i> Set a realistic budget for better P&L tracking</li>
                            <li class="mb-2"><i class="fas fa-check text-success"></i> Use 'Planning' status for upcoming events</li>
                            <li class="mb-2"><i class="fas fa-check text-success"></i> Add venue details for complete records</li>
                            <li class="mb-2"><i class="fas fa-check text-success"></i> You can add expenses and revenue after creation</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
