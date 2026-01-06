@extends('adminlte::page')

@section('title', 'Audit Log')

@section('content_header')
    <h1><i class="fas fa-history"></i> Audit Log</h1>
@stop

@section('content')
    <!-- Filters -->
    <div class="card card-outline card-primary mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('pnl.audit.index') }}" class="row align-items-end">
                <div class="col-md-2">
                    <div class="form-group mb-0">
                        <label>Action</label>
                        <select name="action" class="form-control">
                            <option value="">All Actions</option>
                            @foreach($actions as $key => $label)
                                <option value="{{ $key }}" {{ request('action') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-0">
                        <label>Model</label>
                        <select name="model_type" class="form-control">
                            <option value="">All Models</option>
                            <option value="Event" {{ request('model_type') === 'Event' ? 'selected' : '' }}>Events</option>
                            <option value="Expense" {{ request('model_type') === 'Expense' ? 'selected' : '' }}>Expenses</option>
                            <option value="Payment" {{ request('model_type') === 'Payment' ? 'selected' : '' }}>Payments</option>
                            <option value="Vendor" {{ request('model_type') === 'Vendor' ? 'selected' : '' }}>Vendors</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-0">
                        <label>From Date</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-0">
                        <label>To Date</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filter</button>
                    <a href="{{ route('pnl.audit.index') }}" class="btn btn-secondary"><i class="fas fa-times"></i></a>
                </div>
            </form>
        </div>
    </div>

    <!-- Audit Logs -->
    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>Date/Time</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Model</th>
                        <th>Changes</th>
                        <th width="80"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td>
                                <small>
                                    {{ $log->created_at->format('d M Y') }}<br>
                                    {{ $log->created_at->format('h:i A') }}
                                </small>
                            </td>
                            <td>{{ $log->user?->name ?? 'System' }}</td>
                            <td>
                                <span class="badge badge-{{ $log->action_color }}">{{ ucfirst($log->action) }}</span>
                            </td>
                            <td>
                                {{ class_basename($log->auditable_type) }}
                                <br><small class="text-muted">{{ substr($log->auditable_id, 0, 8) }}...</small>
                            </td>
                            <td>
                                @if($log->action === 'updated' && $log->changed_fields)
                                    @foreach(array_slice($log->changed_fields, 0, 2) as $field => $change)
                                        <small><strong>{{ $field }}:</strong> {{ $change['old'] ?? '-' }} → {{ $change['new'] ?? '-' }}</small><br>
                                    @endforeach
                                    @if(count($log->changed_fields) > 2)
                                        <small class="text-muted">+{{ count($log->changed_fields) - 2 }} more</small>
                                    @endif
                                @elseif($log->reason)
                                    <small>{{ $log->reason }}</small>
                                @else
                                    <small class="text-muted">-</small>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('pnl.audit.show', $log) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-history fa-3x mb-3"></i>
                                <p>No audit logs found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
            <div class="card-footer">{{ $logs->links() }}</div>
        @endif
    </div>
@stop
