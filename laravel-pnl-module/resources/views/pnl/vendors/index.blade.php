@extends('adminlte::page')

@section('title', 'Vendors & Artists')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-users"></i> Vendors & Artists</h1>
        <div>
            <a href="{{ route('pnl.vendors.export', ['format' => 'xlsx']) }}" class="btn btn-success">
                <i class="fas fa-download"></i> Export Contacts
            </a>
            <a href="{{ route('pnl.vendors.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New
            </a>
        </div>
    </div>
@stop

@section('content')
    <!-- Filters -->
    <div class="card card-outline card-primary mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('pnl.vendors.index') }}" class="row align-items-end">
                <div class="col-md-4">
                    <div class="form-group mb-0">
                        <label>Search</label>
                        <input type="text" name="search" class="form-control" placeholder="Name, email, phone..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group mb-0">
                        <label>Type</label>
                        <select name="type" class="form-control">
                            <option value="">All Types</option>
                            @foreach($vendorTypes as $key => $label)
                                <option value="{{ $key }}" {{ request('type') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-0">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="">All</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filter</button>
                    <a href="{{ route('pnl.vendors.index') }}" class="btn btn-secondary"><i class="fas fa-times"></i></a>
                </div>
            </form>
        </div>
    </div>

    <!-- Vendors Table -->
    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Contact</th>
                        <th>Status</th>
                        <th class="text-right">Total Paid</th>
                        <th class="text-right">Pending</th>
                        <th width="130">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vendors as $vendor)
                        <tr>
                            <td>
                                <strong><a href="{{ route('pnl.vendors.show', $vendor) }}">{{ $vendor->display_name }}</a></strong>
                                @if($vendor->business_name && $vendor->full_name !== $vendor->business_name)
                                    <br><small class="text-muted">{{ $vendor->full_name }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-info">{{ ucfirst($vendor->type) }}</span>
                            </td>
                            <td>
                                <i class="fas fa-envelope"></i> {{ $vendor->email }}<br>
                                @if($vendor->phone)
                                    <i class="fas fa-phone"></i> {{ $vendor->phone }}
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $vendor->is_active ? 'success' : 'secondary' }}">
                                    {{ $vendor->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="text-right text-success">₹{{ number_format($vendor->total_paid, 0) }}</td>
                            <td class="text-right text-warning">₹{{ number_format($vendor->total_pending, 0) }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('pnl.vendors.show', $vendor) }}" class="btn btn-sm btn-outline-primary" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('pnl.vendors.edit', $vendor) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                            onclick="confirmDelete('{{ route('pnl.vendors.destroy', $vendor) }}')" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-user-plus fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">No vendors/artists found</h5>
                                <a href="{{ route('pnl.vendors.create') }}" class="btn btn-primary mt-2">
                                    <i class="fas fa-plus"></i> Add Your First Vendor
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($vendors->hasPages())
            <div class="card-footer">{{ $vendors->links() }}</div>
        @endif
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white"><i class="fas fa-exclamation-triangle"></i> Confirm Delete</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">Are you sure you want to delete this vendor/artist?</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <form id="deleteForm" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        function confirmDelete(url) {
            document.getElementById('deleteForm').action = url;
            $('#deleteModal').modal('show');
        }
    </script>
@stop
