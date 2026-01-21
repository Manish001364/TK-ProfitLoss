@extends('layouts.organiser_layout')

@section('content')
    <div class="container py-4" style="max-width: 900px;">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">Edit Event</h4>
                <p class="text-muted small mb-0">{{ $event->name }}</p>
            </div>
            <a href="{{ route('pnl.events.show', $event) }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <form action="{{ route('pnl.events.update', $event) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Event Details -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-danger text-white border-0 py-3">
                    <h6 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Event Details</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label small">Event Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $event->name) }}" required placeholder="Summer Music Festival 2025">
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="draft" {{ old('status', $event->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="planning" {{ old('status', $event->status) === 'planning' ? 'selected' : '' }}>Planning</option>
                                <option value="active" {{ old('status', $event->status) === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="completed" {{ old('status', $event->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ old('status', $event->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label small">Description</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Event description...">{{ old('description', $event->description) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Venue & Location -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white border-0 py-3">
                    <h6 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Venue & Location</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small">Venue Name</label>
                            <input type="text" name="venue" class="form-control" 
                                   value="{{ old('venue', $event->venue) }}" placeholder="O2 Arena, Wembley Stadium">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Location/City</label>
                            <input type="text" name="location" class="form-control" 
                                   value="{{ old('location', $event->location) }}" placeholder="London, UK">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Date, Time & Budget -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-success text-white border-0 py-3">
                    <h6 class="mb-0"><i class="fas fa-clock me-2"></i>Date, Time & Budget</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small">Event Date <span class="text-danger">*</span></label>
                            <input type="date" name="event_date" class="form-control @error('event_date') is-invalid @enderror" 
                                   value="{{ old('event_date', $event->event_date->format('Y-m-d')) }}" required>
                            @error('event_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Event Time</label>
                            <input type="time" name="event_time" class="form-control" 
                                   value="{{ old('event_time', $event->event_time?->format('H:i')) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Budget (£)</label>
                            <div class="input-group">
                                <span class="input-group-text">£</span>
                                <input type="number" step="0.01" name="budget" class="form-control" 
                                       value="{{ old('budget', $event->budget) }}" min="0" placeholder="10000">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Event Summary (Read Only) -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-secondary text-white border-0 py-3">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Current P&L Summary</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3 text-center">
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <small class="text-muted d-block mb-1">Total Revenue</small>
                                <h5 class="mb-0 text-success">£{{ number_format($event->total_revenue, 0) }}</h5>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <small class="text-muted d-block mb-1">Total Expenses</small>
                                <h5 class="mb-0 text-danger">£{{ number_format($event->total_expenses, 0) }}</h5>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <small class="text-muted d-block mb-1">Net Profit/Loss</small>
                                @php $profit = $event->net_profit; @endphp
                                <h5 class="mb-0 {{ $profit >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $profit >= 0 ? '' : '-' }}£{{ number_format(abs($profit), 0) }}
                                </h5>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <small class="text-muted d-block mb-1">Budget Used</small>
                                @php 
                                    $budgetUsed = $event->budget > 0 ? ($event->total_expenses / $event->budget) * 100 : 0;
                                @endphp
                                <h5 class="mb-0 {{ $budgetUsed > 100 ? 'text-danger' : ($budgetUsed > 80 ? 'text-warning' : 'text-success') }}">
                                    {{ number_format($budgetUsed, 0) }}%
                                </h5>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('pnl.events.show', $event) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-chart-bar me-1"></i> View Full P&L Report
                        </a>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-save me-1"></i> Update Event
                </button>
                <a href="{{ route('pnl.events.show', $event) }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
