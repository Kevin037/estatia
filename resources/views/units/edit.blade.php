<x-admin-layout>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Unit</h1>
            <p class="mt-1 text-sm text-gray-600">Update unit information and manage photos</p>
        </div>
    </div>

    <div class="card">
        <form action="{{ route('units.update', $unit) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="form-label">Unit Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $unit->name) }}" class="form-input @error('name') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror" required>
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="no" class="form-label">Unit Number</label>
                        <input type="text" name="no" id="no" value="{{ old('no', $unit->no) }}" class="form-input @error('no') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror" readonly>
                        <p class="mt-1 text-xs text-gray-500">Unit number is auto-generated and cannot be changed</p>
                        @error('no')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="cluster_id" class="form-label">Cluster <span class="text-red-500">*</span></label>
                        <select name="cluster_id" id="cluster_id" class="form-input @error('cluster_id') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror" required disabled>
                            <option value="">Select Cluster</option>
                            @foreach($clusters as $cluster)
                                <option value="{{ $cluster->id }}" {{ old('cluster_id', $unit->cluster_id) == $cluster->id ? 'selected' : '' }}>
                                    {{ $cluster->name }} ({{ $cluster->project->name ?? 'No Project' }})
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="cluster_id" value="{{ $unit->cluster_id }}">
                        <p class="mt-1 text-xs text-gray-500">Cluster cannot be changed after unit creation</p>
                        @error('cluster_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="product_id" class="form-label">Product/Type <span class="text-red-500">*</span></label>
                        <select name="product_id" id="product_id" class="form-input @error('product_id') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror" required disabled>
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ old('product_id', $unit->product_id) == $product->id ? 'selected' : '' }}>
                                    {{ $product->type->name ?? 'Unknown' }} - {{ $product->sku }}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="product_id" value="{{ $unit->product_id }}">
                        <p class="mt-1 text-xs text-gray-500">Product cannot be changed after unit creation</p>
                        @error('product_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="price" class="form-label">Price <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                            <input type="number" name="price" id="price" value="{{ old('price', $unit->price) }}" class="form-input pl-12 @error('price') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror" placeholder="0" step="1000" min="0" required>
                        </div>
                        @error('price')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="sales_id" class="form-label">Sales Agent</label>
                        <select name="sales_id" id="sales_id" class="form-input @error('sales_id') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror">
                            <option value="">No Sales Assigned</option>
                            @foreach($salesList as $sales)
                                <option value="{{ $sales->id }}" {{ old('sales_id', $unit->sales_id) == $sales->id ? 'selected' : '' }}>
                                    {{ $sales->name }} ({{ $sales->phone }})
                                </option>
                            @endforeach
                        </select>
                        @error('sales_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="status" class="form-label">Status <span class="text-red-500">*</span></label>
                        <select name="status" id="status" class="form-input @error('status') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror" required>
                            <option value="available" {{ old('status', $unit->status) == 'available' ? 'selected' : '' }}>Available</option>
                            <option value="reserved" {{ old('status', $unit->status) == 'reserved' ? 'selected' : '' }}>Reserved</option>
                            <option value="sold" {{ old('status', $unit->status) == 'sold' ? 'selected' : '' }}>Sold</option>
                            <option value="handed_over" {{ old('status', $unit->status) == 'handed_over' ? 'selected' : '' }}>Handed Over</option>
                        </select>
                        @error('status')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="desc" class="form-label">Description</label>
                    <textarea name="desc" id="desc" rows="3" class="form-input @error('desc') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror" placeholder="Enter unit description...">{{ old('desc', $unit->desc) }}</textarea>
                    @error('desc')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="facilities" class="form-label">Facilities</label>
                    <textarea name="facilities" id="facilities" rows="3" class="form-input @error('facilities') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror" placeholder="Enter unit facilities...">{{ old('facilities', $unit->facilities) }}</textarea>
                    @error('facilities')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Existing Unit Photos -->
                @if($unit->unitPhotos->count() > 0)
                <div x-data="{ photosToDelete: [] }">
                    <label class="form-label">Existing Unit Photos</label>
                    <div class="mt-2 flex flex-wrap gap-3">
                        @foreach($unit->unitPhotos as $photo)
                        <div class="relative group" x-data="{ isDeleted: false }">
                            <div class="h-24 w-24 rounded overflow-hidden border-2 transition-all duration-200" 
                                 :class="isDeleted ? 'border-red-500 opacity-50' : 'border-gray-200'">
                                <img src="{{ $photo->photo_url }}" alt="{{ $photo->name }}" class="w-full h-full object-cover">
                            </div>
                            <button 
                                type="button"
                                @click="isDeleted = !isDeleted; 
                                        if(isDeleted) { 
                                            photosToDelete.push({{ $photo->id }});
                                        } else {
                                            photosToDelete = photosToDelete.filter(id => id !== {{ $photo->id }});
                                        }"
                                class="absolute -top-2 -right-2 rounded-full p-1.5 shadow-lg transition-all duration-200 opacity-0 group-hover:opacity-100"
                                :class="isDeleted ? 'bg-red-600 hover:bg-red-700' : 'bg-red-500 hover:bg-red-600'"
                                title="Delete photo">
                                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                            </button>
                            <input type="hidden" name="delete_photos[]" :value="{{ $photo->id }}" x-show="isDeleted">
                            <p class="text-xs mt-1 text-center truncate w-24" 
                               :class="isDeleted ? 'text-red-600 font-medium' : 'text-gray-600'">
                                <span x-show="isDeleted">Will delete</span>
                                <span x-show="!isDeleted">{{ $photo->name }}</span>
                            </p>
                        </div>
                        @endforeach
                    </div>
                    <p class="mt-2 text-xs text-gray-500">Click the trash icon to mark photos for deletion.</p>
                </div>
                @endif

                <!-- Add New Unit Photos -->
                <div x-data="unitPhotos()">
                    <label class="form-label">Add New Unit Photos</label>
                    <div 
                        @dragover.prevent="isDragging = true"
                        @dragleave.prevent="isDragging = false"
                        @drop.prevent="handleDrop"
                        :class="isDragging ? 'border-emerald-500 bg-emerald-50' : 'border-gray-300'"
                        class="mt-2 border-2 border-dashed rounded-lg p-6 transition-colors duration-200"
                    >
                        <input 
                            type="file" 
                            id="unit_photos" 
                            name="unit_photos[]"
                            accept="image/*" 
                            multiple
                            @change="handleFiles($event.target.files)"
                            class="hidden">
                        
                        <div class="text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                            </svg>
                            <div class="mt-4 flex text-sm leading-6 text-gray-600 justify-center">
                                <label for="unit_photos" class="relative cursor-pointer rounded-md bg-white font-semibold text-emerald-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-emerald-600 focus-within:ring-offset-2 hover:text-emerald-500">
                                    <span>Upload files</span>
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs leading-5 text-gray-600">PNG, JPG, GIF up to 2MB each (multiple files allowed)</p>
                        </div>
                    </div>

                    <!-- Preview new photos to upload -->
                    <div x-show="photos.length > 0" class="mt-4">
                        <p class="text-sm text-gray-700 mb-2 font-medium">New Photos to Upload:</p>
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

                    @error('unit_photos')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('unit_photos.*')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-between bg-gray-50 px-6 py-4 rounded-lg mt-6">
                <a href="{{ route('units.show', $unit) }}" class="btn btn-secondary">
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
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                        </svg>
                    </span>
                    <span x-text="loading ? 'Updating…' : 'Update Unit'"></span>
                </button>
            </div>
        </form>
    </div>

    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    @endpush

    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('#cluster_id, #product_id, #sales_id, #status').select2({
                width: '100%'
            });
        });

        function unitPhotos() {
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
                    const input = document.getElementById('unit_photos');
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
