<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900">Edit Product</h2>
            <a href="{{ route('products.index') }}" class="btn btn-secondary">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Back to List
            </a>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="card mb-6">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-gray-900">Product Information</h3>
                    <p class="mt-1 text-sm text-gray-600">Update product details using the fields below</p>
                </div>

                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="name" class="form-label">Product Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" class="form-input @error('name') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror" placeholder="Product name" required>
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="sku" class="form-label">SKU <span class="text-red-500">*</span></label>
                        <input type="text" name="sku" id="sku" value="{{ old('sku', $product->sku) }}" class="form-input @error('sku') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror" placeholder="e.g., PRD-001" required>
                        @error('sku')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div x-data="{ photoPreview: null }">
                        <label for="photo" class="form-label">Product Photo</label>
                        <div class="mt-2 flex items-center gap-4">
                            <div class="flex-shrink-0">
                                <template x-if="photoPreview">
                                    <img :src="photoPreview" alt="Photo preview" class="h-24 w-24 rounded object-cover border-2 border-gray-200">
                                </template>
                                <template x-if="!photoPreview">
                                    @if($product->photo)
                                        <img src="{{ $product->photo_url }}" alt="{{ $product->name }}" class="h-24 w-24 rounded object-cover border-2 border-gray-200">
                                    @else
                                        <div class="h-24 w-24 rounded bg-gray-100 flex items-center justify-center border-2 border-dashed border-gray-300">
                                            <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                            </svg>
                                        </div>
                                    @endif
                                </template>
                            </div>
                            <div class="flex-1">
                                <input type="file" name="photo" id="photo" accept="image/*" 
                                    @change="photoPreview = $event.target.files.length > 0 ? URL.createObjectURL($event.target.files[0]) : null"
                                    class="form-input @error('photo') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror">
                                <p class="mt-2 text-xs text-gray-500">JPG, PNG, GIF up to 2MB. Upload new file to replace current photo.</p>
                            </div>
                        </div>
                        @error('photo')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="price" class="form-label">Price <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                            <input type="number" name="price" id="price" value="{{ old('price', $product->price) }}" class="form-input pl-12 @error('price') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror" placeholder="0" step="0.01" min="0" required>
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
                                <option value="{{ $formula->id }}" {{ old('formula_id', $product->formula_id) == $formula->id ? 'selected' : '' }}>
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
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                        </svg>
                    </span>
                    <span x-text="loading ? 'Updating…' : 'Update Product'"></span>
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
