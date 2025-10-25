<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900">Edit Formula</h2>
            <a href="{{ route('formulas.index') }}" class="btn btn-secondary">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Back to List
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <form action="{{ route('formulas.update', $formula) }}" method="POST" x-data="formulaForm()">
            @csrf
            @method('PUT')

            <div class="card mb-6">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-gray-900">Formula Information</h3>
                    <p class="mt-1 text-sm text-gray-600">Update formula information</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="code" class="form-label">Formula Code <span class="text-red-500">*</span></label>
                        <input type="text" name="code" id="code" value="{{ old('code', $formula->code) }}" class="form-input @error('code') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror" placeholder="e.g., F-001" required>
                        @error('code')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="name" class="form-label">Formula Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $formula->name) }}" class="form-input @error('name') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror" placeholder="Formula name" required>
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Formula Details Card -->
            <div class="card mb-6">
                <div class="card-header flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Formula Details</h3>
                        <p class="mt-1 text-sm text-gray-600">Update materials and quantities for this formula</p>
                    </div>
                    <button type="button" @click="addRow()" class="btn btn-primary btn-sm">
                        <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Add Row
                    </button>
                </div>

                @error('material_ids')
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    </div>
                @enderror

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Material <span class="text-red-500">*</span></th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity <span class="text-red-500">*</span></th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-for="(row, index) in rows" :key="index">
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900" x-text="index + 1"></td>
                                    <td class="px-4 py-3">
                                        <select :name="'material_ids[]'" x-model="row.material_id" @change="updatePrice(index)" class="form-input text-sm" required>
                                            <option value="">Select Material</option>
                                            @foreach($materials as $material)
                                                <option value="{{ $material->id }}" data-price="{{ $material->price }}">
                                                    {{ $material->name }} (Stock: {{ $material->qty }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        <span x-text="'Rp ' + formatNumber(row.price)"></span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="number" :name="'quantities[]'" x-model="row.qty" @input="calculateSubtotal(index)" step="0.01" min="0.01" class="form-input text-sm w-32" placeholder="0.00" required>
                                    </td>
                                    <td class="px-4 py-3 text-sm font-semibold text-emerald-600">
                                        <span x-text="'Rp ' + formatNumber(row.subtotal)"></span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <button type="button" @click="removeRow(index)" x-show="rows.length > 1" class="btn-icon btn-icon-danger" title="Remove">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-right text-sm font-semibold text-gray-900">Total Cost:</td>
                                <td colspan="2" class="px-4 py-3 text-sm font-bold text-emerald-600 text-lg">
                                    <span x-text="'Rp ' + formatNumber(totalCost)"></span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="flex items-center justify-between bg-gray-50 px-6 py-4 rounded-lg">
                <a href="{{ route('formulas.index') }}" class="btn btn-secondary">
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
                    <span x-text="loading ? 'Updating…' : 'Update Formula'"></span>
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        function formulaForm() {
            return {
                rows: [
                    @foreach($formula->details as $detail)
                    {
                        material_id: '{{ $detail->material_id }}',
                        price: {{ $detail->material->price }},
                        qty: '{{ $detail->qty }}',
                        subtotal: {{ $detail->material->price * $detail->qty }}
                    },
                    @endforeach
                ],
                
                get totalCost() {
                    return this.rows.reduce((sum, row) => sum + parseFloat(row.subtotal || 0), 0);
                },
                
                addRow() {
                    this.rows.push({
                        material_id: '',
                        price: 0,
                        qty: '',
                        subtotal: 0
                    });
                },
                
                removeRow(index) {
                    if (this.rows.length > 1) {
                        this.rows.splice(index, 1);
                    }
                },
                
                updatePrice(index) {
                    const row = this.rows[index];
                    const select = event.target;
                    const selectedOption = select.options[select.selectedIndex];
                    row.price = parseFloat(selectedOption.getAttribute('data-price')) || 0;
                    this.calculateSubtotal(index);
                },
                
                calculateSubtotal(index) {
                    const row = this.rows[index];
                    row.subtotal = (parseFloat(row.price) || 0) * (parseFloat(row.qty) || 0);
                },
                
                formatNumber(number) {
                    return new Intl.NumberFormat('id-ID').format(number || 0);
                }
            }
        }
    </script>
    @endpush
</x-admin-layout>
