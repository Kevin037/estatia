<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">
                    Add New Project
                </h2>
                <p class="mt-1 text-sm text-gray-600">Create a new project with contractors, milestones, and clusters</p>
            </div>
            <a href="{{ route('projects.index') }}" class="btn btn-secondary">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Back to List
            </a>
        </div>
    </x-slot>

    <form action="{{ route('projects.store') }}" method="POST" x-data="projectForm()">
        @csrf

        <!-- Project Information -->
        <div class="card mb-6">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900">Project Information</h3>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="form-label required">Project Name</label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name" 
                        class="form-input @error('name') border-red-500 @enderror" 
                        value="{{ old('name') }}"
                        required
                    >
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="land_id" class="form-label required">Land/Location</label>
                    <select 
                        name="land_id" 
                        id="land_id" 
                        class="form-input select2 @error('land_id') border-red-500 @enderror"
                        required
                    >
                        <option value="">Select Land</option>
                        @foreach($lands as $land)
                            <option value="{{ $land->id }}" {{ old('land_id') == $land->id ? 'selected' : '' }}>
                                {{ $land->address }}
                            </option>
                        @endforeach
                    </select>
                    @error('land_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="dt_start" class="form-label required">Start Date</label>
                    <input 
                        type="date" 
                        name="dt_start" 
                        id="dt_start" 
                        class="form-input @error('dt_start') border-red-500 @enderror" 
                        value="{{ old('dt_start') }}"
                        required
                    >
                    @error('dt_start')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="dt_end" class="form-label required">End Date</label>
                    <input 
                        type="date" 
                        name="dt_end" 
                        id="dt_end" 
                        class="form-input @error('dt_end') border-red-500 @enderror" 
                        value="{{ old('dt_end') }}"
                        required
                    >
                    @error('dt_end')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status" class="form-label required">Status</label>
                    <select 
                        name="status" 
                        id="status" 
                        class="form-input select2 @error('status') border-red-500 @enderror"
                        required
                    >
                        <option value="">Select Status</option>
                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="contractors" class="form-label required">Contractors (Multiple)</label>
                    <select 
                        name="contractors[]" 
                        id="contractors" 
                        class="form-input select2-multiple @error('contractors') border-red-500 @enderror"
                        multiple
                        required
                    >
                        @foreach($contractors as $contractor)
                            <option value="{{ $contractor->id }}" {{ in_array($contractor->id, old('contractors', [])) ? 'selected' : '' }}>
                                {{ $contractor->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('contractors')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Project Milestones -->
        <div class="card mb-6">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900">Project Milestones</h3>
            </div>

            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Milestone Name</th>
                            <th>Target Date</th>
                            <th>Completed Date</th>
                            <th class="text-center" style="width: 80px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(milestone, index) in milestones" :key="milestone.id">
                            <tr>
                                <td>
                                    <select 
                                        :name="`milestones[${index}][milestone_id]`" 
                                        class="form-input"
                                        required
                                    >
                                        <option value="">Select Milestone</option>
                                        @foreach($milestones as $milestone)
                                            <option value="{{ $milestone->id }}">{{ $milestone->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input 
                                        type="date" 
                                        :name="`milestones[${index}][target_dt]`" 
                                        class="form-input"
                                        required
                                    >
                                </td>
                                <td>
                                    <input 
                                        type="date" 
                                        :name="`milestones[${index}][completed_dt]`" 
                                        class="form-input"
                                    >
                                </td>
                                <td class="text-center">
                                    <button 
                                        type="button" 
                                        @click="removeMilestone(index)" 
                                        class="btn btn-sm btn-danger"
                                        x-show="milestones.length > 1"
                                    >
                                        Remove
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <button type="button" @click="addMilestone()" class="btn btn-secondary">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Add Milestone
                </button>
            </div>
        </div>

        <!-- Clusters Section -->
        <div class="card mb-6">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900">Clusters</h3>
            </div>

            <template x-for="(cluster, clusterIndex) in clusters" :key="cluster.id">
                <div class="border border-gray-200 rounded-lg p-4 mb-4">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-md font-semibold text-gray-800" x-text="`Cluster ${clusterIndex + 1}`"></h4>
                        <button 
                            type="button" 
                            @click="removeCluster(clusterIndex)" 
                            class="btn btn-sm btn-danger"
                            x-show="clusters.length > 1"
                        >
                            Remove Cluster
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="form-label required">Cluster Name</label>
                            <input 
                                type="text" 
                                :name="`clusters[${clusterIndex}][name]`" 
                                class="form-input"
                                required
                            >
                        </div>

                        <div>
                            <label class="form-label required">Road Width (m)</label>
                            <input 
                                type="number" 
                                step="0.01"
                                :name="`clusters[${clusterIndex}][road_width]`" 
                                class="form-input"
                                required
                            >
                        </div>

                        <div class="md:col-span-2">
                            <label class="form-label">Description</label>
                            <textarea 
                                :name="`clusters[${clusterIndex}][desc]`" 
                                class="form-input"
                                rows="2"
                            ></textarea>
                        </div>

                        <div class="md:col-span-2">
                            <label class="form-label">Facilities</label>
                            <textarea 
                                :name="`clusters[${clusterIndex}][facilities]`" 
                                class="form-input"
                                rows="2"
                            ></textarea>
                        </div>
                    </div>

                    <!-- Products in Cluster -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h5 class="text-sm font-semibold text-gray-700 mb-3">Products</h5>
                        
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th style="width: 150px;">Quantity</th>
                                        <th class="text-center" style="width: 80px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(product, productIndex) in cluster.products" :key="product.id">
                                        <tr>
                                            <td>
                                                <select 
                                                    :name="`clusters[${clusterIndex}][products][${productIndex}][product_id]`" 
                                                    class="form-input"
                                                    required
                                                >
                                                    <option value="">Select Product</option>
                                                    @foreach($products as $product)
                                                        <option value="{{ $product->id }}">
                                                            {{ $product->code }} - {{ $product->type->name ?? 'N/A' }} ({{ 'Rp ' . number_format($product->price, 0, ',', '.') }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input 
                                                    type="number" 
                                                    :name="`clusters[${clusterIndex}][products][${productIndex}][qty]`" 
                                                    class="form-input"
                                                    min="1"
                                                    required
                                                >
                                            </td>
                                            <td class="text-center">
                                                <button 
                                                    type="button" 
                                                    @click="removeProduct(clusterIndex, productIndex)" 
                                                    class="btn btn-sm btn-danger"
                                                    x-show="cluster.products.length > 1"
                                                >
                                                    Remove
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            <button type="button" @click="addProduct(clusterIndex)" class="btn btn-sm btn-secondary">
                                <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                                Add Product
                            </button>
                        </div>
                    </div>
                </div>
            </template>

            <div class="mt-4">
                <button type="button" @click="addCluster()" class="btn btn-secondary">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Add Cluster
                </button>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-end gap-x-4">
            <a href="{{ route('projects.index') }}" class="btn btn-secondary">
                Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Create Project
            </button>
        </div>
    </form>

    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    @endpush

    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                theme: 'default',
                width: '100%'
            });

            $('.select2-multiple').select2({
                theme: 'default',
                width: '100%',
                placeholder: 'Select contractors'
            });
        });

        function projectForm() {
            return {
                milestones: [
                    { id: Date.now() }
                ],
                clusters: [
                    { 
                        id: Date.now(), 
                        products: [
                            { id: Date.now() }
                        ] 
                    }
                ],
                
                // Milestone methods
                addMilestone() {
                    this.milestones.push({ id: Date.now() });
                },
                removeMilestone(index) {
                    if (this.milestones.length > 1) {
                        this.milestones.splice(index, 1);
                    }
                },

                // Cluster methods
                addCluster() {
                    this.clusters.push({ 
                        id: Date.now(), 
                        products: [
                            { id: Date.now() }
                        ]
                    });
                },
                removeCluster(index) {
                    if (this.clusters.length > 1) {
                        this.clusters.splice(index, 1);
                    }
                },

                // Product methods
                addProduct(clusterIndex) {
                    this.clusters[clusterIndex].products.push({ id: Date.now() });
                },
                removeProduct(clusterIndex, productIndex) {
                    if (this.clusters[clusterIndex].products.length > 1) {
                        this.clusters[clusterIndex].products.splice(productIndex, 1);
                    }
                }
            }
        }
    </script>
    @endpush
</x-admin-layout>
