<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">
                    Add New Milestone
                </h2>
                <p class="mt-1 text-sm text-gray-600">Create a new milestone record with name and description</p>
            </div>
            <a href="{{ route('milestones.index') }}" class="btn btn-secondary">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Back to List
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <form action="{{ route('milestones.store') }}" method="POST" id="milestoneCreateForm">
            @csrf

            <!-- Milestone Information Section -->
            <div class="card mb-6">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-gray-900">Milestone Information</h3>
                    <p class="mt-1 text-sm text-gray-600">Basic details about the milestone</p>
                </div>

                <div class="space-y-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="form-label">
                            Name <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="name" 
                            id="name" 
                            value="{{ old('name') }}"
                            class="form-input @error('name') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror"
                            placeholder="Enter milestone name"
                            required
                            autofocus
                        >
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="desc" class="form-label">Description</label>
                        <textarea 
                            name="desc" 
                            id="desc" 
                            rows="4"
                            class="form-input @error('desc') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror"
                            placeholder="Enter milestone description (optional)"
                        >{{ old('desc') }}</textarea>
                        @error('desc')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Optional: Provide detailed information about this milestone</p>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-between bg-gray-50 px-6 py-4 rounded-lg">
                <a href="{{ route('milestones.index') }}" class="btn btn-secondary">
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
                    <span :class="{'opacity-10': loading}">Create Milestone</span>
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
