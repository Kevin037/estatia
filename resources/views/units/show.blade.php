<x-admin-layout>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Unit Details</h1>
            <p class="mt-1 text-sm text-gray-600">{{ $unit->name }}</p>
        </div>
        <a href="{{ route('units.edit', $unit) }}" class="btn btn-primary">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
            </svg>
            Edit Unit
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="card">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Unit Name</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $unit->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Unit Number</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $unit->no ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Price</dt>
                        <dd class="mt-1 text-lg font-bold text-emerald-600">Rp {{ number_format($unit->price, 0, ',', '.') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            @php
                                $colors = [
                                    'available' => 'bg-green-100 text-green-800',
                                    'reserved' => 'bg-yellow-100 text-yellow-800',
                                    'sold' => 'bg-blue-100 text-blue-800',
                                    'handed_over' => 'bg-gray-100 text-gray-800',
                                ];
                                $color = $colors[$unit->status] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $color }}">
                                {{ ucfirst(str_replace('_', ' ', $unit->status)) }}
                            </span>
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Project & Cluster Information -->
            <div class="card">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Location Information</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Project</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $unit->cluster->project->name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Cluster</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $unit->cluster->name ?? '-' }}</dd>
                    </div>
                    @if($unit->cluster->desc)
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Cluster Description</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $unit->cluster->desc }}</dd>
                    </div>
                    @endif
                    @if($unit->cluster->facilities)
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Cluster Facilities</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $unit->cluster->facilities }}</dd>
                    </div>
                    @endif
                    @if($unit->cluster->road_width)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Road Width</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $unit->cluster->road_width }} m</dd>
                    </div>
                    @endif
                </dl>
            </div>

            <!-- Product Information -->
            <div class="card">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Product Information</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Type</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $unit->product->type->name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Product Code</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $unit->product->sku ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Land Area</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $unit->product->type->land_area ?? '-' }} m²</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Building Area</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $unit->product->type->building_area ?? '-' }} m²</dd>
                    </div>
                </dl>
            </div>

            <!-- Description & Facilities -->
            @if($unit->desc || $unit->facilities)
            <div class="card">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Information</h3>
                @if($unit->desc)
                <div class="mb-4">
                    <dt class="text-sm font-medium text-gray-500">Description</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $unit->desc }}</dd>
                </div>
                @endif
                @if($unit->facilities)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Facilities</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $unit->facilities }}</dd>
                </div>
                @endif
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Sales Information -->
            <div class="card">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Sales Information</h3>
                @if($unit->sales)
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Sales Name</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $unit->sales->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Phone</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $unit->sales->phone }}</dd>
                    </div>
                </dl>
                @else
                <p class="text-sm text-gray-500">No sales assigned</p>
                @endif
            </div>

            <!-- Product Photos -->
            <div class="card">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Product Photos</h3>
                @if($unit->product->productPhotos->count() > 0)
                <div class="flex flex-wrap gap-3">
                    @foreach($unit->product->productPhotos as $index => $photo)
                    <div class="cursor-pointer rounded overflow-hidden border-2 border-gray-200 hover:border-emerald-500 transition-colors" 
                         style="width: 15rem; height: 15rem;"
                         onclick="openProductGallery({{ $index }})">
                        <img src="{{ $photo->photo_url }}" alt="{{ $photo->name }}" class="w-full h-full object-cover">
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">No product photos</p>
                </div>
                @endif
            </div>

            <!-- Unit Photos -->
            <div class="card">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Unit Photos</h3>
                @if($unit->unitPhotos->count() > 0)
                <div class="flex flex-wrap gap-3">
                    @foreach($unit->unitPhotos as $index => $photo)
                    <div class="cursor-pointer rounded overflow-hidden border-2 border-gray-200 hover:border-emerald-500 transition-colors" 
                         style="width: 15rem; height: 15rem;"
                         onclick="openUnitGallery({{ $index }})">
                        <img src="{{ $photo->photo_url }}" alt="{{ $photo->name }}" class="w-full h-full object-cover">
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">No photos uploaded</p>
                </div>
                @endif
            </div>

            <!-- Actions -->
            <div class="card">
                <a href="{{ route('units.index') }}" class="btn btn-secondary w-full justify-center">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Back to List
                </a>
            </div>
        </div>
    </div>

    <!-- Photo Gallery Modal for Unit Photos -->
    <div id="unitGalleryModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex min-h-screen items-center justify-center p-4">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-black bg-opacity-75 transition-opacity" onclick="closeUnitGallery()"></div>
            
            <!-- Modal content -->
            <div class="relative z-50 w-full max-w-5xl">
                <!-- Close button -->
                <button onclick="closeUnitGallery()" class="absolute top-4 right-4 text-white hover:text-gray-300 z-50">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Image container -->
                <div class="relative flex items-center justify-center" onclick="closeUnitGallery()">
                    <!-- Previous button -->
                    <button onclick="event.stopPropagation(); previousUnitPhoto()" class="absolute left-4 text-white hover:text-gray-300 bg-black bg-opacity-50 rounded-full p-2 z-10">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                        </svg>
                    </button>

                    <!-- Image -->
                    <img id="unitGalleryImage" src="" alt="Unit Photo" width="700" class="object-contain rounded-lg" onclick="event.stopPropagation()">

                    <!-- Next button -->
                    <button onclick="event.stopPropagation(); nextUnitPhoto()" class="absolute right-4 text-white hover:text-gray-300 bg-black bg-opacity-50 rounded-full p-2 z-10">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                        </svg>
                    </button>
                </div>

                <!-- Photo counter -->
                <div class="text-center mt-4 text-white">
                    <span id="unitPhotoCounter"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Photo Gallery Modal for Product Photos -->
    <div id="productGalleryModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex min-h-screen items-center justify-center p-4">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-black bg-opacity-75 transition-opacity" onclick="closeProductGallery()"></div>
            
            <!-- Modal content -->
            <div class="relative z-50 w-full max-w-5xl">
                <!-- Close button -->
                <button onclick="closeProductGallery()" class="absolute top-4 right-4 text-white hover:text-gray-300 z-50">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Image container -->
                <div class="relative flex items-center justify-center" onclick="closeProductGallery()">
                    <!-- Previous button -->
                    <button onclick="event.stopPropagation(); previousProductPhoto()" class="absolute left-4 text-white hover:text-gray-300 bg-black bg-opacity-50 rounded-full p-2 z-10">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                        </svg>
                    </button>

                    <!-- Image -->
                    <img id="productGalleryImage" src="" alt="Product Photo" width="700" class="object-contain rounded-lg" onclick="event.stopPropagation()">

                    <!-- Next button -->
                    <button onclick="event.stopPropagation(); nextProductPhoto()" class="absolute right-4 text-white hover:text-gray-300 bg-black bg-opacity-50 rounded-full p-2 z-10">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                        </svg>
                    </button>
                </div>

                <!-- Photo counter -->
                <div class="text-center mt-4 text-white">
                    <span id="productPhotoCounter"></span>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Unit Photos Gallery
        const unitPhotos = @json($unit->unitPhotos->map(fn($photo) => $photo->photo_url)->values());
        let currentUnitIndex = 0;

        function openUnitGallery(index) {
            currentUnitIndex = index;
            showUnitPhoto();
            document.getElementById('unitGalleryModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeUnitGallery() {
            document.getElementById('unitGalleryModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function showUnitPhoto() {
            document.getElementById('unitGalleryImage').src = unitPhotos[currentUnitIndex];
            document.getElementById('unitPhotoCounter').textContent = `${currentUnitIndex + 1} / ${unitPhotos.length}`;
        }

        function nextUnitPhoto() {
            currentUnitIndex = (currentUnitIndex + 1) % unitPhotos.length;
            showUnitPhoto();
        }

        function previousUnitPhoto() {
            currentUnitIndex = (currentUnitIndex - 1 + unitPhotos.length) % unitPhotos.length;
            showUnitPhoto();
        }

        // Product Photos Gallery
        const productPhotos = @json($unit->product->productPhotos->map(fn($photo) => $photo->photo_url)->values());
        let currentProductIndex = 0;

        function openProductGallery(index) {
            currentProductIndex = index;
            showProductPhoto();
            document.getElementById('productGalleryModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeProductGallery() {
            document.getElementById('productGalleryModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function showProductPhoto() {
            document.getElementById('productGalleryImage').src = productPhotos[currentProductIndex];
            document.getElementById('productPhotoCounter').textContent = `${currentProductIndex + 1} / ${productPhotos.length}`;
        }

        function nextProductPhoto() {
            currentProductIndex = (currentProductIndex + 1) % productPhotos.length;
            showProductPhoto();
        }

        function previousProductPhoto() {
            currentProductIndex = (currentProductIndex - 1 + productPhotos.length) % productPhotos.length;
            showProductPhoto();
        }

        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            const unitModal = document.getElementById('unitGalleryModal');
            const productModal = document.getElementById('productGalleryModal');
            
            if (!unitModal.classList.contains('hidden')) {
                if (e.key === 'ArrowRight') nextUnitPhoto();
                if (e.key === 'ArrowLeft') previousUnitPhoto();
                if (e.key === 'Escape') closeUnitGallery();
            }
            
            if (!productModal.classList.contains('hidden')) {
                if (e.key === 'ArrowRight') nextProductPhoto();
                if (e.key === 'ArrowLeft') previousProductPhoto();
                if (e.key === 'Escape') closeProductGallery();
            }
        });
    </script>
    @endpush
</x-admin-layout>
