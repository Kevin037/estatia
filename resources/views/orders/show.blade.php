<x-admin-layout>
    <div class="min-h-screen">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Order Details</h1>
                    <p class="mt-1 text-sm text-gray-600">View complete order information</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to List
                    </a>
                    <a href="{{ route('orders.edit', $order) }}" class="btn btn-emerald">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit
                    </a>
                    <form action="{{ route('orders.destroy', $order) }}" method="POST" class="inline delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <svg class="-ml-0.5 mr-1.5 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Order Information -->
            <div class="lg:col-span-2">
                <div class="card">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Order Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Order Number</label>
                            <p class="mt-1 text-base text-gray-900 font-semibold">{{ $order->no }}</p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-500">Order Date</label>
                            <p class="mt-1 text-base text-gray-900">{{ $order->dt->format('d M Y') }}</p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-500">Customer</label>
                            <p class="mt-1 text-base text-gray-900">{{ $order->customer->name }}</p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-500">Status</label>
                            <div class="mt-1">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'completed' => 'bg-green-100 text-green-800',
                                    ];
                                    $color = $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium {{ $color }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>
                        </div>

                        @if($order->notes)
                        <div class="md:col-span-2">
                            <label class="text-sm font-medium text-gray-500">Notes</label>
                            <p class="mt-1 text-base text-gray-900 whitespace-pre-wrap">{{ $order->notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Unit Information -->
                @if($order->unit)
                <div class="card mt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Unit Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Project</label>
                            <p class="mt-1 text-base text-gray-900">{{ $order->project->name ?? '-' }}</p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-500">Cluster</label>
                            <p class="mt-1 text-base text-gray-900">{{ $order->cluster->name ?? '-' }}</p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-500">Unit Number</label>
                            <p class="mt-1 text-base text-gray-900 font-semibold">{{ $order->unit->no }}</p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-500">Unit Name</label>
                            <p class="mt-1 text-base text-gray-900">{{ $order->unit->name }}</p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-500">Price</label>
                            <p class="mt-1 text-xl text-emerald-600 font-bold">Rp {{ number_format($order->unit->price, 0, ',', '.') }}</p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-500">Status</label>
                            <div class="mt-1">
                                @php
                                    $unitStatusColors = [
                                        'available' => 'bg-green-100 text-green-800',
                                        'reserved' => 'bg-yellow-100 text-yellow-800',
                                        'sold' => 'bg-blue-100 text-blue-800',
                                        'handed_over' => 'bg-gray-100 text-gray-800'
                                    ];
                                    $unitColor = $unitStatusColors[$order->unit->status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium {{ $unitColor }}">
                                    {{ ucfirst(str_replace('_', ' ', $order->unit->status)) }}
                                </span>
                            </div>
                        </div>

                        @if($order->unit->product)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Product Type</label>
                            <p class="mt-1 text-base text-gray-900">{{ $order->unit->product->type->name ?? '-' }}</p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-500">Product Code</label>
                            <p class="mt-1 text-base text-gray-900">{{ $order->unit->product->code ?? '-' }}</p>
                        </div>
                        @endif

                        @if($order->unit->desc)
                        <div class="md:col-span-2">
                            <label class="text-sm font-medium text-gray-500">Description</label>
                            <p class="mt-1 text-base text-gray-900 whitespace-pre-wrap">{{ $order->unit->desc }}</p>
                        </div>
                        @endif

                        @if($order->unit->facilities)
                        <div class="md:col-span-2">
                            <label class="text-sm font-medium text-gray-500">Facilities</label>
                            <p class="mt-1 text-base text-gray-900 whitespace-pre-wrap">{{ $order->unit->facilities }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Product Photos -->
                @if($order->unit && $order->unit->product && $order->unit->product->productPhotos->count() > 0)
                <div class="card mt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Product Photos</h3>
                    <div class="flex flex-wrap gap-4">
                        @foreach($order->unit->product->productPhotos as $photo)
                        <div class="cursor-pointer rounded overflow-hidden border-2 border-gray-200 hover:border-emerald-500 transition-colors photo-item" 
                             style="width: 15rem; height: 15rem;"
                             data-photo="{{ $photo->photo_url }}">
                            <img src="{{ $photo->photo_url }}" alt="{{ $photo->name }}" class="w-full h-full object-cover">
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Unit Photos -->
                @if($order->unit && $order->unit->unitPhotos->count() > 0)
                <div class="card mt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Unit Photos</h3>
                    <div class="flex flex-wrap gap-4">
                        @foreach($order->unit->unitPhotos as $photo)
                        <div class="cursor-pointer rounded overflow-hidden border-2 border-gray-200 hover:border-emerald-500 transition-colors photo-item" 
                             style="width: 15rem; height: 15rem;"
                             data-photo="{{ $photo->photo_url }}">
                            <img src="{{ $photo->photo_url }}" alt="{{ $photo->name }}" class="w-full h-full object-cover">
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Order Summary Sidebar -->
            <div class="lg:col-span-1">
                <div class="card sticky top-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Order Summary</h3>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between items-start pb-4 border-b border-gray-200">
                            <span class="text-sm text-gray-600">Order Number:</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $order->no }}</span>
                        </div>

                        <div class="flex justify-between items-start pb-4 border-b border-gray-200">
                            <span class="text-sm text-gray-600">Date:</span>
                            <span class="text-sm font-medium text-gray-900">{{ $order->dt->format('d M Y') }}</span>
                        </div>

                        <div class="flex justify-between items-start pb-4 border-b border-gray-200">
                            <span class="text-sm text-gray-600">Customer:</span>
                            <span class="text-sm font-medium text-gray-900">{{ $order->customer->name }}</span>
                        </div>

                        @if($order->unit)
                        <div class="flex justify-between items-start pb-4 border-b border-gray-200">
                            <span class="text-sm text-gray-600">Unit:</span>
                            <span class="text-sm font-medium text-gray-900">{{ $order->unit->no }} - {{ $order->unit->name }}</span>
                        </div>

                        <div class="flex justify-between items-start pb-4 border-b border-gray-200">
                            <span class="text-sm text-gray-600">Unit Price:</span>
                            <span class="text-lg font-bold text-emerald-600">Rp {{ number_format($order->unit->price, 0, ',', '.') }}</span>
                        </div>
                        @endif

                        <div class="flex justify-between items-start pt-4 border-t-2 border-gray-300">
                            <span class="text-base font-semibold text-gray-900">Total:</span>
                            <span class="text-xl font-bold text-emerald-600">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Photo Modal -->
    <div id="photoModal" class="fixed inset-0 bg-black bg-opacity-75 hidden items-center justify-center z-50" style="display: none;">
        <div class="relative max-w-5xl max-h-screen p-4">
            <button type="button" id="closeModal" class="absolute top-6 right-6 text-white hover:text-gray-300 z-10">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            <img id="modalImage" src="" alt="Photo" class="max-w-full max-h-screen object-contain">
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Photo modal
            $('.photo-item').on('click', function() {
                const photoUrl = $(this).data('photo');
                $('#modalImage').attr('src', photoUrl);
                $('#photoModal').fadeIn().css('display', 'flex');
            });

            $('#closeModal, #photoModal').on('click', function(e) {
                if (e.target === this) {
                    $('#photoModal').fadeOut();
                }
            });

            // Keyboard navigation
            $(document).on('keydown', function(e) {
                if ($('#photoModal').is(':visible')) {
                    if (e.key === 'Escape') {
                        $('#photoModal').fadeOut();
                    }
                }
            });

            // Delete confirmation
            $('.delete-form').on('submit', function(e) {
                e.preventDefault();
                const form = this;
                
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
    @endpush
</x-admin-layout>
