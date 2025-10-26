<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900">
                Balance Sheet (Neraca)
            </h2>
        </div>
    </x-slot>

    <!-- Date Filter Form -->
    <div class="card mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Report Parameters</h3>
        
        @if ($errors->any())
            <div class="mb-4 rounded-md bg-red-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Validation errors</h3>
                        <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form method="GET" action="{{ route('reports.balance_sheet') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="form-label">As of Date <span class="text-red-500">*</span></label>
                    <input type="date" name="as_of" id="as_of" 
                           value="{{ $asOfDate }}" 
                           class="form-input" 
                           required>
                    <p class="mt-1 text-xs text-gray-500">Snapshot date for balance sheet</p>
                </div>
                <div>
                    <label class="form-label">P&L Start Date</label>
                    <input type="date" name="pl_start_date" id="pl_start_date" 
                           value="{{ $plStartDate }}" 
                           class="form-input">
                    <p class="mt-1 text-xs text-gray-500">For net profit calculation</p>
                </div>
                <div>
                    <label class="form-label">P&L End Date</label>
                    <input type="date" name="pl_end_date" id="pl_end_date" 
                           value="{{ $plEndDate }}" 
                           class="form-input">
                    <p class="mt-1 text-xs text-gray-500">For net profit calculation</p>
                </div>
            </div>
            
            <div class="flex items-center gap-2">
                <button type="submit" class="btn btn-primary">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25M3.75 14.25h16.5V3.75H3.75v10.5zM16.5 14.25v5.25m0 0H7.5m9 0h3.75M7.5 19.5v-5.25" />
                    </svg>
                    Generate Report
                </button>
                <a href="{{ route('reports.balance_sheet') }}" class="btn btn-secondary">
                    Reset
                </a>
            </div>
        </form>

        <div class="mt-4 pt-4 border-t border-gray-200">
            <div class="flex items-center justify-between text-sm">
                <div>
                    <span class="text-gray-600">As of: </span>
                    <span class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($asOfDate)->format('d M Y') }}</span>
                </div>
                <div>
                    <span class="text-gray-600">P&L Period: </span>
                    <span class="font-semibold text-gray-900">
                        {{ \Carbon\Carbon::parse($plStartDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($plEndDate)->format('d M Y') }}
                    </span>
                </div>
                <div class="flex items-center">
                    <span class="text-gray-600 mr-2">Status: </span>
                    @if ($data['balanced'])
                        <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-800">
                            <svg class="mr-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Balanced
                        </span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-xs font-medium text-red-800">
                            <svg class="mr-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            Unbalanced
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Balance Sheet Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Assets Section -->
        <div class="card">
            <div class="bg-blue-50 px-6 py-3 border-b border-blue-200">
                <h3 class="text-lg font-semibold text-blue-900">ASSETS (Aset)</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Account
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Amount (Rp)
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($data['assets_breakdown'] as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3">
                                    <div class="text-sm text-gray-900">{{ $item['account_name'] }}</div>
                                    @if (isset($item['account_id']))
                                        <div class="text-xs text-gray-500">{{ $item['account_id'] }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ number_format($item['amount'], 0, ',', '.') }}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-6 py-8 text-center text-sm text-gray-500">
                                    No asset data available
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-blue-100 border-t-2 border-blue-300">
                        <tr>
                            <td class="px-6 py-3 text-left">
                                <div class="text-sm font-bold text-blue-900">TOTAL ASSETS</div>
                            </td>
                            <td class="px-6 py-3 text-right">
                                <div class="text-sm font-bold text-blue-900">
                                    {{ number_format($data['assets_total'], 0, ',', '.') }}
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Liabilities & Equity Column -->
        <div class="space-y-6">
            <!-- Liabilities Section -->
            <div class="card">
                <div class="bg-red-50 px-6 py-3 border-b border-red-200">
                    <h3 class="text-lg font-semibold text-red-900">LIABILITIES (Kewajiban)</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Account
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Amount (Rp)
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($data['liabilities_breakdown'] as $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-3">
                                        <div class="text-sm text-gray-900">{{ $item['account_name'] }}</div>
                                        @if (isset($item['account_id']))
                                            <div class="text-xs text-gray-500">{{ $item['account_id'] }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 text-right">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ number_format($item['amount'], 0, ',', '.') }}
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-6 py-8 text-center text-sm text-gray-500">
                                        No liability data available
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-red-100 border-t-2 border-red-300">
                            <tr>
                                <td class="px-6 py-3 text-left">
                                    <div class="text-sm font-bold text-red-900">TOTAL LIABILITIES</div>
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <div class="text-sm font-bold text-red-900">
                                        {{ number_format($data['liabilities_total'], 0, ',', '.') }}
                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Equity Section -->
            <div class="card">
                <div class="bg-green-50 px-6 py-3 border-b border-green-200">
                    <h3 class="text-lg font-semibold text-green-900">EQUITY (Ekuitas)</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Account
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Amount (Rp)
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($data['equities_breakdown'] as $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-3">
                                        <div class="text-sm text-gray-900">{{ $item['account_name'] }}</div>
                                        @if (isset($item['account_id']))
                                            <div class="text-xs text-gray-500">{{ $item['account_id'] }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 text-right">
                                        <div class="text-sm font-medium {{ $item['amount'] >= 0 ? 'text-gray-900' : 'text-red-600' }}">
                                            {{ number_format($item['amount'], 0, ',', '.') }}
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-6 py-8 text-center text-sm text-gray-500">
                                        No equity data available
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-green-100 border-t-2 border-green-300">
                            <tr>
                                <td class="px-6 py-3 text-left">
                                    <div class="text-sm font-bold text-green-900">TOTAL EQUITY</div>
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <div class="text-sm font-bold text-green-900">
                                        {{ number_format($data['equities_total'], 0, ',', '.') }}
                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Card -->
    <div class="card bg-gradient-to-r from-emerald-50 to-blue-50">
        <div class="px-6 py-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Balance Sheet Summary</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2 border-b border-gray-200">
                        <span class="text-sm font-medium text-gray-700">Total Assets:</span>
                        <span class="text-lg font-bold text-blue-700">
                            Rp {{ number_format($data['assets_total'], 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-200">
                        <span class="text-sm font-medium text-gray-700">Total Liabilities:</span>
                        <span class="text-lg font-bold text-red-700">
                            Rp {{ number_format($data['liabilities_total'], 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-200">
                        <span class="text-sm font-medium text-gray-700">Total Equity:</span>
                        <span class="text-lg font-bold text-green-700">
                            Rp {{ number_format($data['equities_total'], 0, ',', '.') }}
                        </span>
                    </div>
                </div>
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2 border-b border-gray-200">
                        <span class="text-sm font-medium text-gray-700">Net Profit (Current Period):</span>
                        <span class="text-lg font-bold {{ $data['net_profit'] >= 0 ? 'text-green-700' : 'text-red-700' }}">
                            Rp {{ number_format($data['net_profit'], 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-200">
                        <span class="text-sm font-medium text-gray-700">Liabilities + Equity:</span>
                        <span class="text-lg font-bold text-gray-700">
                            Rp {{ number_format($data['liabilities_total'] + $data['equities_total'], 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-2 bg-white rounded-lg px-3 {{ $data['balanced'] ? 'border-2 border-green-500' : 'border-2 border-red-500' }}">
                        <span class="text-sm font-semibold text-gray-900">Balance Check:</span>
                        <span class="text-lg font-bold {{ $data['balanced'] ? 'text-green-700' : 'text-red-700' }}">
                            {{ $data['balanced'] ? '✓ BALANCED' : '✗ UNBALANCED' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Print Button -->
    <div class="mt-6 flex justify-end">
        <button onclick="window.print()" class="btn btn-secondary">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z" />
            </svg>
            Print Balance Sheet
        </button>
    </div>

    <!-- Print Styles -->
    <style>
        @media print {
            .sidebar, .header, nav, button, .no-print, form {
                display: none !important;
            }
            body {
                padding: 0;
                margin: 0;
            }
            .card {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
                page-break-inside: avoid;
            }
            .grid {
                display: block !important;
            }
            .lg\:grid-cols-2 > div {
                page-break-inside: avoid;
                margin-bottom: 1rem;
            }
        }
    </style>
</x-admin-layout>
