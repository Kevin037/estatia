<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900">
                Create New Invoice
            </h2>
            <a href="{{ route('invoices.index') }}" class="btn btn-secondary">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Back to List
            </a>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form Column (2/3 width) -->
        <div class="lg:col-span-2">
            <div class="card">
                <form action="{{ route('invoices.store') }}" method="POST" id="invoiceForm">
                    @csrf

                    <!-- Order Selection -->
                    <div class="form-group">
                        <label for="order_id" class="form-label required">Select Order</label>
                        <select name="order_id" id="order_id" class="form-select" required>
                            <option value="">-- Select Order --</option>
                            @foreach($orders as $order)
                                <option value="{{ $order->id }}" {{ old('order_id') == $order->id ? 'selected' : '' }}>
                                    {{ $order->no }} - {{ $order->customer->name ?? 'N/A' }}
                                </option>
                            @endforeach
                        </select>
                        @error('order_id')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date -->
                    <div class="form-group">
                        <label for="dt" class="form-label required">Invoice Date</label>
                        <input type="date" name="dt" id="dt" value="{{ old('dt', date('Y-m-d')) }}" class="form-input" required>
                        @error('dt')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-end gap-x-3 pt-4 border-t">
                        <a href="{{ route('invoices.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                            Create Invoice
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Order Preview Sidebar (1/3 width) -->
        <div class="lg:col-span-1">
            <div class="card sticky top-6" id="orderPreview" style="display: none;">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-3 border-b">Order Preview</h3>
                
                <div id="orderPreviewContent">
                    <!-- Content will be loaded via AJAX -->
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--single {
            height: 42px;
            padding: 0.5rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 26px;
            color: #111827;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
        }
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #059669;
            outline: 2px solid transparent;
            outline-offset: 2px;
            box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1);
        }
        .photo-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, 15rem);
            gap: 1rem;
        }
        .photo-item {
            width: 15rem;
            height: 15rem;
            border-radius: 0.5rem;
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }
        .photo-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
    @endpush

    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('#order_id').select2({
                placeholder: '-- Select Order --',
                allowClear: true,
                width: '100%'
            });

            // Load order preview when order is selected
            $('#order_id').on('change', function() {
                const orderId = $(this).val();
                
                if (orderId) {
                    loadOrderPreview(orderId);
                } else {
                    $('#orderPreview').hide();
                }
            });

            // Load order preview on page load if order_id is already selected
            @if(old('order_id'))
                loadOrderPreview({{ old('order_id') }});
            @endif

            function loadOrderPreview(orderId) {
                $.ajax({
                    url: "{{ route('invoices.order-details') }}",
                    type: 'GET',
                    data: { order_id: orderId },
                    beforeSend: function() {
                        $('#orderPreviewContent').html('<div class="flex justify-center py-8"><svg class="animate-spin h-8 w-8 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></div>');
                        $('#orderPreview').show();
                    },
                    success: function(response) {
                        if (response.success && response.data) {
                            renderOrderPreview(response.data);
                        } else {
                            $('#orderPreviewContent').html('<p class="text-sm text-red-600">Failed to load order details.</p>');
                        }
                    },
                    error: function() {
                        $('#orderPreviewContent').html('<p class="text-sm text-red-600">Error loading order details.</p>');
                    }
                });
            }

            function renderOrderPreview(data) {
                let html = '';

                // Order Info
                html += '<div class="mb-4">';
                html += '<h4 class="text-sm font-medium text-gray-700 mb-2">Order Information</h4>';
                html += '<div class="space-y-1 text-sm">';
                html += '<p><span class="text-gray-600">Order No:</span> <span class="font-medium">' + (data.order_no || 'N/A') + '</span></p>';
                html += '<p><span class="text-gray-600">Date:</span> <span class="font-medium">' + (data.date || 'N/A') + '</span></p>';
                html += '<p><span class="text-gray-600">Total:</span> <span class="font-medium">Rp ' + (data.total_formatted || '0') + '</span></p>';
                html += '</div>';
                html += '</div>';

                // Customer Info
                if (data.customer) {
                    html += '<div class="mb-4 pb-4 border-b">';
                    html += '<h4 class="text-sm font-medium text-gray-700 mb-2">Customer</h4>';
                    html += '<div class="space-y-1 text-sm">';
                    html += '<p class="font-medium">' + (data.customer.name || 'N/A') + '</p>';
                    if (data.customer.email) html += '<p class="text-gray-600">' + data.customer.email + '</p>';
                    if (data.customer.phone) html += '<p class="text-gray-600">' + data.customer.phone + '</p>';
                    html += '</div>';
                    html += '</div>';
                }

                // Property Info
                html += '<div class="mb-4 pb-4 border-b">';
                html += '<h4 class="text-sm font-medium text-gray-700 mb-2">Property Details</h4>';
                html += '<div class="space-y-1 text-sm">';
                if (data.project) html += '<p><span class="text-gray-600">Project:</span> <span class="font-medium">' + data.project + '</span></p>';
                if (data.cluster) html += '<p><span class="text-gray-600">Cluster:</span> <span class="font-medium">' + data.cluster + '</span></p>';
                if (data.unit_no) html += '<p><span class="text-gray-600">Unit:</span> <span class="font-medium">' + data.unit_no + '</span></p>';
                html += '</div>';
                html += '</div>';

                // Unit Photos
                if (data.unit_photos && data.unit_photos.length > 0) {
                    html += '<div class="mb-4 pb-4 border-b">';
                    html += '<h4 class="text-sm font-medium text-gray-700 mb-3">Unit Photos</h4>';
                    html += '<div class="photo-grid">';
                    data.unit_photos.forEach(function(photo) {
                        html += '<div class="photo-item">';
                        html += '<img src="' + photo.url + '" alt="Unit Photo" />';
                        html += '</div>';
                    });
                    html += '</div>';
                    html += '</div>';
                }

                // Product Photos
                if (data.product_photos && data.product_photos.length > 0) {
                    html += '<div class="mb-4">';
                    html += '<h4 class="text-sm font-medium text-gray-700 mb-3">Product Photos</h4>';
                    html += '<div class="photo-grid">';
                    data.product_photos.forEach(function(photo) {
                        html += '<div class="photo-item">';
                        html += '<img src="' + photo.url + '" alt="Product Photo" />';
                        html += '</div>';
                    });
                    html += '</div>';
                    html += '</div>';
                }

                $('#orderPreviewContent').html(html);
            }
        });
    </script>
    @endpush
</x-admin-layout>
