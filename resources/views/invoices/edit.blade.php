<x-admin-layout><x-admin-layout>

    <x-slot name="header">    <div class="min-h-screen">

        <div class="flex items-center justify-between">        <!-- Header -->

            <h2 class="text-2xl font-bold text-gray-900">        <div class="mb-6">

                Edit Invoice: {{ $invoice->no }}            <div class="flex justify-between items-center">

            </h2>                <div>

            <a href="{{ route('invoices.index') }}" class="btn btn-secondary">                    <h1 class="text-2xl font-semibold text-gray-900">Edit Invoice</h1>

                <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">                    <p class="mt-1 text-sm text-gray-600">Update invoice record</p>

                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />                </div>

                </svg>                <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-secondary">

                Back to List                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">

            </a>                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />

        </div>                    </svg>

    </x-slot>                    Back to Invoice

                </a>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">            </div>

        <!-- Form Column (2/3 width) -->        </div>

        <div class="lg:col-span-2">

            <div class="card">        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <form action="{{ route('invoices.update', $invoice->id) }}" method="POST" id="invoiceForm">            <!-- Form Section -->

                    @csrf            <div class="lg:col-span-2">

                    @method('PUT')                <form action="{{ route('invoices.update', $invoice) }}" method="POST" id="invoiceForm">

                    @csrf

                    <!-- Invoice Number (Read-only) -->                    @method('PUT')

                    <div class="form-group">                    

                        <label for="invoice_no" class="form-label">Invoice Number</label>                    <div class="card">

                        <input type="text" id="invoice_no" value="{{ $invoice->no }}" class="form-input bg-gray-50" readonly>                        <h3 class="text-lg font-medium text-gray-900 mb-4">Invoice Information</h3>

                    </div>                        

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- Order Selection -->                            <!-- Date -->

                    <div class="form-group">                            <div class="md:col-span-2">

                        <label for="order_id" class="form-label required">Select Order</label>                                <label for="dt" class="form-label required">Invoice Date</label>

                        <select name="order_id" id="order_id" class="form-select" required>                                <input type="date" name="dt" id="dt" class="form-input @error('dt') border-red-500 @enderror" 

                            <option value="">-- Select Order --</option>                                       value="{{ old('dt', $invoice->dt ? $invoice->dt->format('Y-m-d') : '') }}" required>

                            @foreach($orders as $order)                                @error('dt')

                                <option value="{{ $order->id }}"                                     <p class="mt-1 text-sm text-red-600">{{ $message }}</p>

                                    {{ (old('order_id', $invoice->order_id) == $order->id) ? 'selected' : '' }}>                                @enderror

                                    {{ $order->no }} - {{ $order->customer->name ?? 'N/A' }}                            </div>

                                </option>

                            @endforeach                            <!-- Order -->

                        </select>                            <div class="md:col-span-2">

                        @error('order_id')                                <label for="order_id" class="form-label required">Select Order</label>

                            <p class="form-error">{{ $message }}</p>                                <select name="order_id" id="order_id" class="form-input @error('order_id') border-red-500 @enderror" required>

                        @enderror                                    <option value="">Select Order</option>

                    </div>                                    @foreach($orders as $order)

                                        <option value="{{ $order->id }}" {{ old('order_id', $invoice->order_id) == $order->id ? 'selected' : '' }}>

                    <!-- Date -->                                            {{ $order->no }} - {{ $order->customer->name }} ({{ $order->dt->format('d M Y') }})

                    <div class="form-group">                                        </option>

                        <label for="dt" class="form-label required">Invoice Date</label>                                    @endforeach

                        <input type="date" name="dt" id="dt" value="{{ old('dt', $invoice->dt?->format('Y-m-d')) }}" class="form-input" required>                                </select>

                        @error('dt')                                @error('order_id')

                            <p class="form-error">{{ $message }}</p>                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>

                        @enderror                                @enderror

                    </div>                            </div>

                        </div>

                    <!-- Submit Button -->                    </div>

                    <div class="flex items-center justify-end gap-x-3 pt-4 border-t">

                        <a href="{{ route('invoices.index') }}" class="btn btn-secondary">Cancel</a>                    <div class="flex justify-end gap-2 mt-6">

                        <button type="submit" class="btn btn-primary">                        <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-secondary">Cancel</a>

                            <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">                        <button type="submit" class="btn btn-primary" id="submitBtn">

                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />                            <svg class="-ml-0.5 mr-1.5 h-5 w-5 hidden" id="loadingSpinner" fill="none" viewBox="0 0 24 24">

                            </svg>                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>

                            Update Invoice                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>

                        </button>                            </svg>

                    </div>                            <span id="submitText">Update Invoice</span>

                </form>                        </button>

            </div>                    </div>

        </div>                </form>

            </div>

        <!-- Order Preview Sidebar (1/3 width) -->

        <div class="lg:col-span-1">            <!-- Order Preview Sidebar -->

            <div class="card sticky top-6" id="orderPreview">            <div class="lg:col-span-1">

                <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-3 border-b">Order Preview</h3>                <div class="card sticky top-6" id="orderPreview" style="display: none;">

                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Order Information</h3>

                <div id="orderPreviewContent">                    

                    <!-- Content will be loaded via AJAX -->                    <div class="space-y-4">

                </div>                        <div>

            </div>                            <label class="text-sm font-medium text-gray-500">Order No</label>

        </div>                            <p class="mt-1 text-base text-gray-900 font-semibold" id="preview_order_no">-</p>

    </div>                        </div>



    @push('styles')                        <div>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />                            <label class="text-sm font-medium text-gray-500">Customer</label>

    <style>                            <p class="mt-1 text-base text-gray-900" id="preview_customer">-</p>

        .select2-container--default .select2-selection--single {                        </div>

            height: 42px;

            padding: 0.5rem 0.75rem;                        <div>

            border: 1px solid #d1d5db;                            <label class="text-sm font-medium text-gray-500">Project</label>

            border-radius: 0.5rem;                            <p class="mt-1 text-base text-gray-900" id="preview_project">-</p>

        }                        </div>

        .select2-container--default .select2-selection--single .select2-selection__rendered {

            line-height: 26px;                        <div>

            color: #111827;                            <label class="text-sm font-medium text-gray-500">Cluster</label>

        }                            <p class="mt-1 text-base text-gray-900" id="preview_cluster">-</p>

        .select2-container--default .select2-selection--single .select2-selection__arrow {                        </div>

            height: 40px;

        }                        <div>

        .select2-container--default.select2-container--focus .select2-selection--single {                            <label class="text-sm font-medium text-gray-500">Unit</label>

            border-color: #059669;                            <p class="mt-1 text-base text-gray-900" id="preview_unit">-</p>

            outline: 2px solid transparent;                        </div>

            outline-offset: 2px;

            box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1);                        <div>

        }                            <label class="text-sm font-medium text-gray-500">Type</label>

        .photo-grid {                            <p class="mt-1 text-base text-gray-900" id="preview_type">-</p>

            display: grid;                        </div>

            grid-template-columns: repeat(auto-fill, 15rem);

            gap: 1rem;                        <div class="pt-4 border-t border-gray-200">

        }                            <label class="text-sm font-medium text-gray-500">Total Amount</label>

        .photo-item {                            <p class="mt-1 text-xl text-emerald-600 font-bold" id="preview_total">-</p>

            width: 15rem;                        </div>

            height: 15rem;

            border-radius: 0.5rem;                        <!-- Product Photos -->

            overflow: hidden;                        <div id="productPhotosContainer" style="display: none;">

            border: 1px solid #e5e7eb;                            <label class="text-sm font-medium text-gray-500 mb-2 block">Product Photos</label>

        }                            <div class="flex flex-wrap gap-2" id="productPhotos"></div>

        .photo-item img {                        </div>

            width: 100%;

            height: 100%;                        <!-- Unit Photos -->

            object-fit: cover;                        <div id="unitPhotosContainer" style="display: none;">

        }                            <label class="text-sm font-medium text-gray-500 mb-2 block">Unit Photos</label>

    </style>                            <div class="flex flex-wrap gap-2" id="unitPhotos"></div>

    @endpush                        </div>

                    </div>

    @push('scripts')                </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>            </div>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>        </div>

        </div>

    <script>

        $(document).ready(function() {    @push('scripts')

            // Initialize Select2    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

            $('#order_id').select2({    <script>

                placeholder: '-- Select Order --',        $(document).ready(function() {

                allowClear: true,            // Initialize Select2

                width: '100%'            $('#order_id').select2({

            });                placeholder: 'Select Order',

                allowClear: true,

            // Load order preview when order is selected                width: '100%'

            $('#order_id').on('change', function() {            });

                const orderId = $(this).val();

                            // Load order details when order is selected

                if (orderId) {            $('#order_id').on('change', function() {

                    loadOrderPreview(orderId);                const orderId = $(this).val();

                } else {                

                    $('#orderPreview').hide();                if (orderId) {

                }                    $.ajax({

            });                        url: '{{ route("invoices.order-details") }}',

                        type: 'GET',

            // Load order preview on page load with current invoice's order                        data: { order_id: orderId },

            @if($invoice->order_id)                        success: function(data) {

                loadOrderPreview({{ $invoice->order_id }});                            $('#preview_order_no').text(data.order_no);

            @endif                            $('#preview_customer').text(data.customer.name);

                            $('#preview_project').text(data.project.name);

            function loadOrderPreview(orderId) {                            $('#preview_cluster').text(data.cluster.name);

                $.ajax({                            $('#preview_unit').text(data.unit.no + ' - ' + data.unit.name);

                    url: "{{ route('invoices.order-details') }}",                            $('#preview_type').text(data.unit.type);

                    type: 'GET',                            $('#preview_total').text(data.total_formatted);

                    data: { order_id: orderId },

                    beforeSend: function() {                            // Product photos

                        $('#orderPreviewContent').html('<div class="flex justify-center py-8"><svg class="animate-spin h-8 w-8 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></div>');                            if (data.product_photos && data.product_photos.length > 0) {

                        $('#orderPreview').show();                                let photosHtml = '';

                    },                                data.product_photos.forEach(photo => {

                    success: function(response) {                                    photosHtml += `

                        if (response.success && response.data) {                                        <div class="cursor-pointer rounded overflow-hidden border-2 border-gray-200 hover:border-emerald-500 transition-colors" 

                            renderOrderPreview(response.data);                                             style="width: 15rem; height: 15rem;">

                        } else {                                            <img src="${photo.url}" alt="${photo.name}" class="w-full h-full object-cover">

                            $('#orderPreviewContent').html('<p class="text-sm text-red-600">Failed to load order details.</p>');                                        </div>

                        }                                    `;

                    },                                });

                    error: function() {                                $('#productPhotos').html(photosHtml);

                        $('#orderPreviewContent').html('<p class="text-sm text-red-600">Error loading order details.</p>');                                $('#productPhotosContainer').show();

                    }                            } else {

                });                                $('#productPhotosContainer').hide();

            }                            }



            function renderOrderPreview(data) {                            // Unit photos

                let html = '';                            if (data.unit_photos && data.unit_photos.length > 0) {

                                let photosHtml = '';

                // Order Info                                data.unit_photos.forEach(photo => {

                html += '<div class="mb-4">';                                    photosHtml += `

                html += '<h4 class="text-sm font-medium text-gray-700 mb-2">Order Information</h4>';                                        <div class="cursor-pointer rounded overflow-hidden border-2 border-gray-200 hover:border-emerald-500 transition-colors" 

                html += '<div class="space-y-1 text-sm">';                                             style="width: 15rem; height: 15rem;">

                html += '<p><span class="text-gray-600">Order No:</span> <span class="font-medium">' + (data.order_no || 'N/A') + '</span></p>';                                            <img src="${photo.url}" alt="${photo.name}" class="w-full h-full object-cover">

                html += '<p><span class="text-gray-600">Date:</span> <span class="font-medium">' + (data.date || 'N/A') + '</span></p>';                                        </div>

                html += '<p><span class="text-gray-600">Total:</span> <span class="font-medium">Rp ' + (data.total_formatted || '0') + '</span></p>';                                    `;

                html += '</div>';                                });

                html += '</div>';                                $('#unitPhotos').html(photosHtml);

                                $('#unitPhotosContainer').show();

                // Customer Info                            } else {

                if (data.customer) {                                $('#unitPhotosContainer').hide();

                    html += '<div class="mb-4 pb-4 border-b">';                            }

                    html += '<h4 class="text-sm font-medium text-gray-700 mb-2">Customer</h4>';

                    html += '<div class="space-y-1 text-sm">';                            $('#orderPreview').show();

                    html += '<p class="font-medium">' + (data.customer.name || 'N/A') + '</p>';                        }

                    if (data.customer.email) html += '<p class="text-gray-600">' + data.customer.email + '</p>';                    });

                    if (data.customer.phone) html += '<p class="text-gray-600">' + data.customer.phone + '</p>';                } else {

                    html += '</div>';                    $('#orderPreview').hide();

                    html += '</div>';                }

                }            });



                // Property Info            // Form submission

                html += '<div class="mb-4 pb-4 border-b">';            $('#invoiceForm').on('submit', function() {

                html += '<h4 class="text-sm font-medium text-gray-700 mb-2">Property Details</h4>';                $('#loadingSpinner').removeClass('hidden').addClass('animate-spin');

                html += '<div class="space-y-1 text-sm">';                $('#submitText').text('Updating...');

                if (data.project) html += '<p><span class="text-gray-600">Project:</span> <span class="font-medium">' + data.project + '</span></p>';                $('#submitBtn').prop('disabled', true);

                if (data.cluster) html += '<p><span class="text-gray-600">Cluster:</span> <span class="font-medium">' + data.cluster + '</span></p>';            });

                if (data.unit_no) html += '<p><span class="text-gray-600">Unit:</span> <span class="font-medium">' + data.unit_no + '</span></p>';

                html += '</div>';            // Load existing order details on page load

                html += '</div>';            @if(old('order_id', $invoice->order_id))

                const existingOrderId = {{ old('order_id', $invoice->order_id) }};

                // Unit Photos                $.ajax({

                if (data.unit_photos && data.unit_photos.length > 0) {                    url: '{{ route("invoices.order-details") }}',

                    html += '<div class="mb-4 pb-4 border-b">';                    type: 'GET',

                    html += '<h4 class="text-sm font-medium text-gray-700 mb-3">Unit Photos</h4>';                    data: { order_id: existingOrderId },

                    html += '<div class="photo-grid">';                    success: function(data) {

                    data.unit_photos.forEach(function(photo) {                        $('#preview_order_no').text(data.order_no);

                        html += '<div class="photo-item">';                        $('#preview_customer').text(data.customer.name);

                        html += '<img src="' + photo.url + '" alt="Unit Photo" />';                        $('#preview_project').text(data.project.name);

                        html += '</div>';                        $('#preview_cluster').text(data.cluster.name);

                    });                        $('#preview_unit').text(data.unit.no + ' - ' + data.unit.name);

                    html += '</div>';                        $('#preview_type').text(data.unit.type);

                    html += '</div>';                        $('#preview_total').text(data.total_formatted);

                }

                        // Product photos

                // Product Photos                        if (data.product_photos && data.product_photos.length > 0) {

                if (data.product_photos && data.product_photos.length > 0) {                            let photosHtml = '';

                    html += '<div class="mb-4">';                            data.product_photos.forEach(photo => {

                    html += '<h4 class="text-sm font-medium text-gray-700 mb-3">Product Photos</h4>';                                photosHtml += `

                    html += '<div class="photo-grid">';                                    <div class="cursor-pointer rounded overflow-hidden border-2 border-gray-200 hover:border-emerald-500 transition-colors" 

                    data.product_photos.forEach(function(photo) {                                         style="width: 15rem; height: 15rem;">

                        html += '<div class="photo-item">';                                        <img src="${photo.url}" alt="${photo.name}" class="w-full h-full object-cover">

                        html += '<img src="' + photo.url + '" alt="Product Photo" />';                                    </div>

                        html += '</div>';                                `;

                    });                            });

                    html += '</div>';                            $('#productPhotos').html(photosHtml);

                    html += '</div>';                            $('#productPhotosContainer').show();

                }                        }



                $('#orderPreviewContent').html(html);                        // Unit photos

            }                        if (data.unit_photos && data.unit_photos.length > 0) {

        });                            let photosHtml = '';

    </script>                            data.unit_photos.forEach(photo => {

    @endpush                                photosHtml += `

</x-admin-layout>                                    <div class="cursor-pointer rounded overflow-hidden border-2 border-gray-200 hover:border-emerald-500 transition-colors" 

                                         style="width: 15rem; height: 15rem;">
                                        <img src="${photo.url}" alt="${photo.name}" class="w-full h-full object-cover">
                                    </div>
                                `;
                            });
                            $('#unitPhotos').html(photosHtml);
                            $('#unitPhotosContainer').show();
                        }

                        $('#orderPreview').show();
                    }
                });
            @endif
        });
    </script>
    @endpush

    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    @endpush
</x-admin-layout>
