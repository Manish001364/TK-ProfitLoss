@extends('pnl.layouts.app')

@section('pnl_content')
    <div class="container-fluid" style="max-width: 1100px;">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Vendors & Artists</h4>
            <div class="d-flex gap-2">
                <a href="{{ route('pnl.vendors.export', ['format' => 'xlsx']) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-download"></i> Export
                </a>
                <a href="{{ route('pnl.vendors.create') }}" class="btn btn-danger btn-sm">
                    <i class="fas fa-plus"></i> Add Vendor/Artist
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small text-muted mb-1">Live Search</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" id="liveSearch" class="form-control" placeholder="Type to search name, email, phone...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted mb-1">Type</label>
                        <select id="typeFilter" class="form-select form-select-sm">
                            <option value="">All Types</option>
                            @foreach($vendorTypes as $key => $label)
                                <option value="{{ $key }}" {{ request('type') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted mb-1">Status</label>
                        <select id="statusFilter" class="form-select form-select-sm">
                            <option value="">All</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="button" id="clearFilters" class="btn btn-sm btn-outline-secondary"><i class="fas fa-times"></i> Clear</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vendors Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="vendorsTable">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">Name</th>
                                <th class="border-0">Type</th>
                                <th class="border-0">Contact</th>
                                <th class="border-0">Status</th>
                                <th class="border-0 text-end">Total Paid</th>
                                <th class="border-0 text-end">Pending</th>
                                <th class="border-0" width="100">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vendors as $vendor)
                                <tr data-type="{{ $vendor->type }}" data-status="{{ $vendor->is_active ? 'active' : 'inactive' }}">
                                    <td class="border-0">
                                        <strong><a href="{{ route('pnl.vendors.show', $vendor) }}" class="text-dark">{{ $vendor->display_name }}</a></strong>
                                        @if($vendor->business_name && $vendor->full_name !== $vendor->business_name)
                                            <br><small class="text-muted">{{ $vendor->full_name }}</small>
                                        @endif
                                    </td>
                                    <td class="border-0">
                                        <span class="badge bg-info-subtle text-info">{{ $vendor->service_type_name }}</span>
                                    </td>
                                    <td class="border-0 small">
                                        <i class="fas fa-envelope text-muted me-1"></i>{{ $vendor->email ?? '-' }}<br>
                                        @if($vendor->phone)
                                            <i class="fas fa-phone text-muted me-1"></i>{{ $vendor->phone }}
                                        @endif
                                    </td>
                                    <td class="border-0">
                                        <span class="badge bg-{{ $vendor->is_active ? 'success' : 'secondary' }}-subtle text-{{ $vendor->is_active ? 'success' : 'secondary' }}">
                                            {{ $vendor->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="border-0 text-end text-success">£{{ number_format($vendor->total_paid, 0) }}</td>
                                    <td class="border-0 text-end text-warning">£{{ number_format($vendor->total_pending, 0) }}</td>
                                    <td class="border-0">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('pnl.vendors.show', $vendor) }}" class="btn btn-outline-secondary"><i class="fas fa-eye"></i></a>
                                            <a href="{{ route('pnl.vendors.edit', $vendor) }}" class="btn btn-outline-secondary"><i class="fas fa-edit"></i></a>
                                            <button type="button" class="btn btn-outline-danger" onclick="confirmDelete('{{ route('pnl.vendors.destroy', $vendor) }}')"><i class="fas fa-trash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 border-0">
                                        <i class="fas fa-user-plus fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">No vendors/artists found</h5>
                                        <a href="{{ route('pnl.vendors.create') }}" class="btn btn-danger btn-sm mt-2">
                                            <i class="fas fa-plus"></i> Add Vendor/Artist
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($vendors->hasPages())
                <div class="card-footer bg-white border-0">{{ $vendors->links() }}</div>
            @endif
        </div>

        <!-- Delete Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <h6 class="modal-title">Confirm Delete</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">Are you sure?</div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <form id="deleteForm" method="POST" style="display:inline;">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('customjs')
    <script>
        function confirmDelete(url) {
            document.getElementById('deleteForm').action = url;
            var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        }

        $(document).ready(function() {
            // Live Search - filters as you type
            function filterTable() {
                const searchText = $('#liveSearch').val().toLowerCase();
                const typeFilter = $('#typeFilter').val().toLowerCase();
                const statusFilter = $('#statusFilter').val().toLowerCase();

                $('#vendorsTable tbody tr').each(function() {
                    const row = $(this);
                    const rowText = row.text().toLowerCase();
                    const rowType = row.data('type') || '';
                    const rowStatus = row.data('status') || '';

                    let showRow = true;

                    // Text search
                    if (searchText && rowText.indexOf(searchText) === -1) {
                        showRow = false;
                    }

                    // Type filter
                    if (typeFilter && rowType !== typeFilter) {
                        showRow = false;
                    }

                    // Status filter
                    if (statusFilter && rowStatus !== statusFilter) {
                        showRow = false;
                    }

                    row.toggle(showRow);
                });
            }

            // Bind events
            $('#liveSearch').on('keyup', filterTable);
            $('#typeFilter, #statusFilter').on('change', filterTable);

            // Clear filters
            $('#clearFilters').on('click', function() {
                $('#liveSearch').val('');
                $('#typeFilter').val('');
                $('#statusFilter').val('');
                filterTable();
            });
        });
    </script>
@endsection
