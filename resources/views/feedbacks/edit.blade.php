<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">
                    Edit Feedback
                </h2>
                <p class="mt-1 text-sm text-gray-600">Update feedback information</p>
            </div>
            <a href="{{ route('feedbacks.index') }}" class="btn btn-secondary">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Back to List
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <form action="{{ route('feedbacks.update', $feedback->id) }}" method="POST" enctype="multipart/form-data" id="feedbackEditForm">
            @csrf
            @method('PUT')

            <!-- Feedback Information Section -->
            <div class="card mb-6">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-gray-900">Feedback Information</h3>
                    <p class="mt-1 text-sm text-gray-600">Basic details about the feedback</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Order -->
                    <div class="md:col-span-2">
                        <label for="order_id" class="form-label">
                            Order <span class="text-red-500">*</span>
                        </label>
                        <select 
                            name="order_id" 
                            id="order_id" 
                            class="form-input select2 @error('order_id') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror"
                            required
                        >
                            <option value="">Select Order</option>
                            @foreach($orders as $order)
                                <option value="{{ $order->id }}" {{ old('order_id', $feedback->order_id) == $order->id ? 'selected' : '' }}>
                                    {{ $order->no }} - {{ $order->customer->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('order_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Select the order related to this feedback</p>
                    </div>

                    <!-- Date -->
                    <div class="md:col-span-2">
                        <label for="dt" class="form-label">
                            Date <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="date" 
                            name="dt" 
                            id="dt" 
                            value="{{ old('dt', $feedback->dt?->format('Y-m-d')) }}"
                            class="form-input @error('dt') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror"
                            required
                        >
                        @error('dt')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label for="desc" class="form-label">
                            Feedback/Testimonial <span class="text-red-500">*</span>
                        </label>
                        <textarea 
                            name="desc" 
                            id="desc" 
                            rows="6"
                            class="form-input @error('desc') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror"
                            placeholder="Enter customer feedback or testimonial..."
                            required
                        >{{ old('desc', $feedback->desc) }}</textarea>
                        @error('desc')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Write the complete feedback or testimonial from the customer</p>
                    </div>
                </div>
            </div>

            <!-- Photo Section -->
            <div class="card mb-6">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-gray-900">Photo</h3>
                    <p class="mt-1 text-sm text-gray-600">Upload a photo related to the feedback (optional)</p>
                </div>

                <div class="space-y-4">
                    <!-- Current Photo Display -->
                    @if($feedback->photo)
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <label class="form-label">Current Photo</label>
                        <div class="mt-2">
                            <img src="{{ $feedback->photo_url }}" alt="Current Photo" class="max-w-md rounded-lg border-2 border-gray-200">
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Upload a new photo below to replace this image</p>
                    </div>
                    @endif

                    <div>
                        <label for="photo" class="form-label">
                            {{ $feedback->photo ? 'Upload New Photo' : 'Upload Photo' }}
                        </label>
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
                        <p class="mt-1 text-xs text-gray-500">JPEG, JPG or PNG (max 2MB)</p>
                    </div>

                    <!-- Photo Preview -->
                    <div id="photoPreview" style="display: none;">
                        <label class="form-label">New Photo Preview</label>
                        <div class="mt-2">
                            <img src="" alt="Photo Preview" class="max-w-md rounded-lg border-2 border-gray-200">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-between bg-gray-50 px-6 py-4 rounded-lg">
                <a href="{{ route('feedbacks.index') }}" class="btn btn-secondary">
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
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                    <span :class="{'opacity-10': loading}">Update Feedback</span>
                </button>
            </div>
        </form>
    </div>

    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container .select2-selection--single {
            height: 42px;
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 26px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
        }
    </style>
    @endpush

    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                placeholder: 'Select Order',
                allowClear: true,
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
                    $('#photoPreview').hide();
                }
            });
        });
    </script>
    @endpush
</x-admin-layout>
