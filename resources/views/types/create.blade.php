<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">
                    Add New Type
                </h2>
                <p class="mt-1 text-sm text-gray-600">Create a new property type with land and building area</p>
            </div>
            <a href="{{ route('types.index') }}" class="btn btn-secondary">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Back to List
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <form action="{{ route('types.store') }}" method="POST" id="typeCreateForm">
            @csrf

            <!-- Type Information Section -->
            <div class="card mb-6">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-gray-900">Type Information</h3>
                    <p class="mt-1 text-sm text-gray-600">Basic details about the property type</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div class="md:col-span-2">
                        <label for="name" class="form-label">
                            Type Name <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="name" 
                            id="name" 
                            value="{{ old('name') }}"
                            class="form-input @error('name') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror"
                            placeholder="e.g., Type 36, Type 45"
                            required
                            autofocus
                        >
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Land Area -->
                    <div>
                        <label for="land_area" class="form-label">
                            Land Area (m²) <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="number" 
                            name="land_area" 
                            id="land_area" 
                            value="{{ old('land_area') }}"
                            step="0.01"
                            min="0"
                            class="form-input @error('land_area') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror"
                            placeholder="e.g., 72.00"
                            required
                        >
                        @error('land_area')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Total land area in square meters</p>
                    </div>

                    <!-- Building Area -->
                    <div>
                        <label for="building_area" class="form-label">
                            Building Area (m²) <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="number" 
                            name="building_area" 
                            id="building_area" 
                            value="{{ old('building_area') }}"
                            step="0.01"
                            min="0"
                            class="form-input @error('building_area') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror"
                            placeholder="e.g., 36.00"
                            required
                        >
                        @error('building_area')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Total building area in square meters</p>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-between bg-gray-50 px-6 py-4 rounded-lg">
                <a href="{{ route('types.index') }}" class="btn btn-secondary">
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
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span :class="{'opacity-10': loading}">Create Type</span>
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
