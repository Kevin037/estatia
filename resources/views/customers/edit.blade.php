<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900">Edit Customer</h2>
            <a href="{{ route('customers.index') }}" class="btn btn-secondary">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Back to List
            </a>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <form action="{{ route('customers.update', $customer) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card mb-6">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-gray-900">Customer Information</h3>
                    <p class="mt-1 text-sm text-gray-600">Update the customer details below</p>
                </div>

                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="name" class="form-label">Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $customer->name) }}" class="form-input @error('name') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror" placeholder="Customer name" required>
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="form-label">Phone Number <span class="text-red-500">*</span></label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $customer->phone) }}" class="form-input @error('phone') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror" placeholder="081234567890" required>
                        @error('phone')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between bg-gray-50 px-6 py-4 rounded-lg">
                <a href="{{ route('customers.index') }}" class="btn btn-secondary">
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
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                    </span>
                    <span x-text="loading ? 'Updating…' : 'Update Customer'"></span>
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
