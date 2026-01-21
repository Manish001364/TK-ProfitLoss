@extends('layouts.organiser_layout')

@section('content')
    <div class="container py-4" style="max-width: 900px;">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">Audit Log Details</h4>
                <p class="text-muted small mb-0">View change history</p>
            </div>
            <a href="{{ route('pnl.audit.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0"><i class="fas fa-history me-2 text-info"></i>Log Entry</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td class="text-muted" width="150">Action:</td>
                        <td><span class="badge bg-{{ $auditLog->action === 'created' ? 'success' : ($auditLog->action === 'deleted' ? 'danger' : 'warning') }}">{{ ucfirst($auditLog->action) }}</span></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Type:</td>
                        <td>{{ class_basename($auditLog->auditable_type) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">User:</td>
                        <td>{{ $auditLog->user->name ?? 'System' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Date:</td>
                        <td>{{ $auditLog->created_at->format('d M Y H:i:s') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">IP Address:</td>
                        <td><code>{{ $auditLog->ip_address ?? 'N/A' }}</code></td>
                    </tr>
                </table>

                @if($auditLog->old_values)
                <h6 class="mt-4">Old Values:</h6>
                <pre class="bg-light p-3 rounded small">{{ json_encode($auditLog->old_values, JSON_PRETTY_PRINT) }}</pre>
                @endif

                @if($auditLog->new_values)
                <h6 class="mt-4">New Values:</h6>
                <pre class="bg-light p-3 rounded small">{{ json_encode($auditLog->new_values, JSON_PRETTY_PRINT) }}</pre>
                @endif
            </div>
        </div>
    </div>
@endsection
