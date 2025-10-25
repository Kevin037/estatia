<x-admin-layout>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Order</h1>
            <p class="mt-1 text-sm text-gray-600">Update order information</p>
        </div>
        <a href="{{ route('orders.show', $order) }}" class="btn btn-secondary">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Back to Order
        </a>
    </div>

    <form action="{{ route('orders.update', $order) }}" method="POST" id="orderForm">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Form -->
            <div class="lg:col-span-2">
                <div class="card">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Order Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Date -->
                        <div>
                            <label for="dt" class="form-label required">Order Date</label>
                            <input type="date" name="dt" id="dt" class="form-input @error('dt') border-red-500 @enderror" 
                                   value="{{ old('dt', $order->dt ? $order->dt->format('Y-m-d') : '') }}" required>
                            @error('dt')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Customer -->
                        <div>
                            <label for="customer_id" class="form-label required">Customer</label>
                            <select name="customer_id" id="customer_id" class="form-input @error('customer_id') border-red-500 @enderror" required>
                                <option value="">Select Customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ old('customer_id', $order->customer_id) == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('customer_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Project -->
                        <div>
                            <label for="project_id" class="form-label required">Project</label>
                            <select name="project_id" id="project_id" class="form-input @error('project_id') border-red-500 @enderror" required>
                                <option value="">Select Project</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ old('project_id', $order->project_id) == $project->id ? 'selected' : '' }}>
                                        {{ $project->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('project_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Cluster -->
                        <div>
                            <label for="cluster_id" class="form-label required">Cluster</label>
                            <select name="cluster_id" id="cluster_id" class="form-input @error('cluster_id') border-red-500 @enderror" required>
                                <option value="">Select Cluster</option>
                                @foreach($clusters as $cluster)
                                    <option value="{{ $cluster->id }}" {{ old('cluster_id', $order->cluster_id) == $cluster->id ? 'selected' : '' }}>
                                        {{ $cluster->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('cluster_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Unit -->
                        <div class="md:col-span-2">
                            <label for="unit_id" class="form-label required">Unit</label>
                            <select name="unit_id" id="unit_id" class="form-input @error('unit_id') border-red-500 @enderror" required>
                                <option value="">Select Unit</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}" {{ old('unit_id', $order->unit_id) == $unit->id ? 'selected' : '' }}>
                                        {{ $unit->no }} - {{ $unit->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('unit_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="form-label required">Status</label>
                            <select name="status" id="status" class="form-input @error('status') border-red-500 @enderror" required>
                                <option value="pending" {{ old('status', $order->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="completed" {{ old('status', $order->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div class="md:col-span-2">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea name="notes" id="notes" rows="3" class="form-input @error('notes') border-red-500 @enderror">{{ old('notes', $order->notes) }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 mt-6">
                        <a href="{{ route('orders.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <svg class="-ml-0.5 mr-1.5 h-5 w-5 hidden" id="loadingSpinner" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span id="submitText">Update Order</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Unit Preview Sidebar -->
            <div class="lg:col-span-1">
                <div class="card" id="unitPreview" style="display: none;">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Unit Details</h3>
                    
                    <div id="unitDetails">
                        <div class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Unit Name</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-semibold" id="preview_name">-</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Unit Number</dt>
                                <dd class="mt-1 text-sm text-gray-900" id="preview_no">-</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Price</dt>
                                <dd class="mt-1 text-lg font-bold text-emerald-600" id="preview_price">-</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1" id="preview_status">-</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Type</dt>
                                <dd class="mt-1 text-sm text-gray-900" id="preview_type">-</dd>
                            </div>
                        </div>

                        <!-- Product Photos -->
                        <div class="mt-4" id="productPhotosContainer" style="display: none;">
                            <dt class="text-sm font-medium text-gray-500 mb-2">Product Photos</dt>
                            <div class="flex flex-wrap gap-3" id="productPhotos"></div>
                        </div>

                        <!-- Unit Photos -->
                        <div class="mt-4" id="unitPhotosContainer" style="display: none;">
                            <dt class="text-sm font-medium text-gray-500 mb-2">Unit Photos</dt>
                            <div class="flex flex-wrap gap-3" id="unitPhotos"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    @endpush

    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('#customer_id, #project_id, #cluster_id, #unit_id, #status').select2({
                width: '100%'
            });

            // Project change -> Load clusters
            $('#project_id').on('change', function() {
                const projectId = $(this).val();
                
                // Reset cluster and unit
                $('#cluster_id').html('<option value="">Select Cluster</option>').prop('disabled', true).trigger('change');
                $('#unit_id').html('<option value="">Select Unit</option>').prop('disabled', true).trigger('change');
                $('#unitPreview').hide();

                if (projectId) {
                    // Load clusters
                    $.ajax({
                        url: '{{ route("orders.clusters") }}',
                        type: 'GET',
                        data: { project_id: projectId },
                        success: function(data) {
                            let options = '<option value="">Select Cluster</option>';
                            data.forEach(cluster => {
                                options += `<option value="${cluster.id}">${cluster.name}</option>`;
                            });
                            $('#cluster_id').html(options).prop('disabled', false);
                        }
                    });
                }
            });

            // Cluster change -> Load units
            $('#cluster_id').on('change', function() {
                const clusterId = $(this).val();
                
                // Reset unit
                $('#unit_id').html('<option value="">Select Unit</option>').prop('disabled', true).trigger('change');
                $('#unitPreview').hide();

                if (clusterId) {
                    // Load units
                    $.ajax({
                        url: '{{ route("orders.units") }}',
                        type: 'GET',
                        data: { cluster_id: clusterId },
                        success: function(data) {
                            let options = '<option value="">Select Unit</option>';
                            data.forEach(unit => {
                                options += `<option value="${unit.id}">${unit.no} - ${unit.name} (${unit.product.type.name})</option>`;
                            });
                            $('#unit_id').html(options).prop('disabled', false);
                        }
                    });
                }
            });

            // Unit change -> Load unit details
            $('#unit_id').on('change', function() {
                const unitId = $(this).val();

                if (unitId) {
                    // Load unit details
                    $.ajax({
                        url: '{{ route("orders.unit-details") }}',
                        type: 'GET',
                        data: { unit_id: unitId },
                        success: function(data) {
                            // Show preview
                            $('#unitPreview').show();

                            // Update basic info
                            $('#preview_name').text(data.name);
                            $('#preview_no').text(data.no);
                            $('#preview_price').text(data.price_formatted);
                            $('#preview_type').text(data.product.type);
                            
                            // Status badge
                            const statusColors = {
                                'available': 'bg-green-100 text-green-800',
                                'reserved': 'bg-yellow-100 text-yellow-800',
                                'sold': 'bg-blue-100 text-blue-800',
                                'handed_over': 'bg-gray-100 text-gray-800'
                            };
                            const statusColor = statusColors[data.status] || 'bg-gray-100 text-gray-800';
                            $('#preview_status').html(`<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusColor}">${data.status_label}</span>`);

                            // Product photos
                            if (data.product_photos && data.product_photos.length > 0) {
                                let photosHtml = '';
                                data.product_photos.forEach(photo => {
                                    photosHtml += `
                                        <div class="cursor-pointer rounded overflow-hidden border-2 border-gray-200 hover:border-emerald-500 transition-colors" 
                                             style="width: 15rem; height: 15rem;">
                                            <img src="${photo.url}" alt="${photo.name}" class="w-full h-full object-cover">
                                        </div>
                                    `;
                                });
                                $('#productPhotos').html(photosHtml);
                                $('#productPhotosContainer').show();
                            } else {
                                $('#productPhotosContainer').hide();
                            }

                            // Unit photos
                            if (data.unit_photos && data.unit_photos.length > 0) {
                                let photosHtml = '';
                                data.unit_photos.forEach(photo => {
                                    photosHtml += `
                                        <div class="cursor-pointer rounded overflow-hidden border-2 border-gray-200 hover:border-emerald-500 transition-colors" 
                                             style="width: 15rem; height: 15rem;">
                                            <img src="${photo.url}" alt="${photo.name}" class="w-full h-full object-cover">
                                        </div>
                                    `;
                                });
                                $('#unitPhotos').html(photosHtml);
                                $('#unitPhotosContainer').show();
                            } else {
                                $('#unitPhotosContainer').hide();
                            }
                        }
                    });
                } else {
                    $('#unitPreview').hide();
                }
            });

            // Form submission
            $('#orderForm').on('submit', function() {
                $('#loadingSpinner').removeClass('hidden').addClass('animate-spin');
                $('#submitText').text('Updating...');
                $('#submitBtn').prop('disabled', true);
            });

            // Load existing unit details on page load if unit exists
            @if(old('unit_id', $order->unit_id))
                const existingUnitId = {{ old('unit_id', $order->unit_id) }};
                $.ajax({
                    url: '{{ route("orders.unit-details") }}',
                    type: 'GET',
                    data: { unit_id: existingUnitId },
                    success: function(data) {
                        $('#preview_name').text(data.name);
                        $('#preview_no').text(data.no);
                        $('#preview_price').text(data.price_formatted);
                        $('#preview_type').text(data.product?.type?.name || '-');
                        
                        // Status badge
                        const statusColors = {
                            'available': 'bg-green-100 text-green-800',
                            'reserved': 'bg-yellow-100 text-yellow-800',
                            'sold': 'bg-blue-100 text-blue-800',
                            'handed_over': 'bg-gray-100 text-gray-800'
                        };
                        const statusColor = statusColors[data.status] || 'bg-gray-100 text-gray-800';
                        $('#preview_status').html(`<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusColor}">${data.status_label}</span>`);

                        // Product photos
                        if (data.product_photos && data.product_photos.length > 0) {
                            let photosHtml = '';
                            data.product_photos.forEach(photo => {
                                photosHtml += `
                                    <div class="cursor-pointer rounded overflow-hidden border-2 border-gray-200 hover:border-emerald-500 transition-colors" 
                                         style="width: 15rem; height: 15rem;">
                                        <img src="${photo.url}" alt="${photo.name}" class="w-full h-full object-cover">
                                    </div>
                                `;
                            });
                            $('#productPhotos').html(photosHtml);
                            $('#productPhotosContainer').show();
                        } else {
                            $('#productPhotosContainer').hide();
                        }

                        // Unit photos
                        if (data.unit_photos && data.unit_photos.length > 0) {
                            let photosHtml = '';
                            data.unit_photos.forEach(photo => {
                                photosHtml += `
                                    <div class="cursor-pointer rounded overflow-hidden border-2 border-gray-200 hover:border-emerald-500 transition-colors" 
                                         style="width: 15rem; height: 15rem;">
                                        <img src="${photo.url}" alt="${photo.name}" class="w-full h-full object-cover">
                                    </div>
                                `;
                            });
                            $('#unitPhotos').html(photosHtml);
                            $('#unitPhotosContainer').show();
                        } else {
                            $('#unitPhotosContainer').hide();
                        }

                        // Show preview
                        $('#unitPreview').show();
                    }
                });
            @endif
        });
    </script>
    @endpush
</x-admin-layout>
