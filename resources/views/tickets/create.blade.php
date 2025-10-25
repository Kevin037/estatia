<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">
                    Create New Ticket
                </h2>
                <p class="mt-1 text-sm text-gray-600">Add a new support ticket for an order</p>
            </div>
            <a href="{{ route('tickets.index') }}" class="btn btn-secondary">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Back to List
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <form action="{{ route('tickets.store') }}" method="POST" enctype="multipart/form-data" id="ticketCreateForm">
            @csrf

            <!-- Order Information Section -->
            <div class="card mb-6">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-gray-900">Order Information</h3>
                    <p class="mt-1 text-sm text-gray-600">Select the order related to this ticket</p>
                </div>

                <div>
                    <label for="order_id" class="form-label">
                        Select Order <span class="text-red-500">*</span>
                    </label>
                    <select 
                        name="order_id" 
                        id="order_id" 
                        class="form-select @error('order_id') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror"
                        required
                    >
                        <option value="">-- Select Order --</option>
                        @foreach($orders as $order)
                            <option value="{{ $order->id }}" {{ old('order_id') == $order->id ? 'selected' : '' }}>
                                {{ $order->no }} - {{ $order->customer->name ?? 'N/A' }}
                            </option>
                        @endforeach
                    </select>
                    @error('order_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Ticket Details Section -->
            <div class="card mb-6">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-gray-900">Ticket Details</h3>
                    <p class="mt-1 text-sm text-gray-600">Provide details about the issue or request</p>
                </div>

                <div class="space-y-6">
                    <!-- Date -->
                    <div>
                        <label for="dt" class="form-label">
                            Date <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="date" 
                            name="dt" 
                            id="dt" 
                            value="{{ old('dt', date('Y-m-d')) }}"
                            class="form-input @error('dt') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror"
                            required
                        >
                        @error('dt')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Title -->
                    <div>
                        <label for="title" class="form-label">
                            Title <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="title" 
                            id="title" 
                            value="{{ old('title') }}"
                            class="form-input @error('title') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror"
                            placeholder="Enter ticket title"
                            required
                        >
                        @error('title')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="desc" class="form-label">
                            Description <span class="text-red-500">*</span>
                        </label>
                        <textarea 
                            name="desc" 
                            id="desc" 
                            rows="6"
                            class="form-input @error('desc') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror"
                            placeholder="Describe the issue or request in detail..."
                            required
                        >{{ old('desc') }}</textarea>
                        @error('desc')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Photo -->
                    <div>
                        <label for="photo" class="form-label">Photo (Optional)</label>
                        <input 
                            type="file" 
                            name="photo" 
                            id="photo" 
                            class="form-input"
                            accept="image/jpeg,image/jpg,image/png"
                        >
                        @error('photo')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">JPG, PNG (Max: 2MB)</p>
                        <div id="photoPreview" class="mt-3" style="display: none;">
                            <img src="" alt="Preview" class="h-32 w-32 object-cover rounded-lg border-2 border-gray-200">
                        </div>
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="form-label">Status</label>
                        <select 
                            name="status" 
                            id="status" 
                            class="form-select @error('status') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror"
                        >
                            <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                        @error('status')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-between bg-gray-50 px-6 py-4 rounded-lg">
                <a href="{{ route('tickets.index') }}" class="btn btn-secondary">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Cancel
                </a>
                <button 
                    x-data="{ loading: false }" 
                    x-init="$el.form && $el.form.addEventListener('submit', () => loading = true)"
                    :disabled="loading"
                    type="submit" 
                    class="btn btn-primary relative">
                    <template x-if="loading">
                        <svg class="absolute left-3 h-5 w-5 animate-spin text-white" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                    </template>
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span :class="{'opacity-10': loading}">Create Ticket</span>
                </button>
            </div>
        </form>
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
            $('#order_id').select2({
                placeholder: '-- Select Order --',
                allowClear: true,
                width: '100%'
            });

            $('#status').select2({
                minimumResultsForSearch: Infinity,
                width: '100%'
            });

            // Photo preview
            $('#photo').on('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#photoPreview img').attr('src', e.target.result);
                        $('#photoPreview').fadeIn();
                    }
                    reader.readAsDataURL(file);
                } else {
                    $('#photoPreview').fadeOut();
                }
            });
        });
    </script>
    @endpush
</x-admin-layout>
