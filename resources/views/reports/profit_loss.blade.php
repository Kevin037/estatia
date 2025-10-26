<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900">
                Profit & Loss Report (Laporan Laba Rugi)
            </h2>
        </div>
    </x-slot>

    <!-- Date Range Filter -->
    <div class="card mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Filter Periode</h3>
        
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

        <form method="GET" action="{{ route('reports.profit_loss') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="start_date" id="start_date" 
                       value="{{ $startDate }}" 
                       class="form-input" 
                       required>
            </div>
            <div>
                <label class="form-label">Tanggal Akhir</label>
                <input type="date" name="end_date" id="end_date" 
                       value="{{ $endDate }}" 
                       class="form-input" 
                       required>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="btn btn-primary">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25M3.75 14.25h16.5V3.75H3.75v10.5zM16.5 14.25v5.25m0 0H7.5m9 0h3.75M7.5 19.5v-5.25" />
                    </svg>
                    Generate Report
                </button>
                <a href="{{ route('reports.profit_loss') }}" class="btn btn-secondary">
                    Reset
                </a>
            </div>
        </form>

        <div class="mt-4 text-sm text-gray-600">
            <p>Periode: <span class="font-semibold">{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}</span> sampai <span class="font-semibold">{{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</span></p>
        </div>
    </div>

    <!-- Profit & Loss Report Table -->
    <div class="card">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Description
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Amount (Rp)
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <!-- Revenue Section -->
                    <tr class="bg-blue-50">
                        <td colspan="2" class="px-6 py-3">
                            <h3 class="text-sm font-semibold text-gray-900 uppercase">Revenue (Pendapatan)</h3>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">Total Sales</div>
                            <div class="text-xs text-gray-500">Penjualan dari orders yang completed</div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="text-sm font-medium text-green-600">
                                Rp {{ number_format($data['total_sales'], 0, ',', '.') }}
                            </div>
                        </td>
                    </tr>

                    <!-- Cost of Goods Sold Section -->
                    <tr class="bg-yellow-50">
                        <td colspan="2" class="px-6 py-3">
                            <h3 class="text-sm font-semibold text-gray-900 uppercase">Cost of Goods Sold (HPP)</h3>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">Total HPP</div>
                            <div class="text-xs text-gray-500">Harga Pokok Penjualan dari purchase orders</div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="text-sm font-medium text-red-600">
                                Rp {{ number_format($data['total_hpp'], 0, ',', '.') }}
                            </div>
                        </td>
                    </tr>

                    <!-- Gross Profit -->
                    <tr class="bg-green-50">
                        <td class="px-6 py-4">
                            <div class="text-sm font-semibold text-gray-900">Gross Profit (Laba Kotor)</div>
                            <div class="text-xs text-gray-500">Total Sales - Total HPP</div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="text-sm font-bold {{ $data['gross_profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                Rp {{ number_format($data['gross_profit'], 0, ',', '.') }}
                            </div>
                        </td>
                    </tr>

                    <!-- Operating Expenses Section -->
                    <tr class="bg-orange-50">
                        <td colspan="2" class="px-6 py-3">
                            <h3 class="text-sm font-semibold text-gray-900 uppercase">Operating Expenses (Beban Operasional)</h3>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">Total Expenses</div>
                            <div class="text-xs text-gray-500">Beban dari akun expense (kode 5xxx)</div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="text-sm font-medium text-red-600">
                                Rp {{ number_format($data['total_expenses'], 0, ',', '.') }}
                            </div>
                        </td>
                    </tr>

                    <!-- Net Profit -->
                    <tr class="bg-emerald-100 border-t-2 border-emerald-300">
                        <td class="px-6 py-4">
                            <div class="text-base font-bold text-gray-900">Net Profit (Laba Bersih)</div>
                            <div class="text-xs text-gray-600">Gross Profit - Total Expenses</div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="text-lg font-bold {{ $data['net_profit'] >= 0 ? 'text-green-700' : 'text-red-700' }}">
                                Rp {{ number_format($data['net_profit'], 0, ',', '.') }}
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Summary Cards -->
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Gross Profit Margin -->
                <div class="bg-white rounded-lg p-4 shadow-sm">
                    <div class="text-xs text-gray-500 uppercase">Gross Profit Margin</div>
                    <div class="mt-1 text-xl font-semibold {{ ($data['total_sales'] > 0 ? ($data['gross_profit'] / $data['total_sales'] * 100) : 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $data['total_sales'] > 0 ? number_format(($data['gross_profit'] / $data['total_sales'] * 100), 2) : '0.00' }}%
                    </div>
                </div>

                <!-- Net Profit Margin -->
                <div class="bg-white rounded-lg p-4 shadow-sm">
                    <div class="text-xs text-gray-500 uppercase">Net Profit Margin</div>
                    <div class="mt-1 text-xl font-semibold {{ ($data['total_sales'] > 0 ? ($data['net_profit'] / $data['total_sales'] * 100) : 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $data['total_sales'] > 0 ? number_format(($data['net_profit'] / $data['total_sales'] * 100), 2) : '0.00' }}%
                    </div>
                </div>

                <!-- Expense Ratio -->
                <div class="bg-white rounded-lg p-4 shadow-sm">
                    <div class="text-xs text-gray-500 uppercase">Expense Ratio</div>
                    <div class="mt-1 text-xl font-semibold text-orange-600">
                        {{ $data['total_sales'] > 0 ? number_format(($data['total_expenses'] / $data['total_sales'] * 100), 2) : '0.00' }}%
                    </div>
                </div>
            </div>
        </div>

        <!-- Print Button -->
        <div class="bg-white px-6 py-3 border-t border-gray-200">
            <button onclick="window.print()" class="btn btn-secondary">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z" />
                </svg>
                Print Report
            </button>
        </div>
    </div>

    <!-- Print Styles -->
    <style>
        @media print {
            .sidebar, .header, nav, button, .no-print {
                display: none !important;
            }
            body {
                padding: 0;
                margin: 0;
            }
            .card {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
            }
        }
    </style>
</x-admin-layout>
