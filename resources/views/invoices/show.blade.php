<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900">
                Invoice Details: {{ $invoice->no }}
            </h2>
            <div class="flex items-center gap-x-3">
                <a href="{{ route('invoices.index') }}" class="btn btn-secondary">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Back to List
                </a>
                <a href="{{ route('invoices.pdf', $invoice->id) }}" class="btn btn-purple" target="_blank">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m.75 12l3 3m0 0l3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                    Export PDF
                </a>
                <a href="{{ route('invoices.edit', $invoice->id) }}" class="btn btn-emerald">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                    </svg>
                    Edit
                </a>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content (2/3 width) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Invoice Information -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Invoice Information</h3>
                </div>
                <div class="card-body">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-600">Invoice Number</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $invoice->no }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-600">Invoice Date</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $invoice->dt?->format('d M Y') ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-600">Order Number</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $invoice->order->no ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-600">Order Date</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $invoice->order->dt?->format('d M Y') ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-600">Payment Status</dt>
                            <dd class="mt-1">
                                @if($invoice->payment_status === 'paid')
                                    <span class="badge badge-success">Paid</span>
                                @else
                                    <span class="badge badge-warning">Pending</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-600">Total Amount</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-bold">Rp {{ number_format($invoice->order->total ?? 0, 0, ',', '.') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Customer Information -->
            @if($invoice->order && $invoice->order->customer)
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Customer Information</h3>
                </div>
                <div class="card-body">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-600">Customer Name</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $invoice->order->customer->name }}</dd>
                        </div>
                        @if($invoice->order->customer->email)
                        <div>
                            <dt class="text-sm font-medium text-gray-600">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $invoice->order->customer->email }}</dd>
                        </div>
                        @endif
                        @if($invoice->order->customer->phone)
                        <div>
                            <dt class="text-sm font-medium text-gray-600">Phone</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $invoice->order->customer->phone }}</dd>
                        </div>
                        @endif
                        @if($invoice->order->customer->address)
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-600">Address</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $invoice->order->customer->address }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>
            @endif

            <!-- Property Information -->
            @if($invoice->order)
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Property Information</h3>
                </div>
                <div class="card-body">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                        @if($invoice->order->project)
                        <div>
                            <dt class="text-sm font-medium text-gray-600">Project</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $invoice->order->project->name }}</dd>
                        </div>
                        @endif
                        @if($invoice->order->cluster)
                        <div>
                            <dt class="text-sm font-medium text-gray-600">Cluster</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $invoice->order->cluster->name }}</dd>
                        </div>
                        @endif
                        @if($invoice->order->unit)
                        <div>
                            <dt class="text-sm font-medium text-gray-600">Unit Number</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $invoice->order->unit->no }}</dd>
                        </div>
                        @if($invoice->order->unit->product)
                        <div>
                            <dt class="text-sm font-medium text-gray-600">Product Type</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $invoice->order->unit->product->type->name ?? 'N/A' }}</dd>
                        </div>
                        @endif
                        @endif
                    </dl>
                </div>
            </div>
            @endif

            <!-- Unit Photos -->
            @if($invoice->order && $invoice->order->unit && $invoice->order->unit->unitPhotos->isNotEmpty())
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Unit Photos</h3>
                </div>
                <div class="card-body">
                    <div class="photo-grid">
                        @foreach($invoice->order->unit->unitPhotos as $photo)
                        <div class="photo-item cursor-pointer" onclick="openLightbox({{ $loop->index }}, 'unit')">
                            <img src="{{ $photo->photo_url }}" alt="Unit Photo" />
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Product Photos -->
            @if($invoice->order && $invoice->order->unit && $invoice->order->unit->product && $invoice->order->unit->product->productPhotos->isNotEmpty())
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Product Photos</h3>
                </div>
                <div class="card-body">
                    <div class="photo-grid">
                        @foreach($invoice->order->unit->product->productPhotos as $photo)
                        <div class="photo-item cursor-pointer" onclick="openLightbox({{ $loop->index }}, 'product')">
                            <img src="{{ $photo->photo_url }}" alt="Product Photo" />
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar (1/3 width) -->
        <div class="lg:col-span-1">
            <!-- Payment Summary -->
            <div class="card sticky top-6">
                <div class="card-header">
                    <h3 class="card-title">Payment Summary</h3>
                </div>
                <div class="card-body space-y-4">
                    <div class="flex justify-between items-center pb-3 border-b">
                        <span class="text-sm font-medium text-gray-600">Order Total</span>
                        <span class="text-sm font-bold text-gray-900">Rp {{ number_format($invoice->order->total ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center pb-3 border-b">
                        <span class="text-sm font-medium text-gray-600">Total Paid</span>
                        <span class="text-sm font-semibold text-emerald-600">Rp {{ number_format($invoice->total_paid, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center pb-3 border-b">
                        <span class="text-sm font-medium text-gray-600">Remaining</span>
                        <span class="text-sm font-semibold text-red-600">Rp {{ number_format(($invoice->order->total ?? 0) - $invoice->total_paid, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center pt-2">
                        <span class="text-base font-semibold text-gray-900">Status</span>
                        @if($invoice->payment_status === 'paid')
                            <span class="badge badge-success text-base">Paid</span>
                        @else
                            <span class="badge badge-warning text-base">Pending</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lightbox Modal -->
    <div id="lightboxModal" class="hidden fixed inset-0 z-50 overflow-auto bg-black bg-opacity-90" onclick="closeLightbox()">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative max-w-5xl w-full" onclick="event.stopPropagation()">
                <button onclick="closeLightbox()" class="absolute top-4 right-4 text-white hover:text-gray-300 z-10">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <button onclick="previousPhoto()" class="absolute left-4 top-1/2 transform -translate-y-1/2 text-white hover:text-gray-300 z-10">
                    <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <button onclick="nextPhoto()" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-white hover:text-gray-300 z-10">
                    <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <img id="lightboxImage" src="" alt="Photo" class="w-full h-auto rounded-lg" />
                <div class="text-center mt-4">
                    <span id="lightboxCounter" class="text-white text-lg"></span>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
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
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .photo-item:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        .photo-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        let currentPhotoIndex = 0;
        let currentPhotoType = 'unit';
        
        const unitPhotos = @json($invoice->order && $invoice->order->unit ? $invoice->order->unit->unitPhotos->map(function($photo) {
            return $photo->photo_url;
        }) : []);
        
        const productPhotos = @json($invoice->order && $invoice->order->unit && $invoice->order->unit->product ? $invoice->order->unit->product->productPhotos->map(function($photo) {
            return $photo->photo_url;
        }) : []);

        function openLightbox(index, type) {
            currentPhotoIndex = index;
            currentPhotoType = type;
            updateLightboxImage();
            document.getElementById('lightboxModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeLightbox() {
            document.getElementById('lightboxModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function nextPhoto() {
            const photos = currentPhotoType === 'unit' ? unitPhotos : productPhotos;
            currentPhotoIndex = (currentPhotoIndex + 1) % photos.length;
            updateLightboxImage();
        }

        function previousPhoto() {
            const photos = currentPhotoType === 'unit' ? unitPhotos : productPhotos;
            currentPhotoIndex = (currentPhotoIndex - 1 + photos.length) % photos.length;
            updateLightboxImage();
        }

        function updateLightboxImage() {
            const photos = currentPhotoType === 'unit' ? unitPhotos : productPhotos;
            document.getElementById('lightboxImage').src = photos[currentPhotoIndex];
            document.getElementById('lightboxCounter').textContent = `${currentPhotoIndex + 1} / ${photos.length}`;
        }

        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            const modal = document.getElementById('lightboxModal');
            if (!modal.classList.contains('hidden')) {
                if (e.key === 'Escape') closeLightbox();
                if (e.key === 'ArrowLeft') previousPhoto();
                if (e.key === 'ArrowRight') nextPhoto();
            }
        });
    </script>
    @endpush
</x-admin-layout>
