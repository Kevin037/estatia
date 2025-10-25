<x-admin-layout>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Clusters</h1>
            <p class="mt-1 text-sm text-gray-600">Manage and view cluster information across all projects</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-6" id="filterCard" style="display: none;">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Filters</h3>
        </div>
        <form id="filterForm" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label for="project_id" class="form-label">Project</label>
                <select name="project_id" id="project_id" class="form-input">
                    <option value="">All Projects</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2 lg:col-span-3 flex items-end gap-2">
                <button type="submit" class="btn btn-primary">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                    Apply Filters
                </button>
                <button type="button" id="resetFilter" class="btn btn-secondary">
                    Reset
                </button>
            </div>
        </form>
    </div>

    <!-- Clusters Table -->
    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Clusters List</h3>
            <button type="button" id="filterBtn" class="btn btn-secondary">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z" />
                </svg>
                Filters
            </button>
        </div>
        <div class="table-container">
            <table id="clustersTable" class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Name</th>
                        <th>Project Name</th>
                        <th>Total Units</th>
                        <th>Price Range</th>
                        <th>Status</th>
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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    @endpush

    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('#project_id').select2({
                width: '100%'
            });

            // Initialize DataTable
            const table = $('#clustersTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('clusters.index') }}",
                    data: function(d) {
                        d.project_id = $('#project_id').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'name', name: 'name' },
                    { data: 'project_name', name: 'project.name' },
                    { data: 'total_units', name: 'total_units', orderable: false },
                    { data: 'price_range', name: 'price_range', orderable: false },
                    { data: 'status', name: 'status', orderable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
                ],
                pageLength: 10,
                order: [[1, 'asc']],
                language: {
                    processing: '<div class="flex justify-center items-center"><svg class="animate-spin h-8 w-8 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></div>',
                    emptyTable: "No clusters found",
                    zeroRecords: "No matching clusters found"
                }
            });

            // Filter toggle
            $('#filterBtn').on('click', function() {
                $('#filterCard').slideToggle(300);
            });

            // Apply filters
            $('#filterForm').on('submit', function(e) {
                e.preventDefault();
                table.ajax.reload();
            });

            // Reset filters
            $('#resetFilter').on('click', function() {
                $('#filterForm')[0].reset();
                $('#project_id').val(null).trigger('change');
                table.ajax.reload();
            });
        });
    </script>
    @endpush
</x-admin-layout>
