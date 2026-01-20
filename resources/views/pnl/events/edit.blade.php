@extends('layouts.organiser_layout')

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="mb-4">
            <h1 class="h3 mb-0"><i class="fas fa-edit"></i> Edit Event: {{ $event->name }}</h1>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="card-title mb-0">Event Details</h5>
                    </div>
                    <form action="{{ route('pnl.events.update', $event) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group mb-3">
                                        <label for="name">Event Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                               id="name" name="name" value="{{ old('name', $event->name) }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="status">Status <span class="text-danger">*</span></label>
                                        <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                            <option value="draft" {{ old('status', $event->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                                            <option value="planning" {{ old('status', $event->status) === 'planning' ? 'selected' : '' }}>Planning</option>
                                            <option value="active" {{ old('status', $event->status) === 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="completed" {{ old('status', $event->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                                            <option value="cancelled" {{ old('status', $event->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
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
                                          id="description" name="description" rows="3">{{ old('description', $event->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="venue">Venue</label>
                                        <input type="text" class="form-control @error('venue') is-invalid @enderror" 
                                               id="venue" name="venue" value="{{ old('venue', $event->venue) }}">
                                        @error('venue')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="location">Location</label>
                                        <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                               id="location" name="location" value="{{ old('location', $event->location) }}">
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
                                               id="event_date" name="event_date" value="{{ old('event_date', $event->event_date->format('Y-m-d')) }}" required>
                                        @error('event_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="event_time">Event Time</label>
                                        <input type="time" class="form-control @error('event_time') is-invalid @enderror" 
                                               id="event_time" name="event_time" value="{{ old('event_time', $event->event_time?->format('H:i')) }}">
                                        @error('event_time')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="budget">Budget (₹)</label>
                                        <input type="number" step="0.01" class="form-control @error('budget') is-invalid @enderror" 
                                               id="budget" name="budget" value="{{ old('budget', $event->budget) }}" min="0">
                                        @error('budget')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Update Event
                            </button>
                            <a href="{{ route('pnl.events.show', $event) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
