<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900">
                Customers Management
            </h2>
            <div class="flex items-center gap-x-3">
                <button type="button" id="filterBtn" class="btn btn-secondary">
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
                <a href="{{ route('customers.create') }}" class="btn btn-primary">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Add Customer
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Filter Card -->
    <div id="filterCard" class="card mb-6" style="display: none;">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Filter Customers</h3>
        <form id="filterForm" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="form-label">Start Date</label>
                <input type="date" name="start_date" id="start_date" class="form-input">
            </div>
            <div>
                <label class="form-label">End Date</label>
                <input type="date" name="end_date" id="end_date" class="form-input">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="btn btn-primary">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                    Apply
                </button>
                <button type="button" id="resetFilter" class="btn btn-secondary">
                    Reset
                </button>
            </div>
        </form>
    </div>

    <!-- Customers Table -->
    <div class="card">
        <div class="table-container">
            <table id="customersTable" class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Name</th>
                        <th>Phone Number</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
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
            const table = $('#customersTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('customers.index') }}",
                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'name', name: 'name' },
                    { data: 'phone', name: 'phone' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
                ],
                order: [[1, 'asc']],
                pageLength: 10,
                responsive: true
            });

            // Toggle Filter Card
            $('#filterBtn').click(function() {
                $('#filterCard').slideToggle();
            });

            // Apply Filter
            $('#filterForm').submit(function(e) {
                e.preventDefault();
                table.ajax.reload();
            });

            // Reset Filter
            $('#resetFilter').click(function() {
                $('#start_date').val('');
                $('#end_date').val('');
                table.ajax.reload();
            });

            // Export Excel
            $('#exportBtn').click(function() {
                const startDate = $('#start_date').val();
                const endDate = $('#end_date').val();
                let url = "{{ route('customers.export') }}";
                
                if (startDate && endDate) {
                    url += `?start_date=${startDate}&end_date=${endDate}`;
                }
                
                window.location.href = url;
            });

            // Delete Customer
            $(document).on('click', '.delete-customer', function(e) {
                e.preventDefault();
                const deleteUrl = $(this).data('url');
                
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#059669',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: deleteUrl,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire(
                                    'Deleted!',
                                    response.message,
                                    'success'
                                );
                                table.ajax.reload();
                            },
                            error: function(xhr) {
                                Swal.fire(
                                    'Error!',
                                    xhr.responseJSON.message || 'Failed to delete customer.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });
        });
    </script>
    @endpush
</x-admin-layout>

