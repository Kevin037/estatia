<x-admin-layout>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Cluster Details</h1>
            <p class="mt-1 text-sm text-gray-600">{{ $cluster->name }}</p>
        </div>
        <a href="{{ route('clusters.index') }}" class="btn btn-secondary">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Back to List
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Main Info -->
        <div class="lg:col-span-2">
            <!-- Basic Information -->
            <div class="card">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Cluster Name</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $cluster->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Project</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $cluster->project->name ?? '-' }}</dd>
                    </div>
                    @if($cluster->road_width)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Road Width</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $cluster->road_width }} m</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Total Units</dt>
                        <dd class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                {{ $cluster->units->count() }} units
                            </span>
                        </dd>
                    </div>
                </dl>

                @if($cluster->desc)
                <div class="mt-4">
                    <dt class="text-sm font-medium text-gray-500">Description</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $cluster->desc }}</dd>
                </div>
                @endif

                @if($cluster->facilities)
                <div class="mt-4">
                    <dt class="text-sm font-medium text-gray-500">Facilities</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $cluster->facilities }}</dd>
                </div>
                @endif
            </div>
        </div>

        <!-- Sidebar Statistics -->
        <div class="space-y-6">
            <!-- Units Statistics -->
            <div class="card">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Units Statistics</h3>
                <dl class="space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Total Units</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ $cluster->units->count() }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Available</dt>
                        <dd class="text-sm font-semibold text-green-600">{{ $cluster->units->where('status', 'available')->count() }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Reserved</dt>
                        <dd class="text-sm font-semibold text-yellow-600">{{ $cluster->units->where('status', 'reserved')->count() }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Sold</dt>
                        <dd class="text-sm font-semibold text-blue-600">{{ $cluster->units->where('status', 'sold')->count() }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Handed Over</dt>
                        <dd class="text-sm font-semibold text-gray-600">{{ $cluster->units->where('status', 'handed_over')->count() }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Price Range -->
            @if($cluster->units->count() > 0)
            <div class="card">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Price Range</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Minimum Price</dt>
                        <dd class="mt-1 text-lg font-bold text-emerald-600">Rp {{ number_format($cluster->units->min('price'), 0, ',', '.') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Maximum Price</dt>
                        <dd class="mt-1 text-lg font-bold text-emerald-600">Rp {{ number_format($cluster->units->max('price'), 0, ',', '.') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Average Price</dt>
                        <dd class="mt-1 text-lg font-bold text-emerald-600">Rp {{ number_format($cluster->units->avg('price'), 0, ',', '.') }}</dd>
                    </div>
                </dl>
            </div>
            @endif
        </div>
    </div>

    <!-- Units in This Cluster -->
    <div class="card">
        <div class="mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Units in This Cluster</h3>
            
            <!-- Filter Panel for Units -->
            <div class="mb-4">
                <button type="button" onclick="$('#unitsFilterPanel').slideToggle()" class="text-sm text-emerald-600 hover:text-emerald-700 mb-3">
                    Toggle Unit Filters
                </button>
                
                <div id="unitsFilterPanel" class="hidden grid grid-cols-1 md:grid-cols-5 gap-4 p-4 bg-gray-50 rounded-lg">
                    <!-- Type Filter -->
                    <div>
                        <label for="filter_type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                        <select id="filter_type" class="form-select w-full">
                            <option value="">All Types</option>
                            @foreach($types as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label for="filter_status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select id="filter_status" class="form-select w-full">
                            <option value="">All Status</option>
                            <option value="available">Available</option>
                            <option value="reserved">Reserved</option>
                            <option value="sold">Sold</option>
                            <option value="handed_over">Handed Over</option>
                        </select>
                    </div>

                    <!-- Min Price -->
                    <div>
                        <label for="filter_min_price" class="block text-sm font-medium text-gray-700 mb-1">Min Price</label>
                        <input type="number" id="filter_min_price" class="form-input w-full" placeholder="Min price" step="1000">
                    </div>

                    <!-- Max Price -->
                    <div>
                        <label for="filter_max_price" class="block text-sm font-medium text-gray-700 mb-1">Max Price</label>
                        <input type="number" id="filter_max_price" class="form-input w-full" placeholder="Max price" step="1000">
                    </div>

                    <!-- Actions -->
                    <div class="flex items-end space-x-2">
                        <button type="button" onclick="applyUnitFilters()" class="btn btn-primary btn-sm">
                            Apply
                        </button>
                        <button type="button" onclick="resetUnitFilters()" class="btn btn-secondary btn-sm">
                            Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table id="units-table" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Photos</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize Select2 for filters
            $('#filter_type, #filter_status').select2({
                theme: 'bootstrap-5',
                placeholder: function() {
                    return $(this).find('option:first').text();
                }
            });

            // Initialize Units DataTable
            var unitsTable = $('#units-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('clusters.show', $cluster) }}",
                    data: function(d) {
                        d.type_id = $('#filter_type').val();
                        d.status = $('#filter_status').val();
                        d.min_price = $('#filter_min_price').val();
                        d.max_price = $('#filter_max_price').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'name', name: 'name' },
                    { data: 'no', name: 'no' },
                    { data: 'type_name', name: 'product.type.name' },
                    { data: 'price', name: 'price' },
                    { data: 'photos_count', name: 'photos_count', orderable: false },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                order: [[2, 'asc']], // Order by unit number
                pageLength: 25,
                responsive: true,
                language: {
                    emptyTable: "No units found in this cluster",
                    zeroRecords: "No matching units found"
                }
            });

            window.applyUnitFilters = function() {
                unitsTable.draw();
            };

            window.resetUnitFilters = function() {
                $('#filter_type').val('').trigger('change');
                $('#filter_status').val('').trigger('change');
                $('#filter_min_price').val('');
                $('#filter_max_price').val('');
                unitsTable.draw();
            };
        });
    </script>
    @endpush
</x-admin-layout>
