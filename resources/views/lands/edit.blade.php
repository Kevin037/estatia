<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">
                    Edit Land
                </h2>
                <p class="mt-1 text-sm text-gray-600">Update land record details and photo</p>
            </div>
            <a href="{{ route('lands.index') }}" class="btn btn-secondary">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Back to List
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <form action="{{ route('lands.update', $land->id) }}" method="POST" enctype="multipart/form-data" x-data="{ loading: false }" @submit="loading = true" id="landEditForm">
            @csrf
            @method('PUT')

            <!-- Photo Section -->
            <div class="card mb-6">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-gray-900">Land Photo</h3>
                    <p class="mt-1 text-sm text-gray-600">Upload a photo of the land</p>
                </div>

                <div class="space-y-4">
                    <div>
                        <label for="photo" class="form-label">Photo</label>
                        <input 
                            type="file" 
                            name="photo" 
                            id="photo" 
                            class="form-input @error('photo') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror"
                            accept="image/jpeg,image/jpg,image/png"
                        >
                        @error('photo')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">JPG, PNG (Max: 2MB)</p>
                    </div>
                </div>
            </div>

            <!-- Land Information Section -->
            <div class="card mb-6">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-gray-900">Land Information</h3>
                    <p class="mt-1 text-sm text-gray-600">Basic details about the land</p>
                </div>

                <div class="space-y-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="form-label">
                            Land Name <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="name" 
                            id="name" 
                            value="{{ old('name', $land->name) }}"
                            class="form-input @error('name') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror"
                            placeholder="e.g., Green Valley Estate"
                            required
                            autofocus
                        >
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Address -->
                    <div>
                        <label for="address" class="form-label">
                            Address <span class="text-red-500">*</span>
                        </label>
                        <textarea 
                            name="address" 
                            id="address" 
                            rows="3"
                            class="form-input @error('address') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror"
                            placeholder="Enter full address"
                            required
                        >{{ old('address', $land->address) }}</textarea>
                        @error('address')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Width and Length -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Width -->
                        <div>
                            <label for="wide" class="form-label">
                                Width (m) <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="number" 
                                name="wide" 
                                id="wide" 
                                value="{{ old('wide', $land->wide) }}"
                                step="0.01"
                                min="0"
                                class="form-input @error('wide') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror"
                                placeholder="e.g., 50.00"
                                required
                            >
                            @error('wide')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Width in meters</p>
                        </div>

                        <!-- Length -->
                        <div>
                            <label for="length" class="form-label">
                                Length (m) <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="number" 
                                name="length" 
                                id="length" 
                                value="{{ old('length', $land->length) }}"
                                step="0.01"
                                min="0"
                                class="form-input @error('length') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror"
                                placeholder="e.g., 35.00"
                                required
                            >
                            @error('length')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Length in meters</p>
                        </div>
                    </div>

                    <!-- Location -->
                    <div>
                        <label for="location" class="form-label">Location</label>
                        <textarea 
                            name="location" 
                            id="location" 
                            rows="2"
                            class="form-input @error('location') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror"
                            placeholder="Nearby landmarks, access roads, etc."
                        >{{ old('location', $land->location) }}</textarea>
                        @error('location')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Additional location details</p>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="desc" class="form-label">Description</label>
                        <textarea 
                            name="desc" 
                            id="desc" 
                            rows="4"
                            class="form-input @error('desc') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror"
                            placeholder="Detailed description about the land, potential uses, etc."
                        >{{ old('desc', $land->desc) }}</textarea>
                        @error('desc')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Additional notes or description</p>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-x-4">
                <a href="{{ route('lands.index') }}" class="btn btn-secondary">
                    Cancel
                </a>
                <button type="submit" class="btn btn-primary" :disabled="loading">
                    <svg x-show="loading" class="animate-spin -ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span :class="{'opacity-0': loading}">Update Land</span>
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
