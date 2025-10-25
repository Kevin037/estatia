<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">
                    Edit Project
                </h2>
                <p class="mt-1 text-sm text-gray-600">{{ $project->name }}</p>
            </div>
            <a href="{{ route('projects.index') }}" class="btn btn-secondary">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Back to List
            </a>
        </div>
    </x-slot>

    <form action="{{ route('projects.update', $project) }}" method="POST" x-data="projectForm()">
        @csrf
        @method('PUT')

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
                        value="{{ old('name', $project->name) }}"
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
                            <option value="{{ $land->id }}" {{ old('land_id', $project->land_id) == $land->id ? 'selected' : '' }}>
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
                        value="{{ old('dt_start', $project->dt_start->format('Y-m-d')) }}"
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
                        value="{{ old('dt_end', $project->dt_end->format('Y-m-d')) }}"
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
                        <option value="pending" {{ old('status', $project->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ old('status', $project->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ old('status', $project->status) == 'completed' ? 'selected' : '' }}>Completed</option>
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
                            <option value="{{ $contractor->id }}" 
                                {{ in_array($contractor->id, old('contractors', $project->contractors->pluck('id')->toArray())) ? 'selected' : '' }}>
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

        <!-- Note: Milestones and Clusters sections similar to create.blade.php but with pre-filled data -->
        <!-- For brevity, showing simplified structure -->
        
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <p class="text-sm text-yellow-800">
                <strong>Note:</strong> Editing will replace all existing clusters, units, and milestone data. 
                Current units: {{ $project->units()->count() }}, Current clusters: {{ $project->clusters()->count() }}
            </p>
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
                Update Project
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
                // Similar to create form
            }
        }
    </script>
    @endpush
</x-admin-layout>
