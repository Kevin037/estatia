<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900">Edit Supplier</h2>
            <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Back to List
            </a>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <form action="{{ route('suppliers.update', $supplier) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card mb-6">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-gray-900">Supplier Information</h3>
                    <p class="mt-1 text-sm text-gray-600">Update supplier information below</p>
                </div>

                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="name" class="form-label">Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $supplier->name) }}" class="form-input @error('name') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror" placeholder="Supplier name" required>
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="form-label">Phone Number <span class="text-red-500">*</span></label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $supplier->phone) }}" class="form-input @error('phone') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror" placeholder="081234567890" required>
                        @error('phone')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="materials" class="form-label">Select Materials (Optional)</label>
                        <p class="mb-2 text-xs text-gray-500">Select materials to assign to this supplier. Materials can be reassigned from other suppliers.</p>
                        <select name="material_ids[]" id="materials" multiple class="form-input">
                            @foreach($availableMaterials as $material)
                                <option value="{{ $material->id }}" {{ in_array($material->id, old('material_ids', $assignedMaterialIds)) ? 'selected' : '' }}>
                                    {{ $material->name }} (Stock: {{ $material->qty }}, Price: Rp {{ number_format($material->price, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                        @error('material_ids')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between bg-gray-50 px-6 py-4 rounded-lg">
                <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">
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
                    <span x-text="loading ? 'Updating…' : 'Update Supplier'"></span>
                </button>
            </div>
        </form>
    </div>

    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--multiple {
            min-height: 42px;
            border-color: #d1d5db;
            border-radius: 0.375rem;
        }
        .select2-container--default.select2-container--focus .select2-selection--multiple {
            border-color: #059669;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #059669;
            border-color: #059669;
            color: white;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: white;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
            color: #f3f4f6;
        }
        .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
            background-color: #059669 !important;
        }
    </style>
    @endpush

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#materials').select2({
                placeholder: 'Search and select materials...',
                allowClear: true,
                width: '100%',
                closeOnSelect: false
            });
        });
    </script>
    @endpush
</x-admin-layout>
