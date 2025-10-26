<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900">Add Product</h2>
            <a href="{{ route('products.index') }}" class="btn btn-secondary">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Back to List
            </a>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="card mb-6">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-gray-900">Product Information</h3>
                    <p class="mt-1 text-sm text-gray-600">Add a new product using the fields below</p>
                </div>

                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="name" class="form-label">Product Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-input @error('name') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror" placeholder="Product name" required>
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="sku" class="form-label">SKU <span class="text-red-500">*</span></label>
                        <input type="text" name="sku" id="sku" value="{{ old('sku') }}" class="form-input @error('sku') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror" placeholder="e.g., PRD-001" required>
                        @error('sku')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div x-data="{ photoPreview: null }">
                        <label for="photo" class="form-label">Main Product Photo</label>
                        <div class="mt-2">
                            <div class="flex items-center gap-4 mb-3">
                                <div class="flex-shrink-0">
                                    <template x-if="photoPreview">
                                        <img :src="photoPreview" alt="Photo preview" class="h-24 w-24 rounded object-cover border-2 border-gray-200">
                                    </template>
                                    <template x-if="!photoPreview">
                                        <div class="h-24 w-24 rounded bg-gray-100 flex items-center justify-center border-2 border-dashed border-gray-300">
                                            <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                            </svg>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            
                            <div 
                                x-data="{ 
                                    isDragging: false,
                                    handleDrop(e) {
                                        this.isDragging = false;
                                        const file = e.dataTransfer.files[0];
                                        if (file && file.type.startsWith('image/')) {
                                            const input = document.getElementById('photo');
                                            const dataTransfer = new DataTransfer();
                                            dataTransfer.items.add(file);
                                            input.files = dataTransfer.files;
                                            photoPreview = URL.createObjectURL(file);
                                        }
                                    }
                                }"
                                @dragover.prevent="isDragging = true"
                                @dragleave.prevent="isDragging = false"
                                @drop.prevent="handleDrop"
                                :class="isDragging ? 'border-emerald-500 bg-emerald-50' : 'border-gray-300'"
                                class="relative border-2 border-dashed rounded-lg p-6 transition-colors duration-200"
                            >
                                <input 
                                    type="file" 
                                    name="photo" 
                                    id="photo" 
                                    accept="image/*" 
                                    @change="photoPreview = $event.target.files.length > 0 ? URL.createObjectURL($event.target.files[0]) : null"
                                    class="hidden">
                                
                                <div class="text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                    </svg>
                                    <div class="mt-4 flex text-sm leading-6 text-gray-600 justify-center">
                                        <label for="photo" class="relative cursor-pointer rounded-md bg-white font-semibold text-emerald-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-emerald-600 focus-within:ring-offset-2 hover:text-emerald-500">
                                            <span>Upload a file</span>
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs leading-5 text-gray-600">PNG, JPG, GIF up to 2MB</p>
                                </div>
                            </div>
                        </div>
                        @error('photo')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Multiple Product Photos -->
                    <div x-data="productPhotos()">
                        <label class="form-label">Additional Product Photos</label>
                        <div 
                            @dragover.prevent="isDragging = true"
                            @dragleave.prevent="isDragging = false"
                            @drop.prevent="handleDrop"
                            :class="isDragging ? 'border-emerald-500 bg-emerald-50' : 'border-gray-300'"
                            class="mt-2 border-2 border-dashed rounded-lg p-6 transition-colors duration-200"
                        >
                            <input 
                                type="file" 
                                id="product_photos" 
                                name="product_photos[]"
                                accept="image/*" 
                                multiple
                                @change="handleFiles($event.target.files)"
                                class="hidden">
                            
                            <div class="text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                </svg>
                                <div class="mt-4 flex text-sm leading-6 text-gray-600 justify-center">
                                    <label for="product_photos" class="relative cursor-pointer rounded-md bg-white font-semibold text-emerald-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-emerald-600 focus-within:ring-offset-2 hover:text-emerald-500">
                                        <span>Upload files</span>
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs leading-5 text-gray-600">PNG, JPG, GIF up to 2MB each (multiple files allowed)</p>
                            </div>
                        </div>

                        <!-- Preview uploaded photos -->
                        <div x-show="photos.length > 0" class="mt-4">
                            <p class="text-sm text-gray-700 mb-2 font-medium">Selected Photos:</p>
                            <div class="flex flex-wrap gap-3">
                                <template x-for="(photo, index) in photos" :key="index">
                                    <div class="relative">
                                        <div class="h-24 w-24 rounded overflow-hidden border-2 border-gray-200">
                                            <img :src="photo.preview" :alt="`Photo ${index + 1}`" class="w-full h-full object-cover">
                                        </div>
                                        <button 
                                            type="button" 
                                            @click="removePhoto(index)" 
                                            class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full p-1.5 shadow-lg z-10"
                                            title="Remove photo"
                                        >
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                        <p class="text-xs text-gray-600 mt-1 text-center truncate w-24" x-text="'Photo ' + (index + 1)"></p>
                                    </div>
                                </template>
                            </div>
                        </div>

                        @error('product_photos')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @error('product_photos.*')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="price" class="form-label">Price <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                            <input type="number" name="price" id="price" value="{{ old('price') }}" class="form-input pl-12 @error('price') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror" placeholder="0" step="0.01" min="0" required>
                        </div>
                        @error('price')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="formula_id" class="form-label">Formula (Optional)</label>
                        <select name="formula_id" id="formula_id" class="form-input @error('formula_id') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror">
                            <option value="">No Formula</option>
                            @foreach($formulas as $formula)
                                <option value="{{ $formula->id }}" {{ old('formula_id') == $formula->id ? 'selected' : '' }}>
                                    {{ $formula->code }} - {{ $formula->name }} (Rp {{ number_format($formula->total, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                        @error('formula_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between bg-gray-50 px-6 py-4 rounded-lg">
                <a href="{{ route('products.index') }}" class="btn btn-secondary">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Cancel
                </a>
                <button 
                    x-data="{ loading: false }" 
                    x-init="$el.form.addEventListener('submit', () => { loading = true; }, { once: true });"
                    :disabled="loading"
                    type="submit" 
                    class="btn btn-primary relative"
                    :class="{'opacity-60 cursor-not-allowed': loading}">
                    <span x-show="loading" class="inline-flex mr-2">
                        <svg class="h-5 w-5 animate-spin text-white" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                    </span>
                    <span x-show="!loading" class="-ml-0.5 mr-1.5 inline-flex">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </span>
                    <span x-text="loading ? 'Creating…' : 'Create Product'"></span>
                </button>
            </div>
        </form>
    </div>

    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--single {
            height: 42px;
            border-color: #d1d5db;
            border-radius: 0.375rem;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 42px;
            padding-left: 12px;
            color: #374151;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
        }
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #059669;
        }
        .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
            background-color: #059669 !important;
        }
    </style>
    @endpush

    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#formula_id').select2({
                placeholder: 'Select a formula (optional)',
                allowClear: true,
                width: '100%'
            });
        });

        function productPhotos() {
            return {
                photos: [],
                isDragging: false,
                
                handleFiles(files) {
                    Array.from(files).forEach(file => {
                        if (file.type.startsWith('image/') && file.size <= 2097152) { // 2MB
                            this.photos.push({
                                file: file,
                                name: file.name,
                                preview: URL.createObjectURL(file)
                            });
                        }
                    });
                    this.updateInput();
                },
                
                handleDrop(e) {
                    this.isDragging = false;
                    this.handleFiles(e.dataTransfer.files);
                },
                
                removePhoto(index) {
                    URL.revokeObjectURL(this.photos[index].preview);
                    this.photos.splice(index, 1);
                    this.updateInput();
                },
                
                updateInput() {
                    const input = document.getElementById('product_photos');
                    const dataTransfer = new DataTransfer();
                    
                    this.photos.forEach(photo => {
                        dataTransfer.items.add(photo.file);
                    });
                    
                    input.files = dataTransfer.files;
                }
            }
        }
    </script>
    @endpush
</x-admin-layout>
