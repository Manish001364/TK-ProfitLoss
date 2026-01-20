@extends('layouts.organiser_layout')

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0"><i class="fas fa-history"></i> Audit Log</h1>
            <a href="{{ route('pnl.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('pnl.audit.index') }}" class="row align-items-end">
                    <div class="col-md-3">
                        <div class="form-group mb-2 mb-md-0">
                            <label>Action</label>
                            <select name="action" class="form-control">
                                <option value="">All Actions</option>
                                <option value="created" {{ request('action') === 'created' ? 'selected' : '' }}>Created</option>
                                <option value="updated" {{ request('action') === 'updated' ? 'selected' : '' }}>Updated</option>
                                <option value="deleted" {{ request('action') === 'deleted' ? 'selected' : '' }}>Deleted</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-2 mb-md-0">
                            <label>From Date</label>
                            <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-2 mb-md-0">
                            <label>To Date</label>
                            <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filter</button>
                        <a href="{{ route('pnl.audit.index') }}" class="btn btn-secondary"><i class="fas fa-times"></i></a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Audit Log Table -->
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date/Time</th>
                                <th>User</th>
                                <th>Action</th>
                                <th>Item</th>
                                <th>Changes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                                <tr>
                                    <td>
                                        {{ $log->created_at->format('d M Y') }}
                                        <br><small class="text-muted">{{ $log->created_at->format('H:i:s') }}</small>
                                    </td>
                                    <td>{{ $log->user?->name ?? 'System' }}</td>
                                    <td>
                                        @php
                                            $actionColors = [
                                                'created' => 'success',
                                                'updated' => 'warning',
                                                'deleted' => 'danger',
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $actionColors[$log->action] ?? 'secondary' }}">
                                            {{ ucfirst($log->action) }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong>{{ class_basename($log->loggable_type) }}</strong>
                                        @if($log->loggable)
                                            <br><small>{{ $log->loggable->title ?? $log->loggable->name ?? 'ID: ' . $log->loggable_id }}</small>
                                        @else
                                            <br><small class="text-muted">ID: {{ $log->loggable_id }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($log->old_values || $log->new_values)
                                            <button type="button" class="btn btn-sm btn-outline-info" 
                                                    onclick="showChanges({{ json_encode($log->old_values) }}, {{ json_encode($log->new_values) }})">
                                                <i class="fas fa-eye"></i> View Changes
                                            </button>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <i class="fas fa-history fa-4x text-muted mb-3"></i>
                                        <h5 class="text-muted">No audit logs found</h5>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($logs->hasPages())
                <div class="card-footer">{{ $logs->links() }}</div>
            @endif
        </div>

        <!-- Changes Modal -->
        <div class="modal fade" id="changesModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-exchange-alt"></i> Changes</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-danger">Old Values</h6>
                                <pre id="oldValues" class="bg-light p-3 rounded"></pre>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-success">New Values</h6>
                                <pre id="newValues" class="bg-light p-3 rounded"></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('customjs')
    <script>
        function showChanges(oldValues, newValues) {
            document.getElementById('oldValues').textContent = JSON.stringify(oldValues, null, 2);
            document.getElementById('newValues').textContent = JSON.stringify(newValues, null, 2);
            var changesModal = new bootstrap.Modal(document.getElementById('changesModal'));
            changesModal.show();
        }
    </script>
@endsection
