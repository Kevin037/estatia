<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">
                    Purchase Orders
                </h2>
                <p class="mt-1 text-sm text-gray-600">Manage material procurement for projects</p>
            </div>
            <div class="flex items-center gap-x-3">
                <button type="button" id="filterToggle" class="btn btn-secondary">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z" />
                    </svg>
                    Filter
                </button>
                <button type="button" id="exportBtn" class="btn btn-secondary">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    Export Excel
                </button>
                <a href="{{ route('purchase-orders.create') }}" class="btn btn-primary">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Add Purchase Order
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Filter Card -->
    <div id="filterCard" class="card mb-6" style="display: none;">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Filter Purchase Orders</h3>
        <form id="filterForm" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="filter_status" class="form-label">Status</label>
                <select id="filter_status" name="status" class="form-input">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="completed">Completed</option>
                </select>
            </div>
            <div>
                <label for="filter_start_date" class="form-label">Start Date</label>
                <input 
                    type="date" 
                    id="filter_start_date" 
                    name="start_date" 
                    class="form-input"
                >
            </div>
            <div>
                <label for="filter_end_date" class="form-label">End Date</label>
                <input 
                    type="date" 
                    id="filter_end_date" 
                    name="end_date" 
                    class="form-input"
                >
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit" class="btn btn-primary flex-1">
                    Apply
                </button>
                <button type="button" id="resetFilter" class="btn btn-secondary flex-1">
                    Reset
                </button>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="card">
        <div class="table-container">
            <table id="purchaseOrdersTable" class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Transaction No</th>
                        <th>Supplier</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- DataTables will populate this -->
                </tbody>
            </table>
        </div>
    </div>

    @push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    @endpush

    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            const table = $('#purchaseOrdersTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('purchase-orders.index') }}',
                    data: function(d) {
                        d.status = $('#filter_status').val();
                        d.start_date = $('#filter_start_date').val();
                        d.end_date = $('#filter_end_date').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'no', name: 'no' },
                    { data: 'supplier_id', name: 'supplier.name' },
                    { data: 'dt', name: 'dt' },
                    { data: 'total', name: 'total' },
                    { data: 'status', name: 'status' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center' }
                ],
                order: [[3, 'desc']],
                pageLength: 10,
                responsive: true
            });

            // Filter toggle
            $('#filterToggle').click(function() {
                $('#filterCard').slideToggle();
            });

            // Apply filter
            $('#filterForm').submit(function(e) {
                e.preventDefault();
                table.ajax.reload();
            });

            // Reset filter
            $('#resetFilter').click(function() {
                $('#filterForm')[0].reset();
                table.ajax.reload();
            });

            // Export functionality
            $('#exportBtn').click(function() {
                const status = $('#filter_status').val();
                const startDate = $('#filter_start_date').val();
                const endDate = $('#filter_end_date').val();
                
                let url = '{{ route('purchase-orders.export') }}?';
                if (status) url += 'status=' + status + '&';
                if (startDate) url += 'start_date=' + startDate + '&';
                if (endDate) url += 'end_date=' + endDate;
                
                window.location.href = url;
            });

            // Delete functionality with SweetAlert2
            $(document).on('click', '.delete-purchase-order', function(e) {
                e.preventDefault();
                const url = $(this).data('url');
                const no = $(this).data('no');

                Swal.fire({
                    title: 'Delete Purchase Order?',
                    html: `Are you sure you want to delete Purchase Order <strong>${no}</strong>?<br><span class="text-sm text-gray-600">This will also revert material stock changes.</span>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: '#ef4444',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: response.message,
                                    icon: 'success',
                                    confirmButtonColor: '#10b981'
                                });
                                table.ajax.reload();
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    title: 'Error!',
                                    text: xhr.responseJSON?.message || 'Failed to delete purchase order',
                                    icon: 'error',
                                    confirmButtonColor: '#ef4444'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
    @endpush
</x-admin-layout>
