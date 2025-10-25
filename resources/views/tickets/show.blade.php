<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900">
                Ticket Details: {{ $ticket->no }}
            </h2>
            <div class="flex gap-x-3">
                <a href="{{ route('tickets.edit', $ticket->id) }}" class="btn bg-emerald-600 hover:bg-emerald-700 text-white">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                    </svg>
                    Edit
                </a>
                <a href="{{ route('tickets.index') }}" class="btn btn-secondary">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Back to List
                </a>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content (2/3 width) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Ticket Information Card -->
            <div class="card">
                <div class="flex items-center justify-between mb-6 pb-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Ticket Information</h3>
                    <div>
                        @if($ticket->status === 'pending')
                            <span class="inline-flex items-center rounded-md bg-yellow-50 px-3 py-1.5 text-sm font-medium text-yellow-800 ring-1 ring-inset ring-yellow-600/20">
                                <svg class="mr-1.5 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-13a.75.75 0 00-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 000-1.5h-3.25V5z" clip-rule="evenodd" />
                                </svg>
                                Pending
                            </span>
                        @else
                            <span class="inline-flex items-center rounded-md bg-green-50 px-3 py-1.5 text-sm font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                                <svg class="mr-1.5 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                                </svg>
                                Completed
                            </span>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="text-sm font-medium text-gray-700">Ticket Number</label>
                        <p class="mt-1 text-base text-gray-900 font-mono">{{ $ticket->no }}</p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">Date</label>
                        <p class="mt-1 text-base text-gray-900">{{ $ticket->dt?->format('d F Y') ?? 'N/A' }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-sm font-medium text-gray-700">Title</label>
                        <p class="mt-1 text-lg font-semibold text-gray-900">{{ $ticket->title }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-sm font-medium text-gray-700">Description</label>
                        <div class="mt-1 text-base text-gray-900 whitespace-pre-wrap bg-gray-50 p-4 rounded-lg border border-gray-200">{{ $ticket->desc }}</div>
                    </div>
                </div>
            </div>

            <!-- Photo Card (if exists) -->
            @if($ticket->photo)
                <div class="card">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6 pb-4 border-b">Attached Photo</h3>
                    <div>
                        <img src="{{ $ticket->photo_url }}" alt="{{ $ticket->title }}" class="w-full max-w-2xl h-auto rounded-lg border-2 border-gray-200 shadow-lg">
                    </div>
                </div>
            @endif

            <!-- Order Information Card -->
            <div class="card">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 pb-4 border-b">Related Order</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="text-sm font-medium text-gray-700">Order Number</label>
                        <p class="mt-1">
                            <a href="{{ route('orders.show', $ticket->order_id) }}" class="text-emerald-600 hover:text-emerald-700 font-medium hover:underline">
                                {{ $ticket->order->no }}
                            </a>
                        </p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">Order Status</label>
                        <p class="mt-1">
                            @if($ticket->order->status === 'completed')
                                <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                                    Completed
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-md bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-800 ring-1 ring-inset ring-yellow-600/20">
                                    Pending
                                </span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Customer Information Card -->
            @if($ticket->order->customer)
                <div class="card">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6 pb-4 border-b">Customer Information</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-sm font-medium text-gray-700">Customer Name</label>
                            <p class="mt-1 text-base text-gray-900">{{ $ticket->order->customer->name }}</p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-700">Phone</label>
                            <p class="mt-1 text-base text-gray-900">{{ $ticket->order->customer->phone ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Property Details Card (if available) -->
            @if($ticket->order->unit)
                <div class="card">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6 pb-4 border-b">Property Details</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if($ticket->order->project)
                            <div>
                                <label class="text-sm font-medium text-gray-700">Project</label>
                                <p class="mt-1 text-base text-gray-900">{{ $ticket->order->project->name }}</p>
                            </div>
                        @endif

                        @if($ticket->order->cluster)
                            <div>
                                <label class="text-sm font-medium text-gray-700">Cluster</label>
                                <p class="mt-1 text-base text-gray-900">{{ $ticket->order->cluster->name }}</p>
                            </div>
                        @endif

                        <div>
                            <label class="text-sm font-medium text-gray-700">Unit Number</label>
                            <p class="mt-1 text-base text-gray-900">{{ $ticket->order->unit->no }}</p>
                        </div>

                        @if($ticket->order->unit->product)
                            <div>
                                <label class="text-sm font-medium text-gray-700">Product Type</label>
                                <p class="mt-1 text-base text-gray-900">{{ $ticket->order->unit->product->name }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar (1/3 width) -->
        <div class="lg:col-span-1">
            <div class="card sticky top-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 pb-4 border-b">Quick Actions</h3>

                <div class="space-y-3">
                    <a href="{{ route('tickets.edit', $ticket->id) }}" class="btn btn-primary w-full">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                        </svg>
                        Edit Ticket
                    </a>
                </div>

                <div class="mt-6 pt-6 border-t">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Ticket Information</h4>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Created:</dt>
                            <dd class="font-medium text-gray-900">{{ $ticket->created_at->format('d M Y') }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Last Updated:</dt>
                            <dd class="font-medium text-gray-900">{{ $ticket->updated_at->diffForHumans() }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Delete confirmation with SweetAlert2
        document.querySelector('.delete-form')?.addEventListener('submit', function(e) {
            e.preventDefault();
            
            Swal.fire({
                title: 'Delete Ticket?',
                text: 'This action cannot be undone!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    </script>
    @endpush
</x-admin-layout>
