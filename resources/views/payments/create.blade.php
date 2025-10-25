<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900">
                Create New Payment
            </h2>
            <a href="{{ route('payments.index') }}" class="btn btn-secondary">
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
                <form action="{{ route('payments.store') }}" method="POST" id="paymentForm">
                    @csrf

                    <!-- Invoice Selection -->
                    <div class="form-group">
                        <label for="invoice_id" class="form-label required">Select Invoice</label>
                        <select name="invoice_id" id="invoice_id" class="form-select" required>
                            <option value="">-- Select Invoice --</option>
                            @foreach($invoices as $invoice)
                                <option value="{{ $invoice->id }}" {{ old('invoice_id') == $invoice->id ? 'selected' : '' }}>
                                    {{ $invoice->no }} - {{ $invoice->order->customer->name ?? 'N/A' }}
                                </option>
                            @endforeach
                        </select>
                        @error('invoice_id')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date -->
                    <div class="form-group">
                        <label for="dt" class="form-label required">Payment Date</label>
                        <input type="date" name="dt" id="dt" value="{{ old('dt', date('Y-m-d')) }}" class="form-input" required>
                        @error('dt')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Payment Type -->
                    <div class="form-group">
                        <label for="payment_type" class="form-label required">Payment Method</label>
                        <select name="payment_type" id="payment_type" class="form-select" required>
                            <option value="">-- Select Payment Method --</option>
                            <option value="cash" {{ old('payment_type') == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="transfer" {{ old('payment_type', 'transfer') == 'transfer' ? 'selected' : '' }}>Bank Transfer</option>
                        </select>
                        @error('payment_type')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Amount -->
                    <div class="form-group">
                        <label for="amount" class="form-label required">Payment Amount</label>
                        <input type="number" name="amount" id="amount" value="{{ old('amount') }}" class="form-input" step="0.01" min="0" required>
                        @error('amount')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Bank Fields (conditional) -->
                    <div id="bankFields" style="display: {{ old('payment_type', 'transfer') == 'transfer' ? 'block' : 'none' }};">
                        <div class="form-group">
                            <label for="bank_account_id" class="form-label required">Account Number</label>
                            <input type="text" name="bank_account_id" id="bank_account_id" value="{{ old('bank_account_id') }}" class="form-input">
                            @error('bank_account_id')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="bank_account_type" class="form-label required">Bank Name</label>
                            <input type="text" name="bank_account_type" id="bank_account_type" value="{{ old('bank_account_type') }}" class="form-input" placeholder="e.g., BCA, Mandiri, BNI">
                            @error('bank_account_type')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="bank_account_name" class="form-label required">Account Name</label>
                            <input type="text" name="bank_account_name" id="bank_account_name" value="{{ old('bank_account_name') }}" class="form-input">
                            @error('bank_account_name')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-end gap-x-3 pt-4 border-t">
                        <a href="{{ route('payments.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                            Create Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Invoice Preview Sidebar (1/3 width) -->
        <div class="lg:col-span-1">
            <div class="card sticky top-6" id="invoicePreview" style="display: none;">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-3 border-b">Invoice Details</h3>
                
                <div id="invoicePreviewContent">
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
    </style>
    @endpush

    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('#invoice_id').select2({
                placeholder: '-- Select Invoice --',
                allowClear: true,
                width: '100%'
            });

            $('#payment_type').select2({
                placeholder: '-- Select Payment Method --',
                width: '100%'
            });

            // Show/hide bank fields based on payment type
            $('#payment_type').on('change', function() {
                const paymentType = $(this).val();
                if (paymentType === 'transfer') {
                    $('#bankFields').slideDown();
                    // Make bank fields required
                    $('#bank_account_id, #bank_account_type, #bank_account_name').prop('required', true);
                } else {
                    $('#bankFields').slideUp();
                    // Remove required from bank fields
                    $('#bank_account_id, #bank_account_type, #bank_account_name').prop('required', false);
                }
            });

            // Load invoice preview when invoice is selected
            $('#invoice_id').on('change', function() {
                const invoiceId = $(this).val();
                
                if (invoiceId) {
                    loadInvoicePreview(invoiceId);
                } else {
                    $('#invoicePreview').hide();
                }
            });

            // Load invoice preview on page load if invoice_id is already selected
            @if(old('invoice_id'))
                loadInvoicePreview({{ old('invoice_id') }});
            @endif

            function loadInvoicePreview(invoiceId) {
                $.ajax({
                    url: "{{ route('payments.invoice-details') }}",
                    type: 'GET',
                    data: { invoice_id: invoiceId },
                    beforeSend: function() {
                        $('#invoicePreviewContent').html('<div class="flex justify-center py-8"><svg class="animate-spin h-8 w-8 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></div>');
                        $('#invoicePreview').show();
                    },
                    success: function(response) {
                        if (response.success && response.data) {
                            renderInvoicePreview(response.data);
                        } else {
                            $('#invoicePreviewContent').html('<p class="text-sm text-red-600">Failed to load invoice details.</p>');
                        }
                    },
                    error: function() {
                        $('#invoicePreviewContent').html('<p class="text-sm text-red-600">Error loading invoice details.</p>');
                    }
                });
            }

            function renderInvoicePreview(data) {
                let html = '';

                // Invoice Info
                html += '<div class="mb-4 pb-4 border-b">';
                html += '<h4 class="text-sm font-medium text-gray-700 mb-2">Invoice Information</h4>';
                html += '<div class="space-y-1 text-sm">';
                html += '<p><span class="text-gray-600">Invoice No:</span> <span class="font-medium">' + (data.invoice_no || 'N/A') + '</span></p>';
                html += '<p><span class="text-gray-600">Invoice Date:</span> <span class="font-medium">' + (data.invoice_date || 'N/A') + '</span></p>';
                html += '<p><span class="text-gray-600">Order No:</span> <span class="font-medium">' + (data.order_no || 'N/A') + '</span></p>';
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
                if (data.product_type) html += '<p><span class="text-gray-600">Type:</span> <span class="font-medium">' + data.product_type + '</span></p>';
                html += '</div>';
                html += '</div>';

                // Payment Summary
                html += '<div class="mb-4">';
                html += '<h4 class="text-sm font-medium text-gray-700 mb-2">Payment Summary</h4>';
                html += '<div class="space-y-2 text-sm">';
                html += '<div class="flex justify-between"><span class="text-gray-600">Order Total:</span> <span class="font-bold">Rp ' + (data.total_formatted || '0') + '</span></div>';
                html += '<div class="flex justify-between"><span class="text-gray-600">Total Paid:</span> <span class="font-semibold text-emerald-600">Rp ' + (data.total_paid_formatted || '0') + '</span></div>';
                html += '<div class="flex justify-between pt-2 border-t"><span class="text-gray-900 font-medium">Remaining:</span> <span class="font-bold text-red-600">Rp ' + (data.remaining_formatted || '0') + '</span></div>';
                html += '</div>';
                html += '</div>';

                $('#invoicePreviewContent').html(html);
                
                // Auto-fill amount with remaining balance
                if (data.remaining > 0) {
                    $('#amount').val(data.remaining);
                }
            }
        });
    </script>
    @endpush
</x-admin-layout>
