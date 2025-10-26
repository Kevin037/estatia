<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('projects.index') }}" class="text-gray-600 hover:text-gray-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $project->name }}</h2>
                    <p class="text-sm text-gray-500 mt-1">Project Details & Overview</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('projects.edit', $project) }}" class="btn btn-secondary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit Project
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Project Status Badge -->
    <div class="mb-6">
        @php
            $statusClasses = [
                'pending' => 'bg-yellow-100 text-yellow-800',
                'in_progress' => 'bg-blue-100 text-blue-800',
                'completed' => 'bg-green-100 text-green-800',
            ];
        @endphp
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusClasses[$project->status] ?? 'bg-gray-100 text-gray-800' }}">
            @if($project->status === 'completed')
                <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            @elseif($project->status === 'in_progress')
                <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                </svg>
            @else
                <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                </svg>
            @endif
            {{ ucfirst(str_replace('_', ' ', $project->status)) }}
        </span>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-6">
        <!-- Total Units -->
        <div class="card bg-gradient-to-br from-emerald-50 to-white">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-emerald-500 text-white">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Units</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ $stats['total_units'] }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Available Units -->
        <div class="card bg-gradient-to-br from-blue-50 to-white">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-blue-500 text-white">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Available</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ $stats['available_units'] }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Sold Units -->
        <div class="card bg-gradient-to-br from-green-50 to-white">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-green-500 text-white">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Sold</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ $stats['sold_units'] }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Total Value -->
        <div class="card bg-gradient-to-br from-purple-50 to-white">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-purple-500 text-white">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Value</dt>
                        <dd class="text-lg font-bold text-gray-900">Rp {{ number_format($stats['total_value'], 0, ',', '.') }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Project Information & Milestones Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Project Information Card -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Project Information
                </h3>
            </div>
            <div class="space-y-4">
                <div class="flex justify-between items-start border-b border-gray-100 pb-3">
                    <span class="text-sm font-medium text-gray-500">Land Location</span>
                    <span class="text-sm text-gray-900 text-right">{{ $project->land->address ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between items-start border-b border-gray-100 pb-3">
                    <span class="text-sm font-medium text-gray-500">Land Area</span>
                    <span class="text-sm text-gray-900">{{ number_format($project->land->area ?? 0, 0, ',', '.') }} m²</span>
                </div>
                <div class="flex justify-between items-start border-b border-gray-100 pb-3">
                    <span class="text-sm font-medium text-gray-500">Start Date</span>
                    <span class="text-sm text-gray-900">{{ $project->dt_start->format('d M Y') }}</span>
                </div>
                <div class="flex justify-between items-start border-b border-gray-100 pb-3">
                    <span class="text-sm font-medium text-gray-500">End Date</span>
                    <span class="text-sm text-gray-900">{{ $project->dt_end->format('d M Y') }}</span>
                </div>
                <div class="flex justify-between items-start border-b border-gray-100 pb-3">
                    <span class="text-sm font-medium text-gray-500">Duration</span>
                    <span class="text-sm text-gray-900">{{ $project->dt_start->diffInDays($project->dt_end) }} days</span>
                </div>
                <div class="flex justify-between items-start">
                    <span class="text-sm font-medium text-gray-500">Contractors</span>
                    <div class="text-right">
                        @foreach($project->contractors as $contractor)
                            <span class="inline-block px-2 py-1 text-xs font-medium rounded-md bg-gray-100 text-gray-800 mb-1">
                                {{ $contractor->name }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Milestones Progress Card -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center justify-between">
                    <span class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                        </svg>
                        Project Milestones
                    </span>
                    <span class="text-sm font-normal text-gray-500">{{ $stats['completed_milestones'] }} / {{ $stats['total_milestones'] }} completed</span>
                </h3>
            </div>

            <!-- Progress Bar -->
            <div class="mb-4">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium text-gray-700">Overall Progress</span>
                    <span class="text-sm font-bold text-emerald-600">{{ $stats['milestone_progress'] }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="bg-gradient-to-r from-emerald-500 to-green-600 h-3 rounded-full transition-all duration-500" 
                         style="width: {{ $stats['milestone_progress'] }}%"></div>
                </div>
            </div>

            <!-- Milestones List -->
            <div class="space-y-3 max-h-64 overflow-y-auto">
                @forelse($project->projectMilestones as $projectMilestone)
                    <div class="flex items-start p-3 rounded-lg {{ $projectMilestone->status === 'completed' ? 'bg-green-50' : 'bg-gray-50' }}">
                        <div class="flex-shrink-0 mr-3">
                            @if($projectMilestone->status === 'completed')
                                <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                </svg>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">{{ $projectMilestone->milestone->name }}</p>
                            <p class="text-xs text-gray-500 mt-1">
                                Target: {{ \Carbon\Carbon::parse($projectMilestone->target_dt)->format('d M Y') }}
                                @if($projectMilestone->completed_dt)
                                    • Completed: {{ \Carbon\Carbon::parse($projectMilestone->completed_dt)->format('d M Y') }}
                                @endif
                            </p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 text-center py-4">No milestones set for this project</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Clusters & Units Section -->
    <div class="card mb-6">
        <div class="card-header">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Clusters & Units Breakdown
            </h3>
        </div>

        @forelse($project->clusters as $cluster)
            <div class="mb-6 last:mb-0">
                <!-- Cluster Header -->
                <div class="bg-gradient-to-r from-purple-50 to-indigo-50 p-4 rounded-lg mb-4">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h4 class="text-lg font-bold text-gray-900 mb-2">{{ $cluster->name }}</h4>
                            @if($cluster->desc)
                                <p class="text-sm text-gray-600 mb-2">{{ $cluster->desc }}</p>
                            @endif
                            <div class="flex flex-wrap gap-4 text-sm">
                                @if($cluster->facilities)
                                    <span class="flex items-center text-gray-700">
                                        <svg class="w-4 h-4 mr-1.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                                        </svg>
                                        {{ $cluster->facilities }}
                                    </span>
                                @endif
                                <span class="flex items-center text-gray-700">
                                    <svg class="w-4 h-4 mr-1.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                    </svg>
                                    Road Width: {{ $cluster->road_width }}m
                                </span>
                                <span class="flex items-center text-gray-700 font-medium">
                                    <svg class="w-4 h-4 mr-1.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    Total Units: {{ $cluster->units->count() }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Products in Cluster -->
                @if($cluster->unitsByProduct)
                    <div class="space-y-6 ml-4">
                        @foreach($cluster->unitsByProduct as $productGroup)
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                <!-- Product Header -->
                                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                            </svg>
                                            <div>
                                                <h5 class="font-semibold text-gray-900">{{ $productGroup['product']->name }}</h5>
                                                <p class="text-xs text-gray-500">{{ $productGroup['product']->type->name ?? 'Product' }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-4 text-sm">
                                            <span class="text-gray-600">
                                                <span class="font-medium text-gray-900">{{ $productGroup['total'] }}</span> units
                                            </span>
                                            <span class="text-green-600">
                                                <span class="font-medium">{{ $productGroup['available'] }}</span> available
                                            </span>
                                            <span class="text-blue-600">
                                                <span class="font-medium">{{ $productGroup['sold'] }}</span> sold
                                            </span>
                                            @if($productGroup['reserved'] > 0)
                                                <span class="text-orange-600">
                                                    <span class="font-medium">{{ $productGroup['reserved'] }}</span> reserved
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Units Table -->
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Unit No</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Unit Name</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Facilities</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($productGroup['units'] as $unit)
                                                <tr class="hover:bg-gray-50">
                                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $unit->no }}</td>
                                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $unit->name }}</td>
                                                    <td class="px-4 py-3 text-sm text-gray-900 font-medium">
                                                        Rp {{ number_format($unit->price, 0, ',', '.') }}
                                                    </td>
                                                    <td class="px-4 py-3 text-sm">
                                                        @php
                                                            $statusBadges = [
                                                                'available' => 'bg-green-100 text-green-800',
                                                                'sold' => 'bg-blue-100 text-blue-800',
                                                                'reserved' => 'bg-orange-100 text-orange-800',
                                                            ];
                                                        @endphp
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusBadges[$unit->status] ?? 'bg-gray-100 text-gray-800' }}">
                                                            {{ ucfirst($unit->status) }}
                                                        </span>
                                                    </td>
                                                    <td class="px-4 py-3 text-sm text-gray-600">
                                                        {{ $unit->facilities ?? '-' }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            @if(!$loop->last)
                <hr class="my-6 border-gray-300">
            @endif
        @empty
            <p class="text-center text-gray-500 py-8">No clusters found for this project</p>
        @endforelse
    </div>

    <!-- Purchase Orders Section -->
    @if($project->purchaseOrders->count() > 0)
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Purchase Orders
                    <span class="ml-2 text-sm font-normal text-gray-500">({{ $project->purchaseOrders->count() }} orders)</span>
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">PO Number</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supplier</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Items</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($project->purchaseOrders as $po)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $po->no }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ \Carbon\Carbon::parse($po->dt)->format('d M Y') }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $po->supplier->name ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $po->details->count() }} items</td>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                    Rp {{ number_format($po->total, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @php
                                        $poStatusBadges = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'completed' => 'bg-green-100 text-green-800',
                                            'cancelled' => 'bg-red-100 text-red-800',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $poStatusBadges[$po->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($po->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</x-admin-layout>
