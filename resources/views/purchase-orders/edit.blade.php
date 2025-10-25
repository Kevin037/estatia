<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">
                    Edit Purchase Order
                </h2>
                <p class="mt-1 text-sm text-gray-600">Update purchase order: {{ $purchaseOrder->no }}</p>
            </div>
            <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Back to List
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto">
        <form action="{{ route('purchase-orders.update', $purchaseOrder) }}" method="POST" id="purchaseOrderForm">
            @csrf
            @method('PUT')

            <!-- Purchase Order Information -->
            <div class="card mb-6">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-gray-900">Purchase Order Information</h3>
                    <p class="mt-1 text-sm text-gray-600">Basic details for this purchase order</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Project -->
                    <div>
                        <label for="project_id" class="form-label">
                            Project <span class="text-red-500">*</span>
                        </label>
                        <select 
                            name="project_id" 
                            id="project_id" 
                            class="form-input select2 @error('project_id') border-red-500 @enderror"
                            required
                        >
                            <option value="">Select Project</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ old('project_id', $purchaseOrder->project_id) == $project->id ? 'selected' : '' }}>
                                    {{ $project->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('project_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date -->
                    <div>
                        <label for="dt" class="form-label">
                            Date <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="date" 
                            name="dt" 
                            id="dt" 
                            value="{{ old('dt', $purchaseOrder->dt->format('Y-m-d')) }}"
                            class="form-input @error('dt') border-red-500 @enderror"
                            required
                        >
                        @error('dt')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Supplier -->
                    <div>
                        <label for="supplier_id" class="form-label">
                            Supplier <span class="text-red-500">*</span>
                        </label>
                        <select 
                            name="supplier_id" 
                            id="supplier_id" 
                            class="form-input select2 @error('supplier_id') border-red-500 @enderror"
                            required
                        >
                            <option value="">Select Supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id', $purchaseOrder->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('supplier_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="form-label">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select 
                            name="status" 
                            id="status" 
                            class="form-input @error('status') border-red-500 @enderror"
                            required
                        >
                            <option value="pending" {{ old('status', $purchaseOrder->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="completed" {{ old('status', $purchaseOrder->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                        @error('status')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Materials Section -->
            <div class="card mb-6" x-data="materialManager()">
                <div class="card-header flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Materials</h3>
                        <p class="mt-1 text-sm text-gray-600">Select materials and quantities for this purchase order</p>
                    </div>
                    <button 
                        type="button" 
                        @click="addRow()"
                        class="btn btn-primary btn-sm"
                    >
                        <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Add Row
                    </button>
                </div>

                @error('materials')
                    <div class="px-6 pb-2">
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    </div>
                @enderror

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 5%">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 50%">Material <span class="text-red-500">*</span></th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 15%">Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 15%">Qty <span class="text-red-500">*</span></th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 10%">Subtotal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 5%">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="materialsTableBody">
                            <template x-for="(row, index) in rows" :key="row.id">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="index + 1"></td>
                                    <td class="px-6 py-4">
                                        <select 
                                            :name="`materials[${index}][material_id]`"
                                            class="form-input form-input-sm select2-material"
                                            @change="updatePrice(index, $event.target.value)"
                                            :value="row.material_id"
                                            required
                                        >
                                            <option value="">Select Material</option>
                                            @foreach($materials as $material)
                                                <option 
                                                    value="{{ $material->id }}"
                                                    data-price="{{ $material->price }}"
                                                    data-stock="{{ $material->qty }}"
                                                >
                                                    {{ $material->name }} (Stock: {{ $material->qty }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <p class="text-xs text-red-600 mt-1" x-show="row.error" x-text="row.error"></p>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <span x-text="formatRupiah(row.price)"></span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <input 
                                            type="number" 
                                            :name="`materials[${index}][qty]`"
                                            x-model="row.qty"
                                            @input="updateSubtotal(index)"
                                            class="form-input form-input-sm"
                                            placeholder="0"
                                            step="0.01"
                                            min="0.01"
                                            required
                                        >
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <span x-text="formatRupiah(row.subtotal)"></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <button 
                                            type="button"
                                            @click="removeRow(index)"
                                            class="text-red-600 hover:text-red-900"
                                            :disabled="rows.length === 1"
                                            :class="{'opacity-50 cursor-not-allowed': rows.length === 1}"
                                        >
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-right font-semibold text-gray-900">
                                    Total:
                                </td>
                                <td colspan="2" class="px-6 py-4 font-bold text-lg text-gray-900">
                                    <span x-text="formatRupiah(total)"></span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-between bg-gray-50 px-6 py-4 rounded-lg">
                <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary">
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
                    class="btn btn-primary relative"
                >
                    <template x-if="loading">
                        <svg class="absolute left-3 h-5 w-5 animate-spin text-white" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                    </template>
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                    <span :class="{'opacity-10': loading}">Update Purchase Order</span>
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        function materialManager() {
            return {
                rows: @json($purchaseOrder->details->map(function($detail) {
                    return [
                        'id' => $detail->id,
                        'material_id' => $detail->material_id,
                        'price' => $detail->material->price,
                        'qty' => $detail->qty,
                        'subtotal' => $detail->material->price * $detail->qty,
                        'error' => ''
                    ];
                })),
                materials: @json($materials),
                
                init() {
                    this.$nextTick(() => {
                        this.initSelect2();
                        // Set initial values for select2
                        this.rows.forEach((row, index) => {
                            $(`select[name="materials[${index}][material_id]"]`).val(row.material_id).trigger('change');
                        });
                    });
                },

                addRow() {
                    this.rows.push({ 
                        id: Date.now(), 
                        material_id: '', 
                        price: 0, 
                        qty: 0, 
                        subtotal: 0,
                        error: ''
                    });
                    this.$nextTick(() => {
                        this.initSelect2();
                    });
                },

                removeRow(index) {
                    if (this.rows.length > 1) {
                        this.rows.splice(index, 1);
                    }
                },

                updatePrice(index, materialId) {
                    if (materialId) {
                        const material = this.materials.find(m => m.id == materialId);
                        if (material) {
                            this.rows[index].price = parseFloat(material.price);
                            this.rows[index].material_id = materialId;
                            this.updateSubtotal(index);
                            this.rows[index].error = '';
                        }
                    } else {
                        this.rows[index].price = 0;
                        this.rows[index].material_id = '';
                        this.updateSubtotal(index);
                    }
                },

                updateSubtotal(index) {
                    const row = this.rows[index];
                    row.subtotal = row.price * (parseFloat(row.qty) || 0);
                },

                get total() {
                    return this.rows.reduce((sum, row) => sum + row.subtotal, 0);
                },

                formatRupiah(amount) {
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount || 0);
                },

                initSelect2() {
                    $('.select2').select2({
                        theme: 'classic',
                        width: '100%'
                    });

                    $('.select2-material').select2({
                        theme: 'classic',
                        width: '100%',
                        placeholder: 'Select Material'
                    });
                }
            }
        }
    </script>
    @endpush
</x-admin-layout>
