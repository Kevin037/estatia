<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900">
                Trial Balance (Neraca Saldo)
            </h2>
        </div>
    </x-slot>

    <!-- Filter Card -->
    <div class="card mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Filter Periode</h3>
        <form method="GET" action="{{ route('trial-balance.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="form-input" required>
            </div>
            <div>
                <label class="form-label">Tanggal Akhir</label>
                <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="form-input" required>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="btn btn-primary">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                    Terapkan
                </button>
                <a href="{{ route('trial-balance.index') }}" class="btn btn-secondary">
                    Reset
                </a>
            </div>
        </form>
        <div class="mt-4 text-sm text-gray-600">
            <p>Periode: <span class="font-semibold">{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}</span> sampai <span class="font-semibold">{{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</span></p>
        </div>
    </div>

    <!-- Trial Balance Table -->
    <div class="card">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            ID
                        </th>
                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Kode Akun
                        </th>
                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nama Akun
                        </th>
                        <th scope="col" class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Saldo Awal<br>Debit
                        </th>
                        <th scope="col" class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Saldo Awal<br>Kredit
                        </th>
                        <th scope="col" class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Mutasi<br>Debit
                        </th>
                        <th scope="col" class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Mutasi<br>Kredit
                        </th>
                        <th scope="col" class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Saldo Akhir<br>Debit
                        </th>
                        <th scope="col" class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Saldo Akhir<br>Kredit
                        </th>
                        <th scope="col" class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Saldo Akhir<br>(Debit-Kredit)
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($trialBalanceData as $data)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-3 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $data['account']->id }}
                                </div>
                            </td>
                            <td class="px-3 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $data['account']->id }}
                                </div>
                            </td>
                            <td class="px-3 py-4">
                                <div class="text-sm text-gray-900">
                                    {{ $data['account']->name }}
                                </div>
                            </td>
                            <td class="px-3 py-4 whitespace-nowrap text-right">
                                <div class="text-sm {{ $data['saldo_awal_debit'] > 0 ? 'text-green-600 font-medium' : 'text-gray-400' }}">
                                    {{ $data['saldo_awal_debit'] > 0 ? number_format($data['saldo_awal_debit'], 0, ',', '.') : '-' }}
                                </div>
                            </td>
                            <td class="px-3 py-4 whitespace-nowrap text-right">
                                <div class="text-sm {{ $data['saldo_awal_kredit'] > 0 ? 'text-red-600 font-medium' : 'text-gray-400' }}">
                                    {{ $data['saldo_awal_kredit'] > 0 ? number_format($data['saldo_awal_kredit'], 0, ',', '.') : '-' }}
                                </div>
                            </td>
                            <td class="px-3 py-4 whitespace-nowrap text-right">
                                <div class="text-sm {{ $data['mutasi_debit'] > 0 ? 'text-green-600 font-medium' : 'text-gray-400' }}">
                                    {{ $data['mutasi_debit'] > 0 ? number_format($data['mutasi_debit'], 0, ',', '.') : '-' }}
                                </div>
                            </td>
                            <td class="px-3 py-4 whitespace-nowrap text-right">
                                <div class="text-sm {{ $data['mutasi_kredit'] > 0 ? 'text-red-600 font-medium' : 'text-gray-400' }}">
                                    {{ $data['mutasi_kredit'] > 0 ? number_format($data['mutasi_kredit'], 0, ',', '.') : '-' }}
                                </div>
                            </td>
                            <td class="px-3 py-4 whitespace-nowrap text-right">
                                <div class="text-sm {{ $data['saldo_akhir_debit'] > 0 ? 'text-green-600 font-medium' : 'text-gray-400' }}">
                                    {{ $data['saldo_akhir_debit'] > 0 ? number_format($data['saldo_akhir_debit'], 0, ',', '.') : '-' }}
                                </div>
                            </td>
                            <td class="px-3 py-4 whitespace-nowrap text-right">
                                <div class="text-sm {{ $data['saldo_akhir_kredit'] > 0 ? 'text-red-600 font-medium' : 'text-gray-400' }}">
                                    {{ $data['saldo_akhir_kredit'] > 0 ? number_format($data['saldo_akhir_kredit'], 0, ',', '.') : '-' }}
                                </div>
                            </td>
                            <td class="px-3 py-4 whitespace-nowrap text-right">
                                <div class="text-sm font-semibold {{ $data['saldo_akhir'] >= 0 ? 'text-gray-900' : 'text-red-600' }}">
                                    {{ number_format($data['saldo_akhir'], 0, ',', '.') }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada data</h3>
                                <p class="mt-1 text-sm text-gray-500">Tidak ada akun yang ditemukan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <!-- Footer with totals -->
                @if (count($trialBalanceData) > 0)
                    <tfoot class="bg-gray-100 border-t-2 border-gray-300">
                        <tr class="font-bold">
                            <td colspan="3" class="px-3 py-4 text-right text-sm text-gray-900">
                                TOTAL:
                            </td>
                            <td class="px-3 py-4 whitespace-nowrap text-right">
                                <div class="text-sm {{ $totals['saldo_awal_debit'] > 0 ? 'text-green-600' : 'text-gray-400' }}">
                                    {{ $totals['saldo_awal_debit'] > 0 ? number_format($totals['saldo_awal_debit'], 0, ',', '.') : '-' }}
                                </div>
                            </td>
                            <td class="px-3 py-4 whitespace-nowrap text-right">
                                <div class="text-sm {{ $totals['saldo_awal_kredit'] > 0 ? 'text-red-600' : 'text-gray-400' }}">
                                    {{ $totals['saldo_awal_kredit'] > 0 ? number_format($totals['saldo_awal_kredit'], 0, ',', '.') : '-' }}
                                </div>
                            </td>
                            <td class="px-3 py-4 whitespace-nowrap text-right">
                                <div class="text-sm {{ $totals['mutasi_debit'] > 0 ? 'text-green-600' : 'text-gray-400' }}">
                                    {{ $totals['mutasi_debit'] > 0 ? number_format($totals['mutasi_debit'], 0, ',', '.') : '-' }}
                                </div>
                            </td>
                            <td class="px-3 py-4 whitespace-nowrap text-right">
                                <div class="text-sm {{ $totals['mutasi_kredit'] > 0 ? 'text-red-600' : 'text-gray-400' }}">
                                    {{ $totals['mutasi_kredit'] > 0 ? number_format($totals['mutasi_kredit'], 0, ',', '.') : '-' }}
                                </div>
                            </td>
                            <td class="px-3 py-4 whitespace-nowrap text-right">
                                <div class="text-sm {{ $totals['saldo_akhir_debit'] > 0 ? 'text-green-600' : 'text-gray-400' }}">
                                    {{ $totals['saldo_akhir_debit'] > 0 ? number_format($totals['saldo_akhir_debit'], 0, ',', '.') : '-' }}
                                </div>
                            </td>
                            <td class="px-3 py-4 whitespace-nowrap text-right">
                                <div class="text-sm {{ $totals['saldo_akhir_kredit'] > 0 ? 'text-red-600' : 'text-gray-400' }}">
                                    {{ $totals['saldo_akhir_kredit'] > 0 ? number_format($totals['saldo_akhir_kredit'], 0, ',', '.') : '-' }}
                                </div>
                            </td>
                            <td class="px-3 py-4 whitespace-nowrap text-right">
                                <div class="text-sm {{ $totals['saldo_akhir'] >= 0 ? 'text-gray-900' : 'text-red-600' }}">
                                    {{ number_format($totals['saldo_akhir'], 0, ',', '.') }}
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        <!-- Footer with count -->
        @if (count($trialBalanceData) > 0)
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
                <div class="text-sm text-gray-700">
                    Total Akun: <span class="font-semibold">{{ count($trialBalanceData) }}</span>
                </div>
            </div>
        @endif
    </div>
</x-admin-layout>
