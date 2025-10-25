<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">
                    Feedback Details
                </h2>
                <p class="mt-1 text-sm text-gray-600">View complete feedback information</p>
            </div>
            <a href="{{ route('feedbacks.index') }}" class="btn btn-secondary">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Back to List
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Feedback Information -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-lg font-semibold text-gray-900">Feedback Information</h3>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Date</label>
                            <p class="mt-1 text-base text-gray-900">{{ $feedback->dt ? $feedback->dt->format('l, d F Y') : 'N/A' }}</p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-500">Feedback/Testimonial</label>
                            <div class="mt-2 p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <p class="text-gray-900 whitespace-pre-wrap">{{ $feedback->desc }}</p>
                            </div>
                        </div>

                        @if($feedback->photo)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Photo</label>
                            <div class="mt-2">
                                <img src="{{ $feedback->photo_url }}" alt="Feedback Photo" class="max-w-full rounded-lg border-2 border-gray-200">
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Related Order -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-lg font-semibold text-gray-900">Related Order</h3>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Order Number</label>
                            <p class="mt-1">
                                <a href="{{ route('orders.show', $feedback->order->id) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                    {{ $feedback->order->no }}
                                </a>
                            </p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-500">Order Date</label>
                            <p class="mt-1 text-base text-gray-900">{{ $feedback->order->dt ? $feedback->order->dt->format('d M Y') : 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Customer Information -->
                @if($feedback->order->customer)
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-lg font-semibold text-gray-900">Customer Information</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Name</label>
                            <p class="mt-1 text-base text-gray-900">{{ $feedback->order->customer->name }}</p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-500">Email</label>
                            <p class="mt-1 text-base text-gray-900">{{ $feedback->order->customer->email ?? 'N/A' }}</p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-500">Phone</label>
                            <p class="mt-1 text-base text-gray-900">{{ $feedback->order->customer->phone ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Property Details -->
                @if($feedback->order->project || $feedback->order->cluster || $feedback->order->unit)
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-lg font-semibold text-gray-900">Property Details</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($feedback->order->project)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Project</label>
                            <p class="mt-1 text-base text-gray-900">{{ $feedback->order->project->name }}</p>
                        </div>
                        @endif

                        @if($feedback->order->cluster)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Cluster</label>
                            <p class="mt-1 text-base text-gray-900">{{ $feedback->order->cluster->name }}</p>
                        </div>
                        @endif

                        @if($feedback->order->unit)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Unit</label>
                            <p class="mt-1 text-base text-gray-900">{{ $feedback->order->unit->code }}</p>
                        </div>

                        @if($feedback->order->unit->product)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Product Type</label>
                            <p class="mt-1 text-base text-gray-900">{{ $feedback->order->unit->product->name }}</p>
                        </div>
                        @endif
                        @endif
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
                    </div>
                    
                    <div class="space-y-3">
                        <a href="{{ route('feedbacks.edit', $feedback->id) }}" class="btn btn-secondary w-full justify-center">
                            <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                            </svg>
                            Edit Feedback
                        </a>
                    </div>
                </div>

                <!-- Feedback Information -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-lg font-semibold text-gray-900">Feedback Information</h3>
                    </div>
                    
                    <div class="space-y-3 text-sm">
                        <div>
                            <label class="text-gray-500">Created</label>
                            <p class="mt-1 text-gray-900">{{ $feedback->created_at->format('d M Y, H:i') }}</p>
                        </div>

                        <div>
                            <label class="text-gray-500">Last Updated</label>
                            <p class="mt-1 text-gray-900">{{ $feedback->updated_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        .btn-danger {
            background-color: #dc2626;
            color: white;
        }
        .btn-danger:hover {
            background-color: #b91c1c;
        }
    </style>
    @endpush

    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        function deleteFeedback(feedbackId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#059669',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('feedbacks.destroy', $feedback->id) }}",
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire(
                                'Deleted!',
                                response.message,
                                'success'
                            ).then(() => {
                                window.location.href = "{{ route('feedbacks.index') }}";
                            });
                        },
                        error: function(xhr) {
                            Swal.fire(
                                'Error!',
                                xhr.responseJSON.message || 'Failed to delete feedback.',
                                'error'
                            );
                        }
                    });
                }
            });
        }
    </script>
    @endpush
</x-admin-layout>
