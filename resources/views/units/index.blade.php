<x-admin-layout>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Units</h1>
            <p class="mt-1 text-sm text-gray-600">Manage and view all property units</p>
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
            <div>
                <label for="type_id" class="form-label">Type</label>
                <select name="type_id" id="type_id" class="form-input">
                    <option value="">All Types</option>
                    @foreach($types as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-input">
                    <option value="">All Status</option>
                    <option value="available">Available</option>
                    <option value="reserved">Reserved</option>
                    <option value="sold">Sold</option>
                    <option value="handed_over">Handed Over</option>
                </select>
            </div>
            <div>
                <label for="min_price" class="form-label">Min Price (Rp)</label>
                <input type="number" name="min_price" id="min_price" class="form-input" placeholder="0" step="1000" min="0">
            </div>
            <div>
                <label for="max_price" class="form-label">Max Price (Rp)</label>
                <input type="number" name="max_price" id="max_price" class="form-input" placeholder="0" step="1000" min="0">
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

    <!-- Units Table -->
    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Units List</h3>
            <div class="flex items-center gap-x-3">
                <button type="button" id="filterBtn" class="btn btn-secondary">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z" />
                    </svg>
                    Filters
                </button>
                <button type="button" id="exportBtn" class="btn btn-secondary">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    Export Excel
                </button>
            </div>
        </div>
        <div class="table-container">
            <table id="unitsTable" class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Name</th>
                        <th>Unit No</th>
                        <th>Project Name</th>
                        <th>Cluster Name</th>
                        <th>Type</th>
                        <th>Price</th>
                        <th>Photos</th>
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
            $('#project_id, #type_id, #status').select2({
                width: '100%'
            });

            // Initialize DataTable
            const table = $('#unitsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('units.index') }}",
                    data: function(d) {
                        d.project_id = $('#project_id').val();
                        d.type_id = $('#type_id').val();
                        d.status = $('#status').val();
                        d.min_price = $('#min_price').val();
                        d.max_price = $('#max_price').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'name', name: 'name' },
                    { data: 'no', name: 'no' },
                    { data: 'project_name', name: 'project_name' },
                    { data: 'cluster_name', name: 'cluster_name' },
                    { data: 'type_name', name: 'type_name' },
                    { data: 'price', name: 'price' },
                    { data: 'photos_count', name: 'photos_count', orderable: false, searchable: false },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
                ],
                pageLength: 10,
                order: [[1, 'asc']],
                language: {
                    processing: '<div class="flex justify-center items-center"><svg class="animate-spin h-8 w-8 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></div>',
                    emptyTable: "No units found",
                    zeroRecords: "No matching units found"
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
                $('#project_id, #type_id, #status').val(null).trigger('change');
                table.ajax.reload();
            });

            // Export functionality
            $('#exportBtn').click(function() {
                const projectId = $('#project_id').val();
                const typeId = $('#type_id').val();
                const status = $('#status').val();
                const minPrice = $('#min_price').val();
                const maxPrice = $('#max_price').val();
                
                let url = '{{ route('units.export') }}?';
                if (projectId) url += 'project_id=' + projectId + '&';
                if (typeId) url += 'type_id=' + typeId + '&';
                if (status) url += 'status=' + status + '&';
                if (minPrice) url += 'min_price=' + minPrice + '&';
                if (maxPrice) url += 'max_price=' + maxPrice;
                
                window.location.href = url;
            });
        });
    </script>
    @endpush
</x-admin-layout>
